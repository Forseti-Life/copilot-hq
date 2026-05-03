- Flow id: agentic_sdlc
- Flow run id: dungeoncrawler-auto-bug-report
- Flow node: Code Review
- Flow owner seat: agent-code-review
- Flow previous node: Generate Code
- Flow source outbox: sessions/dev-dungeoncrawler/outbox/20260503-flow-agentic_sdlc-dungeoncrawler-auto-bug-report-generate-code-r2.md
- Product team id: dungeoncrawler
- Product team label: Dungeoncrawler
- Available flow outcomes: Approved | Changes requested

# Flow handoff: agentic_sdlc / Code Review

This inbox item was routed automatically from `Generate Code` after `dev-dungeoncrawler` completed the previous step.

## Required action
1. Execute the responsibilities of `Code Review` as the owning seat `agent-code-review`.
2. Review the source outbox: `sessions/dev-dungeoncrawler/outbox/20260503-flow-agentic_sdlc-dungeoncrawler-auto-bug-report-generate-code-r2.md` for the completed upstream context.
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
- Treat the upstream dev outbox as a handoff receipt, not the only source of truth; verify the repo state and approved feature docs still match before approving.
- If `sessions/dev-dungeoncrawler/outbox/20260503-flow-agentic_sdlc-dungeoncrawler-auto-bug-report-generate-code-r2.md` omits the exact implementation commit hash, changed-file context, or verification needed to understand the diff, finish with `- Status: done` and `- Flow outcome: Changes requested`; do not guess or drift into `needs-info`.
- An `Approved` verdict must cite the exact reviewed artifact path(s) and the verified implementation commit hash or equivalent repo-state evidence from the upstream handoff.
- If you identify substantive problems, enumerate finding severity + file path + recommended fix pattern and use `- Flow outcome: Changes requested` instead of a legacy blocker response.

## Required artifacts
- Review the upstream implementation handoff: `sessions/dev-dungeoncrawler/outbox/20260503-flow-agentic_sdlc-dungeoncrawler-auto-bug-report-generate-code-r2.md`.
- Review the approved feature brief: `features/dungeoncrawler-auto-bug-report/feature.md`.
- Review the acceptance criteria: `features/dungeoncrawler-auto-bug-report/01-acceptance-criteria.md`.
- Review the test plan when present: `features/dungeoncrawler-auto-bug-report/03-test-plan.md`.
- Cite at least one reviewed artifact path in your `- Summary:` or findings section.
