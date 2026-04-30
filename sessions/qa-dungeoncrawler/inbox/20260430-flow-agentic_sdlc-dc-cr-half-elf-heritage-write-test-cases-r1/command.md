- Flow id: agentic_sdlc
- Flow run id: dc-cr-half-elf-heritage
- Flow node: Write Test Cases
- Flow owner seat: qa-dungeoncrawler
- Flow previous node: PM Scope Rebaseline
- Flow source outbox: sessions/pm-dungeoncrawler/outbox/20260430-flow-agentic_sdlc-dc-cr-half-elf-heritage-pm-scope-rebaseline-r1.md
- Flow owner binding: product_team.qa_agent
- Product team id: dungeoncrawler
- Product team label: Dungeoncrawler
- Flow incoming conditions: Resume test design
- Available flow outcomes: Scope decision required
- Flow direct route available: yes

# Flow handoff: agentic_sdlc / Write Test Cases

This inbox item was routed automatically from `PM Scope Rebaseline` after `pm-dungeoncrawler` completed the previous step.

## Required action
1. Execute the responsibilities of `Write Test Cases` as the owning seat `qa-dungeoncrawler`.
2. Review the source outbox: `sessions/pm-dungeoncrawler/outbox/20260430-flow-agentic_sdlc-dc-cr-half-elf-heritage-pm-scope-rebaseline-r1.md` for the completed upstream context.
3. If this node has branching outcomes, include one or more `- Flow outcome:` lines in your outbox using the exact allowed values listed above.
4. If this node has only one direct next step, no Flow outcome line is required.
5. If the work is complete but needs a graph-defined branch (for example scope rebaseline, QA failure, or requested changes), keep `- Status: done` and use the matching `- Flow outcome:` line instead of escalating through a legacy `needs-*` artifact.
6. If product-team selection is required for this node, include `- Product team id: <team-id>` using one of the listed product-team IDs.
- Agent: qa-dungeoncrawler
- Status: pending
