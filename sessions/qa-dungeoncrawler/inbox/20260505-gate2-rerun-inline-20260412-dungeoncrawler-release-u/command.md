Perform Gate 2 verification for Dungeoncrawler release `20260412-dungeoncrawler-release-u`.

The prior closeout attempt is stale because QA did not receive a usable in-scope feature list, and the PM follow-up used placeholder IDs instead of the real feature IDs. Ignore the old malformed list.

Authoritative in-scope features for this release:
- `dc-apg-ancestries` — APG Ancestries and Versatile Heritages
- `dc-apg-archetypes` — APG Archetypes System
- `dc-apg-class-expansions` — APG Core Class Expansions
- `dc-apg-class-witch` — Witch Class Mechanics (APG)
- `dc-apg-rituals` — APG New Rituals
- `dc-apg-spells` — APG New Spells
- `dc-cr-action-economy` — Action Economy System
- `dc-cr-alchemical-items` — Alchemical Items
- `dc-cr-ancestry-feat-schedule` — Ancestry Feat Schedule
- `dc-cr-ancestry-system` — Ancestry System
- `dc-cr-ceaseless-shadows` — Halfling Feat — Ceaseless Shadows
- `dc-cr-character-creation` — Character Creation Workflow
- `dc-cr-conditions` — Conditions System
- `dc-cr-darkvision` — Darkvision Sense
- `dc-cr-dice-system` — Polyhedral Dice Engine
- `dc-cr-difficulty-class` — Difficulty Class (DC) System
- `dc-cr-dwarf-heritage-ancient-blooded` — Dwarf Heritage — Ancient-Blooded
- `dc-cr-dwarf-heritage-death-warden` — Dwarf Heritage — Death Warden
- `dc-cr-dwarf-heritage-forge` — Dwarf Heritage — Forge Dwarf
- `dc-cr-dwarf-heritage-rock` — Dwarf Heritage — Rock Dwarf
- `dc-cr-dwarf-heritage-strong-blooded` — Dwarf Heritage — Strong-Blooded Dwarf
- `dc-cr-dwarven-weapon-expertise` — Dwarven Weapon Expertise
- `dc-cr-dwarven-weapon-familiarity` — Dwarven Weapon Familiarity (Ancestry Feat)
- `dc-cr-economy` — Economy, Services, and Currency
- `dc-cr-elf-ancestry` — Elf Ancestry
- `dc-cr-elf-heritage-arctic` — Arctic Elf Heritage
- `dc-cr-elf-heritage-cavern` — Cavern Elf Heritage
- `dc-cr-encounter-rules` — Encounter and Combat Rules
- `dc-cr-equipment-system` — Equipment and Gear System
- `dc-cr-focus-spells` — Focus Spells
- `dc-cr-general-feats` — General Feats
- `dc-cr-gm-tools` — GM Tools and Adventure Preparation
- `dc-cr-half-elf-heritage` — Half-Elf Heritage
- `dc-cr-halfling-resolve` — Halfling Resolve
- `dc-cr-halfling-weapon-expertise` — Halfling Weapon Expertise
- `dc-cr-languages` — Languages System
- `dc-cr-low-light-vision` — Low-Light Vision
- `dc-cr-magic-items` — Magic Items and Treasure
- `dc-cr-mountains-stoutness` — Mountain's Stoutness (Dwarf Ancestry Feat)
- `dc-cr-rituals` — Ritual Magic System
- `dc-cr-rock-runner` — Rock Runner (Dwarf Ancestry Feat)
- `dc-cr-skill-feats` — Skill Feats
- `dc-cr-unburdened-iron` — Unburdened Iron (Dwarf Ancestry Feat)
- `dc-cr-vengeful-hatred` — Vengeful Hatred (Dwarf Ancestry Feat)
- `dc-cr-xp-award-system` — XP Award System
- `dc-cr-xp-rewards` — XP and Rewards System

Required actions:
1. For each feature above, read `features/<feature-id>/feature.md` and confirm `Status: done` and `Release: 20260412-dungeoncrawler-release-u`.
2. Review any existing QA evidence under `sessions/qa-dungeoncrawler/artifacts/` relevant to those features.
3. Determine whether release `20260412-dungeoncrawler-release-u` should receive Gate 2 APPROVE or BLOCK.
4. Write exactly one canonical outbox artifact for `20260412-dungeoncrawler-release-u` with explicit APPROVE or BLOCK verdict and a per-feature evidence table.
5. If BLOCK, cite the exact blocking features and missing evidence.

Do not rely on `feature-ids.txt` injection alone; the authoritative list is already inlined above.
