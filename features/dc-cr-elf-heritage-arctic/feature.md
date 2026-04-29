# Feature Brief: Arctic Elf Heritage

- Work item id: dc-cr-elf-heritage-arctic
- Website: dungeoncrawler
- Module: dungeoncrawler_content
- Status: in_progress
- Priority: P2
- PM owner: pm-dungeoncrawler
- Dev owner: dev-dungeoncrawler
- QA owner: qa-dungeoncrawler
- Depends on: dc-cr-elf-ancestry, dc-cr-heritage-system
- Source: PF2E Core Rulebook (Fourth Printing), lines 6148–6154
- Category: game-mechanic
- Schema changes: no
- Cross-site modules: none
- Defer reason: 2026-04-28 release recovery decision: remove nonessential blocked elf heritage variants from release-x so Gate 2 can focus on the core character work already in flight. Arctic Elf still lacks concrete cold-resistance implementation and will be reconsidered after elf ancestry stabilization.
- Audit note: 2026-04-27 implementation audit found only a heritage definition with descriptive benefit text. No concrete implementation was found for half-level cold resistance or the environmental cold severity downgrade described in the feature brief.
- Created: 2026-04-06

## Goal

Implement the Arctic Elf heritage granting cold resistance equal to half the character's level (minimum 1) and the ability to treat environmental cold severity as one step less extreme (incredible cold → extreme, extreme cold → severe, severe → moderate, etc.). This heritage enables cold-environment survival builds and interacts with the environmental hazard system.

## Source reference

> Arctic Elf — You dwell deep in the frozen north and have gained incredible resilience against cold environments, granting you cold resistance equal to half your level (minimum 1). You treat environmental cold effects as if they were one step less extreme (incredible cold becomes extreme, extreme cold becomes severe, and so on).

## Implementation hint

Add `cold_resistance: half_level_min_1` to the character's damage resistance table when this heritage is selected. Implement an environmental-severity-downgrade hook: when the encounter engine applies cold environmental effects, check if the character has Arctic Elf and reduce the severity tier by one step before applying damage/conditions. Requires the hazards/environment system to expose a severity-tier modifier interface.

## Mission alignment

- [x] Aligns with democratized community game experience
- [x] Does not add surveillance or restrict community access

## Security acceptance criteria

- Security AC exemption: passive ancestry heritage behavior only; no new routes or input surfaces beyond existing heritage assignment, resistance, and hazard-resolution handlers.
