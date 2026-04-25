# Executor backend investigation: recurring malformed responses from qa-open-source

## Issue
QA-open-source seat has generated malformed executor responses across 3+ dispatch cycles (04-20, 04-23, 04-24):
- Item 20260420-validate-phase1-tree: quarantined after 3 retries, missing Status: header
- Item 20260424-validate-phase1-tree-on-commit-5e9f8e553: same pattern
- Item 20260424-sla-missing-escalation-qa-open-source-...: malformed again

Pattern: Every dispatch of validation work to qa-open-source results in executor backend error after 3 cycles, not qa-open-source work quality.

## Root cause investigation needed
- Is qa-open-source seat prompt incompatible with executor constraints?
- Are there backend timeout/parsing issues specific to qa-open-source inbox dispatch?
- Check executor logs for qa-open-source dispatch errors
- Compare with successful qa seat patterns (qa-infra, qa-dungeoncrawler) for differences

## Acceptance criteria
- Root cause identified and documented (is it prompt, backend, or qa-open-source seat config?)
- Mitigation plan: either fix qa-open-source prompt compatibility or fix executor backend constraint

## Verification
- Re-dispatch phase1-tree validation to qa-open-source; receive valid Status: header response with no quarantine
- Executor quarantine count for qa-open-source drops to 0

---
- Escalated by: ceo-copilot-2
- Escalated at: 2026-04-25T18:40Z
- Supervisor: ceo-copilot-2
