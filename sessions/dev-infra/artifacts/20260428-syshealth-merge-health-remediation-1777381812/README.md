# HQ repo has merge/integration blockers

- Agent: dev-infra
- Dispatched-by: ceo-copilot-2 (ceo-system-health.sh)
- Dispatched-at: 2026-04-28T13:10:09Z
- Source: system health check

## Issue

The HQ repo has merge/integration blockers.

Summary: 9 tracked local change(s)

Details:
```
Tracked change: drupal-langgraph
Tracked change: features/dc-apg-archetypes/feature.md
Tracked change: features/dc-apg-spells/feature.md
Tracked change: features/dc-cr-economy/feature.md
Tracked change: features/dc-cr-elf-ancestry/feature.md
Tracked change: features/dc-cr-languages/feature.md
Tracked change: orchestrator/runtime_graph/consume_replies.py
Tracked change: orchestrator/runtime_graph/engine.py
Tracked change: org-chart/sites/dungeoncrawler/qa-regression-checklist.md
Untracked file: orchestrator/runtime_graph/catalog.py
Untracked file: orchestrator/runtime_graph/export_flow_catalog.py
Untracked file: orchestrator/tests/test_runtime_flow_catalog.py
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
