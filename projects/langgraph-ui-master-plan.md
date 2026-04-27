# Drupal LangGraph UI master plan

Problem:
The console now has a usable shell, flow registry, flow context, and operator
dashboard, but it is not yet a complete LangGraph management UI. We need one
master plan that defines what LangGraph management expects, how that maps to
our Drupal UI, what is already supported, and what is still missing before the
UI can be called complete.

Approach:
1. Treat the LangGraph UI as a control plane for three things: graph
   definition, graph execution, and graph observation/governance.
2. Use the ideal IA as the target frame:
   global `Overview`, `Flows`, `Admin`; then per-flow `Overview`, `Build`,
   `Test`, `Run`, `Observe`, `Release`.
3. Track every LangGraph command and data structure against a concrete Drupal
   route, control, and support level: `Supported`, `Partial`, or `Missing`.
4. Use this file as the master completion contract for the UI. The UI is not
   done until every required capability below is either implemented or
   intentionally deferred.

## Ideal nested workspace contract

| Layer | UI section | Route pattern | Purpose |
| --- | --- | --- | --- |
| Global | Overview | `/admin/reports/drupal-langgraph/langgraph-console` | Operator summary and entry into LangGraph management |
| Global | Flows | `/admin/reports/drupal-langgraph/langgraph-console/flows` | Registry of all flows and entry into flow workspaces |
| Global | Admin | `/admin/reports/drupal-langgraph/langgraph-console/admin` | Runtime roots, artifact health, and control contracts |
| Flow workspace | Overview | `/admin/reports/drupal-langgraph/langgraph-console/flows/{flow_id}` | Flow summary and workspace index |
| Flow workspace | Build | `/admin/reports/drupal-langgraph/langgraph-console/flows/{flow_id}/build` | Graph authoring, structure, and policy |
| Flow workspace | Test | `/admin/reports/drupal-langgraph/langgraph-console/flows/{flow_id}/test` | Validation, parity, and checkpoint replay |
| Flow workspace | Run | `/admin/reports/drupal-langgraph/langgraph-console/flows/{flow_id}/run` | Manual execution and execution history |
| Flow workspace | Observe | `/admin/reports/drupal-langgraph/langgraph-console/flows/{flow_id}/observe` | Traces, metrics, drift, alerts, and progress |
| Flow workspace | Release | `/admin/reports/drupal-langgraph/langgraph-console/flows/{flow_id}/release` | Versions, promotion, evidence, and troubleshooting |

## Current route contract

| UI section | Route | Current purpose |
| --- | --- | --- |
| Overview | `/admin/reports/drupal-langgraph/langgraph-console` | Operator dashboard and health summary |
| Flows | `/admin/reports/drupal-langgraph/langgraph-console/flows` | Registry of built-in and custom flows |
| New Process Flow | `/admin/reports/drupal-langgraph/langgraph-console/flows/add` | Create draft flow definition |
| Flow Detail / Workspace Overview | `/admin/reports/drupal-langgraph/langgraph-console/flows/{flow_id}` | Per-flow summary and workspace index |
| Flow Build | `/admin/reports/drupal-langgraph/langgraph-console/flows/{flow_id}/build` | Flow-scoped Build workspace with stubbed controls |
| Flow Test | `/admin/reports/drupal-langgraph/langgraph-console/flows/{flow_id}/test` | Flow-scoped Test workspace with stubbed controls |
| Flow Run | `/admin/reports/drupal-langgraph/langgraph-console/flows/{flow_id}/run` | Flow-scoped Run workspace with stubbed controls |
| Flow Observe | `/admin/reports/drupal-langgraph/langgraph-console/flows/{flow_id}/observe` | Flow-scoped Observe workspace with nested controls |
| Flow Release | `/admin/reports/drupal-langgraph/langgraph-console/flows/{flow_id}/release` | Flow-scoped Release workspace with nested controls |
| Admin | `/admin/reports/drupal-langgraph/langgraph-console/admin` | Runtime roots and artifact contract |
| Compatibility Build/Test/Run/Observe/Release | `/admin/reports/drupal-langgraph/langgraph-console/{section}` | Flow-selection landing or redirect into the selected flow workspace |

## LangGraph command model -> UI mapping

| LangGraph management command | User intent | Expected UI surface | Current Drupal control | Status | Notes |
| --- | --- | --- | --- | --- | --- |
| Create flow | Register a new graph/process flow | Flows | `New Process Flow` form | Supported | Draft flow creation works |
| Open flow control panel | Enter a specific flow workspace | Flows | Flow row link / flow detail page | Supported | Also sets selected flow context |
| Select active flow context | Scope lifecycle work to one flow | Flow Detail + nested flow tabs | Auto-selected via flow detail + user.data | Supported | Flow-scoped workspace is now the preferred path |
| Edit flow metadata | Change label, owner, status, entrypoint, version | Build | Nested `Flow Metadata` editor | Supported | Flow edits now live inside the flow workspace |
| Archive flow | Retire a flow without deleting it | Build | Nested `Flow Metadata` editor via `status=archived` | Supported | Archive is currently handled through status changes |
| Define state schema | Describe state model carried across nodes | Build | Nested `State Schema` editor | Supported | Editable inside the flow workspace |
| Add node | Define graph node set | Build | Nested `Nodes` editor | Supported | Line-based editor for now |
| Edit node | Change node definitions | Build | Nested `Nodes` editor | Supported | Rich per-node authoring still future work |
| Connect routing / edges | Define graph transitions and conditionals | Build | Nested `Routing` editor | Supported | Rule-based editor for now |
| Bind tools | Define tools/resources available to the graph | Build | Nested `Tools` editor | Supported | Checkbox-based binding editor |
| Configure prompts / policy | Manage orchestration notes, prompt guardrails, policy | Build | Nested `Prompts & Policy` editor | Supported | Text-based editor for now |
| Validate structure | Check graph completeness and shape | Test | Nested `Validate Structure` report | Supported | Validates saved flow contract against structural and runtime expectations |
| Validate parity | Compare runtime against expected structure | Test | Current parity evidence page | Supported | Runtime parity evidence is already surfaced |
| Replay checkpoint | Re-run from checkpoint / prior state | Test / Run | Nested `Replay Checkpoints` inventory | Partial | Artifact inventory exists; replay/resume action is not wired yet |
| Run now | Trigger manual execution | Run | No action yet | Missing | Run page is read-only today |
| Pause run | Halt flow execution | Run | No action yet | Missing | Requires control artifact/action path |
| Resume run | Resume paused execution | Run | No action yet | Missing | Same control path as pause |
| Inspect recent runs | Review recent execution activity | Run | Recent tick timeline | Supported | Present as execution-plane evidence |
| Inspect traces | Inspect node/step traces | Observe | Node Traces subsection | Supported | Artifact-backed |
| Inspect metrics | Review cadence/worker/anomaly metrics | Observe | Runtime Metrics subsection | Supported | Artifact-backed |
| Inspect drift | Review deviations from baseline | Observe | Drift subsection | Supported | Artifact-backed |
| Inspect alerts/incidents | Review failures and blockers | Observe | Alerts & Incidents subsection | Supported | Artifact-backed |
| Inspect flow-scoped feature progress | View LangGraph work status by flow | Observe | Feature Progress subsection | Supported | Explicitly flow-scoped |
| Create version | Save/reify releaseable graph version | Release | No action yet | Missing | Needs version object + UI |
| Promote version | Promote a version toward release | Release | No action yet | Missing | Release page is evidence-oriented today |
| Review release evidence | Inspect release readiness and signoffs | Release | Release Evidence subsection | Supported | Artifact-backed |
| Troubleshoot release blockers | Work blocker queue and inbox pressure | Release | Release Troubleshooting subsection | Supported | Artifact-backed |
| Inspect runtime roots | Validate filesystem/runtime contract | Admin | Runtime Roots subsection | Supported | Present |
| Inspect artifact health | Validate required artifact files | Admin | Artifact Health table | Supported | Present |

## LangGraph data structure model -> UI mapping

| Data structure | Purpose | Current storage/source | Current UI surface | Status | Notes |
| --- | --- | --- | --- | --- | --- |
| Flow registry entry | Top-level graph/process record | Drupal config `drupal_langgraph.process_flows` + built-ins | Flows registry, flow detail | Supported | Core registry is working |
| Flow ID | Stable machine identifier | Config + route param | Flows, flow detail | Supported | Used for selection/context |
| Flow label + description | Human-readable identity | Config | Flows, flow detail | Supported | Create-only for now |
| Flow status | Draft/active/paused/archived lifecycle state | Config | Flows, flow detail, Build metadata editor | Supported | Editable from nested flow workspace |
| Owner | Responsible module/team/system | Config | Flows, flow detail, Build metadata editor | Supported | Editable from nested flow workspace |
| Graph type | State/subgraph/supervisor/router type | Config | Flows, flow detail, Build metadata editor | Supported | Editable from nested flow workspace |
| Default entrypoint | Main node/entry command | Config | Flows, flow detail, Build metadata editor | Supported | Editable from nested flow workspace |
| Primary section | Best-fit lifecycle section | Config | Flows, flow detail, current flow context | Supported | Used as descriptive metadata |
| Version | Flow version marker | Config | Flows, flow detail, Build metadata editor | Supported | Editable marker, not yet a full version history system |
| State schema summary | Description of carried state | Config | Flow detail, Build state-schema editor | Supported | Now editable in the workspace |
| Nodes | Graph node set | Config array | Flow detail, Build nodes editor | Supported | Now editable in the workspace |
| Routing rules | Graph edges/conditions | Config array | Flow detail, Build routing editor | Supported | Now editable in the workspace |
| Tools | Graph tool bindings/resources | Config array | Flow detail, Build tools editor | Supported | Now editable in the workspace |
| Prompt notes | Prompt/policy/orchestration notes | Config | Flow detail, Build prompts editor | Supported | Now editable in the workspace |
| Selected flow context | Current user-scoped flow | Drupal `user.data` | Current flow panel across sections | Supported | Working |
| Tick stream | Execution timeline + step results | JSONL artifacts | Overview, Run, Observe | Supported | Artifact-backed |
| Parity report | Runtime validation evidence | JSON artifact | Test | Supported | Working |
| Metrics | Runtime aggregate signals | Observe artifacts/services | Observe Metrics | Supported | Working |
| Drift signals | Behavioral drift evidence | Observe artifacts/services | Observe Drift | Supported | Working |
| Incidents/alerts | Error/blocker/anomaly summaries | Observe artifacts/services | Observe Alerts | Supported | Working |
| Feature progress snapshot | LangGraph-owned work progress | Feature progress artifact | Observe Feature Progress | Supported | Working |
| Org/release controls | Runtime enable/disable controls | Control artifacts | Overview, Admin | Supported | Read-only at present |
| Checkpoints | Resume/replay state markers | Runtime logs and checkpoint scripts | Test checkpoint inventory | Partial | Artifacts are visible; replay UX is not wired yet |
| Execution commands | Run/pause/resume actions | Not yet modeled in Drupal | None | Missing | Required for true runtime control |
| Version history | Version list, provenance, promotion state | Not yet modeled in Drupal | None | Missing | Required for full release management |

## Section-by-section definition of done

### Overview
- Done when it surfaces operator summary, active issues, next action, and
  current flow without forcing users into raw artifacts first.
- Current state: mostly complete for the operator dashboard.

### Flows
- Done when users can create, open, edit, archive, and version flows from the
  registry and detail surfaces.
- Current state: create/open and edit/archive via the nested Build workspace are done; version history is not.

### Build
- Done when users can author and update the graph contract:
  state schema, nodes, routing, tools, and prompt policy.
- Current state: nested Build workspace exists with live editors; richer graph authoring and validation are still incomplete.

### Test
- Done when users can validate graph shape, parity, and replay/checkpoint paths.
- Current state: nested Test workspace exists with structure validation and parity evidence; replay is still partial.

### Run
- Done when users can manually trigger, pause, resume, and inspect executions.
- Current state: nested Run workspace exists with execution history; execution controls do not.

### Observe
- Done when traces, metrics, drift, alerts, and flow-scoped progress are easy
  to inspect from one coherent flow-aware workspace.
- Current state: nested Observe workspace exists and is the strongest read-only ops surface.

### Release
- Done when users can inspect evidence, create versions, and promote versions
  with explicit readiness signals and blockers.
- Current state: nested Release workspace exists with evidence and troubleshooting; versioning/promote controls do not.

### Admin
- Done when runtime roots, artifact health, and control files are visible and
  any writable controls needed by the UI can be managed safely.
- Current state: read-only inspection exists.

## Completion checklist

- [x] Add top-level console IA and flow registry
- [x] Add flow creation and flow selection context
- [x] Add flow detail with structural summary
- [x] Add operator-focused overview dashboard
- [x] Collapse legacy routes to canonical console routes
- [x] Add nested flow workspace tabs and stub control pages
- [x] Add flow edit/archive actions
- [x] Add Build editors for schema, nodes, routing, tools, and prompt policy
- [ ] Add Test actions for structural validation and checkpoint replay
- [ ] Add Run actions for manual execution and pause/resume
- [ ] Add Release actions for version creation and promotion
- [ ] Add explicit writable control surfaces where runtime actions require them
- [ ] Audit every command/data structure in this document against the live UI

Todos:
- Done: establish this file as the UI master contract and tracking source.
- Pending: implement missing command controls in Flows, Build, Test, Run, and
  Release.
- Pending: add real editors for LangGraph data structures, not just create-time
  capture and read-only display.
- Pending: audit the live UI against this matrix and close remaining gaps.

Notes:
- The preferred architecture is now explicitly reflected in the UI:
  `Overview`, `Flows`, and `Admin` are global, while `Build`, `Test`, `Run`,
  `Observe`, and `Release` live under the selected flow workspace.
- Compatibility routes for global lifecycle sections still exist so older entry
  points can redirect into the selected flow or prompt the user to choose one.
- The command map currently shown in the UI is aspirational in places. This plan
  now distinguishes aspirational mappings from truly implemented controls so we
  can close the gap deliberately.
