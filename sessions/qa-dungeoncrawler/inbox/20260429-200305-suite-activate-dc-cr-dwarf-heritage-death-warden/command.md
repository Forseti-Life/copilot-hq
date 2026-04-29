# Suite Activation: dc-cr-dwarf-heritage-death-warden

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
   **CRITICAL: tag every new entry with `"feature_id": "dc-cr-dwarf-heritage-death-warden"`**  
   This links the test to the living requirements doc at `features/dc-cr-dwarf-heritage-death-warden/`.  
   Dev reads this field to know: failing test = new feature to implement, not a regression.  
   Minimum suite entry structure:
   ```json
   {
     "id": "dc-cr-dwarf-heritage-death-warden-e2e",
     "label": "<describe what the test verifies>",
     "type": "e2e",
     "feature_id": "dc-cr-dwarf-heritage-death-warden",
     "command": "<playwright or test command>",
     "artifacts": ["<report path>"],
     "required_for_release": true
   }
   ```

2. **Add permission rules to** `org-chart/sites/dungeoncrawler.life/qa-permissions.json`  
   For any new routes/ACL expectations.  
   **CRITICAL: tag every new rule with `"feature_id": "dc-cr-dwarf-heritage-death-warden"`**  
   Example:
   ```json
   {
     "id": "dc-cr-dwarf-heritage-death-warden-<route-slug>",
     "feature_id": "dc-cr-dwarf-heritage-death-warden",
     "path_regex": "/your-new-route",
     "notes": "Added for feature dc-cr-dwarf-heritage-death-warden",
     "expect": { "anon": "...", "authenticated": "..." }
   }
   ```

3. **Validate the suite:**
   ```bash
   python3 scripts/qa-suite-validate.py
   ```

4. **Write outbox** confirming: how many entries added, feature_id tagged on each, suite validated, any gaps flagged.

### Test plan (written during grooming)

# Test Plan: dc-cr-dwarf-heritage-death-warden

## Coverage summary
- AC items: 9 (4 happy path, 3 edge cases, 2 failure modes)
- Test cases: 5 (TC-DWD-01-05)
- Suites: playwright (character creation, save resolution, combat log)
- Security: Security AC exemption: passive ancestry heritage behavior only; no new routes or input surfaces beyond existing heritage assignment and combat-resolution handlers.

---

## TC-DWD-01 — Heritage availability and ancestry gating
- Description: The Death Warden heritage exists as a selectable dwarf heritage and is unavailable to non-dwarf ancestries.
- Suite: playwright/character-creation
- Expected: The Death Warden heritage exists as a selectable dwarf heritage and is unavailable to non-dwarf ancestries.
- AC: Happy Path-1

## TC-DWD-02 — Primary passive effect application
- Description: When a Death Warden dwarf succeeds on a saving throw against a necromancy effect, the final result is upgraded to a critical success.
- Suite: playwright/encounter
- Expected: When a Death Warden dwarf succeeds on a saving throw against a necromancy effect, the final result is upgraded to a critical success.; Necromancy critical successes remain critical successes rather than being double-upgraded or otherwise altered.
- AC: Happy Path-2, Happy Path-3

## TC-DWD-03 — Scaling, automation, and visible state updates
- Description: Necromancy critical successes remain critical successes rather than being double-upgraded or otherwise altered.
- Suite: playwright/encounter
- Expected: Necromancy critical successes remain critical successes rather than being double-upgraded or otherwise altered.; The heritage effect is passive and automatic; no extra player action or toggle is required during save resolution.
- AC: Happy Path-3, Happy Path-4

## TC-DWD-04 — Edge-case rules interaction coverage
- Description: The save upgrade only applies to effects tagged as necromancy and does not modify non-necromancy saves.
- Suite: playwright/encounter
- Expected: The save upgrade only applies to effects tagged as necromancy and does not modify non-necromancy saves.; Characters can hold only one dwarf heritage at a time, so Death Warden cannot stack with another dwarf heritage bonus.; Save logs or combat resolution output clearly show the upgraded outcome for QA traceability.
- AC: Edge Cases-1, Edge Cases-2, Edge Cases-3

## TC-DWD-05 — Validation errors and safe fallback behavior
- Description: Invalid heritage selection for the wrong ancestry is rejected rather than persisted.
- Suite: playwright/encounter
- Expected: Invalid heritage selection for the wrong ancestry is rejected rather than persisted.; If an effect lacks the necromancy tag, the save resolver falls back to the baseline success result without throwing an error.
- AC: Failure Modes-1, Failure Modes-2

### Acceptance criteria (reference)

# Acceptance Criteria — dc-cr-dwarf-heritage-death-warden

- Feature: Dwarf Heritage — Death Warden
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Turn the Death Warden dwarf heritage into a testable contract covering heritage availability, necromancy save upgrades, and the boundaries of the passive so it can be implemented inside the save-resolution pipeline.

## Dependency checkpoints

- Depends on: dc-cr-dwarf-ancestry, dc-cr-heritage-system
- Merged into: dc-cr-dwarf-ancestry (all heritages and ancestry feats covered in bulk AC)

## Happy Path

- [ ] `[NEW]` The Death Warden heritage exists as a selectable dwarf heritage and is unavailable to non-dwarf ancestries.
- [ ] `[NEW]` When a Death Warden dwarf succeeds on a saving throw against a necromancy effect, the final result is upgraded to a critical success.
- [ ] `[NEW]` Necromancy critical successes remain critical successes rather than being double-upgraded or otherwise altered.
- [ ] `[NEW]` The heritage effect is passive and automatic; no extra player action or toggle is required during save resolution.

## Edge Cases

- [ ] `[NEW]` The save upgrade only applies to effects tagged as necromancy and does not modify non-necromancy saves.
- [ ] `[NEW]` Characters can hold only one dwarf heritage at a time, so Death Warden cannot stack with another dwarf heritage bonus.
- [ ] `[NEW]` Save logs or combat resolution output clearly show the upgraded outcome for QA traceability.

## Failure Modes

- [ ] `[NEW]` Invalid heritage selection for the wrong ancestry is rejected rather than persisted.
- [ ] `[NEW]` If an effect lacks the necromancy tag, the save resolver falls back to the baseline success result without throwing an error.

## Security acceptance criteria

- Security AC exemption: passive ancestry heritage behavior only; no new routes or input surfaces beyond existing heritage assignment and combat-resolution handlers.
