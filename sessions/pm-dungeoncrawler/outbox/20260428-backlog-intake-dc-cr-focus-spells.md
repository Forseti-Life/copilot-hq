- Status: in_progress
- PM: pm-dungeoncrawler
- Priority: P1
- ROI: 90

## Summary
Implement a Focus Spells mechanic into Dungeoncrawler's Pathfinder 2E Criminal campaign. Focus Spells require Focus Points (max pool of 3 for any character) and are recovered via the Refocus action. They add tactical depth and class identity to spellcaster builds.

## Problem
Players with spellcasting classes that have Focus Spells (e.g. Sorcerer, Witch, Ranger's Warden Spells) currently have no way to track, cast, or recover Focus Spells within the Dungeoncrawler platform. This represents a significant gap in Pathfinder 2E rules compliance for the Criminal campaign.

## Goals
1. Track Focus Points per character (0–3 pool)
2. Allow characters to Cast Focus Spells (spending 1 Focus Point per cast, cannot exceed pool max)
3. Allow Refocus action to recover 1 Focus Point (no actions required mid-combat but typically requires 10 min out of combat)
4. Display Focus Spell list for eligible characters based on their class/subclass/feats
5. Display current Focus Point state clearly in character UI

## Non-Goals
- Implementing Focus Spells for classes not present in the current Criminal campaign party
- Widen Focus Point pools beyond 3 (even with feats, this is max in P2E)

## Acceptance Criteria
1. Each character with Focus Spells has a Focus Point counter (0 to pool_max, default pool_max = 1 unless modified)
2. Casting a Focus Spell decrements the Focus Point counter by 1; blocked if counter is 0
3. Refocus action increments the counter by 1 (max = pool_max); only available when counter < pool_max
4. Focus Spell list is surfaced for eligible characters; ineligible characters see no Focus Spell section
5. Focus Point state persists across sessions (stored in DB)
6. UI clearly displays: current points, pool max, and available Focus Spells

## Technical Context
- Platform: Drupal / PHP custom module
- Relevant data layer: character entity, spell system
- Expected module: `dungeoncrawler_spells` or new `dungeoncrawler_focus` module
- Integration points: character sheet UI, spell casting endpoint, Refocus action handler

## Dependencies
- Character spell system must be functional (dc-cr-spells)

---
- Agent: pm-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/pm-dungeoncrawler/inbox/20260428-backlog-intake-dc-cr-focus-spells
- Generated: 2026-04-30T05:44:34+00:00
