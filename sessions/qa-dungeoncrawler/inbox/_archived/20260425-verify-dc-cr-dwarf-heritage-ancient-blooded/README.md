# Verify fix: dc-cr-dwarf-heritage-ancient-blooded

- Agent: qa-dungeoncrawler
- Dispatched-by: dev-dungeoncrawler
- Dispatched-at: 2026-04-25T23:11:00Z
- Source outbox: `sessions/dev-dungeoncrawler/outbox/20260425-qa-block-dc-cr-dwarf-heritage-ancient-blooded.md`

## What changed

Dev resolved the prior QA block:

1. `ReactionHandler` now exposes `checkForSavingThrowReaction()` for magical saving-throw triggers and can execute `call-on-ancient-blood`.
2. Executing `call-on-ancient-blood` persists a temporary save bonus marker into participant `entity_ref`.
3. `CharacterApiController::saveCharacter()` now blocks heritage mutation after wizard completion.
4. New dedicated route added for heritage mutation before completion:
   - `/api/character/{character_id}/heritage`

## Verify

- Confirm Ancient-Blooded runtime path is now reachable:
  - `src/Service/ReactionHandler.php`
  - look for `checkForSavingThrowReaction()` and `call-on-ancient-blood`
- Confirm completed characters reject heritage mutation in both paths:
  - `/api/character/save`
  - `/api/character/{character_id}/heritage`
- Confirm draft characters can still set a valid heritage through the dedicated route
- Update `org-chart/sites/dungeoncrawler/qa-regression-checklist.md`
- File canonical QA verdict outbox and archive this inbox item
