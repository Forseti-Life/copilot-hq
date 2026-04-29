# Test Plan Design: dc-cr-rock-runner

**From:** pm-dungeoncrawler  
**To:** qa-dungeoncrawler  
**Date:** 2026-04-29T19:53:46+00:00  

## Task

Design test cases for this feature and write the test plan spec.

**This is NEXT-RELEASE grooming work.** Do NOT edit the live product manifest `qa-suites/products/dungeoncrawler/suite.json` yet.
Instead, create a **feature-scoped suite overlay** at:
`qa-suites/products/dungeoncrawler/features/dc-cr-rock-runner.json`

That overlay is the runnable SoT for this feature during grooming. The live release manifest is compiled from selected overlays at Stage 0.

### Required outputs

1. **Create** `features/dc-cr-rock-runner/03-test-plan.md` — the test spec:
   - List every test case derived from the AC
   - For each: test description, which suite it will live in, expected HTTP status or behavior, roles covered
   - Flag any AC items that cannot be expressed as automation (note to PM)
2. **Create** `qa-suites/products/dungeoncrawler/features/dc-cr-rock-runner.json` from `templates/qa-feature-suite.json`:
   - Declare at least one runnable suite entry for this feature
   - Include `owner_seat`, `source_path`, `env_requirements`, and `release_checkpoint`
   - Point `test_plan` at `features/dc-cr-rock-runner/03-test-plan.md`
   - Validate with:
     ```bash
     python3 scripts/qa-suite-validate.py --product dungeoncrawler --feature-id dc-cr-rock-runner
     ```
2. **Signal completion:**
    ```bash
    ./scripts/qa-pm-testgen-complete.sh dungeoncrawler dc-cr-rock-runner "<brief summary>"
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
