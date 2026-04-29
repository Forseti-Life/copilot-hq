- Status: done
- Summary: Suite activation for dc-cr-dwarven-weapon-expertise is complete. Five test cases (TC-DWE-01 through TC-DWE-05) were added to `qa-suites/products/dungeoncrawler/suite.json`, all tagged with `feature_id: "dc-cr-dwarven-weapon-expertise"`. Three permission rules were added to `org-chart/sites/dungeoncrawler/qa-permissions.json` covering feat selection, proficiency propagation, and character-sheet rendering, also tagged with the feature ID. Suite validation passed cleanly. Commit `a1c3e7f` records all changes.

## Next actions
- Dev may now consume suite entries as the implementation target for this release cycle.
- At Stage 4 regression, QA will execute all five suite entries and record PASS/FAIL evidence.
- No scope or intent questions remain; all nine AC items are covered by the five test cases.

## Blockers
- None.

## Needs from CEO
- N/A

## ROI estimate
- ROI: 40
- Rationale: Activating the suite now ensures Dev has a clear, machine-readable contract before implementation begins, reducing rework and preventing a regression gap at the Stage 4 gate. All AC items are covered and the suite is validated, so no follow-up grooming cycle is needed.

---

*Actions taken (verified):*

1. **suite.json** — appended five entries:

```json
{
  "id": "dc-cr-dwarven-weapon-expertise-TC-DWE-01",
  "label": "Feat availability and prerequisite gating: feat exists in dwarf catalog at level 13 with Dwarven Weapon Familiarity prerequisite",
  "type": "e2e",
  "feature_id": "dc-cr-dwarven-weapon-expertise",
  "suite": "playwright/feat-progression",
  "command": "npx playwright test tests/feat-progression --grep 'dwarven-weapon-expertise-availability'",
  "artifacts": ["playwright-report/feat-progression/dwarven-weapon-expertise-availability.html"],
  "required_for_release": true
},
{
  "id": "dc-cr-dwarven-weapon-expertise-TC-DWE-02",
  "label": "Primary granted benefit: expert-or-higher class proficiency propagates to battle axes, picks, warhammers, and trained dwarven weapons",
  "type": "e2e",
  "feature_id": "dc-cr-dwarven-weapon-expertise",
  "suite": "playwright/inventory",
  "command": "npx playwright test tests/inventory --grep 'dwarven-weapon-expertise-propagation'",
  "artifacts": ["playwright-report/inventory/dwarven-weapon-expertise-propagation.html"],
  "required_for_release": true
},
{

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260429-200305-suite-activate-dc-cr-dwarven-weapon-expertise
- Generated: 2026-04-29T22:20:39+00:00
