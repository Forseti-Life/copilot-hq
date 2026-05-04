I'll work through this systematically — reading dev outboxes, running tests, and updating the checklist.

## Step 1: Read the checklist and dev outboxes

**Action:** Read the existing qa-regression-checklist.md and all dev outboxes.

Let me start executing.

<tool_call>
{"name": "bash", "arguments": {"command": "cat /home/ubuntu/forseti.life/org-chart/sites/dungeoncrawler/qa-regression-checklist.md 2>/dev/null || echo 'FILE NOT FOUND'"}}
</tool_call>
<tool_response>
# QA Regression Checklist — DungeonCrawler

**Site:** dungeoncrawler  
**Last updated:** 2026-04-20  
**Owner:** qa-dungeoncrawler

---

## Heritage Features

- [ ] dc-cr-halfling-heritage-gutsy — Gutsy: +1 status bonus to saves vs fear; frightened condition reduced by 1 at end of turn
- [ ] dc-cr-halfling-heritage-hillock — Hillock: regain +1 HP when healed by ally spell/ability
- [ ] dc-cr-goblin-weapon-frenzy — Goblin Weapon Frenzy: +1 status bonus to attack rolls after killing blow in melee; duration 1 round
- [ ] dc-cr-vivacious-conduit — Vivacious Conduit: fast healing 1 while in Conduit stance (elementalist)

## UI / Display Features

- [ ] dc-ui-character-sheet-heritage-display — Heritage field renders correctly on character sheet (front-end)
- [ ] dc-ui-condition-tracker — Condition tracker: apply/remove conditions, turn-end auto-reduce logic
- [ ] dc-ui-combat-log — Combat log: entries for damage, healing, conditions, status effects

## Access Control

- [ ] Route /dungeoncrawler/character/{id} returns 200 for owner, 403 for non-owner, 403 for anonymous
- [ ] Route /dungeoncrawler/campaign/{id} returns 200 for DM, 403 for non-member anonymous

## Core Regression

- [ ] Character creation: all ancestry/heritage combos save without error
- [ ] Combat round resolution: initiative, attack, damage, HP update in sequence
- [ ] Condition application: conditions stack/replace correctly per ruleset
</tool_response>

<tool_call>
{"name": "bash", "arguments": {"command": "cat /home/ubuntu/forseti.life/sessions/dev-dungeoncrawler/outbox/20260414-203541-impl-dc-cr-halfling-heritage-gutsy.md 2>/dev/null || echo 'FILE NOT FOUND'"}}
</tool_call>
<tool_response>
- Status: done
- Summary: Implemented Gutsy halfling heritage ability: +1 status bonus

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260420-112200-targeted-regression-batch-1
- Generated: 2026-05-04T16:51:48+00:00
