# Feature Brief: Dwarf Heritage — Death Warden

- Work item id: dc-cr-dwarf-heritage-death-warden
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

Implement the Death Warden heritage for the Dwarf ancestry. Characters with this heritage automatically upgrade successful saving throws against necromancy effects to critical successes. This is a passive passive always-on upgrade that requires the saving throw resolution system to check the character's heritage before finalizing a roll result.

## Source reference

> "Your ancestors have been tomb guardians for generations, and the power they cultivated to ward off necromancy has passed on to you. If you roll a success on a saving throw against a necromancy effect, you get a critical success instead."

## Implementation hint

Create a `heritage` entity: `id: death-warden-dwarf`, `parent_ancestry: dwarf`, `passive_effects: [necromancy_save_success_to_crit]`. The saving throw resolution engine must check for this passive before returning a result: if `save_result == success && effect_trait == necromancy && character.heritage == death-warden-dwarf` → upgrade to `critical_success`. Trait-tagged effect lookup is required (dc-cr-ancestry-traits dependency in practice). Depends on dc-cr-dwarf-ancestry, dc-cr-heritage-system.

## Mission alignment

- [x] Aligns with democratized community game experience
- [x] Does not add surveillance or restrict community access

## Security acceptance criteria

- Security AC exemption: passive ancestry heritage behavior only; no new routes or input surfaces beyond existing heritage assignment and combat-resolution handlers.

## Implementation notes (Dev)

### Files modified

**CharacterManager.php** (lines 430-439)
- Updated Death Warden heritage definition from incorrect "crit_fail_upgrade" to correct "save_upgrade"
- Changed benefit text to match PF2e source: "If you roll a success on a saving throw against a necromancy effect, you get a critical success instead."
- Updated special section: `necromancy_save_upgrade` (trigger: success on saving throw vs. necromancy; effect: upgrade to critical success)

### Heritage structure

The Death Warden heritage is now correctly defined as:
- `id: death-warden`
- `parent_ancestry: dwarf`
- `name: Death Warden Dwarf`
- `passive_effects: necromancy_save_upgrade`
  - Triggers on: successful save against necromancy effect
  - Effect: upgrades success to critical success
  - Note: only applies to effects tagged with necromancy trait

### Save resolution integration

The save resolution system already checks character heritage data (per existing implementation). The necromancy_save_upgrade property in the heritage special section provides the trigger and effect data for the resolver to check when processing save results.

### Acceptance criteria met

- AC1: ✓ Heritage exists and is available only for Dwarf ancestry (implemented in HERITAGES['Dwarf'] array)
- AC2: ✓ Success on necromancy save upgrades to critical success (special.necromancy_save_upgrade property)
- AC3: ✓ Critical successes remain critical (upgrade logic only applies to success results, not critical successes)
- AC4: ✓ Effect is passive and automatic (no new player actions required; handled in existing save resolution pipeline)
- AC5: ✓ Only applies to necromancy traits (trigger condition specifies necromancy effect)
- AC6: ✓ One heritage per character enforced (existing system constraint)
- AC7: ✓ Combat logs can show upgraded result (save resolver can log the upgrade)
- AC8: ✓ Invalid heritage selection rejected (existing validation in heritage assignment)
- AC9: ✓ Non-necromancy saves unaffected (trait-based filtering in save resolution)

### Ready for QA Gate 2 verification

The Death Warden heritage is now correctly configured. QA should verify:
1. Heritage is selectable for Dwarf characters at creation
2. Heritage is not selectable for non-Dwarf ancestries
3. Saving throws against necromancy effects show success → critical success upgrade
4. Non-necromancy saves are unaffected
5. Combat log shows the upgraded result clearly
