# Suite Activation: dc-cr-xp-rewards

**From:** pm-dungeoncrawler  
**To:** qa-dungeoncrawler  
**Date:** 2026-04-29T20:03:06+00:00  

## Task

This feature has been selected into the current release scope. Activate its test plan into the live QA suite.

**Now** is when you add tests to `suite.json` and `qa-permissions.json`.
The feature is in scope; Dev will implement it this release. Tests must be live for Stage 4 regression.

### Required actions

1. **Add a suite entry to** `qa-suites/products/dungeoncrawler/suite.json`  
   Use the test plan below as the spec.  
   **CRITICAL: tag every new entry with `"feature_id": "dc-cr-xp-rewards"`**  
   This links the test to the living requirements doc at `features/dc-cr-xp-rewards/`.  
   Dev reads this field to know: failing test = new feature to implement, not a regression.  
   Minimum suite entry structure:
   ```json
   {
     "id": "dc-cr-xp-rewards-e2e",
     "label": "<describe what the test verifies>",
     "type": "e2e",
     "feature_id": "dc-cr-xp-rewards",
     "command": "<playwright or test command>",
     "artifacts": ["<report path>"],
     "required_for_release": true
   }
   ```

2. **Add permission rules to** `org-chart/sites/dungeoncrawler.life/qa-permissions.json`  
   For any new routes/ACL expectations.  
   **CRITICAL: tag every new rule with `"feature_id": "dc-cr-xp-rewards"`**  
   Example:
   ```json
   {
     "id": "dc-cr-xp-rewards-<route-slug>",
     "feature_id": "dc-cr-xp-rewards",
     "path_regex": "/your-new-route",
     "notes": "Added for feature dc-cr-xp-rewards",
     "expect": { "anon": "...", "authenticated": "..." }
   }
   ```

3. **Validate the suite:**
   ```bash
   python3 scripts/qa-suite-validate.py
   ```

4. **Write outbox** confirming: how many entries added, feature_id tagged on each, suite validated, any gaps flagged.

### Test plan (written during grooming)

# Test Plan: dc-cr-xp-rewards

## Coverage summary
- AC items: 9 (4 happy path, 3 edge cases, 2 failure modes)
- Test cases: 5 (TC-XPR-01-05)
- Suites: playwright (reward ledger, level-up threshold, access control)
- Security: XP award writes require authenticated GM/system access; players can read progress but cannot mint XP for themselves.

---

## TC-XPR-01 — Milestone availability and slot gating
- Description: Characters can earn XP from encounters, hazards, and story milestones using the same progression ledger.
- Suite: playwright/progression
- Expected: Characters can earn XP from encounters, hazards, and story milestones using the same progression ledger.
- AC: Happy Path-1

## TC-XPR-02 — Primary progression rule application
- Description: Reaching the configured level-up threshold (default 1,000 XP) triggers the character-leveling workflow instead of leaving XP in an unresolved state.
- Suite: playwright/progression
- Expected: Reaching the configured level-up threshold (default 1,000 XP) triggers the character-leveling workflow instead of leaving XP in an unresolved state.; XP tracking remains aligned with the consolidated `dc-cr-xp-award-system` rules for award sources and threshold handling.
- AC: Happy Path-2, Happy Path-3

## TC-XPR-03 — Persistence and recalculation across level changes
- Description: XP tracking remains aligned with the consolidated `dc-cr-xp-award-system` rules for award sources and threshold handling.
- Suite: playwright/progression
- Expected: XP tracking remains aligned with the consolidated `dc-cr-xp-award-system` rules for award sources and threshold handling.; Party or campaign reward flows can identify which characters received XP and what source generated the reward.
- AC: Happy Path-3, Happy Path-4

## TC-XPR-04 — Edge-case rebuild and empty-option handling
- Description: XP progress across multiple rewards accumulates correctly until the threshold is crossed.
- Suite: playwright/progression
- Expected: XP progress across multiple rewards accumulates correctly until the threshold is crossed.; Rewards of 0 XP (for example trivial events under the broader system) are handled explicitly rather than silently ignored.; Characters already behind or ahead in XP state still level through the same validated threshold logic.
- AC: Edge Cases-1, Edge Cases-2, Edge Cases-3

## TC-XPR-05 — Validation, ownership, and invalid input handling
- Description: Invalid XP amounts or unknown reward-source types return validation errors.
- Suite: playwright/progression
- Expected: Invalid XP amounts or unknown reward-source types return validation errors.; Awarding XP to a character outside the active party/campaign context is blocked.
- AC: Failure Modes-1, Failure Modes-2

### Acceptance criteria (reference)

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
