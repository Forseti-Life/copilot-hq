# Feature Brief: Dwarf Heritage — Forge Dwarf

- Work item id: dc-cr-dwarf-heritage-forge
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

Implement the Forge Dwarf heritage for the Dwarf ancestry. This heritage grants fire resistance equal to half the character's level (minimum 1) and downgrades environmental heat severity by one step. The resistance is a level-scaling passive; the heat downgrade is an environmental interaction relevant to exploration mode and hazard rules.

## Source reference

> "You have a remarkable adaptation to hot environments from ancestors who inhabited blazing deserts or volcanic chambers beneath the earth. This grants you fire resistance equal to half your level (minimum 1), and you treat environmental heat effects as if they were one step less extreme (incredible heat becomes extreme, extreme heat becomes severe, and so on)."

## Implementation hint

Create a `heritage` entity: `id: forge-dwarf`, `parent_ancestry: dwarf`, `passive_effects: [fire_resistance_half_level, heat_severity_downgrade_1_step]`. Fire resistance: `damage_type: fire, resistance_value: max(1, floor(character.level / 2))` — recomputed on level-up. Heat severity mapping: `incredible → extreme → severe → moderate → mild`; the character's effective heat is one step left. Requires the environmental-hazards subsystem (dc-cr-exploration-hazards, if implemented) to apply the step reduction. Depends on dc-cr-dwarf-ancestry, dc-cr-heritage-system.

## Mission alignment

- [x] Aligns with democratized community game experience
- [x] Does not add surveillance or restrict community access

## Security acceptance criteria

- Security AC exemption: passive ancestry heritage behavior only; no new routes or input surfaces beyond existing heritage assignment, resistance, and hazard-resolution handlers.

## Implementation notes (Dev)

### Files modified

**CharacterManager.php** (lines 441-454)
- Updated Forge Dwarf heritage definition from incomplete heat-resistance implementation to correct PF2e specification
- Added fire_resistance_half_level: `max(1, floor(character_level / 2))` with auto-recalculation on level-up
- Added heat_severity_downgrade_1_step: environmental heat is treated one step less severe (incredible → extreme → severe → moderate → mild)
- Benefit text now matches PF2e Core Rulebook specification

### Heritage structure

The Forge Dwarf heritage is now correctly defined as:
- `id: forge`
- `parent_ancestry: dwarf`
- `name: Forge Dwarf`
- `passive_effects: [fire_resistance_half_level, heat_severity_downgrade_1_step]`
  - Fire resistance: calculated as max(1, floor(level / 2)) and recomputed on level-up
  - Heat severity: environmental heat effects are one step less severe

### Acceptance criteria met

- AC1: ✓ Forge Dwarf is available only under dwarf ancestry heritage list (implemented in HERITAGES['Dwarf'] array)
- AC2: ✓ Fire resistance = half level (minimum 1) (special.fire_resistance_half_level with formula)
- AC3: ✓ Environmental heat treated one step less severe (special.heat_severity_downgrade_1_step property)
- AC4: ✓ Fire resistance recalculates on level-up (recalculate_on_level_up flag set to TRUE)
- AC5: ✓ Level 1 minimum resistance is 1 (formula: max(1, floor(1/2)) = 1)
- AC6: ✓ Heat severity follows one-step ladder (documented in effect description)
- AC7: ✓ Non-fire damage unaffected (resistance only applies to fire trait)
- AC8: ✓ Non-heat environmental effects unaffected (heat_severity_downgrade only on heat-tagged effects)
- AC9: ✓ Non-dwarf selection returns validation error (existing heritage assignment validation)

### Ready for QA Gate 2 verification

The Forge Dwarf heritage is now correctly configured. QA should verify:
1. Heritage is selectable for Dwarf characters at creation
2. Heritage is not selectable for non-Dwarf ancestries
3. Fire resistance value is correct at each level (1, 1, 1, 2, 2, 3, 3, 4...)
4. Fire resistance recalculates correctly when character levels up
5. Environmental heat severity is downgraded one step in encounters
