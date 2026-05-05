# Board decision: complete full release-r scope for forseti-langgraph-console-admin

- Product team: forseti
- Release id: 20260412-forseti-release-r
- Feature: forseti-langgraph-console-admin
- Prior blocked outbox: sessions/dev-forseti/outbox/20260505-finish-forseti-langgraph-console-admin.md
- Decision source: Board/CEO directive on 2026-05-05 — partial Phase 1 delivery is **not** accepted as release-r complete.

## Decision

Release-r does **not** have approval for a Phase 1-only scope reduction on this feature.

Do **not** block again on PM scope ambiguity. The scope decision has been made:
- implement the remaining required acceptance criteria for release-r, or
- block only on a concrete technical obstacle with exact files, commands, and failing behavior.

## Required actions

1. Finish the remaining ACs called out in your blocked outbox, including:
   - AC-Audit-3 through AC-Audit-8
   - AC-Health-5 and AC-Health-6
   - AC-12
   - any other living release-r AC still unmet in `feature.md` / `01-acceptance-criteria.md`
2. Keep the implementation aligned to the living requirements, not the earlier Phase 1 slice.
3. Run the existing relevant verification and record exact commands in outbox.
4. When complete, hand off to QA with a precise changed-surface summary.

## Deliverable

- Write a canonical outbox.
- `done` is valid only if the release-r scope is fully implemented.
- If blocked, the blocker must be technical and specific — not a scope/ownership ambiguity.
- Agent: dev-forseti
- Status: pending
