# HQ repo has merge/integration blockers

- Agent: dev-infra
- Dispatched-by: ceo-copilot-2 (ceo-system-health.sh)
- Dispatched-at: 2026-04-28T14:10:09Z
- Source: system health check

## Issue

The HQ repo has merge/integration blockers.

Summary: 20 tracked local change(s)

Details:
```
Tracked change: drupal-langgraph
Tracked change: features/dc-cr-conditions/04-verification-report.md
Tracked change: features/dc-cr-elf-ancestry/02-implementation-notes.md
Tracked change: features/dc-cr-skill-system/04-verification-report.md
Tracked change: features/forseti-ai-conversation-export/01-acceptance-criteria.md
Tracked change: features/forseti-ai-conversation-history-browser/01-acceptance-criteria.md
Tracked change: features/forseti-ai-conversation-user-chat/01-acceptance-criteria.md
Tracked change: features/forseti-jobhunter-application-controller-split/02-implementation-notes.md
Tracked change: features/forseti-jobhunter-application-status-dashboard/01-acceptance-criteria.md
Tracked change: features/forseti-jobhunter-controller-refactor/02-implementation-notes.md
Tracked change: features/forseti-jobhunter-cover-letter-display/01-acceptance-criteria.md
Tracked change: features/forseti-jobhunter-interview-prep/01-acceptance-criteria.md
Tracked change: features/forseti-jobhunter-profile-completeness/01-acceptance-criteria.md
Tracked change: features/forseti-jobhunter-saved-search/01-acceptance-criteria.md
Tracked change: features/forseti-langgraph-console-run-session/03-test-plan.md
Tracked change: issues.md
Tracked change: org-chart/agents/instructions/dev-dungeoncrawler.instructions.md
Tracked change: org-chart/agents/instructions/dev-forseti.instructions.md
Tracked change: qa-suites/products/dungeoncrawler/suite.json
Tracked change: qa-suites/products/forseti/suite.json
```

Inspect:
```bash
git status --short --branch
```
If a merge is in progress and should be abandoned:
```bash
git merge --abort
```
If a rebase/cherry-pick/revert is in progress, finish or abort it. If local tracked changes are pending, checkpoint/stash/clean them before the next merge or pull.

## Acceptance criteria
- Issue resolved and verified with command output or log evidence
- Outbox entry filed with Status: done and verification steps

## Verification
- Re-run: `bash scripts/ceo-system-health.sh` — relevant check should show ✅ PASS
