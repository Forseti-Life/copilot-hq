- Status: done
- Summary: Updated the Dungeoncrawler `/hexmap` page-render path to resume from the persisted campaign-character room and hex when the URL does not explicitly override them, while preserving manual deep-link overrides and reusing one shared campaign-character lookup path across launch hydration and player-entity injection.

## What changed

- Updated both maintained `HexMapController.php` copies:
  - `dungeoncrawler-pf2e/web/modules/custom/dungeoncrawler_content/src/Controller/HexMapController.php`
  - `sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/src/Controller/HexMapController.php`
- Added launch-context override flags at request parse time so the controller can distinguish:
  - explicit URL overrides
  - missing query params that should fall back to persisted campaign state
- Added `applyPersistedLaunchContext()` so non-URL-driven `/hexmap` resumes inherit:
  - persisted room id from `location_ref` / `last_room_id`
  - persisted hex from `position_q` / `position_r`
- Added shared helpers:
  - `loadCampaignCharacterRecord()`
  - `resolveCampaignCharacterRoomId()`
- Refactored launch summary hydration and player-character entity injection to use the shared record loader.

## Behavior impact

1. Refresh/resume flows now reopen the player in the last persisted room/hex even if the URL only carries campaign/character context.
2. Explicit deep links still win:
   - if `room_id` is present in the query string, it is respected
   - if `start_q` / `start_r` are present, they are respected
3. Player token placement during page render is now aligned with the same campaign-character record used for summary hydration.

## Verification

- `php -l` passed for both updated controller files.
- Production Drupal cache rebuild succeeded with:
  - `cd /var/www/html/dungeoncrawler && vendor/bin/drush cr`

## Remaining thread

- The broader architect thread remains the `/hexmap` thin-client cleanup:
  - remove the remaining unauthenticated/local connection-click fallback when appropriate
  - continue extracting shell/runtime responsibilities out of `hexmap.js`
