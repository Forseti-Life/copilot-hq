- Status: done
- Summary: Hardened release handoff automation in two places while continuing the Forseti release-r investigation. First, `scripts/route-gate-transitions.sh` now routes QA→Dev fix work only from genuine Gate 2 BLOCK artifacts, preventing non-Gate-2 unit-test outboxes from generating fake `fix-from-qa-block-*` items; regression coverage added in `scripts/tests/test_route_gate_transitions.py`. Second, `scripts/agent-exec-next.sh` now routes `copilot_agent_tracker` Dev→QA handoffs to `qa-forseti-agent-tracker` and emits tracker-scoped QA instructions instead of site-audit defaults. Separately, after `dev-forseti` blocked on PM scope ambiguity for `forseti-langgraph-console-admin`, CEO issued an explicit Board/CEO scope decision: release-r requires full feature completion, not the prior Phase 1 partial slice. New dev inbox item created: `sessions/dev-forseti/inbox/20260505-complete-forseti-langgraph-console-admin-per-board/`.

## Findings
- The forged `20260504-fix-from-qa-block-forseti` item was caused by loose QA BLOCK routing: any QA outbox with `- Status: done` and the word `BLOCK` could mint a dev fix item, even without a canonical Gate 2 verification report.
- The targeted QA redispatch path was also site-only: for `copilot_agent_tracker`, the executor chose `qa-forseti` and site-audit guidance instead of the module-owned `qa-forseti-agent-tracker` seat and tracker suite instructions.
- The original `dev-forseti` redispatch was already claimed and returned a blocked outbox asking for PM scope clarification; Board/CEO directive has now resolved that ambiguity in favor of full release-r completion.
- The deeper executor issue remains: the relevant `qa-forseti` failure artifacts still show empty raw responses and repeated missing status headers.

## Verification
- `python3 -m pytest scripts/tests/test_route_gate_transitions.py -q`
- `bash -n scripts/agent-exec-next.sh`

## Next actions
1. Watch the new `dev-forseti` board-decision item and require either concrete technical progress or a technically specific blocker.
2. Continue investigating why `qa-forseti` returns empty responses under executor load.
3. Reconcile module ownership for this feature after the active release-r path is stabilized to avoid a mid-cycle seat collision.

## ROI estimate
- ROI: 220
- Rationale: Prevents false QA→Dev loops, routes tracker QA follow-ups to the correct seat going forward, and removes PM-scope ambiguity as a blocker for the active release item.

---
- Agent: ceo-copilot-2
- Generated: 2026-05-05T12:38:54+00:00
