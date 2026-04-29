# Test Plan Design: dc-cr-dwarf-heritage-rock

**From:** pm-dungeoncrawler  
**To:** qa-dungeoncrawler  
**Date:** 2026-04-29T19:53:45+00:00  

## Task

Design test cases for this feature and write the test plan spec.

**This is NEXT-RELEASE grooming work.** Do NOT edit the live product manifest `qa-suites/products/dungeoncrawler/suite.json` yet.
Instead, create a **feature-scoped suite overlay** at:
`qa-suites/products/dungeoncrawler/features/dc-cr-dwarf-heritage-rock.json`

That overlay is the runnable SoT for this feature during grooming. The live release manifest is compiled from selected overlays at Stage 0.

### Required outputs

1. **Create** `features/dc-cr-dwarf-heritage-rock/03-test-plan.md` — the test spec:
   - List every test case derived from the AC
   - For each: test description, which suite it will live in, expected HTTP status or behavior, roles covered
   - Flag any AC items that cannot be expressed as automation (note to PM)
2. **Create** `qa-suites/products/dungeoncrawler/features/dc-cr-dwarf-heritage-rock.json` from `templates/qa-feature-suite.json`:
   - Declare at least one runnable suite entry for this feature
   - Include `owner_seat`, `source_path`, `env_requirements`, and `release_checkpoint`
   - Point `test_plan` at `features/dc-cr-dwarf-heritage-rock/03-test-plan.md`
   - Validate with:
     ```bash
     python3 scripts/qa-suite-validate.py --product dungeoncrawler --feature-id dc-cr-dwarf-heritage-rock
     ```
2. **Signal completion:**
    ```bash
    ./scripts/qa-pm-testgen-complete.sh dungeoncrawler dc-cr-dwarf-heritage-rock "<brief summary>"
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

# Acceptance Criteria — dc-cr-dwarf-heritage-rock

- Feature: Dwarf Heritage — Rock Dwarf
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Capture Rock Dwarf as a heritage contract for anti-displacement combat rules, including the defense bonus against Shove/Trip/knock-prone effects and the forced-movement reduction behavior.

## Dependency checkpoints

- Depends on: dc-cr-dwarf-ancestry, dc-cr-heritage-system
- Merged into: dc-cr-dwarf-ancestry (all heritages and ancestry feats covered in bulk AC)

## Happy Path

- [ ] `[NEW]` Rock Dwarf is selectable only for dwarf characters within the heritage system.
- [ ] `[NEW]` The heritage grants a +2 circumstance bonus to the relevant Fortitude or Reflex DC / save checks against Shove, Trip, and knock-prone effects.
- [ ] `[NEW]` Forced movement affecting the character is reduced by half when the pushed or pulled distance is 10 feet or more.
- [ ] `[NEW]` The passive applies automatically during maneuver resolution without any manual toggle.

## Edge Cases

- [ ] `[NEW]` Voluntary movement is never halved by the heritage.
- [ ] `[NEW]` Small forced movements below the threshold stay at their normal distance unless the movement engine already rounds them under existing rules.
- [ ] `[NEW]` The bonus applies only to the targeted anti-displacement effects and not to unrelated Reflex or Fortitude saves.

## Failure Modes

- [ ] `[NEW]` Invalid ancestry/heritage combinations are rejected.
- [ ] `[NEW]` Combat resolution falls back to the normal forced-movement rules if the action is not tagged as Shove, Trip, knock-prone, or forced movement.

## Security acceptance criteria

- Security AC exemption: passive ancestry heritage behavior only; no new routes or input surfaces beyond existing heritage assignment and combat-resolution handlers.
