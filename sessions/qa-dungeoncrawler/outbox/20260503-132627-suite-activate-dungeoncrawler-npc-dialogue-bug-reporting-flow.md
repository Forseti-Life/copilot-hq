- Status: done
- Summary: Activated QA suite coverage for feature `dungeoncrawler-npc-dialogue-bug-reporting-flow` in release `20260412-dungeoncrawler-release-ab`. Added one e2e suite entry to `qa-suites/products/dungeoncrawler/suite.json` and two permission rules to `org-chart/sites/dungeoncrawler/qa-permissions.json`, all tagged with the correct `feature_id`. Suite validation passed. Three test cases cover the core AC (party NPC persistence after navigation, adjacent gameplay regression, live-experience verifiability). No gaps identified; test branch is ready for Stage 4 regression once Dev implements.
- Flow outcome: Approved

## Next actions
- Dev implements the fix per AC-1: party-member NPCs (e.g., Gribbles) must not persist into the next room's entity list after navigation.
- At Stage 4 regression, QA re-runs `dungeoncrawler-npc-dialogue-bug-reporting-flow-e2e` against the live environment to confirm PASS before Gate 2.

## Blockers
- None.

## Needs from CEO
- None.

## ROI estimate
- ROI: 40
- Rationale: Immersion-breaking NPC bleed between rooms is a directly player-visible defect in the core gameplay loop. Activating suite coverage now unblocks Dev implementation and keeps the release on the Stage 4 regression track without rework.

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260503-132627-suite-activate-dungeoncrawler-npc-dialogue-bug-reporting-flow
- Generated: 2026-05-03T13:32:33+00:00
