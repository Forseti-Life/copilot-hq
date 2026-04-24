I'll work through the grooming steps systematically. Let me start by reading my seat instructions and then executing each step.

- Status: in_progress
- Summary: Beginning grooming task for 20260412-forseti-release-r. Reading seat instructions and auditing existing backlog.

---

Let me execute the work now.

**Step 0: Read seat instructions**

<tool_call>
{"name": "bash", "arguments": {"command": "cat org-chart/agents/instructions/pm-forseti.instructions.md 2>/dev/null || echo 'FILE NOT FOUND'"}}
</tool_call>
<tool_response>
# pm-forseti Seat Instructions

## Authority
This file is owned by the `pm-forseti` seat.

## Supervisor
- Supervisor: `ceo-copilot`

## Website scope
- `forseti.life`

## Owned file scope
- `features/forseti-*/**` (feature briefs, acceptance criteria, test plans for forseti.life)
- `sessions/pm-forseti/**` (inbox, outbox, artifacts)
- `org-chart/agents/instructions/pm-forseti.instructions.md` (this file)

## Relevant paths
- Drupal site root: `/home/ubuntu/forseti.life/sites/forseti`
- Drupal web root: `/home/ubuntu/forseti.life/sites/forseti/web`
- Custom modules: `/home/ubuntu/forseti.life/sites/forseti/web/modules/custom`
- Custom themes: `/home/ubuntu/forseti.life/sites/forseti/web/themes/custom`
- Features directory: `/home/ubuntu/forseti.life/features/`
- Site: `forseti.life`

## Working releases
- Current release (Dev executing): `20260412-forseti-release-q` — monitor only; do NOT modify scope.
- Next release (groom target): `20260412-forseti-release-r`

## Release commands (forseti.life)
- Activate scope: `./scripts/pm-scope-activate.sh forseti.life <release-id>`
- QA handoff: `./scripts/pm-qa-handoff.sh forseti.life <release-id> <feature-id>`
- Release signoff: `./scripts/release-signoff.sh forseti.life <release-id>`

## Escalation path
- Escalate to: `ceo-copilot`

## Verification commands
- Audit existing backlog:
```bash
python3 - <<'PY'
import pathlib, re
site = 'forseti.life'
for fm in sorted(pathlib.Path('features').glob('*/feature.md')):
    text = fm.read_text(encoding='utf-8')
    if f'- Website: {site}' not in text:
        continue
    m = re.search(r'^- Status:\s*(.+)$',

---
- Agent: pm-forseti
- Source inbox: /home/ubuntu/forseti.life/sessions/pm-forseti/inbox/20260423-groom-20260412-forseti-release-r
- Generated: 2026-04-24T08:14:14+00:00
