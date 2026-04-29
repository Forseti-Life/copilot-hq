# Test Plan Design: dc-cr-general-feats

**From:** pm-dungeoncrawler  
**To:** qa-dungeoncrawler  
**Date:** 2026-04-29T19:53:46+00:00  

## Task

Design test cases for this feature and write the test plan spec.

**This is NEXT-RELEASE grooming work.** Do NOT edit the live product manifest `qa-suites/products/dungeoncrawler/suite.json` yet.
Instead, create a **feature-scoped suite overlay** at:
`qa-suites/products/dungeoncrawler/features/dc-cr-general-feats.json`

That overlay is the runnable SoT for this feature during grooming. The live release manifest is compiled from selected overlays at Stage 0.

### Required outputs

1. **Create** `features/dc-cr-general-feats/03-test-plan.md` — the test spec:
   - List every test case derived from the AC
   - For each: test description, which suite it will live in, expected HTTP status or behavior, roles covered
   - Flag any AC items that cannot be expressed as automation (note to PM)
2. **Create** `qa-suites/products/dungeoncrawler/features/dc-cr-general-feats.json` from `templates/qa-feature-suite.json`:
   - Declare at least one runnable suite entry for this feature
   - Include `owner_seat`, `source_path`, `env_requirements`, and `release_checkpoint`
   - Point `test_plan` at `features/dc-cr-general-feats/03-test-plan.md`
   - Validate with:
     ```bash
     python3 scripts/qa-suite-validate.py --product dungeoncrawler --feature-id dc-cr-general-feats
     ```
2. **Signal completion:**
    ```bash
    ./scripts/qa-pm-testgen-complete.sh dungeoncrawler dc-cr-general-feats "<brief summary>"
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

# Acceptance Criteria — dc-cr-general-feats

- Feature: General Feats
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Define the general-feat backlog as a QA-ready contract for the level-based feat schedule, catalog visibility, prerequisite validation, and representative feat effects that apply across classes.

## Dependency checkpoints

- Consolidated into: dc-cr-feats-ch05 (requirements covered in that feature's acceptance criteria)

## Happy Path

- [ ] `[NEW]` General feat slots open at levels 3, 7, 11, 15, and 19 and are distinct from class, ancestry, and skill feat slots.
- [ ] `[NEW]` The general-feat catalog includes the chapter's core cross-class options (for example Armor Proficiency, Shield Block, Toughness, and Incredible Initiative) with the metadata needed for the picker.
- [ ] `[NEW]` The feat picker only offers general feats whose prerequisites are satisfied by the current character build.
- [ ] `[NEW]` Taking a general feat applies its listed modifier, action, or rules flag to the character state in a testable way.

## Edge Cases

- [ ] `[NEW]` A feat available from multiple sources is still tracked in the correct feat pool and not duplicated across slot types.
- [ ] `[NEW]` Leveling without an eligible general feat choice leaves the slot open rather than auto-assigning an invalid feat.
- [ ] `[NEW]` Retraining recalculates downstream prerequisites for other feat selections.

## Failure Modes

- [ ] `[NEW]` General feats cannot be selected in ancestry-feat or class-feat slots.
- [ ] `[NEW]` Submitting a feat without meeting its prerequisites returns a validation error instead of corrupting the build.

## Security acceptance criteria

- Security AC exemption: feat-catalog and character-build scope only; no new routes or input surfaces beyond existing feat assignment handlers.
