import json
import os
import subprocess
from pathlib import Path


SCRIPT = Path(__file__).resolve().parents[1] / "ceo-release-health.sh"


def _make_root(tmp_path: Path) -> Path:
    root = tmp_path / "hq"
    (root / "org-chart" / "products").mkdir(parents=True)
    (root / "tmp" / "release-cycle-active").mkdir(parents=True)
    (root / "features").mkdir(parents=True)
    (root / "sessions" / "qa-forseti" / "outbox").mkdir(parents=True)
    (root / "sessions" / "pm-forseti" / "artifacts" / "release-signoffs").mkdir(parents=True)

    teams = {
        "teams": [
            {
                "id": "forseti",
                "site": "forseti.life",
                "pm_agent": "pm-forseti",
                "qa_agent": "qa-forseti",
                "active": True,
                "coordinated_release_default": True,
            }
        ]
    }
    (root / "org-chart" / "products" / "product-teams.json").write_text(
        json.dumps(teams),
        encoding="utf-8",
    )

    release_id = "20260418-forseti-release-m"
    (root / "tmp" / "release-cycle-active" / "forseti.release_id").write_text(
        release_id + "\n",
        encoding="utf-8",
    )
    (root / "tmp" / "release-cycle-active" / "forseti.next_release_id").write_text(
        "20260418-forseti-release-n\n",
        encoding="utf-8",
    )
    (root / "tmp" / "release-cycle-active" / "forseti.started_at").write_text(
        "2026-04-18T12:00:00+00:00\n",
        encoding="utf-8",
    )
    (root / "sessions" / "pm-forseti" / "artifacts" / "release-signoffs" / f"{release_id}.md").write_text(
        "# PM signoff\n",
        encoding="utf-8",
    )
    (root / "sessions" / "qa-forseti" / "outbox" / f"20260418-000000-empty-release-self-cert-{release_id}.md").write_text(
        f"# Gate 2 Self-Certification — Empty Release\n\n{release_id} — APPROVE — empty release self-certified by PM\n",
        encoding="utf-8",
    )
    return root


def _run(root: Path) -> subprocess.CompletedProcess[str]:
    env = os.environ.copy()
    env["HQ_ROOT_DIR"] = str(root)
    return subprocess.run(
        ["bash", str(SCRIPT)],
        cwd=root,
        env=env,
        capture_output=True,
        text=True,
    )


def test_empty_release_self_cert_counts_as_gate2_evidence(tmp_path):
    root = _make_root(tmp_path)

    result = _run(root)

    assert result.returncode == 0, result.stdout + result.stderr
    assert "Gate 2 evidence:" in result.stdout
    assert "empty-release-self-cert" in result.stdout
    assert "PM signoff (pm-forseti): found" in result.stdout
    assert "All checks PASSED — release cycle is healthy" in result.stdout


def test_missing_advance_markers_fail_when_push_marker_exists(tmp_path):
    root = tmp_path / "hq"
    (root / "org-chart" / "products").mkdir(parents=True)
    (root / "tmp" / "release-cycle-active").mkdir(parents=True)
    (root / "tmp" / "auto-push-dispatched").mkdir(parents=True)
    (root / "features").mkdir(parents=True)
    (root / "sessions" / "qa-forseti" / "outbox").mkdir(parents=True)
    (root / "sessions" / "pm-forseti" / "artifacts" / "release-signoffs").mkdir(parents=True)
    (root / "sessions" / "qa-dungeoncrawler" / "outbox").mkdir(parents=True)
    (root / "sessions" / "pm-dungeoncrawler" / "artifacts" / "release-signoffs").mkdir(parents=True)

    teams = {
        "teams": [
            {
                "id": "forseti",
                "site": "forseti.life",
                "pm_agent": "pm-forseti",
                "qa_agent": "qa-forseti",
                "active": True,
                "coordinated_release_default": True,
            },
            {
                "id": "dungeoncrawler",
                "site": "dungeoncrawler.forseti.life",
                "pm_agent": "pm-dungeoncrawler",
                "qa_agent": "qa-dungeoncrawler",
                "active": True,
                "coordinated_release_default": True,
            },
        ]
    }
    (root / "org-chart" / "products" / "product-teams.json").write_text(json.dumps(teams), encoding="utf-8")

    forseti_rid = "20260412-forseti-release-u"
    dc_rid = "20260412-dungeoncrawler-release-w"
    (root / "tmp" / "release-cycle-active" / "forseti.release_id").write_text(forseti_rid + "\n", encoding="utf-8")
    (root / "tmp" / "release-cycle-active" / "forseti.next_release_id").write_text("20260412-forseti-release-v\n", encoding="utf-8")
    (root / "tmp" / "release-cycle-active" / "dungeoncrawler.release_id").write_text(dc_rid + "\n", encoding="utf-8")
    (root / "tmp" / "release-cycle-active" / "dungeoncrawler.next_release_id").write_text("20260412-dungeoncrawler-release-x\n", encoding="utf-8")

    (root / "sessions" / "pm-forseti" / "artifacts" / "release-signoffs" / f"{forseti_rid}.md").write_text("# PM signoff\n", encoding="utf-8")
    (root / "sessions" / "pm-dungeoncrawler" / "artifacts" / "release-signoffs" / f"{dc_rid}.md").write_text("# PM signoff\n", encoding="utf-8")
    (root / "sessions" / "pm-forseti" / "artifacts" / "release-signoffs" / f"{dc_rid}.md").write_text("# PM co-signoff\n", encoding="utf-8")
    (root / "sessions" / "pm-dungeoncrawler" / "artifacts" / "release-signoffs" / f"{forseti_rid}.md").write_text("# PM co-signoff\n", encoding="utf-8")
    (root / "sessions" / "qa-forseti" / "outbox" / f"20260418-gate2-{forseti_rid}.md").write_text(f"{forseti_rid}\nAPPROVE\n", encoding="utf-8")
    (root / "sessions" / "qa-dungeoncrawler" / "outbox" / f"20260418-gate2-{dc_rid}.md").write_text(f"{dc_rid}\nAPPROVE\n", encoding="utf-8")
    (root / "tmp" / "auto-push-dispatched" / f"{dc_rid}__{forseti_rid}.pushed").write_text("2026-04-27T12:37:40+00:00\n", encoding="utf-8")

    result = _run(root)

    assert result.returncode == 1, result.stdout + result.stderr
    assert f"Missing advance marker: {dc_rid}__{forseti_rid}.forseti.advanced" in result.stdout
    assert "post-coordinated-push.sh did not complete" in result.stdout
