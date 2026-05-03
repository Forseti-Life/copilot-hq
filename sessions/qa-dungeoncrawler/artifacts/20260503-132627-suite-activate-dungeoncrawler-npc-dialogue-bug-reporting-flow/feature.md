# Feature Brief: Party-member NPCs who explicitly agree to travel with the player (e.g., Gribbles in The Gilded Ta...

- Work item id: dungeoncrawler-npc-dialogue-bug-reporting-flow
- Website: dungeoncrawler.life
- Module: _TBD (PM to assign)_
- Status: ready
- Release: 20260412-dungeoncrawler-release-ab

- Priority: P1
- PM owner: pm-dungeoncrawler
- Dev owner: dev-dungeoncrawler
- QA owner: qa-dungeoncrawler
- Source: community_suggestion NID 43 (Talk to Forseti intake)
- Category: Technical Improvement
- Created: 2026-05-02

## Goal

Party-member NPCs who explicitly agree to travel with the player (e.g., Gribbles in The Gilded Tankard) are not persisting into the next room's entity list after navigation. They disappear from the room inventory on arrival despite having joined the party.

## Non-goals

_PM to define during acceptance criteria refinement._

## Acceptance Criteria

_PM to write. See `templates/01-acceptance-criteria.md`._

## Security acceptance criteria

- Authentication/permission surface: Preserve the existing player/GM permission boundaries for the affected flow; do not introduce new anonymous or cross-campaign access without explicit follow-up review.
- CSRF expectations: Any new or changed write actions, forms, or commands must continue to use the existing CSRF/session protections before state changes persist.
- Input validation: Validate user-controlled identifiers, gameplay references, and generated payload fields before storage, state mutation, or rendering.
- PII/logging constraints: Do not log raw player prompts, private campaign state, or bug-report payload details beyond the current approved debug surface.

## Mission Alignment

This feature was submitted by a user via the "Talk to Forseti" channel. It aligns with the mission:
> "Democratize and decentralize internet services by building community-managed versions of core systems
> for scientific, technology-focused, and tolerant people."

_PM to confirm: how specifically does this feature advance that mission?_

## Original User Message

> Gribbles agreed to come with me. Unless he changed his mind and snuck away while I was talking and I failed a perception check.

## Risks

_PM to assess during triage._

## Security & Release Integrity Gate

- Board security review required: no
- Board approval artifact: n/a
- Intake risk signals: none

## Latest updates

- 2026-05-03: Auto-groomed from approved intake handoff; acceptance criteria + test plan were materialized and the item was scoped to `20260412-dungeoncrawler-release-ac`.

- 2026-05-02: Auto-groomed from approved intake handoff; acceptance criteria + test plan were materialized and the item was scoped to `20260412-dungeoncrawler-release-ac`.

- 2026-05-02: Auto-groomed from approved intake handoff; acceptance criteria + test plan were materialized and the item was scoped to `20260412-dungeoncrawler-release-ab`.

- 2026-05-02: Created from community_suggestion NID 43 via suggestion-triage.sh
