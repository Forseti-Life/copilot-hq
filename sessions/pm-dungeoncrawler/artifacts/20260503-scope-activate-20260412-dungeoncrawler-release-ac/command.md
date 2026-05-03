# Scope activation required: 20260412-dungeoncrawler-release-ac

The active release `20260412-dungeoncrawler-release-ac` still has zero scoped in-progress features even though ready backlog exists.
Activate release-ready features using the exact commands below.

## Exact activation commands
```bash
bash scripts/pm-scope-activate.sh dungeoncrawler dc-gm-auto-bug-report
bash scripts/pm-scope-activate.sh dungeoncrawler dungeoncrawler-auto-bug-report
bash scripts/pm-scope-activate.sh dungeoncrawler dungeoncrawler-auto-bug-reporting
bash scripts/pm-scope-activate.sh dungeoncrawler dungeoncrawler-npc-autonomy-doctrine
bash scripts/pm-scope-activate.sh dungeoncrawler dungeoncrawler-npc-dialogue-bug-reporting-flow
bash scripts/pm-scope-activate.sh dungeoncrawler dungeoncrawler-npc-dialogue-failure-reporting
bash scripts/pm-scope-activate.sh dungeoncrawler dungeoncrawler-npc-dialogue-fix-nid-38
bash scripts/pm-scope-activate.sh dungeoncrawler dungeoncrawler-npc-dialogue-silent-fix
```

## Done when
1. At least one listed feature for `20260412-dungeoncrawler-release-ac` is `- Status: in_progress` in `features/<id>/feature.md`.
2. The corresponding Dev and QA inbox items were created.
3. Your outbox cites the feature id(s) you activated and the generated inbox artifact paths.
