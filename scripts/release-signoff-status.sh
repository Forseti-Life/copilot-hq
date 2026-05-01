#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="${HQ_ROOT_DIR:-$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)}"
cd "$ROOT_DIR"

PRODUCT_TEAMS_JSON="org-chart/products/product-teams.json"

release_id="${1:-}"
fmt="${2:-}"

if [ -z "$release_id" ]; then
  echo "Usage: $0 <release-id> [--json]" >&2
  exit 2
fi

slug="$(printf '%s' "$release_id" | tr -cs 'A-Za-z0-9._-' '-' | sed 's/^-//;s/-$//' | cut -c1-80)"

if [ ! -f "$PRODUCT_TEAMS_JSON" ]; then
  echo "ERROR: missing product team registry: $PRODUCT_TEAMS_JSON" >&2
  exit 2
fi

if ! rows="$(python3 - "$PRODUCT_TEAMS_JSON" "$release_id" <<'PY'
import sys
from pathlib import Path

root = Path.cwd()
sys.path.insert(0, str(root / "scripts" / "lib"))

from release_cycle_helpers import load_product_teams, release_cohort

config_path = Path(sys.argv[1])
release_id = str(sys.argv[2] or "").strip().lower()

teams = load_product_teams(config_path)
team_ids: list[str] = []
best_len = 0
for team in teams:
    if not team.get("active", False):
        continue
    candidates = [str(team.get("id") or "").strip().lower()]
    candidates.extend(str(alias or "").strip().lower() for alias in (team.get("aliases") or []))
    for cand in candidates:
        if cand and cand in release_id and len(cand) > best_len:
            best_len = len(cand)
            team_ids = [str(team.get("id") or "").strip()]

selected = []
if team_ids:
    selected = release_cohort(config_path, team_ids[0])
else:
    selected = [
        team
        for team in teams
        if team.get("active") and team.get("coordinated_release_default")
    ]

for team in selected:
    team_id = str(team.get('id') or '').strip()
    pm_agent = str(team.get('pm_agent') or '').strip()
    if not team_id or not pm_agent:
        continue
    print(f"{team_id}\t{pm_agent}")
PY
  2>&1)"; then
  echo "$rows" >&2
  exit 2
fi

if [ -z "$rows" ]; then
  echo "ERROR: no release-signoff PM seats resolved in $PRODUCT_TEAMS_JSON" >&2
  exit 2
fi

ready=true
required_count=0
rows_with_status=""
while IFS=$'\t' read -r team_id pm_agent; do
  [ -n "$team_id" ] || continue
  [ -n "$pm_agent" ] || continue
  required_count=$((required_count + 1))
  signoff_file="sessions/${pm_agent}/artifacts/release-signoffs/${slug}.md"
  has_signoff=false
  if [ -f "$signoff_file" ]; then
    has_signoff=true
  else
    ready=false
  fi
  rows_with_status+="${team_id}"$'\t'"${pm_agent}"$'\t'"${signoff_file}"$'\t'"${has_signoff}"$'\n'
done <<<"$rows"

if [ "${fmt:-}" = "--json" ]; then
  python3 - "$release_id" "$slug" "$ready" "$rows_with_status" <<'PY'
import json
import sys

release_id, slug, ready, rows = sys.argv[1:]

required = []
for line in rows.splitlines():
  parts = line.split('\t')
  if len(parts) != 4:
    continue
  team_id, pm_agent, signoff_file, has_signoff = parts
  required.append(
    {
      "team_id": team_id,
      "pm_agent": pm_agent,
      "signoff_file": signoff_file,
      "signed_off": (has_signoff == "true"),
    }
  )

out = {
  "release_id": release_id,
  "slug": slug,
  "required_pm_signoffs": required,
  "required_count": len(required),
  "signed_off_count": sum(1 for r in required if r["signed_off"]),
  "ready_for_official_push": (ready == "true"),
}

# Backward-compatible keys for legacy consumers when those seats are present.
for r in required:
  if r["pm_agent"] == "pm-forseti":
    out["pm_forseti_signed_off"] = r["signed_off"]
    out["pm_forseti_file"] = r["signoff_file"]
  if r["pm_agent"] == "pm-dungeoncrawler":
    out["pm_dungeoncrawler_signed_off"] = r["signed_off"]
    out["pm_dungeoncrawler_file"] = r["signoff_file"]

print(json.dumps(out))
PY
  exit 0
fi

echo "Release id: ${release_id}"
echo "- required PM signoffs: ${required_count}"
while IFS=$'\t' read -r team_id pm_agent signoff_file has_signoff; do
  [ -n "$team_id" ] || continue
  printf '%s\n' "- ${team_id} (${pm_agent}) signoff: ${has_signoff} (${signoff_file})"
done <<<"$rows_with_status"
echo "- ready for official push:   ${ready}"

if [ "$ready" != true ]; then
  exit 1
fi
