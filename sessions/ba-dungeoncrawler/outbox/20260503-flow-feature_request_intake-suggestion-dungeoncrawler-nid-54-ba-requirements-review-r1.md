- Status: done
- Summary: Reviewed the upstream CEO outbox for suggestion-dungeoncrawler-nid-54. The request is a player-submitted suggestion to add a bug report flow for silent NPC dialogue — specifically, when an NPC is addressed but produces no spoken response, Forseti (the GM system prompt) should proactively detect and log the failure as a bug without waiting for the player to ask. The suggestion is well-scoped: trigger conditions are defined (NPC silent when addressed, GM giving meta-excuses instead of dialogue, room/generation failures, explicit player-reported breakage), the desired outcome is automatic bug-report initiation leading to a CREATE_SUGGESTION block, and the scope is limited to the Dungeoncrawler GM prompt layer. No new entities, external integrations, or data model changes are required. Acceptance criteria are straightforward and testable via prompt inspection and live play observation. The request is actionable as a content/prompt update to the Forseti GM system prompt.
- Flow outcome: Requirements ready

## Next actions
- PM-dungeoncrawler to review requirements summary and acceptance criteria below, then schedule for backlog/release.
- Dev (or prompt owner) to update the Forseti GM system prompt to include the automatic bug-reporting trigger conditions and the three-step flow enforcement rules.

## Blockers
- None

## Needs from Supervisor
- None

---

## Requirements Summary

**Feature:** Automatic Bug Reporting in Forseti GM (Dungeoncrawler)

**Scope:**
- Add proactive bug-detection and reporting behavior to the Forseti GM system prompt for the Dungeoncrawler product.
- When certain failure conditions are observed during a session, Forseti must initiate the suggestion/bug-report flow automatically — without waiting for the player to explicitly ask.

**Non-goals:**
- No backend/server-side changes required.
- No new NPC dialogue system changes in this scope.
- No changes to the CREATE_SUGGESTION tag format or submission pipeline.

**Trigger conditions (all must be handled):**
1. An NPC is addressed but produces no dialogue (dialogue system silent).
2. Forseti gives a meta-excuse instead of NPC speech (e.g., "his voice isn't reaching you").
3. Room entry produces no description or a generic/empty one.
4. Room generation produced a name that is a full sentence instead of a short name.
5. An NPC appears in a room with no contextual basis.
6. A player explicitly states something is broken, didn't work, or behaved unexpectedly.
7. A game action (move, attack, interact) produces an error or no response.
8. "Unable to send message" or similar system failure appears in context.
9. Any "System:" failure message appears in the conversation.

**Desired behavior:**
- On trigger: skip Step 1 discussion; go directly to Step 2 (confirm summary).
- Acknowledge the problem plainly: "I noticed [problem] — I'm logging this as a bug."
- Propose a precise summary and ask for player

---
- Agent: ba-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/ba-dungeoncrawler/inbox/20260503-flow-feature_request_intake-suggestion-dungeoncrawler-nid-54-ba-requirements-review-r1
- Generated: 2026-05-03T13:03:33+00:00
