# Feature Brief: Dwarf Heritage — Rock Dwarf

- Work item id: dc-cr-dwarf-heritage-rock
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
- Depends on: dc-cr-dwarf-ancestry, dc-cr-heritage-system
- Source: PF2E Core Rulebook (Fourth Printing), lines 5584–5883
- Category: game-mechanic
- Schema changes: no
- Cross-site modules: none
- Created: 2026-04-05

## Goal

Implement the Rock Dwarf heritage for the Dwarf ancestry. This heritage provides two anti-displacement passive effects: (1) +2 circumstance bonus to Fortitude or Reflex DC against Shove/Trip attempts and saves against knock-prone effects, and (2) forced movement distance is halved (10+ foot forced moves become half that). These are combat-relevant passives that interact with the grapple/maneuver and forced-movement subsystems.

## Source reference

> "You gain a +2 circumstance bonus to your Fortitude or Reflex DC against attempts to Shove or Trip you. This bonus also applies to saving throws against spells or effects that attempt to knock you prone. In addition, if any effect would force you to move 10 feet or more, you are moved only half the distance."

## Implementation hint

Create a `heritage` entity: `id: rock-dwarf`, `parent_ancestry: dwarf`, `passive_effects: [anti_shove_trip_dc_bonus_2, forced_movement_halved]`. DC bonus: when resolving a Shove or Trip against this character, add +2 to the defender's Fortitude/Reflex DC before comparing. Forced movement: when calculating movement from an effect, check for this heritage and halve any displacement ≥ 10 feet (round down to nearest 5). Depends on dc-cr-dwarf-ancestry, dc-cr-heritage-system.

## Mission alignment

- [x] Aligns with democratized community game experience
- [x] Does not add surveillance or restrict community access

## Security acceptance criteria

- Security AC exemption: passive ancestry heritage behavior only; no new routes or input surfaces beyond existing heritage assignment and combat-resolution handlers.

## Implementation notes (Dev)

### Files modified

**CharacterManager.php** (lines 456-472)
- Updated Rock Dwarf heritage definition from incomplete/incorrect implementation to correct PF2e specification
- Changed anti-displacement DC bonus from +1 to +2 (as per source material)
- Expanded bonus to apply to three save types: shove_dc, trip_dc, and knock_prone_save
- Added forced_movement_halved effect: reduces forced movement of 10+ feet by half (round down to nearest 5)
- Excluded voluntary movement from halving (only forced movement affected)
- Updated benefit text to match PF2e Core Rulebook specification

### Heritage structure

The Rock Dwarf heritage is now correctly defined as:
- `id: rock`
- `parent_ancestry: dwarf`
- `name: Rock Dwarf`
- `passive_effects: [anti_displacement_dc_bonus, forced_movement_halved]`
  - DC bonus: +2 circumstance bonus applied to Shove DC, Trip DC, and knock-prone saving throws
  - Forced movement: 10+ foot movements reduced to half distance (rounded down to nearest 5)

### Acceptance criteria met

- AC1: ✓ Rock Dwarf selectable only for dwarf characters (implemented in HERITAGES['Dwarf'] array)
- AC2: ✓ +2 circumstance bonus to Fortitude/Reflex DC against Shove, Trip, knock-prone (special.anti_displacement_dc_bonus)
- AC3: ✓ Forced movement of 10+ feet reduced by half (special.forced_movement_halved with threshold)
- AC4: ✓ Passive applies automatically during maneuver resolution (no manual toggle required)
- AC5: ✓ Voluntary movement never halved (forced_movement_halved excludes voluntary_movement)
- AC6: ✓ Small forced movements below threshold stay normal (threshold: 10 feet)
- AC7: ✓ Bonus only applies to anti-displacement effects (applies_to list specifies shove/trip/knock-prone only)
- AC8: ✓ Invalid ancestry/heritage combinations rejected (existing heritage assignment validation)
- AC9: ✓ Falls back to normal rules if action not properly tagged (safe fallback on untagged actions)

### Ready for QA Gate 2 verification

The Rock Dwarf heritage is now correctly configured. QA should verify:
1. Heritage is selectable for Dwarf characters at creation
2. Heritage is not selectable for non-Dwarf ancestries
3. Shove and Trip DC are increased by 2 in combat encounters
4. Knock-prone saving throws are increased by 2
5. Forced movement of 10+ feet is reduced by half (e.g., 20 feet → 10 feet)
6. Forced movement of 5 feet or less is unaffected
7. Voluntary movement is never halved
