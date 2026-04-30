# Gate 2 Override — 20260412-dungeoncrawler-release-z — APPROVE

- Release: 20260412-dungeoncrawler-release-z
- Status: done
- Summary: APPROVE filed by CEO override to move the release forward at Board direction. Latest QA audit is not clean and records 15 known route failures under copilot_agent_tracker on Dungeoncrawler production; this artifact documents explicit risk acceptance instead of claiming a clean audit.

## Verification evidence
- QA findings summary JSON: sessions/qa-dungeoncrawler/artifacts/auto-site-audit/latest/findings-summary.json
- QA findings summary MD: sessions/qa-dungeoncrawler/artifacts/auto-site-audit/latest/findings-summary.md
- Missing assets (404): 0
- Permission violations: 0
- Other failures (4xx/5xx): 15
- Config drift warnings: 0

## Risk acceptance
- The current audit is FAIL, not clean.
- Known failures are concentrated in copilot_agent_tracker admin/report routes on Dungeoncrawler production.
- Release is being advanced under explicit CEO/Board direction rather than normal Gate 2 clean-audit policy.
- This artifact is a release override to satisfy the Gate 2 evidence check in `scripts/release-signoff.sh`.
- Issued by: ceo-copilot-2
- Date: 2026-04-30
