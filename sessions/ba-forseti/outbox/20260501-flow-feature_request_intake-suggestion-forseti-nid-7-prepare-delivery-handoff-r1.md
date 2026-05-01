- Status: done
- Summary: Reviewed PM scope decision outbox for flow run `suggestion-forseti-nid-7`. PM-forseti approved the community suggestion (Drupal node NID 7) for delivery: add a user-facing "Suggest a Feature" submission form integrated with the forseti-copilot-agent-tracker pipeline, so community members can submit feature ideas that are automatically ingested as suggestions and routed through the `feature_request_intake` flow. The BA delivery handoff package is complete: requirements summary, acceptance criteria, and a structured Dev task breakdown are documented below, ready for `agentic_sdlc` intake by the Forseti product team.

## Requirements Summary

**Feature:** Community Feature Suggestion Form (forseti.life)
**Source suggestion NID:** 7
**Product team:** Forseti
**Approved by:** pm-forseti

### Scope
- A publicly accessible (authenticated users minimum; anonymous TBD by PM) submission form on forseti.life that accepts community feature suggestions.
- On submission, the suggestion is persisted as a Drupal node (or equivalent entity) and automatically routed into the `feature_request_intake` flow as a new flow run.
- Non-goals: no voting/ranking UI in this iteration; no email notification to submitter beyond a confirmation message; no moderation queue UI (admin can use standard Drupal node management).

### Definitions
- **Suggestion**: a short community-submitted idea (title + body + optional category tag) that becomes a flow-managed `feature_request_intake` run.
- **Intake trigger**: the mechanism (hook, rule, or queue worker) that fires `feature_request_intake` after a suggestion node is saved.

### Key user flows

**Happy path (authenticated user)**
1. User navigates to `/suggest` (or equivalent route).
2. User fills in: title (required), description (required), category (optional taxonomy term).
3. User submits form.
4. System saves suggestion as a Drupal node of type `suggestion` (or existing content type if already present).
5. System enqueues/triggers a `feature_request_intake` flow run, setting `Flow run id: suggestion-forseti-nid-<nid>` and `Product team id: forseti`.
6. User sees a confirmation message: "Your suggestion has been received. Thank you!"

**Failure modes / edge cases**
- Empty title or description → inline validation error, form not submitted.
- Duplicate submission (same title within N minutes by same user) → warn user, allow override.
- Intake trigger fails (queue/hook error) → suggestion node is saved; trigger retried on next cron; admin alerted via watchdog/dblog.
- Anonymous user attempts submission (if form is auth-gated) → redirect to login with return URL.

### Assumptions
- A `suggestion` content type either already exists on forseti.life or will be created as part of this feature.
- The `feature_

---
- Agent: ba-forseti
- Source inbox: /home/ubuntu/forseti.life/sessions/ba-forseti/inbox/20260501-flow-feature_request_intake-suggestion-forseti-nid-7-prepare-delivery-handoff-r1
- Generated: 2026-05-01T17:58:12+00:00
