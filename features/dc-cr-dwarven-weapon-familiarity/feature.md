# Feature Brief: Dwarven Weapon Familiarity (Ancestry Feat)

- Work item id: dc-cr-dwarven-weapon-familiarity
- Website: dungeoncrawler
- Module: dungeoncrawler_content
- Status: shipped
- Release: 20260412-dungeoncrawler-release-z
- Defer reason: Depends on dc-cr-dwarf-ancestry (deferred); re-evaluate when dwarf ancestry is activated
- Merged into: dc-cr-dwarf-ancestry (all heritages and ancestry feats covered in bulk AC)
- Priority: P2
- PM owner: pm-dungeoncrawler
- Dev owner: dev-dungeoncrawler
- QA owner: qa-dungeoncrawler
- Depends on: dc-cr-dwarf-ancestry, dc-cr-ancestry-feat-schedule, dc-cr-equipment-system
- Source: PF2E Core Rulebook (Fourth Printing), lines 5584–5883
- Category: game-mechanic
- Schema changes: no
- Cross-site modules: none
- Created: 2026-04-05

## Goal

Implement the Dwarven Weapon Familiarity level-1 ancestry feat for Dwarf characters. When selected, this feat grants trained proficiency with the battle axe, pick, and warhammer, and access to all uncommon dwarf weapons. It also reclassifies martial dwarf weapons as simple and advanced dwarf weapons as martial for the purposes of this character's proficiency calculation. This establishes the pattern for ancestry-specific weapon proficiency overrides.

## Source reference

> "Your kin have instilled in you an affinity for hard-hitting weapons, and you prefer these to more elegant arms. You are trained with the battle axe, pick, and warhammer. You also gain access to all uncommon dwarf weapons. For the purpose of determining your proficiency, martial dwarf weapons are simple weapons and advanced dwarf weapons are martial weapons."

## Implementation hint

Create an `ancestry_feat` entity: `id: dwarven-weapon-familiarity`, `level: 1`, `ancestry: dwarf`. Effects: (1) set proficiency to `trained` for `battle-axe`, `pick`, `warhammer` if not already higher; (2) grant access to uncommon dwarf-trait weapons; (3) add a proficiency reclassification rule: when computing proficiency for a dwarf-trait weapon, downgrade its category by one tier for this character. The equipment system must support the `dwarf` weapon trait and `uncommon` access gating (dc-cr-equipment-system dependency). Depends on dc-cr-dwarf-ancestry, dc-cr-ancestry-feat-schedule, dc-cr-equipment-system.

## Mission alignment

- [x] Aligns with democratized community game experience
- [x] Does not add surveillance or restrict community access

## Security acceptance criteria

- Security AC exemption: ancestry-feat and proficiency-calculation scope only; no new routes or input surfaces beyond existing feat assignment and character build handlers.

## Implementation notes

**Feat already implemented in ANCESTRY_FEATS['Dwarf']** (CharacterManager.php lines 846-852):
- ID: `dwarven-weapon-familiarity`
- Level: 1 (ancestry feat gate, available at character creation)
- Ancestry: Dwarf (traits: ['Dwarf'])
- Prerequisites: None (available to all dwarves)
- Benefit: Copied verbatim from PF2e Core Rulebook (lines 5584–5883)

**Special section structure**:
- `weapon_proficiencies`: Grants trained proficiency for battleaxe, pick, and warhammer
  - Format: `['battleaxe' => 'trained', 'pick' => 'trained', 'warhammer' => 'trained']`
  - These are applied when the feat is selected during character creation or feat retrain
- `dwarf_weapon_proficiency_shift`: Reclassifies weapon categories for proficiency calculations
  - Format: `['martial' => 'simple', 'advanced' => 'martial']`
  - Applies to all dwarf-trait weapons: martial dwarf weapons count as simple; advanced count as martial
- `dwarf_weapon_feats_unlocked`: Boolean flag (TRUE) that signals uncommon dwarf weapon access is unlocked
  - Used by equipment system to gate access to all dwarf-tagged uncommon weapons

**Acceptance criteria verification**:
- AC Happy Path-1: ✓ Feat exists at level 1 and requires open ancestry-feat slot (feat gating inherited from system)
- AC Happy Path-2: ✓ Battle axe, pick, warhammer proficiencies set to trained
- AC Happy Path-3: ✓ Flag signals equipment system to grant uncommon dwarf weapon access
- AC Happy Path-4: ✓ Proficiency shift configured for martial↔simple and advanced↔martial remapping
- AC Edge Cases-1,2,3: ✓ Non-dwarf/no-slot gating and proficiency recalculation on rebuild (system-level)
- AC Failure Modes-1,2: ✓ Validation infrastructure in place

**Ready for QA**: All acceptance criteria covered. Test suite coverage in `03-test-plan.md`.
