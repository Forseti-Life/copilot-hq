import json
import importlib.util
import textwrap
from datetime import datetime, timedelta, timezone
from pathlib import Path

SCRIPT = Path(__file__).resolve().parents[1] / "dispatch.py"
SPEC = importlib.util.spec_from_file_location("dispatch_module", SCRIPT)
dispatch = importlib.util.module_from_spec(SPEC)
assert SPEC and SPEC.loader
SPEC.loader.exec_module(dispatch)


def _write_product_team(root: Path, *, team_id: str, site: str, pm: str, dev: str, qa: str) -> None:
    path = root / "org-chart" / "products"
    path.mkdir(parents=True, exist_ok=True)
    payload = {
        "teams": [
            {
                "id": team_id,
                "label": team_id.title(),
                "site": site,
                "pm_agent": pm,
                "dev_agent": dev,
                "qa_agent": qa,
                "active": True,
                "release_preflight_enabled": True,
            }
        ]
    }
    (path / "product-teams.json").write_text(json.dumps(payload, indent=2) + "\n", encoding="utf-8")


def _write_feature(
    root: Path,
    *,
    feature_id: str,
    site: str,
    release_id: str,
    status: str,
    dev_owner: str,
    qa_owner: str,
) -> None:
    feature_dir = root / "features" / feature_id
    feature_dir.mkdir(parents=True, exist_ok=True)
    (feature_dir / "feature.md").write_text(
        textwrap.dedent(
            f"""\
            # Feature Brief

            - Work item id: {feature_id}
            - Website: {site}
            - Status: {status}
            - Release: {release_id}
            - Dev owner: {dev_owner}
            - QA owner: {qa_owner}
            """
        ),
        encoding="utf-8",
    )
    (feature_dir / "01-acceptance-criteria.md").write_text("# Acceptance criteria\n", encoding="utf-8")
    (feature_dir / "03-test-plan.md").write_text("# Test plan\n", encoding="utf-8")


def _setup_dispatch(root: Path) -> None:
    dispatch.setup(
        root,
        root / "tmp" / "signoff-reminder-state.json",
        root / "tmp" / "proactive-signoff-state.json",
        root / "tmp" / "release-close-state",
    )


def test_scope_activate_nudge_dispatches_pm_item_for_ready_backlog(tmp_path):
    root = tmp_path / "hq"
    (root / "features").mkdir(parents=True)
    (root / "sessions").mkdir()
    active_dir = root / "tmp" / "release-cycle-active"
    active_dir.mkdir(parents=True)
    release_id = "20990101-dungeoncrawler-release-z"
    (active_dir / "dungeoncrawler.release_id").write_text(f"{release_id}\n", encoding="utf-8")
    started_at = datetime.now(timezone.utc) - timedelta(minutes=90)
    (active_dir / "dungeoncrawler.started_at").write_text(started_at.isoformat(), encoding="utf-8")
    _write_product_team(
        root,
        team_id="dungeoncrawler",
        site="dungeoncrawler",
        pm="pm-dungeoncrawler",
        dev="dev-dungeoncrawler",
        qa="qa-dungeoncrawler",
    )
    _write_feature(
        root,
        feature_id="dc-release-ready-feature",
        site="dungeoncrawler",
        release_id=release_id,
        status="ready",
        dev_owner="dev-dungeoncrawler",
        qa_owner="qa-dungeoncrawler",
    )

    _setup_dispatch(root)
    dispatch._dispatch_scope_activate_nudge()

    inbox = root / "sessions" / "pm-dungeoncrawler" / "inbox"
    items = list(inbox.glob("*-scope-activate-*"))
    assert len(items) == 1
    command = (items[0] / "command.md").read_text(encoding="utf-8")
    readme = (items[0] / "README.md").read_text(encoding="utf-8")
    assert release_id in readme
    assert "dc-release-ready-feature" in readme
    assert "pm-scope-activate.sh dungeoncrawler dc-release-ready-feature" in command
    assert "generated inbox artifact paths" in command
    assert "pm-scope-activate.sh dungeoncrawler <feature-id>" in readme


def test_feature_gap_remediation_restores_dev_handoff_even_if_qa_artifact_exists(tmp_path):
    root = tmp_path / "hq"
    (root / "features").mkdir(parents=True)
    (root / "sessions" / "qa-dungeoncrawler" / "outbox").mkdir(parents=True)
    active_dir = root / "tmp" / "release-cycle-active"
    active_dir.mkdir(parents=True)
    release_id = "20990101-dungeoncrawler-release-z"
    (active_dir / "dungeoncrawler.release_id").write_text(f"{release_id}\n", encoding="utf-8")
    _write_product_team(
        root,
        team_id="dungeoncrawler",
        site="dungeoncrawler",
        pm="pm-dungeoncrawler",
        dev="dev-dungeoncrawler",
        qa="qa-dungeoncrawler",
    )
    _write_feature(
        root,
        feature_id="dc-release-missing-dev",
        site="dungeoncrawler",
        release_id=release_id,
        status="in_progress",
        dev_owner="dev-dungeoncrawler",
        qa_owner="qa-dungeoncrawler",
    )
    (
        root
        / "sessions"
        / "qa-dungeoncrawler"
        / "outbox"
        / "20990101-suite-activate-dc-release-missing-dev.md"
    ).write_text(
        "- Status: done\n- Feature id: dc-release-missing-dev\n",
        encoding="utf-8",
    )

    _setup_dispatch(root)
    dispatch._dispatch_feature_gap_remediation()

    dev_inbox = root / "sessions" / "dev-dungeoncrawler" / "inbox"
    dev_items = list(dev_inbox.glob("*-auto-recover-impl-dc-release-missing-dev"))
    assert len(dev_items) == 1
    command = (dev_items[0] / "command.md").read_text(encoding="utf-8")
    assert "- Flow id: agentic_sdlc" in command
    assert "- Flow node: Generate Code" in command
    assert f"- Release id: {release_id}" in command

    qa_recovery_items = list((root / "sessions" / "qa-dungeoncrawler" / "inbox").glob("*-auto-recover-suite-activate-dc-release-missing-dev"))
    assert qa_recovery_items == []
