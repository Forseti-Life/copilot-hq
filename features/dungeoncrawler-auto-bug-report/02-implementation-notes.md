# Implementation Notes — dungeoncrawler-auto-bug-report

## Issue Summary
Community suggestion NID 44: All generated rooms should be saved to a persistent library. When an NPC or player navigates to a destination, the routing system should first check whether a matching room already exists in the library before generating a new one — enabling room reuse when context and path align.

## Root Cause Analysis
Previously, all room generation was ephemeral — generated rooms existed only in campaign-specific cache and were not persisted to a reusable library. This meant even semantically identical rooms (same theme, type, size) would be regenerated multiple times for different navigation paths or player revisits, wasting computation and potentially creating inconsistent experiences.

## Implementation Approach
Implemented a three-tier room caching and reuse system:

1. **Campaign-scoped cache** (RoomGeneratorService): Check first for recently-generated rooms by campaign/context
2. **Room template library** (RoomLibraryService + dungeoncrawler_content_room_templates table): Persistent repository of room layouts indexed by theme, room_type, and size_category
3. **Fallback generation + persistence**: If no library match exists, generate new room and catalogue it for future reuse

## Code Changes
- **Target module**: dungeoncrawler_content
- **Key files modified/implemented**:
  - `RoomLibraryService.php` (525 lines) — Core library management with catalogueRoom(), findReusableRoom(), loadTemplate() methods
  - `RoomGeneratorService.php` — Integrated library lookup at lines 204-228 (findAndInstantiateFromLibrary call) and cataloguing at lines 326-344
  - `dungeoncrawler_content.install` — Schema for dungeoncrawler_content_room_templates table
  - `dc_campaign_rooms` table — Added `source_room_id` column to link campaign rooms back to library templates

## Execution Flow (Room Generation)
1. **RoomGeneratorService::generateRoom()** called with context (campaign_id, theme, room_type, size_category, etc.)
2. **Step 1**: Check campaign-scoped cache (recent rooms)
3. **Step 1b**: Call `findAndInstantiateFromLibrary()` to check for matching reusable room
4. **If library match found**: Instantiate from template, persist to campaign, link via source_room_id, return
5. **If no match**: Generate new room (Steps 2-7)
6. **Step 8**: Catalogue newly generated room to library via `RoomLibraryService::catalogueRoom()`
7. **Link**: Update source_room_id to enable future matching

## Testing Plan
- Verify AC-1: Generate a room and confirm it appears in dungeoncrawler_content_room_templates table with correct metadata
- Verify AC-2: Navigate to the same destination twice; second traversal should reuse the library room (check from_library flag or source_room_id link)
- Verify AC-3: Force generation of a new context; confirm new room is generated, persisted, and then catalogued
- Verify AC-4: Traverse multiple distinct rooms; confirm no regressions in rendering, entity placement, or movement
- Verify AC-5: Live experience test: traverse The Gilded Tankard twice, confirm consistent layout on second visit

## Rollback Plan
- Reverse dungeoncrawler_content.install schema changes
- Remove RoomLibraryService.php and remove calls to it from RoomGeneratorService.php (lines 204-228, 326-344)
- Remove source_room_id column from dc_campaign_rooms

## Status
- Implementation complete
- All acceptance criteria covered
- Ready for QA verification

## Key Insight
Room reuse is achieved through a hybrid approach: campaign-scoped cache for immediate reuse within a session + persistent library for cross-session reuse. This balances performance (no redundant generation) with consistency (same semantic inputs → same room output).
