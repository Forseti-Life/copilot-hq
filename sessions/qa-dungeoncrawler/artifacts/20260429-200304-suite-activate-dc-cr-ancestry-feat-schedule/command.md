- Status: done
- Completed: 2026-04-29T22:14:28Z

# Suite Activation: dc-cr-ancestry-feat-schedule

**From:** pm-dungeoncrawler  
**To:** qa-dungeoncrawler  
**Date:** 2026-04-29T20:03:04+00:00  

## Task

This feature has been selected into the current release scope. Activate its test plan into the live QA suite.

**Now** is when you add tests to `suite.json` and `qa-permissions.json`.
The feature is in scope; Dev will implement it this release. Tests must be live for Stage 4 regression.

### Required actions

1. **Add a suite entry to** `qa-suites/products/dungeoncrawler/suite.json`  
   Use the test plan below as the spec.  
   **CRITICAL: tag every new entry with `"feature_id": "dc-cr-ancestry-feat-schedule"`**  
   This links the test to the living requirements doc at `features/dc-cr-ancestry-feat-schedule/`.  
   Dev reads this field to know: failing test = new feature to implement, not a regression.  
   Minimum suite entry structure:
   ```json
   {
     "id": "dc-cr-ancestry-feat-schedule-e2e",
     "label": "<describe what the test verifies>",
     "type": "e2e",
     "feature_id": "dc-cr-ancestry-feat-schedule",
     "command": "<playwright or test command>",
     "artifacts": ["<report path>"],
     "required_for_release": true
   }
   ```

2. **Add permission rules to** `org-chart/sites/dungeoncrawler.life/qa-permissions.json`  
   For any new routes/ACL expectations.  
   **CRITICAL: tag every new rule with `"feature_id": "dc-cr-ancestry-feat-schedule"`**  
   Example:
   ```json
   {
     "id": "dc-cr-ancestry-feat-schedule-<route-slug>",
     "feature_id": "dc-cr-ancestry-feat-schedule",
     "path_regex": "/your-new-route",
     "notes": "Added for feature dc-cr-ancestry-feat-schedule",
     "expect": { "anon": "...", "authenticated": "..." }
   }
   ```

3. **Validate the suite:**
   ```bash
   python3 scripts/qa-suite-validate.py
   ```

4. **Write outbox** confirming: how many entries added, feature_id tagged on each, suite validated, any gaps flagged.

### Test plan (written during grooming)

# Test Plan: dc-cr-ancestry-feat-schedule

## Coverage summary
- AC items: 9 (4 happy path, 3 edge cases, 2 failure modes)
- Test cases: 5 (TC-AFS-01-05)
- Suites: playwright (character progression, rebuild/import, access control)
- Security: All ancestry-feat selection writes require authenticated character-owner or GM access.

---

## TC-AFS-01 — Milestone availability and slot gating
- Description: Characters receive ancestry feat selection opportunities at levels 1, 5, 9, 13, and 17 and not at unrelated levels.
- Suite: playwright/feat-progression
- Expected: Characters receive ancestry feat selection opportunities at levels 1, 5, 9, 13, and 17 and not at unrelated levels.
- AC: Happy Path-1

## TC-AFS-02 — Primary progression rule application
- Description: At each ancestry-feat milestone, the picker allows any ancestry feat whose level is less than or equal to the character level and whose prerequisites are satisfied.
- Suite: playwright/feat-progression
- Expected: At each ancestry-feat milestone, the picker allows any ancestry feat whose level is less than or equal to the character level and whose prerequisites are satisfied.; Previously selected ancestry feats remain attached to the character after later level-ups and do not get replaced when a new slot opens.
- AC: Happy Path-2, Happy Path-3

## TC-AFS-03 — Persistence and recalculation across level changes
- Description: Previously selected ancestry feats remain attached to the character after later level-ups and do not get replaced when a new slot opens.
- Suite: playwright/feat-progression
- Expected: Previously selected ancestry feats remain attached to the character after later level-ups and do not get replaced when a new slot opens.; Level-up output clearly indicates when an ancestry feat is pending so QA can verify the milestone is visible in the character progression flow.
- AC: Happy Path-3, Happy Path-4

## TC-AFS-04 — Edge-case rebuild and empty-option handling
- Description: A character leveling through multiple milestones in one rebuild or import can fill each missing ancestry-feat slot in order.
- Suite: playwright/feat-progression
- Expected: A character leveling through multiple milestones in one rebuild or import can fill each missing ancestry-feat slot in order.; An ancestry with no currently legal feat options reports a blocked selection state instead of offering invalid choices.; Retraining or rebuild flows recalculate ancestry feat eligibility from the current level and ancestry rather than leaving stale choices in place.
- AC: Edge Cases-1, Edge Cases-2, Edge Cases-3

## TC-AFS-05 — Validation, ownership, and invalid input handling
- Description: Submitting an ancestry feat above the character level or without prerequisites returns a validation error instead of being silently accepted.
- Suite: playwright/feat-progression
- Expected: Submitting an ancestry feat above the character level or without prerequisites returns a validation error instead of being silently accepted.; A character cannot mutate ancestry-feat slots belonging to another character or campaign context.
- AC: Failure Modes-1, Failure Modes-2

### Acceptance criteria (reference)

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
