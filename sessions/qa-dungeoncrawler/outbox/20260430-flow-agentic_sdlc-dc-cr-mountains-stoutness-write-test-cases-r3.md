- Status: in_progress

## Summary
Passive attribute system for the Criminal campaign that awards a persistent HP bonus at milestone levels based on point accumulation tied to endurance actions in mountain terrain.

## Acceptance Criteria

### AC-01: Point Accumulation
- Given a player character is in a mountain terrain room
- When the character performs an endurance action (e.g., climbing, enduring harsh weather, resisting exhaustion)
- Then the character earns 1 Stoutness Point per qualifying action

### AC-02: Milestone Thresholds
- Milestone levels are: 10, 20, and 30 Stoutness Points
- When a character reaches a milestone, they receive a permanent HP bonus of +5 HP per milestone
- Maximum HP bonus from Stoutness is +15 HP (all three milestones)

### AC-03: Persistence
- Stoutness Points and HP bonuses must persist across sessions (stored in player/character state)
- Points and bonuses must not reset on character death, room transitions, or campaign restarts

### AC-04: Terrain Gate

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260430-flow-agentic_sdlc-dc-cr-mountains-stoutness-write-test-cases-r3
- Generated: 2026-04-30T19:41:57+00:00
