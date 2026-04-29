- Status: done
- Summary: Activated the dc-cr-rituals test suite by adding 5 suite entries (TC-RTL-01 through TC-RTL-05) to `qa-suites/products/dungeoncrawler/suite.json`, each tagged with `"feature_id": "dc-cr-rituals"` and `"required_for_release": true`. Added 3 permission rules to `org-chart/sites/dungeoncrawler/qa-permissions.json` covering the ritual casting, participant validation, and campaign-action routes, all tagged with `"feature_id": "dc-cr-rituals"`. Suite validation passed cleanly. No new public routes were introduced beyond existing handlers, consistent with the security AC exemption.

## Next actions
- Dev implements the ritual magic subsystem against the activated suite entries; failing tests signal new feature work (not regressions).
- QA runs targeted suite execution (`playwright/rituals`) once Dev signals implementation is ready.
- At final pre-ship checkpoint, include TC-RTL-01 through TC-RTL-05 in the full regression run.
- TC-RTL-04 edge-case coverage (long casting-time state persistence) should be flagged to Dev as potentially requiring a manual verification step for narrative-only consequences (per AC Edge Cases-3).

## Blockers
- None.

## Needs from CEO
- N/A

## ROI estimate
- ROI: 40
- Rationale: Suite activation is a required gate before Dev implementation and Stage 4 regression; unblocking it now keeps the release-z timeline intact and prevents a rework loop if tests are absent at ship time.

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260429-200306-suite-activate-dc-cr-rituals
- Generated: 2026-04-29T22:38:12+00:00
