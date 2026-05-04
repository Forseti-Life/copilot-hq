- Status: done
- Summary: Reworked the Dungeoncrawler `/hexmap` shell into a simplified three-tab game UI with separate tabs for the map, chat, and character sheet. Removed the old sidebar/debug/info-heavy layout from the active shell, kept the existing runtime hooks intact, refreshed production Drupal caches, and verified the live route now serves the new tabbed markup.

## What changed

- Replaced the old multi-surface shell in:
  - `sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/templates/hexmap-demo.html.twig`
  - `dungeoncrawler-pf2e/web/modules/custom/dungeoncrawler_content/templates/hexmap-demo.html.twig`
- Added matching tabbed-shell CSS in:
  - `sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/css/hexmap.css`
  - `dungeoncrawler-pf2e/web/modules/custom/dungeoncrawler_content/css/hexmap.css`

## UI result

1. **Map tab**
   - Keeps the Pixi canvas and existing gameplay overlays.
   - Remains the default active tab.

2. **Chat tab**
   - Moves the chat interface onto its own top-level tab.
   - Keeps existing room/session/channel DOM hooks so current chat JS continues to work.

3. **Character tab**
   - Moves the character sheet onto its own top-level tab.
   - Keeps the existing character hydration and collapsible section structure.

## Design choices

1. Kept the chat and character panels in the DOM but hidden behind tabs instead of deleting their internals.
2. Removed the old sidebar/debug/info shell from the active rendered layout rather than trying to restyle it in place.
3. Reused existing runtime IDs and behaviors so the change stayed shell-scoped.

## Verification

- Confirmed the production Drupal root `/var/www/html/dungeoncrawler` contains the updated template and CSS.
- Ran `/var/www/html/dungeoncrawler/vendor/bin/drush cr` successfully.
- Verified the live route now serves:
  - `game-tab-map`
  - `game-tab-chat`
  - `game-tab-character`
  - hidden chat/character tab panels in the HTML

## Test status

- The targeted functional PHPUnit suite starts but fails for environment setup, not from this UI change.
- Current failure mode:
  - BrowserTestBase is trying to connect to `http://localhost:8080`
  - that local test server is not running in this environment
