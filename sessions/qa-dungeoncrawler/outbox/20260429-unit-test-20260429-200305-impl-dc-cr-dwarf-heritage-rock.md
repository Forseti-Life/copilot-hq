- Status: done

## Summary
Implemented the Dwarf Heritage: Rock passive feature for the Dungeoncrawler Criminal campaign. This feature provides Dwarf characters with a passive ability that activates when in rocky/underground terrain.

## What was changed

### New files created
- `sites/dungeoncrawler/web/modules/custom/dc_character/src/Heritage/DwarfHeritageRock.php` — Heritage class implementing the Rock passive
- `sites/dungeoncrawler/web/modules/custom/dc_character/tests/src/Unit/Heritage/DwarfHeritageRockTest.php` — PHPUnit unit tests for the heritage class

### Modified files
- `sites/dungeoncrawler/web/modules/custom/dc_character/dc_character.module` — registered the new heritage in hook_dc_heritages()
- `sites/dungeoncrawler/web/modules/custom/dc_character/src/Heritage/HeritageRegistry.php` — added DwarfHeritageRock to the registry map

## Implementation details

### DwarfHeritageRock passive behavior
- **Trigger condition**: character is a Dwarf AND current terrain type is `rocky` or `underground`
- **Effect**: grants +2 AC bonus (passive, stacks with armor)
- **Method**: `isActive(CharacterInterface $character, TerrainInterface $terrain): bool`
- **Method**: `getACBonus(CharacterInterface $character): int` — returns 2 when active, 0 otherwise
- **Constant**: `HERITAGE_ID = 'dwarf_heritage_rock'`

### Registration
- Heritage ID: `dwarf_heritage_rock`
- Registered in `HeritageRegistry::$heritages` map
- Discovered via `hook_dc_heritages()` in `dc_character.module`

## Tests written
- `testIsActiveReturnsTrueForDwarfInRockyTerrain()` — happy path: Dwarf + rocky = active
- `testIsActiveReturnsTrueForDwarfInUndergroundTerrain()` — happy path: Dwarf + underground = active
- `testIsActiveReturnsFalseForNonDwarfInRockyTerrain()` — negative: non-Dwarf + rocky = inactive
- `

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260429-unit-test-20260429-200305-impl-dc-cr-dwarf-heritage-rock
- Generated: 2026-04-29T23:09:33+00:00
