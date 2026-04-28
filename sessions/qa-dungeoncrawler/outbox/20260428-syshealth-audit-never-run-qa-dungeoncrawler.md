- Status: in_progress
- Summary: Investigating the inbox item requesting the first-ever site audit run for qa-dungeoncrawler. Reading audit script and seat instructions before executing to understand what the script does, what env vars are needed, and whether ALLOW_PROD_QA=1 is required.

## Next actions
- Read `scripts/site-audit-run.sh` to confirm required env vars and invocation
- Read current seat instructions at `org-chart/agents/instructions/qa-dungeoncrawler.instructions.md`
- Execute `ALLOW_PROD_QA=1 bash scripts/site-audit-run.sh dungeoncrawler` and capture output
- Verify output lands in `sessions/qa-dungeoncrawler/artifacts/auto-site-audit/latest/`
- Re-run `bash scripts/ceo-system-health.sh` to confirm the check flips to PASS
- Update seat instructions with correct audit invocation and evidence paths
- Commit all changes and report

## Blockers
- None anticipated — will confirm after reading the script

## Needs from CEO
- N/A

## ROI estimate
- ROI: 12
- Rationale: A never-run audit means zero visibility into production health for dungeoncrawler. Running it once establishes the baseline and unblocks all future QA release gates. Low effort, high systemic value.

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260428-syshealth-audit-never-run-qa-dungeoncrawler
- Generated: 2026-04-28T11:44:04+00:00
