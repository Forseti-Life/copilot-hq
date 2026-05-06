- Status: done
- Completed: 2026-05-01T14:24:38Z

- Flow id: agentic_sdlc
- Flow run id: dc-cr-rituals
- Flow node: Test Cases Review
- Flow owner seat: qa-dungeoncrawler
- Flow previous node: PM Scope Decision
- Product team id: dungeoncrawler
- Product team label: Dungeoncrawler
- Release id: 20260412-dungeoncrawler-release-aa
- Feature id: dc-cr-rituals
- Available flow outcomes: Approved | Changes requested

# Flow handoff: agentic_sdlc / Test Cases Review

This feature has been selected into the current release scope. Activate its test plan into the live QA suite and confirm the release-ready verification coverage for the SDLC test branch.

This is a legacy requeue of an already-scoped release item so QA coverage is re-established from the beginning of the flow-managed lane.

**Now** is when you add tests to `suite.json` and `qa-permissions.json`.
The feature is in scope; Dev will implement it this release. Tests must be live for Stage 4 regression.

### Required actions

1. **Add a suite entry to** `qa-suites/products/dungeoncrawler/suite.json`  
   Use the test plan below as the spec.  
   **CRITICAL: tag every new entry with `"feature_id": "dc-cr-rituals"`**  
   This links the test to the living requirements doc at `features/dc-cr-rituals/`.  
   Dev reads this field to know: failing test = new feature to implement, not a regression.  
   Minimum suite entry structure:
   ```json
   {
     "id": "dc-cr-rituals-e2e",
     "label": "<describe what the test verifies>",
     "type": "e2e",
     "feature_id": "dc-cr-rituals",
     "command": "<playwright or test command>",
     "artifacts": ["<report path>"],
     "required_for_release": true
   }
   ```

2. **Add permission rules to** `org-chart/sites/dungeoncrawler/qa-permissions.json`  
   For any new routes/ACL expectations.  
   **CRITICAL: tag every new rule with `"feature_id": "dc-cr-rituals"`**  
   Example:
   ```json
   {
     "id": "dc-cr-rituals-<route-slug>",
     "feature_id": "dc-cr-rituals",
     "path_regex": "/your-new-route",
     "notes": "Added for feature dc-cr-rituals",
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

# Test Plan: dc-cr-rituals

## Coverage summary
- AC items: 9 (4 happy path, 3 edge cases, 2 failure modes)
- Test cases: 5 (TC-RTL-01-05)
- Suites: playwright (ritual casting, participant validation, campaign actions)
- Security: Security AC exemption: spellcasting/rules-engine scope only; no new public routes expected beyond existing spellcasting, downtime, or session-action handlers.
- Existing implementation seed: `CharacterManager::RITUALS` provides ritual catalog fixtures; tests should focus on execution flow and validation gaps rather than re-proving catalog ingestion.

---

## TC-RTL-01 — Feature availability and subsystem entry points
- Description: Rituals are represented separately from standard spellcasting and do not consume prepared spell slots or spontaneous spell slots.
- Suite: playwright/rituals
- Expected: Rituals are represented separately from standard spellcasting and do not consume prepared spell slots or spontaneous spell slots.
- AC: Happy Path-1

## TC-RTL-02 — Primary subsystem rule resolution
- Description: A ritual definition captures casting time, primary caster requirements, optional/required secondary casters, and the relevant skill checks.
- Suite: playwright/rituals
- Expected: A ritual definition captures casting time, primary caster requirements, optional/required secondary casters, and the relevant skill checks.; Ritual execution supports success, failure, and critical-failure outcomes with explicit consequences.
- AC: Happy Path-2, Happy Path-3

## TC-RTL-03 — State recovery, caps, or long-running flow handling
- Description: Ritual execution supports success, failure, and critical-failure outcomes with explicit consequences.
- Suite: playwright/rituals
- Expected: Ritual execution supports success, failure, and critical-failure outcomes with explicit consequences.; Rituals can be surfaced as campaign-scale actions without being mixed into everyday encounter spellcasting UI.
- AC: Happy Path-3, Happy Path-4

## TC-RTL-04 — Edge-case subsystem coverage
- Description: Rituals with long casting times (minutes to days) preserve progress and requirements across the full casting window.
- Suite: playwright/rituals
- Expected: Rituals with long casting times (minutes to days) preserve progress and requirements across the full casting window.; Insufficient or invalid secondary casters block ritual completion with a clear validation path.; Narrative-only or partially manual ritual consequences are identified so QA can separate automation from manual verification.
- AC: Edge Cases-1, Edge Cases-2, Edge Cases-3

## TC-RTL-05 — Validation errors and wrong-surface rejection handling
- Description: Attempting to cast a ritual through the normal spellcasting action flow is rejected.
- Suite: playwright/rituals
- Expected: Attempting to cast a ritual through the normal spellcasting action flow is rejected.; Missing required skill-check metadata or ritual participants fails validation rather than creating a partially resolved ritual.
- AC: Failure Modes-1, Failure Modes-2

### Acceptance criteria (reference)

# Acceptance Criteria — dc-cr-rituals

- Feature: Ritual Magic System
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Define ritual magic as a separate QA-ready subsystem contract covering long casting times, caster roles, skill checks, and non-slot failure consequences for narrative-scale magic.

## Dependency checkpoints

- Related parent: dc-cr-spells-ch07 shipped the spell catalog, but ritual execution remains standalone scope.
- Existing seed asset: `CharacterManager::RITUALS` already contains CRB/APG ritual definitions and participant metadata for integration reuse.

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
