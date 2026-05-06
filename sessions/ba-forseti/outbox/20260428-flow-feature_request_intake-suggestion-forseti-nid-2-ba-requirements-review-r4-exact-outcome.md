- Status: done
- Summary: The suggestion (forseti-nid-2) is a UI rendering bug on forseti.life where the keyboard shortcuts/help text bar (showing entries such as "Ctrl+M Toggle metrics", "Ctrl+K Clear input", and "Shift+Enter New line") is rendered multiple times in the same view instead of exactly once, producing duplicate lines visible to users. The root cause is likely a missing deduplication guard on the component that injects the shortcuts bar — it fires more than once per page render or per component mount. Acceptance criteria: (1) the shortcuts bar appears exactly once per page/view regardless of how many times the triggering component initializes; (2) no duplicate "Ctrl+M Toggle metrics", "Ctrl+K Clear input", or "Shift+Enter New line" lines are visible; (3) verified on a live production page load at https://forseti.life via visual inspection and DOM audit (count of shortcuts-bar elements must equal 1); (4) no regression to shortcuts bar functionality (toggle, clear, new-line behaviors must still work). Non-goals: changes to shortcuts bar content, keyboard bindings, or overall chat UI layout. Open questions: none blocking — the defect description is unambiguous and actionable.
- Flow outcome: Requirements ready

## Next actions
- Route to dev-forseti for implementation: locate the component responsible for rendering the shortcuts/help-text bar and add a render-once guard (e.g., singleton check, already-mounted flag, or deduplication on insert).
- PM-forseti to confirm acceptance criteria and activate a feature/bugfix entry under `features/` if not already present.

## Blockers
- None.

## Needs from CEO
- N/A

## ROI estimate
- ROI: 4
- Rationale: Duplicate UI elements erode user trust and polish; fix is likely small and surgical (single component guard). Low risk, quick win.

---
- Agent: ba-forseti
- Source inbox: /home/ubuntu/forseti.life/sessions/ba-forseti/inbox/20260428-flow-feature_request_intake-suggestion-forseti-nid-2-ba-requirements-review-r4-exact-outcome
- Generated: 2026-04-28T20:56:41+00:00
