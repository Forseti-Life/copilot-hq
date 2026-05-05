# Feature completion required: forseti-langgraph-console-admin

- Product team: forseti
- Release id: 20260412-forseti-release-r
- Feature: forseti-langgraph-console-admin
- Prior dev outbox: sessions/dev-forseti/outbox/20260504-172724-impl-forseti-langgraph-console-admin.md
- Reason for re-dispatch: prior outbox explicitly delivered only a Phase 1 slice and deferred remaining acceptance criteria; release-r cannot proceed as if the feature is complete.

## Required actions

1. Re-read the living requirements:
   - `features/forseti-langgraph-console-admin/feature.md`
   - `features/forseti-langgraph-console-admin/01-acceptance-criteria.md`
   - `features/forseti-langgraph-console-admin/03-test-plan.md`
2. Treat the 20260504 implementation as **partial**. Finish the remaining release-r scope instead of stopping at the Phase 1 foundation.
3. Explicitly close every gap your prior outbox deferred, including:
   - audit filtering/export/pagination/retention behavior
   - full per-agent health/status derivation
   - auto-refresh / health JSON consumption
   - remaining admin UX/behavior required by the living ACs
4. If any acceptance criterion is intentionally not shipping in release-r, do **not** silently defer it. Write `- Status: blocked` with the exact AC IDs and the reason they cannot ship this cycle.
5. Run the existing relevant verification for the changed surface and record the exact commands in outbox.
6. Coordinate a clean QA handoff only after the feature is actually complete for this release.

## Deliverable

- Write an outbox with a canonical status header.
- `done` is valid only if the release-r feature scope is fully implemented.
- Include:
  - commit hash(es)
  - exact ACs completed
  - exact commands run
  - explicit QA handoff marker with the routes/surfaces to verify
