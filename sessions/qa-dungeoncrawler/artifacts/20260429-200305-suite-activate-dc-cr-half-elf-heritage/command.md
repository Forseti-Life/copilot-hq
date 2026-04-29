- Status: done
- Completed: 2026-04-29T22:32:37Z

# Suite Activation: dc-cr-half-elf-heritage

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
   **CRITICAL: tag every new entry with `"feature_id": "dc-cr-half-elf-heritage"`**  
   This links the test to the living requirements doc at `features/dc-cr-half-elf-heritage/`.  
   Dev reads this field to know: failing test = new feature to implement, not a regression.  
   Minimum suite entry structure:
   ```json
   {
     "id": "dc-cr-half-elf-heritage-e2e",
     "label": "<describe what the test verifies>",
     "type": "e2e",
     "feature_id": "dc-cr-half-elf-heritage",
     "command": "<playwright or test command>",
     "artifacts": ["<report path>"],
     "required_for_release": true
   }
   ```

2. **Add permission rules to** `org-chart/sites/dungeoncrawler.life/qa-permissions.json`  
   For any new routes/ACL expectations.  
   **CRITICAL: tag every new rule with `"feature_id": "dc-cr-half-elf-heritage"`**  
   Example:
   ```json
   {
     "id": "dc-cr-half-elf-heritage-<route-slug>",
     "feature_id": "dc-cr-half-elf-heritage",
     "path_regex": "/your-new-route",
     "notes": "Added for feature dc-cr-half-elf-heritage",
     "expect": { "anon": "...", "authenticated": "..." }
   }
   ```

3. **Validate the suite:**
   ```bash
   python3 scripts/qa-suite-validate.py
   ```

4. **Write outbox** confirming: how many entries added, feature_id tagged on each, suite validated, any gaps flagged.

### Test plan (written during grooming)

# Test Plan: dc-cr-half-elf-heritage

## Coverage summary
- AC items: 9 (4 happy path, 3 edge cases, 2 failure modes)
- Test cases: 5 (TC-HEF-01-05)
- Suites: playwright (character creation, ancestry feat picker, validation)
- Security: Security AC exemption: ancestry heritage and feat-eligibility scope only; no new routes or input surfaces beyond existing heritage assignment and ancestry-feat handlers.

---

## TC-HEF-01 — Heritage availability and ancestry gating
- Description: Half-Elf is implemented as a selectable Human heritage rather than a standalone ancestry.
- Suite: playwright/character-creation
- Expected: Half-Elf is implemented as a selectable Human heritage rather than a standalone ancestry.
- AC: Happy Path-1

## TC-HEF-02 — Primary passive effect application
- Description: Selecting the heritage grants the elf trait, the half-elf trait, and low-light vision.
- Suite: playwright/character-creation
- Expected: Selecting the heritage grants the elf trait, the half-elf trait, and low-light vision.; Ancestry-feat selection for a Half-Elf character can draw from human, elf, and half-elf feat pools while still enforcing feat prerequisites.
- AC: Happy Path-2, Happy Path-3

## TC-HEF-03 — Scaling, automation, and visible state updates
- Description: Ancestry-feat selection for a Half-Elf character can draw from human, elf, and half-elf feat pools while still enforcing feat prerequisites.
- Suite: playwright/feat-progression
- Expected: Ancestry-feat selection for a Half-Elf character can draw from human, elf, and half-elf feat pools while still enforcing feat prerequisites.; The expanded feat-pool behavior is visible anywhere the character gains an ancestry feat slot.
- AC: Happy Path-3, Happy Path-4

## TC-HEF-04 — Edge-case rules interaction coverage
- Description: If the character already has low-light vision from another valid source, the heritage does not create duplicate sense flags.
- Suite: playwright/feat-progression
- Expected: If the character already has low-light vision from another valid source, the heritage does not create duplicate sense flags.; Half-Elf remains mutually exclusive with other Human heritages.; Feat browsing clearly indicates why an elf, half-elf, or human feat is or is not selectable for the current character.
- AC: Edge Cases-1, Edge Cases-2, Edge Cases-3

## TC-HEF-05 — Validation errors and safe fallback behavior
- Description: Non-Human characters cannot select the Half-Elf heritage.
- Suite: playwright/feat-progression
- Expected: Non-Human characters cannot select the Half-Elf heritage.; The feat picker rejects ancestry feats outside the allowed human/elf/half-elf pools instead of silently accepting them.
- AC: Failure Modes-1, Failure Modes-2

### Acceptance criteria (reference)

# Acceptance Criteria — dc-cr-half-elf-heritage

- Feature: Half-Elf Heritage
- Release target: 20260412-dungeoncrawler-release-z
- PM owner: pm-dungeoncrawler
- Date groomed: 2026-04-29

## Scope

Define Half-Elf heritage as a Human heritage overlay contract covering trait grants, low-light vision, and the expanded ancestry-feat pool used at later feat milestones.

## Dependency checkpoints

- Depends on: dc-cr-human-ancestry, dc-cr-heritage-system, dc-cr-low-light-vision, dc-cr-ancestry-feat-schedule

## Happy Path

- [ ] `[NEW]` Half-Elf is implemented as a selectable Human heritage rather than a standalone ancestry.
- [ ] `[NEW]` Selecting the heritage grants the elf trait, the half-elf trait, and low-light vision.
- [ ] `[NEW]` Ancestry-feat selection for a Half-Elf character can draw from human, elf, and half-elf feat pools while still enforcing feat prerequisites.
- [ ] `[NEW]` The expanded feat-pool behavior is visible anywhere the character gains an ancestry feat slot.

## Edge Cases

- [ ] `[NEW]` If the character already has low-light vision from another valid source, the heritage does not create duplicate sense flags.
- [ ] `[NEW]` Half-Elf remains mutually exclusive with other Human heritages.
- [ ] `[NEW]` Feat browsing clearly indicates why an elf, half-elf, or human feat is or is not selectable for the current character.

## Failure Modes

- [ ] `[NEW]` Non-Human characters cannot select the Half-Elf heritage.
- [ ] `[NEW]` The feat picker rejects ancestry feats outside the allowed human/elf/half-elf pools instead of silently accepting them.

## Security acceptance criteria

- Security AC exemption: ancestry heritage and feat-eligibility scope only; no new routes or input surfaces beyond existing heritage assignment and ancestry-feat handlers.
- Agent: qa-dungeoncrawler
- Status: pending
