from __future__ import annotations

from datetime import date
from pathlib import Path
import re


def promote_feature_brief_to_ready(feature_brief: Path, note_date: str | None = None) -> str:
    """Promote a groomed feature brief to ready.

    Deferred features are explicitly supported so grooming completion can move
    them back into the actionable ready pool.
    """
    text = feature_brief.read_text(encoding="utf-8")
    updated = re.sub(
        r"^(- Status:\s*)(in_progress|planned|deferred)\s*$",
        r"\g<1>ready",
        text,
        flags=re.MULTILINE,
        count=1,
    )

    if updated == text and "- Status: ready" not in text:
        raise ValueError(f"feature brief does not contain a promotable status: {feature_brief}")

    note_date = note_date or date.today().isoformat()
    if "## Latest updates" in updated:
        lines = updated.split("\n")
        for i, line in enumerate(lines):
            if line.strip() == "## Latest updates":
                lines.insert(i + 1, f"\n- {note_date}: Grooming complete — test plan written by QA. Ready for next Stage 0 scope selection.")
                break
        updated = "\n".join(lines)

    feature_brief.write_text(updated, encoding="utf-8")
    return updated
