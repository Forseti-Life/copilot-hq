#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
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

if ! owner_row="$(python3 - "$PRODUCT_TEAMS_JSON" "$release_id" <<'PY'
import json
import sys

with open(sys.argv[1], 'r', encoding='utf-8') as fh:
  data = json.load(fh)

release_id = (sys.argv[2] or '').strip().lower()
best_team = None
best_len = 0

for team in (data.get('teams') or []):
    if not team.get('active', False):
        continue
    team_id = str(team.get('id') or '').strip().lower()
    aliases = [str(a).strip().lower() for a in (team.get('aliases') or []) if str(a).strip()]
    candidates = [team_id] + aliases
    for cand in candidates:
        if cand and cand in release_id and len(cand) > best_len:
            best_len = len(cand)
            best_team = team

if best_team:
    print(f"{str(best_team.get('id') or '').strip()}\t{str(best_team.get('pm_agent') or '').strip()}")
PY
  2>&1)"; then
  echo "$owner_row" >&2
  exit 2
fi

if [ -z "$owner_row" ]; then
  echo "ERROR: could not infer owning team for release '${release_id}' from $PRODUCT_TEAMS_JSON" >&2
  exit 2
fi

IFS=$'\t' read -r owner_team_id owner_pm_agent <<<"$owner_row"
signoff_file="sessions/${owner_pm_agent}/artifacts/release-signoffs/${slug}.md"
ready=false
if [ -f "$signoff_file" ]; then
  ready=true
fi

if [ "${fmt:-}" = "--json" ]; then
  python3 - "$release_id" "$slug" "$ready" "$owner_team_id" "$owner_pm_agent" "$signoff_file" <<'PY'
import json
import sys

release_id, slug, ready, owner_team_id, owner_pm_agent, signoff_file = sys.argv[1:]
required = [{
  "team_id": owner_team_id,
  "pm_agent": owner_pm_agent,
  "signoff_file": signoff_file,
  "signed_off": (ready == "true"),
}]

out = {
  "release_id": release_id,
  "slug": slug,
  "owner_team_id": owner_team_id,
  "owner_pm_agent": owner_pm_agent,
  "required_pm_signoffs": required,
  "required_count": 1,
  "signed_off_count": 1 if ready == "true" else 0,
  "ready_for_official_push": (ready == "true"),
}

print(json.dumps(out))
PY
  exit 0
fi

echo "Release id: ${release_id}"
echo "- required PM signoffs: 1"
echo "- owner team: ${owner_team_id}"
echo "- ${owner_team_id} (${owner_pm_agent}) signoff: ${ready} (${signoff_file})"
echo "- ready for official push:   ${ready}"

if [ "$ready" != true ]; then
  exit 1
fi
