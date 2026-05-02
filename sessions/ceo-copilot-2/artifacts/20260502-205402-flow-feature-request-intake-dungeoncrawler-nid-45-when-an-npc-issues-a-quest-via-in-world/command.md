- Status: done
- Completed: 2026-05-02T20:54:28Z

- Flow id: feature_request_intake
- Flow run id: suggestion-dungeoncrawler-nid-45
- Flow node: Receive Feature Request
- Flow owner seat: ceo-copilot-2

# Incoming feature request from community suggestion

- Source system: Drupal community_suggestion
- Source site: dungeoncrawler
- Suggestion NID: 45
- Source conversation node: n/a
- Suggestion category: Technical Improvement
- Created at: 2026-05-02 20:53
- Suggested product team: dungeoncrawler

## Request summary

When an NPC issues a quest via in-world dialogue, the quest objective should be automatically
created and logged to the player's active quest tracker. Currently, quest briefings delivered
through NPC conversation are not persisting as tracked objectives, leaving players without a formal
mission record after the conversation ends.

## Suggestion title

When an NPC issues a quest via in-world dialogue, the quest objective should be automatically cre...

## Original user message

Suggestion summary: When an NPC issues a quest via in-world dialogue, the quest objective should be
automatically created and logged to the player's active quest tracker. Currently, quest briefings
delivered through NPC conversation are not persisting as tracked objectives, leaving players without
a formal mission record after the conversation ends. Suggestion category: technical_improvement
Original player request: Log the defect/enhancement for us — no formal quest objective is currently
logged for this dungeon run despite Gribbles issuing the job via dialogue.  Context: Campaign ID: 14
Room ID: b2b538a8-ed05-48d3-8e57-4ed9b9c79bda Character ID: 35  Recent conversation reference (last
15 lines, max 150): Bob Brevant: Is every room that is generated saved off into the library? If not,
it should be. make them all re-usable if the right circumstance calls for it. I think there is code
for auto generating rooms. We may need to just add a check on path/destionation routing by the
NPC/Player that we check to see if there is an already generated map. Game Master: *stepping out of
the GM voice for a moment* That's a solid systems suggestion, Bob — room persistence and reuse would
add real depth to the world. Reusing already-generated maps when navigation paths align makes sense
both for performance and world consistency. Let me make sure I capture this accurately before
logging it. Here's how I'd summarize it for the backlog: > *"All generated rooms should be saved to
a persistent library. When an NPC or player navigates to a destination, the routing system should
first check whether a matching room already exists in the library before generating a new one —
enabling room reuse when context and path align."* Does that capture it accurately? I'll submit it
once you confirm. Bob Brevant: confirm Game Master: Logged and submitted to the dev team — good call
on that one. Room reuse will make the world feel a lot more grounded as the dungeon grows. *Back in
the GM's chair.* The drip and echo of The Dripping Dark Chamber surrounds you. Vex stands a few
paces ahead, sharp eyes scanning the shadows with quiet patience. Bob Brevant: I'll draw my sword
and prepare for a fight if we are going to have one. Fix my torch to the front of my shield for
light. "OK Vex, Lets get going. What direction?" I'll start moving in whatever direction he says,
carefully. "What are we here for?" Game Master: Bob draws his sword with a practiced pull, the blade
catching the fractured torchlight as water droplets bead along the steel. He tucks a torch to the
face of his shield — makeshift but effective, casting a steady cone of amber light into the
chamber's dripping dark. He turns toward his lean, sharp-featured companion, voice low and direct.
Vex's eyes flick toward Bob at the question, a brief, unreadable expression crossing his face before
he glances down one of the passages leading deeper into the dark. Vex: "North passage. Less standing
water — means less noise when we move." A beat, then, flat and matter-of-fact: "You hired me to get
you through. What you're *here* for is your business." Bob Brevant: To the GM, What am I here for?
Gribbles hired me to do this and then didn't tell me what I"m doing? Game Master: *Fair question,
and a good one to flag.* Looking back through the session, Gribbles gave you the job but the
specific objective wasn't captured before you set out — that's a gap in the quest briefing, not your
memory failing you. Mechanically, you have a mission hook but no explicit objective logged. If you
want the full picture, your best move is to either press Vex for what *he* knows about the job, or
consider heading back to Gribbles for clarification before going deeper. Bob Brevant: Vex, what do
you know about why Gribbles hired me? GM... We do have a Quest tracking inventory? We reuse quests
via the library right? Game Master: Vex goes still at the question, jaw tightening almost
imperceptibly — the kind of reaction that suggests he knows more than he's volunteered. He turns to
face Bob directly, arms crossed, expression carefully neutral. --- *On your system questions:* Yes —
there is quest tracking infrastructure in place, and the intent is for quests to be persistent and
reusable through the same library system discussed earlier. However, based on what's in your active
character sheet right now, **no quest objective is currently logged for this dungeon run** — which
is either a briefing gap (Gribbles didn't formally issue it) or a logging issue on the system side.
Worth investigating. Vex: "Gribbles told me to get you in and back out alive. What's down here worth
retrieving — that part he kept close." A pause. "Which tells me it's either valuable enough to be
careful with, or dangerous enough that he didn't want you walking in knowing the full shape of it."
Bob Brevant: Log the defect/enhancement for us. I"ll move foward.. There a door evident yet, or are
we just walking through a dark, huge room? Game Master: I noticed that no formal quest objective is
currently logged for this dungeon run despite Gribbles issuing the job — that's a system gap worth
flagging. Here's how I'd log it: **"When an NPC issues a quest via in-world dialogue, the quest
objective should be automatically created and logged to the player's active quest tracker.
Currently, quest briefings delivered through NPC conversation are not persisting as tracked
objectives, leaving players without a formal mission record."** Does that capture it? I'll submit it
to the backlog now. --- Bob moves forward, torch-shield cutting amber light through the drip and
dark. The chamber doesn't end in a wall — it narrows ahead into a rough-hewn passage, the ceiling
dropping lower as the floor slopes slightly downward, puddles catching the torchlight in broken
flashes. No door yet, just stone and shadow and the sound of water finding its way deeper into the
earth. Bob Brevant: yea, that captures it. keep moving forward.

## Intake notes

- This request was automatically seeded by `scripts/suggestion-intake.sh`.
- Legacy PM-only suggestion triage has been retired; use the `feature_request_intake` flow to review, clarify, defer, reject, or approve this request.
- If approved, the intake flow should decide whether to materialize or update a backlog feature artifact before launching delivery.
- Drupal node edit URL: /node/45/edit

## Cross-site warning

This suggestion mentions another site or product alias. Do not assume the source site owns it without review.

- `infrastructure` appears to reference product team `infrastructure`
