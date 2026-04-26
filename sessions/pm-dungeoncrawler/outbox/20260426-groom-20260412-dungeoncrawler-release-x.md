I'll work through this grooming task systematically. Let me start by reading my seat instructions, then audit the backlog and run the required scripts.

## Step 0: Read seat instructions and relevant context

- Status: in_progress
- Summary: Starting grooming task for 20260412-dungeoncrawler-release-x. Reading seat instructions and auditing existing backlog first.

---

Let me check my seat instructions file and relevant context files.

<tool_call>
{"name": "bash", "arguments": {"command": "cat /home/ubuntu/forseti.life/org-chart/agents/instructions/pm-dungeoncrawler.instructions.md 2>/dev/null || echo 'FILE_NOT_FOUND'"}}
</tool_call>
<tool_response>
# Seat Instructions: pm-dungeoncrawler

## Authority
- This file is owned by: `pm-dungeoncrawler`
- Supervisor: `ceo-copilot`

## Website scope
- `dungeoncrawler`

## Owned file scope
- `features/` items tagged `Website: dungeoncrawler`
- `sessions/pm-dungeoncrawler/`
- Coordinated release artifacts for dungeoncrawler releases

## Key paths
- Drupal site root: `/home/ubuntu/forseti.life/sites/dungeoncrawler`
- Features: `/home/ubuntu/forseti.life/features/`
- Scripts: `/home/ubuntu/forseti.life/scripts/`
- QA artifacts: `sessions/qa-dungeoncrawler/artifacts/auto-site-audit/latest/`

## Release workflow
1. Groom backlog → write AC → hand off to QA
2. Activate scope at Stage 0: `./scripts/pm-scope-activate.sh dungeoncrawler <feature-id>`
3. Monitor dev/QA execution
4. Run release signoff when gates pass: `./scripts/release-signoff.sh dungeoncrawler <release-id>`

## Escalation
- Supervisor: `ceo-copilot`
- Matrix: `org-chart/DECISION_OWNERSHIP_MATRIX.md`

## Scripts (quick reference)
- `./scripts/suggestion-intake.sh dungeoncrawler`
- `./scripts/suggestion-triage.sh dungeoncrawler <nid> accept <feature-id>`
- `./scripts/pm-qa-handoff.sh dungeoncrawler <feature-id>`
- `./scripts/pm-scope-activate.sh dungeoncrawler <feature-id>`
- `./scripts/release-signoff.sh dungeoncrawler <release-id>`

## Knowledgebase
- Check `knowledgebase/` for lessons before starting.
- Record new lessons after significant work.

## Last refreshed
- 2026-04-12
</tool_response>

<tool_call>
{"name": "bash", "arguments":

---
- Agent: pm-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/pm-dungeoncrawler/inbox/20260426-groom-20260412-dungeoncrawler-release-x
- Generated: 2026-04-26T21:08:52+00:00
