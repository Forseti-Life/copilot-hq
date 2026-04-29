# Suite Activation: dc-cr-mountains-stoutness

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
   **CRITICAL: tag every new entry with `"feature_id": "dc-cr-mountains-stoutness"`**  
   This links the test to the living requirements doc at `features/dc-cr-mountains-stoutness/`.  
   Dev reads this field to know: failing test = new feature to implement, not a regression.  
   Minimum suite entry structure:
   ```json
   {
     "id": "dc-cr-mountains-stoutness-e2e",
     "label": "<describe what the test verifies>",
     "type": "e2e",
     "feature_id": "dc-cr-mountains-stoutness",
     "command": "<playwright or test command>",
     "artifacts": ["<report path>"],
     "required_for_release": true
   }
   ```

2. **Add permission rules to** `org-chart/sites/dungeoncrawler.life/qa-permissions.json`  
   For any new routes/ACL expectations.  
   **CRITICAL: tag every new rule with `"feature_id": "dc-cr-mountains-stoutness"`**  
   Example:
   ```json
   {
     "id": "dc-cr-mountains-stoutness-<route-slug>",
     "feature_id": "dc-cr-mountains-stoutness",
     "path_regex": "/your-new-route",
     "notes": "Added for feature dc-cr-mountains-stoutness",
     "expect": { "anon": "...", "authenticated": "..." }
   }
   ```

3. **Validate the suite:**
   ```bash
   python3 scripts/qa-suite-validate.py
   ```

4. **Write outbox** confirming: how many entries added, feature_id tagged on each, suite validated, any gaps flagged.

### Test plan (written during grooming)

# Test Plan: dc-cr-mountains-stoutness

## Coverage summary
- AC items: 9 (4 happy path, 3 edge cases, 2 failure modes)
- Test cases: 5 (TC-MST-01-05)
- Suites: playwright (feat progression, HP state, dying/recovery)
- Security: Security AC exemption: ancestry-feat and character-state math scope only; no new routes or input surfaces beyond existing feat assignment and dying-state handlers.

---

## TC-MST-01 — Feat availability and prerequisite gating
- Description: Mountain's Stoutness exists as a level-9 dwarf ancestry feat.
- Suite: playwright/feat-progression
- Expected: Mountain's Stoutness exists as a level-9 dwarf ancestry feat.
- AC: Happy Path-1

## TC-MST-02 — Primary granted benefit application
- Description: Selecting the feat adds the character's current level to maximum Hit Points.
- Suite: playwright/encounter
- Expected: Selecting the feat adds the character's current level to maximum Hit Points.; While dying, the recovery-check DC becomes `9 + dying_value` instead of the baseline `10 + dying_value`.
- AC: Happy Path-2, Happy Path-3

## TC-MST-03 — Recalculation, retraining, and later progression behavior
- Description: While dying, the recovery-check DC becomes `9 + dying_value` instead of the baseline `10 + dying_value`.
- Suite: playwright/encounter
- Expected: While dying, the recovery-check DC becomes `9 + dying_value` instead of the baseline `10 + dying_value`.; If the character also has Toughness, the HP bonuses stack and the recovery-check DC becomes `6 + dying_value`.
- AC: Happy Path-3, Happy Path-4

## TC-MST-04 — Edge-case rules interaction coverage
- Description: Level changes recalculate the added max HP automatically.
- Suite: playwright/feat-progression
- Expected: Level changes recalculate the added max HP automatically.; Characters without Toughness still receive the Mountain's Stoutness recovery-check adjustment without any extra flags.; Retraining or removing the feat restores the baseline HP and recovery-check formulas.
- AC: Edge Cases-1, Edge Cases-2, Edge Cases-3

## TC-MST-05 — Validation errors and malformed-data handling
- Description: Selecting the feat below level 9 or without a valid dwarf ancestry slot is rejected.
- Suite: playwright/feat-progression
- Expected: Selecting the feat below level 9 or without a valid dwarf ancestry slot is rejected.; The feat never changes unrelated death-and-dying rules beyond the documented recovery-check DC adjustment.
- AC: Failure Modes-1, Failure Modes-2

### Acceptance criteria (reference)

# Acceptance Criteria — dc-cr-mountains-stoutness

- Feature: Mountain's Stoutness (Dwarf Ancestry Feat)
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Turn Mountain's Stoutness into a QA-ready level-9 ancestry-feat contract for the added max HP, modified recovery-check DC, and Toughness stacking interaction.

## Dependency checkpoints

- Depends on: dc-cr-dwarf-ancestry, dc-cr-ancestry-feat-schedule, dc-cr-character-leveling, dc-cr-conditions
- Merged into: dc-cr-dwarf-ancestry (all heritages and ancestry feats covered in bulk AC)

## Happy Path

- [ ] `[NEW]` Mountain's Stoutness exists as a level-9 dwarf ancestry feat.
- [ ] `[NEW]` Selecting the feat adds the character's current level to maximum Hit Points.
- [ ] `[NEW]` While dying, the recovery-check DC becomes `9 + dying_value` instead of the baseline `10 + dying_value`.
- [ ] `[NEW]` If the character also has Toughness, the HP bonuses stack and the recovery-check DC becomes `6 + dying_value`.

## Edge Cases

- [ ] `[NEW]` Level changes recalculate the added max HP automatically.
- [ ] `[NEW]` Characters without Toughness still receive the Mountain's Stoutness recovery-check adjustment without any extra flags.
- [ ] `[NEW]` Retraining or removing the feat restores the baseline HP and recovery-check formulas.

## Failure Modes

- [ ] `[NEW]` Selecting the feat below level 9 or without a valid dwarf ancestry slot is rejected.
- [ ] `[NEW]` The feat never changes unrelated death-and-dying rules beyond the documented recovery-check DC adjustment.

## Security acceptance criteria

- Security AC exemption: ancestry-feat and character-state math scope only; no new routes or input surfaces beyond existing feat assignment and dying-state handlers.
- Agent: qa-dungeoncrawler
- Status: pending
