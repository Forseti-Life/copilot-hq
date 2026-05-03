Resolve the source-of-truth conflict for `dungeoncrawler-npc-dialogue-fix-nid-38` before Dev is requeued.

Conflicting artifacts:
- Intake/CEO artifacts say NID 38 is a map/rendering issue for Marta the Scholar missing from the in-game map:
  - `sessions/ceo-copilot-2/outbox/20260502-193702-flow-feature-request-intake-dungeoncrawler-nid-38-marta-the-scholar-quest_giver-npc-is-pre.md`
  - `sessions/ceo-copilot-2/outbox/20260502-flow-feature_request_intake-suggestion-dungeoncrawler-nid-38-intake-review-r1.md`
- BA/PM/QA flow artifacts later shifted to NPC dialogue silence.
- Current package is corrupted/inconsistent:
  - `features/dungeoncrawler-npc-dialogue-fix-nid-38/feature.md`
  - `features/dungeoncrawler-npc-dialogue-fix-nid-38/01-acceptance-criteria.md`
  - `features/dungeoncrawler-npc-dialogue-fix-nid-38/03-test-plan.md`
- Dev blocked on the corrupted AC:
  - `sessions/dev-dungeoncrawler/outbox/20260503-flow-agentic_sdlc-dungeoncrawler-npc-dialogue-fix-nid-38-generate-code-r1.md`

Required action:
1. Choose the canonical scope for NID 38 based on the trustworthy intake chain.
2. Repair the feature package so feature brief, AC, and test plan all align.
3. Emit a release-safe disposition: resume implementation under corrected scope, or hold/defer if the source truth cannot be reconciled in-cycle.
4. If resuming, ensure Dev has a coherent downstream handoff.

Deliverable:
- Final outbox with explicit decision and exact artifact paths updated.
