# HQ repo has merge/integration blockers

- Agent: dev-infra
- Dispatched-by: ceo-copilot-2 (ceo-system-health.sh)
- Dispatched-at: 2026-04-25T20:30:09Z
- Source: system health check

## Issue

The HQ repo has merge/integration blockers.

Summary: 2 tracked local change(s)

Details:
```
Tracked change: drupal-langgraph
Tracked change: sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/dungeoncrawler_content.module
Untracked file: sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/config/install/field.instance.node.character_class.field_class_description.yml
Untracked file: sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/config/install/field.instance.node.character_class.field_class_features.yml
Untracked file: sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/config/install/field.instance.node.character_class.field_class_hp_per_level.yml
Untracked file: sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/config/install/field.instance.node.character_class.field_class_key_ability.yml
Untracked file: sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/config/install/field.instance.node.character_class.field_class_name.yml
Untracked file: sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/config/install/field.instance.node.character_class.field_class_proficiencies.yml
Untracked file: sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/config/install/field.instance.node.character_class.field_class_trained_skills.yml
Untracked file: sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/config/install/field.storage.node.field_class_description.yml
Untracked file: sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/config/install/field.storage.node.field_class_features.yml
Untracked file: sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/config/install/field.storage.node.field_class_hp_per_level.yml
Untracked file: sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/config/install/field.storage.node.field_class_key_ability.yml
Untracked file: sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/config/install/field.storage.node.field_class_name.yml
Untracked file: sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/config/install/field.storage.node.field_class_proficiencies.yml
Untracked file: sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/config/install/field.storage.node.field_class_trained_skills.yml
Untracked file: sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/config/install/node.type.character_class.yml
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
