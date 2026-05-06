- Status: done
- Completed: 2026-04-29T21:18:32Z

# Test Plan Design: dc-cr-half-elf-heritage

**From:** pm-dungeoncrawler  
**To:** qa-dungeoncrawler  
**Date:** 2026-04-29T19:53:46+00:00  

## Task

Design test cases for this feature and write the test plan spec.

**This is NEXT-RELEASE grooming work.** Do NOT edit the live product manifest `qa-suites/products/dungeoncrawler/suite.json` yet.
Instead, create a **feature-scoped suite overlay** at:
`qa-suites/products/dungeoncrawler/features/dc-cr-half-elf-heritage.json`

That overlay is the runnable SoT for this feature during grooming. The live release manifest is compiled from selected overlays at Stage 0.

### Required outputs

1. **Create** `features/dc-cr-half-elf-heritage/03-test-plan.md` — the test spec:
   - List every test case derived from the AC
   - For each: test description, which suite it will live in, expected HTTP status or behavior, roles covered
   - Flag any AC items that cannot be expressed as automation (note to PM)
2. **Create** `qa-suites/products/dungeoncrawler/features/dc-cr-half-elf-heritage.json` from `templates/qa-feature-suite.json`:
   - Declare at least one runnable suite entry for this feature
   - Include `owner_seat`, `source_path`, `env_requirements`, and `release_checkpoint`
   - Point `test_plan` at `features/dc-cr-half-elf-heritage/03-test-plan.md`
   - Validate with:
     ```bash
     python3 scripts/qa-suite-validate.py --product dungeoncrawler --feature-id dc-cr-half-elf-heritage
     ```
2. **Signal completion:**
    ```bash
    ./scripts/qa-pm-testgen-complete.sh dungeoncrawler dc-cr-half-elf-heritage "<brief summary>"
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

# Acceptance Criteria — dc-cr-half-elf-heritage

- Feature: Half-Elf Heritage
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Define Half-Elf heritage as a Human heritage overlay contract covering trait grants, low-light vision, and the expanded ancestry-feat pool used at later feat milestones.

## Dependency checkpoints

- Depends on: dc-cr-human-ancestry, dc-cr-heritage-system, dc-cr-low-light-vision, dc-cr-ancestry-feat-schedule

## Happy Path

- [ ] `[NEW]` Half-Elf is implemented as a selectable Human heritage rather than a standalone ancestry.
- [ ] `[NEW]` Selecting the heritage grants the elf trait, the half-elf trait, and low-light vision.
- [ ] `[NEW]` Ancestry-feat selection for a Half-Elf character can draw from human, elf, and half-elf feat pools while still enforcing feat prerequisites.
- [ ] `[NEW]` The expanded feat-pool behavior is visible anywhere the character gains an ancestry feat slot.

## Edge Cases

- [ ] `[NEW]` If the character already has low-light vision from another valid source, the heritage does not create duplicate sense flags.
- [ ] `[NEW]` Half-Elf remains mutually exclusive with other Human heritages.
- [ ] `[NEW]` Feat browsing clearly indicates why an elf, half-elf, or human feat is or is not selectable for the current character.

## Failure Modes

- [ ] `[NEW]` Non-Human characters cannot select the Half-Elf heritage.
- [ ] `[NEW]` The feat picker rejects ancestry feats outside the allowed human/elf/half-elf pools instead of silently accepting them.

## Security acceptance criteria

- Security AC exemption: ancestry heritage and feat-eligibility scope only; no new routes or input surfaces beyond existing heritage assignment and ancestry-feat handlers.
- Agent: qa-dungeoncrawler
- Status: pending
