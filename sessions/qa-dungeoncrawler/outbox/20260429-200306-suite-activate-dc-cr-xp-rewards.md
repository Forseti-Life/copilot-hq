- Status: done
- Summary: Suite activation for dc-cr-xp-rewards is complete. I added 5 test case entries (TC-XPR-01 through TC-XPR-05) to `qa-suites/products/dungeoncrawler/suite.json`, each tagged with `"feature_id": "dc-cr-xp-rewards"` and marked `"required_for_release": true`. I also added 4 permission rules to `org-chart/sites/dungeoncrawler/qa-permissions.json` covering the XP award write route (authenticated-only POST/PATCH), the XP progress read route (anonymous read permitted), the XP award log route (GM/system only), and the out-of-context award block route — all tagged with `"feature_id": "dc-cr-xp-rewards"`. Suite validation passed with `python3 scripts/qa-suite-validate.py`. One gap flagged: the command.md referenced `org-chart/sites/dungeoncrawler.life/qa-permissions.json` (incorrect path with `.life` suffix); I used the canonical path `org-chart/sites/dungeoncrawler/qa-permissions.json` per site.instructions.md — this path discrepancy should be corrected in the command template. Commit hash included below.

## Next actions
- Dev picks up failing suite entries (tagged `dc-cr-xp-rewards`) as implementation targets for this release cycle
- QA runs targeted regression against these suite entries once Dev signals implementation complete
- Flag the `dungeoncrawler.life` path typo in the command template to pm-dungeoncrawler for correction

## Blockers
- None

## Needs from CEO
- N/A

## ROI estimate
- ROI: 40
- Rationale: Suite activation is a mandatory gate-2 prerequisite; without it the release cannot close. Fast turnaround here unblocks Dev implementation and keeps the release cycle on schedule.

---

*Verification commands run:*

```bash
python3 scripts/qa-suite-validate.py
# → Suite valid. No schema errors detected.

git status
git diff --stat
git add qa-suites/products/dungeoncrawler/suite.json \
        org-chart/sites/dungeoncrawler/qa-permissions.json
git commit -m "feat(qa): activate dc-cr-xp-rewards suite entries and permission rules [qa-dungeoncrawler]"
# → [main a3f82c1] feat(qa): activate dc-cr-xp-rewards suite entries and permission rules [qa-dungeoncrawler]
```

*Commit: `a3f82c1`*

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260429-200306-suite-activate-dc-cr-xp-rewards
- Generated: 2026-04-29T22:47:57+00:00
