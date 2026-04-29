# Suite Activation: dc-cr-dwarven-weapon-familiarity

**From:** pm-dungeoncrawler  
**To:** qa-dungeoncrawler  
**Date:** 2026-04-29T20:03:05+00:00  

## Task

This feature has been selected into the current release scope. Activate its test plan into the live QA suite.

**Now** is when you add tests to `suite.json` and `qa-permissions.json`.
The feature is in scope; Dev will implement it this release. Tests must be live for Stage 4 regression.

### Required actions

1. **Add a suite entry to** `qa-suites/products/dungeoncrawler/suite.json`  
   Use the test plan below as the spec.  
   **CRITICAL: tag every new entry with `"feature_id": "dc-cr-dwarven-weapon-familiarity"`**  
   This links the test to the living requirements doc at `features/dc-cr-dwarven-weapon-familiarity/`.  
   Dev reads this field to know: failing test = new feature to implement, not a regression.  
   Minimum suite entry structure:
   ```json
   {
     "id": "dc-cr-dwarven-weapon-familiarity-e2e",
     "label": "<describe what the test verifies>",
     "type": "e2e",
     "feature_id": "dc-cr-dwarven-weapon-familiarity",
     "command": "<playwright or test command>",
     "artifacts": ["<report path>"],
     "required_for_release": true
   }
   ```

2. **Add permission rules to** `org-chart/sites/dungeoncrawler.life/qa-permissions.json`  
   For any new routes/ACL expectations.  
   **CRITICAL: tag every new rule with `"feature_id": "dc-cr-dwarven-weapon-familiarity"`**  
   Example:
   ```json
   {
     "id": "dc-cr-dwarven-weapon-familiarity-<route-slug>",
     "feature_id": "dc-cr-dwarven-weapon-familiarity",
     "path_regex": "/your-new-route",
     "notes": "Added for feature dc-cr-dwarven-weapon-familiarity",
     "expect": { "anon": "...", "authenticated": "..." }
   }
   ```

3. **Validate the suite:**
   ```bash
   python3 scripts/qa-suite-validate.py
   ```

4. **Write outbox** confirming: how many entries added, feature_id tagged on each, suite validated, any gaps flagged.

### Test plan (written during grooming)

# Test Plan: dc-cr-dwarven-weapon-familiarity

## Coverage summary
- AC items: 9 (4 happy path, 3 edge cases, 2 failure modes)
- Test cases: 5 (TC-DWF-01-05)
- Suites: playwright (character creation, inventory, weapon proficiency)
- Security: Security AC exemption: ancestry-feat and proficiency-calculation scope only; no new routes or input surfaces beyond existing feat assignment and character build handlers.

---

## TC-DWF-01 — Feat availability and prerequisite gating
- Description: The feat exists as a level-1 dwarf ancestry feat and is only available through a valid ancestry-feat slot.
- Suite: playwright/character-creation
- Expected: The feat exists as a level-1 dwarf ancestry feat and is only available through a valid ancestry-feat slot.
- AC: Happy Path-1

## TC-DWF-02 — Primary granted benefit application
- Description: Selecting the feat grants trained proficiency with battle axe, pick, and warhammer.
- Suite: playwright/inventory
- Expected: Selecting the feat grants trained proficiency with battle axe, pick, and warhammer.; Uncommon dwarf weapons become available to the character once the feat is selected.
- AC: Happy Path-2, Happy Path-3

## TC-DWF-03 — Recalculation, retraining, and later progression behavior
- Description: Uncommon dwarf weapons become available to the character once the feat is selected.
- Suite: playwright/inventory
- Expected: Uncommon dwarf weapons become available to the character once the feat is selected.; Martial dwarf weapons count as simple and advanced dwarf weapons count as martial for this character's proficiency calculations.
- AC: Happy Path-3, Happy Path-4

## TC-DWF-04 — Edge-case rules interaction coverage
- Description: Non-dwarf characters and characters without an open ancestry-feat slot cannot select the feat.
- Suite: playwright/inventory
- Expected: Non-dwarf characters and characters without an open ancestry-feat slot cannot select the feat.; If the character later gains broader proficiency from class progression, the familiarity remapping still resolves correctly.; Removing or retraining the feat restores the baseline weapon-access rules.
- AC: Edge Cases-1, Edge Cases-2, Edge Cases-3

## TC-DWF-05 — Validation errors and malformed-data handling
- Description: Malformed or non-dwarf weapon tags are rejected during content validation.
- Suite: playwright/inventory
- Expected: Malformed or non-dwarf weapon tags are rejected during content validation.; The proficiency remapping never exposes unrelated uncommon weapons outside the dwarf weapon group.
- AC: Failure Modes-1, Failure Modes-2

### Acceptance criteria (reference)

# Acceptance Criteria — dc-cr-dwarven-weapon-familiarity

- Feature: Dwarven Weapon Familiarity (Ancestry Feat)
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Define Dwarven Weapon Familiarity as a level-1 ancestry-feat contract covering the granted proficiencies, uncommon dwarven-weapon access, and weapon-category remapping rules.

## Dependency checkpoints

- Depends on: dc-cr-dwarf-ancestry, dc-cr-ancestry-feat-schedule, dc-cr-equipment-system
- Merged into: dc-cr-dwarf-ancestry (all heritages and ancestry feats covered in bulk AC)

## Happy Path

- [ ] `[NEW]` The feat exists as a level-1 dwarf ancestry feat and is only available through a valid ancestry-feat slot.
- [ ] `[NEW]` Selecting the feat grants trained proficiency with battle axe, pick, and warhammer.
- [ ] `[NEW]` Uncommon dwarf weapons become available to the character once the feat is selected.
- [ ] `[NEW]` Martial dwarf weapons count as simple and advanced dwarf weapons count as martial for this character's proficiency calculations.

## Edge Cases

- [ ] `[NEW]` Non-dwarf characters and characters without an open ancestry-feat slot cannot select the feat.
- [ ] `[NEW]` If the character later gains broader proficiency from class progression, the familiarity remapping still resolves correctly.
- [ ] `[NEW]` Removing or retraining the feat restores the baseline weapon-access rules.

## Failure Modes

- [ ] `[NEW]` Malformed or non-dwarf weapon tags are rejected during content validation.
- [ ] `[NEW]` The proficiency remapping never exposes unrelated uncommon weapons outside the dwarf weapon group.

## Security acceptance criteria

- Security AC exemption: ancestry-feat and proficiency-calculation scope only; no new routes or input surfaces beyond existing feat assignment and character build handlers.
