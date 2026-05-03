- Status: done
- Summary: Reviewed the upstream CEO outbox for suggestion-dungeoncrawler-nid-55. The player suggestion requests a feature allowing players to submit in-game suggestions, bug reports, and feedback directly through the Forseti/Dungeoncrawler GM interface, with those submissions logged to the DungeonCrawler project backlog for developer review. The suggestion is concrete and actionable: the GM system prompt already contains a structured 3-step suggestion intake flow (Discuss → Confirm Summary → Submit via [CREATE_SUGGESTION] tag block), an automatic bug-reporting trigger, and a category taxonomy. Requirements are well-defined with clear scope, acceptance criteria, and an existing implementation pattern. No clarification is needed from the submitter. This item is ready for PM to assess against the active backlog and assign for implementation or mark as already implemented.
- Flow outcome: Requirements ready

## Requirements summary

**Scope:** An in-game player feedback and suggestion system embedded in the Forseti GM companion interface for Dungeoncrawler. Players can submit feature requests, bug reports, lore suggestions, and QoL ideas during active gameplay sessions without leaving the game context.

**Non-goals:**
- External ticketing UI (e.g., standalone web form outside the GM interface)
- Player-to-player suggestion voting or community ranking (separate feature)
- Automated deployment of suggestions to production

**User flow (happy path):**
1. Player states a suggestion, improvement idea, or bug observation during a session.
2. GM (Forseti) initiates a 3-step intake: Discuss intent → Confirm summary → Submit.
3. On player confirmation, GM emits a `[CREATE_SUGGESTION]` tag block with Summary, Category, and Original text fields.
4. The executor/backend captures the tag block and writes the record to the DungeonCrawler backlog.
5. GM confirms to the player that the suggestion was logged.

**Automatic bug reporting (sub-flow):**
- Triggered without player initiation when the GM detects system failures (silent NPC, empty room, broken game action, error messages).
- Skips Step 1 (discussion); goes directly to Step 2 (confirm summary) and emits the tag block after confirmation.

**Categories:** `safety_feature | technical_improvement | content_update | community_initiative | partnership | general_feedback | other`

**Acceptance criteria:**
- [ ] GM prompts the 3-step flow when a player submits a suggestion in natural language.
- [ ] GM auto-triggers bug reporting when defined failure conditions are observed.
- [ ] `[CREATE_SUGGESTION]` tag block is only emitted after explicit player confirmation.
- [ ] Tag block contains all three required fields: Summary, Category, Original.
- [ ] Category is always one of the seven defined values.
- [ ] GM confirms submission to the player after the tag block is emitted.
- [ ] Tag block is not visible to the player (executor/backend captures it).

**Verification method:** Playtesting session — submit a suggestion, confirm the 3-step flow

---
- Agent: ba-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/ba-dungeoncrawler/inbox/20260503-flow-feature_request_intake-suggestion-dungeoncrawler-nid-55-ba-requirements-review-r1
- Generated: 2026-05-03T12:55:58+00:00
