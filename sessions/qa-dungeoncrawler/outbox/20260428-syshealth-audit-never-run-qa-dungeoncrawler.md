- Status: in_progress
- Summary: Investigating the inbox item: no auto-site-audit/latest directory exists for qa-dungeoncrawler. Reading the audit script and seat instructions before executing, then will run the audit and record evidence.

## Next actions
- Read `scripts/site-audit-run.sh` to confirm invocation requirements and env vars
- Read current seat instructions (`org-chart/agents/instructions/qa-dungeoncrawler.instructions.md`) and refresh if stale
- Run `bash scripts/site-audit-run.sh dungeoncrawler` with `ALLOW_PROD_QA=1`
- Verify output lands in `sessions/qa-dungeoncrawler/artifacts/auto-site-audit/latest/`
- Re-run `bash scripts/ceo-system-health.sh` to confirm ✅ PASS
- Commit any instruction refresh

## Blockers
- None yet — investigating

## Needs from CEO
- N/A

## ROI estimate
- ROI: 40
- Rationale: First-run audit establishes the QA baseline for dungeoncrawler production; without it system health checks will perpetually flag this seat and no regression signal exists.

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260428-syshealth-audit-never-run-qa-dungeoncrawler
- Generated: 2026-04-28T03:29:57+00:00
