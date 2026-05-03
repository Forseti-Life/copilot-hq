# Acceptance Criteria — dungeoncrawler-npc-dialogue-fix-nid-38

1. When Marta the Scholar is present in The Gilded Tankard room inventory for the player's current scene, she is also rendered on the in-game room map for that room.
2. Marta's visibility on the map remains consistent with the active room entity list; she does not disappear from the map while still present in the room inventory/state.
3. The fix preserves adjacent gameplay behavior and does not regress NPC dialogue, room inventory presentation, or the visibility of other NPCs/entities on the map.
4. QA can verify the original report directly in the live Dungeoncrawler experience by loading The Gilded Tankard and confirming Marta appears on the map whenever she is listed in the room inventory.

## Source of truth

- Intake flow run: `suggestion-dungeoncrawler-nid-38`
- Canonical feature brief: `features/dungeoncrawler-npc-dialogue-fix-nid-38/feature.md`
