- Status: done
- Completed: 2026-04-29T21:00:09Z

# Test Plan Design: dc-cr-dwarven-weapon-expertise

**From:** pm-dungeoncrawler  
**To:** qa-dungeoncrawler  
**Date:** 2026-04-29T19:53:46+00:00  

## Task

Design test cases for this feature and write the test plan spec.

**This is NEXT-RELEASE grooming work.** Do NOT edit the live product manifest `qa-suites/products/dungeoncrawler/suite.json` yet.
Instead, create a **feature-scoped suite overlay** at:
`qa-suites/products/dungeoncrawler/features/dc-cr-dwarven-weapon-expertise.json`

That overlay is the runnable SoT for this feature during grooming. The live release manifest is compiled from selected overlays at Stage 0.

### Required outputs

1. **Create** `features/dc-cr-dwarven-weapon-expertise/03-test-plan.md` — the test spec:
   - List every test case derived from the AC
   - For each: test description, which suite it will live in, expected HTTP status or behavior, roles covered
   - Flag any AC items that cannot be expressed as automation (note to PM)
2. **Create** `qa-suites/products/dungeoncrawler/features/dc-cr-dwarven-weapon-expertise.json` from `templates/qa-feature-suite.json`:
   - Declare at least one runnable suite entry for this feature
   - Include `owner_seat`, `source_path`, `env_requirements`, and `release_checkpoint`
   - Point `test_plan` at `features/dc-cr-dwarven-weapon-expertise/03-test-plan.md`
   - Validate with:
     ```bash
     python3 scripts/qa-suite-validate.py --product dungeoncrawler --feature-id dc-cr-dwarven-weapon-expertise
     ```
2. **Signal completion:**
    ```bash
    ./scripts/qa-pm-testgen-complete.sh dungeoncrawler dc-cr-dwarven-weapon-expertise "<brief summary>"
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

# Acceptance Criteria — dc-cr-dwarven-weapon-expertise

- Feature: Dwarven Weapon Expertise
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Turn Dwarven Weapon Expertise into a testable ancestry-feat contract for high-level proficiency propagation onto battle axes, picks, warhammers, and other trained dwarven weapons.

## Dependency checkpoints

- Depends on: dc-cr-dwarf-ancestry, dc-cr-ancestry-feat-schedule, dc-cr-dwarven-weapon-familiarity, dc-cr-equipment-system

## Happy Path

- [ ] `[NEW]` The feat exists in the dwarf ancestry-feat catalog at level 13 with Dwarven Weapon Familiarity as a prerequisite.
- [ ] `[NEW]` When the character gains a class feature that grants expert or higher weapon proficiency, that rank is copied to battle axes, picks, warhammers, and any trained dwarven weapons.
- [ ] `[NEW]` The upgrade uses the character's current trained dwarven-weapon set rather than granting expertise to unrelated weapon families.
- [ ] `[NEW]` Rebuilds or later class-proficiency upgrades recalculate the dwarven-weapon expertise bonus correctly.

## Edge Cases

- [ ] `[NEW]` Characters without the prerequisite feat cannot select Dwarven Weapon Expertise.
- [ ] `[NEW]` If a weapon already has an equal or higher proficiency rank from another source, the feat does not downgrade or duplicate that rank.
- [ ] `[NEW]` New dwarven weapons learned later inherit the propagated proficiency if they satisfy the trained-weapon requirement.

## Failure Modes

- [ ] `[NEW]` Selecting the feat below level 13 or on a non-dwarf build fails validation.
- [ ] `[NEW]` Missing dwarven-weapon tags or malformed proficiency mappings do not crash the character sheet; they surface a validation defect instead.

## Security acceptance criteria

- Security AC exemption: ancestry-feat and proficiency-calculation scope only; no new routes or input surfaces beyond existing feat assignment and character build handlers.
- Agent: qa-dungeoncrawler
- Status: pending
