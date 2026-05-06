#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
LIVE_CUSTOM_DIR="${ROOT_DIR}/sites/forseti/web/modules/custom"
CANONICAL_DIR="${ROOT_DIR}/module-sources"
FILTER="${1:-}"

if [ ! -d "$LIVE_CUSTOM_DIR" ]; then
  echo "ERROR: custom module directory not found: $LIVE_CUSTOM_DIR" >&2
  exit 1
fi

classify_source() {
  local target="$1"
  case "$target" in
    */forseti-job-hunter/web/modules/custom/*)
      echo "nested-in-forseti-job-hunter"
      ;;
    ${ROOT_DIR}/*)
      echo "standalone-repo-or-tree"
      ;;
    *)
      echo "nonstandard"
      ;;
  esac
}

print_row() {
  local name="$1"
  local canonical_path="$2"
  local target_path="$3"
  local kind="$4"
  printf '%-28s %-18s %-55s %s\n' "$name" "$kind" "$canonical_path" "$target_path"
}

printf '%-28s %-18s %-55s %s\n' "MODULE" "SOURCE_KIND" "CANONICAL_PATH" "RESOLVES_TO"
printf '%-28s %-18s %-55s %s\n' "------" "-----------" "--------------" "-----------"

while IFS= read -r -d '' path; do
  name="$(basename "$path")"
  canonical_path="${CANONICAL_DIR}/${name}"
  if [ -n "$FILTER" ] && [ "$name" != "$FILTER" ]; then
    continue
  fi

  if [ -L "$path" ]; then
    target="$(readlink -f "$path" 2>/dev/null || true)"
    if [ -z "$target" ]; then
      print_row "$name" "$canonical_path" "BROKEN_SYMLINK" "broken"
    else
      print_row "$name" "$canonical_path" "$target" "$(classify_source "$target")"
    fi
  elif [ -d "$path" ]; then
    print_row "$name" "$canonical_path" "$path" "direct-directory"
  fi
done < <(find "$LIVE_CUSTOM_DIR" -mindepth 1 -maxdepth 1 \( -type l -o -type d \) -print0 | sort -z)

if [ -n "$FILTER" ] && ! find "$LIVE_CUSTOM_DIR" -mindepth 1 -maxdepth 1 \( -type l -o -type d \) -name "$FILTER" | grep -q .; then
  echo
  echo "No module named '$FILTER' found under $LIVE_CUSTOM_DIR" >&2
  exit 2
fi
