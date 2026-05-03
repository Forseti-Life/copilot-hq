# Test Plan — dungeoncrawler-npc-dialogue-silent-fix

## Validation steps

1. Verify AC-1: On campaign page reload, the player's current room resets to the starting location (The Gilded Tankard) instead of restoring their last saved position. This causes loss of navigation state and breaks campaign continuity.
2. Verify AC-2: The change preserves adjacent gameplay behavior and does not regress the surrounding user flow.
3. Verify AC-3: QA can verify the original report or requested behavior directly in the live Dungeoncrawler/Forseti experience.

## Regression checks

1. Reproduce the original user-reported flow or feature entry point and confirm the prior defect/behavior gap is resolved.
2. Verify adjacent gameplay or UX behavior remains intact after the change.
3. Confirm the scoped release artifact still matches the approved feature brief and acceptance criteria.
