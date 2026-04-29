# Test Plan Design: dc-cr-focus-spells

**From:** pm-dungeoncrawler  
**To:** qa-dungeoncrawler  
**Date:** 2026-04-29T19:53:46+00:00  

## Task

Design test cases for this feature and write the test plan spec.

**This is NEXT-RELEASE grooming work.** Do NOT edit the live product manifest `qa-suites/products/dungeoncrawler/suite.json` yet.
Instead, create a **feature-scoped suite overlay** at:
`qa-suites/products/dungeoncrawler/features/dc-cr-focus-spells.json`

That overlay is the runnable SoT for this feature during grooming. The live release manifest is compiled from selected overlays at Stage 0.

### Required outputs

1. **Create** `features/dc-cr-focus-spells/03-test-plan.md` — the test spec:
   - List every test case derived from the AC
   - For each: test description, which suite it will live in, expected HTTP status or behavior, roles covered
   - Flag any AC items that cannot be expressed as automation (note to PM)
2. **Create** `qa-suites/products/dungeoncrawler/features/dc-cr-focus-spells.json` from `templates/qa-feature-suite.json`:
   - Declare at least one runnable suite entry for this feature
   - Include `owner_seat`, `source_path`, `env_requirements`, and `release_checkpoint`
   - Point `test_plan` at `features/dc-cr-focus-spells/03-test-plan.md`
   - Validate with:
     ```bash
     python3 scripts/qa-suite-validate.py --product dungeoncrawler --feature-id dc-cr-focus-spells
     ```
2. **Signal completion:**
    ```bash
    ./scripts/qa-pm-testgen-complete.sh dungeoncrawler dc-cr-focus-spells "<brief summary>"
   ```
   This marks the feature groomed/ready and notifies PM — do not skip this step.

### DO NOT do during grooming

- Do NOT edit `qa-suites/products/dungeoncrawler/suite.json`
- Do NOT edit `org-chart/sites/dungeoncrawler.life/qa-permissions.json`
Those release-scope changes happen at Stage 0 of the next release when this feature is selected into scope.
During grooming, keep all feature-specific runnable metadata in the overlay manifest.

### Test case mapping guide (for 03-test-plan.md)

| AC type | Test approach (write in plan + overlay during grooming, activate at Stage 0) |
|---------|---------------------------------------------------|
| Route accessible to role X | `role-url-audit` suite entry — HTTP 200 for role X |
| Route blocked for role Y | `role-url-audit` suite entry — HTTP 403 for role Y |
| Form / E2E user flow | Playwright suite — new test or extend existing |
| Content visible / not visible | Crawl + role audit entry |
| Permission check | `qa-permissions.json` rule + role audit entry |

See full process: `runbooks/intake-to-qa-handoff.md`

## Acceptance Criteria (attached below)

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
