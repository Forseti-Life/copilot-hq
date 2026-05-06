- Status: ready

## Summary
Rock Runner is a persistent NPC companion that joins the player's party during dungeon crawls. He provides tactical commentary, adapts behavior to party composition, scouts ahead when commanded, and can be dismissed/recalled. Loot-sharing mechanic is explicitly excluded from this release.

## Acceptance Criteria

### AC-01: Rock Runner joins the party
- **Given** a player is in an eligible dungeon room
- **When** the player triggers the Rock Runner encounter (via room event or item use)
- **Then** Rock Runner appears in the party roster with correct stats (HP, speed, attack bonus)
- **And** a join confirmation message is displayed

### AC-02: Rock Runner tactical commentary
- **Given** Rock Runner is in the party
- **When** the player enters a new room or initiates combat
- **Then** Rock Runner delivers a context-appropriate tactical comment (

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260430-flow-agentic_sdlc-dc-cr-rock-runner-write-test-cases-r1
- Generated: 2026-04-30T18:03:02+00:00
