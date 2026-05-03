# Acceptance Criteria — dc-gm-auto-bug-report

1. Room descriptions, discovered environmental details, and GM-generated features should be cached to a persistent room template library upon first generation. When a room is re-instanced in a future campaign run, the system should load from the cached template rather than regenerating, preserving consistency and reducing compute overhead.
2. The change preserves adjacent gameplay behavior and does not regress the surrounding user flow.
3. QA can verify the original report or requested behavior directly in the live Dungeoncrawler/Forseti experience.

## Source of truth

- Intake flow run: `suggestion-dungeoncrawler-nid-47`
- Canonical feature brief: `features/dc-gm-auto-bug-report/feature.md`
