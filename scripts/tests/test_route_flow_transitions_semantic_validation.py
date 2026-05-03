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

    errors = MODULE.validate_flow_done_outbox(command_meta, "", outbox_text)

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

    errors = MODULE.validate_flow_done_outbox(command_meta, "", outbox_text)

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

    errors = MODULE.validate_flow_done_outbox(command_meta, "", outbox_text)

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

    errors = MODULE.validate_flow_done_outbox(command_meta, "", outbox_text)

    assert errors == []


def test_validate_flow_done_outbox_requires_existing_pm_signoff_artifact(tmp_path, monkeypatch):
    root = tmp_path / "hq"
    root.mkdir()
    monkeypatch.setattr(MODULE, "ROOT", root)
    command_meta = {
        "Flow id": "release_shipping_flow",
        "Flow node": "PM Signoff Readiness Check",
        "Flow owner seat": "pm-dungeoncrawler",
        "Flow run id": "20260412-dungeoncrawler-release-aa",
    }
    outbox_text = """- Status: done
- Summary: Release gates are clear and signoff was completed.
- Flow outcome: Ready for signoff and push
"""

    errors = MODULE.validate_flow_done_outbox(command_meta, "", outbox_text)

    assert any("canonical PM signoff artifact path" in error for error in errors)
    assert any("until the canonical PM signoff artifact exists" in error for error in errors)


def test_validate_flow_done_outbox_accepts_ready_for_push_with_existing_signoff_artifact(tmp_path, monkeypatch):
    root = tmp_path / "hq"
    artifact = root / "sessions" / "pm-dungeoncrawler" / "artifacts" / "release-signoffs" / "20260412-dungeoncrawler-release-aa.md"
    artifact.parent.mkdir(parents=True)
    artifact.write_text("signed\n", encoding="utf-8")
    monkeypatch.setattr(MODULE, "ROOT", root)
    command_meta = {
        "Flow id": "release_shipping_flow",
        "Flow node": "PM Signoff Readiness Check",
        "Flow owner seat": "pm-dungeoncrawler",
        "Flow run id": "20260412-dungeoncrawler-release-aa",
    }
    outbox_text = """- Status: done
- Summary: Release gates are clear and signoff artifact `sessions/pm-dungeoncrawler/artifacts/release-signoffs/20260412-dungeoncrawler-release-aa.md` is recorded.
- Flow outcome: Ready for signoff and push
"""

    errors = MODULE.validate_flow_done_outbox(command_meta, "", outbox_text)

    assert errors == []


def test_validate_flow_done_outbox_requires_feature_id_for_pm_delivery_approval():
    command_meta = {
        "Flow id": "feature_request_intake",
        "Flow node": "PM Scope Decision",
        "Flow owner seat": "pm-forseti",
    }
    outbox_text = """- Status: done
- Summary: Approving this suggestion for delivery.
- Flow outcome: Approved for delivery
"""

    errors = MODULE.validate_flow_done_outbox(command_meta, "", outbox_text)

    assert any("PM Scope Decision must include '- Feature id:" in error for error in errors)


def test_validate_flow_done_outbox_requires_matching_feature_id_for_prepare_delivery_handoff(tmp_path, monkeypatch):
    root = tmp_path / "hq"
    root.mkdir()
    source = root / "source.md"
    source.write_text(
        "- Status: done\n"
        "- Summary: Approved request.\n"
        "- Flow outcome: Approved for delivery\n"
        "- Feature id: forseti-homepage-fun-time\n",
        encoding="utf-8",
    )
    monkeypatch.setattr(MODULE, "ROOT", root)
    command_meta = {
        "Flow id": "feature_request_intake",
        "Flow node": "Prepare Delivery Handoff",
        "Flow owner seat": "ba-forseti",
        "Flow source outbox": "source.md",
    }
    outbox_text = """- Status: done
- Summary: Prepared the delivery handoff.
- Feature id: forseti-ai-assistant
"""

    errors = MODULE.validate_flow_done_outbox(command_meta, "", outbox_text)

    assert any("must preserve the exact Feature id" in error for error in errors)


def test_validate_flow_done_outbox_rejects_release_code_review_clear_when_scope_artifacts_are_missing():
    command_meta = {
        "Flow id": "release_shipping_flow",
        "Flow node": "Release Code Review",
        "Flow owner seat": "code-review-dungeoncrawler",
    }
    command_text = """# Flow handoff: release_shipping_flow / Release Code Review

## Release scope artifacts
- No active release-scoped feature artifacts were found for this release.
"""
    outbox_text = """- Status: done
- Flow outcome: No MEDIUM+ findings
- Summary: No actionable findings remain.
"""

    errors = MODULE.validate_flow_done_outbox(command_meta, command_text, outbox_text)

    assert any("cannot clear with 'No MEDIUM+ findings'" in error for error in errors)


def test_main_queues_validation_retry_when_conditional_done_outbox_omits_flow_outcome(tmp_path, monkeypatch):
    root = tmp_path / "hq"
    command_dir = root / "sessions" / "code-review-dungeoncrawler" / "inbox" / "20260501-release-code-review"
    command_dir.mkdir(parents=True, exist_ok=True)
    command_path = command_dir / "command.md"
    command_path.write_text(
        """- Flow id: release_shipping_flow
- Flow run id: 20260501-dungeoncrawler-release-z
- Flow node: Release Code Review
- Flow owner seat: code-review-dungeoncrawler

# Flow handoff: release_shipping_flow / Release Code Review

- Available flow outcomes: MEDIUM+ findings present | No MEDIUM+ findings
""",
        encoding="utf-8",
    )
    (command_dir / "roi.txt").write_text("50\n", encoding="utf-8")
    outbox = root / "sessions" / "code-review-dungeoncrawler" / "outbox" / "20260501-release-code-review.md"
    outbox.parent.mkdir(parents=True, exist_ok=True)
    outbox.write_text(
        """- Status: done
- Summary: Reviewed the release handoff and found no critical concerns.
""",
        encoding="utf-8",
    )

    monkeypatch.setattr(MODULE, "ROOT", root)
    monkeypatch.setattr(MODULE, "DRUSH_ROOT", root / "missing-drush-root")
    monkeypatch.setattr(MODULE.sys, "argv", ["route-flow-transitions.py", "code-review-dungeoncrawler", "20260501-release-code-review", str(outbox)])

    result = MODULE.main()

    assert result == 0
    retry_items = list((root / "sessions" / "code-review-dungeoncrawler" / "inbox").glob("*-validation-r1"))
    assert len(retry_items) == 1
    retry_command = (retry_items[0] / "command.md").read_text(encoding="utf-8")
    assert "did not pass flow-validation and was not routed" in retry_command
    assert "must include '- Flow outcome: <value>' for conditional routing" in retry_command
