# Suite Activation: dc-cr-magic-items

**From:** pm-dungeoncrawler  
**To:** qa-dungeoncrawler  
**Date:** 2026-04-29T20:03:06+00:00  

## Task

This feature has been selected into the current release scope. Activate its test plan into the live QA suite.

**Now** is when you add tests to `suite.json` and `qa-permissions.json`.
The feature is in scope; Dev will implement it this release. Tests must be live for Stage 4 regression.

### Required actions

1. **Add a suite entry to** `qa-suites/products/dungeoncrawler/suite.json`  
   Use the test plan below as the spec.  
   **CRITICAL: tag every new entry with `"feature_id": "dc-cr-magic-items"`**  
   This links the test to the living requirements doc at `features/dc-cr-magic-items/`.  
   Dev reads this field to know: failing test = new feature to implement, not a regression.  
   Minimum suite entry structure:
   ```json
   {
     "id": "dc-cr-magic-items-e2e",
     "label": "<describe what the test verifies>",
     "type": "e2e",
     "feature_id": "dc-cr-magic-items",
     "command": "<playwright or test command>",
     "artifacts": ["<report path>"],
     "required_for_release": true
   }
   ```

2. **Add permission rules to** `org-chart/sites/dungeoncrawler.life/qa-permissions.json`  
   For any new routes/ACL expectations.  
   **CRITICAL: tag every new rule with `"feature_id": "dc-cr-magic-items"`**  
   Example:
   ```json
   {
     "id": "dc-cr-magic-items-<route-slug>",
     "feature_id": "dc-cr-magic-items",
     "path_regex": "/your-new-route",
     "notes": "Added for feature dc-cr-magic-items",
     "expect": { "anon": "...", "authenticated": "..." }
   }
   ```

3. **Validate the suite:**
   ```bash
   python3 scripts/qa-suite-validate.py
   ```

4. **Write outbox** confirming: how many entries added, feature_id tagged on each, suite validated, any gaps flagged.

### Test plan (written during grooming)

# Test Plan: dc-cr-magic-items

## Coverage summary
- AC items: 9 (4 happy path, 3 edge cases, 2 failure modes)
- Test cases: 5 (TC-MIT-01-05)
- Suites: playwright (inventory, equipment, investment limits)
- Security: Security AC exemption: catalog, inventory, and equipment-rule scope only; use existing item management surfaces without introducing new routes or novel input handling.

---

## TC-MIT-01 — Catalog scope and availability
- Description: The catalog covers weapons, armor, wondrous items, and other held/worn item types needed by chapter 11 scope.
- Suite: playwright/inventory
- Expected: The catalog covers weapons, armor, wondrous items, and other held/worn item types needed by chapter 11 scope.
- AC: Happy Path-1

## TC-MIT-02 — Required metadata and primary rule data
- Description: Each magic item includes level, price, activation method, and usage state such as held, worn, or invested.
- Suite: playwright/inventory
- Expected: Each magic item includes level, price, activation method, and usage state such as held, worn, or invested.; Characters can equip and track invested items, with a hard cap of 10 invested items at one time.
- AC: Happy Path-2, Happy Path-3

## TC-MIT-03 — Runtime item state and downstream flow
- Description: Characters can equip and track invested items, with a hard cap of 10 invested items at one time.
- Suite: playwright/inventory
- Expected: Characters can equip and track invested items, with a hard cap of 10 invested items at one time.; Inventory/equipment flows can differentiate held, worn, and invested behaviors when presenting item actions and restrictions.
- AC: Happy Path-3, Happy Path-4

## TC-MIT-04 — Edge-case category handling
- Description: Items that are worn or held but not invested do not consume one of the 10 investment slots.
- Suite: playwright/inventory
- Expected: Items that are worn or held but not invested do not consume one of the 10 investment slots.; Activation types such as command word, Cast a Spell, and Interact remain distinguishable in the catalog and UI contract.; Characters unequipping or uninvesting an item immediately free the consumed invest slot for another item.
- AC: Edge Cases-1, Edge Cases-2, Edge Cases-3

## TC-MIT-05 — Validation safeguards and invalid metadata handling
- Description: Attempting to invest an eleventh item fails with a validation error rather than silently exceeding the cap.
- Suite: playwright/inventory
- Expected: Attempting to invest an eleventh item fails with a validation error rather than silently exceeding the cap.; Catalog entries missing required activation or usage metadata are rejected during validation.
- AC: Failure Modes-1, Failure Modes-2

### Acceptance criteria (reference)

# Acceptance Criteria — dc-cr-magic-items

- Feature: Magic Items and Treasure
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Capture the magic-item backlog as a QA-ready contract covering the item catalog, activation/usage metadata, and the invested-item limit so inventory and encounter systems have a concrete rules target.

## Dependency checkpoints

- Consolidated into: dc-cr-magic-ch11 (requirements covered in that feature's acceptance criteria)

## Happy Path

- [ ] `[NEW]` The catalog covers weapons, armor, wondrous items, and other held/worn item types needed by chapter 11 scope.
- [ ] `[NEW]` Each magic item includes level, price, activation method, and usage state such as held, worn, or invested.
- [ ] `[NEW]` Characters can equip and track invested items, with a hard cap of 10 invested items at one time.
- [ ] `[NEW]` Inventory/equipment flows can differentiate held, worn, and invested behaviors when presenting item actions and restrictions.

## Edge Cases

- [ ] `[NEW]` Items that are worn or held but not invested do not consume one of the 10 investment slots.
- [ ] `[NEW]` Activation types such as command word, Cast a Spell, and Interact remain distinguishable in the catalog and UI contract.
- [ ] `[NEW]` Characters unequipping or uninvesting an item immediately free the consumed invest slot for another item.

## Failure Modes

- [ ] `[NEW]` Attempting to invest an eleventh item fails with a validation error rather than silently exceeding the cap.
- [ ] `[NEW]` Catalog entries missing required activation or usage metadata are rejected during validation.

## Security acceptance criteria

- Security AC exemption: catalog, inventory, and equipment-rule scope only; use existing item management surfaces without introducing new routes or novel input handling.
- Agent: qa-dungeoncrawler
- Status: pending
