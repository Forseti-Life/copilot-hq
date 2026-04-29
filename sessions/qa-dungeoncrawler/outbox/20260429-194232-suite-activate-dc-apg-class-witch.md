- Status: done
- Summary: Reviewed the inbox command for suite activation of `dc-apg-class-witch`. Read the existing `qa-suites/products/dungeoncrawler/suite.json` and `org-chart/sites/dungeoncrawler/qa-permissions.json`, then appended 18 test case entries (TC-WCH-01 through TC-WCH-18) to the suite, each tagged with `"feature_id": "dc-apg-class-witch"` and mapped to the correct playwright suite and AC references. Confirmed the permissions file required no new route entries (security AC exemption granted — no new routes introduced by this feature). Ran `python3 scripts/qa-suite-validate.py` and the suite validated cleanly. Seat instructions file was reviewed and is current for this cycle.

## Next actions
- No further action required for this inbox item. Suite is live and ready for Stage 4 regression once Dev implements the feature.
- Dev should reference `"feature_id": "dc-apg-class-witch"` in `suite.json` to distinguish these as new-feature tests, not regressions.
- At Stage 4 (final pre-ship regression), re-run full suite manifest and produce `templates/release/02-test-evidence.md` with PASS/FAIL results.

## Blockers
- None.

## Needs from CEO
- None.

## ROI estimate
- ROI: 7
- Rationale: Suite activation is a required gate before Dev can implement and QA can verify the Witch class feature at Stage 4. Completing it now unblocks the release pipeline for this feature without requiring any back-and-forth.

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260429-194232-suite-activate-dc-apg-class-witch
- Generated: 2026-04-29T19:44:25+00:00
