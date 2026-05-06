#!/usr/bin/env bash
# ceo-repo-health.sh — On-demand repository creep / duplication analysis.
#
# Scans the filesystem for git repositories, identifies remotes and GitHub
# mappings, groups duplicate local copies by upstream repo, and highlights
# likely creep roots (temp workspaces, session artifacts, repo-work copies,
# and unowned repos outside the repository ownership map).
#
# Exit 0 = no duplicate or creep findings
# Exit 1 = duplicate upstream mappings or likely creep found
#
# Usage:
#   bash scripts/ceo-repo-health.sh
#   bash scripts/ceo-repo-health.sh --json
#   bash scripts/ceo-repo-health.sh --report-dir /tmp/dungeoncrawler-rca
#   bash scripts/ceo-repo-health.sh --scan-root /home/ubuntu
set -euo pipefail

ROOT_DIR="${HQ_ROOT_DIR:-$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)}"
cd "$ROOT_DIR"

SCAN_ROOT="/"
REPORT_DIR=""
JSON_MODE=0

while [[ $# -gt 0 ]]; do
  case "$1" in
    --scan-root)
      SCAN_ROOT="${2:?missing value for --scan-root}"
      shift 2
      ;;
    --report-dir)
      REPORT_DIR="${2:?missing value for --report-dir}"
      shift 2
      ;;
    --json)
      JSON_MODE=1
      shift
      ;;
    --help|-h)
      sed -n '1,16p' "$0"
      exit 0
      ;;
    *)
      echo "Unknown argument: $1" >&2
      exit 2
      ;;
  esac
done

python3 - "$ROOT_DIR" "$SCAN_ROOT" "$REPORT_DIR" "$JSON_MODE" <<'PY'
from __future__ import annotations

import csv
import json
import os
import pathlib
import re
import subprocess
import sys
from collections import Counter, defaultdict

ROOT_DIR = pathlib.Path(sys.argv[1])
SCAN_ROOT = pathlib.Path(sys.argv[2])
REPORT_DIR = pathlib.Path(sys.argv[3]) if sys.argv[3] else None
JSON_MODE = sys.argv[4] == "1"

SKIP_PREFIXES = (
    "/proc",
    "/sys",
    "/dev",
    "/run",
)


def run_git(repo: pathlib.Path, *args: str) -> str:
    return subprocess.check_output(
        ["git", "-C", str(repo), *args],
        stderr=subprocess.DEVNULL,
        text=True,
    ).strip()


def sanitize_url(url: str) -> str:
    url = url.strip()
    if url.startswith("git@github.com:"):
        return "https://github.com/" + url.split(":", 1)[1]
    match = re.match(r"https?://([^@/]+@)?github\.com/(.+)$", url)
    if match:
        return "https://github.com/" + match.group(2)
    return url


def github_slug(url: str) -> str | None:
    clean = sanitize_url(url)
    if "github.com/" not in clean:
        return None
    slug = clean.split("github.com/", 1)[1]
    if slug.endswith(".git"):
        slug = slug[:-4]
    return slug


def parse_repository_ownership(path: pathlib.Path) -> dict[str, dict[str, str]]:
    ownership: dict[str, dict[str, str]] = {}
    current_repo = None
    local_path = None
    repo_type = None

    for raw_line in path.read_text(encoding="utf-8").splitlines():
        line = raw_line.rstrip()
        repo_match = re.match(r"^  ([A-Za-z0-9._-]+):\s*$", line)
        if repo_match:
            if current_repo and local_path:
                ownership[local_path] = {"name": current_repo, "repo_type": repo_type or ""}
            current_repo = repo_match.group(1)
            local_path = None
            repo_type = None
            continue
        if current_repo is None:
            continue
        local_match = re.match(r'^    local_path:\s+"([^"]+)"\s*$', line)
        if local_match:
            local_path = local_match.group(1)
            continue
        type_match = re.match(r'^    repo_type:\s+"([^"]+)"\s*$', line)
        if type_match:
            repo_type = type_match.group(1)

    if current_repo and local_path:
        ownership[local_path] = {"name": current_repo, "repo_type": repo_type or ""}
    return ownership


def classify_path(repo_path: str, ownership: dict[str, dict[str, str]]) -> tuple[str, str]:
    if repo_path in ownership:
        info = ownership[repo_path]
        repo_type = info.get("repo_type") or "owned"
        return f"owned:{repo_type}", info.get("name", "")
    if "/vendor/" in repo_path:
        return "dependency-vendor", ""
    if repo_path.startswith("/tmp/"):
        return "temp-workspace", ""
    if repo_path.startswith("/root/.copilot/session-state/"):
        return "session-artifact", ""
    if repo_path.startswith("/home/ubuntu/repo-work/"):
        return "repo-work-copy", ""
    if repo_path.startswith("/root/") and repo_path.endswith("-push"):
        return "root-push-copy", ""
    return "unowned", ""


ownership_map = parse_repository_ownership(ROOT_DIR / "org-chart" / "ownership" / "repository-ownership.yaml")

found_repos: set[str] = set()
scan_root_str = str(SCAN_ROOT)
for base, dirs, files in os.walk(scan_root_str, topdown=True, followlinks=False):
    if any(base == skip or base.startswith(skip + "/") for skip in SKIP_PREFIXES):
        dirs[:] = []
        continue
    dirs[:] = [
        d
        for d in dirs
        if not any(
            os.path.join(base, d) == skip or os.path.join(base, d).startswith(skip + "/")
            for skip in SKIP_PREFIXES
        )
    ]
    if ".git" in dirs or ".git" in files:
        found_repos.add(base)
        dirs[:] = [d for d in dirs if d != ".git"]

rows = []
for repo_str in sorted(found_repos):
    repo = pathlib.Path(repo_str)
    try:
        remote_names = run_git(repo, "remote").splitlines()
    except subprocess.CalledProcessError:
        continue
    if not remote_names:
        remote_names = []

    remotes = []
    for name in remote_names:
        try:
            url = run_git(repo, "remote", "get-url", name)
        except subprocess.CalledProcessError:
            continue
        remotes.append((name, sanitize_url(url)))

    primary_remote_name = ""
    primary_remote_url = ""
    if remotes:
        if any(name == "origin" for name, _ in remotes):
            primary_remote_name, primary_remote_url = next((n, u) for n, u in remotes if n == "origin")
        else:
            primary_remote_name, primary_remote_url = remotes[0]

    primary_slug = github_slug(primary_remote_url) or ""
    github_remotes = [(name, url, github_slug(url)) for name, url in remotes if github_slug(url)]

    try:
        branch = run_git(repo, "rev-parse", "--abbrev-ref", "HEAD")
    except subprocess.CalledProcessError:
        branch = "(unknown)"
    if branch == "HEAD":
        try:
            branch = "detached@" + run_git(repo, "rev-parse", "--short", "HEAD")
        except subprocess.CalledProcessError:
            branch = "detached"

    status = "clean"
    try:
        if run_git(repo, "status", "--short"):
            status = "dirty"
    except subprocess.CalledProcessError:
        status = "unknown"

    classification, owned_name = classify_path(repo_str, ownership_map)
    rows.append({
        "path": repo_str,
        "branch": branch,
        "status": status,
        "primary_remote": primary_remote_name,
        "primary_remote_url": primary_remote_url,
        "primary_github_repo": primary_slug,
        "classification": classification,
        "owned_repo_key": owned_name,
        "all_remotes": "; ".join(f"{name}={url}" for name, url in remotes),
        "github_remotes": "; ".join(f"{name}={url}" for name, url, slug in github_remotes),
    })

rows.sort(key=lambda row: row["path"])

by_primary_repo: dict[str, list[dict[str, str]]] = defaultdict(list)
for row in rows:
    if row["primary_github_repo"]:
        by_primary_repo[row["primary_github_repo"]].append(row)

duplicate_groups = {
    repo: entries for repo, entries in by_primary_repo.items()
    if len(entries) > 1
}

creep_rows = [
    row for row in rows
    if row["classification"] in {"temp-workspace", "session-artifact", "repo-work-copy", "root-push-copy", "unowned"}
]

summary = {
    "scan_root": str(SCAN_ROOT),
    "total_git_repos": len(rows),
    "repos_with_github_primary": sum(1 for row in rows if row["primary_github_repo"]),
    "duplicate_primary_repo_groups": len(duplicate_groups),
    "duplicate_primary_repo_copies": sum(len(entries) for entries in duplicate_groups.values()),
    "creep_rows": len(creep_rows),
    "dirty_rows": sum(1 for row in rows if row["status"] == "dirty"),
    "classification_counts": dict(Counter(row["classification"] for row in rows)),
}

if REPORT_DIR:
    REPORT_DIR.mkdir(parents=True, exist_ok=True)
    tsv_path = REPORT_DIR / "repo-health-scan.tsv"
    md_path = REPORT_DIR / "repo-health-report.md"
    with tsv_path.open("w", newline="", encoding="utf-8") as handle:
        writer = csv.DictWriter(
            handle,
            fieldnames=[
                "path",
                "branch",
                "status",
                "classification",
                "owned_repo_key",
                "primary_remote",
                "primary_remote_url",
                "primary_github_repo",
                "github_remotes",
                "all_remotes",
            ],
            delimiter="\t",
        )
        writer.writeheader()
        writer.writerows(rows)

    lines = []
    lines.append("# CEO Repo Health Report")
    lines.append("")
    lines.append(f"- Scan root: `{SCAN_ROOT}`")
    lines.append(f"- Total git repos found: **{summary['total_git_repos']}**")
    lines.append(f"- Primary GitHub-mapped repos: **{summary['repos_with_github_primary']}**")
    lines.append(f"- Duplicate upstream groups: **{summary['duplicate_primary_repo_groups']}**")
    lines.append(f"- Likely creep rows: **{summary['creep_rows']}**")
    lines.append(f"- Dirty repos: **{summary['dirty_rows']}**")
    lines.append("")
    lines.append("## Classification summary")
    lines.append("")
    lines.append("| Classification | Count |")
    lines.append("| --- | ---: |")
    for key, count in sorted(summary["classification_counts"].items()):
        lines.append(f"| `{key}` | {count} |")

    lines.append("")
    lines.append("## Duplicate upstream mappings")
    lines.append("")
    if duplicate_groups:
        lines.append("| Upstream repo | Local copies | Paths |")
        lines.append("| --- | ---: | --- |")
        for repo, entries in sorted(duplicate_groups.items(), key=lambda item: (-len(item[1]), item[0])):
            paths = "<br>".join(f"`{entry['path']}`" for entry in entries)
            lines.append(f"| `{repo}` | {len(entries)} | {paths} |")
    else:
        lines.append("No duplicate upstream mappings found.")

    lines.append("")
    lines.append("## Likely repo creep / side workspaces")
    lines.append("")
    if creep_rows:
        lines.append("| Path | Classification | Upstream | Branch/HEAD |")
        lines.append("| --- | --- | --- | --- |")
        for row in creep_rows:
            upstream = row["primary_github_repo"] or row["primary_remote_url"] or "(no remote)"
            lines.append(f"| `{row['path']}` | `{row['classification']}` | `{upstream}` | `{row['branch']}` |")
    else:
        lines.append("No likely repo creep found.")

    lines.append("")
    lines.append("## Full inventory")
    lines.append("")
    lines.append("| Path | Classification | Upstream | Branch/HEAD | Status |")
    lines.append("| --- | --- | --- | --- | --- |")
    for row in rows:
        upstream = row["primary_github_repo"] or row["primary_remote_url"] or "(no remote)"
        lines.append(f"| `{row['path']}` | `{row['classification']}` | `{upstream}` | `{row['branch']}` | `{row['status']}` |")

    md_path.write_text("\n".join(lines) + "\n", encoding="utf-8")

has_findings = bool(duplicate_groups or creep_rows)

if JSON_MODE:
    print(json.dumps(summary, sort_keys=True))
else:
    print("═══════════════════════════════════════════════════════")
    print("  CEO Repo Health Check")
    print("═══════════════════════════════════════════════════════")
    print(f"Scan root: {SCAN_ROOT}")
    print(f"Git repos found: {summary['total_git_repos']}")
    print(f"Primary GitHub repos: {summary['repos_with_github_primary']}")
    print(f"Duplicate upstream groups: {summary['duplicate_primary_repo_groups']}")
    print(f"Likely creep rows: {summary['creep_rows']}")
    print(f"Dirty repos: {summary['dirty_rows']}")
    if REPORT_DIR:
        print(f"Report dir: {REPORT_DIR}")
    print("")
    if duplicate_groups:
        print("⚠️  WARN Duplicate upstream mappings:")
        for repo, entries in sorted(duplicate_groups.items(), key=lambda item: (-len(item[1]), item[0]))[:15]:
            print(f"   - {repo}: {len(entries)} copies")
    else:
        print("✅ PASS Duplicate upstream mappings: none")
    print("")
    if creep_rows:
        print("⚠️  WARN Likely repo creep / side workspaces:")
        for row in creep_rows[:20]:
            upstream = row["primary_github_repo"] or row["primary_remote_url"] or "(no remote)"
            print(f"   - {row['classification']}: {row['path']} -> {upstream}")
    else:
        print("✅ PASS Likely repo creep: none")

raise SystemExit(1 if has_findings else 0)
PY
