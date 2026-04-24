- Status: done
- Completed: 2026-04-24T01:14:29Z

# QA Findings — Action Required

- Product team: dungeoncrawler
- Release id: 20260412-dungeoncrawler-release-s
- Site label: dungeoncrawler
- Base URL: https://dungeoncrawler.forseti.life
- QA run: 20260424-001221
- Findings summary: sessions/qa-dungeoncrawler/artifacts/auto-site-audit/20260424-001221/findings-summary.md
- Findings JSON: sessions/qa-dungeoncrawler/artifacts/auto-site-audit/20260424-001221/findings-summary.json
- Total open issues: 7

    ## REGRESSION FIXES REQUIRED
    The following failures are on tests that previously passed (or have no feature_id).
    Treat these as regressions — identify what changed and restore correct behavior.

    Open issue buckets:
    - Missing assets (404): 0
    - Permission violations: 0
    - Other failures (4xx/5xx): 7
    - Total: 7 (approx — subtract new-feature paths above)

    Required actions:
    1) Fix highest-impact failures first.
    2) For each fix, notify QA immediately with explicit handoff marker.
    3) Keep notes concise in outbox and include touched files/routes.

Deliverable:
- Outbox update with Status + Summary and QA handoff notes.
- For new features: create/update features/<feature_id>/02-implementation-notes.md.
- Agent: dev-dungeoncrawler
- Status: pending
