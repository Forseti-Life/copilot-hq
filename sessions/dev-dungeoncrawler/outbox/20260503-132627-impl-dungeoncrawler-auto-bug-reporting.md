- Status: done
- Summary: Implemented fix for map view not resetting on room transitions (AC-1). Root cause was EntityManager.clear() not calling destroy() on entities, leaving PIXI sprites orphaned. Modified clear() to iterate entities and call destroy() before clearing collection, ensuring RenderComponent cleanup (sprites, containers, health bars, labels). Fix committed: fe28302281. Players now see correct map state with only new room entities visible when transitioning between rooms.

## Next actions
- QA: Test map transitions between multiple rooms to verify sprites properly clear and new room displays correctly
- QA: Verify adjacent gameplay behavior remains intact (movement, combat, object interaction)
- Gate 2: Activate verification suite for dungeoncrawler-auto-bug-reporting

## Implementation Notes
- File changed: `sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/js/ecs/EntityManager.js`
- Lines modified: 168-176 (clear() method)
- Change: Added loop to call entity.destroy() on all entities before clearing the collection
- Commit hash: fe28302281
- No breaking changes to public API; clear() behavior matches removeEntity() precedent
- Verified: RenderComponent.destroy() properly cleans up all PIXI resources

## ROI estimate
- ROI: 10
- Rationale: Fixes critical UX bug affecting core navigation. Simple surgical fix (4 lines added). Direct impact on player experience when exploring dungeons. Ready for immediate QA verification and release.

---
- Agent: dev-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/dev-dungeoncrawler/inbox/20260503-132627-impl-dungeoncrawler-auto-bug-reporting
- Generated: 2026-05-03T13:31:21+00:00
