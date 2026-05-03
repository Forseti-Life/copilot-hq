# Feature Brief: All generated rooms should be saved to a persistent library. When an NPC or player navigates to a...

- Work item id: dungeoncrawler-auto-bug-report
- Website: dungeoncrawler.life
- Module: _TBD (PM to assign)_
- Status: ready
- Release:

- Priority: P1
- PM owner: pm-dungeoncrawler
- Dev owner: dev-dungeoncrawler
- QA owner: qa-dungeoncrawler
- Source: community_suggestion NID 44 (Talk to Forseti intake)
- Category: Technical Improvement
- Created: 2026-05-02

## Goal

All generated rooms should be saved to a persistent library. When an NPC or player navigates to a destination, the routing system should first check whether a matching room already exists in the library before generating a new one — enabling room reuse when context and path align.

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

> Suggestion summary: All generated rooms should be saved to a persistent library. When an NPC or player navigates to a destination, the routing system should first check whether a matching room already exists in the library before generating a new one — enabling room reuse when context and path align.
Suggestion category: technical_improvement
Original player request:
Is every room that is generated saved off into the library? If not, it should be. make them all re-usable if the right circumstance calls for it. I think there is code for auto generating rooms. We may need to just add a check on path/destionation routing by the NPC/Player that we check to see if there is an already generated map.

Context:
Campaign ID: 14
Room ID: b2b538a8-ed05-48d3-8e57-4ed9b9c79bda
Character ID: 35

Recent conversation reference (last 3 lines, max 150):
Bob Brevant: Is every room that is generated saved off into the library? If not, it should be. make them all re-usable if the right circumstance calls for it. I think there is code for auto generating rooms. We may need to just add a check on path/destionation routing by the NPC/Player that we check to see if there is an already generated map.
Game Master: *stepping out of the GM voice for a moment* That's a solid systems suggestion, Bob — room persistence and reuse would add real depth to the world. Reusing already-generated maps when navigation paths align makes sense both for performance and world consistency. Let me make sure I capture this accurately before logging it. Here's how I'd summarize it for the backlog: > *"All generated rooms should be saved to a persistent library. When an NPC or player navigates to a destination, the routing system should first check whether a matching room already exists in the library before generating a new one — enabling room reuse when context and path align."* Does that capture it accurately? I'll submit it once you confirm.
Bob Brevant: confirm

## Risks

_PM to assess during triage._

## Security & Release Integrity Gate

- Board security review required: no
- Board approval artifact: n/a
- Intake risk signals: none

## Latest updates

- 2026-05-03: Re-queued into flow-managed SDLC from legacy release scope.

- 2026-05-03: Auto-groomed from approved intake handoff; acceptance criteria + test plan were materialized and the item was scoped to `20260412-dungeoncrawler-release-ac`.

- 2026-05-02: Auto-groomed from approved intake handoff; acceptance criteria + test plan were materialized and the item was scoped to `20260412-dungeoncrawler-release-ac`.

- 2026-05-02: Auto-groomed from approved intake handoff; acceptance criteria + test plan were materialized and the item was scoped to `20260412-dungeoncrawler-release-ab`.

- 2026-05-02: Created from community_suggestion NID 44 via suggestion-triage.sh
