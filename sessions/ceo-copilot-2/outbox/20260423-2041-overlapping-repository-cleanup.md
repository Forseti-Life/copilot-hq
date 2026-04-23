- Status: done
- Summary: Resolved the active overlapping-repository ambiguity in HQ operations by making `/home/ubuntu/forseti.life` the canonical live HQ root across ownership files, startup guidance, seat instructions, and runtime scripts. The nested `copilot-hq/` directory is now explicitly treated as a subtree/export artifact only, not a second repo root or inbox-scan root. Updated the org-chart validator, ownership audit, executor prompt, checkpoint/cron/reply-processing paths, and related runtime tooling so the cleaned-up root model is actually enforced in operations rather than only documented.

## Next actions
- Clear merge health and review unrelated local modifications before any HQ commit/push.
- Leave historical checklist/export references alone unless they affect live runtime behavior; live instructions are now normalized.

## Blockers
- None for the ownership-path cleanup itself.

## ROI estimate
- ROI: 180
- Rationale: Removes a systemic source of ghost inbox processing, conflicting repo-root assumptions, and ownership ambiguity across active seats.
