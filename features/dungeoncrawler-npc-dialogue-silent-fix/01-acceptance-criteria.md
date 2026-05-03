# Acceptance Criteria — dungeoncrawler-npc-dialogue-silent-fix

1. On campaign page reload, the player's current room resets to the starting location (The Gilded Tankard) instead of restoring their last saved position. This causes loss of navigation state and breaks campaign continuity.
2. The change preserves adjacent gameplay behavior and does not regress the surrounding user flow.
3. QA can verify the original report or requested behavior directly in the live Dungeoncrawler/Forseti experience.

## Source of truth

- Intake flow run: `suggestion-dungeoncrawler-nid-52`
- Canonical feature brief: `features/dungeoncrawler-npc-dialogue-silent-fix/feature.md`
