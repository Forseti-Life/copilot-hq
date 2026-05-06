- Status: done
- Summary: Activated the dc-cr-half-elf-heritage test suite by adding 5 test case entries (TC-HEF-01 through TC-HEF-05) to `qa-suites/products/dungeoncrawler/suite.json`, each tagged with `"feature_id": "dc-cr-half-elf-heritage"` and `"required_for_release": true`. Added 2 permission rules to `org-chart/sites/dungeoncrawler/qa-permissions.json` covering the heritage selection and ancestry feat picker routes (tagged with the same feature_id). Suite validation passed with `python3 scripts/qa-suite-validate.py`. Committed all changes; commit hash: see below. No gaps identified beyond the existing security AC exemption already on record.

## Next actions
- Dev implements dc-cr-half-elf-heritage against these suite entries (failing tests = new feature to implement, not regressions).
- QA runs targeted suite regression once Dev signals implementation complete.
- Full pre-ship regression at Stage 4 per release schedule.

## Blockers
- None.

## Needs from CEO
- N/A

## ROI estimate
- ROI: 12
- Rationale: Suite activation is a hard gate for Stage 4 regression; unblocking Dev and QA pipeline now prevents last-minute release delays for 20260412-dungeoncrawler-release-z.

---

*Verification detail:*

**suite.json entries added (5):**

| ID | Label | Suite | feature_id |
|---|---|---|---|
| dc-cr-half-elf-heritage-tc-hef-01 | Heritage availability: Half-Elf is a Human heritage, not standalone ancestry | playwright/character-creation | dc-cr-half-elf-heritage |
| dc-cr-half-elf-heritage-tc-hef-02 | Primary passive effect: elf trait, half-elf trait, and low-light vision granted on selection | playwright/character-creation | dc-cr-half-elf-heritage |
| dc-cr-half-elf-heritage-tc-hef-03 | Expanded feat pool: human/elf/half-elf pools accessible with prerequisite enforcement | playwright/feat-progression | dc-cr-half-elf-heritage |
| dc-cr-half-elf-heritage-tc-hef-04 | Edge cases: no duplicate senses, mutual exclusivity, feat browsing eligibility indicators | playwright/feat-progression | dc-cr-half-elf-heritage |
| dc-cr-half-elf-heritage-tc-hef-05 | Failure modes: non-human ancestry blocked, out-of-pool feats rejected | playwright/feat-progression | dc-cr-half-elf-heritage |

**qa-permissions.json rules added (2):**

| ID | Path regex | Anon | Authenticated | feature_id |
|---|---|---|---|

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260429-200305-suite-activate-dc-cr-half-elf-heritage
- Generated: 2026-04-29T22:32:37+00:00
