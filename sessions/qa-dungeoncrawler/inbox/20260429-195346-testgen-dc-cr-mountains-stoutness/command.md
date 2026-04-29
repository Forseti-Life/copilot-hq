# Test Plan Design: dc-cr-mountains-stoutness

**From:** pm-dungeoncrawler  
**To:** qa-dungeoncrawler  
**Date:** 2026-04-29T19:53:46+00:00  

## Task

Design test cases for this feature and write the test plan spec.

**This is NEXT-RELEASE grooming work.** Do NOT edit the live product manifest `qa-suites/products/dungeoncrawler/suite.json` yet.
Instead, create a **feature-scoped suite overlay** at:
`qa-suites/products/dungeoncrawler/features/dc-cr-mountains-stoutness.json`

That overlay is the runnable SoT for this feature during grooming. The live release manifest is compiled from selected overlays at Stage 0.

### Required outputs

1. **Create** `features/dc-cr-mountains-stoutness/03-test-plan.md` — the test spec:
   - List every test case derived from the AC
   - For each: test description, which suite it will live in, expected HTTP status or behavior, roles covered
   - Flag any AC items that cannot be expressed as automation (note to PM)
2. **Create** `qa-suites/products/dungeoncrawler/features/dc-cr-mountains-stoutness.json` from `templates/qa-feature-suite.json`:
   - Declare at least one runnable suite entry for this feature
   - Include `owner_seat`, `source_path`, `env_requirements`, and `release_checkpoint`
   - Point `test_plan` at `features/dc-cr-mountains-stoutness/03-test-plan.md`
   - Validate with:
     ```bash
     python3 scripts/qa-suite-validate.py --product dungeoncrawler --feature-id dc-cr-mountains-stoutness
     ```
2. **Signal completion:**
    ```bash
    ./scripts/qa-pm-testgen-complete.sh dungeoncrawler dc-cr-mountains-stoutness "<brief summary>"
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

# Acceptance Criteria — dc-cr-mountains-stoutness

- Feature: Mountain's Stoutness (Dwarf Ancestry Feat)
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Turn Mountain's Stoutness into a QA-ready level-9 ancestry-feat contract for the added max HP, modified recovery-check DC, and Toughness stacking interaction.

## Dependency checkpoints

- Depends on: dc-cr-dwarf-ancestry, dc-cr-ancestry-feat-schedule, dc-cr-character-leveling, dc-cr-conditions
- Merged into: dc-cr-dwarf-ancestry (all heritages and ancestry feats covered in bulk AC)

## Happy Path

- [ ] `[NEW]` Mountain's Stoutness exists as a level-9 dwarf ancestry feat.
- [ ] `[NEW]` Selecting the feat adds the character's current level to maximum Hit Points.
- [ ] `[NEW]` While dying, the recovery-check DC becomes `9 + dying_value` instead of the baseline `10 + dying_value`.
- [ ] `[NEW]` If the character also has Toughness, the HP bonuses stack and the recovery-check DC becomes `6 + dying_value`.

## Edge Cases

- [ ] `[NEW]` Level changes recalculate the added max HP automatically.
- [ ] `[NEW]` Characters without Toughness still receive the Mountain's Stoutness recovery-check adjustment without any extra flags.
- [ ] `[NEW]` Retraining or removing the feat restores the baseline HP and recovery-check formulas.

## Failure Modes

- [ ] `[NEW]` Selecting the feat below level 9 or without a valid dwarf ancestry slot is rejected.
- [ ] `[NEW]` The feat never changes unrelated death-and-dying rules beyond the documented recovery-check DC adjustment.

## Security acceptance criteria

- Security AC exemption: ancestry-feat and character-state math scope only; no new routes or input surfaces beyond existing feat assignment and dying-state handlers.
- Agent: qa-dungeoncrawler
- Status: pending
