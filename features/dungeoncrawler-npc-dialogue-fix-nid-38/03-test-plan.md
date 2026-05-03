# Test Plan — dungeoncrawler-npc-dialogue-fix-nid-38

## Validation steps

1. Verify AC-1: when Marta the Scholar is present in The Gilded Tankard room inventory, she is also rendered on the in-game room map for that room.
2. Verify AC-2: Marta remains visible on the map as long as she remains present in the active room entity list and inventory/state.
3. Verify AC-3: the fix does not regress NPC dialogue, room inventory presentation, or the visibility of other NPCs/entities on the map.
4. Verify AC-4: QA can reproduce the original report in the live experience by loading The Gilded Tankard and confirming Marta appears on the map whenever she is listed in the room inventory.

## Regression checks

1. Reproduce the original user-reported flow or feature entry point and confirm the prior defect/behavior gap is resolved.
2. Verify adjacent gameplay or UX behavior remains intact after the change.
3. Confirm the scoped release artifact still matches the approved feature brief and acceptance criteria.
