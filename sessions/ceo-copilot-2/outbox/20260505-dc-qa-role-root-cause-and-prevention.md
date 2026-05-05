- Status: done
- Summary: Identified a QA role-level failure pattern behind the Dungeoncrawler release stall and applied prevention changes: standardized QA outbox response contracts across QA seats and added Gate 2 follow-up dedup so repeated QA failures cannot flood a release with duplicate follow-up items.

## Root cause
- `qa-dungeoncrawler` produced multiple malformed outboxes that began with planning narration / tool transcript content instead of the canonical `- Status:` / `- Summary:` header.
- Historic executor failures for the same seat show blank responses (`tmp/executor-failures/*-qa-dungeoncrawler.md`), indicating the seat also suffered the stale-session / empty-response failure mode previously seen on Forseti.
- PM had already written an outbox claiming the stale release-s features were retargeted into release-t, but that work never persisted to the feature files, so the runtime source of truth drifted from the narrative outbox.
- `scripts/ceo-pipeline-remediate.py` had a cross-day dedup guard for signoff reminders, but not for `gate2-followup`, so the remediation loop could keep generating fresh Gate 2 follow-up items for the same release when QA stayed broken.

## Prevention changes applied
- Added `gate2-followup` pending-item dedup in `scripts/ceo-pipeline-remediate.py`.
- Added regression test coverage in `scripts/tests/test_ceo_pipeline_remediate.py` for stale pending Gate 2 follow-up items.
- Standardized the QA outbox response contract across QA seats:
  - first two lines must be `- Status:` and `- Summary:`
  - canonical outbox text must come first
  - planning narration / `<tool_call>` / `<tool_response>` transcripts are explicitly forbidden in outbox artifacts
- Updated:
  - `org-chart/agents/instructions/qa-dungeoncrawler.instructions.md`
  - `org-chart/agents/instructions/qa-forseti.instructions.md`
  - `org-chart/agents/instructions/qa-forseti-agent-tracker.instructions.md`
  - `org-chart/agents/instructions/qa-jobhunter.instructions.md`
  - `org-chart/agents/instructions/qa-open-source.instructions.md`
  - `org-chart/agents/instructions/qa-infra.instructions.md`

## Operational conclusion
- The Dungeoncrawler release did not merely stall on missing final QA work; it entered a bad state because QA response formatting was unreliable, executor retries were returning blank results, and remediation automation lacked a release-level dedup guard for repeated Gate 2 follow-up dispatches.
- Prevention is now materially stronger, but `qa-dungeoncrawler` should still be treated as a seat needing a fresh session / clean redispatch before trusting new Gate 2 execution.

---
- Agent: ceo-copilot-2
- Generated: 2026-05-05T00:00:00Z
