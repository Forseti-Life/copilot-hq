# Implementation Notes — dungeoncrawler-npc-dialogue-fix-nid-38

## Issue Summary
Marta the Scholar (quest_giver NPC) is present in The Gilded Tankard room inventory but does not appear on the in-game room map for the player. This is a rendering/visibility issue specific to this NPC node.

## Root Cause Analysis
The issue appears to be in the NPC map rendering layer where entities in the room inventory are not all being included in the map display. The hexmap/entity visibility system likely has a filtering condition that is excluding Marta despite her being present in the room state.

## Implementation Approach
1. Trace the NPC/entity rendering pipeline for room maps
2. Identify why Marta is not appearing on the map despite being in inventory
3. Apply a targeted fix to restore visibility
4. Verify no regressions in adjacent NPC/entity visibility

## Code Changes
- Target module: dungeoncrawler_content (entity/NPC rendering)
- Key files to inspect:
  - NPC rendering templates (Twig)
  - Entity visibility logic
  - Hexmap/map display components
  
## Testing Plan
- Verify Marta appears on The Gilded Tankard map when in room inventory
- Confirm she remains visible consistently
- Test other NPCs are not regressed
- Run adjacent dialogue/entity gameplay flows

## Rollback Plan
If issues arise, revert the commit and restore prior behavior.

## Status
- In progress
- Created: 2026-05-03
