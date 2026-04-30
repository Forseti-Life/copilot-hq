#!/usr/bin/env bash
# ceo-release-health.sh — CEO release cycle diagnostic
#
# Runs an end-to-end health check of the release cycle for all active coordinated teams.
# Covers every issue that has historically caused blocked releases:
#   • Stale release_id / next_release_id runtime files
#   • Feature coverage and status mismatches
#   • Gate 2 APPROVE presence
#   • PM signoffs (own + cross-team)
#   • Coordinated push readiness
#   • GitHub Actions deploy.yml enabled + last run
#   • Orphaned features — in_progress on stale/closed releases with no dev work
#   • Backlog health — ready features awaiting grooming into upcoming releases
#
# Usage:
#   bash scripts/ceo-release-health.sh
#   bash scripts/ceo-release-health.sh --fix     # auto-fix stale next_release_id AND reset orphaned features (CEO authority)
#
# Output uses ✅ PASS / ❌ FAIL / ⚠️  WARN prefixes.
# Exit 0 = all checks pass. Exit 1 = at least one FAIL.
#
set -euo pipefail
ROOT_DIR="${HQ_ROOT_DIR:-$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)}"
cd "${ROOT_DIR}"

FIX_MODE=0
RELEASE_START_GRACE_SECONDS="${RELEASE_START_GRACE_SECONDS:-14400}"
for arg in "$@"; do
  [ "$arg" = "--fix" ] && FIX_MODE=1
done

PRODUCT_TEAMS_JSON="org-chart/products/product-teams.json"
ACTIVE_DIR="tmp/release-cycle-active"
PASS="✅ PASS"
FAIL="❌ FAIL"
WARN="⚠️  WARN"
INFO="   ℹ️ "

FAILURES=0

fail() { echo "$FAIL $*"; FAILURES=$((FAILURES + 1)); }
pass() { echo "$PASS $*"; }
warn() { echo "$WARN $*"; }
info() { echo "$INFO $*"; }

hr() { echo "────────────────────────────────────────────────────────"; }

find_gate2_evidence() {
  local qa_outbox="$1"
  local release_id="$2"
  [ -d "$qa_outbox" ] || return 0
  find "$qa_outbox" -maxdepth 1 \( -name "*gate2-approve*" -o -name "*empty-release-self-cert*" \) -type f 2>/dev/null \
    | xargs grep -l "$release_id" 2>/dev/null | head -1 || true
}

echo
echo "═══════════════════════════════════════════════════════"
echo "  CEO Release Cycle Health Check"
echo "  $(date -u '+%Y-%m-%dT%H:%M:%SZ')"
echo "═══════════════════════════════════════════════════════"

# ── Load release-enabled teams ───────────────────────────────────────────────
if [ ! -f "$PRODUCT_TEAMS_JSON" ]; then
  fail "product-teams.json missing: $PRODUCT_TEAMS_JSON"
  exit 1
fi

COORDINATED_TEAMS="$(python3 - "$PRODUCT_TEAMS_JSON" <<'PY'
import json, sys
data = json.load(open(sys.argv[1], encoding='utf-8'))
for t in data.get('teams', []):
    if t.get('active') and t.get('release_preflight_enabled'):
        print('\t'.join([
            t.get('id',''),
            t.get('pm_agent', 'pm-' + t.get('id','')),
            t.get('qa_agent', 'qa-' + t.get('id','')),
        ]))
PY
)"

if [ -z "$COORDINATED_TEAMS" ]; then
  fail "No active release-enabled teams found in product-teams.json"
  exit 1
fi

# ── GitHub Actions deploy.yml check ─────────────────────────────────────────
echo
hr
echo "  GitHub Actions: deploy.yml"
hr

REPO="keithaumiller/forseti.life"
GH_BIN="$(command -v gh 2>/dev/null || true)"
DEPLOY_CHECKED=0

if [ -n "$GH_BIN" ] && [ -f /home/ubuntu/github.token ]; then
  TOKEN="$(cat /home/ubuntu/github.token)"
  WF_STATE="$(GH_TOKEN="$TOKEN" "$GH_BIN" api "repos/${REPO}/actions/workflows/deploy.yml" --jq '.state' 2>/dev/null || echo 'unknown')"
  if [ "$WF_STATE" = "active" ]; then
    pass "deploy.yml is enabled (state=active)"
  elif [ "$WF_STATE" = "disabled_manually" ] || [ "$WF_STATE" = "disabled_fork" ]; then
    fail "deploy.yml is DISABLED (state=$WF_STATE) — production will not update on push"
    if [ "$FIX_MODE" = "1" ]; then
      info "FIX: re-enabling deploy.yml..."
      GH_TOKEN="$TOKEN" "$GH_BIN" workflow enable deploy.yml --repo "$REPO" 2>/dev/null && info "deploy.yml re-enabled" || warn "could not re-enable deploy.yml"
    else
      info "Run with --fix to re-enable, or: GH_TOKEN=\$(cat /home/ubuntu/github.token) gh workflow enable deploy.yml --repo $REPO"
    fi
  else
    warn "deploy.yml state=$WF_STATE (could not determine)"
  fi

  # Last run
  LAST_RUN="$(GH_TOKEN="$TOKEN" "$GH_BIN" run list --workflow=deploy.yml --repo "$REPO" --limit 1 --json conclusion,createdAt,displayTitle -q '.[0]' 2>/dev/null || echo '')"
  if [ -n "$LAST_RUN" ]; then
    LAST_DATE="$(echo "$LAST_RUN" | python3 -c "import json,sys; d=json.load(sys.stdin); print(d.get('createdAt','?'))")"
    LAST_STATUS="$(echo "$LAST_RUN" | python3 -c "import json,sys; d=json.load(sys.stdin); print(d.get('conclusion','in_progress'))")"
    LAST_TITLE="$(echo "$LAST_RUN" | python3 -c "import json,sys; d=json.load(sys.stdin); print(d.get('displayTitle','?')[:60])")"
    AGE_SECS="$(python3 -c "from datetime import datetime,timezone; a='$LAST_DATE'; dt=datetime.fromisoformat(a.replace('Z','+00:00')); print(int((datetime.now(timezone.utc)-dt).total_seconds()))" 2>/dev/null || echo '?')"
    if [ "$AGE_SECS" != "?" ] && [ "$AGE_SECS" -gt 86400 ]; then
      warn "Last deploy run was $((AGE_SECS/3600))h ago ($LAST_DATE) — status=$LAST_STATUS"
      info "Title: $LAST_TITLE"
      info "On this host, stale deploy.yml age alone is not a code-deploy blocker because module/theme code is live from the monorepo checkout; verify config/composer drift separately when relevant."
    elif [ "$LAST_STATUS" = "failure" ]; then
      fail "Last deploy run FAILED ($LAST_DATE) — investigate GitHub Actions"
    else
      pass "Last deploy: $LAST_DATE status=$LAST_STATUS"
    fi
  fi
  DEPLOY_CHECKED=1
fi

if [ "$DEPLOY_CHECKED" = "0" ]; then
  warn "gh CLI or /home/ubuntu/github.token not available — skipping deploy.yml check"
fi

# ── Per-team release cycle checks ───────────────────────────────────────────
ALL_RELEASE_IDS=""

while IFS=$'\t' read -r TEAM PM_AGENT QA_AGENT; do
  [ -n "$TEAM" ] || continue

  echo
  hr
  echo "  Team: $TEAM  |  pm=$PM_AGENT  qa=$QA_AGENT"
  hr

  # 1. release_id
  RELEASE_ID_FILE="$ACTIVE_DIR/${TEAM}.release_id"
  NEXT_ID_FILE="$ACTIVE_DIR/${TEAM}.next_release_id"

  if [ ! -f "$RELEASE_ID_FILE" ]; then
    fail "[$TEAM] $RELEASE_ID_FILE not found — release cycle not started"
    continue
  fi

  RELEASE_ID="$(cat "$RELEASE_ID_FILE" | tr -d '[:space:]')"
  NEXT_RELEASE_ID="$(cat "$NEXT_ID_FILE" 2>/dev/null | tr -d '[:space:]' || echo '')"
  STARTED_AT_FILE="$ACTIVE_DIR/${TEAM}.started_at"
  RELEASE_AGE_SECS=""
  if [ -f "$STARTED_AT_FILE" ]; then
    STARTED_AT="$(cat "$STARTED_AT_FILE" | tr -d '[:space:]')"
    RELEASE_AGE_SECS="$(python3 - "$STARTED_AT" <<'PY'
from datetime import datetime, timezone
import sys

value = sys.argv[1].strip()
if not value:
    print("")
    raise SystemExit(0)
try:
    dt = datetime.fromisoformat(value.replace("Z", "+00:00"))
except Exception:
    print("")
    raise SystemExit(0)
print(int((datetime.now(timezone.utc) - dt).total_seconds()))
PY
)"
  fi
  IN_RELEASE_GRACE=0
  if [ -n "$RELEASE_AGE_SECS" ] && [ "$RELEASE_AGE_SECS" -lt "$RELEASE_START_GRACE_SECONDS" ]; then
    IN_RELEASE_GRACE=1
  fi

  echo "  Release ID:  $RELEASE_ID"
  echo "  Next ID:     ${NEXT_RELEASE_ID:-<not set>}"

  RELEASE_FLOW_RUNTIME="tmp/flow-runs/release_shipping_flow/$(printf '%s' "$RELEASE_ID" | tr '[:upper:]' '[:lower:]' | tr -cs 'a-z0-9._-' '-')/product-team.json"
  if [ -f "$RELEASE_FLOW_RUNTIME" ]; then
    pass "[$TEAM] release_shipping_flow runtime seeded"
  else
    warn "[$TEAM] release_shipping_flow runtime missing for $RELEASE_ID"
    info "Expected: $RELEASE_FLOW_RUNTIME"
  fi

  # 2. Stale next_release_id check (next should not equal or precede current)
  if [ -n "$NEXT_RELEASE_ID" ]; then
    NEED_NEXT_FIX=0
    if [ "$NEXT_RELEASE_ID" = "$RELEASE_ID" ]; then
      fail "[$TEAM] next_release_id == release_id ($RELEASE_ID) — definitely stale"
      NEED_NEXT_FIX=1
    else
      NEXT_IS_AHEAD="$(python3 - "$RELEASE_ID" "$NEXT_RELEASE_ID" "$TEAM" <<'PY'
import re
import sys

current_release_id, next_release_id, team_id = sys.argv[1], sys.argv[2], sys.argv[3]

def rank(release_id: str) -> int:
    match = re.match(rf"^\d{{8}}-{re.escape(team_id)}-(.+)$", release_id or "")
    suffix = match.group(1) if match else ""
    if suffix == "release":
        return 0
    if suffix == "release-next":
        return 1
    label_match = re.fullmatch(r"release-([a-z]+)", suffix)
    if not label_match:
        return -1
    value = 0
    for ch in label_match.group(1):
        value = (value * 26) + (ord(ch) - ord("a") + 1)
    return 1 + value

print("1" if rank(next_release_id) > rank(current_release_id) else "0")
PY
)"
      if [ "$NEXT_IS_AHEAD" != "1" ]; then
        fail "[$TEAM] next_release_id ($NEXT_RELEASE_ID) sorts before release_id ($RELEASE_ID) — stale"
        NEED_NEXT_FIX=1
      else
        pass "[$TEAM] next_release_id ($NEXT_RELEASE_ID) is ahead of current — OK"
      fi
    fi

    if [ "$NEED_NEXT_FIX" = "1" ]; then
      if [ "$FIX_MODE" = "1" ]; then
        NEW_NEXT="$(python3 - "$RELEASE_ID" "$TEAM" <<'PY'
import re
import sys

release_id, team_id = sys.argv[1], sys.argv[2]
match = re.match(rf"^(\d{{8}})-{re.escape(team_id)}-(.+)$", release_id or "")
if not match:
    print("")
    raise SystemExit(0)

date_part = match.group(1)
suffix = match.group(2)

if suffix == "release":
    next_suffix = "release-next"
elif suffix == "release-next":
    next_suffix = "release-b"
else:
    label_match = re.fullmatch(r"release-([a-z]+)", suffix)
    if not label_match:
        next_suffix = "release-b"
    else:
        chars = list(label_match.group(1))
        idx = len(chars) - 1
        while idx >= 0 and chars[idx] == "z":
            chars[idx] = "a"
            idx -= 1
        if idx < 0:
            chars.insert(0, "a")
        else:
            chars[idx] = chr(ord(chars[idx]) + 1)
        next_suffix = f"release-{''.join(chars)}"

print(f"{date_part}-{team_id}-{next_suffix}")
PY
)"
        if [ -n "$NEW_NEXT" ]; then
          echo "$NEW_NEXT" > "$NEXT_ID_FILE"
          info "FIX: next_release_id set to $NEW_NEXT"
          NEXT_RELEASE_ID="$NEW_NEXT"
        fi
      else
        info "Run with --fix to auto-correct, or: echo '<correct-id>' > $NEXT_ID_FILE"
      fi
    fi
  else
    warn "[$TEAM] next_release_id not set"
  fi

  ALL_RELEASE_IDS="$ALL_RELEASE_IDS $RELEASE_ID"

  # 3. Features in current release
  echo
  echo "  Features in $RELEASE_ID:"
  FEATURES_IN_RELEASE=()
  FEATURES_NOT_DONE=()
  FEATURES_WAITING_FOR_IMPL=0
  while IFS= read -r FEAT_DIR; do
    [ -d "$FEAT_DIR" ] || continue
    FM="$FEAT_DIR/feature.md"
    [ -f "$FM" ] || continue
    TEXT="$(cat "$FM")"
    # Match release_id in feature.md
    if echo "$TEXT" | grep -qE "^-\s+Release:\s*${RELEASE_ID}\s*$"; then
      FEAT_NAME="$(basename "$FEAT_DIR")"
      STATUS="$(echo "$TEXT" | grep -E '^-\s+Status:' | head -1 | sed 's/^-\s*Status:\s*//')"
      FEATURES_IN_RELEASE+=("$FEAT_NAME")
      if [ "$STATUS" = "done" ]; then
        pass "  feature: $FEAT_NAME (status=$STATUS)"
      elif [ "$STATUS" = "in_progress" ]; then
        # Check for dev outbox (implementation)
        DEV_AGENT="dev-$TEAM"
        HAS_IMPL="$(ls "sessions/${DEV_AGENT}/outbox/" 2>/dev/null | grep "$FEAT_NAME" | head -1 || true)"
        if [ -n "$HAS_IMPL" ]; then
          pass "  feature: $FEAT_NAME (status=$STATUS, dev outbox: $HAS_IMPL)"
        else
          if [ "$IN_RELEASE_GRACE" = "1" ]; then
            warn "  feature: $FEAT_NAME (status=$STATUS, no dev outbox yet — release still within startup grace)"
          else
            fail "  feature: $FEAT_NAME (status=$STATUS, NO dev outbox found — implementation missing)"
          fi
          FEATURES_WAITING_FOR_IMPL=$((FEATURES_WAITING_FOR_IMPL + 1))
          FEATURES_NOT_DONE+=("$FEAT_NAME")
        fi
      else
        warn "  feature: $FEAT_NAME (status=${STATUS:-unknown})"
        FEATURES_NOT_DONE+=("$FEAT_NAME")
      fi
    fi
  done < <(find features -maxdepth 1 -mindepth 1 -type d 2>/dev/null)

  FEAT_COUNT="${#FEATURES_IN_RELEASE[@]}"
  if [ "$FEAT_COUNT" -eq 0 ]; then
    warn "[$TEAM] No features scoped to $RELEASE_ID"
  else
    info "[$TEAM] $FEAT_COUNT feature(s) in scope"
  fi

  # 4. Gate 2 APPROVE
  echo
  QA_OUTBOX="sessions/${QA_AGENT}/outbox"
  GATE2_FILE="$(find_gate2_evidence "$QA_OUTBOX" "$RELEASE_ID")"

  if [ -n "$GATE2_FILE" ]; then
    pass "[$TEAM] Gate 2 evidence: $(basename "$GATE2_FILE")"
  else
    if [ "$FEAT_COUNT" -eq 0 ]; then
      warn "[$TEAM] Gate 2 APPROVE not found (empty release — may need --empty-release flag)"
    elif [ "$FEATURES_WAITING_FOR_IMPL" -gt 0 ]; then
      warn "[$TEAM] Gate 2 APPROVE pending implementation completion (${FEATURES_WAITING_FOR_IMPL} feature(s) still missing dev outbox)"
    else
      fail "[$TEAM] Gate 2 APPROVE not found in $QA_OUTBOX for $RELEASE_ID"
      info "Expected: $QA_OUTBOX/<timestamp>-gate2-approve-<slug>.md containing '$RELEASE_ID' and 'APPROVE'"
    fi
  fi

  # 5. PM signoff
  PM_SIGNOFF="sessions/${PM_AGENT}/artifacts/release-signoffs/${RELEASE_ID}.md"
  if [ -f "$PM_SIGNOFF" ]; then
    pass "[$TEAM] PM signoff ($PM_AGENT): found"
  elif [ "$FEAT_COUNT" -eq 0 ]; then
    warn "[$TEAM] PM signoff pending scope activation for $RELEASE_ID"
  elif [ "$FEATURES_WAITING_FOR_IMPL" -gt 0 ]; then
    warn "[$TEAM] PM signoff pending implementation and QA completion for $RELEASE_ID"
  elif [ -z "$GATE2_FILE" ]; then
    warn "[$TEAM] PM signoff pending Gate 2 APPROVE for $RELEASE_ID"
  else
    fail "[$TEAM] PM signoff missing: $PM_SIGNOFF"
    info "Run: bash scripts/release-signoff.sh $TEAM $RELEASE_ID"
  fi

  # 6. Orphaned features — in_progress on a stale/closed release (Python for speed)
  echo
  ORPHAN_RESULTS="$(python3 - features "$TEAM" "$RELEASE_ID" <<'PY'
import pathlib, sys, re
feat_root = pathlib.Path(sys.argv[1])
team, current_release = sys.argv[2], sys.argv[3]
for feat_dir in sorted(feat_root.iterdir()):
    fm = feat_dir / "feature.md"
    if not fm.exists(): continue
    text = fm.read_text()
    status = next((re.sub(r"^- Status:\s*", "", l).strip()
                   for l in text.splitlines() if re.match(r"^- Status:", l)), "")
    if status != "in_progress": continue
    release = next((re.sub(r"^- Release:\s*", "", l).strip()
                    for l in text.splitlines() if re.match(r"^- Release:", l)), "")
    if not release or release in ("none", "(set by PM at activation)"): continue
    if release == current_release: continue
    if team not in release: continue   # only flag features belonging to this team
    print(f"{feat_dir.name}\t{release}")
PY
)"

  ORPHAN_COUNT=0
  if [ -n "$ORPHAN_RESULTS" ]; then
    while IFS=$'\t' read -r FEAT_NAME F_RELEASE; do
      [ -n "$FEAT_NAME" ] || continue
      DEV_AGENT="dev-$TEAM"
      HAS_IMPL="$(ls "sessions/${DEV_AGENT}/outbox/" 2>/dev/null | grep "$FEAT_NAME" | head -1 || true)"
      if [ -n "$HAS_IMPL" ]; then
        warn "[$TEAM] ORPHAN: $FEAT_NAME (in_progress on OLD $F_RELEASE — dev outbox exists, reconcile status instead of deleting)"
      else
        fail "[$TEAM] ORPHAN: $FEAT_NAME (in_progress on CLOSED $F_RELEASE — no dev work done)"
        info "  Fix: reset to ready + clear release; do not delete the feature record. Run with --fix to auto-reset."
        if [ "$FIX_MODE" = "1" ]; then
          FM="features/$FEAT_NAME/feature.md"
          sed -i 's/^- Status: in_progress/- Status: ready/' "$FM"
          sed -i "s|^- Release: ${F_RELEASE}|- Release: |" "$FM"
          info "  FIX: reset $FEAT_NAME → ready, release cleared (feature preserved)"
        fi
      fi
      ORPHAN_COUNT=$((ORPHAN_COUNT + 1))
    done <<<"$ORPHAN_RESULTS"
  fi

  if [ "$ORPHAN_COUNT" -eq 0 ]; then
    pass "[$TEAM] No orphaned in_progress features on stale/closed releases"
  fi

done <<<"$COORDINATED_TEAMS"

# ── Dependency signoff matrix ────────────────────────────────────────────────
echo
hr
echo "  Dependency signoff matrix"
hr

DEPENDENCY_STATUS="$(
python3 - "$PRODUCT_TEAMS_JSON" "$ACTIVE_DIR" "$ROOT_DIR" <<'PY'
import json, sys
from pathlib import Path

cfg_path = Path(sys.argv[1])
active_dir = Path(sys.argv[2])
root = Path(sys.argv[3])
sys.path.insert(0, str(root / "scripts" / "lib"))
from release_cycle_helpers import explicit_release_dependencies, release_enabled_team_map  # noqa: E402

team_map = release_enabled_team_map(cfg_path)
failures = 0
lines = []
any_dependencies = False

for team_id in sorted(team_map):
    team = team_map[team_id]
    deps = [dep for dep in explicit_release_dependencies(team) if dep in team_map]
    if not deps:
        lines.append(f"PASS [{team_id}] No explicit cross-release dependencies configured")
        continue
    any_dependencies = True
    for dep in deps:
        dep_team = team_map[dep]
        dep_rid_file = active_dir / f"{dep}.release_id"
        dep_rid = dep_rid_file.read_text(encoding='utf-8').strip() if dep_rid_file.exists() else ""
        if not dep_rid:
            lines.append(f"WARN [{team_id}] dependency {dep} has no active release_id yet")
            continue
        pm_agent = str(dep_team.get("pm_agent") or "").strip()
        signoff = root / "sessions" / pm_agent / "artifacts" / "release-signoffs" / f"{dep_rid}.md"
        if signoff.exists():
            lines.append(f"PASS [{team_id}] dependency signoff satisfied: {dep} `{dep_rid}`")
        else:
            failures += 1
            lines.append(f"FAIL [{team_id}] dependency signoff missing: {dep} `{dep_rid}`")

if not any_dependencies:
    lines = ["PASS No explicit cross-release dependencies configured — releases are independent by default"]

for line in lines:
    print(line)
print(f"FAILURES_DELTA={failures}")
PY
)"
while IFS= read -r LINE; do
  [ -n "$LINE" ] || continue
  case "$LINE" in
    FAILURES_DELTA=*) FAILURES=$((FAILURES + ${LINE#FAILURES_DELTA=}));;
    PASS\ *) pass "${LINE#PASS }";;
    WARN\ *) warn "${LINE#WARN }";;
    FAIL\ *) fail "${LINE#FAIL }";;
  esac
done <<<"$DEPENDENCY_STATUS"

# ── Push readiness ────────────────────────────────────────────────────────────
echo
hr
echo "  Release push readiness"
hr

PUSH_READINESS="$(
python3 - "$PRODUCT_TEAMS_JSON" "$ACTIVE_DIR" "$ROOT_DIR" <<'PY'
import sys
from pathlib import Path

cfg_path = Path(sys.argv[1])
active_dir = Path(sys.argv[2])
root = Path(sys.argv[3])
sys.path.insert(0, str(root / "scripts" / "lib"))
from release_cycle_helpers import combined_release_marker_key, explicit_release_dependencies, release_enabled_team_map  # noqa: E402

team_map = release_enabled_team_map(cfg_path)
failures = 0
printed_independent = False

for team_id in sorted(team_map):
    team = team_map[team_id]
    release_id_file = active_dir / f"{team_id}.release_id"
    release_id = release_id_file.read_text(encoding='utf-8').strip() if release_id_file.exists() else ""
    if not release_id:
        continue
    pm_agent = str(team.get("pm_agent") or "").strip()
    signoff_path = root / "sessions" / pm_agent / "artifacts" / "release-signoffs" / f"{release_id}.md"
    deps = [dep for dep in explicit_release_dependencies(team) if dep in team_map]
    if not deps:
        if signoff_path.exists():
            print(f"PASS [{team_id}] Independent release `{release_id}` is push-ready on its own signoff")
        else:
            print(f"INFO [{team_id}] Independent release `{release_id}` is waiting on owner PM signoff")
        printed_independent = True
        continue

    cohort = [team, *[team_map[dep] for dep in sorted(deps)]]
    team_release_ids = {}
    missing_signoffs = []
    for member in cohort:
        member_id = str(member.get("id") or "").strip()
        member_release_file = active_dir / f"{member_id}.release_id"
        member_release = member_release_file.read_text(encoding='utf-8').strip() if member_release_file.exists() else ""
        if not member_release:
            missing_signoffs.append(member_id)
            continue
        team_release_ids[member_id] = member_release
        member_pm = str(member.get("pm_agent") or "").strip()
        member_signoff = root / "sessions" / member_pm / "artifacts" / "release-signoffs" / f"{member_release}.md"
        if not member_signoff.exists():
            missing_signoffs.append(member_id)

    combined_key = combined_release_marker_key(team_release_ids, cohort)
    marker = root / "tmp" / "auto-push-dispatched" / f"{combined_key}.pushed"
    missing_advances = [
        member_id for member_id in team_release_ids
        if not (root / "tmp" / "auto-push-dispatched" / f"{combined_key}.{member_id}.advanced").exists()
    ]

    if marker.exists() and missing_advances:
        failures += 1
        print(f"FAIL [{team_id}] push marker exists for `{combined_key}` but boundary advance is incomplete: {', '.join(sorted(missing_advances))}")
    elif marker.exists():
        print(f"WARN [{team_id}] push already dispatched for cohort `{combined_key}`")
    elif missing_signoffs:
        print(f"INFO [{team_id}] push not yet ready — waiting on signoffs from: {', '.join(sorted(set(missing_signoffs)))}")
    else:
        print(f"PASS [{team_id}] dependency cohort is push-ready: `{combined_key}`")

if printed_independent:
    print("PASS Independent-by-default release logic active")
print(f"FAILURES_DELTA={failures}")
PY
)"
while IFS= read -r LINE; do
  [ -n "$LINE" ] || continue
  case "$LINE" in
    FAILURES_DELTA=*) FAILURES=$((FAILURES + ${LINE#FAILURES_DELTA=}));;
    PASS\ *) pass "${LINE#PASS }";;
    WARN\ *) warn "${LINE#WARN }";;
    FAIL\ *) fail "${LINE#FAIL }";;
    INFO\ *) info "${LINE#INFO }";;
  esac
done <<<"$PUSH_READINESS"

# ── Feature backlog health ────────────────────────────────────────────────────
echo
hr
echo "  Feature Backlog Health"
hr

while IFS=$'\t' read -r TEAM PM_AGENT QA_AGENT; do
  [ -n "$TEAM" ] || continue

  COUNTS="$(python3 - features "$PM_AGENT" <<'PY'
import pathlib, sys, re
feat_root = pathlib.Path(sys.argv[1])
pm_agent = sys.argv[2]
ready, unassigned = 0, 0
for feat_dir in sorted(feat_root.iterdir()):
    fm = feat_dir / "feature.md"
    if not fm.exists():
        continue
    text = fm.read_text()
    # Only count features owned by this team's PM
    if not any(pm_agent in l for l in text.splitlines() if re.match(r"^- PM owner:", l)):
        continue
    status_val = next(
        (re.sub(r"^- Status:\s*", "", l).strip()
         for l in text.splitlines() if re.match(r"^- Status:", l)), "")
    if status_val != "ready":
        continue
    ready += 1
    release_val = next(
        (re.sub(r"^- Release:\s*", "", l).strip()
         for l in text.splitlines() if re.match(r"^- Release:", l)), "")
    if not release_val or release_val in ("none", "(set by PM at activation)"):
        unassigned += 1
print(f"{ready}\t{unassigned}")
PY
)"

  READY_COUNT="$(echo "$COUNTS" | cut -f1)"
  UNASSIGNED_COUNT="$(echo "$COUNTS" | cut -f2)"

  if [ "${READY_COUNT:-0}" -ge 30 ]; then
    warn "[$TEAM] ${READY_COUNT} ready features in backlog (${UNASSIGNED_COUNT} unassigned to any release) — grooming needed"
    info "Dispatch grooming task to $PM_AGENT: scope next batch into upcoming release"
  elif [ "${READY_COUNT:-0}" -gt 0 ]; then
    pass "[$TEAM] ${READY_COUNT} ready feature(s) in backlog (${UNASSIGNED_COUNT} unassigned) — healthy"
  else
    pass "[$TEAM] Backlog empty — no unshipped ready features"
  fi
done <<<"$COORDINATED_TEAMS"

# ── Summary ───────────────────────────────────────────────────────────────────
echo
hr
if [ "$FAILURES" -eq 0 ]; then
  echo "✅  All checks PASSED — release cycle is healthy"
else
  echo "❌  $FAILURES check(s) FAILED — see items above"
fi
hr
echo

exit "$( [ "$FAILURES" -eq 0 ] && echo 0 || echo 1 )"
