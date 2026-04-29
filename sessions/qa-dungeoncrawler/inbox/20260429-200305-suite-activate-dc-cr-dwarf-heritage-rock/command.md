# Suite Activation: dc-cr-dwarf-heritage-rock

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
   **CRITICAL: tag every new entry with `"feature_id": "dc-cr-dwarf-heritage-rock"`**  
   This links the test to the living requirements doc at `features/dc-cr-dwarf-heritage-rock/`.  
   Dev reads this field to know: failing test = new feature to implement, not a regression.  
   Minimum suite entry structure:
   ```json
   {
     "id": "dc-cr-dwarf-heritage-rock-e2e",
     "label": "<describe what the test verifies>",
     "type": "e2e",
     "feature_id": "dc-cr-dwarf-heritage-rock",
     "command": "<playwright or test command>",
     "artifacts": ["<report path>"],
     "required_for_release": true
   }
   ```

2. **Add permission rules to** `org-chart/sites/dungeoncrawler.life/qa-permissions.json`  
   For any new routes/ACL expectations.  
   **CRITICAL: tag every new rule with `"feature_id": "dc-cr-dwarf-heritage-rock"`**  
   Example:
   ```json
   {
     "id": "dc-cr-dwarf-heritage-rock-<route-slug>",
     "feature_id": "dc-cr-dwarf-heritage-rock",
     "path_regex": "/your-new-route",
     "notes": "Added for feature dc-cr-dwarf-heritage-rock",
     "expect": { "anon": "...", "authenticated": "..." }
   }
   ```

3. **Validate the suite:**
   ```bash
   python3 scripts/qa-suite-validate.py
   ```

4. **Write outbox** confirming: how many entries added, feature_id tagged on each, suite validated, any gaps flagged.

### Test plan (written during grooming)

# Test Plan: dc-cr-dwarf-heritage-rock

## Coverage summary
- AC items: 9 (4 happy path, 3 edge cases, 2 failure modes)
- Test cases: 5 (TC-DRK-01-05)
- Suites: playwright (character creation, maneuvers, forced movement)
- Security: Security AC exemption: passive ancestry heritage behavior only; no new routes or input surfaces beyond existing heritage assignment and combat-resolution handlers.

---

## TC-DRK-01 — Heritage availability and ancestry gating
- Description: Rock Dwarf is selectable only for dwarf characters within the heritage system.
- Suite: playwright/character-creation
- Expected: Rock Dwarf is selectable only for dwarf characters within the heritage system.
- AC: Happy Path-1

## TC-DRK-02 — Primary passive effect application
- Description: The heritage grants a +2 circumstance bonus to the relevant Fortitude or Reflex DC / save checks against Shove, Trip, and knock-prone effects.
- Suite: playwright/encounter
- Expected: The heritage grants a +2 circumstance bonus to the relevant Fortitude or Reflex DC / save checks against Shove, Trip, and knock-prone effects.; Forced movement affecting the character is reduced by half when the pushed or pulled distance is 10 feet or more.
- AC: Happy Path-2, Happy Path-3

## TC-DRK-03 — Scaling, automation, and visible state updates
- Description: Forced movement affecting the character is reduced by half when the pushed or pulled distance is 10 feet or more.
- Suite: playwright/encounter
- Expected: Forced movement affecting the character is reduced by half when the pushed or pulled distance is 10 feet or more.; The passive applies automatically during maneuver resolution without any manual toggle.
- AC: Happy Path-3, Happy Path-4

## TC-DRK-04 — Edge-case rules interaction coverage
- Description: Voluntary movement is never halved by the heritage.
- Suite: playwright/encounter
- Expected: Voluntary movement is never halved by the heritage.; Small forced movements below the threshold stay at their normal distance unless the movement engine already rounds them under existing rules.; The bonus applies only to the targeted anti-displacement effects and not to unrelated Reflex or Fortitude saves.
- AC: Edge Cases-1, Edge Cases-2, Edge Cases-3

## TC-DRK-05 — Validation errors and safe fallback behavior
- Description: Invalid ancestry/heritage combinations are rejected.
- Suite: playwright/encounter
- Expected: Invalid ancestry/heritage combinations are rejected.; Combat resolution falls back to the normal forced-movement rules if the action is not tagged as Shove, Trip, knock-prone, or forced movement.
- AC: Failure Modes-1, Failure Modes-2

### Acceptance criteria (reference)

# Acceptance Criteria — dc-cr-dwarf-heritage-rock

- Feature: Dwarf Heritage — Rock Dwarf
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Capture Rock Dwarf as a heritage contract for anti-displacement combat rules, including the defense bonus against Shove/Trip/knock-prone effects and the forced-movement reduction behavior.

## Dependency checkpoints

- Depends on: dc-cr-dwarf-ancestry, dc-cr-heritage-system
- Merged into: dc-cr-dwarf-ancestry (all heritages and ancestry feats covered in bulk AC)

## Happy Path

- [ ] `[NEW]` Rock Dwarf is selectable only for dwarf characters within the heritage system.
- [ ] `[NEW]` The heritage grants a +2 circumstance bonus to the relevant Fortitude or Reflex DC / save checks against Shove, Trip, and knock-prone effects.
- [ ] `[NEW]` Forced movement affecting the character is reduced by half when the pushed or pulled distance is 10 feet or more.
- [ ] `[NEW]` The passive applies automatically during maneuver resolution without any manual toggle.

## Edge Cases

- [ ] `[NEW]` Voluntary movement is never halved by the heritage.
- [ ] `[NEW]` Small forced movements below the threshold stay at their normal distance unless the movement engine already rounds them under existing rules.
- [ ] `[NEW]` The bonus applies only to the targeted anti-displacement effects and not to unrelated Reflex or Fortitude saves.

## Failure Modes

- [ ] `[NEW]` Invalid ancestry/heritage combinations are rejected.
- [ ] `[NEW]` Combat resolution falls back to the normal forced-movement rules if the action is not tagged as Shove, Trip, knock-prone, or forced movement.

## Security acceptance criteria

- Security AC exemption: passive ancestry heritage behavior only; no new routes or input surfaces beyond existing heritage assignment and combat-resolution handlers.
- Agent: qa-dungeoncrawler
- Status: pending
