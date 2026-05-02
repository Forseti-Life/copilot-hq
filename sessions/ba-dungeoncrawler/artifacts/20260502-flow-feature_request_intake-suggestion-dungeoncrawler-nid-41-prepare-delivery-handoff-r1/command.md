- Flow id: feature_request_intake
- Flow run id: suggestion-dungeoncrawler-nid-41
- Flow node: Prepare Delivery Handoff
- Flow owner seat: ba-dungeoncrawler
- Flow previous node: PM Scope Decision
- Flow source outbox: sessions/pm-dungeoncrawler/outbox/20260502-flow-feature_request_intake-suggestion-dungeoncrawler-nid-41-pm-scope-decision-r1.md
- Feature id: dungeoncrawler-auto-bug-reporting
- Flow owner binding: product_team.ba_agent
- Product team id: dungeoncrawler
- Product team label: Dungeoncrawler
- Flow incoming conditions: Approved for delivery
- Flow direct route available: yes

# Flow handoff: feature_request_intake / Prepare Delivery Handoff

This inbox item was routed automatically from `PM Scope Decision` after `pm-dungeoncrawler` completed the previous step.

## Required action
1. Execute the responsibilities of `Prepare Delivery Handoff` as the owning seat `ba-dungeoncrawler`.
2. Review the source outbox: `sessions/pm-dungeoncrawler/outbox/20260502-flow-feature_request_intake-suggestion-dungeoncrawler-nid-41-pm-scope-decision-r1.md` for the completed upstream context.
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
- Repeat the exact `- Feature id:` selected by PM; do not rename the work item during the handoff.
- For community suggestions, this handoff is the point where the canonical backlog artifact must exist before downstream delivery starts.
- Use the approved requirements package to support the handoff, but keep the feature definition anchored to `features/<feature-id>/feature.md` rather than the temporary suggestion run id.

## Required artifacts
- Include `- Feature id: <canonical-id>` in the outbox so delivery launch can use the canonical backlog/work-item id.
- For community suggestions, the router will materialize `features/<feature-id>/feature.md` via `scripts/suggestion-triage.sh` before launching downstream delivery.
- For non-suggestion intake runs, ensure `features/<feature-id>/feature.md` already exists before marking the handoff complete.
