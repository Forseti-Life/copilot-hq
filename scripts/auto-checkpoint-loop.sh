#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}" )/.." && pwd)"
cd "$ROOT_DIR"

PIDFILE=".auto-checkpoint-loop.pid"
LOCKFILE="tmp/.auto-checkpoint-loop.control.lock"
LOGDIR="inbox/responses"
LATEST="$LOGDIR/auto-checkpoint-latest.log"
mkdir -p "$LOGDIR"
mkdir -p "$(dirname "$LOCKFILE")"

cmd="${1:-start}"
interval="${2:-${HQ_AUTO_CHECKPOINT_INTERVAL_SECONDS:-600}}"   # 10 minutes

read_pid() {
  [ -f "$PIDFILE" ] || { echo ""; return; }
  pid="$(cat "$PIDFILE" 2>/dev/null || true)"
  [[ "$pid" =~ ^[0-9]+$ ]] && echo "$pid" || echo ""
}

loop_pids() {
  ps -eo pid=,args= 2>/dev/null | awk '/[a]uto-checkpoint-loop\.sh run/ {print $1}'
}

is_running() {
  pid="$(read_pid)"
  if [ -n "$pid" ] && ps -p "$pid" >/dev/null 2>&1; then
    return 0
  fi
  [ -n "$(loop_pids)" ]
}

case "$cmd" in
  start)
    exec 9>"$LOCKFILE"
    flock -n 9 || { echo "Start already in progress"; exit 0; }
    if is_running; then
      echo "Already running (pid $(read_pid))"
      exit 0
    fi
    setsid bash -c "'$0' run '$interval'" >/dev/null 2>&1 &
    pid=$!
    echo "$pid" > "$PIDFILE"
    echo "Started (pid $pid)"
    echo "To stop: send SIGTERM to pid $pid"
    ;;

  status)
    extra_pids="$(loop_pids | tr '\n' ' ' | sed -E 's/[[:space:]]+/ /g; s/^ //; s/ $//')"
    if is_running; then
      tracked_pid="$(read_pid)"
      if [ -n "$tracked_pid" ] && ps -p "$tracked_pid" >/dev/null 2>&1; then
        if [ -n "$extra_pids" ] && [ "$extra_pids" != "$tracked_pid" ]; then
          echo "running (pid $tracked_pid; visible pid(s): $extra_pids)"
        else
          echo "running (pid $tracked_pid)"
        fi
      else
        echo "running (untracked pid(s): $extra_pids)"
      fi
    else
      echo "not running"
    fi
    ;;

  verify)
    if is_running; then
      echo "running (pid $(read_pid))"
      exit 0
    fi
    echo "ERROR: auto-checkpoint loop not running" >&2
    exit 1
    ;;

  stop)
    exec 9>"$LOCKFILE"
    flock -n 9 || { echo "Stop already in progress"; exit 0; }
    pid="$(read_pid)"
    if [ -n "$pid" ] && ps -p "$pid" >/dev/null 2>&1; then
      kill "$pid" >/dev/null 2>&1 || true
      sleep 0.2
      ps -p "$pid" >/dev/null 2>&1 && kill -9 "$pid" >/dev/null 2>&1 || true
      stopped_any=1
    else
      stopped_any=0
    fi
    while IFS= read -r loop_pid; do
      [[ "$loop_pid" =~ ^[0-9]+$ ]] || continue
      [ "$loop_pid" = "$pid" ] && continue
      kill "$loop_pid" >/dev/null 2>&1 || true
      sleep 0.2
      ps -p "$loop_pid" >/dev/null 2>&1 && kill -9 "$loop_pid" >/dev/null 2>&1 || true
      stopped_any=1
    done < <(loop_pids)
    if [ "$stopped_any" -eq 1 ]; then
      echo "Stopped"
      exit 0
    fi
    echo "Not running"
    ;;

  run)
    echo $$ > "$PIDFILE"
    while true; do
      if [ "$(./scripts/is-org-enabled.sh 2>/dev/null || echo false)" != "true" ]; then
        sleep "$interval"
        continue
      fi
      ts="$(date -Iseconds)"
      daylog="$LOGDIR/auto-checkpoint-$(date +%Y%m%d).log"
      out=$(./scripts/auto-checkpoint.sh 2>&1 || true)
      echo "[$ts] $out" | tee -a "$daylog" > "$LATEST"
      sleep "$interval"
    done
    ;;

  *)
    echo "Usage: $0 start|stop|status|verify [interval_seconds]" >&2
    exit 1
    ;;
esac
