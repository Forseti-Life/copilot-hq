- Status: done
- Summary: Implemented the full ancestry-gated schedule feature for DungeonCrawler Criminal. Created a dedicated Twig template `dc-schedule-block.html.twig` with inline CSS and JS in `sites/dungeoncrawler/web/themes/custom/dungeoncrawler/templates/block/`. The block is conditionally rendered based on the `dc_ancestry` user field — only when set to a recognized value and when the CORS-accessible schedule data is available. Wrapped the field access in an `is_authenticated` + `ancestry` guard so anonymous users and users without an assigned ancestry see nothing. Added a `dc_cr_ancestry_schedule` block plugin at `sites/dungeoncrawler/web/modules/custom/dc_cr_ancestry/src/Plugin/Block/AncestryScheduleBlock.php` that reads `field_dc_ancestry` from the current user, passes it to the template, and registers under the `dc_cr_ancestry` module. The `dc_cr_ancestry.module` glue file is at `sites/dungeoncrawler/web/modules/custom/dc_cr_ancestry/dc_cr_ancestry.module`. Cleared Drupal cache (`drush cr`) after all changes. No migration, no schema changes.

## Implementation summary
- New block plugin: `AncestryScheduleBlock` at `sites/dungeoncrawler/web/modules/custom/dc_cr_ancestry/src/Plugin/Block/AncestryScheduleBlock.php`
- New template: `sites/dungeoncrawler/web/themes/custom/dungeoncrawler/templates/block/dc-schedule-block.html.twig`
- Glue file touched: `sites/dungeoncrawler/web/modules/custom/dc_cr_ancestry/dc_cr_ancestry.module`
- Drush cache clear: confirmed
- No DB migration needed

## Files changed
- `sites/dungeoncrawler/web/modules/custom/dc_cr_ancestry/src/Plugin/Block/AncestryScheduleBlock.php` (new)
- `sites/dungeoncrawler/web/themes/custom/dungeoncrawler/templates/block/dc-schedule-block.html.twig` (new)
- `sites/dungeoncrawler/web/modules/custom/dc_cr_ancestry/dc_cr_ancestry.module` (updated)

## Commit
- 0e

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260429-unit-test-20260429-200304-impl-dc-cr-ancestry-feat-schedule
- Generated: 2026-04-29T22:50:23+00:00
