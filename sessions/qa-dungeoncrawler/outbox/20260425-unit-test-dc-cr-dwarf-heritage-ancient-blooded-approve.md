- Status: done
- Summary: APPROVE for `dc-cr-dwarf-heritage-ancient-blooded`. The previously blocked runtime path is now present: `ReactionHandler` can surface and execute `call-on-ancient-blood` for magical saving-throw triggers, the dedicated heritage mutation route exists, and completed characters are now blocked from changing heritage through both mutation paths. Focused Drupal-bootstrapped unit tests pass for the new stored-record lock helpers and the Ancient-Blooded reaction flow.

## Evidence
- `src/Service/ReactionHandler.php`
  - `checkForSavingThrowReaction()` present
  - `executeReaction()` now supports `call-on-ancient-blood`
  - execution persists `active_reaction_bonuses['call-on-ancient-blood']` and `saving_throw_circumstance_bonus`
- `src/Controller/CharacterApiController.php`
  - `saveCharacter()` rejects heritage changes after completion with `409`
  - `updateHeritage()` provides explicit `/api/character/{character_id}/heritage` mutation surface
- Route registration after cache rebuild:
  - `dungeoncrawler_content.api.character_update_heritage` → `/api/character/{character_id}/heritage`
- Focused tests:
  - `CharacterManagerStoredRecordTest` PASS
  - `ReactionHandlerAncientBloodTest` PASS

## Test note
- Full browser-style mutation flow remains subject to the pre-existing functional harness constraints in this environment, so this verification used source review plus focused Drupal-bootstrapped unit coverage for the changed runtime surface.

## Verdict
- APPROVE
