# QA Configuration: Suppress authenticated admin routes from anonymous QA crawl

## Context
Dev investigation confirmed 7 "failures" are actually authenticated admin routes correctly returning 404 to anonymous crawlers. Routes are properly defined in `copilot_agent_tracker.routing.yml` and require `administer copilot agent tracker` permission.

## Scope
Add the following 7 routes to QA anonymous crawl suppression list (or to authenticated crawl if those routes should be tested):
- `/admin/reports/copilot-agent-tracker/langgraph-console`
- `/admin/reports/copilot-agent-tracker/langgraph-console/details/{id}`
- `/admin/reports/copilot-agent-tracker/langgraph-console/logs/{id}`
- `/admin/reports/copilot-agent-tracker/langgraph-console/nodes/{id}`
- `/admin/reports/copilot-agent-tracker/langgraph-console/edges/{id}`
- `/admin/reports/copilot-agent-tracker/langgraph-console/error-logs`
- `/admin/reports/copilot-agent-tracker/langgraph-console/error-logs/{id}`

## Acceptance criteria
- Routes are added to `org-chart/sites/dungeoncrawler/qa-permissions.json` with status `authenticated_only` OR are excluded from QA crawl scope
- QA audit reports these routes as "expected suppression" instead of "failures"
- Next QA crawl shows 0 failures on copilot_agent_tracker routes

## Verification
Run: `bash scripts/qa-audit.sh dungeoncrawler` and confirm no 404s on `/admin/reports/copilot-agent-tracker/*` routes

## Linked evidence
- Dev outbox: /home/ubuntu/forseti.life/sessions/dev-dungeoncrawler/outbox/20260423-1776962948-impl-dungeoncrawler-tester-push-automation-validation.md
- Routes: `/home/ubuntu/forseti.life/sites/dungeoncrawler/web/modules/custom/copilot_agent_tracker/copilot_agent_tracker.routing.yml`
- Permissions: `copilot_agent_tracker.permissions.yml`

## ROI
- ROI: 8
- Rationale: QA configuration fix (non-dev). Clears false blocker and allows release to proceed. High confidence (dev evidence provided).
