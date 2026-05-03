- Status: done
- Summary: RCA complete for the recurring PM signoff drift seen in the DungeonCrawler lane. This was **not** primarily an instruction gap: `pm-dungeoncrawler.instructions.md` already had an explicit release-signoff proof rule requiring the artifact plus `release-signoff-status.sh` confirmation before `Status: done`. The actual root cause was executor-side: `scripts/agent-exec-next.sh` validated only canonical outbox format, so PM signoff-related inbox items could emit success-shaped markdown claiming signoff had run and still be accepted/archived even when `sessions/pm-dungeoncrawler/artifacts/release-signoffs/<release-id>.md` did not exist. I fixed the executor to reject any PM signoff-related `done` outbox unless the exact release id is present and the corresponding signoff artifact exists in repo state. Added regression coverage in `scripts/tests/test_agent_exec_outbox_recovery.py` for both reject/accept paths. This converts the prior “instructions-only” safeguard into a hard repo-state guard at the executor boundary.

## Evidence chain
- `sessions/pm-dungeoncrawler/outbox/20260503-202718-gate2-ready-dungeoncrawler.md` claimed `release-signoff.sh` had been run, but the signoff artifact was absent.
- `sessions/pm-dungeoncrawler/outbox/20260503-signoff-reminder-20260412-dungeoncrawler-release-ab.md` initially emitted `Status: in_progress` / narrative execution text instead of repo-state proof.
- `pm-dungeoncrawler.instructions.md` already contained the release signoff proof rule (`ls .../release-signoffs/<release-id>.md` + `bash scripts/release-signoff-status.sh <release-id>`).
- `agent-exec-next.sh` semantic validation had no signoff-specific repo-state check before this fix.

## Fixes applied
- Updated `scripts/agent-exec-next.sh` to reject PM signoff-related `done` outboxes when the canonical artifact is missing.
- Added regression tests in `scripts/tests/test_agent_exec_outbox_recovery.py`.
- Added KB lesson `knowledgebase/lessons/20260503-pm-signoff-executor-truth-gap.md`.

## Verification
- `python3 scripts/tests/test_agent_exec_outbox_recovery.py`
- `PYTHONPATH=. python3 orchestrator/tests/test_signoff_reminder_dispatch.py`
- `bash -n scripts/agent-exec-next.sh`

## Next actions
- Watch the next real PM signoff reminder/gate-ready cycle; it should now retry or quarantine instead of silently accepting a false `done`.
- If the seat still struggles, tighten the retry prompt for signoff-related items, but only if the new executor guard proves insufficient.

## Blockers
- None.

---
- Agent: ceo-copilot-2
- Generated: 2026-05-03T20:40:00+00:00
