# Suite Activation: dc-cr-general-feats

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
   **CRITICAL: tag every new entry with `"feature_id": "dc-cr-general-feats"`**  
   This links the test to the living requirements doc at `features/dc-cr-general-feats/`.  
   Dev reads this field to know: failing test = new feature to implement, not a regression.  
   Minimum suite entry structure:
   ```json
   {
     "id": "dc-cr-general-feats-e2e",
     "label": "<describe what the test verifies>",
     "type": "e2e",
     "feature_id": "dc-cr-general-feats",
     "command": "<playwright or test command>",
     "artifacts": ["<report path>"],
     "required_for_release": true
   }
   ```

2. **Add permission rules to** `org-chart/sites/dungeoncrawler.life/qa-permissions.json`  
   For any new routes/ACL expectations.  
   **CRITICAL: tag every new rule with `"feature_id": "dc-cr-general-feats"`**  
   Example:
   ```json
   {
     "id": "dc-cr-general-feats-<route-slug>",
     "feature_id": "dc-cr-general-feats",
     "path_regex": "/your-new-route",
     "notes": "Added for feature dc-cr-general-feats",
     "expect": { "anon": "...", "authenticated": "..." }
   }
   ```

3. **Validate the suite:**
   ```bash
   python3 scripts/qa-suite-validate.py
   ```

4. **Write outbox** confirming: how many entries added, feature_id tagged on each, suite validated, any gaps flagged.

### Test plan (written during grooming)

# Test Plan: dc-cr-general-feats

## Coverage summary
- AC items: 9 (4 happy path, 3 edge cases, 2 failure modes)
- Test cases: 5 (TC-GFE-01-05)
- Suites: playwright (feat progression, build validation, retraining)
- Security: Security AC exemption: feat-catalog and character-build scope only; no new routes or input surfaces beyond existing feat assignment handlers.

---

## TC-GFE-01 — Milestone availability and slot gating
- Description: General feat slots open at levels 3, 7, 11, 15, and 19 and are distinct from class, ancestry, and skill feat slots.
- Suite: playwright/feat-progression
- Expected: General feat slots open at levels 3, 7, 11, 15, and 19 and are distinct from class, ancestry, and skill feat slots.
- AC: Happy Path-1

## TC-GFE-02 — Primary progression rule application
- Description: The general-feat catalog includes the chapter's core cross-class options (for example Armor Proficiency, Shield Block, Toughness, and Incredible Initiative) with the metadata needed for the picker.
- Suite: playwright/feat-progression
- Expected: The general-feat catalog includes the chapter's core cross-class options (for example Armor Proficiency, Shield Block, Toughness, and Incredible Initiative) with the metadata needed for the picker.; The feat picker only offers general feats whose prerequisites are satisfied by the current character build.
- AC: Happy Path-2, Happy Path-3

## TC-GFE-03 — Persistence and recalculation across level changes
- Description: The feat picker only offers general feats whose prerequisites are satisfied by the current character build.
- Suite: playwright/feat-progression
- Expected: The feat picker only offers general feats whose prerequisites are satisfied by the current character build.; Taking a general feat applies its listed modifier, action, or rules flag to the character state in a testable way.
- AC: Happy Path-3, Happy Path-4

## TC-GFE-04 — Edge-case rebuild and empty-option handling
- Description: A feat available from multiple sources is still tracked in the correct feat pool and not duplicated across slot types.
- Suite: playwright/feat-progression
- Expected: A feat available from multiple sources is still tracked in the correct feat pool and not duplicated across slot types.; Leveling without an eligible general feat choice leaves the slot open rather than auto-assigning an invalid feat.; Retraining recalculates downstream prerequisites for other feat selections.
- AC: Edge Cases-1, Edge Cases-2, Edge Cases-3

## TC-GFE-05 — Validation, ownership, and invalid input handling
- Description: General feats cannot be selected in ancestry-feat or class-feat slots.
- Suite: playwright/feat-progression
- Expected: General feats cannot be selected in ancestry-feat or class-feat slots.; Submitting a feat without meeting its prerequisites returns a validation error instead of corrupting the build.
- AC: Failure Modes-1, Failure Modes-2

### Acceptance criteria (reference)

# Acceptance Criteria — dc-cr-general-feats

- Feature: General Feats
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Define the general-feat backlog as a QA-ready contract for the level-based feat schedule, catalog visibility, prerequisite validation, and representative feat effects that apply across classes.

## Dependency checkpoints

- Consolidated into: dc-cr-feats-ch05 (requirements covered in that feature's acceptance criteria)

## Happy Path

- [ ] `[NEW]` General feat slots open at levels 3, 7, 11, 15, and 19 and are distinct from class, ancestry, and skill feat slots.
- [ ] `[NEW]` The general-feat catalog includes the chapter's core cross-class options (for example Armor Proficiency, Shield Block, Toughness, and Incredible Initiative) with the metadata needed for the picker.
- [ ] `[NEW]` The feat picker only offers general feats whose prerequisites are satisfied by the current character build.
- [ ] `[NEW]` Taking a general feat applies its listed modifier, action, or rules flag to the character state in a testable way.

## Edge Cases

- [ ] `[NEW]` A feat available from multiple sources is still tracked in the correct feat pool and not duplicated across slot types.
- [ ] `[NEW]` Leveling without an eligible general feat choice leaves the slot open rather than auto-assigning an invalid feat.
- [ ] `[NEW]` Retraining recalculates downstream prerequisites for other feat selections.

## Failure Modes

- [ ] `[NEW]` General feats cannot be selected in ancestry-feat or class-feat slots.
- [ ] `[NEW]` Submitting a feat without meeting its prerequisites returns a validation error instead of corrupting the build.

## Security acceptance criteria

- Security AC exemption: feat-catalog and character-build scope only; no new routes or input surfaces beyond existing feat assignment handlers.
- Agent: qa-dungeoncrawler
- Status: pending
