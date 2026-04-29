# Feature Brief: Ancestry Feat Schedule

- Work item id: dc-cr-ancestry-feat-schedule
- Website: dungeoncrawler
- Module: dungeoncrawler_content
- Status: done
- Release: 20260412-dungeoncrawler-release-z
- Defer reason: Depends on dc-cr-character-leveling (deferred); re-evaluate when character leveling is activated.
- Priority: P3 (depends on dc-cr-character-leveling which is deferred; ancestry feat slots blocked until leveling system exists)
- PM owner: pm-dungeoncrawler
- Dev owner: dev-dungeoncrawler
- QA owner: qa-dungeoncrawler
- Source: PF2E Core Rulebook (Fourth Printing), lines 5284–5583
- Category: game-mechanic
- Created: 2026-02-28

## Goal

Implement the ancestry feat progression schedule: characters gain their first ancestry feat at level 1, then additional ancestry feats at levels 5, 9, 13, and 17. At each selection point, the player may choose any ancestry feat of their character's level or lower (provided prerequisites are met). This is a required leveling milestone for all characters and must integrate with the character leveling system.

## Source reference

> "You gain your first ancestry feat at 1st level, and you gain another at 5th level, 9th level, 13th level, and 17th level, as indicated in the class advancement table in the descriptions of each class. Ancestry feats are organized by level. As a starting character, you can choose from only 1st-level ancestry feats, but later choices can be made from any feat of your level or lower. These feats also sometimes list prerequisites—requirements that your character must fulfill to select that feat."

## Implementation hint

Add `ancestry_feat` slots to the character entity at levels 1, 5, 9, 13, 17. The feat selection UI must filter to `feat_type: ancestry` and `parent_ancestry: <character.ancestry>`, and filter by `level <= character.level` with prerequisite checking. This integrates with dc-cr-character-leveling (level-up workflow) and the `feat` content type from dc-cr-general-feats. Per-ancestry feat catalogs (e.g., all dwarven ancestry feats) will be added as individual feats are scanned from the rulebook.

## Mission alignment

- [x] Aligns with democratized community game experience
- [x] Does not add surveillance or restrict community access

## Security acceptance criteria

- All ancestry-feat selection writes require authenticated character-owner or GM access.
- POST/PATCH ancestry-feat mutation routes require `_csrf_request_header_mode: TRUE`.
- Server-side validation enforces ancestry, level, and prerequisite checks before persisting a feat choice.
- Handoff/test evidence must confirm no cross-character feat-slot mutation is possible.

## Implementation notes (Dev)

### Files modified

**CharacterManager.php** (lines 9513-9517)
- Added ancestry_feat slot at level 1: `1 => ['feat_slots' => [$class_feat, $ancestry_feat], ...]`
- Ancestry feat slots now present at levels 1, 5, 9, 13, 17 as per PF2e rules

**CharacterLevelingService.php** (multiple locations)
- Added getAncestryFeatsForCharacter() helper method (lines 552-560)
  - Retrieves character's ancestry from basicInfo
  - Returns flat array of feats for that ancestry from CharacterManager::ANCESTRY_FEATS
  
- Updated getEligibleFeats() method (line 510)
  - Now calls getAncestryFeatsForCharacter() instead of returning entire nested array
  - Ensures only ancestry-appropriate feats are available for selection
  
- Updated validateFeat() method (line 703)
  - Now calls getAncestryFeatsForCharacter() to build catalog
  - Validates against ancestry-specific feats only

### Acceptance criteria met

- AC1: ✓ Characters receive ancestry feat selection at levels 1, 5, 9, 13, 17
- AC2: ✓ Picker filters to character's ancestry (automatic via getAncestryFeatsForCharacter)
- AC3: ✓ Previously selected feats remain (CharacterLevelingService handles persistence)
- AC4: ✓ Level-up output indicates pending ancestry feat (feat_choice slot_type = 'ancestry_feat')
- AC5: ✓ Multiple milestones can be filled in one rebuild (pendingChoices array supports multiple slots)
- AC6: ✓ Empty option handling (getEligibleFeats returns empty array if no valid feats)
- AC7: ✓ Retraining/rebuild recalculates (validation happens at submission time)
- AC8: ✓ Validation errors returned for invalid selections (validateFeat throws exceptions)
- AC9: ✓ Access control maintained (existing character-owner/GM checks in place)

### Security implementation

- Ancestry feat selection uses existing CharacterLevelingService::submitFeatChoice() method
- That method requires authenticated character-owner or GM access (per role checks)
- Server-side validation enforces ancestry, level, and prerequisite checks via validateFeat()
- CSRF protection via existing framework integration

### Ready for QA Gate 2 verification
