# Scope activation required: 20260412-forseti-release-x

The active release `20260412-forseti-release-x` still has zero scoped in-progress features even though ready backlog exists.
Activate release-ready features using the exact commands below.

## Exact activation commands
```bash
bash scripts/pm-scope-activate.sh forseti forseti-release-runtime-20260501-200109
```

## Done when
1. At least one listed feature for `20260412-forseti-release-x` is `- Status: in_progress` in `features/<id>/feature.md`.
2. The corresponding Dev and QA inbox items were created.
3. Your outbox cites the feature id(s) you activated and the generated inbox artifact paths.
