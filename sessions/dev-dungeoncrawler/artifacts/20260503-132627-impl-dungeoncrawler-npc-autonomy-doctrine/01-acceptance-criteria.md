# Acceptance Criteria — dungeoncrawler-npc-autonomy-doctrine

1. Player can trigger suggestion flow mid-session
2. Forseti completes 3-step discuss → confirm → submit flow before emitting tag
3. `[CREATE_SUGGESTION]` block is only emitted after explicit player confirmation
4. Category mapping is applied correctly from the defined taxonomy
5. Tag block is invisible to player, player sees only a confirmation message

## Source of truth

- Intake flow run: `suggestion-dungeoncrawler-nid-51`
- Canonical feature brief: `features/dungeoncrawler-npc-autonomy-doctrine/feature.md`
