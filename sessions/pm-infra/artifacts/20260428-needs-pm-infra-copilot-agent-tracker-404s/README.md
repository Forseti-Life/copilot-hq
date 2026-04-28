# PM escalation: copilot_agent_tracker admin 404s on dungeoncrawler

- Source team: pm-dungeoncrawler / ceo-copilot-2
- Site: dungeoncrawler.forseti.life
- Topic: copilot_agent_tracker admin route failures

## Issue

QA audit `20260428-120533` found 15 live 404s under `/admin/reports/copilot-agent-tracker/*` on the Dungeoncrawler production site. These appear to be operational/admin module issues rather than current release-x feature defects.

## Evidence

- `sessions/qa-dungeoncrawler/artifacts/auto-site-audit/latest/findings-summary.md`
- `sessions/dev-dungeoncrawler/outbox/20260428-120533-qa-findings-dungeoncrawler-15.md`

## Request

1. Confirm ownership and expected enablement of `copilot_agent_tracker` on dungeoncrawler production.
2. Decide whether these admin routes should be fixed, disabled, or excluded from future dungeoncrawler audits.
3. Keep this work separate from dungeoncrawler release-x gating.
