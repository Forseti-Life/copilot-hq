- Flow id: agentic_sdlc
- Flow run id: dungeoncrawler-auto-bug-report
- Flow node: Generate Code
- Flow owner seat: dev-dungeoncrawler
- Flow previous node: PM Scope Decision
- Flow source outbox: sessions/pm-dungeoncrawler/outbox/20260503-flow-agentic_sdlc-dungeoncrawler-auto-bug-report-pm-scope-rebaseline-r1.md
- Flow owner binding: product_team.dev_agent
- Product team id: dungeoncrawler
- Product team label: Dungeoncrawler
- Flow incoming conditions: Resume implementation
- Available flow outcomes: Scope decision required
- Flow direct route available: yes

# Flow handoff: agentic_sdlc / Generate Code

This feature has been re-queued after source-of-truth package repair. The canonical scope is room persistence and room reuse for navigation, as described in `features/dungeoncrawler-auto-bug-report/feature.md`.

## Required action
1. Review `features/dungeoncrawler-auto-bug-report/feature.md`, `features/dungeoncrawler-auto-bug-report/01-acceptance-criteria.md`, and `features/dungeoncrawler-auto-bug-report/03-test-plan.md`.
2. Execute the responsibilities of `Generate Code` as the owning seat `dev-dungeoncrawler`.
3. If implementation is ready for the normal review path, finish with `- Status: done` and no `- Flow outcome:` line.
4. If implementation cannot continue until PM re-baselines scope again, finish with `- Status: done` and `- Flow outcome: Scope decision required`.
5. Include commit hashes or concrete repo-state evidence for any implementation you claim complete.

## Accepted status values
- The only accepted `- Status:` values are: `done | in_progress | blocked | needs-info`
- Use `- Status: done` when this node is complete, even if you also need a graph-defined branch via `- Flow outcome:`.
- Use `- Status: in_progress` only when you are actively continuing the same inbox item and it should remain queued.
- Use `- Status: blocked` or `- Status: needs-info` only when you truly cannot proceed and must escalate.

## Required outbox template
```md
- Status: done | in_progress | blocked | needs-info
- Summary: <one paragraph>

## Next actions
- <next action>

## Blockers
- <explicit blocker or `None`>

## Needs from Supervisor
- <specific need, or `None` when status is done/in_progress>
```
- Agent: dev-dungeoncrawler
