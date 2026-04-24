#!/usr/bin/env bash
set -euo pipefail

# Installs/updates a user crontab entry to run auto-checkpoint every 10 minutes.

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}" )/.." && pwd)"
cd "$ROOT_DIR"

LOG_DIR="$ROOT_DIR/inbox/responses"
mkdir -p "$LOG_DIR"

MARKER="# copilot-sessions-hq:auto-checkpoint"
CMD="$ROOT_DIR/scripts/auto-checkpoint.sh"
LOG="$LOG_DIR/auto-checkpoint-cron.log"

# Every 10 minutes.
LINE="*/10 * * * * $CMD >> $LOG 2>&1 $MARKER"

current=""
if crontab -l >/dev/null 2>&1; then
  current="$(crontab -l)"
fi

# Remove prior entries for this job.
filtered="$(printf '%s\n' "$current" | grep -vF "$MARKER" | grep -vF "$CMD" || true)"

# Install new.
{
  printf '%s\n' "$filtered" | sed '/^$/d'
  echo "$LINE"
} | crontab -

echo "Installed cron: $LINE"
