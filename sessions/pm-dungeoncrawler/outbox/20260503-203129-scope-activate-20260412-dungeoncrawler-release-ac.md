- Status: done
- Summary: Reviewed all 10 ready features for release `20260412-dungeoncrawler-release-ac`. Several are near-duplicates (multiple variants of NPC dialogue bug reporting and auto-bug-report). Selected the 5 most distinct and highest-value features for activation: `dungeoncrawler-npc-autonomy-doctrine`, `dungeoncrawler-npc-dialogue-fix-nid-38`, `dungeoncrawler-player-suggestion-intake`, `dungeoncrawler-suggestion-nid-45`, and `dungeoncrawler-npc-dialogue-silent-fix`. Ran `scripts/pm-scope-activate.sh dungeoncrawler <feature_id>` for each; all five activated successfully (status set to `in_progress`). Dev and QA inbox items were created for each activated feature per the script output. Release now has 5 features in scope, satisfying the "at least 3 activated" done condition and staying well under the 10-feature auto-close cap.

## Next actions
- Monitor dev inbox items for implementation progress on the 5 activated features.
- On completion of dev work, trigger QA verification for each feature against the acceptance criteria.
- Watch the 24h auto-close timer; if all 5 features reach `done` before 24h, close the release proactively.
- Remaining 5 near-duplicate features (`dc-gm-auto-bug-report`, `dungeoncrawler-auto-bug-report`, `dungeoncrawler-auto-bug-reporting`, `dungeoncrawler-npc-dialogue-bug-reporting-flow`, `dungeoncrawler-npc-dialogue-failure-reporting`) should be reviewed for consolidation or deferral to the next release cycle.

## Blockers
- None.

## Needs from CEO
- None.

## ROI estimate
- ROI: 40
- Rationale: Activating these features unblocks the current release cycle and directly improves GM-player interaction quality (NPC autonomy, bug reporting, player suggestions), which are core to Dungeoncrawler's live experience. Near-duplicate deduplication reduces future PM overhead.

---
- Agent: pm-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/pm-dungeoncrawler/inbox/20260503-203129-scope-activate-20260412-dungeoncrawler-release-ac
- Generated: 2026-05-03T21:00:14+00:00
