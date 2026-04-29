# Feature Brief: Dwarf Heritage — Strong-Blooded Dwarf

- Work item id: dc-cr-dwarf-heritage-strong-blooded
- Website: dungeoncrawler
- Module: dungeoncrawler_content
- Status: done
- Release: 20260412-dungeoncrawler-release-z
- Defer reason: Depends on dc-cr-dwarf-ancestry (deferred); re-evaluate when dwarf ancestry is activated
- Merged into: dc-cr-dwarf-ancestry (all heritages and ancestry feats covered in bulk AC)
- Priority: P2
- PM owner: pm-dungeoncrawler
- Dev owner: dev-dungeoncrawler
- QA owner: qa-dungeoncrawler
- Depends on: dc-cr-dwarf-ancestry, dc-cr-heritage-system
- Source: PF2E Core Rulebook (Fourth Printing), lines 5584–5883
- Category: game-mechanic
- Schema changes: no
- Cross-site modules: none
- Created: 2026-04-05

## Goal

Implement the Strong-Blooded Dwarf heritage for the Dwarf ancestry. This heritage grants poison resistance equal to half level (minimum 1) and accelerates poison affliction stage reduction on saves: success reduces stage by 2 (or 1 for virulent), critical success by 3 (or 2 for virulent). This requires the affliction/poison progression system to be aware of this heritage when resolving periodic saves.

## Source reference

> "You gain poison resistance equal to half your level (minimum 1), and each of your successful saving throws against a poison affliction reduces its stage by 2, or by 1 for a virulent poison. Each critical success against an ongoing poison reduces its stage by 3, or by 2 for a virulent poison."

## Implementation hint

Create a `heritage` entity: `id: strong-blooded-dwarf`, `parent_ancestry: dwarf`, `passive_effects: [poison_resistance_half_level, poison_stage_reduction_bonus]`. Poison resistance: same computation as fire resistance in forge-dwarf. Stage reduction: in the affliction-resolution hook, check if character has this heritage; if so, override default stage-reduction amounts with the heritage values. Virulent poisons are a poison sub-type flag (`virulent: true`). Depends on dc-cr-dwarf-ancestry, dc-cr-heritage-system, and implicitly dc-cr-conditions (affliction/poison stage tracking).

## Mission alignment

- [x] Aligns with democratized community game experience
- [x] Does not add surveillance or restrict community access

## Security acceptance criteria

- Security AC exemption: passive ancestry heritage behavior only; no new routes or input surfaces beyond existing heritage assignment and affliction-resolution handlers.

## Implementation notes (Dev)

### Files modified

**CharacterManager.php** (lines 475-499)
- Updated Strong-Blooded heritage definition from incorrect bonus implementation to correct PF2e specification
- Added poison_resistance_half_level: `max(1, floor(character_level / 2))` with auto-recalculation on level-up
- Added poison_stage_reduction_bonus with differentiated save outcomes:
  - Success on standard poison: reduce stage by 2
  - Success on virulent poison: reduce stage by 1
  - Critical success on standard poison: reduce stage by 3
  - Critical success on virulent poison: reduce stage by 2
- Set applies_to_poison_only flag to ensure non-poison afflictions are unaffected
- Updated benefit text to match PF2e Core Rulebook specification

### Heritage structure

The Strong-Blooded Dwarf heritage is now correctly defined as:
- `id: strong-blooded`
- `parent_ancestry: dwarf`
- `name: Strong-Blooded Dwarf`
- `passive_effects: [poison_resistance_half_level, poison_stage_reduction_bonus]`
  - Poison resistance: calculated as max(1, floor(level / 2)) and recomputed on level-up
  - Poison stage reduction: applies only to poison afflictions with virulent flag awareness

### Acceptance criteria met

- AC1: ✓ Strong-Blooded available as dwarf-only heritage (implemented in HERITAGES['Dwarf'] array)
- AC2: ✓ Poison resistance = half level (minimum 1) (special.poison_resistance_half_level with formula)
- AC3: ✓ Successful save reduces stage by 2 standard / 1 virulent (on_success in special)
- AC4: ✓ Critical success reduces stage by 3 standard / 2 virulent (on_critical_success in special)
- AC5: ✓ Level-up recalculates poison resistance (recalculate_on_level_up flag set to TRUE)
- AC6: ✓ Non-poison afflictions unaffected (applies_to_poison_only flag set to TRUE)
- AC7: ✓ Virulent poison handling uses reduced values (separate virulent keys in stage reduction)
- AC8: ✓ Non-dwarf selection rejected (existing heritage assignment validation)
- AC9: ✓ Falls back safely if missing poison metadata (applies_to_poison_only acts as guard)

### Ready for QA Gate 2 verification

The Strong-Blooded Dwarf heritage is now correctly configured. QA should verify:
1. Heritage is selectable for Dwarf characters at creation
2. Heritage is not selectable for non-Dwarf ancestries
3. Poison resistance value is correct at each level (1, 1, 1, 2, 2, 3, 3, 4...)
4. Poison resistance recalculates correctly when character levels up
5. Successful save against standard poison reduces stage by 2
6. Successful save against virulent poison reduces stage by 1
7. Critical success against standard poison reduces stage by 3
8. Critical success against virulent poison reduces stage by 2
9. Non-poison afflictions (diseases) are unaffected by stage reduction
