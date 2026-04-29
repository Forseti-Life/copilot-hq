# Suite Activation: dc-cr-dwarven-weapon-expertise

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
   **CRITICAL: tag every new entry with `"feature_id": "dc-cr-dwarven-weapon-expertise"`**  
   This links the test to the living requirements doc at `features/dc-cr-dwarven-weapon-expertise/`.  
   Dev reads this field to know: failing test = new feature to implement, not a regression.  
   Minimum suite entry structure:
   ```json
   {
     "id": "dc-cr-dwarven-weapon-expertise-e2e",
     "label": "<describe what the test verifies>",
     "type": "e2e",
     "feature_id": "dc-cr-dwarven-weapon-expertise",
     "command": "<playwright or test command>",
     "artifacts": ["<report path>"],
     "required_for_release": true
   }
   ```

2. **Add permission rules to** `org-chart/sites/dungeoncrawler.life/qa-permissions.json`  
   For any new routes/ACL expectations.  
   **CRITICAL: tag every new rule with `"feature_id": "dc-cr-dwarven-weapon-expertise"`**  
   Example:
   ```json
   {
     "id": "dc-cr-dwarven-weapon-expertise-<route-slug>",
     "feature_id": "dc-cr-dwarven-weapon-expertise",
     "path_regex": "/your-new-route",
     "notes": "Added for feature dc-cr-dwarven-weapon-expertise",
     "expect": { "anon": "...", "authenticated": "..." }
   }
   ```

3. **Validate the suite:**
   ```bash
   python3 scripts/qa-suite-validate.py
   ```

4. **Write outbox** confirming: how many entries added, feature_id tagged on each, suite validated, any gaps flagged.

### Test plan (written during grooming)

# Test Plan: dc-cr-dwarven-weapon-expertise

## Coverage summary
- AC items: 9 (4 happy path, 3 edge cases, 2 failure modes)
- Test cases: 5 (TC-DWE-01-05)
- Suites: playwright (feat progression, weapon proficiency, rebuild)
- Security: Security AC exemption: ancestry-feat and proficiency-calculation scope only; no new routes or input surfaces beyond existing feat assignment and character build handlers.

---

## TC-DWE-01 — Feat availability and prerequisite gating
- Description: The feat exists in the dwarf ancestry-feat catalog at level 13 with Dwarven Weapon Familiarity as a prerequisite.
- Suite: playwright/feat-progression
- Expected: The feat exists in the dwarf ancestry-feat catalog at level 13 with Dwarven Weapon Familiarity as a prerequisite.
- AC: Happy Path-1

## TC-DWE-02 — Primary granted benefit application
- Description: When the character gains a class feature that grants expert or higher weapon proficiency, that rank is copied to battle axes, picks, warhammers, and any trained dwarven weapons.
- Suite: playwright/inventory
- Expected: When the character gains a class feature that grants expert or higher weapon proficiency, that rank is copied to battle axes, picks, warhammers, and any trained dwarven weapons.; The upgrade uses the character's current trained dwarven-weapon set rather than granting expertise to unrelated weapon families.
- AC: Happy Path-2, Happy Path-3

## TC-DWE-03 — Recalculation, retraining, and later progression behavior
- Description: The upgrade uses the character's current trained dwarven-weapon set rather than granting expertise to unrelated weapon families.
- Suite: playwright/feat-progression
- Expected: The upgrade uses the character's current trained dwarven-weapon set rather than granting expertise to unrelated weapon families.; Rebuilds or later class-proficiency upgrades recalculate the dwarven-weapon expertise bonus correctly.
- AC: Happy Path-3, Happy Path-4

## TC-DWE-04 — Edge-case rules interaction coverage
- Description: Characters without the prerequisite feat cannot select Dwarven Weapon Expertise.
- Suite: playwright/feat-progression
- Expected: Characters without the prerequisite feat cannot select Dwarven Weapon Expertise.; If a weapon already has an equal or higher proficiency rank from another source, the feat does not downgrade or duplicate that rank.; New dwarven weapons learned later inherit the propagated proficiency if they satisfy the trained-weapon requirement.
- AC: Edge Cases-1, Edge Cases-2, Edge Cases-3

## TC-DWE-05 — Validation errors and malformed-data handling
- Description: Selecting the feat below level 13 or on a non-dwarf build fails validation.
- Suite: playwright/inventory
- Expected: Selecting the feat below level 13 or on a non-dwarf build fails validation.; Missing dwarven-weapon tags or malformed proficiency mappings do not crash the character sheet; they surface a validation defect instead.
- AC: Failure Modes-1, Failure Modes-2

### Acceptance criteria (reference)

# Acceptance Criteria — dc-cr-dwarven-weapon-expertise

- Feature: Dwarven Weapon Expertise
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Turn Dwarven Weapon Expertise into a testable ancestry-feat contract for high-level proficiency propagation onto battle axes, picks, warhammers, and other trained dwarven weapons.

## Dependency checkpoints

- Depends on: dc-cr-dwarf-ancestry, dc-cr-ancestry-feat-schedule, dc-cr-dwarven-weapon-familiarity, dc-cr-equipment-system

## Happy Path

- [ ] `[NEW]` The feat exists in the dwarf ancestry-feat catalog at level 13 with Dwarven Weapon Familiarity as a prerequisite.
- [ ] `[NEW]` When the character gains a class feature that grants expert or higher weapon proficiency, that rank is copied to battle axes, picks, warhammers, and any trained dwarven weapons.
- [ ] `[NEW]` The upgrade uses the character's current trained dwarven-weapon set rather than granting expertise to unrelated weapon families.
- [ ] `[NEW]` Rebuilds or later class-proficiency upgrades recalculate the dwarven-weapon expertise bonus correctly.

## Edge Cases

- [ ] `[NEW]` Characters without the prerequisite feat cannot select Dwarven Weapon Expertise.
- [ ] `[NEW]` If a weapon already has an equal or higher proficiency rank from another source, the feat does not downgrade or duplicate that rank.
- [ ] `[NEW]` New dwarven weapons learned later inherit the propagated proficiency if they satisfy the trained-weapon requirement.

## Failure Modes

- [ ] `[NEW]` Selecting the feat below level 13 or on a non-dwarf build fails validation.
- [ ] `[NEW]` Missing dwarven-weapon tags or malformed proficiency mappings do not crash the character sheet; they surface a validation defect instead.

## Security acceptance criteria

- Security AC exemption: ancestry-feat and proficiency-calculation scope only; no new routes or input surfaces beyond existing feat assignment and character build handlers.
