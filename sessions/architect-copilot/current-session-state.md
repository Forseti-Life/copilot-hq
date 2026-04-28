# Architect Session State — architect-copilot

> **Rolling file. Overwrite this at the end of each working session (and briefly before starting each task).**
> Last updated: 2026-04-28 during Drupal LangGraph deprecated-CEO cleanup

---

## Currently Working On

Refining the `drupal_langgraph` Org Chart UX after landing direct drill-in from
diagram nodes into seat detail panels and then clustering the CEO layer so the
first row stays readable without rewriting real reporting lines.

### Current state

- Added a new top-level **Org Chart** admin page to `drupal_langgraph`.
- Introduced an `OrgChartService` that reads:
  - `org-chart/agents/agents.yaml`
  - `org-chart/ownership/module-ownership.yaml`
  - `org-chart/ownership/repository-ownership.yaml`
  - instruction-layer file presence across org-wide / role / site / seat levels
- The Org Chart page now renders:
  - a refactored Chart.js hierarchy diagram with Board as the synthetic root
  - CEO on the second level by default
  - subtree-aware parent/child layout instead of row-based placement
  - node click drill-in to matching seat detail panels
  - an in-node expand/collapse chip for subordinate branches
  - synthetic CEO-level cluster nodes for product leads, shared capabilities,
    executive extensions, and paused seats
  - seat registry and reporting relationships
  - ownership context from module/repository mappings
  - instruction-layer coverage per seat
  - flow-to-seat ownership mapping
- Seat detail panels now gain a temporary highlight when opened from the
  diagram so operators can see which section the chart selected.
- Org Chart guidance and module README were updated to describe the new drill-in
  behavior.
- The CEO layer now resolves to four grouped nodes in Drupal runtime settings
  instead of a flat list of direct reports; capability and paused clusters are
  collapsed by default.
- Deprecated CEO stub seats (`ceo-copilot`, `ceo-copilot-3`) are now filtered
  out of the org-chart service entirely, so they no longer appear in the
  diagram, registry, or org summary counts.
- Built-in flow ownership was normalized to the real seat ID
  `ceo-copilot-2` instead of the generic `drupal_langgraph` label.
- Flow registry/detail/current-flow surfaces now render owners as seat
  relationships rather than raw labels.
- Flow owner help text/schema were updated so owner values are treated as seat
  IDs.
- Drupal caches were rebuilt and the new org chart route was confirmed live.

### Key decisions

1. Keep the control-plane work inside `drupal_langgraph`.
2. Build toward a real graph-management console in slices.
3. Treat seat/agent behavior as a first-class read-only model in the module,
   sourced from org files rather than duplicated into Drupal config.
4. Flow ownership should point to real seat IDs; for HQ orchestration flows the
   owner is `ceo-copilot-2`.
5. Represent the instruction stack explicitly in the UI before introducing any
   editing affordances.
6. The hierarchy needs both a visual overview and a textual registry; the
   diagram is the orientation layer and the seat details remain the source of
   drill-down depth.
7. Tree diagrams need subtree-aware layout and reliable click geometry; naive
   depth-row placement was not good enough for the real seat graph.

### Next actions

1. Decide whether flow owner entry should become a constrained seat selector
   instead of a freeform seat-ID field.
2. Decide whether seat details should stay inline-only or graduate to dedicated
   seat-detail routes once deeper per-seat views exist.
3. Decide whether the seat registry table should mirror the same active/paused
   clustering or filtering options now used in the diagram.
4. Continue the next UX slice around richer node/routing/tool/prompt editing
   surfaces once the org/ownership model is settled.
