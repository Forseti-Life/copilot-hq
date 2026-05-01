# Feature Brief: Focus Spells

- Work item id: dc-cr-focus-spells
- Website: dungeoncrawler
- Module: dungeoncrawler_content
- Status: shipped
- Release:
- Scope resolution: Closed via shipped parent dc-cr-spells-ch07 on 2026-05-01 during CEO release-z backlog rebaseline.
- Consolidated into: dc-cr-spells-ch07 (requirements covered in that feature's acceptance criteria)
- Priority: P1
- PM owner: pm-dungeoncrawler
- Dev owner: dev-dungeoncrawler
- QA owner: qa-dungeoncrawler
- Source: PF2E Core Rulebook (Fourth Printing), lines 1–300
- Category: game-mechanic
- Created: 2026-02-26

## Goal

Implement the focus spell subsystem where certain classes and archetypes have a pool of Focus Points (max 3) used to cast focus spells without consuming spell slots. Focus Points recharge on a 10-minute rest (Refocus action). Focus spells auto-heighten to the character's highest spell level, making them consistently powerful throughout a campaign.

## Source reference

> "This section includes the rules for spellcasting, hundreds of spell descriptions, focus spells used by certain classes, and rituals." (Chapter 7: Spells)

## Implementation hint

Character entity fields: `focus_points` (current), `focus_points_max`. Focus spell content type reuses the `spell` content type with flag `is_focus_spell: true`. Refocus action API endpoint restores 1 focus point (up to max). Classes like Champion, Cleric, Druid, Monk, and Sorcerer each have class-specific focus spell pools.

## Mission alignment

- [x] Aligns with democratized community game experience
- [x] Does not add surveillance or restrict community access

## Security acceptance criteria

- Security AC exemption: spellcasting rules and character-state scope only; no new public routes expected beyond existing spellcasting and rest/action handlers.
