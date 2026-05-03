- Flow id: agentic_sdlc
- Flow run id: dungeoncrawler-auto-bug-report
- Flow node: Write Test Cases
- Flow owner seat: qa-dungeoncrawler
- Flow previous node: PM Scope Rebaseline
- Flow source outbox: sessions/pm-dungeoncrawler/outbox/20260503-flow-agentic_sdlc-dungeoncrawler-auto-bug-report-pm-scope-rebaseline-r1.md
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
2. Review the source outbox: `sessions/pm-dungeoncrawler/outbox/20260503-flow-agentic_sdlc-dungeoncrawler-auto-bug-report-pm-scope-rebaseline-r1.md` for the completed upstream context.
3. If this node has branching outcomes, include one or more `- Flow outcome:` lines in your outbox using the exact allowed values listed above.
4. If this node has only one direct next step, no Flow outcome line is required.
5. If the work is complete but needs a graph-defined branch (for example scope rebaseline, QA failure, or requested changes), keep `- Status: done` and use the matching `- Flow outcome:` line instead of escalating through a legacy `needs-*` artifact.
6. If product-team selection is required for this node, include `- Product team id: <team-id>` using one of the listed product-team IDs.

## Accepted status values
- The only accepted `- Status:` values are: `done | in_progress | blocked | needs-info`
- Use `- Status: done` when this node is complete, even if you also need a graph branch via `- Flow outcome:`.
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

## Node-specific guidance
- Translate the existing PM-approved feature scope into QA artifacts; do not redefine or rename the feature.
- If `features/dungeoncrawler-auto-bug-report/03-test-plan.md` and `qa-suites/products/dungeoncrawler/features/dungeoncrawler-auto-bug-report.json` already exist and still match the current feature scope, fast-exit with `- Status: done` and cite those exact paths instead of regenerating them.
- Use `- Status: in_progress` only when you are actively continuing the same artifact-authoring work and your outbox cites the exact artifact path(s) already updated plus the next concrete completion step.
- If the upstream PM/dev context contradicts the approved feature docs, finish with `- Status: done` and `- Flow outcome: Scope decision required` rather than inventing a new scope.

## Required artifacts
- Write or update `sessions/qa-dungeoncrawler/artifacts/dungeoncrawler-auto-bug-report-test-plan.md` with the concrete test plan for this feature.
- Write or update `qa-suites/products/dungeoncrawler/features/dungeoncrawler-auto-bug-report.json` with the feature-level suite overlay or equivalent QA coverage metadata.
- Reference the exact artifact path(s) you changed in your `- Summary:` or `## Next actions` section.
