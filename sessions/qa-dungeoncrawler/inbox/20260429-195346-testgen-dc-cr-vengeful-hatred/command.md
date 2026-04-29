# Test Plan Design: dc-cr-vengeful-hatred

**From:** pm-dungeoncrawler  
**To:** qa-dungeoncrawler  
**Date:** 2026-04-29T19:53:46+00:00  

## Task

Design test cases for this feature and write the test plan spec.

**This is NEXT-RELEASE grooming work.** Do NOT edit the live product manifest `qa-suites/products/dungeoncrawler/suite.json` yet.
Instead, create a **feature-scoped suite overlay** at:
`qa-suites/products/dungeoncrawler/features/dc-cr-vengeful-hatred.json`

That overlay is the runnable SoT for this feature during grooming. The live release manifest is compiled from selected overlays at Stage 0.

### Required outputs

1. **Create** `features/dc-cr-vengeful-hatred/03-test-plan.md` — the test spec:
   - List every test case derived from the AC
   - For each: test description, which suite it will live in, expected HTTP status or behavior, roles covered
   - Flag any AC items that cannot be expressed as automation (note to PM)
2. **Create** `qa-suites/products/dungeoncrawler/features/dc-cr-vengeful-hatred.json` from `templates/qa-feature-suite.json`:
   - Declare at least one runnable suite entry for this feature
   - Include `owner_seat`, `source_path`, `env_requirements`, and `release_checkpoint`
   - Point `test_plan` at `features/dc-cr-vengeful-hatred/03-test-plan.md`
   - Validate with:
     ```bash
     python3 scripts/qa-suite-validate.py --product dungeoncrawler --feature-id dc-cr-vengeful-hatred
     ```
2. **Signal completion:**
    ```bash
    ./scripts/qa-pm-testgen-complete.sh dungeoncrawler dc-cr-vengeful-hatred "<brief summary>"
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

# Acceptance Criteria — dc-cr-vengeful-hatred

- Feature: Vengeful Hatred (Dwarf Ancestry Feat)
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Define Vengeful Hatred as a QA-ready level-1 dwarf ancestry-feat contract for ancestry-foe selection, damage-bonus scaling by weapon dice, and the temporary retaliation bonus after taking a critical hit.

## Dependency checkpoints

- Depends on: dc-cr-dwarf-ancestry, dc-cr-ancestry-feat-schedule, dc-cr-ancestry-traits
- Merged into: dc-cr-dwarf-ancestry (all heritages and ancestry feats covered in bulk AC)

## Happy Path

- [ ] `[NEW]` Vengeful Hatred exists as a level-1 dwarf ancestry feat and prompts the player to choose one ancestral foe type from drow, duergar, giant, or orc.
- [ ] `[NEW]` The chosen foe type grants a +1 circumstance bonus to weapon and unarmed damage against that foe, scaling by the number of weapon damage dice at higher levels.
- [ ] `[NEW]` If a creature critically hits the character and deals damage, the character gains the same damage bonus against that specific creature for 1 minute even if it is not the chosen ancestral foe type.
- [ ] `[NEW]` The chosen foe type and any active temporary retaliation target are visible in character/combat state for QA verification.

## Edge Cases

- [ ] `[NEW]` Changing the chosen ancestral foe requires a retrain/rebuild flow rather than an in-combat toggle.
- [ ] `[NEW]` Damage scaling updates when the weapon's number of damage dice increases.
- [ ] `[NEW]` The temporary retaliation bonus expires after 1 minute and does not persist between encounters unless the timer is refreshed by another triggering critical hit.

## Failure Modes

- [ ] `[NEW]` Invalid ancestral foe choices are rejected during feat selection.
- [ ] `[NEW]` A critical hit that deals no damage does not grant the temporary retaliation bonus.

## Security acceptance criteria

- Security AC exemption: ancestry-feat and combat-modifier scope only; no new routes or input surfaces beyond existing feat assignment and combat-resolution handlers.
