# Acceptance Criteria — dungeoncrawler-npc-dialogue-bug-reporting-flow

1. Party-member NPCs who explicitly agree to travel with the player (e.g., Gribbles in The Gilded Tankard) are not persisting into the next room's entity list after navigation. They disappear from the room inventory on arrival despite having joined the party.
2. The change preserves adjacent gameplay behavior and does not regress the surrounding user flow.
3. QA can verify the original report or requested behavior directly in the live Dungeoncrawler/Forseti experience.

## Source of truth

- Intake flow run: `suggestion-dungeoncrawler-nid-43`
- Canonical feature brief: `features/dungeoncrawler-npc-dialogue-bug-reporting-flow/feature.md`
