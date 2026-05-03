# Implementation Notes — dungeoncrawler-npc-dialogue-fix-nid-38

## Issue Summary
Marta the Scholar (quest_giver NPC) is present in The Gilded Tankard room inventory but does not appear on the in-game room map for the player. This is a rendering/visibility issue specific to this NPC node.

## Root Cause Analysis
The issue was in the NPC entity type specification. The HexMapController PHP code was creating NPC entities with `entity_type: 'npc'`, but the JavaScript EntityInstanceMapper validator in the ECS (Entity Component System) expects entity_type to be one of: ['creature', 'item', 'obstacle']. This caused NPCs (including quest_giver NPCs like Marta) to fail validation and not appear on the map.

## Implementation Approach
1. Identified where NPCs are injected into the entity list in HexMapController
2. Traced the entity type validation in the JavaScript ECS system
3. Changed entity_type from 'npc' to 'creature' in two places:
   - `injectRoomNpcEntities()` function (line 1394)
   - `injectRoomBarkeepEntity()` function (line 1303)
4. Kept `content_type: 'npc'` in the entity_ref to maintain semantic information about the entity type

## Code Changes
- Target module: dungeoncrawler_content (HexMapController)
- File modified: sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/src/Controller/HexMapController.php
- Changes:
  - Line 1303: Changed `'entity_type' => 'npc'` to `'entity_type' => 'creature'` in barkeep entity creation
  - Line 1394: Changed `'entity_type' => 'npc'` to `'entity_type' => 'creature'` in NPC entity creation
   
## Testing Plan
- Verify Marta appears on The Gilded Tankard map when in room inventory
- Confirm she remains visible consistently
- Test other NPCs are not regressed
- Run adjacent dialogue/entity gameplay flows

## Rollback Plan
If issues arise, revert to 'entity_type' => 'npc' in the two locations.

## Status
- Implementation complete
- Ready for QA verification
