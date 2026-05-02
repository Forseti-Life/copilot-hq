# Acceptance Criteria — dungeoncrawler-auto-bug-reporting

1. When a player transitions between rooms, the map view fails to reset — tokens, objects, and tile states from the previous room remain visible on the new room's map instead of being replaced by the current room's layout.
2. The change preserves adjacent gameplay behavior and does not regress the surrounding user flow.
3. QA can verify the original report or requested behavior directly in the live Dungeoncrawler/Forseti experience.

## Source of truth

- Intake flow run: `suggestion-dungeoncrawler-nid-41`
- Canonical feature brief: `features/dungeoncrawler-auto-bug-reporting/feature.md`
