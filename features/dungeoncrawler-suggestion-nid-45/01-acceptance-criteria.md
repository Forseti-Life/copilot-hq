# Acceptance Criteria — dungeoncrawler-suggestion-nid-45

1. When an NPC issues a quest via in-world dialogue, the quest objective should be automatically created and logged to the player's active quest tracker. Currently, quest briefings delivered through NPC conversation are not persisting as tracked objectives, leaving players without a formal mission record after the conversation ends.
2. The change preserves adjacent gameplay behavior and does not regress the surrounding user flow.
3. QA can verify the original report or requested behavior directly in the live Dungeoncrawler/Forseti experience.

## Source of truth

- Intake flow run: `suggestion-dungeoncrawler-nid-45`
- Canonical feature brief: `features/dungeoncrawler-suggestion-nid-45/feature.md`
