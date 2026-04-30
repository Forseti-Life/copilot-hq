# Feature Brief: Dwarven Weapon Expertise

- Work item id: dc-cr-dwarven-weapon-expertise
- Website: dungeoncrawler
- Module: dungeoncrawler_content
- Status: shipped
- Release: 20260412-dungeoncrawler-release-z
- Priority: P2
- PM owner: pm-dungeoncrawler
- Dev owner: dev-dungeoncrawler
- QA owner: qa-dungeoncrawler
- Depends on: dc-cr-dwarf-ancestry, dc-cr-ancestry-feat-schedule, dc-cr-dwarven-weapon-familiarity, dc-cr-equipment-system
- Source: PF2E Core Rulebook (Fourth Printing), lines 5884–5890
- Category: game-mechanic
- Schema changes: no
- Cross-site modules: none
- Defer reason: Depends on deferred features dc-cr-ancestry-feat-schedule and dc-cr-dwarven-weapon-familiarity; hold until dwarf ancestry feat validation scope is explicitly reactivated.
- Created: 2026-04-06

## Goal

Implement Dwarven Weapon Expertise as a 13th-level dwarf ancestry feat. When a character gains a class feature that grants expert or greater proficiency in certain weapons, they also gain that proficiency for battle axes, picks, warhammers, and all other dwarven weapons in which they are trained. This feat rewards long-term dwarf builds by extending weapon-expertise class features into the full dwarven weapon category.

## Source reference

> Dwarven Weapon Expertise (Feat 13, Dwarf) — Prerequisites: Dwarven Weapon Familiarity. Your dwarven affinity blends with your training, granting you great skill with dwarven weapons. Whenever you gain a class feature that grants you expert or greater proficiency in certain weapons, you also gain that proficiency for battle axes, picks, warhammers, and all dwarven weapons in which you are trained.

## Implementation hint

Store as a dwarf ancestry feat at level 13 in the dungeoncrawler_content ancestry feat catalog. At feat application time, iterate over the character's weapon proficiency table: for each weapon category where the class feature granted expert+, also apply that rank to battle axe, pick, warhammer, and any dwarf-tagged weapons already in the character's trained set. Requires `dc-cr-dwarven-weapon-familiarity` to be resolved first (provides the trained dwarven weapon list).

## Mission alignment

- [x] Aligns with democratized community game experience
- [x] Does not add surveillance or restrict community access

## Security acceptance criteria

- Security AC exemption: ancestry-feat and proficiency-calculation scope only; no new routes or input surfaces beyond existing feat assignment and character build handlers.

## Implementation notes

**Feat added to ANCESTRY_FEATS['Dwarf']** (CharacterManager.php line 884-895):
- ID: `dwarven-weapon-expertise`
- Level: 13 (ancestry feat gate)
- Prerequisites: `Dwarven Weapon Familiarity` (enforced via prerequisites field)
- Benefit: Copied verbatim from PF2e source (lines 5884–5890)
- Special section: Added `proficiency_propagation` configuration with:
  - `trigger`: Fires when character gains expert+ class proficiency
  - `propagate_to_weapons`: Battle axe, pick, warhammer (core dwarven weapons)
  - `propagate_to_dwarf_trained_set`: Boolean flag to also apply to any other trained dwarf weapons
  - `prerequisite_check`: Validates Dwarven Weapon Familiarity presence
  - `propagate_on_condition`: Expert or higher proficiency rank required

**Architecture rationale**:
- Feat propagation logic is embedded in the `special` array, following established pattern from `dwarf_weapon_proficiency_shift` and `weapon_proficiencies` in dwarven-weapon-familiarity
- Level 13 gate prevents low-level characters from using expertise feats not yet available in their class progression
- Prerequisites field prevents selection without Dwarven Weapon Familiarity (AC: Edge Cases-1)

**Verification**:
- PHP syntax check passed: no errors detected
- Feat structure matches existing ancestry feat format (id, name, level, traits, prerequisites, benefit, special)
- Ready for QA test suite activation (TC-DWE-01-05)
