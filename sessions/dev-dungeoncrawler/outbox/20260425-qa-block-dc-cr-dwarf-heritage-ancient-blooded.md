- Status: done
- Summary: Resolved the `dc-cr-dwarf-heritage-ancient-blooded` QA block by wiring the missing runtime and mutation surfaces. `ReactionHandler` now exposes a dedicated magical-saving-throw check for `call-on-ancient-blood`, supports executing that reaction, and persists the temporary save bonus state into the participant `entity_ref` for runtime inspection. `CharacterApiController` now hard-blocks heritage changes after character creation is complete, and a dedicated heritage mutation route was added so QA has an explicit surface to verify heritage updates before wizard completion.

## Changes
- `src/Service/ReactionHandler.php`
  - added `checkForSavingThrowReaction()` for `saving_throw_before_roll_magical`
  - added `call-on-ancient-blood` execution branch in `executeReaction()`
  - persists `active_reaction_bonuses['call-on-ancient-blood']` and `saving_throw_circumstance_bonus`
  - added runtime helpers for reaction ability detection and magical-save trigger validation
- `src/Controller/CharacterApiController.php`
  - generic `saveCharacter()` now returns `409` if a completed character attempts to change heritage
  - added `updateHeritage()` endpoint for explicit heritage mutation before wizard completion
- `dungeoncrawler_content.routing.yml`
  - added route `dungeoncrawler_content.api.character_update_heritage`
  - path: `/api/character/{character_id}/heritage`
- Added focused unit coverage:
  - `tests/src/Unit/Service/ReactionHandlerAncientBloodTest.php`
  - `tests/src/Unit/Service/CharacterManagerStoredRecordTest.php`

## Evidence
- Route registered after cache rebuild:
  - `dungeoncrawler_content.api.character_update_heritage` → `/api/character/{character_id}/heritage`
- Focused unit tests pass under Drupal bootstrap:
  - `CharacterManagerStoredRecordTest`
  - `ReactionHandlerAncientBloodTest`
- Lock message is now present in both mutation paths:
  - save/update path
  - dedicated heritage update path

## QA focus
- Verify `call-on-ancient-blood` is surfaced for a magical saving throw trigger and marks the reaction slot spent when executed.
- Verify a completed character cannot change heritage through either `/api/character/save` or `/api/character/{character_id}/heritage`.
- Verify draft characters can still mutate heritage through the dedicated route when ancestry/heritage pairing is valid.
