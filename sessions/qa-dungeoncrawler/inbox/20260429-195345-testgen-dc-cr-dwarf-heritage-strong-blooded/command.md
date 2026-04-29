# Test Plan Design: dc-cr-dwarf-heritage-strong-blooded

**From:** pm-dungeoncrawler  
**To:** qa-dungeoncrawler  
**Date:** 2026-04-29T19:53:46+00:00  

## Task

Design test cases for this feature and write the test plan spec.

**This is NEXT-RELEASE grooming work.** Do NOT edit the live product manifest `qa-suites/products/dungeoncrawler/suite.json` yet.
Instead, create a **feature-scoped suite overlay** at:
`qa-suites/products/dungeoncrawler/features/dc-cr-dwarf-heritage-strong-blooded.json`

That overlay is the runnable SoT for this feature during grooming. The live release manifest is compiled from selected overlays at Stage 0.

### Required outputs

1. **Create** `features/dc-cr-dwarf-heritage-strong-blooded/03-test-plan.md` — the test spec:
   - List every test case derived from the AC
   - For each: test description, which suite it will live in, expected HTTP status or behavior, roles covered
   - Flag any AC items that cannot be expressed as automation (note to PM)
2. **Create** `qa-suites/products/dungeoncrawler/features/dc-cr-dwarf-heritage-strong-blooded.json` from `templates/qa-feature-suite.json`:
   - Declare at least one runnable suite entry for this feature
   - Include `owner_seat`, `source_path`, `env_requirements`, and `release_checkpoint`
   - Point `test_plan` at `features/dc-cr-dwarf-heritage-strong-blooded/03-test-plan.md`
   - Validate with:
     ```bash
     python3 scripts/qa-suite-validate.py --product dungeoncrawler --feature-id dc-cr-dwarf-heritage-strong-blooded
     ```
2. **Signal completion:**
    ```bash
    ./scripts/qa-pm-testgen-complete.sh dungeoncrawler dc-cr-dwarf-heritage-strong-blooded "<brief summary>"
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

# Acceptance Criteria — dc-cr-dwarf-heritage-strong-blooded

- Feature: Dwarf Heritage — Strong-Blooded Dwarf
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Define the Strong-Blooded dwarf heritage contract so poison resistance and poison-stage reduction rules can be validated in the affliction engine without ambiguity.

## Dependency checkpoints

- Depends on: dc-cr-dwarf-ancestry, dc-cr-heritage-system
- Merged into: dc-cr-dwarf-ancestry (all heritages and ancestry feats covered in bulk AC)

## Happy Path

- [ ] `[NEW]` Strong-Blooded is available as a dwarf-only heritage selection.
- [ ] `[NEW]` The heritage grants poison resistance equal to half the character level, minimum 1.
- [ ] `[NEW]` On a successful save against a poison affliction, the poison stage is reduced by 2, or by 1 if the poison is virulent.
- [ ] `[NEW]` On a critical success, the poison stage is reduced by 3, or by 2 if the poison is virulent.

## Edge Cases

- [ ] `[NEW]` Level-up recalculates the poison-resistance value without requiring the heritage to be re-selected.
- [ ] `[NEW]` Non-poison afflictions such as disease do not receive the Strong-Blooded stage-reduction benefit.
- [ ] `[NEW]` Virulent-poison handling still uses the reduced stage-drop values rather than the standard success/critical-success drops.

## Failure Modes

- [ ] `[NEW]` Selecting the heritage for a non-dwarf ancestry is rejected.
- [ ] `[NEW]` If the affliction is missing poison metadata, resolution falls back safely instead of applying the Strong-Blooded adjustment incorrectly.

## Security acceptance criteria

- Security AC exemption: passive ancestry heritage behavior only; no new routes or input surfaces beyond existing heritage assignment and affliction-resolution handlers.
- Agent: qa-dungeoncrawler
- Status: pending
