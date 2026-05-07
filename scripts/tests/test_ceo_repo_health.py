import json
import os
import shutil
import subprocess
from pathlib import Path


SCRIPT = Path(__file__).resolve().parents[1] / "ceo-repo-health.sh"


def _init_repo(root: Path) -> None:
    root.mkdir(parents=True, exist_ok=True)
    subprocess.run(["git", "init", "-b", "main"], cwd=root, check=True, capture_output=True, text=True)
    subprocess.run(["git", "config", "user.email", "ceo@example.com"], cwd=root, check=True, capture_output=True, text=True)
    subprocess.run(["git", "config", "user.name", "CEO Test"], cwd=root, check=True, capture_output=True, text=True)
    (root / "README.md").write_text(f"{root.name}\n", encoding="utf-8")
    subprocess.run(["git", "add", "."], cwd=root, check=True, capture_output=True, text=True)
    subprocess.run(["git", "commit", "-m", "init"], cwd=root, check=True, capture_output=True, text=True)


def _make_hq_root(tmp_path: Path) -> Path:
    root = tmp_path / "hq"
    (root / "scripts").mkdir(parents=True)
    (root / "org-chart" / "ownership").mkdir(parents=True)
    shutil.copy2(SCRIPT, root / "scripts" / "ceo-repo-health.sh")
    os.chmod(root / "scripts" / "ceo-repo-health.sh", 0o755)
    (root / "org-chart" / "ownership" / "repository-ownership.yaml").write_text(
        "repositories:\n",
        encoding="utf-8",
    )
    return root


def test_repo_health_counts_nested_git_repositories(tmp_path):
    hq_root = _make_hq_root(tmp_path)
    scan_root = tmp_path / "workspace"

    _init_repo(scan_root / "parent")
    _init_repo(scan_root / "parent" / "nested")
    _init_repo(scan_root / "sibling")

    result = subprocess.run(
        ["bash", str(hq_root / "scripts" / "ceo-repo-health.sh"), "--scan-root", str(scan_root), "--json"],
        cwd=hq_root,
        capture_output=True,
        text=True,
        check=False,
        env={"PATH": "/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"},
    )

    assert result.returncode == 1
    summary = json.loads(result.stdout)
    assert summary["total_git_repos"] == 3


def test_repo_health_reconciles_github_org_inventory(tmp_path):
    hq_root = _make_hq_root(tmp_path)
    scan_root = tmp_path / "workspace"

    _init_repo(scan_root / "legacy-ai-conversation")
    subprocess.run(
        ["git", "remote", "add", "origin", "https://github.com/Forseti-Life/forseti-ai-conversation.git"],
        cwd=scan_root / "legacy-ai-conversation",
        check=True,
        capture_output=True,
        text=True,
    )
    _init_repo(scan_root / "copilot-hq")
    subprocess.run(
        ["git", "remote", "add", "origin", "https://github.com/Forseti-Life/copilot-hq.git"],
        cwd=scan_root / "copilot-hq",
        check=True,
        capture_output=True,
        text=True,
    )

    inventory_path = tmp_path / "org-repos.json"
    inventory_path.write_text(
        json.dumps(
            [
                {"name": ".github", "archived": False},
                {"name": "copilot-hq", "archived": False},
                {"name": "forseti-ai-conversation", "archived": False},
                {"name": "forseti-shared-modules", "archived": True},
            ]
        ),
        encoding="utf-8",
    )

    result = subprocess.run(
        [
            "bash",
            str(hq_root / "scripts" / "ceo-repo-health.sh"),
            "--scan-root",
            str(scan_root),
            "--github-org",
            "Forseti-Life",
            "--github-inventory",
            str(inventory_path),
            "--json",
        ],
        cwd=hq_root,
        capture_output=True,
        text=True,
        check=False,
        env={"PATH": "/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"},
    )

    assert result.returncode == 1
    summary = json.loads(result.stdout)
    assert summary["github_org_repo_total"] == 4
    assert summary["github_org_active_repo_total"] == 3
    assert summary["github_org_archived_repo_total"] == 1
    assert summary["missing_local_active_org_repos"] == ["Forseti-Life/.github"]
    assert summary["missing_local_archived_org_repos"] == ["Forseti-Life/forseti-shared-modules"]
    assert summary["local_name_mismatch_rows"] == 1
