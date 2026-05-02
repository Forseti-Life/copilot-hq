# Feature Brief: Combat initiation against the Skittering Dire Rat in the Deep Dungeon Antechamber did not trigger...

- Work item id: dungeoncrawler-npc-autonomy-doctrine
- Website: dungeoncrawler.life
- Module: _TBD (PM to assign)_
- Status: ready
- Release: 20260412-dungeoncrawler-release-ab

- Priority: P1
- PM owner: pm-dungeoncrawler
- Dev owner: dev-dungeoncrawler
- QA owner: qa-dungeoncrawler
- Source: community_suggestion NID 51 (Talk to Forseti intake)
- Category: Technical Improvement
- Created: 2026-05-02

## Goal

Combat initiation against the Skittering Dire Rat in the Deep Dungeon Antechamber did not trigger the combat phase transition — no initiative was rolled, rounds did not begin, and the enemy did not take mechanical actions against the player. The combat_initiation action type needs to reliably transition the game into structured combat rounds with initiative tracking and enemy turns firing automatically.

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

> Suggestion summary: Combat initiation against the Skittering Dire Rat in the Deep Dungeon Antechamber did not trigger the combat phase transition — no initiative was rolled, rounds did not begin, and the enemy did not take mechanical actions against the player. The combat_initiation action type needs to reliably transition the game into structured combat rounds with initiative tracking and enemy turns firing automatically.
Suggestion category: technical_improvement
Original player request:
Is he fighting back? Is this another defect that the initiative/combat rounds didn't take over when we started combat? Yes, and log a defect

Context:
Campaign ID: 14
Room ID: 40b33d90-5d6f-44c9-9af0-7ead22d4abed
Character ID: 35

Recent conversation reference (last 22 lines, max 150):
Bob Brevant: OK, lets keep going
Game Master: ## Deep Dungeon Antechamber **ATMOSPHERE** — The air here is cold enough to see your breath, and something about the low granite ceiling presses down on you like a held threat. This chamber is older than anything you've passed through — built by hands that answered to no lord you'd recognize. **SIGHT** — Massive, moisture-worn granite blocks form the walls and floor, their surfaces traced with hairline cracks weeping mineral deposits. The only illumination comes from a sickly blue-green bioluminescent fungus colonizing the northern wall in uneven patches, casting long, shifting shadows across the chamber. Two passages are visible — the way back south toward the Gilded Tankard Antechamber, and a rough opening to the west that leads deeper into what appears to be a collapsed mine section. **SOUND** — Near silence, broken only by the faint drip of water somewhere in the dark and a faint, wet scrabbling sound close to the floor. **SMELL / TASTE** — Mineral reek and cold stone, undercut by something distinctly organic — rot that has been disturbed recently, not just the old kind that's settled into the walls. **CREATURES PRESENT** — Crouched near the base of the northern wall, partially obscured by shadow, is a bloated rodent the size of a large cat. Its fur is patchy and mangy, its yellowed incisors bared. Its naked tail lashes the stone floor in agitated, irregular sweeps. It has noticed you — and it is not retreating. --- The rat holds its ground, whiskers trembling, haunches coiled. It's between you and the fungal wall. Your move, Bob.
Bob Brevant: I'll attack with my sword. lets get it on.
Game Master: Bob doesn't have a sword in his inventory — his close-quarters options are his **Clan Dagger**, **Club**, or **Staff**. He lunges forward anyway, the intent clear: put this oversized vermin down fast. Pick your weapon and I'll resolve the strike — or I'll default to the **Club** if you want to just get it done. --- Assuming **Club** (1d6+1 bludgeoning, +3 attack): Bob winds up and swings hard at the snarling rat. Rolling attack: d20 + 3 (STR +1, trained +2) vs. the rat's AC.
Skittering Dire Rat: *a sharp, wet shriek that rises into something almost deliberate* SKREE — you come into MY dark, you breathe MY cold air, and now you swing at me with a STICK? *haunches drop lower, tail whipping stone* I have eaten things bigger than your ambition, soft-flesh.
Bob Brevant: It's a longsword... and I'll get you bitch!
Game Master: Bob's inventory contains no longsword — the closest blade he carries is his **Clan Dagger**. He's making his intent unmistakably clear though, closing the distance fast. Going with **Club** (best melee option) — Bob charges in and swings hard at the snarling rat. **Attack roll:** d20 + 3 → rolling now.
Skittering Dire Rat: *a sound like wet gravel grinding — laughter, almost* You can't even NAME your own weapon, soft-flesh — and you think you'll catch ME?
Bob Brevant: OK, I'll be stabbing with my dagger from here on out...
Game Master: Clan Dagger it is — a short, vicious blade, perfect for getting in close and making it count. Bob drives forward, leading with the dagger in a hard, low thrust at the rat's flank. **Attack roll:** d20 + 3 (STR +1, trained +2) → rolling now.
Skittering Dire Rat: *a low, wet hiss, body dropping flat and sideways along the stone* OH — NOW you find a real tooth. Good. I like it better when they bleed slow.
Bob Brevant: We need a fast forward feature for player to the next obvious reaction for his character. Turn it on or off as you play
Game Master: Great suggestion, Bob — I'll get that logged to the backlog. Here's how I'd summarize it: **"Add a 'Fast Forward' toggle that automatically resolves the player character's most contextually obvious next action (attack continuation, basic move, etc.) without requiring explicit input each turn — toggleable on/off mid-session."** Does that capture what you're after?
Bob Brevant: Yep, and now lets keep stabbing Skittering
Game Master: Confirmed — logging that suggestion now. Bob doesn't waste another second, lunging in low and driving the Clan Dagger hard at the rat's exposed flank. **Attack roll:** d20 + 3 (STR +1, trained +2) — let's see if steel finds flesh.
Skittering Dire Rat: *a sharp, skittering snarl, teeth snapping at the empty air where the blade just passed* You want to KEEP stabbing? Then KEEP bleeding — every inch you push, I take a piece of you with me!
Bob Brevant: AM I hitting this rat? Lets get him
Game Master: Bob's been pressing hard — let's make it official and put steel to skin. Rolling the attack now: **d20 + 3**, targeting the bloated rat's flank in a driving thrust. *The rat coils tight, teeth bared, hackles up — it hasn't broken, but it's watching the blade.*
Skittering Dire Rat: *low chittering, weight shifting back onto haunches, eyes locked on the blade hand* You want to KNOW if you're hitting me? You'd FEEL it if you were — I'd be at your throat before the blood finished dripping!
Bob Brevant: Is he fighting back? Is this another defect that the initiative/combat rounds didn't take over when we started combat?
Game Master: You're right to flag this, Bob — it looks like the combat system didn't formally transition into structured combat rounds when you engaged the rat. The Skittering Dire Rat should have initiative, be taking attacks, and the round structure should be running. That's a bug. I'm logging this now: **"Combat initiation against the Skittering Dire Rat in the Deep Dungeon Antechamber did not trigger the combat phase transition — no initiative was rolled, rounds did not begin, and the enemy did not take mechanical actions against the player."** Does that summary capture it accurately? In the meantime, let me fire the combat initiation properly right now so the system can take over:
Bob Brevant: Yes, and log a defect

## Risks

_PM to assess during triage._

## Security & Release Integrity Gate

- Board security review required: no
- Board approval artifact: n/a
- Intake risk signals: none

## Latest updates

- 2026-05-02: Auto-groomed from approved intake handoff; acceptance criteria + test plan were materialized and the item was scoped to `20260412-dungeoncrawler-release-ac`.

- 2026-05-02: Auto-groomed from approved intake handoff; acceptance criteria + test plan were materialized and the item was scoped to `20260412-dungeoncrawler-release-ab`.

- 2026-05-02: Created from community_suggestion NID 51 via suggestion-triage.sh
