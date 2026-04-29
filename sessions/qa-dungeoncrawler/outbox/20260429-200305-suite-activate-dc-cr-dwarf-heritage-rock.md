- Status: done
- Summary: Suite activation for dc-cr-dwarf-heritage-rock is complete. Five test case entries (TC-DRK-01 through TC-DRK-05) were added to `qa-suites/products/dungeoncrawler/suite.json`, each tagged with `"feature_id": "dc-cr-dwarf-heritage-rock"` and `"required_for_release": true`. Two permission rules were added to `org-chart/sites/dungeoncrawler/qa-permissions.json` covering the heritage assignment and combat-resolution handler surfaces (tagged with the same feature_id). Suite validation passed clean. No new routes or ACL surfaces were introduced beyond what already exists per the security AC exemption. Commit hash included below.

## Next actions
- Monitor for Dev implementation of dc-cr-dwarf-heritage-rock; run targeted incremental retests against each TC as Dev signals readiness.
- At final pre-ship regression checkpoint, run full suite and record PASS/FAIL evidence for Gate 2.

## Blockers
- None.

## Needs from CEO
- N/A

## ROI estimate
- ROI: 40
- Rationale: Activating suite coverage before Dev implements ensures tests are live for Stage 4 regression, directly preventing last-minute integration failures and unverified scope from shipping.

---

*Actions taken (verified):*

**1. suite.json entries added**

Read existing `qa-suites/products/dungeoncrawler/suite.json`, appended five entries:

```json
{
  "id": "dc-cr-dwarf-heritage-rock-tc-drk-01",
  "label": "Rock Dwarf heritage is selectable only for dwarf characters within the heritage system",
  "type": "e2e",
  "feature_id": "dc-cr-dwarf-heritage-rock",
  "command": "npx playwright test tests/character-creation/rock-dwarf-heritage-availability.spec.ts",
  "artifacts": ["playwright-report/dc-cr-dwarf-heritage-rock-tc-drk-01/"],
  "required_for_release": true
},
{
  "id": "dc-cr-dwarf-heritage-rock-tc-drk-02",
  "label": "Rock Dwarf grants +2 circumstance bonus to Fortitude/Reflex DC/save vs Shove, Trip, and knock-prone; forced movement 10ft+ is halved",
  "type": "e2e",
  "feature_id": "dc-cr-dwarf-heritage-rock",
  "command": "npx playwright test tests/encounter/rock-dwarf-passive-effect.spec.ts",
  "artifacts": ["playwright-report/dc-cr-dwarf-heritage-rock-tc-drk-02/"],
  "required_for_release": true
},
{
  "id": "dc-cr-dwarf-heritage-rock-

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260429-200305-suite-activate-dc-cr-dwarf-heritage-rock
- Generated: 2026-04-29T22:18:19+00:00
