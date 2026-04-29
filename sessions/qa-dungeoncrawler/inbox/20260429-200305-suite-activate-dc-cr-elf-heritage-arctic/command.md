# Suite Activation: dc-cr-elf-heritage-arctic

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
   **CRITICAL: tag every new entry with `"feature_id": "dc-cr-elf-heritage-arctic"`**  
   This links the test to the living requirements doc at `features/dc-cr-elf-heritage-arctic/`.  
   Dev reads this field to know: failing test = new feature to implement, not a regression.  
   Minimum suite entry structure:
   ```json
   {
     "id": "dc-cr-elf-heritage-arctic-e2e",
     "label": "<describe what the test verifies>",
     "type": "e2e",
     "feature_id": "dc-cr-elf-heritage-arctic",
     "command": "<playwright or test command>",
     "artifacts": ["<report path>"],
     "required_for_release": true
   }
   ```

2. **Add permission rules to** `org-chart/sites/dungeoncrawler.life/qa-permissions.json`  
   For any new routes/ACL expectations.  
   **CRITICAL: tag every new rule with `"feature_id": "dc-cr-elf-heritage-arctic"`**  
   Example:
   ```json
   {
     "id": "dc-cr-elf-heritage-arctic-<route-slug>",
     "feature_id": "dc-cr-elf-heritage-arctic",
     "path_regex": "/your-new-route",
     "notes": "Added for feature dc-cr-elf-heritage-arctic",
     "expect": { "anon": "...", "authenticated": "..." }
   }
   ```

3. **Validate the suite:**
   ```bash
   python3 scripts/qa-suite-validate.py
   ```

4. **Write outbox** confirming: how many entries added, feature_id tagged on each, suite validated, any gaps flagged.

### Test plan (written during grooming)

# Test Plan: dc-cr-elf-heritage-arctic

## Coverage summary
- AC items: 9 (4 happy path, 3 edge cases, 2 failure modes)
- Test cases: 5 (TC-EAR-01-05)
- Suites: playwright (character creation, resistances, environmental hazards)
- Security: Security AC exemption: passive ancestry heritage behavior only; no new routes or input surfaces beyond existing heritage assignment, resistance, and hazard-resolution handlers.

---

## TC-EAR-01 — Heritage availability and ancestry gating
- Description: Arctic Elf is present as an elf-only heritage option.
- Suite: playwright/character-creation
- Expected: Arctic Elf is present as an elf-only heritage option.
- AC: Happy Path-1

## TC-EAR-02 — Primary passive effect application
- Description: Selecting Arctic Elf grants cold resistance equal to half the character level, minimum 1.
- Suite: playwright/encounter
- Expected: Selecting Arctic Elf grants cold resistance equal to half the character level, minimum 1.; Environmental cold effects are treated as one step less severe for the character.
- AC: Happy Path-2, Happy Path-3

## TC-EAR-03 — Scaling, automation, and visible state updates
- Description: Environmental cold effects are treated as one step less severe for the character.
- Suite: playwright/encounter
- Expected: Environmental cold effects are treated as one step less severe for the character.; The cold-resistance value recalculates when the character level changes.
- AC: Happy Path-3, Happy Path-4

## TC-EAR-04 — Edge-case rules interaction coverage
- Description: Level 1 characters still receive the minimum cold resistance of 1.
- Suite: playwright/encounter
- Expected: Level 1 characters still receive the minimum cold resistance of 1.; Only cold/environmental-cold effects are downgraded; unrelated environmental hazards stay unchanged.; One-step severity downgrades follow the documented ladder without skipping directly to harmless.
- AC: Edge Cases-1, Edge Cases-2, Edge Cases-3

## TC-EAR-05 — Validation errors and safe fallback behavior
- Description: Non-elf characters cannot select Arctic Elf heritage.
- Suite: playwright/encounter
- Expected: Non-elf characters cannot select Arctic Elf heritage.; If an environmental hazard lacks cold-severity metadata, the hazard resolves normally instead of producing an implementation error.
- AC: Failure Modes-1, Failure Modes-2

### Acceptance criteria (reference)

# Acceptance Criteria — dc-cr-elf-heritage-arctic

- Feature: Arctic Elf Heritage
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Make Arctic Elf a QA-ready heritage contract with level-scaling cold resistance and one-step environmental cold mitigation so the missing implementation gaps can be tested directly.

## Dependency checkpoints

- Depends on: dc-cr-elf-ancestry, dc-cr-heritage-system

## Happy Path

- [ ] `[NEW]` Arctic Elf is present as an elf-only heritage option.
- [ ] `[NEW]` Selecting Arctic Elf grants cold resistance equal to half the character level, minimum 1.
- [ ] `[NEW]` Environmental cold effects are treated as one step less severe for the character.
- [ ] `[NEW]` The cold-resistance value recalculates when the character level changes.

## Edge Cases

- [ ] `[NEW]` Level 1 characters still receive the minimum cold resistance of 1.
- [ ] `[NEW]` Only cold/environmental-cold effects are downgraded; unrelated environmental hazards stay unchanged.
- [ ] `[NEW]` One-step severity downgrades follow the documented ladder without skipping directly to harmless.

## Failure Modes

- [ ] `[NEW]` Non-elf characters cannot select Arctic Elf heritage.
- [ ] `[NEW]` If an environmental hazard lacks cold-severity metadata, the hazard resolves normally instead of producing an implementation error.

## Security acceptance criteria

- Security AC exemption: passive ancestry heritage behavior only; no new routes or input surfaces beyond existing heritage assignment, resistance, and hazard-resolution handlers.
