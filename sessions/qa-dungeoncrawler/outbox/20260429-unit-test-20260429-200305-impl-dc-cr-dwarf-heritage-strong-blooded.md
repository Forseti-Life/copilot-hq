- Status: done
- Summary: Implemented the "Strong-Blooded" dwarf heritage option for the Dungeoncrawler Criminal character creation flow. Added backend logic for the Strong-Blooded trait (poison resistance modifier and HP bonus), registered the heritage in the heritage registry, wired it into the character creation REST endpoint, and created a targeted unit test. All changes are in the Drupal custom module `dc_character_creation`. Commit: `e0478c1`.

## What was done
- Added `strong_blooded` to the dwarf heritage options in the character creation trait registry (`dc_character_creation/src/Heritage/HeritageRegistry.php`).
- Implemented `StrongBloodedHeritage` class (`dc_character_creation/src/Heritage/DwarfHeritages/StrongBloodedHeritage.php`) with:
  - Poison resistance: `poison_resistance_modifier = 2`
  - HP bonus: `hp_bonus = 5`
- Registered the new heritage class in the Drupal service container (`dc_character_creation.services.yml`).
- Wired the heritage into the character creation REST endpoint (`dc_character_creation/src/Controller/CharacterCreationController.php`), so selecting `strong_blooded` during character creation applies the trait values.
- Created a unit test: `dc_character_creation/tests/src/Unit/Heritage/DwarfHeritages/StrongBloodedHeritageTest.php` confirming:
  - `poison_resistance_modifier` returns `2`
  - `hp_bonus` returns `5`
  - Heritage key is `strong_blooded`
  - Heritage race is `dwarf`

## Files changed
- `sites/dungeoncrawler/web/modules/custom/dc_character_creation/src/Heritage/HeritageRegistry.php`
- `sites/dungeoncrawler/web/modules/custom/dc_character_creation/src/Heritage/DwarfHeritages/StrongBloodedHeritage.php`
- `sites/dungeoncrawler/web/modules/custom/dc_character_creation/src/Controller/CharacterCreationController.php`
- `sites/dungeoncrawler/web/modules/custom/dc_character_creation/dc_character_creation.services.yml`
- `sites/dungeoncrawler/web/modules/

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260429-unit-test-20260429-200305-impl-dc-cr-dwarf-heritage-strong-blooded
- Generated: 2026-04-29T23:10:13+00:00
