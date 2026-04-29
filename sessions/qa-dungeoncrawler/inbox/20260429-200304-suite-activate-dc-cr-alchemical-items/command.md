# Suite Activation: dc-cr-alchemical-items

**From:** pm-dungeoncrawler  
**To:** qa-dungeoncrawler  
**Date:** 2026-04-29T20:03:04+00:00  

## Task

This feature has been selected into the current release scope. Activate its test plan into the live QA suite.

**Now** is when you add tests to `suite.json` and `qa-permissions.json`.
The feature is in scope; Dev will implement it this release. Tests must be live for Stage 4 regression.

### Required actions

1. **Add a suite entry to** `qa-suites/products/dungeoncrawler/suite.json`  
   Use the test plan below as the spec.  
   **CRITICAL: tag every new entry with `"feature_id": "dc-cr-alchemical-items"`**  
   This links the test to the living requirements doc at `features/dc-cr-alchemical-items/`.  
   Dev reads this field to know: failing test = new feature to implement, not a regression.  
   Minimum suite entry structure:
   ```json
   {
     "id": "dc-cr-alchemical-items-e2e",
     "label": "<describe what the test verifies>",
     "type": "e2e",
     "feature_id": "dc-cr-alchemical-items",
     "command": "<playwright or test command>",
     "artifacts": ["<report path>"],
     "required_for_release": true
   }
   ```

2. **Add permission rules to** `org-chart/sites/dungeoncrawler.life/qa-permissions.json`  
   For any new routes/ACL expectations.  
   **CRITICAL: tag every new rule with `"feature_id": "dc-cr-alchemical-items"`**  
   Example:
   ```json
   {
     "id": "dc-cr-alchemical-items-<route-slug>",
     "feature_id": "dc-cr-alchemical-items",
     "path_regex": "/your-new-route",
     "notes": "Added for feature dc-cr-alchemical-items",
     "expect": { "anon": "...", "authenticated": "..." }
   }
   ```

3. **Validate the suite:**
   ```bash
   python3 scripts/qa-suite-validate.py
   ```

4. **Write outbox** confirming: how many entries added, feature_id tagged on each, suite validated, any gaps flagged.

### Test plan (written during grooming)

# Test Plan: dc-cr-alchemical-items

## Coverage summary
- AC items: 9 (4 happy path, 3 edge cases, 2 failure modes)
- Test cases: 5 (TC-ALC-01-05)
- Suites: playwright (inventory, encounter, daily prep)
- Security: Security AC exemption: catalog/content and rules-data scope only; use existing item, inventory, and crafting surfaces without introducing new routes or novel input handling.

---

## TC-ALC-01 — Catalog scope and availability
- Description: Catalog coverage includes bombs, elixirs, mutagens, poisons, and at least one non-consumable alchemical tool grouping so QA can verify the chapter scope is represented.
- Suite: playwright/inventory
- Expected: Catalog coverage includes bombs, elixirs, mutagens, poisons, and at least one non-consumable alchemical tool grouping so QA can verify the chapter scope is represented.
- AC: Happy Path-1

## TC-ALC-02 — Required metadata and primary rule data
- Description: Each alchemical item record exposes level, price, bulk, activation cost, duration or persistence, and effect text needed by inventory and encounter rendering.
- Suite: playwright/inventory
- Expected: Each alchemical item record exposes level, price, bulk, activation cost, duration or persistence, and effect text needed by inventory and encounter rendering.; Bomb entries are marked as thrown alchemical consumables and identify the damage/effect payload the encounter resolver must apply on use.
- AC: Happy Path-2, Happy Path-3

## TC-ALC-03 — Runtime item state and downstream flow
- Description: Bomb entries are marked as thrown alchemical consumables and identify the damage/effect payload the encounter resolver must apply on use.
- Suite: playwright/encounter
- Expected: Bomb entries are marked as thrown alchemical consumables and identify the damage/effect payload the encounter resolver must apply on use.; Alchemist daily-prep / Infused Reagents flows only surface items flagged as alchemical and consumable where the rules expect that behavior.
- AC: Happy Path-3, Happy Path-4

## TC-ALC-04 — Edge-case category handling
- Description: Alchemical items remain non-magical: they do not consume invest slots and are not mislabeled as spells, runes, or other magical equipment.
- Suite: playwright/inventory
- Expected: Alchemical items remain non-magical: they do not consume invest slots and are not mislabeled as spells, runes, or other magical equipment.; Consumable quantity/use tracking removes a spent item after use while persistent catalog metadata remains intact for future crafting or prep.; Category-specific rules (for example poison delivery vs. mutagen self-use) can be validated without collapsing the categories into a single generic effect bucket.
- AC: Edge Cases-1, Edge Cases-2, Edge Cases-3

## TC-ALC-05 — Validation safeguards and invalid metadata handling
- Description: Items missing required catalog metadata (level, activation, or effect summary) are rejected during content validation rather than silently published.
- Suite: playwright/inventory
- Expected: Items missing required catalog metadata (level, activation, or effect summary) are rejected during content validation rather than silently published.; Magic-item-only behaviors such as investment or rune slots are not attached to alchemical records.
- AC: Failure Modes-1, Failure Modes-2

### Acceptance criteria (reference)

# Acceptance Criteria — dc-cr-alchemical-items

- Feature: Alchemical Items
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Capture the alchemical item backlog as a QA-ready contract covering bombs, elixirs, mutagens, poisons, and other consumables, including the metadata the item catalog and alchemist daily-prep flows need in order to behave consistently.

## Dependency checkpoints

- Consolidated into: dc-cr-equipment-ch06 (requirements covered in that feature's acceptance criteria)

## Happy Path

- [ ] `[NEW]` Catalog coverage includes bombs, elixirs, mutagens, poisons, and at least one non-consumable alchemical tool grouping so QA can verify the chapter scope is represented.
- [ ] `[NEW]` Each alchemical item record exposes level, price, bulk, activation cost, duration or persistence, and effect text needed by inventory and encounter rendering.
- [ ] `[NEW]` Bomb entries are marked as thrown alchemical consumables and identify the damage/effect payload the encounter resolver must apply on use.
- [ ] `[NEW]` Alchemist daily-prep / Infused Reagents flows only surface items flagged as alchemical and consumable where the rules expect that behavior.

## Edge Cases

- [ ] `[NEW]` Alchemical items remain non-magical: they do not consume invest slots and are not mislabeled as spells, runes, or other magical equipment.
- [ ] `[NEW]` Consumable quantity/use tracking removes a spent item after use while persistent catalog metadata remains intact for future crafting or prep.
- [ ] `[NEW]` Category-specific rules (for example poison delivery vs. mutagen self-use) can be validated without collapsing the categories into a single generic effect bucket.

## Failure Modes

- [ ] `[NEW]` Items missing required catalog metadata (level, activation, or effect summary) are rejected during content validation rather than silently published.
- [ ] `[NEW]` Magic-item-only behaviors such as investment or rune slots are not attached to alchemical records.

## Security acceptance criteria

- Security AC exemption: catalog/content and rules-data scope only; use existing item, inventory, and crafting surfaces without introducing new routes or novel input handling.
- Agent: qa-dungeoncrawler
- Status: pending
