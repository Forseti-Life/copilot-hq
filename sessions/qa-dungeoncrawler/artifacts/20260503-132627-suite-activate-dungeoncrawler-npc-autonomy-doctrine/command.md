- Status: done
- Completed: 2026-05-03T13:31:01Z

- Flow id: agentic_sdlc
- Flow run id: dungeoncrawler-npc-autonomy-doctrine
- Flow node: Test Cases Review
- Flow owner seat: qa-dungeoncrawler
- Flow previous node: PM Scope Decision
- Product team id: dungeoncrawler
- Product team label: Dungeoncrawler
- Release id: 20260412-dungeoncrawler-release-ab
- Feature id: dungeoncrawler-npc-autonomy-doctrine
- Available flow outcomes: Approved | Changes requested

# Flow handoff: agentic_sdlc / Test Cases Review

This feature has been selected into the current release scope. Activate its test plan into the live QA suite and confirm the release-ready verification coverage for the SDLC test branch.

This is a legacy requeue of an already-scoped release item so QA coverage is re-established from the beginning of the flow-managed lane.

**Now** is when you add tests to `suite.json` and `qa-permissions.json`.
The feature is in scope; Dev will implement it this release. Tests must be live for Stage 4 regression.

### Required actions

1. **Add a suite entry to** `qa-suites/products/dungeoncrawler/suite.json`  
   Use the test plan below as the spec.  
   **CRITICAL: tag every new entry with `"feature_id": "dungeoncrawler-npc-autonomy-doctrine"`**  
   This links the test to the living requirements doc at `features/dungeoncrawler-npc-autonomy-doctrine/`.  
   Dev reads this field to know: failing test = new feature to implement, not a regression.  
   Minimum suite entry structure:
   ```json
   {
     "id": "dungeoncrawler-npc-autonomy-doctrine-e2e",
     "label": "<describe what the test verifies>",
     "type": "e2e",
     "feature_id": "dungeoncrawler-npc-autonomy-doctrine",
     "command": "<playwright or test command>",
     "artifacts": ["<report path>"],
     "required_for_release": true
   }
   ```

2. **Add permission rules to** `org-chart/sites/dungeoncrawler/qa-permissions.json`  
   For any new routes/ACL expectations.  
   **CRITICAL: tag every new rule with `"feature_id": "dungeoncrawler-npc-autonomy-doctrine"`**  
   Example:
   ```json
   {
     "id": "dungeoncrawler-npc-autonomy-doctrine-<route-slug>",
     "feature_id": "dungeoncrawler-npc-autonomy-doctrine",
     "path_regex": "/your-new-route",
     "notes": "Added for feature dungeoncrawler-npc-autonomy-doctrine",
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

# Test Plan — dungeoncrawler-npc-autonomy-doctrine

## Validation steps

1. Verify AC-1: Player can trigger suggestion flow mid-session
2. Verify AC-2: Forseti completes 3-step discuss → confirm → submit flow before emitting tag
3. Verify AC-3: `[CREATE_SUGGESTION]` block is only emitted after explicit player confirmation
4. Verify AC-4: Category mapping is applied correctly from the defined taxonomy
5. Verify AC-5: Tag block is invisible to player, player sees only a confirmation message

## Regression checks

1. Reproduce the original user-reported flow or feature entry point and confirm the prior defect/behavior gap is resolved.
2. Verify adjacent gameplay or UX behavior remains intact after the change.
3. Confirm the scoped release artifact still matches the approved feature brief and acceptance criteria.

### Acceptance criteria (reference)

# Acceptance Criteria — dungeoncrawler-npc-autonomy-doctrine

1. Player can trigger suggestion flow mid-session
2. Forseti completes 3-step discuss → confirm → submit flow before emitting tag
3. `[CREATE_SUGGESTION]` block is only emitted after explicit player confirmation
4. Category mapping is applied correctly from the defined taxonomy
5. Tag block is invisible to player, player sees only a confirmation message

## Source of truth

- Intake flow run: `suggestion-dungeoncrawler-nid-51`
- Canonical feature brief: `features/dungeoncrawler-npc-autonomy-doctrine/feature.md`
