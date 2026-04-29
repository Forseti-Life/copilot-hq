# Test Plan Design: dc-cr-alchemical-items

**From:** pm-dungeoncrawler  
**To:** qa-dungeoncrawler  
**Date:** 2026-04-29T19:53:45+00:00  

## Task

Design test cases for this feature and write the test plan spec.

**This is NEXT-RELEASE grooming work.** Do NOT edit the live product manifest `qa-suites/products/dungeoncrawler/suite.json` yet.
Instead, create a **feature-scoped suite overlay** at:
`qa-suites/products/dungeoncrawler/features/dc-cr-alchemical-items.json`

That overlay is the runnable SoT for this feature during grooming. The live release manifest is compiled from selected overlays at Stage 0.

### Required outputs

1. **Create** `features/dc-cr-alchemical-items/03-test-plan.md` — the test spec:
   - List every test case derived from the AC
   - For each: test description, which suite it will live in, expected HTTP status or behavior, roles covered
   - Flag any AC items that cannot be expressed as automation (note to PM)
2. **Create** `qa-suites/products/dungeoncrawler/features/dc-cr-alchemical-items.json` from `templates/qa-feature-suite.json`:
   - Declare at least one runnable suite entry for this feature
   - Include `owner_seat`, `source_path`, `env_requirements`, and `release_checkpoint`
   - Point `test_plan` at `features/dc-cr-alchemical-items/03-test-plan.md`
   - Validate with:
     ```bash
     python3 scripts/qa-suite-validate.py --product dungeoncrawler --feature-id dc-cr-alchemical-items
     ```
2. **Signal completion:**
    ```bash
    ./scripts/qa-pm-testgen-complete.sh dungeoncrawler dc-cr-alchemical-items "<brief summary>"
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

# Acceptance Criteria — dc-cr-alchemical-items

- Feature: Alchemical Items
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Capture the alchemical item backlog as a QA-ready contract covering bombs, elixirs, mutagens, poisons, and other consumables, including the metadata the item catalog and alchemist daily-prep flows need in order to behave consistently.

## Dependency checkpoints

- Consolidated into: dc-cr-equipment-ch06 (requirements covered in that feature's acceptance criteria)

## Happy Path

- [ ] `[NEW]` Catalog coverage includes bombs, elixirs, mutagens, poisons, and at least one non-consumable alchemical tool grouping so QA can verify the chapter scope is represented.
- [ ] `[NEW]` Each alchemical item record exposes level, price, bulk, activation cost, duration or persistence, and effect text needed by inventory and encounter rendering.
- [ ] `[NEW]` Bomb entries are marked as thrown alchemical consumables and identify the damage/effect payload the encounter resolver must apply on use.
- [ ] `[NEW]` Alchemist daily-prep / Infused Reagents flows only surface items flagged as alchemical and consumable where the rules expect that behavior.

## Edge Cases

- [ ] `[NEW]` Alchemical items remain non-magical: they do not consume invest slots and are not mislabeled as spells, runes, or other magical equipment.
- [ ] `[NEW]` Consumable quantity/use tracking removes a spent item after use while persistent catalog metadata remains intact for future crafting or prep.
- [ ] `[NEW]` Category-specific rules (for example poison delivery vs. mutagen self-use) can be validated without collapsing the categories into a single generic effect bucket.

## Failure Modes

- [ ] `[NEW]` Items missing required catalog metadata (level, activation, or effect summary) are rejected during content validation rather than silently published.
- [ ] `[NEW]` Magic-item-only behaviors such as investment or rune slots are not attached to alchemical records.

## Security acceptance criteria

- Security AC exemption: catalog/content and rules-data scope only; use existing item, inventory, and crafting surfaces without introducing new routes or novel input handling.
- Agent: qa-dungeoncrawler
- Status: pending
