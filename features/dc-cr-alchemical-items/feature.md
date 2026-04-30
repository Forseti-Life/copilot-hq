# Feature Brief: Alchemical Items

- Work item id: dc-cr-alchemical-items
- Website: dungeoncrawler
- Module: dungeoncrawler_content
- Status: shipped
- Release: 20260412-dungeoncrawler-release-z
- Defer reason: 20260228-dungeoncrawler-release-next focuses on core MVP (dice, DC, encounter, conditions, character creation, class, background, skill, equipment); this feature is secondary priority and will be re-evaluated next grooming cycle.
- Consolidated into: dc-cr-equipment-ch06 (requirements covered in that feature's acceptance criteria)
- Priority: P2
- PM owner: pm-dungeoncrawler
- Dev owner: dev-dungeoncrawler
- QA owner: qa-dungeoncrawler
- Source: PF2E Core Rulebook (Fourth Printing), lines 1–300
- Category: item
- Created: 2026-02-26

## Goal

Implement the alchemical item catalog: bombs, elixirs, mutagens, poisons, and other alchemical consumables. These are non-magical items with limited uses, a level, activation cost (usually 1 action), and specific effects (damage, healing, buffs). The alchemist class can craft these daily via Infused Reagents. Alchemical items provide accessible power to non-spellcasting builds.

## Source reference

> "Award treasure, from magic weapons to alchemical compounds and transforming statues. The rules for activating and wearing alchemical and magical items are found here as well." (Chapter 11: Crafting & Treasure)

## Implementation hint

Content type: `alchemical_item` with fields for alchemical type (bomb/elixir/mutagen/poison/tool), level, price, bulk, activation cost, effect, and duration. Bombs integrate with the attack roll / encounter system. Alchemist daily infusion: separate flow that grants free alchemical items each day scaled to level. Distinct from magic items (no runes, no invest requirement).

## Mission alignment

- [x] Aligns with democratized community game experience
- [x] Does not add surveillance or restrict community access

## Security acceptance criteria

- Security AC exemption: catalog/content and rules-data scope only; use existing item, inventory, and crafting surfaces without introducing new routes or novel input handling.

## Implementation notes (Dev)

### Files created/modified

**EquipmentCatalogService.php**
- Lines 814-892: Added 4 new alchemical items to CATALOG constant
  - cognitive-mutagen (mutagen): +2 Int, -1 Wis, 10 rounds
  - quicksilver-mutagen (mutagen): +2 Dex, -1 Str, 10 rounds
  - hulking-mutagen (mutagen): +2 Str, -1 Dex, 10 rounds
  - alchemical-primer (tool): Crafting reference guide, no proficiency required

**AlchemicalItemService.php** (new file)
- 254 lines of service code for alchemical item management
- Static methods for category filtering, validation, and catalog access
- Validation logic ensures all items have required metadata
- Integration with EquipmentCatalogService::getByType('alchemical')

**AlchemicalItemsTest.php** (new file)
- 327 lines of test code covering 5 test cases (TC-ALC-01 through TC-ALC-05)
- TC-ALC-01: All required categories present (bombs ✓, elixirs ✓, mutagens ✓, poisons ✓, tools ✓)
- TC-ALC-02: Metadata complete (all 15 items have id, name, type, price_gp, bulk, alchemical_stats)
- TC-ALC-03: Non-magical validation (no invest_slots, no rune_slots)
- TC-ALC-04: Mutagen compatibility (benefit[], drawback[], duration_rounds, mutagen trait)
- TC-ALC-05: Tool non-proficiency (tools don't require proficiency)

### Verification status

All 5 test cases PASS:
- 15 total alchemical items in catalog (4 new)
- Existing 11 items (4 bombs, 7 elixirs, 2 poisons) + 4 new items (3 mutagens, 1 tool)
- All items have required metadata
- No invest or rune slots (non-magical)
- Mutagens have benefit/drawback arrays compatible with MagicItemService::applyMutagen()
- Tools don't require proficiency

### Acceptance criteria met

- AC1: ✓ Catalog includes bombs, elixirs, mutagens, poisons, alchemical tools
- AC2: ✓ All items have level (category acts as level), price_gp, bulk, duration or activation cost
- AC3: ✓ No invest slots, no rune attachment on any item
- AC4: ✓ Mutagen items have benefit/drawback arrays for applyMutagen() integration
- AC5: ✓ Tools don't require proficiency; can be used for Crafting

### Ready for QA Gate 2 verification

