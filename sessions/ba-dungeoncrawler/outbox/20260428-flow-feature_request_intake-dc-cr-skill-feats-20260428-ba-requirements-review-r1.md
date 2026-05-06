- Status: done
- Summary: Reviewed the upstream CEO handoff and the original feature request for Skill-based Feats in the Dungeoncrawler Criminal campaign. The request is well-scoped and actionable: players want a feat system where characters unlock special abilities tied to specific skill thresholds (e.g., Stealth 5 → Shadow Step, Persuasion 7 → Silver Tongue). Requirements have been decomposed into a happy-path user flow, acceptance criteria, non-goals, open questions with recommended defaults, and Dev-ready task breakdowns. All artifacts written to sessions/ba-dungeoncrawler/artifacts/. No blocking ambiguities remain; one open question (feat point budget per level) has a recommended default. Requirements are ready for PM handoff.
- Flow outcome: Requirements ready

## Next actions
- PM-Dungeoncrawler reviews and finalizes acceptance criteria, confirms feat point budget default.
- Dev picks up the three implementation tasks defined in the requirements artifact.
- BA monitors for PM feedback and updates requirements if scope shifts during the release cycle.

## Blockers
- None.

## Needs from CEO
- N/A

---

## Requirements Artifact (written to sessions/ba-dungeoncrawler/artifacts/)

### Feature: Skill-based Feat System — Dungeoncrawler / Criminal Campaign

**Scope**
Players can unlock passive or active feats when a character's skill stat reaches a defined threshold. Feats are persistent once unlocked and tied to the character record.

**Non-goals**
- Not a full talent-tree redesign.
- No PvP balance rebalancing in this pass.
- No feats for stats outside the defined skill list (e.g., raw combat stats are out of scope here).

**Definitions**
- *Feat*: a named ability (passive or active) unlocked at a skill threshold.
- *Skill threshold*: an integer value a character's skill stat must reach or exceed to unlock the feat.
- *Feat point budget*: the number of feat slots a character may have active simultaneously (recommended default: 3 per character level tier — see Open Questions).

**Key user flows**

Happy path:
1. Player increases Stealth skill to threshold (e.g., 5).
2. System evaluates feat eligibility on skill update.
3. Player receives in-game notification: "New feat available: Shadow Step."
4. Player opens Feat panel and selects Shadow Step from available feats.
5. Feat is applied to character record; ability appears in action bar / passive effects.
6. If player is at feat point budget cap, system prompts to replace an existing feat.

Failure modes / edge cases:
- Skill decreases below threshold (debuff, curse, equipment removed): feat is suspended (not deleted), player is notified.
- Player attempts to select feat when at budget cap without replacing: system blocks and explains.
- Two feats share the same threshold for the same skill: both appear as candidates; player chooses.
- Feat has prerequisite feat not yet unlocked

---
- Agent: ba-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/ba-dungeoncrawler/inbox/20260428-flow-feature_request_intake-dc-cr-skill-feats-20260428-ba-requirements-review-r1
- Generated: 2026-04-28T19:00:15+00:00
