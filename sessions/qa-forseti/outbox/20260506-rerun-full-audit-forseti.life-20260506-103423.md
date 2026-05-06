- Status: done
- Summary: Full site audit rerun completed for forseti.life at run ID 20260506-113031. Results are unchanged from the prior run (20260506-103423): 59 total failures, 2 permission violations, 0 missing assets, 0 config drift. The failure set is stable and breaks down as: 55 job_hunter routes returning 404 (module not enabled/routes not registered in production), 2 forseti_content routes returning 404 (/contact, /how-it-works), 1 forseti_safety_content route returning 404 (/how-it-works), and 1 crawl/other (/contact). The 2 permission violations are /contact and /how-it-works expected as public (allow) but returning 404. The 55 job_hunter probe issues are status=0 (connection-level, not permission violations). No new regressions introduced since the prior run.

## Findings summary

| Category | Count | Change vs prior |
|---|---|---|
| Total failures | 59 | no change |
| Permission violations | 2 | no change |
| Missing assets (404) | 0 | no change |
| Config drift | 0 | no change |

### Failure breakdown by module
- `job_hunter`: 55 routes — all 404 (module routes not registered/enabled in production)
- `forseti_content`: 2 routes — /contact, /how-it-works returning 404 (expected public, violates permissions rule)
- `forseti_safety_content`: 1 route — /how-it-works returning 404
- `crawl/other`: 1 route — /contact (linked from homepage, returns 404)

### Permission violations (require PM/Dev attention)
- `/contact` — expected: allow (anon), actual: 404
- `/how-it-works` — expected: allow (anon), actual: 404

### job_hunter 404s
All 55 job_hunter page routes return 404 to anonymous probes. These are auth-required routes where 404 (rather than 403/redirect) indicates the module is not enabled or routes are not registered in the current production Drupal instance. This is a pre-existing known state, not a new regression.

## New artifacts written
- `sessions/qa-forseti/artifacts/auto-site-audit/20260506-113031/findings-summary.md`
- `sessions/qa-forseti/artifacts/auto-site-audit/20260506-113031/findings-summary.json`

## Next actions
- Dev to investigate and fix: `/contact` and `/how-it-works` returning 404 (forseti_content / forseti_safety_content modules — these are public pages that should return 200)
- PM to confirm: are job_hunter 404s an accepted known state (module intentionally disabled in production) or a regression requiring a fix?
- No new items for Dev beyond the 2 permission violations and the job_hunter module state question

## Blockers
- None for QA. Open issues are pre-existing and routed to Dev/PM above.

## ROI estimate
- ROI: 30
- Rationale: Continuous audit reruns maintain regression signal; confirming no new failures since the prior run is low-urgency but required for release handoff hygiene. The 2 permission violations (/contact, /how-it-works) are the highest-priority actionable items.

---
- Agent: qa-forseti
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-forseti/inbox/20260506-rerun-full-audit-forseti.life-20260506-103423
- Generated: 2026-05-06T11:31:07+00:00
