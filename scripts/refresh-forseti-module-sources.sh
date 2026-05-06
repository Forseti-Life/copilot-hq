#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
LIVE_CUSTOM_DIR="${ROOT_DIR}/sites/forseti/web/modules/custom"
CANONICAL_DIR="${ROOT_DIR}/module-sources"

mkdir -p "$CANONICAL_DIR"

find "$CANONICAL_DIR" -mindepth 1 -maxdepth 1 -type l -delete

while IFS= read -r -d '' path; do
  name="$(basename "$path")"
  resolved="$(readlink -f "$path" 2>/dev/null || true)"
  if [ -z "$resolved" ]; then
    resolved="$path"
  fi
  ln -sfn "$resolved" "${CANONICAL_DIR}/${name}"
done < <(find "$LIVE_CUSTOM_DIR" -mindepth 1 -maxdepth 1 \( -type l -o -type d \) ! -name README.txt -print0 | sort -z)

echo "Refreshed canonical module source links in $CANONICAL_DIR"
