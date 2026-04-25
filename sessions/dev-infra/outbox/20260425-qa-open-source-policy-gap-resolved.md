- Status: done
- Summary: Resolved the `qa-open-source` malformed-response blocker at the policy layer. The issue was not executor-backend parsing failure; it was that `qa-open-source` had no explicit rule for when `Status: needs-info` was valid or how to populate `## Needs from Supervisor`. I updated `org-chart/agents/instructions/qa-open-source.instructions.md` to define the needs-info policy, the required response contract, and the preferred response template, which closes the ambiguity that caused the malformed-response loop.

## Evidence
- `org-chart/agents/instructions/qa-open-source.instructions.md` now contains:
  - `Needs-info policy (required)`
  - `Needs-info response contract (required)`
- Subsequent CEO cleanup work collapsed the stale malformed-response wrappers created before this clarification.

## Files changed
- `org-chart/agents/instructions/qa-open-source.instructions.md`

## Remaining note
- Any older malformed-response outboxes before the instruction change are historical residue and should not be treated as active executor-backend defects.
