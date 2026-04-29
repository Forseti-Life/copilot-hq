# Suite Activation: dc-cr-dwarf-heritage-strong-blooded

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
   **CRITICAL: tag every new entry with `"feature_id": "dc-cr-dwarf-heritage-strong-blooded"`**  
   This links the test to the living requirements doc at `features/dc-cr-dwarf-heritage-strong-blooded/`.  
   Dev reads this field to know: failing test = new feature to implement, not a regression.  
   Minimum suite entry structure:
   ```json
   {
     "id": "dc-cr-dwarf-heritage-strong-blooded-e2e",
     "label": "<describe what the test verifies>",
     "type": "e2e",
     "feature_id": "dc-cr-dwarf-heritage-strong-blooded",
     "command": "<playwright or test command>",
     "artifacts": ["<report path>"],
     "required_for_release": true
   }
   ```

2. **Add permission rules to** `org-chart/sites/dungeoncrawler.life/qa-permissions.json`  
   For any new routes/ACL expectations.  
   **CRITICAL: tag every new rule with `"feature_id": "dc-cr-dwarf-heritage-strong-blooded"`**  
   Example:
   ```json
   {
     "id": "dc-cr-dwarf-heritage-strong-blooded-<route-slug>",
     "feature_id": "dc-cr-dwarf-heritage-strong-blooded",
     "path_regex": "/your-new-route",
     "notes": "Added for feature dc-cr-dwarf-heritage-strong-blooded",
     "expect": { "anon": "...", "authenticated": "..." }
   }
   ```

3. **Validate the suite:**
   ```bash
   python3 scripts/qa-suite-validate.py
   ```

4. **Write outbox** confirming: how many entries added, feature_id tagged on each, suite validated, any gaps flagged.

### Test plan (written during grooming)

# Test Plan: dc-cr-dwarf-heritage-strong-blooded

## Coverage summary
- AC items: 9 (4 happy path, 3 edge cases, 2 failure modes)
- Test cases: 5 (TC-DSB-01-05)
- Suites: playwright (character creation, afflictions, level scaling)
- Security: Security AC exemption: passive ancestry heritage behavior only; no new routes or input surfaces beyond existing heritage assignment and affliction-resolution handlers.

---

## TC-DSB-01 — Heritage availability and ancestry gating
- Description: Strong-Blooded is available as a dwarf-only heritage selection.
- Suite: playwright/character-creation
- Expected: Strong-Blooded is available as a dwarf-only heritage selection.
- AC: Happy Path-1

## TC-DSB-02 — Primary passive effect application
- Description: The heritage grants poison resistance equal to half the character level, minimum 1.
- Suite: playwright/encounter
- Expected: The heritage grants poison resistance equal to half the character level, minimum 1.; On a successful save against a poison affliction, the poison stage is reduced by 2, or by 1 if the poison is virulent.
- AC: Happy Path-2, Happy Path-3

## TC-DSB-03 — Scaling, automation, and visible state updates
- Description: On a successful save against a poison affliction, the poison stage is reduced by 2, or by 1 if the poison is virulent.
- Suite: playwright/encounter
- Expected: On a successful save against a poison affliction, the poison stage is reduced by 2, or by 1 if the poison is virulent.; On a critical success, the poison stage is reduced by 3, or by 2 if the poison is virulent.
- AC: Happy Path-3, Happy Path-4

## TC-DSB-04 — Edge-case rules interaction coverage
- Description: Level-up recalculates the poison-resistance value without requiring the heritage to be re-selected.
- Suite: playwright/encounter
- Expected: Level-up recalculates the poison-resistance value without requiring the heritage to be re-selected.; Non-poison afflictions such as disease do not receive the Strong-Blooded stage-reduction benefit.; Virulent-poison handling still uses the reduced stage-drop values rather than the standard success/critical-success drops.
- AC: Edge Cases-1, Edge Cases-2, Edge Cases-3

## TC-DSB-05 — Validation errors and safe fallback behavior
- Description: Selecting the heritage for a non-dwarf ancestry is rejected.
- Suite: playwright/encounter
- Expected: Selecting the heritage for a non-dwarf ancestry is rejected.; If the affliction is missing poison metadata, resolution falls back safely instead of applying the Strong-Blooded adjustment incorrectly.
- AC: Failure Modes-1, Failure Modes-2

### Acceptance criteria (reference)

# Acceptance Criteria — dc-cr-dwarf-heritage-strong-blooded

- Feature: Dwarf Heritage — Strong-Blooded Dwarf
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Define the Strong-Blooded dwarf heritage contract so poison resistance and poison-stage reduction rules can be validated in the affliction engine without ambiguity.

## Dependency checkpoints

- Depends on: dc-cr-dwarf-ancestry, dc-cr-heritage-system
- Merged into: dc-cr-dwarf-ancestry (all heritages and ancestry feats covered in bulk AC)

## Happy Path

- [ ] `[NEW]` Strong-Blooded is available as a dwarf-only heritage selection.
- [ ] `[NEW]` The heritage grants poison resistance equal to half the character level, minimum 1.
- [ ] `[NEW]` On a successful save against a poison affliction, the poison stage is reduced by 2, or by 1 if the poison is virulent.
- [ ] `[NEW]` On a critical success, the poison stage is reduced by 3, or by 2 if the poison is virulent.

## Edge Cases

- [ ] `[NEW]` Level-up recalculates the poison-resistance value without requiring the heritage to be re-selected.
- [ ] `[NEW]` Non-poison afflictions such as disease do not receive the Strong-Blooded stage-reduction benefit.
- [ ] `[NEW]` Virulent-poison handling still uses the reduced stage-drop values rather than the standard success/critical-success drops.

## Failure Modes

- [ ] `[NEW]` Selecting the heritage for a non-dwarf ancestry is rejected.
- [ ] `[NEW]` If the affliction is missing poison metadata, resolution falls back safely instead of applying the Strong-Blooded adjustment incorrectly.

## Security acceptance criteria

- Security AC exemption: passive ancestry heritage behavior only; no new routes or input surfaces beyond existing heritage assignment and affliction-resolution handlers.
- Agent: qa-dungeoncrawler
- Status: pending
