# Scope activation required: 20260412-dungeoncrawler-release-ac

- Agent: pm-dungeoncrawler
- Release: 20260412-dungeoncrawler-release-ac
- Status: pending
- Created: 2026-05-03T20:46:39.310830+00:00

## Why this was queued
- The active release `20260412-dungeoncrawler-release-ac` has no scoped in-progress work yet.
- The backlog already has 10 release-ready feature(s) that can be activated now.

## Required action
Run the exact `bash scripts/pm-scope-activate.sh dungeoncrawler <feature-id>` commands listed in `command.md`.
Do not leave the release in a groomed-but-unstarted state.

## Ready feature candidates
- `dc-gm-auto-bug-report`
- `dungeoncrawler-auto-bug-report`
- `dungeoncrawler-auto-bug-reporting`
- `dungeoncrawler-npc-autonomy-doctrine`
- `dungeoncrawler-npc-dialogue-bug-reporting-flow`
- `dungeoncrawler-npc-dialogue-failure-reporting`
- `dungeoncrawler-npc-dialogue-fix-nid-38`
- ... plus 3 more ready feature(s)

## Done when
- At least one feature is `Status: in_progress` for the active release and its Dev/QA inbox items exist, OR the release is explicitly closed/emptied with a canonical PM artifact.
