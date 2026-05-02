import json
from datetime import datetime, timezone
from pathlib import Path
import sys

import orchestrator.release_cycle as release_cycle_module
from orchestrator.release_cycle import run_release_cycle_step

sys.path.insert(0, str(Path(__file__).resolve().parents[2] / "scripts" / "lib"))
from release_cycle_helpers import ensure_runtime_release_defect


def test_release_cycle_stays_idle_without_actionable_work(tmp_path):
    root = tmp_path / "hq"
    teams_dir = root / "org-chart" / "products"
    teams_dir.mkdir(parents=True)
    (teams_dir / "product-teams.json").write_text(
        json.dumps(
            {
                "teams": [
                    {
                        "id": "forseti",
                        "label": "Forseti",
                        "site": "forseti.life",
                        "pm_agent": "pm-forseti",
                        "qa_agent": "qa-forseti",
                        "ba_agent": "ba-forseti",
                        "active": True,
                        "release_preflight_enabled": True,
                    }
                ]
            }
        ),
        encoding="utf-8",
    )
    (root / "features").mkdir(parents=True)
    active_dir = root / "tmp" / "release-cycle-active"
    active_dir.mkdir(parents=True)

    log: list[dict] = []
    run_release_cycle_step(log, root)

    today = datetime.now(timezone.utc).strftime("%Y%m%d")
    assert not (active_dir / "forseti.release_id").exists()
    assert (active_dir / "forseti.next_release_id").read_text().strip() == f"{today}-forseti-release"
    assert not (active_dir / "forseti.started_at").exists()
    assert log == [
        {
            "step": "release_cycle",
            "teams": [
                {
                    "team": "forseti",
                    "action": "idle_waiting_for_work",
                    "current": "",
                    "next": f"{today}-forseti-release",
                    "scoped_count": 0,
                    "ready_backlog_count": 0,
                    "deferred_backlog_count": 0,
                }
            ],
        }
    ]


def test_release_cycle_starts_when_runtime_findings_are_materialized(tmp_path):
    root = tmp_path / "hq"
    teams_dir = root / "org-chart" / "products"
    teams_dir.mkdir(parents=True)
    (teams_dir / "product-teams.json").write_text(
        json.dumps(
            {
                "teams": [
                    {
                        "id": "forseti",
                        "label": "Forseti",
                        "site": "forseti.life",
                        "pm_agent": "pm-forseti",
                        "qa_agent": "qa-forseti",
                        "dev_agent": "dev-forseti",
                        "ba_agent": "ba-forseti",
                        "active": True,
                        "release_preflight_enabled": True,
                    }
                ]
            }
        ),
        encoding="utf-8",
    )
    (root / "features").mkdir(parents=True)
    active_dir = root / "tmp" / "release-cycle-active"
    active_dir.mkdir(parents=True)

    materialized = ensure_runtime_release_defect(
        root,
        {
            "id": "forseti",
            "label": "Forseti",
            "site": "forseti.life",
            "pm_agent": "pm-forseti",
            "qa_agent": "qa-forseti",
            "dev_agent": "dev-forseti",
        },
        run_id="20260501-200109",
        open_issue_count=2,
        dev_latest_outbox="20260428-syshealth-tailoring-queue-errors.md",
    )
    feature_dir = root / "features" / materialized["feature_id"]
    assert (feature_dir / "feature.md").exists()
    assert (feature_dir / "01-acceptance-criteria.md").exists()
    assert (feature_dir / "03-test-plan.md").exists()
    assert "- Status: ready" in (feature_dir / "feature.md").read_text(encoding="utf-8")

    calls: list[list[str]] = []
    original_run = release_cycle_module._run

    def fake_run(cmd: list[str], *, timeout: int = 600):
        calls.append(cmd)
        return 0, "ok"

    release_cycle_module._run = fake_run
    try:
        log: list[dict] = []
        run_release_cycle_step(log, root)
    finally:
        release_cycle_module._run = original_run

    today = datetime.now(timezone.utc).strftime("%Y%m%d")
    assert calls == [[
        "bash",
        "scripts/release-cycle-start.sh",
        "forseti",
        f"{today}-forseti-release",
        f"{today}-forseti-release-next",
    ]]
    assert log == [
        {
            "step": "release_cycle",
            "teams": [
                {
                    "team": "forseti",
                    "action": "start",
                    "current": f"{today}-forseti-release",
                    "next": f"{today}-forseti-release-next",
                    "rc": 0,
                }
            ],
        }
    ]
