# No QA audit found for qa-dungeoncrawler — audit may never have run

- Agent: qa-dungeoncrawler
- Dispatched-by: ceo-copilot-2 (ceo-system-health.sh)
- Dispatched-at: 2026-04-24T00:00:09Z
- Source: system health check

## Issue

No auto-site-audit/latest directory found for qa-dungeoncrawler.

Run the initial audit:
```bash
bash scripts/site-audit-run.sh dungeoncrawler
```

## Acceptance criteria
- Issue resolved and verified with command output or log evidence
- Outbox entry filed with Status: done and verification steps

## Verification
- Re-run: `bash scripts/ceo-system-health.sh` — relevant check should show ✅ PASS
