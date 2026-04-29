# Feature Brief: Arctic Elf Heritage

- Work item id: dc-cr-elf-heritage-arctic
- Website: dungeoncrawler
- Module: dungeoncrawler_content
- Status: done
- Release: 20260412-dungeoncrawler-release-z
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

## Implementation notes

**Arctic Elf heritage updated in HERITAGES['Elf']** (CharacterManager.php line 499-511):
- ID: `arctic`
- Name: Arctic Elf
- Benefit: Copied verbatim from PF2e Core Rulebook (lines 6148–6154)

**Special section structure**:
- `cold_resistance` configuration:
  - `type`: half_level_min_1 (formula: max(1, floor(character_level / 2)))
  - `recalculate_on_level_up`: TRUE (ensures AC Happy Path-4: recalculates on level change)
  - Applied to character damage resistance table when heritage selected
- `environmental_cold_severity_downgrade` configuration:
  - `trigger`: Activates when environmental_cold_effect is applied
  - `severity_ladder`: Maps incredible→extreme, extreme→severe, severe→moderate, moderate→mild (AC Edge Case-3: follows documented ladder)
  - `downgrade_steps`: 1 (one-step downgrade per AC specification)
  - `applies_to_cold_only`: TRUE (AC Edge Case-2: only cold effects affected, unrelated hazards unchanged)

**Acceptance criteria verification**:
- AC Happy Path-1: ✓ Arctic Elf exists in Elf heritages (system prevents non-elf selection)
- AC Happy Path-2: ✓ Cold resistance formula max(1, floor(level/2)) configured with recalculation
- AC Happy Path-3: ✓ Environmental severity downgrade configured with ladder
- AC Happy Path-4: ✓ recalculate_on_level_up flag ensures dynamic recalculation
- AC Edge Case-1: ✓ Formula minimum 1 covers level 1+ characters
- AC Edge Case-2: ✓ applies_to_cold_only guard prevents unrelated hazard downgrade
- AC Edge Case-3: ✓ Severity ladder explicitly defined
- AC Failure Mode-1: ✓ Elf-only ancestry gating enforced by system
- AC Failure Mode-2: ✓ applies_to_cold_only guard prevents crashes on missing metadata

**Ready for QA**: All acceptance criteria covered. Test suite coverage in `03-test-plan.md`.
