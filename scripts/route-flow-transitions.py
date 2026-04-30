#!/usr/bin/env python3
from __future__ import annotations

import json
import re
import sys
from collections import Counter
from datetime import datetime
from pathlib import Path
from subprocess import run
from typing import Any


ROOT = Path(__file__).resolve().parent.parent
DRUSH_ROOT = Path("/var/www/html/forseti")
PRODUCT_TEAMS_PATH = ROOT / "org-chart" / "products" / "product-teams.json"
TRANSCRIPT_MARKERS = (
    "tool call:",
    "**tool call:**",
    "**output:**",
    "```bash",
    "```python",
    "## step 1:",
    "## step 2:",
    "## step 3:",
)
GENERIC_FLOW_WORDS = {
    "status", "summary", "flow", "outcome", "requirements", "review", "request",
    "product", "team", "source", "inbox", "outbox", "agent", "generated", "next",
    "actions", "blockers", "owner", "seat", "node", "current", "details", "route",
    "routed", "routing", "read", "writing", "before", "after", "feature", "work",
    "scope", "user", "users", "goal", "goals", "context", "notes", "suggestion",
    "community", "upstream", "downstream", "decision", "delivery",
}
STOPWORDS = {
    "about", "after", "again", "against", "almost", "also", "always", "another",
    "around", "because", "before", "being", "between", "both", "cannot", "could",
    "details", "during", "each", "from", "have", "into", "just", "like", "make",
    "many", "more", "most", "must", "need", "none", "only", "other", "over",
    "same", "should", "some", "such", "than", "that", "their", "them", "then",
    "there", "these", "they", "this", "those", "through", "under", "very", "what",
    "when", "where", "which", "while", "with", "would", "your",
}


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


def read_item_roi(item_dir: Path, default: int = 20) -> int:
    path = item_dir / "roi.txt"
    if not path.exists():
        return default
    try:
        return max(1, int(path.read_text(encoding="utf-8").strip() or str(default)))
    except (OSError, ValueError):
        return default


def first_nonempty_line(text: str) -> str:
    for line in text.splitlines():
        stripped = line.strip()
        if stripped:
            return stripped
    return ""


def has_transcript_markers(text: str) -> bool:
    lowered = text.lower()
    return any(marker in lowered for marker in TRANSCRIPT_MARKERS)


def tokenize_keywords(text: str) -> list[str]:
    tokens = re.findall(r"[a-z0-9][a-z0-9_-]{3,}", text.lower())
    results: list[str] = []
    for token in tokens:
        if token.isdigit():
            continue
        if token in STOPWORDS or token in GENERIC_FLOW_WORDS:
            continue
        results.append(token)
    return results


def source_anchor_terms(text: str, limit: int = 8) -> list[str]:
    counts = Counter(tokenize_keywords(text))
    if not counts:
        return []
    return [token for token, _count in counts.most_common(limit)]


def semantic_anchor_matches(source_text: str, target_text: str, limit: int = 8) -> tuple[list[str], list[str]]:
    anchors = source_anchor_terms(source_text, limit=limit)
    if not anchors:
        return [], []
    target_lower = target_text.lower()
    matched = [
        term for term in anchors
        if re.search(rf"(?<![a-z0-9]){re.escape(term)}(?![a-z0-9])", target_lower)
    ]
    return anchors, matched


def validation_retry_sequence(run_dir: Path, node: str) -> int:
    counters_dir = run_dir / "validation-retries"
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


def load_command_source_context(command_meta: dict[str, str]) -> str:
    parts: list[str] = []
    source_outbox = command_meta.get("Flow source outbox", "").strip()
    if source_outbox:
        path = (ROOT / source_outbox).resolve() if not source_outbox.startswith("/") else Path(source_outbox)
        try:
            if path.exists():
                parts.append(path.read_text(encoding="utf-8", errors="ignore"))
        except OSError:
            pass
    for key in (
        "Request summary",
        "Suggestion title",
        "Original user message",
        "Flow incoming conditions",
        "Product team label",
    ):
        value = command_meta.get(key, "").strip()
        if value:
            parts.append(value)
    return "\n".join(part for part in parts if part.strip())


def validate_flow_done_outbox(command_meta: dict[str, str], outbox_text: str) -> list[str]:
    errors: list[str] = []
    if first_nonempty_line(outbox_text)[:9].lower() != "- status:":
        errors.append("final outbox must start with '- Status:' on the first non-empty line")
    if has_transcript_markers(outbox_text):
        errors.append("final outbox must not contain tool-call or transcript markers")

    source_text = load_command_source_context(command_meta)
    anchors, matched = semantic_anchor_matches(source_text, outbox_text)
    if len(anchors) >= 4 and len(matched) < 2:
        errors.append(
            "final outbox appears semantically divergent from the upstream request "
            f"(matched anchors: {', '.join(matched) if matched else 'none'}; "
            f"expected anchors include: {', '.join(anchors[:5])})"
        )
    return errors


def queue_validation_retry(
    *,
    run_dir: Path,
    route_date: str,
    flow_id: str,
    run_id: str,
    current_node: str,
    owner_seat: str,
    original_command: str,
    source_roi: int,
    outbox_file: Path,
    errors: list[str],
) -> None:
    sequence = validation_retry_sequence(run_dir, current_node)
    item_name = (
        f"{route_date}-flow-{slugify(flow_id)}-{slugify(run_id)}-"
        f"{slugify(current_node)}-validation-r{sequence}"
    )[:180].rstrip("-")
    error_lines = "\n".join(f"- {error}" for error in errors)
    command_content = (
        original_command.rstrip()
        + "\n\n## Flow validation failure\n"
        + "The previous outbox did not pass flow-validation and was not routed.\n"
        + f"- Rejected outbox: {outbox_file}\n"
        + f"- Validation retry: {sequence}\n"
        + f"{error_lines}\n"
        + "- Produce final outbox markdown only, preserving continuity with the upstream request.\n"
    )
    create_inbox_item(owner_seat, item_name, max(source_roi + 25, 100), command_content)
    validation_dir = run_dir / "validation-failures"
    validation_dir.mkdir(parents=True, exist_ok=True)
    payload = {
        "node": current_node,
        "owner_seat": owner_seat,
        "rejected_outbox": str(outbox_file),
        "errors": errors,
        "retry_item": item_name,
        "created_at": datetime.utcnow().isoformat() + "Z",
    }
    (validation_dir / f"{slugify(current_node)}-r{sequence}.json").write_text(
        json.dumps(payload, indent=2) + "\n",
        encoding="utf-8",
    )


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


def node_detail_map(flow: dict[str, Any]) -> dict[str, dict[str, str]]:
    mapping: dict[str, dict[str, str]] = {}
    for item in flow.get("node_breakdown", []):
        if isinstance(item, dict):
            node = str(item.get("parent_node", "")).strip()
            owner = str(item.get("owner_seat", "")).strip()
            owner_binding = str(item.get("owner_binding", "")).strip()
            handoff_flow_id = str(item.get("handoff_flow_id", "")).strip()
            if node:
                mapping[node] = {
                    "owner_seat": owner,
                    "owner_binding": owner_binding,
                    "handoff_flow_id": handoff_flow_id,
                }
    return mapping


def load_product_teams() -> list[dict[str, Any]]:
    if not PRODUCT_TEAMS_PATH.exists():
        return []
    try:
        payload = json.loads(PRODUCT_TEAMS_PATH.read_text(encoding="utf-8"))
    except (OSError, json.JSONDecodeError):
        return []
    teams = payload.get("teams", [])
    return [team for team in teams if isinstance(team, dict)]


def resolve_product_team(team_hint: str, teams: list[dict[str, Any]]) -> dict[str, Any] | None:
    team_hint = team_hint.strip().lower()
    if not team_hint:
        return None
    for team in teams:
        aliases = [str(alias).strip().lower() for alias in team.get("aliases", []) if str(alias).strip()]
        candidates = {
            str(team.get("id", "")).strip().lower(),
            str(team.get("label", "")).strip().lower(),
            *aliases,
        }
        if team_hint in candidates:
            return team
    return None


def load_saved_product_team(run_dir: Path) -> dict[str, Any] | None:
    path = run_dir / "product-team.json"
    if not path.exists():
        return None
    try:
        payload = json.loads(path.read_text(encoding="utf-8"))
    except (OSError, json.JSONDecodeError):
        return None
    return payload if isinstance(payload, dict) else None


def save_product_team(run_dir: Path, product_team: dict[str, Any]) -> None:
    path = run_dir / "product-team.json"
    path.write_text(json.dumps(product_team, indent=2) + "\n", encoding="utf-8")


def resolve_owner(binding_details: dict[str, str], product_team: dict[str, Any] | None) -> tuple[str, str]:
    owner_seat = binding_details.get("owner_seat", "").strip()
    owner_binding = binding_details.get("owner_binding", "").strip()
    if owner_seat:
        return owner_seat, owner_binding
    if owner_binding.startswith("product_team.") and product_team is not None:
        key = owner_binding.split(".", 1)[1]
        resolved = str(product_team.get(key, "")).strip()
        return resolved, owner_binding
    return "", owner_binding


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
    target_owner_binding: str,
    source_agent: str,
    source_node: str,
    source_outbox: Path,
    incoming_conditions: list[str],
    available_outcomes: list[str],
    product_team: dict[str, Any] | None,
    product_team_selection_required: bool,
    available_product_teams: list[str],
    direct_route_available: bool,
) -> str:
    metadata = [
        f"- Flow id: {flow_id}",
        f"- Flow run id: {run_id}",
        f"- Flow node: {target_node}",
        f"- Flow owner seat: {target_owner}",
        f"- Flow previous node: {source_node}",
        f"- Flow source outbox: {source_outbox}",
    ]
    if target_owner_binding:
        metadata.append(f"- Flow owner binding: {target_owner_binding}")
    if product_team is not None:
        metadata.append(f"- Product team id: {str(product_team.get('id', '')).strip()}")
        metadata.append(f"- Product team label: {str(product_team.get('label', '')).strip()}")
    if product_team_selection_required:
        metadata.append("- Product team selection required: yes")
        if available_product_teams:
            metadata.append(f"- Available product teams: {' | '.join(available_product_teams)}")
    if incoming_conditions:
        metadata.append(f"- Flow incoming conditions: {' | '.join(incoming_conditions)}")
    if available_outcomes:
        metadata.append(f"- Available flow outcomes: {' | '.join(available_outcomes)}")
    if direct_route_available:
        metadata.append("- Flow direct route available: yes")

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
            "5. If the work is complete but needs a graph-defined branch (for example scope rebaseline, QA failure, or requested changes), keep `- Status: done` and use the matching `- Flow outcome:` line instead of escalating through a legacy `needs-*` artifact.",
            "6. If product-team selection is required for this node, include `- Product team id: <team-id>` using one of the listed product-team IDs.",
        ]
    ) + "\n"


def route_to_node(
    *,
    flow: dict[str, Any],
    flow_id: str,
    run_id: str,
    route_date: str,
    target_node: str,
    source_agent: str,
    source_node: str,
    source_outbox: Path,
    incoming_conditions: list[str],
    product_team: dict[str, Any] | None,
    teams: list[dict[str, Any]],
    node_details: dict[str, dict[str, str]],
    roi: int,
    run_dir: Path,
) -> bool:
    target_owner, target_owner_binding = resolve_owner(node_details.get(target_node, {}), product_team)
    if not target_owner:
        if target_owner_binding:
            log(f"skip target {target_node}: unresolved owner binding {target_owner_binding}")
        else:
            log(f"skip target {target_node}: no owner metadata")
        return False

    sequence = next_sequence(run_dir, target_node)
    item_name_out = routed_item_name(route_date, flow_id, run_id, target_node, sequence)
    next_outgoing = outgoing_transitions(flow, target_node)
    available_outcomes = [item["condition"] for item in next_outgoing if item["condition"]]
    direct_route_available = any(item["condition"] == "" for item in next_outgoing)
    product_team_selection_required = node_requires_product_team(flow, target_node, node_details, product_team)
    command_content = build_command(
        flow_id=flow_id,
        run_id=run_id,
        target_node=target_node,
        target_owner=target_owner,
        target_owner_binding=target_owner_binding,
        source_agent=source_agent,
        source_node=source_node,
        source_outbox=source_outbox,
        incoming_conditions=incoming_conditions,
        available_outcomes=available_outcomes,
        product_team=product_team,
        product_team_selection_required=product_team_selection_required,
        available_product_teams=[str(team.get("id", "")).strip() for team in teams if str(team.get("id", "")).strip()],
        direct_route_available=direct_route_available,
    )
    create_inbox_item(target_owner, item_name_out, roi, command_content)
    return True


def route_downstream_flow(
    *,
    handoff_flow_id: str,
    run_id: str,
    route_date: str,
    source_agent: str,
    source_node: str,
    source_outbox: Path,
    product_team: dict[str, Any] | None,
    teams: list[dict[str, Any]],
    roi: int,
) -> bool:
    downstream_flow = load_flow(handoff_flow_id)
    if downstream_flow is None:
        log(f"skip downstream launch for {handoff_flow_id}: flow lookup failed")
        return False
    entry_node = str(downstream_flow.get("default_entrypoint", "")).strip()
    if not entry_node:
        log(f"skip downstream launch for {handoff_flow_id}: missing default entrypoint")
        return False
    downstream_run_dir = flow_runtime_dir(handoff_flow_id, run_id)
    downstream_run_dir.mkdir(parents=True, exist_ok=True)
    if product_team is not None:
        save_product_team(downstream_run_dir, product_team)
    downstream_details = node_detail_map(downstream_flow)
    routed = route_to_node(
        flow=downstream_flow,
        flow_id=handoff_flow_id,
        run_id=run_id,
        route_date=route_date,
        target_node=entry_node,
        source_agent=source_agent,
        source_node=f"{source_node} ({handoff_flow_id} launch)",
        source_outbox=source_outbox,
        incoming_conditions=[],
        product_team=product_team,
        teams=teams,
        node_details=downstream_details,
        roi=roi,
        run_dir=downstream_run_dir,
    )
    if routed:
        log(f"launched downstream flow {handoff_flow_id}/{run_id} at {entry_node}")
    return routed


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
    direct = [transition for transition in outgoing if transition["condition"] == ""]
    if outcomes:
        selected: list[dict[str, str]] = []
        for transition in outgoing:
            if transition["condition"] in outcomes:
                selected.append(transition)
        return selected
    if direct:
        return direct
    return []


def node_requires_product_team(
    flow: dict[str, Any],
    node: str,
    node_details: dict[str, dict[str, str]],
    product_team: dict[str, Any] | None,
) -> bool:
    if product_team is not None:
        return False
    for transition in outgoing_transitions(flow, node):
        target = transition["to_node"]
        if target in {"", "END", "__end__"}:
            continue
        binding = node_details.get(target, {}).get("owner_binding", "").strip()
        if binding.startswith("product_team."):
            return True
    return False


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

    teams = load_product_teams()
    outbox_meta = parse_simple_metadata(outbox_text)
    product_team_hint = outbox_meta.get("Product team id", "").strip() or command_meta.get("Product team id", "").strip()
    product_team = resolve_product_team(product_team_hint, teams) if product_team_hint else None
    if product_team is not None:
        save_product_team(run_dir, product_team)
    else:
        product_team = load_saved_product_team(run_dir)
        if product_team_hint:
            log(f"unknown product team '{product_team_hint}' for {flow_id}/{run_id}")

    node_details = node_detail_map(flow)
    outgoing = outgoing_transitions(flow, current_node)
    transitions = selected_transitions(outgoing, extract_flow_outcomes(outbox_text))
    if outgoing and not transitions:
        log(f"no matching flow outcome for {flow_id}/{current_node}; no handoff created")
        return 0

    source_item_roi = read_item_roi(command_path.parent, 20)
    roi = max(source_item_roi, extract_roi(outbox_text, source_item_roi))
    route_date = route_date_for_item(item_name)
    validation_errors = validate_flow_done_outbox(command_meta, outbox_text)
    if validation_errors:
        queue_validation_retry(
            run_dir=run_dir,
            route_date=route_date,
            flow_id=flow_id,
            run_id=run_id,
            current_node=current_node,
            owner_seat=command_meta.get("Flow owner seat", "").strip() or agent_id,
            original_command=command_path.read_text(encoding="utf-8", errors="ignore"),
            source_roi=roi,
            outbox_file=outbox_file,
            errors=validation_errors,
        )
        log(f"validation failed for {flow_id}/{run_id}/{current_node}; retry queued")
        return 0

    for transition in transitions:
        target_node = transition["to_node"]
        if not target_node or target_node in {"END", "__end__"}:
            (run_dir / "completed.json").write_text(
                json.dumps({"completed_from": current_node, "source_outbox": str(outbox_file)}, indent=2) + "\n",
                encoding="utf-8",
            )
            handoff_flow_id = node_details.get(current_node, {}).get("handoff_flow_id", "").strip()
            if handoff_flow_id:
                route_downstream_flow(
                    handoff_flow_id=handoff_flow_id,
                    run_id=run_id,
                    route_date=route_date,
                    source_agent=agent_id,
                    source_node=current_node,
                    source_outbox=outbox_file,
                    product_team=product_team,
                    teams=teams,
                    roi=roi,
                )
            log(f"flow {flow_id}/{run_id} completed at {current_node}")
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

        route_to_node(
            flow_id=flow_id,
            flow=flow,
            run_id=run_id,
            route_date=route_date,
            target_node=target_node,
            source_agent=agent_id,
            source_node=current_node,
            source_outbox=outbox_file,
            incoming_conditions=incoming_conditions or ([transition["condition"]] if transition["condition"] else []),
            product_team=product_team,
            teams=teams,
            node_details=node_details,
            roi=roi,
            run_dir=run_dir,
        )
        clear_merge_receipts(run_dir, target_node)

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
