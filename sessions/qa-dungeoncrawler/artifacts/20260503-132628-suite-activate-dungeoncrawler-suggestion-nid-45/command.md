- Status: done
- Completed: 2026-05-03T14:08:48Z

- Flow id: agentic_sdlc
- Flow run id: dungeoncrawler-suggestion-nid-45
- Flow node: Test Cases Review
- Flow owner seat: qa-dungeoncrawler
- Flow previous node: PM Scope Decision
- Product team id: dungeoncrawler
- Product team label: Dungeoncrawler
- Release id: 20260412-dungeoncrawler-release-ab
- Feature id: dungeoncrawler-suggestion-nid-45
- Available flow outcomes: Approved | Changes requested

# Flow handoff: agentic_sdlc / Test Cases Review

This feature has been selected into the current release scope. Activate its test plan into the live QA suite and confirm the release-ready verification coverage for the SDLC test branch.

This is a legacy requeue of an already-scoped release item so QA coverage is re-established from the beginning of the flow-managed lane.

**Now** is when you add tests to `suite.json` and `qa-permissions.json`.
The feature is in scope; Dev will implement it this release. Tests must be live for Stage 4 regression.

### Required actions

1. **Add a suite entry to** `qa-suites/products/dungeoncrawler/suite.json`  
   Use the test plan below as the spec.  
   **CRITICAL: tag every new entry with `"feature_id": "dungeoncrawler-suggestion-nid-45"`**  
   This links the test to the living requirements doc at `features/dungeoncrawler-suggestion-nid-45/`.  
   Dev reads this field to know: failing test = new feature to implement, not a regression.  
   Minimum suite entry structure:
   ```json
   {
     "id": "dungeoncrawler-suggestion-nid-45-e2e",
     "label": "<describe what the test verifies>",
     "type": "e2e",
     "feature_id": "dungeoncrawler-suggestion-nid-45",
     "command": "<playwright or test command>",
     "artifacts": ["<report path>"],
     "required_for_release": true
   }
   ```

2. **Add permission rules to** `org-chart/sites/dungeoncrawler/qa-permissions.json`  
   For any new routes/ACL expectations.  
   **CRITICAL: tag every new rule with `"feature_id": "dungeoncrawler-suggestion-nid-45"`**  
   Example:
   ```json
   {
     "id": "dungeoncrawler-suggestion-nid-45-<route-slug>",
     "feature_id": "dungeoncrawler-suggestion-nid-45",
     "path_regex": "/your-new-route",
     "notes": "Added for feature dungeoncrawler-suggestion-nid-45",
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

# Test Plan — dungeoncrawler-suggestion-nid-45

## Validation steps

1. Verify AC-1: When an NPC issues a quest via in-world dialogue, the quest objective should be automatically created and logged to the player's active quest tracker. Currently, quest briefings delivered through NPC conversation are not persisting as tracked objectives, leaving players without a formal mission record after the conversation ends.
2. Verify AC-2: The change preserves adjacent gameplay behavior and does not regress the surrounding user flow.
3. Verify AC-3: QA can verify the original report or requested behavior directly in the live Dungeoncrawler/Forseti experience.

## Regression checks

1. Reproduce the original user-reported flow or feature entry point and confirm the prior defect/behavior gap is resolved.
2. Verify adjacent gameplay or UX behavior remains intact after the change.
3. Confirm the scoped release artifact still matches the approved feature brief and acceptance criteria.

### Acceptance criteria (reference)

# Acceptance Criteria — dungeoncrawler-suggestion-nid-45

1. When an NPC issues a quest via in-world dialogue, the quest objective should be automatically created and logged to the player's active quest tracker. Currently, quest briefings delivered through NPC conversation are not persisting as tracked objectives, leaving players without a formal mission record after the conversation ends.
2. The change preserves adjacent gameplay behavior and does not regress the surrounding user flow.
3. QA can verify the original report or requested behavior directly in the live Dungeoncrawler/Forseti experience.

## Source of truth

- Intake flow run: `suggestion-dungeoncrawler-nid-45`
- Canonical feature brief: `features/dungeoncrawler-suggestion-nid-45/feature.md`
- Agent: qa-dungeoncrawler
- Status: pending
