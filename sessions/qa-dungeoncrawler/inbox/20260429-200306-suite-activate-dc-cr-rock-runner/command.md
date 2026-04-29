# Suite Activation: dc-cr-rock-runner

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
   **CRITICAL: tag every new entry with `"feature_id": "dc-cr-rock-runner"`**  
   This links the test to the living requirements doc at `features/dc-cr-rock-runner/`.  
   Dev reads this field to know: failing test = new feature to implement, not a regression.  
   Minimum suite entry structure:
   ```json
   {
     "id": "dc-cr-rock-runner-e2e",
     "label": "<describe what the test verifies>",
     "type": "e2e",
     "feature_id": "dc-cr-rock-runner",
     "command": "<playwright or test command>",
     "artifacts": ["<report path>"],
     "required_for_release": true
   }
   ```

2. **Add permission rules to** `org-chart/sites/dungeoncrawler.life/qa-permissions.json`  
   For any new routes/ACL expectations.  
   **CRITICAL: tag every new rule with `"feature_id": "dc-cr-rock-runner"`**  
   Example:
   ```json
   {
     "id": "dc-cr-rock-runner-<route-slug>",
     "feature_id": "dc-cr-rock-runner",
     "path_regex": "/your-new-route",
     "notes": "Added for feature dc-cr-rock-runner",
     "expect": { "anon": "...", "authenticated": "..." }
   }
   ```

3. **Validate the suite:**
   ```bash
   python3 scripts/qa-suite-validate.py
   ```

4. **Write outbox** confirming: how many entries added, feature_id tagged on each, suite validated, any gaps flagged.

### Test plan (written during grooming)

# Test Plan: dc-cr-rock-runner

## Coverage summary
- AC items: 9 (4 happy path, 3 edge cases, 2 failure modes)
- Test cases: 5 (TC-RRN-01-05)
- Suites: playwright (feat progression, terrain movement, balance resolution)
- Security: Security AC exemption: ancestry-feat and terrain-resolution scope only; no new routes or input surfaces beyond existing feat assignment and movement handlers.

---

## TC-RRN-01 — Feat availability and prerequisite gating
- Description: Rock Runner exists as a level-1 dwarf ancestry feat.
- Suite: playwright/feat-progression
- Expected: Rock Runner exists as a level-1 dwarf ancestry feat.
- AC: Happy Path-1

## TC-RRN-02 — Primary granted benefit application
- Description: Stone or earth rubble no longer imposes its normal movement penalty on a character with Rock Runner.
- Suite: playwright/encounter
- Expected: Stone or earth rubble no longer imposes its normal movement penalty on a character with Rock Runner.; The character is not flat-footed when balancing on stone or earth narrow surfaces.
- AC: Happy Path-2, Happy Path-3

## TC-RRN-03 — Recalculation, retraining, and later progression behavior
- Description: The character is not flat-footed when balancing on stone or earth narrow surfaces.
- Suite: playwright/encounter
- Expected: The character is not flat-footed when balancing on stone or earth narrow surfaces.; A successful Balance check on stone or earth upgrades to a critical success for the feat owner.
- AC: Happy Path-3, Happy Path-4

## TC-RRN-04 — Edge-case rules interaction coverage
- Description: The feat only changes behavior on terrain or surfaces tagged as stone or earth; wood, metal, ice, and other materials remain baseline.
- Suite: playwright/encounter
- Expected: The feat only changes behavior on terrain or surfaces tagged as stone or earth; wood, metal, ice, and other materials remain baseline.; If the tactical grid omits a surface-material tag, balance and movement resolve with the default rules.; Only the feat owner receives the benefits; adjacent characters on the same tile do not.
- AC: Edge Cases-1, Edge Cases-2, Edge Cases-3

## TC-RRN-05 — Validation errors and malformed-data handling
- Description: Selecting the feat without a valid dwarf ancestry slot is rejected.
- Suite: playwright/encounter
- Expected: Selecting the feat without a valid dwarf ancestry slot is rejected.; Unknown or malformed terrain tags do not crash movement/balance resolution.
- AC: Failure Modes-1, Failure Modes-2

### Acceptance criteria (reference)

# Acceptance Criteria — dc-cr-rock-runner

- Feature: Rock Runner (Dwarf Ancestry Feat)
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Define Rock Runner as a level-1 dwarf ancestry-feat contract covering stone/earth terrain movement, narrow-surface balance benefits, and material-tag requirements in the tactical grid.

## Dependency checkpoints

- Depends on: dc-cr-dwarf-ancestry, dc-cr-ancestry-feat-schedule, dc-cr-tactical-grid
- Merged into: dc-cr-dwarf-ancestry (all heritages and ancestry feats covered in bulk AC)

## Happy Path

- [ ] `[NEW]` Rock Runner exists as a level-1 dwarf ancestry feat.
- [ ] `[NEW]` Stone or earth rubble no longer imposes its normal movement penalty on a character with Rock Runner.
- [ ] `[NEW]` The character is not flat-footed when balancing on stone or earth narrow surfaces.
- [ ] `[NEW]` A successful Balance check on stone or earth upgrades to a critical success for the feat owner.

## Edge Cases

- [ ] `[NEW]` The feat only changes behavior on terrain or surfaces tagged as stone or earth; wood, metal, ice, and other materials remain baseline.
- [ ] `[NEW]` If the tactical grid omits a surface-material tag, balance and movement resolve with the default rules.
- [ ] `[NEW]` Only the feat owner receives the benefits; adjacent characters on the same tile do not.

## Failure Modes

- [ ] `[NEW]` Selecting the feat without a valid dwarf ancestry slot is rejected.
- [ ] `[NEW]` Unknown or malformed terrain tags do not crash movement/balance resolution.

## Security acceptance criteria

- Security AC exemption: ancestry-feat and terrain-resolution scope only; no new routes or input surfaces beyond existing feat assignment and movement handlers.
- Agent: qa-dungeoncrawler
- Status: pending
