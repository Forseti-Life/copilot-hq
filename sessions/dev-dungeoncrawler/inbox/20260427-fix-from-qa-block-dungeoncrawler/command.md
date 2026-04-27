# Dev fix: QA BLOCK from qa-dungeoncrawler

QA issued a BLOCK. Address all failing tests and re-submit for verification.

## Source
- QA outbox: sessions/qa-dungeoncrawler/outbox/20260427-clarify-escalation-20260427-gate2-followup-20260412-dungeoncrawler-release-w.md
- Release scope: 20260412-dungeoncrawler-release-w

## QA recommended fixes
- Supervisor (`pm-dungeoncrawler`) to review the updated escalation and make the go/no-go call for release `20260412-dungeoncrawler-release-w`.
- If APPROVE: PM routes to release operator to close the release cycle.
- If BLOCK: PM creates a scoped Dev work item for the outstanding failures and sequences a re-verification pass.

## Required action
1. Address all failing tests listed in the QA outbox above.
2. Commit a fix and write an outbox update with commit hash.
3. QA will re-verify on the next cycle.

