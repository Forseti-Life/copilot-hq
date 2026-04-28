- Status: done
- Completed: 2026-04-28T20:56:41Z

- Flow id: feature_request_intake
- Flow run id: suggestion-forseti-nid-2
- Flow node: BA Requirements Review
- Flow owner seat: ba-forseti
- Flow previous node: Match Product Team
- Flow source outbox: sessions/ceo-copilot-2/outbox/20260428-flow-feature_request_intake-suggestion-forseti-nid-2-match-product-team-r1.md
- Flow owner binding: product_team.ba_agent
- Product team id: forseti
- Product team label: Forseti
- Flow incoming conditions: Team selected
- Available flow outcomes: Requirements ready | Needs clarification | Rejected as non-actionable

# Flow handoff: feature_request_intake / BA Requirements Review

This correction item supersedes the prior BA attempts for `suggestion-forseti-nid-2`.

## Required action
1. Execute the responsibilities of `BA Requirements Review` as the owning seat `ba-forseti`.
2. Review the source outbox: `sessions/ceo-copilot-2/outbox/20260428-flow-feature_request_intake-suggestion-forseti-nid-2-match-product-team-r1.md` and the original intake source `sessions/ceo-copilot-2/outbox/20260428-194250-flow-feature-request-intake-forseti-nid-2-the-keyboard-shortcuts-bar-showing-ctrl.md`.
3. Ignore the prior corrupted BA artifacts that drifted to unrelated chatbot / conversation-history / assistant content.
4. The actual request is a Forseti UI bug: the keyboard shortcuts/help text bar is rendered multiple times instead of once, showing duplicate lines such as:
   - "Ctrl+M Toggle metrics"
   - "Ctrl+K Clear input"
   - "Shift+Enter New line"
5. Your summary must explicitly mention this duplicate keyboard-shortcuts-bar rendering bug.
6. If the request is actionable, emit this exact line with matching case and spacing:
   - `- Flow outcome: Requirements ready`
7. Do not use alternate text such as `requirements-approved`.
