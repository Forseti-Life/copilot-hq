import json
import os
import subprocess
import textwrap
from pathlib import Path


SCRIPT = Path(__file__).resolve().parents[1] / "release-signoff.sh"


def _make_root(tmp_path: Path) -> tuple[Path, str]:
    root = tmp_path / "hq"
    release_id = "20260412-dungeoncrawler-release-p"

    (root / "org-chart" / "products").mkdir(parents=True)
    (root / "features").mkdir(parents=True)
    (root / "sessions" / "qa-dungeoncrawler" / "outbox").mkdir(parents=True)
    (root / "sessions" / "pm-dungeoncrawler" / "artifacts" / "release-candidates" / release_id).mkdir(parents=True)

    teams = {
        "teams": [
            {
                "id": "dungeoncrawler",
                "site": "dungeoncrawler",
                "aliases": ["dungeoncrawler", "dungeoncrawler.forseti.life"],
                "pm_agent": "pm-dungeoncrawler",
                "qa_agent": "qa-dungeoncrawler",
                "active": True,
                "coordinated_release_default": False,
            }
        ]
    }
    (root / "org-chart" / "products" / "product-teams.json").write_text(
        json.dumps(teams),
        encoding="utf-8",
    )
    (root / "sessions" / "qa-dungeoncrawler" / "outbox" / f"20260412-gate2-approve-{release_id}.md").write_text(
        f"{release_id} — APPROVE\n",
        encoding="utf-8",
    )
    return root, release_id


def _run(root: Path, release_id: str) -> subprocess.CompletedProcess[str]:
    env = os.environ.copy()
    env["HQ_ROOT_DIR"] = str(root)
    return subprocess.run(
        ["bash", str(SCRIPT), "dungeoncrawler", release_id],
        cwd=root,
        env=env,
        capture_output=True,
        text=True,
    )


def test_signoff_passes_with_exact_release_metadata(tmp_path):
    root, release_id = _make_root(tmp_path)
    (root / "sessions" / "pm-dungeoncrawler" / "artifacts" / "release-candidates" / release_id / "01-change-list.md").write_text(
        "### dc-b2-bestiary2\n",
        encoding="utf-8",
    )
    feature_dir = root / "features" / "dc-b2-bestiary2"
    feature_dir.mkdir(parents=True)
    (feature_dir / "feature.md").write_text(
        textwrap.dedent(
            f"""\
            # Feature Brief

            - Work item id: dc-b2-bestiary2
            - Website: dungeoncrawler.forseti.life
            - Status: done
            - Release: {release_id}
            """
        ),
        encoding="utf-8",
    )

    result = _run(root, release_id)

    assert result.returncode == 0, result.stdout + result.stderr
    signoff = root / "sessions" / "pm-dungeoncrawler" / "artifacts" / "release-signoffs" / f"{release_id}.md"
    assert signoff.is_file()
    assert "SIGNED_OFF" in result.stdout


def test_signoff_fails_when_change_list_feature_release_metadata_is_blank(tmp_path):
    root, release_id = _make_root(tmp_path)
    (root / "sessions" / "pm-dungeoncrawler" / "artifacts" / "release-candidates" / release_id / "01-change-list.md").write_text(
        "### dc-ancestry-system\n",
        encoding="utf-8",
    )
    feature_dir = root / "features" / "dc-ancestry-system"
    feature_dir.mkdir(parents=True)
    (feature_dir / "feature.md").write_text(
        textwrap.dedent(
            """\
            # Feature Brief

            - Work item id: dc-ancestry-system
            - Website: dungeoncrawler.forseti.life
            - Status: done
            - Release:
            """
        ),
        encoding="utf-8",
    )

    result = _run(root, release_id)

    assert result.returncode == 1, result.stdout + result.stderr
    assert "release metadata mismatch" in result.stderr
    assert "BLOCKED: PM signoff requires release-bound features" in result.stderr
