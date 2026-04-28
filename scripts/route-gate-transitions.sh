#!/usr/bin/env bash
# Post-execution gate-transition router.
#
# Called by agent-exec-loop.sh after each agent execution to detect gate
# completion signals in the outbox and create follow-on inbox items.
#
# Usage: route-gate-transitions.sh <agent-id> [inbox-item-name]
#
# Non-blocking: always exits 0 so routing failures never abort the exec loop.
# Idempotent: skips creation if the target inbox item or its outbox already exists.

set -uo pipefail

AGENT="${1:-}"
ITEM_NAME="${2:-}"  # optional: name of the inbox item that was just processed

[ -n "$AGENT" ] || exit 0

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

TIMESTAMP="$(date +%Y%m%d)"

log() { echo "[route-gate] $*" >&2; }

# Resolve the outbox file from the processed item name, or fall back to newest
# only when no explicit item name was provided.
find_outbox_file() {
  local agent="$1" item="$2"
  local outbox_dir="sessions/${agent}/outbox"
  [ -d "$outbox_dir" ] || return 0

  if [ -n "$item" ] && [ -f "${outbox_dir}/${item}.md" ]; then
    echo "${outbox_dir}/${item}.md"
    return 0
  fi
  if [ -n "$item" ]; then
    return 0
  fi
  # Fall back to most recently modified .md file only when no specific item was provided.
  ls -t "${outbox_dir}"/*.md 2>/dev/null | head -1
}

find_command_file() {
  local agent="$1" item="$2"
  [ -n "$item" ] || return 0
  local inbox_cmd="sessions/${agent}/inbox/${item}/command.md"
  if [ -f "$inbox_cmd" ]; then
    echo "$inbox_cmd"
    return 0
  fi

  local artifacts_dir="sessions/${agent}/artifacts"
  [ -d "$artifacts_dir" ] || return 0
  ls -td "${artifacts_dir}/${item}"*/command.md 2>/dev/null | head -1
}

# ─── Main routing logic ────────────────────────────────────────────────────────

OUTBOX_FILE="$(find_outbox_file "$AGENT" "$ITEM_NAME")" || true
[ -n "$OUTBOX_FILE" ] && [ -f "$OUTBOX_FILE" ] || exit 0

OUTBOX_CONTENT="$(cat "$OUTBOX_FILE" 2>/dev/null || true)"
[ -n "$OUTBOX_CONTENT" ] || exit 0
COMMAND_FILE="$(find_command_file "$AGENT" "$ITEM_NAME" || true)"
FLOW_MANAGED=false
if [ -n "$COMMAND_FILE" ] && [ -f "$COMMAND_FILE" ] && grep -qiE '^\-\s+Flow id:' "$COMMAND_FILE"; then
  FLOW_MANAGED=true
fi

OUTBOX_BASE="$(basename "$OUTBOX_FILE" .md)"
ROUTE_DATE="$(printf '%s' "$OUTBOX_BASE" | sed -n 's/^\([0-9]\{8\}\).*/\1/p')"
if ! [[ "$ROUTE_DATE" =~ ^[0-9]{8}$ ]]; then
  ROUTE_DATE="$TIMESTAMP"
fi

# ─── Flow-aware routing: uses explicit Flow id / Flow node / owner_seat metadata ───
if [ -f "scripts/route-flow-transitions.py" ]; then
  python3 "scripts/route-flow-transitions.py" "$AGENT" "$ITEM_NAME" "$OUTBOX_FILE" 2>/dev/null || true
fi
exit 0
