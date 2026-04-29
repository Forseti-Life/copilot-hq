# Suite Activation: dc-cr-focus-spells

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
   **CRITICAL: tag every new entry with `"feature_id": "dc-cr-focus-spells"`**  
   This links the test to the living requirements doc at `features/dc-cr-focus-spells/`.  
   Dev reads this field to know: failing test = new feature to implement, not a regression.  
   Minimum suite entry structure:
   ```json
   {
     "id": "dc-cr-focus-spells-e2e",
     "label": "<describe what the test verifies>",
     "type": "e2e",
     "feature_id": "dc-cr-focus-spells",
     "command": "<playwright or test command>",
     "artifacts": ["<report path>"],
     "required_for_release": true
   }
   ```

2. **Add permission rules to** `org-chart/sites/dungeoncrawler.life/qa-permissions.json`  
   For any new routes/ACL expectations.  
   **CRITICAL: tag every new rule with `"feature_id": "dc-cr-focus-spells"`**  
   Example:
   ```json
   {
     "id": "dc-cr-focus-spells-<route-slug>",
     "feature_id": "dc-cr-focus-spells",
     "path_regex": "/your-new-route",
     "notes": "Added for feature dc-cr-focus-spells",
     "expect": { "anon": "...", "authenticated": "..." }
   }
   ```

3. **Validate the suite:**
   ```bash
   python3 scripts/qa-suite-validate.py
   ```

4. **Write outbox** confirming: how many entries added, feature_id tagged on each, suite validated, any gaps flagged.

### Test plan (written during grooming)

# Test Plan: dc-cr-focus-spells

## Coverage summary
- AC items: 9 (4 happy path, 3 edge cases, 2 failure modes)
- Test cases: 5 (TC-FCS-01-05)
- Suites: playwright (character build, spellcasting, rest/refocus)
- Security: Security AC exemption: spellcasting rules and character-state scope only; no new public routes expected beyond existing spellcasting and rest/action handlers.

---

## TC-FCS-01 — Feature availability and subsystem entry points
- Description: Classes and archetypes that grant focus spells also grant a focus-point pool and known focus-spell entries in character state.
- Suite: playwright/character-creation
- Expected: Classes and archetypes that grant focus spells also grant a focus-point pool and known focus-spell entries in character state.
- AC: Happy Path-1

## TC-FCS-02 — Primary subsystem rule resolution
- Description: Casting a focus spell consumes focus points instead of spell slots.
- Suite: playwright/encounter
- Expected: Casting a focus spell consumes focus points instead of spell slots.; Focus-point pools never exceed the rules cap of 3.
- AC: Happy Path-2, Happy Path-3

## TC-FCS-03 — State recovery, caps, or long-running flow handling
- Description: Focus-point pools never exceed the rules cap of 3.
- Suite: playwright/rest
- Expected: Focus-point pools never exceed the rules cap of 3.; A valid Refocus action after 10 minutes restores focus points according to the feature scope, and focus spells auto-heighten to the highest spell level the character can cast.
- AC: Happy Path-3, Happy Path-4

## TC-FCS-04 — Edge-case subsystem coverage
- Description: Characters with no focus pool never see focus-spell casting options.
- Suite: playwright/encounter
- Expected: Characters with no focus pool never see focus-spell casting options.; A character at 0 focus points cannot cast another focus spell until points are restored.; Multiple sources of focus spells share the same capped pool instead of creating separate point trackers.
- AC: Edge Cases-1, Edge Cases-2, Edge Cases-3

## TC-FCS-05 — Validation errors and wrong-surface rejection handling
- Description: Attempting to cast an unknown focus spell or Refocus when prerequisites are not met returns a validation error rather than silently failing.
- Suite: playwright/encounter
- Expected: Attempting to cast an unknown focus spell or Refocus when prerequisites are not met returns a validation error rather than silently failing.; Focus-spell casts do not consume standard spell slots or prepared-spell uses by mistake.
- AC: Failure Modes-1, Failure Modes-2

### Acceptance criteria (reference)

# Acceptance Criteria — dc-cr-focus-spells

- Feature: Focus Spells
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Capture the focus-spell subsystem as a handoff-ready contract covering focus pools, focus-point consumption, Refocus recovery, and auto-heightening so QA can drive implementation across classes and archetypes.

## Dependency checkpoints

- Consolidated into: dc-cr-spells-ch07 (requirements covered in that feature's acceptance criteria)

## Happy Path

- [ ] `[NEW]` Classes and archetypes that grant focus spells also grant a focus-point pool and known focus-spell entries in character state.
- [ ] `[NEW]` Casting a focus spell consumes focus points instead of spell slots.
- [ ] `[NEW]` Focus-point pools never exceed the rules cap of 3.
- [ ] `[NEW]` A valid Refocus action after 10 minutes restores focus points according to the feature scope, and focus spells auto-heighten to the highest spell level the character can cast.

## Edge Cases

- [ ] `[NEW]` Characters with no focus pool never see focus-spell casting options.
- [ ] `[NEW]` A character at 0 focus points cannot cast another focus spell until points are restored.
- [ ] `[NEW]` Multiple sources of focus spells share the same capped pool instead of creating separate point trackers.

## Failure Modes

- [ ] `[NEW]` Attempting to cast an unknown focus spell or Refocus when prerequisites are not met returns a validation error rather than silently failing.
- [ ] `[NEW]` Focus-spell casts do not consume standard spell slots or prepared-spell uses by mistake.

## Security acceptance criteria

- Security AC exemption: spellcasting rules and character-state scope only; no new public routes expected beyond existing spellcasting and rest/action handlers.
- Agent: qa-dungeoncrawler
- Status: pending
