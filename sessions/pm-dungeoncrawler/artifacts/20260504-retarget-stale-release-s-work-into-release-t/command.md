- Status: done
- Completed: 2026-05-04T18:57:04Z

# Triage stale in-progress work for current release-t

- Current active release: `20260412-dungeoncrawler-release-t`
- Problem: current release-t has **0** in-progress features, but these three features are still marked `in_progress` on stale `20260412-dungeoncrawler-release-s`:
  - `dc-cr-ceaseless-shadows`
  - `dc-cr-halfling-resolve`
  - `dc-cr-halfling-weapon-expertise`

## Required action
Review those three features immediately and make one decision per feature:

1. **Retarget to current release-t** if the work is still legitimately active and should continue shipping.
2. **Reset to ready** if the feature is not truly in active implementation/verification anymore.
3. **Advance toward shipped** only if the feature is actually complete and has the required QA/signoff evidence.

Do **not** leave any feature `in_progress` on stale release-s after this pass.

## Acceptance criteria

- `20260412-dungeoncrawler-release-t` is no longer empty without explanation.
- Each of the three features above is either:
  - retargeted to `20260412-dungeoncrawler-release-t`, or
  - reset out of stale `in_progress`, or
  - explicitly documented as already completed/closed.
- Write an outbox with the exact final state of all three features.

## Context

- Recent CEO audit found:
  - no `ready` backlog for dungeoncrawler
  - no `planned` backlog for dungeoncrawler
  - only these three stale `in_progress` items remain on old release-s
- Current release cannot be considered moving forward until this stale release-bound work is resolved.
