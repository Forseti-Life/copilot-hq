- Status: done
- Summary: Resolved the live Dungeoncrawler PHP fatal caused by missing GM running-guide tables (`dc_gm_rarity`, `dc_encounter_metadata`, `dc_gm_campaign_design`, and related 10046/10047 tables). Root cause was that production was still at `dungeoncrawler_content` schema `10043`, so pending updates `10044`, `10046`, and `10047` had never completed. `10044` was itself broken by a Drupal 11-incompatible `merge()->key([...])` call in `DeityService::upsert()`, and `10046`/`10047` used MySQL-invalid defaults on `TEXT` columns. I fixed the broken merge-key usage in `DeityService` and `MapGeneratorService`, removed invalid `TEXT` defaults from the pending update definitions in `dungeoncrawler_content.install`, then ran `drush updatedb -y` successfully to schema `10047`.

## Evidence
- Before fix:
  - `SELECT name, value FROM key_value WHERE collection='system.schema' AND name='dungeoncrawler_content';` → `i:10043;`
  - `SHOW TABLES LIKE 'dc_gm_rarity';` → no rows
  - `drush updatedb -y` blocked in `dungeoncrawler_content_update_10044`
- Code fixes:
  - `src/Service/DeityService.php` — `merge()->key('deity_id', ...)`
  - `src/Service/MapGeneratorService.php` — replaced array-form merge keys with scalar chained keys
  - `dungeoncrawler_content.install` — removed invalid defaults from `TEXT` columns in pending updates 10046/10047
- After fix:
  - `drush updatedb -y` completed through `10047`
  - `SELECT name, value FROM key_value WHERE collection='system.schema' AND name='dungeoncrawler_content';` → `i:10047;`
  - `SHOW TABLES LIKE 'dc_gm_rarity';` → present
  - `SHOW TABLES LIKE 'dc_gm_campaign_design';` → present
  - `drush php:eval '$svc = \Drupal::service("dungeoncrawler_content.gm_running_guide"); var_export($svc->getRarityAllowlist(1));'` returns a valid default payload
  - `/var/log/apache2/dungeoncrawler_error.log` contains `count_last_30m=0` for the prior missing-table signatures

## Remaining note
- `ceo-system-health.sh` still shows a 24-hour historical warning for the earlier fatal lines, but there are no fresh missing-table errors in the last 30 minutes; that warning will age out naturally.
