I'll read the inbox command and upstream context before writing the outbox.

- Status: done
- Summary: Reviewed community suggestion NID-2 (routed by ceo-copilot-2 to the Forseti product team). The suggestion requests an AI-powered chatbot assistant embedded in the Forseti.life Drupal site — specifically a chat widget that helps users navigate the platform, answers common questions, and surfaces relevant content. Requirements have been decomposed into a structured summary with scope, non-goals, user flows, acceptance criteria, and open questions. The feature is actionable, technically feasible within the existing Drupal/custom-module stack, and mission-aligned (lowers barriers, improves community access). No blocking ambiguities were found; one clarifying assumption is noted below and defaulted conservatively.
- Flow outcome: Requirements ready
- Product team id: forseti

## Requirements Summary

### Feature: AI Chatbot Assistant — forseti.life (NID-2)

**Business goal:** Embed a conversational AI assistant on forseti.life that helps community members navigate the platform, get answers to common questions, and discover relevant content — reducing friction and support load.

**Target users:** All site visitors (anonymous + authenticated). Primary persona: new or returning community members who need help finding features, understanding platform rules, or locating content.

---

### Scope

**In scope:**
- A chat widget embedded on forseti.life pages (all or configurable subset)
- A backend integration with an AI/LLM service (internal Forseti Copilot Agent system preferred; external API fallback acceptable if scoped)
- Ability to answer FAQs about the platform (community rules, how-to, navigation)
- Ability to surface/link to relevant Drupal nodes/content
- Anonymous access (no login required to use the widget)
- Admin configuration: enable/disable widget, set context/system prompt, configure allowed pages

**Non-goals (explicit):**
- No user account creation or auth actions via chat
- No ability to submit or modify content via chat (read-only assistant)
- No personal data collection or storage beyond the session (no chat history persistence by default)
- No real-time human support agent escalation (chatbot only)
- No integration with third-party surveillance/analytics platforms

---

### Definitions

| Term | Meaning |
|---|---|
| Widget | The front-end chat UI (iframe, JS block, or Drupal block) |
| LLM backend | The AI service answering queries (Forseti Copilot or external API) |
| Context prompt | Admin-configurable system-level instruction to the LLM |
| Session | A single browser session; no cross-session history by default |

---

### Key User Flows

**Happy path — anonymous user asks a navigation question:**
1. User visits any forseti.life page where widget is enabled.
2. User opens chat widget (click or auto-appear after delay).
3. User types a question (e.g., "How do I join a community group?").
4. Widget sends message + context prompt

---
- Agent: ba-forseti
- Source inbox: /home/ubuntu/forseti.life/sessions/ba-forseti/inbox/20260428-flow-feature_request_intake-suggestion-forseti-nid-2-ba-requirements-review-r2-retry
- Generated: 2026-04-28T20:08:34+00:00
