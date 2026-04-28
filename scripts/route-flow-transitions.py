#!/usr/bin/env python3
from __future__ import annotations

import json
import re
import sys
from datetime import datetime
from pathlib import Path
from subprocess import run
from typing import Any


ROOT = Path(__file__).resolve().parent.parent
DRUSH_ROOT = Path("/var/www/html/forseti")


def log(message: str) -> None:
    print(f"[flow-route] {message}", file=sys.stderr)


def slugify(value: str) -> str:
    slug = re.sub(r"[^A-Za-z0-9._-]+", "-", value).strip("-").lower()
    return slug or "item"


def parse_simple_metadata(text: str) -> dict[str, str]:
    values: dict[str, str] = {}
    for line in text.splitlines():
        match = re.match(r"^\-\s+([^:]+):\s*(.+?)\s*$", line)
        if match:
            values[match.group(1).strip()] = match.group(2).strip()
    return values


def extract_status(text: str) -> str:
    match = re.search(r"^\-\s+Status:\s*(.+?)\s*$", text, re.MULTILINE | re.IGNORECASE)
    if not match:
        return ""
    return re.sub(r"[^a-z-].*$", "", match.group(1).strip().lower().replace("_", "-").replace(" ", "-"))


def extract_roi(text: str, default: int = 20) -> int:
    match = re.search(r"ROI:\s*([0-9]+)", text)
    if not match:
        return default
    return max(1, int(match.group(1)))


def extract_flow_outcomes(text: str) -> list[str]:
    outcomes: list[str] = []
    for raw in re.findall(r"^\-\s+Flow outcome:\s*(.+?)\s*$", text, re.MULTILINE | re.IGNORECASE):
        parts = [part.strip() for part in re.split(r"[;|]", raw) if part.strip()]
        outcomes.extend(parts if parts else [raw.strip()])
    return outcomes


def load_flow(flow_id: str) -> dict[str, Any] | None:
    if not DRUSH_ROOT.exists():
        log(f"skip live flow lookup for {flow_id}: missing {DRUSH_ROOT}")
        return None

    php = (
        f'$flow = \\Drupal::service("drupal_langgraph.process_flow_registry")->getFlow("{flow_id}"); '
        'if (!$flow) { exit(2); } '
        'echo json_encode($flow, JSON_PRETTY_PRINT);'
    )
    proc = run(
        ["vendor/bin/drush", "--uri=https://forseti.life", "php:eval", php],
        cwd=DRUSH_ROOT,
        capture_output=True,
        text=True,
        check=False,
    )
    if proc.returncode != 0 or not proc.stdout.strip():
        log(f"skip flow {flow_id}: drush lookup failed")
        return None
    try:
        payload = json.loads(proc.stdout)
    except json.JSONDecodeError:
        log(f"skip flow {flow_id}: invalid JSON from drush lookup")
        return None
    return payload if isinstance(payload, dict) else None


def node_owner_map(flow: dict[str, Any]) -> dict[str, str]:
    mapping: dict[str, str] = {}
    for item in flow.get("node_breakdown", []):
        if isinstance(item, dict):
            node = str(item.get("parent_node", "")).strip()
            owner = str(item.get("owner_seat", "")).strip()
            if node and owner:
                mapping[node] = owner
    return mapping


def outgoing_transitions(flow: dict[str, Any], node: str) -> list[dict[str, str]]:
    results: list[dict[str, str]] = []
    for item in flow.get("transitions", []):
        if not isinstance(item, dict):
            continue
        if str(item.get("from_node", "")).strip() != node:
            continue
        results.append({
            "from_node": str(item.get("from_node", "")).strip(),
            "to_node": str(item.get("to_node", "")).strip(),
            "condition": str(item.get("condition", "")).strip(),
            "kind": str(item.get("kind", "direct")).strip() or "direct",
        })
    return results


def incoming_transitions(flow: dict[str, Any], node: str) -> list[dict[str, str]]:
    results: list[dict[str, str]] = []
    for item in flow.get("transitions", []):
        if not isinstance(item, dict):
            continue
        if str(item.get("to_node", "")).strip() != node:
            continue
        results.append({
            "from_node": str(item.get("from_node", "")).strip(),
            "to_node": str(item.get("to_node", "")).strip(),
            "condition": str(item.get("condition", "")).strip(),
            "kind": str(item.get("kind", "direct")).strip() or "direct",
        })
    return results


def flow_runtime_dir(flow_id: str, run_id: str) -> Path:
    return ROOT / "tmp" / "flow-runs" / slugify(flow_id) / slugify(run_id)


def next_sequence(run_dir: Path, node: str) -> int:
    counters_dir = run_dir / "counters"
    counters_dir.mkdir(parents=True, exist_ok=True)
    path = counters_dir / f"{slugify(node)}.txt"
    current = 0
    if path.exists():
        try:
            current = int(path.read_text(encoding="utf-8").strip() or "0")
        except ValueError:
            current = 0
    current += 1
    path.write_text(f"{current}\n", encoding="utf-8")
    return current


def route_date_for_item(item_name: str) -> str:
    match = re.match(r"^([0-9]{8})", item_name)
    return match.group(1) if match else datetime.utcnow().strftime("%Y%m%d")


def create_inbox_item(target_agent: str, item_name: str, roi: int, command_content: str) -> None:
    inbox_dir = ROOT / "sessions" / target_agent / "inbox" / item_name
    outbox_path = ROOT / "sessions" / target_agent / "outbox" / f"{item_name}.md"
    if inbox_dir.exists() or outbox_path.exists():
        log(f"skip existing routed item {target_agent}/{item_name}")
        return
    inbox_dir.mkdir(parents=True, exist_ok=True)
    (inbox_dir / "roi.txt").write_text(f"{roi}\n", encoding="utf-8")
    (inbox_dir / "command.md").write_text(command_content, encoding="utf-8")
    log(f"created routed item sessions/{target_agent}/inbox/{item_name}")


def resolve_command_path(agent_id: str, item_name: str) -> Path | None:
    inbox_path = ROOT / "sessions" / agent_id / "inbox" / item_name / "command.md"
    if inbox_path.exists():
        return inbox_path

    artifacts_dir = ROOT / "sessions" / agent_id / "artifacts"
    if not artifacts_dir.exists():
        return None

    matches = sorted(artifacts_dir.glob(f"{item_name}*/command.md"), key=lambda path: path.stat().st_mtime, reverse=True)
    return matches[0] if matches else None


def build_command(
    *,
    flow_id: str,
    run_id: str,
    target_node: str,
    target_owner: str,
    source_agent: str,
    source_node: str,
    source_outbox: Path,
    incoming_conditions: list[str],
    available_outcomes: list[str],
) -> str:
    metadata = [
        f"- Flow id: {flow_id}",
        f"- Flow run id: {run_id}",
        f"- Flow node: {target_node}",
        f"- Flow owner seat: {target_owner}",
        f"- Flow previous node: {source_node}",
        f"- Flow source outbox: {source_outbox}",
    ]
    if incoming_conditions:
        metadata.append(f"- Flow incoming conditions: {' | '.join(incoming_conditions)}")
    if available_outcomes:
        metadata.append(f"- Available flow outcomes: {' | '.join(available_outcomes)}")

    return "\n".join(
        metadata
        + [
            "",
            f"# Flow handoff: {flow_id} / {target_node}",
            "",
            f"This inbox item was routed automatically from `{source_node}` after `{source_agent}` completed the previous step.",
            "",
            "## Required action",
            f"1. Execute the responsibilities of `{target_node}` as the owning seat `{target_owner}`.",
            f"2. Review the source outbox: `{source_outbox}` for the completed upstream context.",
            "3. If this node has branching outcomes, include one or more `- Flow outcome:` lines in your outbox using the exact allowed values listed above.",
            "4. If this node has only one direct next step, no Flow outcome line is required.",
        ]
    ) + "\n"


def merge_receipt_path(run_dir: Path, target_node: str, source_node: str) -> Path:
    return run_dir / "merge-receipts" / slugify(target_node) / f"{slugify(source_node)}.json"


def record_merge_receipt(
    *,
    run_dir: Path,
    target_node: str,
    source_node: str,
    condition: str,
    source_outbox: Path,
) -> None:
    path = merge_receipt_path(run_dir, target_node, source_node)
    path.parent.mkdir(parents=True, exist_ok=True)
    payload = {
        "source_node": source_node,
        "condition": condition,
        "source_outbox": str(source_outbox),
    }
    path.write_text(json.dumps(payload, indent=2) + "\n", encoding="utf-8")


def merge_ready(flow: dict[str, Any], run_dir: Path, target_node: str) -> tuple[bool, list[str]]:
    incoming = incoming_transitions(flow, target_node)
    conditions = [item["condition"] for item in incoming if item["condition"]]
    if len(incoming) <= 1 or len(conditions) != len(incoming) or len(set(conditions)) != 1:
        return True, []
    missing: list[str] = []
    observed_conditions: list[str] = []
    for item in incoming:
        source_node = item["from_node"]
        path = merge_receipt_path(run_dir, target_node, source_node)
        if not path.exists():
            missing.append(source_node)
            continue
        try:
            payload = json.loads(path.read_text(encoding="utf-8"))
        except json.JSONDecodeError:
            missing.append(source_node)
            continue
        condition = str(payload.get("condition", "")).strip()
        if condition:
            observed_conditions.append(condition)
    return not missing, observed_conditions


def clear_merge_receipts(run_dir: Path, target_node: str) -> None:
    receipt_dir = run_dir / "merge-receipts" / slugify(target_node)
    if not receipt_dir.exists():
        return
    for child in receipt_dir.iterdir():
        if child.is_file():
            child.unlink()
    try:
        receipt_dir.rmdir()
    except OSError:
        pass


def routed_item_name(route_date: str, flow_id: str, run_id: str, node: str, sequence: int) -> str:
    base = f"{route_date}-flow-{slugify(flow_id)}-{slugify(run_id)}-{slugify(node)}-r{sequence}"
    return base[:180].rstrip("-")


def selected_transitions(outgoing: list[dict[str, str]], outcomes: list[str]) -> list[dict[str, str]]:
    if not outgoing:
        return []
    if len(outgoing) == 1 and outgoing[0]["condition"] == "":
        return outgoing
    if not outcomes:
        return []
    selected: list[dict[str, str]] = []
    for transition in outgoing:
        if transition["condition"] in outcomes:
            selected.append(transition)
    return selected


def main() -> int:
    if len(sys.argv) < 4:
        return 0

    agent_id = sys.argv[1]
    item_name = sys.argv[2]
    outbox_file = Path(sys.argv[3])
    command_path = resolve_command_path(agent_id, item_name)
    if command_path is None or not outbox_file.exists():
        return 0

    command_meta = parse_simple_metadata(command_path.read_text(encoding="utf-8", errors="ignore"))
    flow_id = command_meta.get("Flow id", "").strip()
    current_node = command_meta.get("Flow node", "").strip()
    if not flow_id or not current_node:
        return 0

    outbox_text = outbox_file.read_text(encoding="utf-8", errors="ignore")
    if extract_status(outbox_text) != "done":
        return 0

    flow = load_flow(flow_id)
    if flow is None:
        return 0

    run_id = command_meta.get("Flow run id", "").strip() or item_name
    run_dir = flow_runtime_dir(flow_id, run_id)
    run_dir.mkdir(parents=True, exist_ok=True)

    owner_map = node_owner_map(flow)
    outgoing = outgoing_transitions(flow, current_node)
    transitions = selected_transitions(outgoing, extract_flow_outcomes(outbox_text))
    if outgoing and not transitions:
        log(f"no matching flow outcome for {flow_id}/{current_node}; no handoff created")
        return 0

    roi = extract_roi(outbox_text, 20)
    route_date = route_date_for_item(item_name)

    for transition in transitions:
        target_node = transition["to_node"]
        if not target_node or target_node in {"END", "__end__"}:
            (run_dir / "completed.json").write_text(
                json.dumps({"completed_from": current_node, "source_outbox": str(outbox_file)}, indent=2) + "\n",
                encoding="utf-8",
            )
            log(f"flow {flow_id}/{run_id} completed at {current_node}")
            continue

        target_owner = owner_map.get(target_node, "").strip()
        if not target_owner:
            log(f"skip target {target_node}: no owner_seat metadata")
            continue

        record_merge_receipt(
            run_dir=run_dir,
            target_node=target_node,
            source_node=current_node,
            condition=transition["condition"],
            source_outbox=outbox_file,
        )

        ready, incoming_conditions = merge_ready(flow, run_dir, target_node)
        if not ready:
            log(f"waiting for merge prerequisites before routing {flow_id}/{run_id} -> {target_node}")
            continue

        sequence = next_sequence(run_dir, target_node)
        item_name_out = routed_item_name(route_date, flow_id, run_id, target_node, sequence)
        next_outgoing = outgoing_transitions(flow, target_node)
        available_outcomes = [item["condition"] for item in next_outgoing if item["condition"]]
        command_content = build_command(
            flow_id=flow_id,
            run_id=run_id,
            target_node=target_node,
            target_owner=target_owner,
            source_agent=agent_id,
            source_node=current_node,
            source_outbox=outbox_file,
            incoming_conditions=incoming_conditions or ([transition["condition"]] if transition["condition"] else []),
            available_outcomes=available_outcomes,
        )
        create_inbox_item(target_owner, item_name_out, roi, command_content)
        clear_merge_receipts(run_dir, target_node)

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
