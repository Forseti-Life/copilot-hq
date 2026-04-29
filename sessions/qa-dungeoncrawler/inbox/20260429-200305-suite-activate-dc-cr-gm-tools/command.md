# Suite Activation: dc-cr-gm-tools

**From:** pm-dungeoncrawler  
**To:** qa-dungeoncrawler  
**Date:** 2026-04-29T20:03:05+00:00  

## Task

This feature has been selected into the current release scope. Activate its test plan into the live QA suite.

**Now** is when you add tests to `suite.json` and `qa-permissions.json`.
The feature is in scope; Dev will implement it this release. Tests must be live for Stage 4 regression.

### Required actions

1. **Add a suite entry to** `qa-suites/products/dungeoncrawler/suite.json`  
   Use the test plan below as the spec.  
   **CRITICAL: tag every new entry with `"feature_id": "dc-cr-gm-tools"`**  
   This links the test to the living requirements doc at `features/dc-cr-gm-tools/`.  
   Dev reads this field to know: failing test = new feature to implement, not a regression.  
   Minimum suite entry structure:
   ```json
   {
     "id": "dc-cr-gm-tools-e2e",
     "label": "<describe what the test verifies>",
     "type": "e2e",
     "feature_id": "dc-cr-gm-tools",
     "command": "<playwright or test command>",
     "artifacts": ["<report path>"],
     "required_for_release": true
   }
   ```

2. **Add permission rules to** `org-chart/sites/dungeoncrawler.life/qa-permissions.json`  
   For any new routes/ACL expectations.  
   **CRITICAL: tag every new rule with `"feature_id": "dc-cr-gm-tools"`**  
   Example:
   ```json
   {
     "id": "dc-cr-gm-tools-<route-slug>",
     "feature_id": "dc-cr-gm-tools",
     "path_regex": "/your-new-route",
     "notes": "Added for feature dc-cr-gm-tools",
     "expect": { "anon": "...", "authenticated": "..." }
   }
   ```

3. **Validate the suite:**
   ```bash
   python3 scripts/qa-suite-validate.py
   ```

4. **Write outbox** confirming: how many entries added, feature_id tagged on each, suite validated, any gaps flagged.

### Test plan (written during grooming)

# Test Plan: dc-cr-gm-tools

## Coverage summary
- AC items: 9 (4 happy path, 3 edge cases, 2 failure modes)
- Test cases: 5 (TC-GMT-01-05)
- Suites: playwright (GM prep, encounter budgeting, access control)
- Security: GM-only prep or generation routes require authenticated GM/admin access and must not be exposed to anonymous or standard player roles.

---

## TC-GMT-01 — GM prep surface availability and scope
- Description: GM prep tooling exposes encounter budget guidance by party level/size and threat category (Trivial, Low, Moderate, Severe, Extreme).
- Suite: playwright/gm-prep
- Expected: GM prep tooling exposes encounter budget guidance by party level/size and threat category (Trivial, Low, Moderate, Severe, Extreme).
- AC: Happy Path-1

## TC-GMT-02 — Primary guidance and generation behavior
- Description: GM prep references include environment/terrain guidance, NPC stat-block structure, and loot-by-level lookup data required for session preparation.
- Suite: playwright/gm-prep
- Expected: GM prep references include environment/terrain guidance, NPC stat-block structure, and loot-by-level lookup data required for session preparation.; AI GM or GM-facing prep flows can retrieve the budgeting and reward data without requiring players to perform manual rules math.
- AC: Happy Path-2, Happy Path-3

## TC-GMT-03 — Data consumption by GM or AI GM flows
- Description: AI GM or GM-facing prep flows can retrieve the budgeting and reward data without requiring players to perform manual rules math.
- Suite: playwright/gm-prep
- Expected: AI GM or GM-facing prep flows can retrieve the budgeting and reward data without requiring players to perform manual rules math.; The feature contract identifies which outputs are read-only guidance and which are GM-triggered generation actions.
- AC: Happy Path-3, Happy Path-4

## TC-GMT-04 — Edge-case table and visibility handling
- Description: Encounter-budget calculations respond predictably when party size or level is outside the normal table range.
- Suite: playwright/gm-prep
- Expected: Encounter-budget calculations respond predictably when party size or level is outside the normal table range.; Loot generation handles missing or incomplete tables with an explicit validation/reporting path instead of producing nonsense rewards.; Player-facing surfaces do not expose GM-only prep details that would spoil encounters or hidden NPC data.
- AC: Edge Cases-1, Edge Cases-2, Edge Cases-3

## TC-GMT-05 — Validation errors and GM-only access control
- Description: Invalid party size, level, or threat-category inputs return a validation error rather than generating misleading encounter budgets.
- Suite: playwright/gm-prep
- Expected: Invalid party size, level, or threat-category inputs return a validation error rather than generating misleading encounter budgets.; GM-only routes return 403 for non-GM roles.
- AC: Failure Modes-1, Failure Modes-2

### Acceptance criteria (reference)

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
