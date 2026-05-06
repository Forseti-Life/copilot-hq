# Task: QA Verification — prep-drupal-ai-extraction

- Created: 2026-04-24T10:12:30+00:00
- Owner: qa-infra
- Priority: P1
- ROI: 40

## Scope

Write a verification report for dev-open-source completed item: `20260420-prep-drupal-ai-extraction`

Reference dev work: `sessions/dev-open-source/outbox/20260420-prep-drupal-ai-extraction.md`

## Acceptance criteria

1. **Read dev outbox** and extract acceptance criteria / verification steps
2. **Run targeted unit test** for this specific feature (derive test from AC)
3. **Update regression checklist**: `org-chart/sites/open-source/qa-regression-checklist.md` with this item
4. **Role-based audit** (if needed): Run `scripts/site-audit-run.sh` per runbooks/role-based-url-audit.md
5. **Write Verification Report** with:
   - Test steps executed
   - Results: APPROVE or BLOCK (explicit)
   - Evidence: URLs, logs, or command output

## Success criteria

- Verification report written to outbox with explicit APPROVE/BLOCK decision
- Regression checklist updated
- No new blockers discovered

CEO note: Previous attempts had scope ambiguity. Rewritten with tighter AC. Proceed.
- Agent: qa-infra
- Status: pending
