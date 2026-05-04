# Suggestion Intake Batch — 20260504-140201

**Site:** dungeoncrawler.life  
**New suggestions:** 1  
**Status:** Pending PM triage  

## What to do

For each suggestion below:
1. Review summary + original message
2. Update triage decision in `triage/NID-triage.md`
3. Run: `./scripts/suggestion-triage.sh dungeoncrawler <nid> <accept|defer|decline|escalate> [feature-id]`
   - `accept`  → creates `features/<feature-id>/feature.md`, marks Drupal node `in_progress`
   - `defer`   → marks Drupal node `deferred`, queued for next cycle
   - `decline` → marks Drupal node `declined`
  - `escalate`→ routes to board-security review queue, keeps node `under_review`

## Mandatory security gate

If a suggestion clearly asks for security abuse, release-gate/integrity bypass, intentionally destructive behavior,
or a major architecture replatform/rewrite,
do not accept it at PM level. Use `escalate` for human board review first.
Normal product improvements should continue through standard PM triage.

## Quick summary table

| # | NID | Category | Title |
|---|-----|----------|-------|
| 1 | 56 | Technical Improvement | When a player initiates dialogue with an NPC (e.g., Marta th... |

## Suggestions (detail)

---
### NID 56: When a player initiates dialogue with an NPC (e.g., Marta the Scholar), the NPC dialogue system f...

- **Created:** 2026-05-04 14:01
- **Category:** Technical Improvement
- **Conversation:** N/A
- **Drupal URL:** /node/56/edit

**Summary:**
When a player initiates dialogue with an NPC (e.g., Marta the Scholar), the NPC dialogue system
fails to fire and the NPC remains silent, forcing the GM to fill the gap with scene description
instead of NPC speech.

**Original user message:**
looks like your NPC talking bug is still there

**Triage:** _(see triage/NID-56-triage.md)_

