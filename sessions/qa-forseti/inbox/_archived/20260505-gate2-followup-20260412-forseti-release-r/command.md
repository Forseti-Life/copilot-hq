- command: |
    Active release `20260412-forseti-release-r` has scoped implementation evidence but no valid Gate 2 APPROVE artifact.

    Scoped feature:
    - `forseti-langgraph-console-admin`

    Implementation evidence to review:
    - `sessions/dev-forseti/outbox/20260505-complete-forseti-langgraph-console-admin-per-board.md`
    - `sessions/dev-forseti/outbox/20260505-finish-forseti-langgraph-console-admin.md` (older blocked attempt; do not treat as latest)

    Important policy update:
    - Auto-filed clean-audit Gate 2 approvals are retired.
    - A clean site audit is supporting evidence only and does NOT satisfy Gate 2 by itself.
    - Write an explicit release-scoped QA verdict based on actual release evidence.

    Required actions:
    1. Review the scoped feature, latest dev evidence, and any relevant QA artifacts.
    2. Run or refresh the necessary Forseti QA checks for this release.
    3. Write exactly one canonical outbox artifact for `20260412-forseti-release-r`:
       - `gate2-approve-...` if QA evidence is sufficient, or
       - `gate2-block-...` / equivalent BLOCK verdict if not.
    4. The outbox must begin with:
       - `- Status: <done|blocked|needs-info>`
       - `- Summary: <one-line machine-consumable summary>`
    5. Include explicit evidence reviewed and a clear APPROVE or BLOCK verdict.

    Success condition:
    - `bash scripts/ceo-release-health.sh` should show either:
      - `[forseti] Gate 2 evidence:` as PASS from a manual QA artifact, or
      - a documented BLOCK artifact explaining the release blocker.
