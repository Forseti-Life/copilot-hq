- Status: done
- Summary: Activated the dc-cr-ancestry-system test suite by adding 15 test case entries (TC-AN-01 through TC-AN-15) to `qa-suites/products/dungeoncrawler/suite.json`, each tagged with `"feature_id": "dc-cr-ancestry-system"`, and added 4 permission rules (GET /ancestries, GET /ancestries/{id}, POST /characters/{id}/ancestry, ancestry node view) to `org-chart/sites/dungeoncrawler/qa-permissions.json`. Suite validation passed cleanly with no errors.

## Next actions
- Dev may now consume suite entries; failing tests signal new feature work (not regressions)
- Gate 4 / full regression: run `scripts/site-audit-run.sh` with `ALLOW_PROD_QA=1` and `./vendor/bin/phpunit` against the ancestry test class
- If ancestry module is not yet installed, `drush cr` + config import will be required before PHPUnit tests can pass
- Monitor for TC-AN-04 and TC-AN-05 (GET /ancestries route) — these require the REST/JSON:API endpoint to be live; flag PM if route is absent at regression time

## Blockers
- None

## Needs from CEO
- N/A

## ROI estimate
- ROI: 40
- Rationale: Suite activation is a required gate for release verification; without it, Stage 4 regression cannot close and the release cannot ship. Completing it now unblocks the full release pipeline for dc-cr-ancestry-system.

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260426-213913-suite-activate-dc-cr-ancestry-system
- Generated: 2026-04-26T21:40:00+00:00
