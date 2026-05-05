- Status: done
- Summary: Extracted `/hexmap` Map-tab room-travel logic into a dedicated `HexmapNavigation.js` adapter in both maintained Dungeoncrawler trees, converted the legacy `hexmap.js` navigation methods into delegating wrappers, and removed the silent local connection-click fallback so room travel remains server-authoritative.

## What changed

- Added `HexmapNavigation.js` in both maintained trees:
  - `dungeoncrawler-pf2e/web/modules/custom/dungeoncrawler_content/js/HexmapNavigation.js`
  - `sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/js/HexmapNavigation.js`
- Moved these Map-tab navigation responsibilities out of `hexmap.js` and behind the adapter:
  - merge navigation entities
  - merge navigation connections
  - update selected entity dungeon placement
  - finalize room transition
  - apply authoritative navigation payloads
  - request authoritative room travel from the backend API
- Updated both `hexmap.js` copies to:
  - import and initialize `HexmapNavigation`
  - clean it up during detach
  - preserve the previous public method names as wrappers that delegate to the adapter
- Removed the previous local room-transition fallback from connection-click travel.

## Behavior impact

1. Connection-click room travel now stays aligned with the authoritative backend navigation contract instead of silently changing rooms client-side.
2. If campaign/character context is missing, the user now gets a system message:
   - `Room travel requires an authenticated campaign character.`
3. The extraction creates a cleaner seam for further Map-tab thin-client work without breaking existing `hexmap.js` call sites.

## Verification

- `node --check` passed for:
  - both `hexmap.js` copies
  - both `HexmapNavigation.js` copies
- Drupal cache rebuild succeeded with:
  - `cd /var/www/html/dungeoncrawler && vendor/bin/drush cr`

## Remaining thread

- Continue decomposing `/hexmap` runtime responsibilities into smaller modules.
- Decide whether any explicit non-campaign/demo room-browsing mode still needs to exist separately from authenticated campaign travel.
