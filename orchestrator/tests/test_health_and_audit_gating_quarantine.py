import importlib.util
from pathlib import Path


MODULE = Path(__file__).resolve().parents[1] / "health_and_audit.py"


def _load_module():
    spec = importlib.util.spec_from_file_location("health_and_audit", MODULE)
    module = importlib.util.module_from_spec(spec)
    assert spec.loader is not None
    spec.loader.exec_module(module)
    return module


def test_gating_quarantine_ignores_feature_scoped_pm_handoff(tmp_path):
    mod = _load_module()
    root = tmp_path / "hq"

    (root / "org-chart" / "products").mkdir(parents=True)
    (root / "tmp" / "release-cycle-active").mkdir(parents=True)
    (root / "features" / "forseti-langgraph-console-admin").mkdir(parents=True)
    (root / "sessions" / "pm-forseti" / "outbox").mkdir(parents=True)
    (root / "sessions" / "ceo-copilot-2" / "inbox").mkdir(parents=True)

    (root / "org-chart" / "products" / "product-teams.json").write_text(
        '{"teams":[{"id":"forseti","pm_agent":"pm-forseti","active":true}]}',
        encoding="utf-8",
    )
    (root / "tmp" / "release-cycle-active" / "forseti.release_id").write_text(
        "20260412-forseti-release-q\n",
        encoding="utf-8",
    )
    (root / "features" / "forseti-langgraph-console-admin" / "feature.md").write_text(
        "- Release: 20260412-forseti-release-q\n",
        encoding="utf-8",
    )
    (root / "sessions" / "pm-forseti" / "outbox" / "20260419-groom-20260412-forseti-release-q.md").write_text(
        "- Status: done\n",
        encoding="utf-8",
    )
    (root / "sessions" / "pm-forseti" / "outbox" / "20260420-needs-dev-forseti-20260420-164124-impl-forseti-langgraph-console-admin.md").write_text(
        "- Status: needs-info\n",
        encoding="utf-8",
    )

    state = root / "tmp" / "orchestrator-quarantine-escalate-last"
    mod.escalate_quarantined_gating_agents(root, state)

    assert not any((root / "sessions" / "ceo-copilot-2" / "inbox").iterdir())
