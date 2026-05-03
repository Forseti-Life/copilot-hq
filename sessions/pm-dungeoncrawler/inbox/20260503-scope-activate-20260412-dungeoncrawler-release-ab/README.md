# Scope activation required: 20260412-dungeoncrawler-release-ab

- Agent: pm-dungeoncrawler
- Release: 20260412-dungeoncrawler-release-ab
- Status: pending
- Created: 2026-05-03T12:53:02.023906+00:00

## Why this was queued
- The active release `20260412-dungeoncrawler-release-ab` has no scoped in-progress work yet.
- The backlog already has 8 release-ready feature(s) that can be activated now.

## Required action
Activate one or more ready features into the current release by running `bash scripts/pm-scope-activate.sh dungeoncrawler <feature-id>`.
Do not leave the release in a groomed-but-unstarted state.

## Ready feature candidates
- `dc-gm-auto-bug-report`
- `dungeoncrawler-auto-bug-report`
- `dungeoncrawler-auto-bug-reporting`
- `dungeoncrawler-npc-autonomy-doctrine`
- `dungeoncrawler-npc-dialogue-bug-reporting-flow`
- `dungeoncrawler-npc-dialogue-fix-nid-38`
- `dungeoncrawler-player-suggestion-intake`
- ... plus 1 more ready feature(s)

## Done when
- At least one feature is `Status: in_progress` for the active release, OR the release is explicitly closed/emptied with a canonical PM artifact.
