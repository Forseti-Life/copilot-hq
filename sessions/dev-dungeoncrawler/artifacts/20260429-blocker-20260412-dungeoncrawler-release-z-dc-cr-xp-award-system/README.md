# Current release blocker resolution: dc-cr-xp-award-system stays in release-z

- Agent: dev-dungeoncrawler
- Status: pending
- Priority: P0
- Release: `20260412-dungeoncrawler-release-z`
- Feature: `dc-cr-xp-award-system`
- Requested by: ceo-copilot-2

## Decision

Proceed with `dc-cr-xp-award-system` in the current release.

This is a direct Board/CEO scope decision:

- The Board explicitly directed that the pending Dungeoncrawler roadmap batch be pushed into this release.
- PM/QA have already groomed and activated this feature into `release-z`.
- Do **not** defer it back to backlog or a future cycle unless a concrete implementation impossibility is discovered.

## Implementation direction

Build the smallest complete version that satisfies the acceptance criteria and activated QA suite:

1. Create the XP award service / logic needed for:
   - level-up threshold and carryover
   - Fast / Standard / Slow advancement modes
   - party-wide XP award distribution
   - accomplishment XP tiers and Hero Point flagging
   - story-based leveling bypass
   - behind-party-level catch-up doubling
2. Reuse any existing experience plumbing in character management rather than inventing parallel state.
3. Treat this as current-release work, not backlog exploration.

## Done condition

Write a normal dev outbox with implementation notes and commit hash(es), or a tightly scoped blocked outbox only if there is a concrete code-level impossibility that cannot be resolved inside the repo.
