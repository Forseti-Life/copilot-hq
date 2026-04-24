- Status: in_progress
- Summary: Reading the inbox item and locating the audit script before executing. Will run `bash scripts/site-audit-run.sh dungeoncrawler` (with `ALLOW_PROD_QA=1` per site instructions) and capture output as evidence, then update seat instructions and file a done outbox.

## Next actions
- Read `scripts/site-audit-run.sh` to confirm required env vars and invocation
- Run the audit against production with `ALLOW_PROD_QA=1`
- Capture output and confirm `sessions/qa-dungeoncrawler/artifacts/auto-site-audit/latest/` is populated
- Re-run `bash scripts/ceo-system-health.sh` to verify the health check passes
- Refresh seat instructions file with correct audit invocation, env vars, and evidence paths
- Commit all file changes

## Blockers
- None anticipated

## Needs from CEO
- N/A

## ROI estimate
- ROI: 40
- Rationale: Unrun QA audit means zero visibility into production health for dungeoncrawler; resolving this unblocks continuous QA signal and satisfies a system health gate.

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260424-syshealth-audit-never-run-qa-dungeoncrawler
- Generated: 2026-04-24T06:47:12+00:00
