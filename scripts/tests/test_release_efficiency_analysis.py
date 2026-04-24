import importlib.util
from datetime import datetime, timezone
from pathlib import Path


SCRIPT = Path(__file__).resolve().parents[1] / "release-efficiency-analysis.py"


def _load_module():
    spec = importlib.util.spec_from_file_location("release_efficiency_analysis", SCRIPT)
    module = importlib.util.module_from_spec(spec)
    assert spec.loader is not None
    spec.loader.exec_module(module)
    return module


def test_artifact_dt_prefers_filename_timestamp(tmp_path):
    mod = _load_module()
    artifact = tmp_path / "20260420-140050-gate2-approve-release-s.md"
    artifact.write_text("ok\n", encoding="utf-8")
    artifact.touch()

    dt = mod.artifact_dt(artifact)

    assert dt == datetime(2026, 4, 20, 14, 0, 50, tzinfo=timezone.utc)


def test_find_r5_audit_time_uses_filename_timestamp(tmp_path):
    mod = _load_module()
    root = tmp_path / "hq"
    mod.ROOT = root

    qa_outbox = root / "sessions" / "qa-dungeoncrawler" / "outbox"
    qa_outbox.mkdir(parents=True)
    artifact = qa_outbox / "20260420-140050-gate2-approve-20260412-dungeoncrawler-release-s.md"
    artifact.write_text("- Status: done\napprove\n", encoding="utf-8")
    artifact.touch()

    push_time = datetime(2026, 4, 20, 13, 28, 58, tzinfo=timezone.utc)

    dt = mod.find_r5_audit_time("dungeoncrawler", push_time, "20260412-dungeoncrawler-release-s")

    assert dt == datetime(2026, 4, 20, 14, 0, 50, tzinfo=timezone.utc)


def test_find_r5_audit_time_ignores_unrelated_recovery_pass(tmp_path):
    mod = _load_module()
    root = tmp_path / "hq"
    mod.ROOT = root

    ceo_outbox = root / "sessions" / "ceo-copilot-2" / "outbox"
    qa_outbox = root / "sessions" / "qa-forseti" / "outbox"
    ceo_outbox.mkdir(parents=True)
    qa_outbox.mkdir(parents=True)

    (ceo_outbox / "20260420-111315-dungeoncrawler-recovery-pass.md").write_text(
        "- Summary: Gate R5 production audit for dungeoncrawler release 20260412-dungeoncrawler-release-s\n",
        encoding="utf-8",
    )
    (qa_outbox / "20260420-191623-gate2-explicit-approval.md").write_text(
        "- Status: done\npost-push approval for 20260412-forseti-release-q\n",
        encoding="utf-8",
    )

    push_time = datetime(2026, 4, 20, 5, 3, 4, tzinfo=timezone.utc)

    dt = mod.find_r5_audit_time("forseti", push_time, "20260412-forseti-release-q")

    assert dt == datetime(2026, 4, 20, 19, 16, 23, tzinfo=timezone.utc)


def test_find_r5_audit_time_ignores_generic_gate2_followup_without_release_id(tmp_path):
    mod = _load_module()
    root = tmp_path / "hq"
    mod.ROOT = root

    qa_outbox = root / "sessions" / "qa-forseti" / "outbox"
    qa_outbox.mkdir(parents=True)
    (qa_outbox / "20260420-191623-gate2-explicit-approval.md").write_text(
        "- Status: needs-info\n",
        encoding="utf-8",
    )

    push_time = datetime(2026, 4, 20, 5, 3, 4, tzinfo=timezone.utc)

    dt = mod.find_r5_audit_time("forseti", push_time, "20260412-forseti-release-q")

    assert dt is None


def test_find_r5_audit_time_ignores_incomplete_followup_audits(tmp_path):
    mod = _load_module()
    root = tmp_path / "hq"
    mod.ROOT = root

    qa_outbox = root / "sessions" / "qa-forseti" / "outbox"
    qa_outbox.mkdir(parents=True)
    (qa_outbox / "20260420-rerun-full-audit-forseti.life-20260420-105935.md").write_text(
        "- Status: needs-info\n",
        encoding="utf-8",
    )

    push_time = datetime(2026, 4, 20, 5, 3, 4, tzinfo=timezone.utc)

    dt = mod.find_r5_audit_time("forseti", push_time, "20260412-forseti-release-q")

    assert dt is None


def test_find_r5_audit_time_ignores_completed_audits_for_other_releases(tmp_path):
    mod = _load_module()
    root = tmp_path / "hq"
    mod.ROOT = root

    qa_outbox = root / "sessions" / "qa-forseti" / "outbox"
    qa_outbox.mkdir(parents=True)
    (qa_outbox / "20260405-post-release-audit-20260322-forseti-release-next.md").write_text(
        "- Status: done\n",
        encoding="utf-8",
    )

    push_time = datetime(2026, 4, 20, 5, 3, 4, tzinfo=timezone.utc)

    dt = mod.find_r5_audit_time("forseti", push_time, "20260412-forseti-release-q")

    assert dt is None


def test_ceo_proxy_sessions_skip_needs_dispatches(tmp_path):
    mod = _load_module()
    root = tmp_path / "hq"
    mod.ROOT = root

    outbox = root / "sessions" / "ceo-copilot-2" / "outbox"
    outbox.mkdir(parents=True)
    (outbox / "20260420-needs-pm-forseti-20260420-signoff-reminder-20260412-dungeoncrawler-release-s.md").write_text(
        "delegation\n", encoding="utf-8"
    )
    (outbox / "20260420-groom-20260412-dungeoncrawler-release-s.md").write_text(
        "pm proxy\n", encoding="utf-8"
    )

    proxy = mod.ceo_proxy_sessions(
        "20260412-dungeoncrawler-release-s",
        ["dc-cr-halfling-resolve"],
        "20260412-forseti-release-q",
        None,
    )

    assert len(proxy.get("pm", [])) == 1
    assert proxy["pm"][0].name == "20260420-groom-20260412-dungeoncrawler-release-s.md"


def test_dev_outbox_files_ignore_transcript_noise(tmp_path):
    mod = _load_module()
    root = tmp_path / "hq"
    mod.ROOT = root

    outbox = root / "sessions" / "dev-dungeoncrawler" / "outbox"
    outbox.mkdir(parents=True)
    (outbox / "20260410-021500-implement-dc-cr-dwarf-ancestry.md").write_text(
        "# Outbox: dc-cr-dwarf-ancestry\n\n- Status: done\n",
        encoding="utf-8",
    )
    (outbox / "20260410-impl-dc-cr-dwarf-ancestry.md").write_text(
        "Good — the Dwarf base entry exists but is missing data.\n\n- Status: done\n",
        encoding="utf-8",
    )

    files = mod.dev_outbox_files_for_feature("dc-cr-dwarf-ancestry", "dev-dungeoncrawler")

    assert [f.name for f in files] == ["20260410-021500-implement-dc-cr-dwarf-ancestry.md"]


def test_gating_outbox_files_for_release_skips_feature_scoped_pm_handoffs(tmp_path):
    mod = _load_module()
    root = tmp_path / "hq"
    mod.ROOT = root

    outbox = root / "sessions" / "pm-forseti" / "outbox"
    outbox.mkdir(parents=True)
    (outbox / "20260419-groom-20260412-forseti-release-q.md").write_text(
        "- Status: done\n", encoding="utf-8"
    )
    (outbox / "20260420-needs-dev-forseti-20260420-164124-impl-forseti-langgraph-console-admin.md").write_text(
        "- Status: needs-info\n", encoding="utf-8"
    )

    files = mod.gating_outbox_files_for_release(
        "pm-forseti",
        "20260412-forseti-release-q",
        ["forseti-langgraph-console-admin"],
    )

    assert [f.name for f in files] == ["20260419-groom-20260412-forseti-release-q.md"]


def test_gating_outbox_files_for_release_skips_pm_signoff_reminders_after_signoff(tmp_path):
    mod = _load_module()
    root = tmp_path / "hq"
    mod.ROOT = root

    outbox = root / "sessions" / "pm-dungeoncrawler" / "outbox"
    signoffs = root / "sessions" / "pm-dungeoncrawler" / "artifacts" / "release-signoffs"
    outbox.mkdir(parents=True)
    signoffs.mkdir(parents=True)
    (outbox / "20260420-signoff-reminder-20260412-forseti-release-q.md").write_text(
        "- Status: needs-info\n", encoding="utf-8"
    )
    (signoffs / "20260412-forseti-release-q.md").write_text("signed\n", encoding="utf-8")

    files = mod.gating_outbox_files_for_release(
        "pm-dungeoncrawler",
        "20260412-forseti-release-q",
        ["forseti-langgraph-console-admin"],
    )

    assert files == []


def test_manual_code_review_gate_verdict_uses_ceo_gate_approval(tmp_path):
    mod = _load_module()
    root = tmp_path / "hq"
    mod.ROOT = root

    outbox = root / "sessions" / "ceo-copilot-2" / "outbox"
    outbox.mkdir(parents=True)
    (outbox / "20260420-132856-code-review-gate-20260412-forseti-release-q.md").write_text(
        "- Status: done\n- Summary: Verdict: APPROVE\n",
        encoding="utf-8",
    )

    verdict = mod.manual_code_review_gate_verdict("20260412-forseti-release-q")

    assert verdict == "approve"


def test_qa_gate2_evidence_recorded_true_for_release_approve(tmp_path):
    mod = _load_module()
    root = tmp_path / "hq"
    mod.ROOT = root

    outbox = root / "sessions" / "qa-forseti" / "outbox"
    outbox.mkdir(parents=True)
    (outbox / "20260420-020547-gate2-approve-20260412-forseti-release-q.md").write_text(
        "- Status: done\n",
        encoding="utf-8",
    )
    (outbox / "20260420-164124-suite-activate-forseti-langgraph-console-admin.md").write_text(
        "- Status: needs-info\n",
        encoding="utf-8",
    )

    assert mod.qa_gate2_evidence_recorded("qa-forseti", "20260412-forseti-release-q") is True


def test_latest_dev_outbox_files_for_release_keeps_latest_per_feature(tmp_path):
    mod = _load_module()
    root = tmp_path / "hq"
    mod.ROOT = root

    outbox = root / "sessions" / "dev-forseti" / "outbox"
    outbox.mkdir(parents=True)
    (outbox / "20260420-164119-impl-forseti-langgraph-console-observe.md").write_text(
        "- Status: needs-info\n",
        encoding="utf-8",
    )
    (outbox / "20260420-172644-impl-forseti-langgraph-console-observe.md").write_text(
        "- Status: done\n",
        encoding="utf-8",
    )
    (outbox / "20260420-164124-impl-forseti-langgraph-console-admin.md").write_text(
        "- Status: needs-info\n",
        encoding="utf-8",
    )
    (outbox / "20260420-172645-impl-forseti-langgraph-console-admin.md").write_text(
        "- Status: done\n",
        encoding="utf-8",
    )

    files = mod.latest_dev_outbox_files_for_release(
        ["forseti-langgraph-console-admin", "forseti-langgraph-console-observe"],
        "dev-forseti",
    )

    assert [f.name for f in files] == [
        "20260420-172644-impl-forseti-langgraph-console-observe.md",
        "20260420-172645-impl-forseti-langgraph-console-admin.md",
    ]
