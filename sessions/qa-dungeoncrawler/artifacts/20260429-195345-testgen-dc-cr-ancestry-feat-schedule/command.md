- Status: done
- Completed: 2026-04-29T20:52:40Z

# Test Plan Design: dc-cr-ancestry-feat-schedule

**From:** pm-dungeoncrawler  
**To:** qa-dungeoncrawler  
**Date:** 2026-04-29T19:53:45+00:00  

## Task

Design test cases for this feature and write the test plan spec.

**This is NEXT-RELEASE grooming work.** Do NOT edit the live product manifest `qa-suites/products/dungeoncrawler/suite.json` yet.
Instead, create a **feature-scoped suite overlay** at:
`qa-suites/products/dungeoncrawler/features/dc-cr-ancestry-feat-schedule.json`

That overlay is the runnable SoT for this feature during grooming. The live release manifest is compiled from selected overlays at Stage 0.

### Required outputs

1. **Create** `features/dc-cr-ancestry-feat-schedule/03-test-plan.md` — the test spec:
   - List every test case derived from the AC
   - For each: test description, which suite it will live in, expected HTTP status or behavior, roles covered
   - Flag any AC items that cannot be expressed as automation (note to PM)
2. **Create** `qa-suites/products/dungeoncrawler/features/dc-cr-ancestry-feat-schedule.json` from `templates/qa-feature-suite.json`:
   - Declare at least one runnable suite entry for this feature
   - Include `owner_seat`, `source_path`, `env_requirements`, and `release_checkpoint`
   - Point `test_plan` at `features/dc-cr-ancestry-feat-schedule/03-test-plan.md`
   - Validate with:
     ```bash
     python3 scripts/qa-suite-validate.py --product dungeoncrawler --feature-id dc-cr-ancestry-feat-schedule
     ```
2. **Signal completion:**
    ```bash
    ./scripts/qa-pm-testgen-complete.sh dungeoncrawler dc-cr-ancestry-feat-schedule "<brief summary>"
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

# Acceptance Criteria — dc-cr-ancestry-feat-schedule

- Feature: Ancestry Feat Schedule
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Define the ancestry-feat progression contract so character leveling grants ancestry feat slots at levels 1, 5, 9, 13, and 17, and the feat picker only offers ancestry feats the character is eligible to take at each milestone.

## Dependency checkpoints

- Dependencies: none explicitly listed in feature.md; QA should validate against the surrounding Core Rulebook chapter implementation.

## Happy Path

- [ ] `[NEW]` Characters receive ancestry feat selection opportunities at levels 1, 5, 9, 13, and 17 and not at unrelated levels.
- [ ] `[NEW]` At each ancestry-feat milestone, the picker allows any ancestry feat whose level is less than or equal to the character level and whose prerequisites are satisfied.
- [ ] `[NEW]` Previously selected ancestry feats remain attached to the character after later level-ups and do not get replaced when a new slot opens.
- [ ] `[NEW]` Level-up output clearly indicates when an ancestry feat is pending so QA can verify the milestone is visible in the character progression flow.

## Edge Cases

- [ ] `[NEW]` A character leveling through multiple milestones in one rebuild or import can fill each missing ancestry-feat slot in order.
- [ ] `[NEW]` An ancestry with no currently legal feat options reports a blocked selection state instead of offering invalid choices.
- [ ] `[NEW]` Retraining or rebuild flows recalculate ancestry feat eligibility from the current level and ancestry rather than leaving stale choices in place.

## Failure Modes

- [ ] `[NEW]` Submitting an ancestry feat above the character level or without prerequisites returns a validation error instead of being silently accepted.
- [ ] `[NEW]` A character cannot mutate ancestry-feat slots belonging to another character or campaign context.

## Security acceptance criteria

- [ ] Ancestry-feat selection endpoints require authenticated character-owner or GM access.
- [ ] POST/PATCH ancestry-feat mutation routes require `_csrf_request_header_mode: TRUE`.
- [ ] Server-side validation enforces ancestry, level, and prerequisite checks before persisting a feat choice.
- [ ] QA verifies a user cannot mutate ancestry-feat slots belonging to another character.
- Agent: qa-dungeoncrawler
- Status: pending
