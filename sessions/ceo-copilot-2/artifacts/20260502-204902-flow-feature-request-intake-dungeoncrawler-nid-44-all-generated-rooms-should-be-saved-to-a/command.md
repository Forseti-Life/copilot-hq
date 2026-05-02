- Status: done
- Completed: 2026-05-02T20:49:26Z

- Flow id: feature_request_intake
- Flow run id: suggestion-dungeoncrawler-nid-44
- Flow node: Receive Feature Request
- Flow owner seat: ceo-copilot-2

# Incoming feature request from community suggestion

- Source system: Drupal community_suggestion
- Source site: dungeoncrawler
- Suggestion NID: 44
- Source conversation node: n/a
- Suggestion category: Technical Improvement
- Created at: 2026-05-02 20:48
- Suggested product team: dungeoncrawler

## Request summary

All generated rooms should be saved to a persistent library. When an NPC or player navigates to a
destination, the routing system should first check whether a matching room already exists in the
library before generating a new one — enabling room reuse when context and path align.

## Suggestion title

All generated rooms should be saved to a persistent library. When an NPC or player navigates to a...

## Original user message

Suggestion summary: All generated rooms should be saved to a persistent library. When an NPC or
player navigates to a destination, the routing system should first check whether a matching room
already exists in the library before generating a new one — enabling room reuse when context and
path align. Suggestion category: technical_improvement Original player request: Is every room that
is generated saved off into the library? If not, it should be. make them all re-usable if the right
circumstance calls for it. I think there is code for auto generating rooms. We may need to just add
a check on path/destionation routing by the NPC/Player that we check to see if there is an already
generated map.  Context: Campaign ID: 14 Room ID: b2b538a8-ed05-48d3-8e57-4ed9b9c79bda Character ID:
35  Recent conversation reference (last 3 lines, max 150): Bob Brevant: Is every room that is
generated saved off into the library? If not, it should be. make them all re-usable if the right
circumstance calls for it. I think there is code for auto generating rooms. We may need to just add
a check on path/destionation routing by the NPC/Player that we check to see if there is an already
generated map. Game Master: *stepping out of the GM voice for a moment* That's a solid systems
suggestion, Bob — room persistence and reuse would add real depth to the world. Reusing already-
generated maps when navigation paths align makes sense both for performance and world consistency.
Let me make sure I capture this accurately before logging it. Here's how I'd summarize it for the
backlog: > *"All generated rooms should be saved to a persistent library. When an NPC or player
navigates to a destination, the routing system should first check whether a matching room already
exists in the library before generating a new one — enabling room reuse when context and path
align."* Does that capture it accurately? I'll submit it once you confirm. Bob Brevant: confirm

## Intake notes

- This request was automatically seeded by `scripts/suggestion-intake.sh`.
- Legacy PM-only suggestion triage has been retired; use the `feature_request_intake` flow to review, clarify, defer, reject, or approve this request.
- If approved, the intake flow should decide whether to materialize or update a backlog feature artifact before launching delivery.
- Drupal node edit URL: /node/44/edit
