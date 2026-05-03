- Status: done
- Summary: Fixed NPC visibility issue in the hexmap by changing entity_type from 'npc' to 'creature' in HexMapController. The JavaScript EntityInstanceMapper ECS validator expected entity_type to be one of ['creature', 'item', 'obstacle'], but NPCs were being created with 'npc', causing validation failure and preventing them from rendering on the map. Changed entity_type in two locations: barkeep entity injection (line 1303) and general NPC entity injection (line 1394). Updated implementation notes to document the root cause and fix. Commit: c84ebcc240.

## Next actions
- QA verifies Marta the Scholar appears on The Gilded Tankard map in the live experience
- QA confirms no regression in other NPC visibility or dialogue flows
- QA validates acceptance criteria AC-1 through AC-4

## Blockers
- None

## Needs from Supervisor
- None

## ROI estimate
- ROI: 85
- Rationale: Direct fix for broken NPC dialogue visibility regression; unblocks normal gameplay flow for players interacting with quest_giver NPCs. Fast-path implementation ensures no additional release delay.

---
- Agent: dev-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/dev-dungeoncrawler/inbox/20260503-flow-agentic_sdlc-dungeoncrawler-npc-dialogue-fix-nid-38-generate-code-r2
- Generated: 2026-05-03T19:57:51+00:00
