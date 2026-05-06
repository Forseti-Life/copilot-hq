- Status: done
- Completed: 2026-04-29T21:06:53Z

# Test Plan Design: dc-cr-elf-heritage-arctic

**From:** pm-dungeoncrawler  
**To:** qa-dungeoncrawler  
**Date:** 2026-04-29T19:53:46+00:00  

## Task

Design test cases for this feature and write the test plan spec.

**This is NEXT-RELEASE grooming work.** Do NOT edit the live product manifest `qa-suites/products/dungeoncrawler/suite.json` yet.
Instead, create a **feature-scoped suite overlay** at:
`qa-suites/products/dungeoncrawler/features/dc-cr-elf-heritage-arctic.json`

That overlay is the runnable SoT for this feature during grooming. The live release manifest is compiled from selected overlays at Stage 0.

### Required outputs

1. **Create** `features/dc-cr-elf-heritage-arctic/03-test-plan.md` — the test spec:
   - List every test case derived from the AC
   - For each: test description, which suite it will live in, expected HTTP status or behavior, roles covered
   - Flag any AC items that cannot be expressed as automation (note to PM)
2. **Create** `qa-suites/products/dungeoncrawler/features/dc-cr-elf-heritage-arctic.json` from `templates/qa-feature-suite.json`:
   - Declare at least one runnable suite entry for this feature
   - Include `owner_seat`, `source_path`, `env_requirements`, and `release_checkpoint`
   - Point `test_plan` at `features/dc-cr-elf-heritage-arctic/03-test-plan.md`
   - Validate with:
     ```bash
     python3 scripts/qa-suite-validate.py --product dungeoncrawler --feature-id dc-cr-elf-heritage-arctic
     ```
2. **Signal completion:**
    ```bash
    ./scripts/qa-pm-testgen-complete.sh dungeoncrawler dc-cr-elf-heritage-arctic "<brief summary>"
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

# Acceptance Criteria — dc-cr-elf-heritage-arctic

- Feature: Arctic Elf Heritage
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Make Arctic Elf a QA-ready heritage contract with level-scaling cold resistance and one-step environmental cold mitigation so the missing implementation gaps can be tested directly.

## Dependency checkpoints

- Depends on: dc-cr-elf-ancestry, dc-cr-heritage-system

## Happy Path

- [ ] `[NEW]` Arctic Elf is present as an elf-only heritage option.
- [ ] `[NEW]` Selecting Arctic Elf grants cold resistance equal to half the character level, minimum 1.
- [ ] `[NEW]` Environmental cold effects are treated as one step less severe for the character.
- [ ] `[NEW]` The cold-resistance value recalculates when the character level changes.

## Edge Cases

- [ ] `[NEW]` Level 1 characters still receive the minimum cold resistance of 1.
- [ ] `[NEW]` Only cold/environmental-cold effects are downgraded; unrelated environmental hazards stay unchanged.
- [ ] `[NEW]` One-step severity downgrades follow the documented ladder without skipping directly to harmless.

## Failure Modes

- [ ] `[NEW]` Non-elf characters cannot select Arctic Elf heritage.
- [ ] `[NEW]` If an environmental hazard lacks cold-severity metadata, the hazard resolves normally instead of producing an implementation error.

## Security acceptance criteria

- Security AC exemption: passive ancestry heritage behavior only; no new routes or input surfaces beyond existing heritage assignment, resistance, and hazard-resolution handlers.
- Agent: qa-dungeoncrawler
- Status: pending
