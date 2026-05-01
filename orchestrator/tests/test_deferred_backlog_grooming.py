from pathlib import Path
import sys

from orchestrator.release_cycle import site_has_incomplete_grooming_backlog

sys.path.insert(0, str(Path(__file__).resolve().parents[2] / "scripts" / "lib"))
from release_cycle_helpers import summarize_release_work


def _write_feature(root: Path, feature_id: str, *, site: str, status: str, release: str = "", extra: str = "") -> None:
    feature_dir = root / "features" / feature_id
    feature_dir.mkdir(parents=True, exist_ok=True)
    body = (
        f"# Feature Brief: {feature_id}\n\n"
        f"- Work item id: {feature_id}\n"
        f"- Website: {site}\n"
        f"- Status: {status}\n"
        f"- Release: {release}\n"
    )
    if extra:
        body += f"{extra}\n"
    (feature_dir / "feature.md").write_text(body, encoding="utf-8")


def test_site_has_incomplete_grooming_backlog_counts_deferred_items(tmp_path):
    root = tmp_path / "hq"
    _write_feature(
        root,
        "dc-cr-rituals",
        site="dungeoncrawler",
        status="deferred",
        extra="- Defer reason: waiting for explicit next-release re-evaluation",
    )
    feature_dir = root / "features" / "dc-cr-rituals"
    (feature_dir / "01-acceptance-criteria.md").write_text("ac\n", encoding="utf-8")
    (feature_dir / "03-test-plan.md").write_text("tp\n", encoding="utf-8")

    assert site_has_incomplete_grooming_backlog("dungeoncrawler", "20260412-dungeoncrawler-release-ab", root)


def test_summarize_release_work_reports_deferred_backlog_separately(tmp_path):
    root = tmp_path / "hq"
    _write_feature(
        root,
        "dc-cr-rituals",
        site="dungeoncrawler",
        status="deferred",
        extra="- Defer reason: waiting for explicit next-release re-evaluation",
    )
    summary = summarize_release_work(
        root,
        {"id": "dungeoncrawler", "site": "dungeoncrawler"},
        "20260412-dungeoncrawler-release-ab",
    )

    assert summary["ready_backlog_count"] == 0
    assert summary["deferred_backlog_count"] == 1
    assert not summary["has_actionable_work"]
