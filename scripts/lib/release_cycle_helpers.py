#!/usr/bin/env python3
"""Shared helpers for coordinated release-cycle scripts."""

from __future__ import annotations

import json
import re
import shutil
from datetime import datetime, timezone
from pathlib import Path
from typing import Any


class TeamLookupError(RuntimeError):
    """Raised when a release-cycle team lookup fails."""


def load_product_teams(config_path: Path) -> list[dict[str, Any]]:
    with open(config_path, "r", encoding="utf-8") as fh:
        data = json.load(fh)
    return list(data.get("teams") or [])


def release_enabled_teams(config_path: Path) -> list[dict[str, Any]]:
    return sorted(
        [
            team
            for team in load_product_teams(config_path)
            if team.get("active") and team.get("release_preflight_enabled")
        ],
        key=lambda team: str(team.get("id") or ""),
    )


def coordinated_teams(config_path: Path) -> list[dict[str, Any]]:
    return sorted(
        [
            team
            for team in load_product_teams(config_path)
            if team.get("active") and team.get("coordinated_release_default")
        ],
        key=lambda team: str(team.get("id") or ""),
    )


def release_enabled_team_map(config_path: Path) -> dict[str, dict[str, Any]]:
    return {
        str(team.get("id") or "").strip(): team
        for team in release_enabled_teams(config_path)
        if str(team.get("id") or "").strip()
    }


def explicit_release_dependencies(team: dict[str, Any]) -> list[str]:
    values = team.get("release_dependencies")
    if not isinstance(values, list):
        return []
    deps: list[str] = []
    team_id = str(team.get("id") or "").strip()
    for value in values:
        dep = str(value or "").strip()
        if not dep or dep == team_id or dep in deps:
            continue
        deps.append(dep)
    return deps


def release_cohort(config_path: Path, team_id: str) -> list[dict[str, Any]]:
    team_map = release_enabled_team_map(config_path)
    normalized_team_id = str(team_id or "").strip()
    if not normalized_team_id or normalized_team_id not in team_map:
        return []

    primary = team_map[normalized_team_id]
    cohort_ids = [normalized_team_id]
    for dep in explicit_release_dependencies(primary):
        if dep in team_map and dep not in cohort_ids:
            cohort_ids.append(dep)

    return [team_map[cohort_id] for cohort_id in sorted(cohort_ids)]


def release_cohort_ids(config_path: Path, team_id: str) -> list[str]:
    return [str(team.get("id") or "").strip() for team in release_cohort(config_path, team_id)]


def lookup_active_team(config_path: Path, query: str) -> dict[str, Any]:
    normalized_query = (query or "").strip().lower()
    for team in load_product_teams(config_path):
        aliases = [
            str(alias).strip().lower()
            for alias in (team.get("aliases") or [])
            if str(alias).strip()
        ]
        team_id = str(team.get("id") or "").strip().lower()
        site = str(team.get("site") or "").strip().lower()
        if normalized_query not in aliases and normalized_query != team_id and normalized_query != site:
            continue
        if not team.get("active", False):
            raise TeamLookupError(f"team is not active for query '{normalized_query}'")
        if not team.get("release_preflight_enabled", False):
            raise TeamLookupError(f"release preflight disabled for team '{team.get('id')}'")
        qa_agent = str(team.get("qa_agent") or "").strip()
        normalized_site = str(team.get("site") or "").strip()
        if not qa_agent or not normalized_site:
            raise TeamLookupError(
                f"team '{team.get('id')}' missing qa_agent/site in registry"
            )
        return team
    raise TeamLookupError(
        f"unknown site/team alias: {normalized_query}\n"
        "Update org-chart/products/product-teams.json to onboard this team."
    )


def slugify(value: str, limit: int = 60) -> str:
    return re.sub(r"[^A-Za-z0-9._-]", "-", value or "").strip("-")[:limit]


def combined_release_marker_key(
    team_release_ids: dict[str, str], teams: list[dict[str, Any]], limit: int = 120
) -> str:
    return "__".join(
        slugify(team_release_ids[team["id"]], limit=80)
        for team in teams
        if team.get("id") in team_release_ids
    )[:limit]


def next_release_id_after(release_id: str, team_id: str, current_day: str) -> str:
    date_part = current_day
    suffix = "release"
    match = re.match(rf"^(\d{{8}})-{re.escape(team_id)}-(.+)$", release_id or "")
    if match:
        date_part = match.group(1)
        suffix = match.group(2)

    if suffix == "release":
        next_suffix = "release-next"
    elif suffix == "release-next":
        next_suffix = "release-b"
    else:
        label_match = re.fullmatch(r"release-([a-z]+)", suffix)
        if not label_match:
            next_suffix = "release-b"
        else:
            chars = list(label_match.group(1))
            idx = len(chars) - 1
            while idx >= 0 and chars[idx] == "z":
                chars[idx] = "a"
                idx -= 1
            if idx < 0:
                chars.insert(0, "a")
            else:
                chars[idx] = chr(ord(chars[idx]) + 1)
            next_suffix = f"release-{''.join(chars)}"

    return f"{date_part}-{team_id}-{next_suffix}"


def _team_site_tokens(team: dict[str, Any]) -> set[str]:
    tokens: set[str] = set()
    team_id = str(team.get("id") or "").strip().lower()
    site = str(team.get("site") or "").strip().lower()
    if team_id:
        tokens.add(team_id)
    if site:
        tokens.add(site)
        if site.endswith(".life"):
            tokens.add(site[: -len(".life")])
    for alias in team.get("aliases") or []:
        alias_text = str(alias or "").strip().lower()
        if alias_text:
            tokens.add(alias_text)
    return {token for token in tokens if token}


def summarize_release_work(
    root: Path, team: dict[str, Any], release_id: str
) -> dict[str, Any]:
    """Summarize whether a team has actionable work for a release.

    Actionable work means either:
    1. The candidate/current release already has scoped work (`in_progress` or `done`).
    2. The site has groomed backlog that can be scoped immediately (`ready` or `done`)
       and is either unassigned or already tagged to the candidate release.
    Deferred backlog is tracked separately for visibility, but is not actionable
    until PM reactivates it to a scoping-eligible state.
    """
    features_root = root / "features"
    if not features_root.exists():
        return {
            "scoped_count": 0,
            "ready_backlog_count": 0,
            "deferred_backlog_count": 0,
            "ready_feature_ids": [],
            "has_actionable_work": False,
        }

    tokens = _team_site_tokens(team)
    scoped_count = 0
    ready_feature_ids: list[str] = []
    deferred_feature_ids: list[str] = []

    for feature_md in sorted(features_root.glob("*/feature.md")):
        try:
            text = feature_md.read_text(encoding="utf-8", errors="ignore")
        except OSError:
            continue

        website_match = re.search(r"^-\s+Website:\s*(.+)$", text, re.MULTILINE | re.IGNORECASE)
        status_match = re.search(r"^-\s+Status:\s*(.+)$", text, re.MULTILINE | re.IGNORECASE)
        release_match = re.search(r"^-\s+Release:[ \t]*([^\n]*)$", text, re.MULTILINE | re.IGNORECASE)

        website = (website_match.group(1).strip().lower() if website_match else "")
        status = (status_match.group(1).strip().lower() if status_match else "")
        feature_release = (release_match.group(1).strip() if release_match else "")

        if not website or not any(token in website for token in tokens):
            continue

        if status in {"in_progress", "done"} and release_id and feature_release == release_id:
            scoped_count += 1

        if status in {"ready", "done"} and (not feature_release or feature_release == release_id):
            ready_feature_ids.append(feature_md.parent.name)
        elif status == "deferred":
            deferred_feature_ids.append(feature_md.parent.name)

    return {
        "scoped_count": scoped_count,
        "ready_backlog_count": len(ready_feature_ids),
        "deferred_backlog_count": len(deferred_feature_ids),
        "ready_feature_ids": ready_feature_ids,
        "has_actionable_work": bool(scoped_count or ready_feature_ids),
    }


def materialized_runtime_release_feature_id(team_id: str, run_id: str) -> str:
    run_slug = slugify(run_id.lower(), limit=40)
    return f"{team_id}-release-runtime-{run_slug}"


def ensure_runtime_release_defect(
    root: Path,
    team: dict[str, Any],
    *,
    run_id: str,
    open_issue_count: int,
    dev_latest_outbox: str = "",
    release_id: str = "",
    source_reason: str = "release-kpi-monitor",
) -> dict[str, Any]:
    """Materialize unresolved runtime findings as a durable release work item.

    The release-cycle opener only trusts tracked feature/defect artifacts. When the
    monitor detects post-dev findings without an active release, create a repo-native
    ready bugfix item so the normal release flow can pick it up on the next tick.
    """
    team_id = str(team.get("id") or "").strip()
    site = str(team.get("site") or team_id).strip() or team_id
    pm_agent = str(team.get("pm_agent") or f"pm-{team_id}").strip()
    qa_agent = str(team.get("qa_agent") or f"qa-{team_id}").strip()
    dev_agent = str(team.get("dev_agent") or f"dev-{team_id}").strip()
    team_label = str(team.get("label") or team_id).strip() or team_id
    feature_id = materialized_runtime_release_feature_id(team_id, run_id)
    feature_dir = root / "features" / feature_id
    feature_md = feature_dir / "feature.md"
    ac_md = feature_dir / "01-acceptance-criteria.md"
    test_plan_md = feature_dir / "03-test-plan.md"

    feature_dir.mkdir(parents=True, exist_ok=True)
    created = not feature_md.exists()
    now_iso = datetime.now(timezone.utc).isoformat().replace("+00:00", "Z")
    current_release = release_id.strip()
    release_line = current_release if current_release else ""
    latest_outbox_line = dev_latest_outbox.strip() or "none"

    if created:
        feature_md.write_text(
            (
                "# Feature Brief\n\n"
                f"- Work item id: {feature_id}\n"
                f"- Website: {site}\n"
                "- Module: release-ops\n"
                "- Project: PROJ-OPS\n"
                "- Group Order: 99\n"
                "- Group: release-runtime\n"
                "- Group Title: Release Runtime Recovery\n"
                "- Group Sort: 99\n"
                "- Status: ready\n"
                f"- Release: {release_line}\n"
                "- Priority: P1\n"
                "- Feature type: bugfix\n"
                f"- PM owner: {pm_agent}\n"
                f"- Dev owner: {dev_agent}\n"
                f"- QA owner: {qa_agent}\n"
                f"- Source: {source_reason} {now_iso}\n"
                f"- Runtime run id: {run_id}\n"
                f"- Runtime open issues: {open_issue_count}\n"
                f"- Runtime latest dev outbox: {latest_outbox_line}\n\n"
                "## Summary\n\n"
                f"Release runtime findings for {team_label} remain unresolved after the latest QA/dev cycle. "
                "This defect item materializes those runtime-only findings into a tracked release work item so "
                "the standard PM/Dev/QA release flow can scope, verify, and close them.\n\n"
                "## Goal\n\n"
                f"Drive the unresolved runtime findings from QA run `{run_id}` to zero open issues using the normal "
                "release ceremony instead of leaving them as monitor-only alerts.\n\n"
                "## Acceptance criteria\n\n"
                f"- AC-1: The follow-up QA audit for run `{run_id}` (or its replacement rerun for the same issue set) reports 0 open issues.\n"
                f"- AC-2: Any code/config/content fixes required to close the {open_issue_count} currently open issue(s) are routed through the owning dev workflow.\n"
                "- AC-3: PM records the disposition of the original runtime findings in release artifacts or outbox notes.\n"
                "- AC-4: QA provides an explicit PASS/BLOCK verdict after rerun, rather than leaving the release in a monitor-only state.\n\n"
                "## Non-goals\n\n"
                "- Creating a parallel release process outside the normal feature/release workflow.\n"
                "- Leaving this item unscoped while the monitor continues to page on the same findings.\n\n"
                "## Security acceptance criteria\n\n"
                "- Authentication/permission surface: No new user-facing surface is introduced by this tracking artifact.\n"
                "- CSRF expectations: Not applicable — release recovery work follows existing routes and forms only.\n"
                "- Input validation requirements: Any fix-forward work must preserve existing validation and permissions for the affected surfaces.\n"
                "- PII/logging constraints: Do not add new logs that expose user-submitted content or private runtime payloads while investigating the findings.\n"
            ),
            encoding="utf-8",
        )

    if not ac_md.exists():
        ac_md.write_text(
            (
                f"# Acceptance Criteria — {feature_id}\n\n"
                f"1. QA rerun evidence for `{run_id}` shows zero remaining open issues.\n"
                "2. Any fix-forward implementation references the owning dev outbox item or commit hash.\n"
                "3. PM records whether the original findings were resolved, deferred with rationale, or superseded by a new rerun.\n"
            ),
            encoding="utf-8",
        )

    if not test_plan_md.exists():
        test_plan_md.write_text(
            (
                f"# Test Plan — {feature_id}\n\n"
                "## Validation steps\n\n"
                f"1. Re-run the relevant site audit / QA workflow for runtime run `{run_id}`.\n"
                "2. Confirm the previously open findings are either resolved or explicitly replaced by a new tracked item.\n"
                "3. Verify PM/QA produce an explicit close-out artifact instead of relying on the KPI monitor alone.\n"
            ),
            encoding="utf-8",
        )

    return {"feature_id": feature_id, "created": created, "path": str(feature_dir)}


def has_groom_item(root: Path, pm_agent: str, next_release_id: str) -> bool:
    slug = slugify(next_release_id)
    inbox = root / "sessions" / pm_agent / "inbox"
    outbox = root / "sessions" / pm_agent / "outbox"
    if inbox.exists():
        for item in inbox.iterdir():
            if item.is_dir() and item.name != "_archived" and item.name.endswith(f"-groom-{slug}"):
                return True
    if outbox.exists():
        for item in outbox.glob(f"*-groom-{slug}.md"):
            if item.is_file():
                return True
    return False


def archive_inbox_dir(item_dir: Path) -> None:
    archive_root = item_dir.parent / "_archived"
    archive_root.mkdir(parents=True, exist_ok=True)
    target = archive_root / item_dir.name
    if target.exists():
        shutil.rmtree(target)
    item_dir.rename(target)


def _superseded_outbox_body(
    item_name: str,
    *,
    current_release_id: str,
    old_release_ids: list[str],
    prior_text: str,
) -> str:
    old_ids = ", ".join(f"`{rid}`" for rid in old_release_ids if rid) or "(unknown prior release)"
    body = (
        "- Status: done\n"
        f"- Summary: Superseded by coordinated release advancement. This PM inbox item still referenced prior release state ({old_ids}), but the live release boundary has already moved forward to `{current_release_id}`. The underlying release transition was completed by CEO/orchestrator backstop, so this item is closed instead of being worked further.\n\n"
        "## Next actions\n"
        "- Continue with the current live release-cycle inbox items seeded after advancement.\n\n"
        "## Blockers\n"
        "- None\n\n"
        "## Superseded by\n"
        "- Actor: CEO/orchestrator release-advance automation\n"
        f"- Current release: `{current_release_id}`\n"
        f"- Prior release references: {old_ids}\n"
    )
    if prior_text.strip():
        body += f"\n## Prior outbox content\n\n{prior_text.strip()}\n"
    return body


def write_superseded_outbox(
    root: Path,
    pm_agent: str,
    item_name: str,
    *,
    current_release_id: str,
    old_release_ids: list[str],
) -> None:
    outbox_dir = root / "sessions" / pm_agent / "outbox"
    outbox_dir.mkdir(parents=True, exist_ok=True)
    outbox_path = outbox_dir / f"{item_name}.md"
    prior_text = ""
    if outbox_path.exists():
        prior_text = outbox_path.read_text(encoding="utf-8", errors="ignore")
    outbox_path.write_text(
        _superseded_outbox_body(
            item_name,
            current_release_id=current_release_id,
            old_release_ids=old_release_ids,
            prior_text=prior_text,
        ),
        encoding="utf-8",
    )


def archive_stale_pm_release_items(
    root: Path,
    pm_agent: str,
    *,
    old_release_ids: list[str],
    current_release_id: str,
) -> list[str]:
    inbox = root / "sessions" / pm_agent / "inbox"
    if not inbox.exists():
        return []

    archived: list[str] = []
    old_release_ids = [rid for rid in old_release_ids if rid]
    release_bound_tokens = (
        "release-close-now",
        "signoff-reminder",
        "coordinated-signoff",
        "push-ready",
        "post-push",
    )

    for item in sorted(inbox.iterdir()):
        if not item.is_dir() or item.name == "_archived":
            continue
        name = item.name

        should_archive = False
        if any(token in name for token in release_bound_tokens):
            should_archive = any(rid in name for rid in old_release_ids)
        elif "groom" in name:
            should_archive = any(rid in name for rid in [*old_release_ids, current_release_id] if rid)
        elif "scope-activate" in name:
            should_archive = any(rid in name for rid in old_release_ids)

        if should_archive:
            write_superseded_outbox(
                root,
                pm_agent,
                name,
                current_release_id=current_release_id,
                old_release_ids=old_release_ids,
            )
            archive_inbox_dir(item)
            archived.append(name)

    return archived
