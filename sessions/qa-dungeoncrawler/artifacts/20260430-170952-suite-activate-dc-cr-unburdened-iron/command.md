- Status: done
- Completed: 2026-04-30T17:12:28Z

- Flow id: agentic_sdlc
- Flow run id: dc-cr-unburdened-iron
- Flow node: Test Cases Review
- Flow owner seat: qa-dungeoncrawler
- Flow previous node: PM Scope Decision
- Product team id: dungeoncrawler
- Product team label: Dungeoncrawler
- Release id: 20260412-dungeoncrawler-release-z
- Feature id: dc-cr-unburdened-iron
- Available flow outcomes: Approved | Changes requested

# Flow handoff: agentic_sdlc / Test Cases Review

This feature has been selected into the current release scope. Activate its test plan into the live QA suite and confirm the release-ready verification coverage for the SDLC test branch.

This is a legacy requeue of an already-scoped release item so QA coverage is re-established from the beginning of the flow-managed lane.

**Now** is when you add tests to `suite.json` and `qa-permissions.json`.
The feature is in scope; Dev will implement it this release. Tests must be live for Stage 4 regression.

### Required actions

1. **Add a suite entry to** `qa-suites/products/dungeoncrawler/suite.json`  
   Use the test plan below as the spec.  
   **CRITICAL: tag every new entry with `"feature_id": "dc-cr-unburdened-iron"`**  
   This links the test to the living requirements doc at `features/dc-cr-unburdened-iron/`.  
   Dev reads this field to know: failing test = new feature to implement, not a regression.  
   Minimum suite entry structure:
   ```json
   {
     "id": "dc-cr-unburdened-iron-e2e",
     "label": "<describe what the test verifies>",
     "type": "e2e",
     "feature_id": "dc-cr-unburdened-iron",
     "command": "<playwright or test command>",
     "artifacts": ["<report path>"],
     "required_for_release": true
   }
   ```

2. **Add permission rules to** `org-chart/sites/dungeoncrawler/qa-permissions.json`  
   For any new routes/ACL expectations.  
   **CRITICAL: tag every new rule with `"feature_id": "dc-cr-unburdened-iron"`**  
   Example:
   ```json
   {
     "id": "dc-cr-unburdened-iron-<route-slug>",
     "feature_id": "dc-cr-unburdened-iron",
     "path_regex": "/your-new-route",
     "notes": "Added for feature dc-cr-unburdened-iron",
     "expect": { "anon": "...", "authenticated": "..." }
   }
   ```

3. **Validate the suite:**
   ```bash
   python3 scripts/qa-suite-validate.py
   ```

4. **Write outbox** confirming: how many entries added, feature_id tagged on each, suite validated, any gaps flagged.
   - If the test branch is ready to proceed, finish with `- Status: done` and `- Flow outcome: Approved`.
   - If QA finds the test branch incomplete or needing revision before release validation, finish with `- Status: done` and `- Flow outcome: Changes requested`.

### Test plan (written during grooming)

# Test Plan: dc-cr-unburdened-iron

## Coverage summary
- AC items: 9 (4 happy path, 3 edge cases, 2 failure modes)
- Test cases: 5 (TC-UBI-01-05)
- Suites: playwright (feat progression, equipment, speed calculation)
- Security: Security AC exemption: ancestry-feat and movement-math scope only; no new routes or input surfaces beyond existing feat assignment and movement handlers.

---

## TC-UBI-01 — Feat availability and prerequisite gating
- Description: Unburdened Iron exists as a level-1 dwarf ancestry feat.
- Suite: playwright/feat-progression
- Expected: Unburdened Iron exists as a level-1 dwarf ancestry feat.
- AC: Happy Path-1

## TC-UBI-02 — Primary granted benefit application
- Description: Worn armor no longer applies its Speed penalty to a character with the feat.
- Suite: playwright/encounter
- Expected: Worn armor no longer applies its Speed penalty to a character with the feat.; The largest single other Speed penalty affecting the character is reduced by 5 feet.
- AC: Happy Path-2, Happy Path-3

## TC-UBI-03 — Recalculation, retraining, and later progression behavior
- Description: The largest single other Speed penalty affecting the character is reduced by 5 feet.
- Suite: playwright/encounter
- Expected: The largest single other Speed penalty affecting the character is reduced by 5 feet.; Speed calculations remain deterministic when armor penalties and other penalties are combined.
- AC: Happy Path-3, Happy Path-4

## TC-UBI-04 — Edge-case rules interaction coverage
- Description: Only the largest non-armor penalty is reduced; multiple non-armor penalties are not each reduced by 5 feet.
- Suite: playwright/encounter
- Expected: Only the largest non-armor penalty is reduced; multiple non-armor penalties are not each reduced by 5 feet.; A character with no armor equipped still receives the largest-other-penalty reduction if one exists.; Speed can never become negative as a result of this adjustment logic.
- AC: Edge Cases-1, Edge Cases-2, Edge Cases-3

## TC-UBI-05 — Validation errors and malformed-data handling
- Description: Selecting the feat without a valid dwarf ancestry slot is rejected.
- Suite: playwright/encounter
- Expected: Selecting the feat without a valid dwarf ancestry slot is rejected.; Malformed speed modifiers do not crash movement calculations; they surface a validation issue instead.
- AC: Failure Modes-1, Failure Modes-2

### Acceptance criteria (reference)

# Acceptance Criteria — dc-cr-unburdened-iron

- Feature: Unburdened Iron (Dwarf Ancestry Feat)
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Turn Unburdened Iron into a QA-ready level-1 ancestry-feat contract for armor speed-penalty removal and the single-largest-other-penalty reduction rule.

## Dependency checkpoints

- Depends on: dc-cr-dwarf-ancestry, dc-cr-ancestry-feat-schedule, dc-cr-equipment-system
- Merged into: dc-cr-dwarf-ancestry (all heritages and ancestry feats covered in bulk AC)

## Happy Path

- [ ] `[NEW]` Unburdened Iron exists as a level-1 dwarf ancestry feat.
- [ ] `[NEW]` Worn armor no longer applies its Speed penalty to a character with the feat.
- [ ] `[NEW]` The largest single other Speed penalty affecting the character is reduced by 5 feet.
- [ ] `[NEW]` Speed calculations remain deterministic when armor penalties and other penalties are combined.

## Edge Cases

- [ ] `[NEW]` Only the largest non-armor penalty is reduced; multiple non-armor penalties are not each reduced by 5 feet.
- [ ] `[NEW]` A character with no armor equipped still receives the largest-other-penalty reduction if one exists.
- [ ] `[NEW]` Speed can never become negative as a result of this adjustment logic.

## Failure Modes

- [ ] `[NEW]` Selecting the feat without a valid dwarf ancestry slot is rejected.
- [ ] `[NEW]` Malformed speed modifiers do not crash movement calculations; they surface a validation issue instead.

## Security acceptance criteria

- Security AC exemption: ancestry-feat and movement-math scope only; no new routes or input surfaces beyond existing feat assignment and movement handlers.
