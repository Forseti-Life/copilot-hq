# Test Plan Design: dc-cr-gm-tools

**From:** pm-dungeoncrawler  
**To:** qa-dungeoncrawler  
**Date:** 2026-04-29T19:53:46+00:00  

## Task

Design test cases for this feature and write the test plan spec.

**This is NEXT-RELEASE grooming work.** Do NOT edit the live product manifest `qa-suites/products/dungeoncrawler/suite.json` yet.
Instead, create a **feature-scoped suite overlay** at:
`qa-suites/products/dungeoncrawler/features/dc-cr-gm-tools.json`

That overlay is the runnable SoT for this feature during grooming. The live release manifest is compiled from selected overlays at Stage 0.

### Required outputs

1. **Create** `features/dc-cr-gm-tools/03-test-plan.md` — the test spec:
   - List every test case derived from the AC
   - For each: test description, which suite it will live in, expected HTTP status or behavior, roles covered
   - Flag any AC items that cannot be expressed as automation (note to PM)
2. **Create** `qa-suites/products/dungeoncrawler/features/dc-cr-gm-tools.json` from `templates/qa-feature-suite.json`:
   - Declare at least one runnable suite entry for this feature
   - Include `owner_seat`, `source_path`, `env_requirements`, and `release_checkpoint`
   - Point `test_plan` at `features/dc-cr-gm-tools/03-test-plan.md`
   - Validate with:
     ```bash
     python3 scripts/qa-suite-validate.py --product dungeoncrawler --feature-id dc-cr-gm-tools
     ```
2. **Signal completion:**
    ```bash
    ./scripts/qa-pm-testgen-complete.sh dungeoncrawler dc-cr-gm-tools "<brief summary>"
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

# Acceptance Criteria — dc-cr-gm-tools

- Feature: GM Tools and Adventure Preparation
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Convert the GM tools backlog into a concrete QA contract for encounter budgeting, environment/terrain references, NPC prep data, and loot generation so the AI GM has a defined rules surface to build against.

## Dependency checkpoints

- Consolidated into: dc-gmg-running-guide (requirements covered in that feature's acceptance criteria)

## Happy Path

- [ ] `[NEW]` GM prep tooling exposes encounter budget guidance by party level/size and threat category (Trivial, Low, Moderate, Severe, Extreme).
- [ ] `[NEW]` GM prep references include environment/terrain guidance, NPC stat-block structure, and loot-by-level lookup data required for session preparation.
- [ ] `[NEW]` AI GM or GM-facing prep flows can retrieve the budgeting and reward data without requiring players to perform manual rules math.
- [ ] `[NEW]` The feature contract identifies which outputs are read-only guidance and which are GM-triggered generation actions.

## Edge Cases

- [ ] `[NEW]` Encounter-budget calculations respond predictably when party size or level is outside the normal table range.
- [ ] `[NEW]` Loot generation handles missing or incomplete tables with an explicit validation/reporting path instead of producing nonsense rewards.
- [ ] `[NEW]` Player-facing surfaces do not expose GM-only prep details that would spoil encounters or hidden NPC data.

## Failure Modes

- [ ] `[NEW]` Invalid party size, level, or threat-category inputs return a validation error rather than generating misleading encounter budgets.
- [ ] `[NEW]` GM-only routes return 403 for non-GM roles.

## Security acceptance criteria

- [ ] GM-only prep or generation routes require authenticated GM/admin access and return 403 to anonymous or standard player roles.
- [ ] All GM prep POST/PATCH actions require `_csrf_request_header_mode: TRUE`.
- [ ] Generated encounter, loot, and NPC prep data is scoped to the current campaign/session context with no cross-campaign leakage.
- [ ] Logs and AI prompt payloads exclude unrelated player secrets/PII and record only minimum traceability identifiers.
- Agent: qa-dungeoncrawler
- Status: pending
