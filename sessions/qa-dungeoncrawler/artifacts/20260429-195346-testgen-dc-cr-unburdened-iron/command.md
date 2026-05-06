- Status: done
- Completed: 2026-04-29T21:51:40Z

# Test Plan Design: dc-cr-unburdened-iron

**From:** pm-dungeoncrawler  
**To:** qa-dungeoncrawler  
**Date:** 2026-04-29T19:53:46+00:00  

## Task

Design test cases for this feature and write the test plan spec.

**This is NEXT-RELEASE grooming work.** Do NOT edit the live product manifest `qa-suites/products/dungeoncrawler/suite.json` yet.
Instead, create a **feature-scoped suite overlay** at:
`qa-suites/products/dungeoncrawler/features/dc-cr-unburdened-iron.json`

That overlay is the runnable SoT for this feature during grooming. The live release manifest is compiled from selected overlays at Stage 0.

### Required outputs

1. **Create** `features/dc-cr-unburdened-iron/03-test-plan.md` — the test spec:
   - List every test case derived from the AC
   - For each: test description, which suite it will live in, expected HTTP status or behavior, roles covered
   - Flag any AC items that cannot be expressed as automation (note to PM)
2. **Create** `qa-suites/products/dungeoncrawler/features/dc-cr-unburdened-iron.json` from `templates/qa-feature-suite.json`:
   - Declare at least one runnable suite entry for this feature
   - Include `owner_seat`, `source_path`, `env_requirements`, and `release_checkpoint`
   - Point `test_plan` at `features/dc-cr-unburdened-iron/03-test-plan.md`
   - Validate with:
     ```bash
     python3 scripts/qa-suite-validate.py --product dungeoncrawler --feature-id dc-cr-unburdened-iron
     ```
2. **Signal completion:**
    ```bash
    ./scripts/qa-pm-testgen-complete.sh dungeoncrawler dc-cr-unburdened-iron "<brief summary>"
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

# Acceptance Criteria — dc-cr-unburdened-iron

- Feature: Unburdened Iron (Dwarf Ancestry Feat)
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Turn Unburdened Iron into a QA-ready level-1 ancestry-feat contract for armor speed-penalty removal and the single-largest-other-penalty reduction rule.

## Dependency checkpoints

- Depends on: dc-cr-dwarf-ancestry, dc-cr-ancestry-feat-schedule, dc-cr-equipment-system
- Merged into: dc-cr-dwarf-ancestry (all heritages and ancestry feats covered in bulk AC)

## Happy Path

- [ ] `[NEW]` Unburdened Iron exists as a level-1 dwarf ancestry feat.
- [ ] `[NEW]` Worn armor no longer applies its Speed penalty to a character with the feat.
- [ ] `[NEW]` The largest single other Speed penalty affecting the character is reduced by 5 feet.
- [ ] `[NEW]` Speed calculations remain deterministic when armor penalties and other penalties are combined.

## Edge Cases

- [ ] `[NEW]` Only the largest non-armor penalty is reduced; multiple non-armor penalties are not each reduced by 5 feet.
- [ ] `[NEW]` A character with no armor equipped still receives the largest-other-penalty reduction if one exists.
- [ ] `[NEW]` Speed can never become negative as a result of this adjustment logic.

## Failure Modes

- [ ] `[NEW]` Selecting the feat without a valid dwarf ancestry slot is rejected.
- [ ] `[NEW]` Malformed speed modifiers do not crash movement calculations; they surface a validation issue instead.

## Security acceptance criteria

- Security AC exemption: ancestry-feat and movement-math scope only; no new routes or input surfaces beyond existing feat assignment and movement handlers.
- Agent: qa-dungeoncrawler
- Status: pending
