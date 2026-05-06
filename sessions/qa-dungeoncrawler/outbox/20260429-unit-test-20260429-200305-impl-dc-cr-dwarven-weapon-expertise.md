- Status: done

## What was implemented

### Summary
Added the **Dwarven Weapon Expertise** racial trait to the DungeonCrawler character creation system. This trait grants Dwarves a passive +1 bonus to attack rolls when wielding axes or hammers.

### Files changed
- `sites/dungeoncrawler/web/modules/custom/dc_character/src/Plugin/Trait/DwarvenWeaponExpertise.php` — new trait plugin
- `sites/dungeoncrawler/web/modules/custom/dc_character/src/Form/CharacterCreationForm.php` — added trait auto-assignment on Dwarf race selection
- `sites/dungeoncrawler/web/modules/custom/dc_character/tests/src/Unit/DwarvenWeaponExpertiseTest.php` — unit tests (3 cases)
- `sites/dungeoncrawler/web/modules/custom/dc_character/dc_character.info.yml` — version bump

## Unit tests
- `DwarvenWeaponExpertiseTest::testTraitIsRegistered` — verifies trait plugin is recognized by the trait registry
- `DwarvenWeaponExpertiseTest::testAttackBonusAxeHammer` — verifies +1 attack bonus applies for axe/hammer weapon types
- `DwarvenWeaponExpertiseTest::testNoBonusOtherWeapons` — verifies no bonus applies for non-axe/hammer weapon types

## Acceptance criteria coverage
- [x] Dwarven Weapon Expertise trait exists as a registerable plugin
- [x] Trait auto-assigns when Dwarf race is selected during character creation
- [x] +1 attack bonus applies for axes and hammers
- [x] No bonus applies for other weapon types
- [x] Unit tests passing

## Verification notes
- All three unit tests pass locally via `vendor/bin/phpunit`.
- Trait plugin auto-assignment confirmed via manual walkthrough of CharacterCreationForm.
- No UI changes; backend logic only.

## Commit
- Hash: `a3f9c12`
- Message: "feat(dc-cr): add Dwarven Weapon Expertise racial trait"
</tool_response>

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260429-unit-test-20260429-200305-impl-dc-cr-dwarven-weapon-expertise
- Generated: 2026-04-29T23:11:16+00:00
