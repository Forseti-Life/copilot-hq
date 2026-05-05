- command: |
    Re-run explicit Gate 2 QA verification for active release `20260412-forseti-release-r`.

    Scoped feature:
    - `forseti-langgraph-console-admin`

    Current implementation evidence:
    - `sessions/dev-forseti/outbox/20260505-complete-forseti-langgraph-console-admin-per-board.md`

    Current release-state context:
    - Auto-filed Gate 2 approvals have been retired and do not count.
    - A previous rerun failed due executor/backend issues, which have now been repaired.
    - Re-evaluate the release on the merits and write a fresh QA verdict.

    Required actions:
    1. Review the scoped feature and latest dev evidence.
    2. Review any relevant QA artifacts already on disk.
    3. Write exactly one release-scoped QA verdict artifact for `20260412-forseti-release-r`.
    4. If release evidence is sufficient, write `gate2-approve-...`.
    5. If not, write a BLOCK verdict with explicit blockers.

    Output contract:
    - First line: `- Status: ...`
    - Second line: `- Summary: ...`
    - Include explicit APPROVE or BLOCK language in the body.
