# Test Plan Design: dc-cr-dwarf-heritage-death-warden

**From:** pm-dungeoncrawler  
**To:** qa-dungeoncrawler  
**Date:** 2026-04-29T19:53:45+00:00  

## Task

Design test cases for this feature and write the test plan spec.

**This is NEXT-RELEASE grooming work.** Do NOT edit the live product manifest `qa-suites/products/dungeoncrawler/suite.json` yet.
Instead, create a **feature-scoped suite overlay** at:
`qa-suites/products/dungeoncrawler/features/dc-cr-dwarf-heritage-death-warden.json`

That overlay is the runnable SoT for this feature during grooming. The live release manifest is compiled from selected overlays at Stage 0.

### Required outputs

1. **Create** `features/dc-cr-dwarf-heritage-death-warden/03-test-plan.md` — the test spec:
   - List every test case derived from the AC
   - For each: test description, which suite it will live in, expected HTTP status or behavior, roles covered
   - Flag any AC items that cannot be expressed as automation (note to PM)
2. **Create** `qa-suites/products/dungeoncrawler/features/dc-cr-dwarf-heritage-death-warden.json` from `templates/qa-feature-suite.json`:
   - Declare at least one runnable suite entry for this feature
   - Include `owner_seat`, `source_path`, `env_requirements`, and `release_checkpoint`
   - Point `test_plan` at `features/dc-cr-dwarf-heritage-death-warden/03-test-plan.md`
   - Validate with:
     ```bash
     python3 scripts/qa-suite-validate.py --product dungeoncrawler --feature-id dc-cr-dwarf-heritage-death-warden
     ```
2. **Signal completion:**
    ```bash
    ./scripts/qa-pm-testgen-complete.sh dungeoncrawler dc-cr-dwarf-heritage-death-warden "<brief summary>"
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

# Acceptance Criteria — dc-cr-dwarf-heritage-death-warden

- Feature: Dwarf Heritage — Death Warden
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Turn the Death Warden dwarf heritage into a testable contract covering heritage availability, necromancy save upgrades, and the boundaries of the passive so it can be implemented inside the save-resolution pipeline.

## Dependency checkpoints

- Depends on: dc-cr-dwarf-ancestry, dc-cr-heritage-system
- Merged into: dc-cr-dwarf-ancestry (all heritages and ancestry feats covered in bulk AC)

## Happy Path

- [ ] `[NEW]` The Death Warden heritage exists as a selectable dwarf heritage and is unavailable to non-dwarf ancestries.
- [ ] `[NEW]` When a Death Warden dwarf succeeds on a saving throw against a necromancy effect, the final result is upgraded to a critical success.
- [ ] `[NEW]` Necromancy critical successes remain critical successes rather than being double-upgraded or otherwise altered.
- [ ] `[NEW]` The heritage effect is passive and automatic; no extra player action or toggle is required during save resolution.

## Edge Cases

- [ ] `[NEW]` The save upgrade only applies to effects tagged as necromancy and does not modify non-necromancy saves.
- [ ] `[NEW]` Characters can hold only one dwarf heritage at a time, so Death Warden cannot stack with another dwarf heritage bonus.
- [ ] `[NEW]` Save logs or combat resolution output clearly show the upgraded outcome for QA traceability.

## Failure Modes

- [ ] `[NEW]` Invalid heritage selection for the wrong ancestry is rejected rather than persisted.
- [ ] `[NEW]` If an effect lacks the necromancy tag, the save resolver falls back to the baseline success result without throwing an error.

## Security acceptance criteria

- Security AC exemption: passive ancestry heritage behavior only; no new routes or input surfaces beyond existing heritage assignment and combat-resolution handlers.
- Agent: qa-dungeoncrawler
- Status: pending
