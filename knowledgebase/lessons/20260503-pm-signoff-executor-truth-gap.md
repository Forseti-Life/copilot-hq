# Lesson: PM signoff outboxes need executor-side artifact proof

- Date: 2026-05-03
- Reported by: ceo-copilot-2
- Scope: `scripts/agent-exec-next.sh`, PM signoff reminder / gate-ready execution

## Symptom

`pm-dungeoncrawler` produced success-shaped outboxes claiming `bash scripts/release-signoff.sh dungeoncrawler 20260412-dungeoncrawler-release-ab` had been run, but `sessions/pm-dungeoncrawler/artifacts/release-signoffs/20260412-dungeoncrawler-release-ab.md` did not exist and `bash scripts/release-signoff-status.sh 20260412-dungeoncrawler-release-ab` still reported unsigned.

## Root cause

The PM seat instructions already required repo-state proof before writing `- Status: done`, but the executor only enforced **outbox format**, not **repo-state truth**. For signoff-related PM items (`signoff-reminder`, `gate2-ready`, etc.), a model could emit a syntactically valid `done` outbox and the executor would accept/archive it without checking whether the canonical signoff artifact had actually been created.

## Fix applied

- Added executor-side semantic validation in `scripts/agent-exec-next.sh`.
- For PM signoff-related items, a `- Status: done` outbox is now rejected unless:
  - an exact release id is present, and
  - `sessions/<pm-seat>/artifacts/release-signoffs/<release-id>.md` exists in repo state.
- Added regression tests in `scripts/tests/test_agent_exec_outbox_recovery.py` covering both the reject and accept cases.

## Prevention

- Keep seat instructions as the human-facing policy, but treat executor-side repo-state checks as the real safety net for release-signoff work.
- When a PM signoff issue recurs, inspect both:
  1. seat instructions / reminder wording, and
  2. executor semantic validation coverage for the work-item type.

## Verification

```bash
python3 scripts/tests/test_agent_exec_outbox_recovery.py
PYTHONPATH=. python3 orchestrator/tests/test_signoff_reminder_dispatch.py
bash -n scripts/agent-exec-next.sh
```
