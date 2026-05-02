# Test Plan — dungeoncrawler-npc-autonomy-doctrine

## Validation steps

1. Verify AC-1: Player can trigger suggestion flow mid-session
2. Verify AC-2: Forseti completes 3-step discuss → confirm → submit flow before emitting tag
3. Verify AC-3: `[CREATE_SUGGESTION]` block is only emitted after explicit player confirmation
4. Verify AC-4: Category mapping is applied correctly from the defined taxonomy
5. Verify AC-5: Tag block is invisible to player, player sees only a confirmation message

## Regression checks

1. Reproduce the original user-reported flow or feature entry point and confirm the prior defect/behavior gap is resolved.
2. Verify adjacent gameplay or UX behavior remains intact after the change.
3. Confirm the scoped release artifact still matches the approved feature brief and acceptance criteria.
