- Status: done
- Completed: 2026-04-29T22:43:23Z

# Suite Activation: dc-cr-vengeful-hatred

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
   **CRITICAL: tag every new entry with `"feature_id": "dc-cr-vengeful-hatred"`**  
   This links the test to the living requirements doc at `features/dc-cr-vengeful-hatred/`.  
   Dev reads this field to know: failing test = new feature to implement, not a regression.  
   Minimum suite entry structure:
   ```json
   {
     "id": "dc-cr-vengeful-hatred-e2e",
     "label": "<describe what the test verifies>",
     "type": "e2e",
     "feature_id": "dc-cr-vengeful-hatred",
     "command": "<playwright or test command>",
     "artifacts": ["<report path>"],
     "required_for_release": true
   }
   ```

2. **Add permission rules to** `org-chart/sites/dungeoncrawler.life/qa-permissions.json`  
   For any new routes/ACL expectations.  
   **CRITICAL: tag every new rule with `"feature_id": "dc-cr-vengeful-hatred"`**  
   Example:
   ```json
   {
     "id": "dc-cr-vengeful-hatred-<route-slug>",
     "feature_id": "dc-cr-vengeful-hatred",
     "path_regex": "/your-new-route",
     "notes": "Added for feature dc-cr-vengeful-hatred",
     "expect": { "anon": "...", "authenticated": "..." }
   }
   ```

3. **Validate the suite:**
   ```bash
   python3 scripts/qa-suite-validate.py
   ```

4. **Write outbox** confirming: how many entries added, feature_id tagged on each, suite validated, any gaps flagged.

### Test plan (written during grooming)

# Test Plan: dc-cr-vengeful-hatred

## Coverage summary
- AC items: 9 (4 happy path, 3 edge cases, 2 failure modes)
- Test cases: 5 (TC-VHT-01-05)
- Suites: playwright (feat progression, encounter damage, duration tracking)
- Security: Security AC exemption: ancestry-feat and combat-modifier scope only; no new routes or input surfaces beyond existing feat assignment and combat-resolution handlers.

---

## TC-VHT-01 — Feat availability and prerequisite gating
- Description: Vengeful Hatred exists as a level-1 dwarf ancestry feat and prompts the player to choose one ancestral foe type from drow, duergar, giant, or orc.
- Suite: playwright/feat-progression
- Expected: Vengeful Hatred exists as a level-1 dwarf ancestry feat and prompts the player to choose one ancestral foe type from drow, duergar, giant, or orc.
- AC: Happy Path-1

## TC-VHT-02 — Primary granted benefit application
- Description: The chosen foe type grants a +1 circumstance bonus to weapon and unarmed damage against that foe, scaling by the number of weapon damage dice at higher levels.
- Suite: playwright/encounter
- Expected: The chosen foe type grants a +1 circumstance bonus to weapon and unarmed damage against that foe, scaling by the number of weapon damage dice at higher levels.; If a creature critically hits the character and deals damage, the character gains the same damage bonus against that specific creature for 1 minute even if it is not the chosen ancestral foe type.
- AC: Happy Path-2, Happy Path-3

## TC-VHT-03 — Recalculation, retraining, and later progression behavior
- Description: If a creature critically hits the character and deals damage, the character gains the same damage bonus against that specific creature for 1 minute even if it is not the chosen ancestral foe type.
- Suite: playwright/encounter
- Expected: If a creature critically hits the character and deals damage, the character gains the same damage bonus against that specific creature for 1 minute even if it is not the chosen ancestral foe type.; The chosen foe type and any active temporary retaliation target are visible in character/combat state for QA verification.
- AC: Happy Path-3, Happy Path-4

## TC-VHT-04 — Edge-case rules interaction coverage
- Description: Changing the chosen ancestral foe requires a retrain/rebuild flow rather than an in-combat toggle.
- Suite: playwright/encounter
- Expected: Changing the chosen ancestral foe requires a retrain/rebuild flow rather than an in-combat toggle.; Damage scaling updates when the weapon's number of damage dice increases.; The temporary retaliation bonus expires after 1 minute and does not persist between encounters unless the timer is refreshed by another triggering critical hit.
- AC: Edge Cases-1, Edge Cases-2, Edge Cases-3

## TC-VHT-05 — Validation errors and malformed-data handling
- Description: Invalid ancestral foe choices are rejected during feat selection.
- Suite: playwright/encounter
- Expected: Invalid ancestral foe choices are rejected during feat selection.; A critical hit that deals no damage does not grant the temporary retaliation bonus.
- AC: Failure Modes-1, Failure Modes-2

### Acceptance criteria (reference)

# Acceptance Criteria — dc-cr-vengeful-hatred

- Feature: Vengeful Hatred (Dwarf Ancestry Feat)
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Define Vengeful Hatred as a QA-ready level-1 dwarf ancestry-feat contract for ancestry-foe selection, damage-bonus scaling by weapon dice, and the temporary retaliation bonus after taking a critical hit.

## Dependency checkpoints

- Depends on: dc-cr-dwarf-ancestry, dc-cr-ancestry-feat-schedule, dc-cr-ancestry-traits
- Merged into: dc-cr-dwarf-ancestry (all heritages and ancestry feats covered in bulk AC)

## Happy Path

- [ ] `[NEW]` Vengeful Hatred exists as a level-1 dwarf ancestry feat and prompts the player to choose one ancestral foe type from drow, duergar, giant, or orc.
- [ ] `[NEW]` The chosen foe type grants a +1 circumstance bonus to weapon and unarmed damage against that foe, scaling by the number of weapon damage dice at higher levels.
- [ ] `[NEW]` If a creature critically hits the character and deals damage, the character gains the same damage bonus against that specific creature for 1 minute even if it is not the chosen ancestral foe type.
- [ ] `[NEW]` The chosen foe type and any active temporary retaliation target are visible in character/combat state for QA verification.

## Edge Cases

- [ ] `[NEW]` Changing the chosen ancestral foe requires a retrain/rebuild flow rather than an in-combat toggle.
- [ ] `[NEW]` Damage scaling updates when the weapon's number of damage dice increases.
- [ ] `[NEW]` The temporary retaliation bonus expires after 1 minute and does not persist between encounters unless the timer is refreshed by another triggering critical hit.

## Failure Modes

- [ ] `[NEW]` Invalid ancestral foe choices are rejected during feat selection.
- [ ] `[NEW]` A critical hit that deals no damage does not grant the temporary retaliation bonus.

## Security acceptance criteria

- Security AC exemption: ancestry-feat and combat-modifier scope only; no new routes or input surfaces beyond existing feat assignment and combat-resolution handlers.
- Agent: qa-dungeoncrawler
- Status: pending
