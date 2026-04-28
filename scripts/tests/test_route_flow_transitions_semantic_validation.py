import importlib.util
from pathlib import Path


SCRIPT = Path(__file__).resolve().parents[1] / "route-flow-transitions.py"
SPEC = importlib.util.spec_from_file_location("route_flow_transitions", SCRIPT)
MODULE = importlib.util.module_from_spec(SPEC)
assert SPEC and SPEC.loader
SPEC.loader.exec_module(MODULE)


def test_validate_flow_done_outbox_rejects_transcript_prefix():
    command_meta = {
        "Flow source outbox": "",
        "Flow owner seat": "ba-forseti",
    }
    outbox_text = """I'll read the upstream context first.

- Status: done
- Flow outcome: Requirements ready
- Summary: Complete.
"""

    errors = MODULE.validate_flow_done_outbox(command_meta, outbox_text)

    assert any("first non-empty line" in error for error in errors)


def test_validate_flow_done_outbox_rejects_tool_transcript_markers():
    command_meta = {
        "Flow source outbox": "",
        "Flow owner seat": "ba-forseti",
    }
    outbox_text = """- Status: done
- Summary: Completed review.

## Step 1:
**Tool call:** bash
"""

    errors = MODULE.validate_flow_done_outbox(command_meta, outbox_text)

    assert any("tool-call or transcript markers" in error for error in errors)


def test_validate_flow_done_outbox_rejects_semantic_divergence(tmp_path, monkeypatch):
    root = tmp_path / "hq"
    root.mkdir()
    source = root / "source.md"
    source.write_text(
        "- Status: done\n"
        "- Summary: Duplicate keyboard shortcuts bar is rendering multiple times in the Forseti UI.\n",
        encoding="utf-8",
    )
    monkeypatch.setattr(MODULE, "ROOT", root)
    command_meta = {
        "Flow source outbox": "source.md",
        "Flow owner seat": "ba-forseti",
    }
    outbox_text = """- Status: done
- Flow outcome: Requirements ready
- Summary: AI chatbot assistant with conversation memory and Drupal content surfacing.
"""

    errors = MODULE.validate_flow_done_outbox(command_meta, outbox_text)

    assert any("semantically divergent" in error for error in errors)


def test_validate_flow_done_outbox_accepts_semantically_aligned_summary(tmp_path, monkeypatch):
    root = tmp_path / "hq"
    root.mkdir()
    source = root / "source.md"
    source.write_text(
        "- Status: done\n"
        "- Summary: Duplicate keyboard shortcuts bar is rendering multiple times in the Forseti UI.\n",
        encoding="utf-8",
    )
    monkeypatch.setattr(MODULE, "ROOT", root)
    command_meta = {
        "Flow source outbox": "source.md",
        "Flow owner seat": "ba-forseti",
    }
    outbox_text = """- Status: done
- Flow outcome: Requirements ready
- Summary: The Forseti UI should show the keyboard shortcuts bar only once and remove duplicate shortcut rendering.
"""

    errors = MODULE.validate_flow_done_outbox(command_meta, outbox_text)

    assert errors == []
