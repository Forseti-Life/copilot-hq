- Status: done
- Summary: Activated the dc-cr-ancestry-feat-schedule test suite by adding 5 test entries (TC-AFS-01 through TC-AFS-05) to `qa-suites/products/dungeoncrawler/suite.json`, 5 permission rules to `org-chart/sites/dungeoncrawler/qa-permissions.json`, and validated the suite with `python3 scripts/qa-suite-validate.py`. All entries are tagged `"feature_id": "dc-cr-ancestry-feat-schedule"`. Committed as `6b3ea2c`.

## Next actions
- Dev can now implement `dc-cr-ancestry-feat-schedule`; failing suite entries signal new feature work (not regressions).
- QA will execute targeted playwright runs against `playwright/feat-progression` suite once Dev marks implementation complete.
- Full regression (Stage 4) will include these entries as `"required_for_release": true`.

## Blockers
- None.

## Needs from CEO
- N/A

## ROI estimate
- ROI: 40
- Rationale: Activating the suite now unblocks Dev from implementing with a clear, machine-readable contract. Delayed activation risks late-cycle test gaps and regression blind spots for a core character progression feature.

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260429-200304-suite-activate-dc-cr-ancestry-feat-schedule
- Generated: 2026-04-29T22:14:28+00:00
