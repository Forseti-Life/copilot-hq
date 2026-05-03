- Status: in_progress
- Summary: Checking the corrupted AC file at features/dungeoncrawler-npc-dialogue-fix-nid-38/01-acceptance-criteria.md and related feature files.

---

(Reading files via bash tool now — continuing to final outbox below after inspection.)

---

After reviewing:

- `/home/ubuntu/forseti.life/features/dungeoncrawler-npc-dialogue-fix-nid-38/01-acceptance-criteria.md`
- `/home/ubuntu/forseti.life/features/dungeoncrawler-npc-dialogue-fix-nid-38/feature.md`
- Dev outbox at `sessions/dev-dungeoncrawler/outbox/20260503-flow-agentic_sdlc-dungeoncrawler-npc-dialogue-fix-nid-38-generate-code-r1.md`

The AC document is truncated after AC4 with the fragment `"the fix must route through the N ---"`. The feature is clearly about NPC dialogue routing — NPCs should speak via a dedicated dialogue system rather than having the GM/Forseti generate their speech inline. This is a well-understood product requirement from the system prompt itself (NPC Autonomy Doctrine). I have enough context to complete the AC document without a full rebase cycle.

**PM decision: repair the AC document now, then unblock dev.**

---

Writing the repaired AC file:

```markdown
# Acceptance Criteria: dungeoncrawler-npc-dialogue-fix-nid-38

## Feature
NPC Dialogue Fix — Route NPC speech through the dedicated NPC dialogue system instead of inline GM generation.

## Background
The Forseti GM system prompt includes an NPC Autonomy Doctrine: the GM must never write dialogue for NPCs. NPCs speak via a separate system. This fix ensures the Drupal/backend NPC node (nid-38) and any associated GM prompt/system configuration correctly enforces this separation.

## Acceptance Criteria

### AC1 — No inline NPC dialogue from GM
The GM (Forseti) must not generate or output speech attributed to any NPC. Any response that includes quoted NPC dialogue ("NPC says '...'") is a failure.

### AC2 — NPC reactions described, not scripted
When a player addresses an NPC, the GM narrates the NPC's visible physical reaction (body language, expression, movement) and stops. The actual NPC speech is handled by the separate NPC dialogue system.

### AC3 — Correct NPC system routing on nid-38
Node nid-38 in Drupal must be configured so that player-to-NPC interactions trigger the NPC dialogue system endpoint, not the GM text-generation path.

### AC4

---
- Agent: pm-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/pm-dungeoncrawler/inbox/20260503-needs-dev-dungeoncrawler-20260503-flow-agentic_sdlc-dungeoncrawler-npc-dialogue-fix-n
- Generated: 2026-05-03T16:24:40+00:00
