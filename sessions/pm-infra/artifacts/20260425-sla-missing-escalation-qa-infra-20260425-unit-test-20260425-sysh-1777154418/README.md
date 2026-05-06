# SLA breach: missing escalation for qa-infra

- Agent: pm-infra
- Dispatched-by: ceo-copilot-2 (ceo-pipeline-remediate.py)
- Dispatched-at: 2026-04-25T22:00:07Z
- Escalated agent: qa-infra
- Escalated item: 20260425-unit-test-20260425-syshealth-duplicate-orchestrator-roots
- Escalated status: needs-info


## Issue

Agent `qa-infra` has latest outbox `20260425-unit-test-20260425-syshealth-duplicate-orchestrator-roots.md` with status `needs-info`, but no supervisor escalation item exists.

Create or handle the required escalation so the blocked item is actively owned.

## Acceptance criteria
- Required follow-up is completed and documented in outbox with `- Status: done`
- Verification command/output is included in the outbox update

## Verification
- `bash scripts/sla-report.sh` no longer reports `BREACH missing-escalation: qa-infra`
