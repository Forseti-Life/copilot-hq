# Feature Brief: Ritual Magic System

- Work item id: dc-cr-rituals
- Website: dungeoncrawler
- Module: dungeoncrawler_content
- Status: ready
- Release: 20260412-dungeoncrawler-release-aa
- Release decision: queued into 20260412-dungeoncrawler-release-aa on 2026-05-01 after CEO release-z backlog rebaseline; do not close via dc-cr-spells-ch07 until ritual execution and validation flows are independently covered.
- Defer reason: lower priority than next-release MVP scope and still needs dedicated ritual execution, participant validation, and failure-path coverage beyond the shipped spell catalog.
- Related parent: dc-cr-spells-ch07 (spell catalog shipped, but ritual subsystem remains separately actionable)
- Priority: P3
- PM owner: pm-dungeoncrawler
- Dev owner: dev-dungeoncrawler
- QA owner: qa-dungeoncrawler
- Source: PF2E Core Rulebook (Fourth Printing), lines 1–300
- Category: rule-system
- Created: 2026-02-26

## Goal

Implement rituals as a distinct form of magic separate from standard spellcasting: they require extended casting time (minutes to days), do not consume spell slots, require skill checks (primary and secondary casters), and have meaningful failure/critical-failure consequences. Rituals enable narrative-scale magic (planar travel, resurrection, commune) that changes the course of a dungeoncrawler campaign.

## Source reference

> "This section includes the rules for spellcasting, hundreds of spell descriptions, focus spells used by certain classes, and rituals." (Chapter 7: Spells)

## Implementation hint

Reuse the shipped ritual catalog data already present in `CharacterManager::RITUALS` as the source seed, but move ritual execution into a dedicated subsystem. The remaining gap is not catalog content; it is runtime flow: explicit ritual initiation outside normal spell-slot casting, participant validation for primary/secondary casters, long-duration progress tracking, and outcome resolution (success / failure / critical failure) with GM approval gates for uncommon or rare rituals. Keep rituals distinct from standard `spell` actions even when they share source chapter coverage with `dc-cr-spells-ch07`.

## Mission alignment

- [x] Aligns with democratized community game experience
- [x] Does not add surveillance or restrict community access

## Security acceptance criteria

- Security AC exemption: spellcasting/rules-engine scope only; no new public routes expected beyond existing spellcasting, downtime, or session-action handlers.
