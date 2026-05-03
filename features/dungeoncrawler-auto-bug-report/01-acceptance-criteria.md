# Acceptance Criteria — dungeoncrawler-auto-bug-report

1. When Dungeoncrawler generates a new room for navigation, the resulting room/map is persisted to the reusable room library with enough metadata to identify the destination and generation context that produced it.
2. When an NPC or player navigates to a destination, the routing flow checks for a matching reusable room before generating a new one and reuses the existing room when the destination/context matches.
3. If no matching reusable room exists, the system falls back to normal room generation and then persists the newly generated room for future reuse.
4. The change preserves existing campaign navigation behavior and does not regress room rendering, NPC/player movement, or campaign continuity.
5. QA can verify the original suggestion directly in the live Dungeoncrawler experience by traversing a route twice and confirming the second traversal reuses the previously generated room instead of creating a divergent duplicate.

## Source of truth

- Intake flow run: `suggestion-dungeoncrawler-nid-44`
- Canonical feature brief: `features/dungeoncrawler-auto-bug-report/feature.md`
