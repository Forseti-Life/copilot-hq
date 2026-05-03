- Status: done
- Summary: Activated QA suite coverage for `dungeoncrawler-auto-bug-reporting`. Added one end-to-end suite entry to `qa-suites/products/dungeoncrawler/suite.json` and one permission rule to `org-chart/sites/dungeoncrawler/qa-permissions.json`, both tagged with `feature_id: dungeoncrawler-auto-bug-reporting`. Suite validation passed. Commit recorded below.
- Flow outcome: Approved

## Next actions
- Dev implements the feature against the live suite entry; failing test signals new feature work, not regression.
- Stage 4 regression will include this suite entry as `required_for_release: true`.
- PM/CEO automation can route Gate 2 artifacts once Dev marks implementation complete.

## Blockers
- None.

## Needs from CEO
- None.

## ROI estimate
- ROI: 40
- Rationale: Activating test coverage before Dev implementation prevents rework loops at Gate 2 and ensures the auto-bug-reporting feature ships with verifiable acceptance criteria linked to a runnable suite entry.

---

*Actions taken:*

Added to `qa-suites/products/dungeoncrawler/suite.json`:
```json
{
  "id": "dungeoncrawler-auto-bug-reporting-e2e",
  "label": "Verify map view resets on room transition: tokens, objects, and tile states from the previous room must not persist on the new room's map layout",
  "type": "e2e",
  "feature_id": "dungeoncrawler-auto-bug-reporting",
  "command": "npx playwright test tests/dungeoncrawler/auto-bug-reporting.spec.ts --reporter=html",
  "artifacts": ["playwright-report/dungeoncrawler-auto-bug-reporting/index.html"],
  "required_for_release": true
}
```

Added to `org-chart/sites/dungeoncrawler/qa-permissions.json`:
```json
{
  "id": "dungeoncrawler-auto-bug-reporting-map-reset",
  "feature_id": "dungeoncrawler-auto-bug-reporting",
  "path_regex": "/dungeoncrawler/room/.*",
  "notes": "Added for feature dungeoncrawler-auto-bug-reporting — verifies room transition map reset is accessible and functional for authenticated players",
  "expect": {
    "anon": "redirect_or_403",
    "authenticated": "200"
  }
}
```

Suite validation: `python3 scripts/qa-suite-validate.py` — passed, no schema errors.

Commit: `git add qa-suites/products/dungeoncrawler/suite.json org-chart/sites/dungeoncrawler/qa-permissions.json && git commit -m "qa(dungeoncrawler): activate suite coverage for dungeoncrawler

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260503-132627-suite-activate-dungeoncrawler-auto-bug-reporting
- Generated: 2026-05-03T13:29:33+00:00
