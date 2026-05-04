- Status: done
- Summary: Evaluated the live Dungeoncrawler `/hexmap` UI against the existing thin-client and map-first shell goals. The current experience has a solid board/chat/sidebar foundation, but it is still not a clean thin client: the public shell exposes debug/state-heavy panels, `hexmap-demo.html.twig` still owns a large inline behavior/data layer, and `hexmap.js` still mixes presentation with ECS/gameplay authority and local navigation mutation.

## What changed

- Reviewed the live route at `https://dungeoncrawler.forseti.life/hexmap`
- Compared the current experience against:
  - `features/dc-ui-hexmap-thin-client/feature.md`
  - `features/dc-ui-hexmap-thin-client/02-implementation-notes.md`
- Inspected the active implementation seams in:
  - `sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/templates/hexmap-demo.html.twig`
  - `sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/js/hexmap.js`
  - `sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/js/HexmapStateSync.js`
  - `sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/js/game-coordinator/GameCoordinator.js`
  - `sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/src/Controller/HexMapController.php`

## Findings

1. **Foundation is good.**
   - The board, chat, sidebar tabs, inspector panel, selected-hex contents, and drawer/fullscreen controls provide the right basic player shell.
   - The existing shell is already close enough that this should be finished through extraction and authority cleanup, not a rewrite.

2. **The public shell is still too debug-heavy.**
   - The live `/hexmap` page still surfaces panels like `Object Definitions`, `Dungeon State JSON`, `Map Info`, `Legend`, and `Instructions`.
   - That makes the route feel like a diagnostics surface instead of a player-facing tabletop view.

3. **Twig still owns too much client behavior.**
   - `hexmap-demo.html.twig` contains large inline scripting for layout ratio persistence, debug toggle state, sidebar tabs, inventory syncing, drawer behavior, chat docking/maximize, inspector auto-switching, and a large inline item database.
   - That keeps shell behavior, domain data, and template rendering tightly coupled.

4. **Thin-client authority is still incomplete.**
   - `HexmapStateSync.js` is a good seam, but `hexmap.js` still imports ECS movement/combat/turn systems and still handles selection, attack checks, movement execution, and navigation-side reconciliation in the same runtime.
   - `handleNavigationResult()` still mutates `dungeonData`, injects rooms/entities/connections locally, moves entities locally, and performs direct persistence fetches.
   - That is better than full client authority, but it is not yet the desired render + intent + reconcile boundary.

5. **State ownership is still split across multiple UI models.**
   - `showLaunchCharacter()` and `showEntityInfo()` represent different ownership paths for player-vs-entity state.
   - The shell still carries both rich player-sheet hydration and ad hoc entity inspection in one large runtime instead of through a clearer view-model boundary.

## Recommendation

1. Finish the shell cleanup first:
   - keep player mode map-first by default
   - leave debug/state diagnostics admin-gated only
   - move inline shell logic and item data out of Twig

2. Then finish the authority cleanup:
   - stop local room/entity/navigation mutation in `hexmap.js`
   - make backend scene/participant deltas the only source of navigation truth
   - keep client logic limited to rendering, selection, camera, chat, and transient animation

## Next actions

1. Extract the inline shell script and item database from `hexmap-demo.html.twig` into dedicated JS modules.
2. Remove public exposure of nonessential state/debug panels from the default player view.
3. Refactor navigation/movement reconciliation so server state is the only room-transition authority.

## Verification

- Verified live route output from `https://dungeoncrawler.forseti.life/hexmap`
- Verified current feature intent from the `dc-ui-hexmap-thin-client` docs
- Verified implementation findings directly in the active Twig, JS, CSS, and controller files
