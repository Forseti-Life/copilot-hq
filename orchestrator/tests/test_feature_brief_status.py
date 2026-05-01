from pathlib import Path
import sys

sys.path.insert(0, str(Path(__file__).resolve().parents[2] / "scripts" / "lib"))
from feature_brief_status import promote_feature_brief_to_ready


def test_promote_feature_brief_to_ready_from_deferred(tmp_path):
    feature_brief = tmp_path / "feature.md"
    feature_brief.write_text(
        "# Feature Brief: Ritual Magic System\n\n"
        "- Work item id: dc-cr-rituals\n"
        "- Website: dungeoncrawler\n"
        "- Status: deferred\n"
        "- Defer reason: waiting for next grooming cycle\n"
        "\n"
        "## Latest updates\n"
        "\n"
        "- 2026-04-01: Deferred pending review.\n",
        encoding="utf-8",
    )

    updated = promote_feature_brief_to_ready(feature_brief, note_date="2026-05-01")

    assert "- Status: ready" in updated
    assert "- Status: deferred" not in updated
    assert "2026-05-01: Grooming complete" in updated


def test_promote_feature_brief_to_ready_keeps_existing_ready(tmp_path):
    feature_brief = tmp_path / "feature.md"
    feature_brief.write_text(
        "# Feature Brief: Skill Feats\n\n"
        "- Work item id: dc-cr-skill-feats\n"
        "- Website: dungeoncrawler\n"
        "- Status: ready\n",
        encoding="utf-8",
    )

    updated = promote_feature_brief_to_ready(feature_brief, note_date="2026-05-01")

    assert updated.count("- Status: ready") == 1
