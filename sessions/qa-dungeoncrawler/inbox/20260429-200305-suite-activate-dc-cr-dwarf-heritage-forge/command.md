# Suite Activation: dc-cr-dwarf-heritage-forge

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
   **CRITICAL: tag every new entry with `"feature_id": "dc-cr-dwarf-heritage-forge"`**  
   This links the test to the living requirements doc at `features/dc-cr-dwarf-heritage-forge/`.  
   Dev reads this field to know: failing test = new feature to implement, not a regression.  
   Minimum suite entry structure:
   ```json
   {
     "id": "dc-cr-dwarf-heritage-forge-e2e",
     "label": "<describe what the test verifies>",
     "type": "e2e",
     "feature_id": "dc-cr-dwarf-heritage-forge",
     "command": "<playwright or test command>",
     "artifacts": ["<report path>"],
     "required_for_release": true
   }
   ```

2. **Add permission rules to** `org-chart/sites/dungeoncrawler.life/qa-permissions.json`  
   For any new routes/ACL expectations.  
   **CRITICAL: tag every new rule with `"feature_id": "dc-cr-dwarf-heritage-forge"`**  
   Example:
   ```json
   {
     "id": "dc-cr-dwarf-heritage-forge-<route-slug>",
     "feature_id": "dc-cr-dwarf-heritage-forge",
     "path_regex": "/your-new-route",
     "notes": "Added for feature dc-cr-dwarf-heritage-forge",
     "expect": { "anon": "...", "authenticated": "..." }
   }
   ```

3. **Validate the suite:**
   ```bash
   python3 scripts/qa-suite-validate.py
   ```

4. **Write outbox** confirming: how many entries added, feature_id tagged on each, suite validated, any gaps flagged.

### Test plan (written during grooming)

# Test Plan: dc-cr-dwarf-heritage-forge

## Coverage summary
- AC items: 9 (4 happy path, 3 edge cases, 2 failure modes)
- Test cases: 5 (TC-DFR-01-05)
- Suites: playwright (character creation, resistances, environmental hazards)
- Security: Security AC exemption: passive ancestry heritage behavior only; no new routes or input surfaces beyond existing heritage assignment, resistance, and hazard-resolution handlers.

---

## TC-DFR-01 — Heritage availability and ancestry gating
- Description: Forge Dwarf is available only under the dwarf ancestry heritage list.
- Suite: playwright/character-creation
- Expected: Forge Dwarf is available only under the dwarf ancestry heritage list.
- AC: Happy Path-1

## TC-DFR-02 — Primary passive effect application
- Description: Selecting Forge Dwarf grants fire resistance equal to half the character level, with a minimum of 1.
- Suite: playwright/encounter
- Expected: Selecting Forge Dwarf grants fire resistance equal to half the character level, with a minimum of 1.; Environmental heat effects are treated as one step less severe for a Forge Dwarf character.
- AC: Happy Path-2, Happy Path-3

## TC-DFR-03 — Scaling, automation, and visible state updates
- Description: Environmental heat effects are treated as one step less severe for a Forge Dwarf character.
- Suite: playwright/encounter
- Expected: Environmental heat effects are treated as one step less severe for a Forge Dwarf character.; The fire-resistance value recalculates automatically when the character level changes.
- AC: Happy Path-3, Happy Path-4

## TC-DFR-04 — Edge-case rules interaction coverage
- Description: Level 1 characters still receive the minimum fire resistance of 1.
- Suite: playwright/encounter
- Expected: Level 1 characters still receive the minimum fire resistance of 1.; Environmental heat downgrades follow the documented one-step ladder and do not skip multiple severity bands.; Non-fire damage and non-heat environmental effects are unaffected by the heritage.
- AC: Edge Cases-1, Edge Cases-2, Edge Cases-3

## TC-DFR-05 — Validation errors and safe fallback behavior
- Description: Selecting Forge Dwarf on a non-dwarf character returns a validation error.
- Suite: playwright/encounter
- Expected: Selecting Forge Dwarf on a non-dwarf character returns a validation error.; If an environmental hazard lacks a heat severity tag, the hazard resolves normally instead of crashing the encounter flow.
- AC: Failure Modes-1, Failure Modes-2

### Acceptance criteria (reference)

# Acceptance Criteria — dc-cr-dwarf-heritage-forge

- Feature: Dwarf Heritage — Forge Dwarf
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Define Forge Dwarf as a QA-ready heritage contract with level-scaling fire resistance and environmental heat mitigation so both combat damage and exploration hazards can be validated against the same rules.

## Dependency checkpoints

- Depends on: dc-cr-dwarf-ancestry, dc-cr-heritage-system
- Merged into: dc-cr-dwarf-ancestry (all heritages and ancestry feats covered in bulk AC)

## Happy Path

- [ ] `[NEW]` Forge Dwarf is available only under the dwarf ancestry heritage list.
- [ ] `[NEW]` Selecting Forge Dwarf grants fire resistance equal to half the character level, with a minimum of 1.
- [ ] `[NEW]` Environmental heat effects are treated as one step less severe for a Forge Dwarf character.
- [ ] `[NEW]` The fire-resistance value recalculates automatically when the character level changes.

## Edge Cases

- [ ] `[NEW]` Level 1 characters still receive the minimum fire resistance of 1.
- [ ] `[NEW]` Environmental heat downgrades follow the documented one-step ladder and do not skip multiple severity bands.
- [ ] `[NEW]` Non-fire damage and non-heat environmental effects are unaffected by the heritage.

## Failure Modes

- [ ] `[NEW]` Selecting Forge Dwarf on a non-dwarf character returns a validation error.
- [ ] `[NEW]` If an environmental hazard lacks a heat severity tag, the hazard resolves normally instead of crashing the encounter flow.

## Security acceptance criteria

- Security AC exemption: passive ancestry heritage behavior only; no new routes or input surfaces beyond existing heritage assignment, resistance, and hazard-resolution handlers.
- Agent: qa-dungeoncrawler
- Status: pending
