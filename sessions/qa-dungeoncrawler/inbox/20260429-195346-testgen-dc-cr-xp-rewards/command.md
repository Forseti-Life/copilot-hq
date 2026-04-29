# Test Plan Design: dc-cr-xp-rewards

**From:** pm-dungeoncrawler  
**To:** qa-dungeoncrawler  
**Date:** 2026-04-29T19:53:46+00:00  

## Task

Design test cases for this feature and write the test plan spec.

**This is NEXT-RELEASE grooming work.** Do NOT edit the live product manifest `qa-suites/products/dungeoncrawler/suite.json` yet.
Instead, create a **feature-scoped suite overlay** at:
`qa-suites/products/dungeoncrawler/features/dc-cr-xp-rewards.json`

That overlay is the runnable SoT for this feature during grooming. The live release manifest is compiled from selected overlays at Stage 0.

### Required outputs

1. **Create** `features/dc-cr-xp-rewards/03-test-plan.md` — the test spec:
   - List every test case derived from the AC
   - For each: test description, which suite it will live in, expected HTTP status or behavior, roles covered
   - Flag any AC items that cannot be expressed as automation (note to PM)
2. **Create** `qa-suites/products/dungeoncrawler/features/dc-cr-xp-rewards.json` from `templates/qa-feature-suite.json`:
   - Declare at least one runnable suite entry for this feature
   - Include `owner_seat`, `source_path`, `env_requirements`, and `release_checkpoint`
   - Point `test_plan` at `features/dc-cr-xp-rewards/03-test-plan.md`
   - Validate with:
     ```bash
     python3 scripts/qa-suite-validate.py --product dungeoncrawler --feature-id dc-cr-xp-rewards
     ```
2. **Signal completion:**
    ```bash
    ./scripts/qa-pm-testgen-complete.sh dungeoncrawler dc-cr-xp-rewards "<brief summary>"
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

# Acceptance Criteria — dc-cr-xp-rewards

- Feature: XP and Rewards System
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Capture the XP-and-rewards backlog as a QA contract for encounter, hazard, and story-milestone XP accrual that advances character levels at the documented threshold and aligns with the newer xp-award-system dependency.

## Dependency checkpoints

- Depends on: dc-cr-character-leveling
- Consolidated into: dc-cr-xp-award-system (requirements covered in that feature's acceptance criteria)

## Happy Path

- [ ] `[NEW]` Characters can earn XP from encounters, hazards, and story milestones using the same progression ledger.
- [ ] `[NEW]` Reaching the configured level-up threshold (default 1,000 XP) triggers the character-leveling workflow instead of leaving XP in an unresolved state.
- [ ] `[NEW]` XP tracking remains aligned with the consolidated `dc-cr-xp-award-system` rules for award sources and threshold handling.
- [ ] `[NEW]` Party or campaign reward flows can identify which characters received XP and what source generated the reward.

## Edge Cases

- [ ] `[NEW]` XP progress across multiple rewards accumulates correctly until the threshold is crossed.
- [ ] `[NEW]` Rewards of 0 XP (for example trivial events under the broader system) are handled explicitly rather than silently ignored.
- [ ] `[NEW]` Characters already behind or ahead in XP state still level through the same validated threshold logic.

## Failure Modes

- [ ] `[NEW]` Invalid XP amounts or unknown reward-source types return validation errors.
- [ ] `[NEW]` Awarding XP to a character outside the active party/campaign context is blocked.

## Security acceptance criteria

- [ ] XP award writes require authenticated GM/system access; players can read progress but cannot mint XP for themselves.
- [ ] POST/PATCH XP award routes require `_csrf_request_header_mode: TRUE`.
- [ ] Server-side validation confirms the target characters belong to the active campaign/session before XP is applied.
- [ ] XP award logging records only the minimum campaign/session and character IDs required for traceability, with no unrelated PII.
- Agent: qa-dungeoncrawler
- Status: pending
