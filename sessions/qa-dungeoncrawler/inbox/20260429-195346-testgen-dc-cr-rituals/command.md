# Test Plan Design: dc-cr-rituals

**From:** pm-dungeoncrawler  
**To:** qa-dungeoncrawler  
**Date:** 2026-04-29T19:53:46+00:00  

## Task

Design test cases for this feature and write the test plan spec.

**This is NEXT-RELEASE grooming work.** Do NOT edit the live product manifest `qa-suites/products/dungeoncrawler/suite.json` yet.
Instead, create a **feature-scoped suite overlay** at:
`qa-suites/products/dungeoncrawler/features/dc-cr-rituals.json`

That overlay is the runnable SoT for this feature during grooming. The live release manifest is compiled from selected overlays at Stage 0.

### Required outputs

1. **Create** `features/dc-cr-rituals/03-test-plan.md` — the test spec:
   - List every test case derived from the AC
   - For each: test description, which suite it will live in, expected HTTP status or behavior, roles covered
   - Flag any AC items that cannot be expressed as automation (note to PM)
2. **Create** `qa-suites/products/dungeoncrawler/features/dc-cr-rituals.json` from `templates/qa-feature-suite.json`:
   - Declare at least one runnable suite entry for this feature
   - Include `owner_seat`, `source_path`, `env_requirements`, and `release_checkpoint`
   - Point `test_plan` at `features/dc-cr-rituals/03-test-plan.md`
   - Validate with:
     ```bash
     python3 scripts/qa-suite-validate.py --product dungeoncrawler --feature-id dc-cr-rituals
     ```
2. **Signal completion:**
    ```bash
    ./scripts/qa-pm-testgen-complete.sh dungeoncrawler dc-cr-rituals "<brief summary>"
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

# Acceptance Criteria — dc-cr-rituals

- Feature: Ritual Magic System
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Define ritual magic as a separate QA-ready subsystem contract covering long casting times, caster roles, skill checks, and non-slot failure consequences for narrative-scale magic.

## Dependency checkpoints

- Consolidated into: dc-cr-spells-ch07 (requirements covered in that feature's acceptance criteria)

## Happy Path

- [ ] `[NEW]` Rituals are represented separately from standard spellcasting and do not consume prepared spell slots or spontaneous spell slots.
- [ ] `[NEW]` A ritual definition captures casting time, primary caster requirements, optional/required secondary casters, and the relevant skill checks.
- [ ] `[NEW]` Ritual execution supports success, failure, and critical-failure outcomes with explicit consequences.
- [ ] `[NEW]` Rituals can be surfaced as campaign-scale actions without being mixed into everyday encounter spellcasting UI.

## Edge Cases

- [ ] `[NEW]` Rituals with long casting times (minutes to days) preserve progress and requirements across the full casting window.
- [ ] `[NEW]` Insufficient or invalid secondary casters block ritual completion with a clear validation path.
- [ ] `[NEW]` Narrative-only or partially manual ritual consequences are identified so QA can separate automation from manual verification.

## Failure Modes

- [ ] `[NEW]` Attempting to cast a ritual through the normal spellcasting action flow is rejected.
- [ ] `[NEW]` Missing required skill-check metadata or ritual participants fails validation rather than creating a partially resolved ritual.

## Security acceptance criteria

- Security AC exemption: spellcasting/rules-engine scope only; no new public routes expected beyond existing spellcasting, downtime, or session-action handlers.
- Agent: qa-dungeoncrawler
- Status: pending
