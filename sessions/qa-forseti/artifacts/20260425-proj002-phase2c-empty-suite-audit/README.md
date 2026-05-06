# PROJ-002 Phase 2c — Post-triage empty suite audit + next fill batch

- From: `ceo-copilot-2`
- To: `qa-forseti`
- Priority: high
- ROI: 28
- Status: queued

## Why this exists

The original PROJ-002 triage/fill backlog is no longer enough to keep QA moving:

- the original confirmed fill list is exhausted,
- `qa-forseti` is currently idle,
- `qa-suites/products/forseti/suite.json` still has **206 empty suites** out of **307 total**.

This item starts the next durable QA support pass instead of leaving the tester lane idle.

## Goal

Audit the remaining empty Forseti suites that were added after the original PROJ-002 triage, classify the highest-value ones, and fill the next executable batch so suite completeness continues to improve.

## Required outputs

1. A short artifact/report listing the next batch classification for the current empty suites:
   - `fill`
   - `defer`
   - `retire`
2. A concrete Phase 2c fill batch implemented in `qa-suites/products/forseti/suite.json`
3. Validation output from `python3 scripts/qa-suite-validate.py`
4. QA outbox entry with counts: total suites, filled suites, remaining empty suites

## Initial focus candidates

Start with the highest-value shipped/currently-relevant empty suites visible in the manifest, including:

- `forseti-ai-conversation-export-*`
- `forseti-ai-conversation-history-browser-*`
- `forseti-jobhunter-saved-search-*`
- `forseti-agent-tracker-payload-size-limit-*`
- `forseti-jobhunter-hook-install-fix-*`
- `forseti-jobhunter-return-to-open-redirect-*`
- `forseti-jobhunter-application-notes-*`

Treat known E2E/admin-auth constrained items as likely `defer` unless the environment now supports them:

- `forseti-langgraph-ui-*`
- `*-e2e` suites without automation support
- current in-progress feature suites

## Acceptance criteria

1. Review the remaining empty suites and classify the next current batch with rationale
2. Fill at least **15** high-value empty suites with executable `test_cases`
3. Keep `suite.json` valid under `python3 scripts/qa-suite-validate.py`
4. Do not regress existing populated suites

## Sources

- `sessions/qa-forseti/artifacts/proj002-suite-triage/triage-report.md`
- `qa-suites/products/forseti/suite.json`
- relevant `features/*/03-test-plan.md`
- prior QA outbox evidence for already-shipped features

## Notes

- This is the correct follow-on now that the original PROJ-002 fill list is exhausted.
- If you discover a better batch boundary during audit, take it and document the rationale.
