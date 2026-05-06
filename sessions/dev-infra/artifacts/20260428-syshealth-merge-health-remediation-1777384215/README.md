# HQ repo has merge/integration blockers

- Agent: dev-infra
- Dispatched-by: ceo-copilot-2 (ceo-system-health.sh)
- Dispatched-at: 2026-04-28T13:50:08Z
- Source: system health check

## Issue

The HQ repo has merge/integration blockers.

Summary: 4 tracked local change(s)

Details:
```
Tracked change: drupal-langgraph
Tracked change: features/dc-cr-languages/feature.md
Tracked change: sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/tests/src/Functional/Controller/LanguagesControllerTest.php
Tracked change: sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/tests/src/Unit/Service/LanguageServiceTest.php
Untracked file: sites/dungeoncrawler/vendor/bin/.phpunit.result.cache
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
