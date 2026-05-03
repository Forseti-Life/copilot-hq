# Acceptance Criteria — dungeoncrawler-auto-bug-report

1. a confirmed player suggestion emitted via `[CREATE_SUGGESTION]` block is reliably persisted to the DungeonCrawler backlog with fields: Summary, Category, Original suggestion text. - Verification method: submit a test suggestion in GM chat, confirm the `[CREATE_SUGGESTION]` block fires, and verify the resulting backlog record contains all three fields and is visible to the product team

## Source of truth

- Intake flow run: `suggestion-dungeoncrawler-nid-44`
- Canonical feature brief: `features/dungeoncrawler-auto-bug-report/feature.md`
