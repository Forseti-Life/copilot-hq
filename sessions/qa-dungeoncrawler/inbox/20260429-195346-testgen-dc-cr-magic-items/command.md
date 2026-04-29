# Test Plan Design: dc-cr-magic-items

**From:** pm-dungeoncrawler  
**To:** qa-dungeoncrawler  
**Date:** 2026-04-29T19:53:46+00:00  

## Task

Design test cases for this feature and write the test plan spec.

**This is NEXT-RELEASE grooming work.** Do NOT edit the live product manifest `qa-suites/products/dungeoncrawler/suite.json` yet.
Instead, create a **feature-scoped suite overlay** at:
`qa-suites/products/dungeoncrawler/features/dc-cr-magic-items.json`

That overlay is the runnable SoT for this feature during grooming. The live release manifest is compiled from selected overlays at Stage 0.

### Required outputs

1. **Create** `features/dc-cr-magic-items/03-test-plan.md` — the test spec:
   - List every test case derived from the AC
   - For each: test description, which suite it will live in, expected HTTP status or behavior, roles covered
   - Flag any AC items that cannot be expressed as automation (note to PM)
2. **Create** `qa-suites/products/dungeoncrawler/features/dc-cr-magic-items.json` from `templates/qa-feature-suite.json`:
   - Declare at least one runnable suite entry for this feature
   - Include `owner_seat`, `source_path`, `env_requirements`, and `release_checkpoint`
   - Point `test_plan` at `features/dc-cr-magic-items/03-test-plan.md`
   - Validate with:
     ```bash
     python3 scripts/qa-suite-validate.py --product dungeoncrawler --feature-id dc-cr-magic-items
     ```
2. **Signal completion:**
    ```bash
    ./scripts/qa-pm-testgen-complete.sh dungeoncrawler dc-cr-magic-items "<brief summary>"
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

# Acceptance Criteria — dc-cr-magic-items

- Feature: Magic Items and Treasure
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Capture the magic-item backlog as a QA-ready contract covering the item catalog, activation/usage metadata, and the invested-item limit so inventory and encounter systems have a concrete rules target.

## Dependency checkpoints

- Consolidated into: dc-cr-magic-ch11 (requirements covered in that feature's acceptance criteria)

## Happy Path

- [ ] `[NEW]` The catalog covers weapons, armor, wondrous items, and other held/worn item types needed by chapter 11 scope.
- [ ] `[NEW]` Each magic item includes level, price, activation method, and usage state such as held, worn, or invested.
- [ ] `[NEW]` Characters can equip and track invested items, with a hard cap of 10 invested items at one time.
- [ ] `[NEW]` Inventory/equipment flows can differentiate held, worn, and invested behaviors when presenting item actions and restrictions.

## Edge Cases

- [ ] `[NEW]` Items that are worn or held but not invested do not consume one of the 10 investment slots.
- [ ] `[NEW]` Activation types such as command word, Cast a Spell, and Interact remain distinguishable in the catalog and UI contract.
- [ ] `[NEW]` Characters unequipping or uninvesting an item immediately free the consumed invest slot for another item.

## Failure Modes

- [ ] `[NEW]` Attempting to invest an eleventh item fails with a validation error rather than silently exceeding the cap.
- [ ] `[NEW]` Catalog entries missing required activation or usage metadata are rejected during validation.

## Security acceptance criteria

- Security AC exemption: catalog, inventory, and equipment-rule scope only; use existing item management surfaces without introducing new routes or novel input handling.
