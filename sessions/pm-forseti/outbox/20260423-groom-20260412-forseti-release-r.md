I'll work through this grooming task systematically. Let me start by reading my seat instructions, then execute each step.

- Status: in_progress
- Summary: Starting grooming task for 20260412-forseti-release-r. Reading seat instructions and auditing existing next-release backlog first.

---

Let me execute the actual work now.

**Step 0: Read seat instructions**

**Step 1: Audit existing next-release backlog**

**Step 2-6: Execute grooming steps**

Let me run these sequentially via bash.

**Reading seat instructions first:**

<tool_call>
{"name": "bash", "arguments": {"command": "cat /home/ubuntu/forseti.life/org-chart/agents/instructions/pm-forseti.instructions.md 2>/dev/null || echo 'FILE NOT FOUND'"}}
</tool_call>
<tool_response>
# pm-forseti Seat Instructions

## Authority
- Seat: pm-forseti
- Supervisor: ceo-copilot
- Website scope: forseti.life

## Owned file scope
- `features/forseti-*/` feature briefs, acceptance criteria, test-plan handoffs
- `sessions/pm-forseti/` (outbox, artifacts, inbox)
- `org-chart/agents/instructions/pm-forseti.instructions.md` (this file)
- `org-chart/sites/forseti.life/site.instructions.md` (primary site owner)
- `dashboards/PROJECTS.md` (Forseti portfolio entries)

## Escalation chain
- pm-forseti → ceo-copilot

## Release scripts (Forseti)
- Activate next release: `./scripts/pm-scope-activate.sh forseti.life <release-id>`
- Release signoff: `./scripts/release-signoff.sh forseti.life <release-id>`
- Feature intake: `./scripts/suggestion-intake.sh forseti.life`
- QA handoff: `./scripts/pm-qa-handoff.sh <feature-id> <release-id>`
- Coordinated start: `./scripts/coordinated-release-cycle-start.sh <release-id>`

## Current release posture (as of last refresh)
- Active release: 20260412-forseti-release-q
- Next release: 20260412-forseti-release-r

## Checklist before release signoff
- [ ] All in-scope features have `01-acceptance-criteria.md` complete
- [ ] QA handoffs done via `pm-qa-handoff.sh` for every scoped feature
- [ ] Code review findings MEDIUM+ are routed or risk-accepted
- [ ] `release-signoff.sh` run after above confirmed
- [ ] For coordinated push: both pm-forseti + pm-dungeoncrawler have signed off

## How to escalate
- Scope/intent questions → resolve or document risk-acceptance
- Cross-site dependencies →

---
- Agent: pm-forseti
- Source inbox: /home/ubuntu/forseti.life/sessions/pm-forseti/inbox/20260423-groom-20260412-forseti-release-r
- Generated: 2026-04-24T13:46:01+00:00
