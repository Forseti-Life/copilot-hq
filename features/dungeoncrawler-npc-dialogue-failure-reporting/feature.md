# Feature Brief: Players currently have no way to view the status of bugs or feature requests they have submitted....

- Work item id: dungeoncrawler-npc-dialogue-failure-reporting
- Website: dungeoncrawler.life
- Module: _TBD (PM to assign)_
- Status: ready
- Release: 20260412-dungeoncrawler-release-ac

- Priority: P1
- PM owner: pm-dungeoncrawler
- Dev owner: dev-dungeoncrawler
- QA owner: qa-dungeoncrawler
- Source: community_suggestion NID 54 (Talk to Forseti intake)
- Category: Technical Improvement
- Created: 2026-05-03

## Goal

Players currently have no way to view the status of bugs or feature requests they have submitted. A status tracking view should be added — either within the game interface or via a player-accessible backlog — showing at minimum whether each submission is pending, triaged, in progress, or resolved. This supports transparency and player trust in the feedback system.

## Non-goals

_PM to define during acceptance criteria refinement._

## Acceptance Criteria

_PM to write. See `templates/01-acceptance-criteria.md`._

## Mission Alignment

This feature was submitted by a user via the "Talk to Forseti" channel. It aligns with the mission:
> "Democratize and decentralize internet services by building community-managed versions of core systems
> for scientific, technology-focused, and tolerant people."

_PM to confirm: how specifically does this feature advance that mission?_

## Original User Message

> Suggestion summary: Players currently have no way to view the status of bugs or feature requests they have submitted. A status tracking view should be added — either within the game interface or via a player-accessible backlog — showing at minimum whether each submission is pending, triaged, in progress, or resolved. This supports transparency and player trust in the feedback system.
Suggestion category: technical_improvement
Original player request:
Lets log another bug about not being able to see the status of the features and bugs we have requested.

Context:
Campaign ID: 14
Room ID: 7f2f1051-5f88-45a2-a66a-0f7063900001
Character ID: 35

Recent conversation reference (last 45 lines, max 150):
Bob Brevant: Where is Martha?
Game Master: A woman sits not far from the bar, her back slightly hunched over a table buried under layers of parchment, open tomes, and a glinting crystal-bound codex. Her quill moves in short, precise strokes — she's deep in concentration, cross-referencing something that clearly demands her full attention. She hasn't looked up yet.
Bob Brevant: I'm looking at the gilded tankard map and I don't see her on there
Game Master: That sounds like a display bug worth flagging. Marta the Scholar is confirmed present in this room's data — she should be visible on the map as a quest giver near the bar area. I'm logging this as a bug for the dev team. Here's how I'd summarize it: *"Marta the Scholar (quest_giver NPC) is present in The Gilded Tankard room inventory but does not appear on the in-game room map for the player."* Does that capture the issue accurately?
Bob Brevant: yes
Game Master: Logged — the dev team will have eyes on it. In the meantime, Marta is very much present, quill still moving, a few paces from the bar. You can approach her whenever you're ready.
Bob Brevant: Hey Martha, where should we go?
Game Master: Bob strides over to the scholar's table with characteristic directness. At the sound of his voice, the woman's quill pauses mid-stroke — she looks up from her codex, eyes sharp and alert behind what appears to be many hours of close reading. Her gaze moves from Bob's face to his gear and back again, a flicker of recognition crossing her expression.
Bob Brevant: You able to talk Martha?
Game Master: Marta sets her quill down deliberately and folds her hands atop the open codex — a clear signal that she's giving Bob her full attention now. She holds his gaze steadily, expression composed but expectant, waiting to hear more before committing to a response.
Bob Brevant: looks like anothe rdefect
Game Master: It looks like Marta's dialogue system isn't responding — she's reacting to Bob's presence but her voice isn't coming through. I'll log this one too. Here's how I'd capture it: *"Marta the Scholar does not produce dialogue when addressed by the player in The Gilded Tankard, despite showing clear body language reactions — NPC dialogue system appears silent for this entity."* Does that summary work?
Bob Brevant: yes, you need to give that ticket the full context of the system and this chat on that ticket. Do you currently do that?
Game Master: Logging it now — and that's a fair question. The summary I submit includes your original message text, but it does **not** automatically attach the full conversation history or system context to the ticket. That's a limitation worth flagging as its own suggestion. Would you like me to log a second item: *"Bug reports submitted via the GM suggestion flow should automatically include relevant conversation history and system context (room state, NPC data, active character) so developers have full reproduction details without manual reconstruction."* Does that capture what you're after?
Bob Brevant: OK, Yes
Game Master: Both items are now logged. The dev team will have the full conversation context and system state attached going forward — no more reconstructing bugs from scratch. Marta remains at her table, hands folded, waiting patiently. The NPC dialogue system is still the blocker for now.
Bob Brevant: hey gibbles can you talk?
Game Master: Bob turns from Marta's table and addresses the third figure in the room — a small, wiry individual who had been lurking near the bar, nursing what appears to be a half-empty tankard. At the sound of his name, Gribbles Rindsworth swivels on his stool and fixes Bob with a long, appraising look — one eyebrow climbing slowly toward his hairline. I'll note that if Gribbles also stays silent, we've got a second instance of the same NPC dialogue bug. Let's see if his system responds.
Gribbles Rindsworth: *snorts* Yeah, I can talk. Whether I *want* to is a different matter. What do you want?
Bob Brevant: this is the kickoff in our game to go adventure are you a quest giver?
Game Master: Gribbles sets his tankard down with a deliberate *clunk*, leaning back on his stool with the air of someone deciding how much they feel like being useful today. He studies Bob for a long moment, arms crossing slowly across his chest — the look of a man weighing something carefully before he commits to an answer.
Gribbles Rindsworth: Quest giver. *huffs* That's a generous way to put it. I've got a problem I'd rather someone else deal with, and if there's coin in it for you, maybe we call that close enough.
Bob Brevant: Sure, but you have to join me. Deal?
Game Master: Gribbles goes very still for a moment — then a slow, unreadable expression crosses his face. He uncrosses his arms, picks up his tankard, and takes a long, deliberate sip, eyes never leaving Bob over the rim.
Gribbles Rindsworth: *sets the tankard down slow* Join you. You're asking the man with the problem to come along and watch you solve it. That's... not how I pictured this going, but fine — long as I'm not the one walking through any doors first.
Bob Brevant: deal. Where do we go?
Game Master: Gribbles slides off his stool and jerks his chin toward the heavy door at the far end of the tavern — the one that leads down into the dungeon antechamber below. He tucks something into his coat with practiced efficiency, expression settling into the grim focus of a man who has already decided he's going to regret this. Marta glances up from her codex just long enough to track the movement, then returns to her work without a word. The door waits. Beyond it: the Dungeon Antechamber — unexplored, and dark.
Gribbles Rindsworth: Down there. That's where my problem lives — or at least where it started. You're going first.
Bob Brevant: I move into the Dungeon Antechamber
Game Master: Bob shoves the heavy door open without ceremony and steps through into the darkness beyond — actions first, thoughts later.
Gribbles Rindsworth: *under his breath, already two steps behind* Good. That's good. Keep moving — I'm right here, just... giving you plenty of room to work.
Bob Brevant: What type of adventure are we doing today Gribbles?
Game Master: Gribbles pauses mid-step, turning to look at Bob with an expression caught somewhere between disbelief and dark amusement — clearly not expecting casual conversation on the threshold of somewhere dangerous. He gestures sharply ahead, chin jutting toward the unopened antechamber door, then taps the side of his coat where he stashed whatever he pocketed earlier. The answer, it seems, is *forward*.
Gribbles Rindsworth: The kind where you do the dangerous parts and I do the remembering-to-breathe parts. Now quit making small talk and move — something down here's been costing me sleep and I'd like to stop paying that debt.
Bob Brevant: When I am reloading the page for the campaign. it seems to reset back to the Gilded tankard tavern. seems like a defect.
Game Master: You're right to flag that — that's not intended behavior. When you reload the campaign page, the system should restore your last known location, not reset you to the starting tavern. I'm logging this as a bug. Here's how I'd capture it for the dev team: **"On campaign page reload, the player's current room resets to the starting location (The Gilded Tankard) instead of restoring their last saved position. This causes loss of navigation state and breaks campaign continuity."** Does that summary capture the issue accurately? I'll submit it to the backlog once you confirm.
Bob Brevant: yes
Game Master: Logged — the dev team will have that on their plate. Now, back to the matter at hand. Despite the page insisting you're standing in the warm glow of the Gilded Tankard, your last steps carried you toward the Dungeon Antechamber — and that's where the story continues. Shall we pick up where you left off and push through that door?
Bob Brevant: How is the text saved for these game sessions? Shouldn't they permanently saved? doesn't seem like it would take much effort to just keep them for reference. Implement a 1st ruling is the truth if there is a dispute.
Game Master: That's a solid suggestion with a clear design intent — session logs and chat history should be persistently saved, and in any dispute about what was said or agreed to in a session, the first recorded ruling stands as canonical truth. Here's how I'd log this for the dev team: **"Session chat logs and GM rulings should be permanently saved and accessible to players for reference. In the event of any dispute about a ruling or agreed outcome, the first recorded instance is treated as canonical (first ruling is truth). This supports accountability, continuity, and trust in the GM system."** Does that capture it accurately? I'll submit it to the backlog once you confirm.
Bob Brevant: No need to confirm for suggestions is my suggestion. Just take it and do it, don't confirm.
Game Master: Noted — and that's a reasonable quality-of-life ask. Logging it now without requiring confirmation. As for the suggestion itself: skipping the confirmation step would mean ideas go straight to the backlog the moment you voice them, which keeps the gameplay flow tight and uninterrupted.
Bob Brevant: Did we fix our bugs that we reported yesterday?
Game Master: I don't have visibility into the development team's resolution status — bug reports get logged to the backlog, but whether they've been patched, triaged, or are still in queue isn't something I can confirm from this side of the table. Your best bet is to check the backlog or patch notes directly if those are accessible to you. What I *can* tell you is that the page-reset bug appears to still be active — you're sitting in the Gilded Tankard right now when your last confirmed position was the Deep Dungeon Antechamber, which suggests that one hasn't landed yet.
Bob Brevant: Lets log another bug about not being able to see the status of the features and bugs we have requested.

## Risks

_PM to assess during triage._

## Security & Release Integrity Gate

- Board security review required: no
- Board approval artifact: n/a
- Intake risk signals: none

## Latest updates

- 2026-05-03: Auto-groomed from approved intake handoff; acceptance criteria + test plan were materialized and the item was scoped to `20260412-dungeoncrawler-release-ac`.

- 2026-05-03: Created from community_suggestion NID 54 via suggestion-triage.sh
