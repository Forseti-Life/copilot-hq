import re
import subprocess
import textwrap
from pathlib import Path


SCRIPT = Path(__file__).resolve().parents[1] / "agent-exec-next.sh"


def _extract_function(name: str) -> str:
    text = SCRIPT.read_text(encoding="utf-8")
    match = re.search(rf"(?ms)^{re.escape(name)}\(\) \{{.*?^}}\n", text)
    assert match, f"Function {name} not found"
    return match.group(0)


def _run_bash(snippet: str) -> subprocess.CompletedProcess[str]:
    return subprocess.run(
        ["bash", "-lc", snippet],
        capture_output=True,
        text=True,
        check=False,
    )


def test_extract_final_canonical_outbox_prefers_last_status_block():
    extract_fn = _extract_function("_extract_final_canonical_outbox")
    script = (
        "set -euo pipefail\n"
        f"{extract_fn}\n"
        "response=$(cat <<'EOF'\n"
        "Let me inspect the release state first.\n\n"
        "- Status: in_progress\n"
        "- Summary: Initial check only.\n\n"
        "- Status: done\n"
        "- Summary: Final verified result.\n\n"
        "## Next actions\n"
        "- None\n"
        "EOF\n"
        ")\n"
        "_extract_final_canonical_outbox \"$response\"\n"
    )

    result = _run_bash(script)

    assert result.returncode == 0, result.stderr
    assert result.stdout.lstrip().startswith("- Status: done\n")
    assert "Let me inspect the release state first." not in result.stdout
    assert "- Summary: Final verified result." in result.stdout


def test_recovered_outbox_passes_semantic_validation_without_transcript_noise(tmp_path):
    extract_fn = _extract_function("_extract_final_canonical_outbox")
    validate_fn = _extract_function("invalid_outbox_reason")
    inbox_item = tmp_path / "inbox-item"
    inbox_item.mkdir()
    script = (
        "set -euo pipefail\n"
        f'inbox_item="{inbox_item}"\n'
        f"{extract_fn}\n"
        f"{validate_fn}\n"
        "response=$(cat <<'EOF'\n"
        "Let me do the actual work now.\n\n"
        "- Status: in_progress\n"
        "- Summary: Interim note.\n\n"
        "- Status: done\n"
        "- Summary: Final verified result.\n\n"
        "## Next actions\n"
        "- None\n\n"
        "## Blockers\n"
        "- None\n\n"
        "## Needs from CEO\n"
        "- N/A\n\n"
        "## ROI estimate\n"
        "- ROI: 5\n"
        "- Rationale: Small executor-format cleanup.\n"
        "EOF\n"
        ")\n"
        "response=\"$(_extract_final_canonical_outbox \"$response\")\"\n"
        "if invalid_outbox_reason \"$response\"; then\n"
        "  exit 1\n"
        "fi\n"
        "printf '%s' \"$response\"\n"
    )

    result = _run_bash(script)

    assert result.returncode == 0, result.stderr
    assert result.stdout.startswith("- Status: done\n")
    assert "Let me do the actual work now." not in result.stdout
