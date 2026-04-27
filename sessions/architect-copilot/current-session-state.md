# Architect Session State — architect-copilot

> **Rolling file. Overwrite this at the end of each working session (and briefly before starting each task).**
> Last updated: 2026-04-27 during Drupal LangGraph richer flow-definition implementation

---

## Currently Working On

Deepening the `drupal_langgraph` process-flow control panel so new flows capture
real structure hints, not just metadata.

### Current state

- The console has:
  - a first-class `Flows` tab
  - a flow registry
  - a flow detail page
  - a new-flow form
  - flow context carried across lifecycle and subsection pages
- Feature Progress is flow-scoped in the console.
- Legacy `/langgraph/*` routes now redirect to canonical console routes.
- The New Process Flow form now captures:
  - state schema summary
  - nodes
  - routing rules
  - tools
  - prompt notes
- The flow detail page now shows those structure details for each flow.
- Drupal caches were rebuilt and the expanded form and detail surfaces were
  verified.

### Key decisions

1. Keep the control-plane work inside `drupal_langgraph`.
2. Build toward a real graph-management console in slices.
3. Use the existing config-backed flow registry for richer draft definitions
   before introducing more complex editing models.

### Next actions

1. Add edit/archive/version actions for custom process flows.
2. Expand from structure hints into editable node/routing/tool/prompt surfaces.
3. When the next IA pass begins, move lifecycle tabs under the selected flow
   workspace.
