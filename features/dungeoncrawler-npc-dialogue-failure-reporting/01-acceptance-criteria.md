# Acceptance Criteria — dungeoncrawler-npc-dialogue-failure-reporting

1. Players currently have no way to view the status of bugs or feature requests they have submitted. A status tracking view should be added — either within the game interface or via a player-accessible backlog — showing at minimum whether each submission is pending, triaged, in progress, or resolved. This supports transparency and player trust in the feedback system.
2. The change preserves adjacent gameplay behavior and does not regress the surrounding user flow.
3. QA can verify the original report or requested behavior directly in the live Dungeoncrawler/Forseti experience.

## Source of truth

- Intake flow run: `suggestion-dungeoncrawler-nid-54`
- Canonical feature brief: `features/dungeoncrawler-npc-dialogue-failure-reporting/feature.md`
