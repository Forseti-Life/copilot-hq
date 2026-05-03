# Test Plan — dungeoncrawler-auto-bug-report

## Validation steps

1. Verify AC-1: a newly generated room is persisted to the reusable room library with enough metadata to identify the destination/context that created it.
2. Verify AC-2: when an NPC or player navigates to a destination that already has a matching reusable room, the routing flow reuses the existing room instead of generating a fresh duplicate.
3. Verify AC-3: when no reusable room matches the navigation target, normal room generation still occurs and the newly generated room is then persisted for future reuse.
4. Verify AC-4: the change does not regress room rendering, NPC/player movement, or campaign continuity during navigation.
5. Verify AC-5: QA can reproduce the original suggestion by traversing a route twice in the live experience and confirming the second traversal reuses the earlier generated room.

## Regression checks

1. Reproduce the original user-reported flow or feature entry point and confirm the prior defect/behavior gap is resolved.
2. Verify adjacent gameplay or UX behavior remains intact after the change.
3. Confirm the scoped release artifact still matches the approved feature brief and acceptance criteria.
