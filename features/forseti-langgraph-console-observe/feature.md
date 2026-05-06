# Feature Brief

- Work item id: forseti-langgraph-console-observe
- Website: forseti.life
- Module: drupal_langgraph
- Project: PROJ-001
- Group Order: 4
- Group: console-ui
- Group Title: Console Routes & UI
- Group Sort: 4
- Status: ready
- Release: 20260412-forseti-release-r
- PM owner: pm-forseti
- Dev owner: dev-forseti
- QA owner: qa-forseti
- Priority: P1
- Source: LangGraph UI roadmap (PROJ-001, Phase 5: Observe & Monitoring)

## Summary

The Drupal LangGraph Observe section (`/admin/reports/drupal-langgraph/langgraph-console/observe`) currently serves a lightweight read-only diagnostic summary. This feature expands that Observe surface to live orchestrator telemetry data: Node Traces panel shows execution timeline with step duration and status; Runtime Metrics dashboard displays tick-level performance data with trend analysis; Drift Detection alerts on anomalous node performance; Alerts & Incidents panel correlates executor failures and blocked agents; Feature Progress integrates the live feature-flow dashboard. All data is read-only. Access is admin-only via the consolidated Drupal LangGraph permission model (`administer drupal langgraph`, with legacy tracker permission accepted during compatibility-shim coexistence).

## Goal

- Operators and the CEO can inspect in-progress orchestration execution: see which nodes ran, how long they took, detect performance anomalies, and identify failures and blocked items in one place.
- Deliver observability for troubleshooting slow ticks, identifying node bottlenecks, and correlating agent blocks with orchestrator events.

## Latest updates

- 2026-04-27: Re-baselined from `shipped` to `ready` after reconciling dev/QA execution history. Specs and QA suite activation exist, but implementation is not complete and the feature should be treated as the next dispatchable backlog slice.
- 2026-04-25: QA suite activation completed for the Observe routes and verification scope.
- 2026-04-24: Dev implementation attempt escalated for architectural re-baselining before coding.

## Acceptance criteria

### AC-1: Node Traces — Timeline View
- `/admin/reports/drupal-langgraph/langgraph-console/observe/traces` displays a timeline of node executions from the last run
- Data source: `copilot-hq/inbox/responses/langgraph-ticks.jsonl` (last tick entry's `step_results[]`)
- Each row shows: node/agent ID, execution timestamp (ISO 8601 formatted as HH:MM:SS), step duration (ms), status (success ✓ / error ✗), result summary (truncated to 120 chars)
- Expandable detail view for each step shows: full I/O summaries, any error message
- No PII in output (truncate/sanitize step results)
- If `langgraph-ticks.jsonl` is missing or `step_results` is empty: display "No trace data available."

### AC-2: Node Traces — Search & Filter
- Search bar filters traces by: node name, timestamp range (start/end inputs)
- Applied client-side (traces already fetched) or server-side (lazy-load if >100 steps)
- Clear button resets all filters

### AC-3: Runtime Metrics — Dashboard
- `/admin/reports/drupal-langgraph/langgraph-console/observe/metrics` displays tick-level metrics:
  - Current tick: duration (T-end − T-start), agents dispatched, agents in backlog, concurrency level (selected_agents count)
  - Data source: latest tick from `langgraph-ticks.jsonl` + derived fields
  - If tick data missing: display "Metrics unavailable — no tick data yet."

### AC-4: Runtime Metrics — Trend Analysis
- Historical trend chart showing last 10 ticks: X-axis = tick sequence, Y-axis = tick duration (ms)
- Render as ASCII table or simple chart (no external JS library required; use Drupal core Chart API if available, else table)
- Highlight current tick in blue, previous in grey

### AC-5: Runtime Metrics — Anomaly Detection
- If current tick duration > 2σ above rolling mean (mean of last 10 ticks): flag as orange warning
- Show: "⚠️ Current tick is 45% slower than average. Check node traces for bottlenecks."
- Rolling mean calculation: use existing tick data only (no pre-aggregation table)

### AC-6: Drift Detection — Baseline Comparison
- `/admin/reports/drupal-langgraph/langgraph-console/observe/drift` compares performance of nodes in last 5 ticks against baseline
- Baseline: compute mean(step_duration) for each node from ALL historical ticks in `langgraph-ticks.jsonl`
- Current: step_duration for each node in last 5 ticks
- Alert if any node > ±50% from baseline: show node name, baseline ms, current ms, % variance

### AC-7: Drift Detection — Alert List & Export
- Display alerts as table: node, baseline (ms), current (ms), % variance, tick number
- Filter by: node name, variance threshold (50% / 75% / 100%)
- Downloadable CSV of metrics for last 100 ticks (node, tick#, duration)

### AC-8: Alerts & Incidents — Incident List
- `/admin/reports/drupal-langgraph/langgraph-console/observe/alerts` lists incidents from last 24h:
  - Incident categories: executor-failure (from `tmp/executor-failures/`), agent-blocked (from inbox `Status: blocked`), tick-timeout (from orchestrator logs)
  - Each row: timestamp (ISO 8601), severity (error/warn/info), category, summary, correlated seat IDs

### AC-9: Alerts & Incidents — Correlation
- Executor-failures: read `$COPILOT_HQ_ROOT/tmp/executor-failures/*.json`, extract agent ID, link to agent tracker
- Agent-blocked: glob `sessions/*/inbox/*/command.md` for `Status: blocked` lines newer than 24h, extract seat ID
- Tick-timeout: parse orchestrator logs (if available) for "tick timeout" messages

### AC-10: Alerts & Incidents — Filter & Search
- Filter by: severity, category, seat ID, date range (start/end)
- Search by: summary text, seat ID
- Results: last 50 incidents (configurable in Phase 7 admin settings)

### AC-11: Feature Progress Integration
- `/admin/reports/drupal-langgraph/langgraph-console/observe/feature-progress` embeds or links the live feature-progress dashboard
- Data source: `dashboards/FEATURE_PROGRESS.md` (auto-refreshed by orchestrator tick)
- Show: total features, in_progress count, done count, blocked count, grouped by phase
- Quick link to each feature detail view (existing route)

### AC-12: Auth & Permissions
- All Observe routes under `/admin/reports/drupal-langgraph/langgraph-console/observe*` require Drupal LangGraph admin access
- Access check accepts either `administer drupal langgraph` or legacy `administer copilot agent tracker` during the compatibility-shim period
- Authenticated non-admin → 403 Forbidden
- Anonymous → 403 Forbidden

### AC-13: COPILOT_HQ_ROOT Env Availability
- Before rendering any live data, verify `COPILOT_HQ_ROOT` env var is set via the `drupal_langgraph` path manager
- If unset: display yellow warning banner "⚠️ Live data unavailable: COPILOT_HQ_ROOT is not configured. Contact admin."
- Gracefully handle missing data files (no PHP fatal errors)

### AC-14: Performance & Caching
- Page load time < 2s for all routes
- No N+1 file reads (pre-aggregate if needed)
- Tick JSON parsing: cache parsed ticks in memory for same request (if multiple routes fetch same file)
- No streaming/auto-refresh (static read-on-load is sufficient)

## Out of scope
- Writing/mutating any orchestrator state
- Real-time streaming or WebSocket updates (static read-on-load is sufficient)
- Historical analysis beyond 100 ticks
- Custom alerting rules or alert thresholds (fixed thresholds for now; configurable in Phase 7)

## Technical notes

- **Data sources:**
  - `copilot-hq/inbox/responses/langgraph-ticks.jsonl` — line-delimited JSON, latest entry has `step_results[]`
  - `$COPILOT_HQ_ROOT/tmp/executor-failures/*.json` — one file per executor failure event
  - `sessions/*/inbox/*/command.md` — status line parsing (lightweight glob)
  - `dashboards/FEATURE_PROGRESS.md` — markdown, parse for section headers

- **Controllers:**
  - Extend `Drupal\\drupal_langgraph\\Controller\\LangGraphConsoleController` with new observe-focused methods
  - OR create a dedicated observe controller under the `drupal_langgraph` module if the class gets too large

- **Helper Services:**
  - New `MetricsAggregator` service: parse ticks, compute baseline, detect drift, format metrics
  - New `IncidentCollector` service: scan executor-failures + agent blocks + orchestrator logs
  - Use `HqPathManager` for all path resolution

- **Rendering:**
  - Twig templates in `drupal_langgraph/templates/` or an observe-specific subdirectory inside the module
  - Shared CSS/JS library under `drupal_langgraph`

## Verification

```bash
# Smoke test: load all Observe routes as admin
curl -s -b admin_cookies.txt https://forseti.life/admin/reports/drupal-langgraph/langgraph-console/observe | grep -i "Node Traces\|Runtime Metrics"
curl -s -b admin_cookies.txt https://forseti.life/admin/reports/drupal-langgraph/langgraph-console/observe/traces | grep -i "timeline\|step_results"
curl -s -b admin_cookies.txt https://forseti.life/admin/reports/drupal-langgraph/langgraph-console/observe/metrics | grep -i "tick duration\|concurrency"
curl -s -b admin_cookies.txt https://forseti.life/admin/reports/drupal-langgraph/langgraph-console/observe/drift | grep -i "baseline\|variance"
curl -s -b admin_cookies.txt https://forseti.life/admin/reports/drupal-langgraph/langgraph-console/observe/alerts | grep -i "incident\|executor-failure"
curl -s -b admin_cookies.txt https://forseti.life/admin/reports/drupal-langgraph/langgraph-console/observe/feature-progress | grep -i "feature\|in_progress"

# Verify no hardcoded paths
grep -r "langgraph-ticks\|executor-failures\|FEATURE_PROGRESS" sites/forseti/web/modules/custom/drupal_langgraph/src/

# Verify auth: non-admin should get 403
curl -s -b user_cookies.txt https://forseti.life/admin/reports/drupal-langgraph/langgraph-console/observe/traces | grep -i "403\|forbidden"

# Verify data freshness
curl -s -b admin_cookies.txt https://forseti.life/admin/reports/drupal-langgraph/langgraph-console/observe/metrics | grep "Last updated"
```

## Security acceptance criteria

- **Authentication/permission surface:** All Observe routes require the consolidated Drupal LangGraph admin access check. No unauthenticated access to tick data, executor-failures, or agent inbox.
- **CSRF expectations:** All routes are GET-only (read-only). No state mutations. CSRF tokens not required.
- **Input validation:** No direct user input to server. File path resolution uses `HqPathManager` (env-var resolved, no user-controlled path segments). Tick JSON parsed from trusted local file; truncate/sanitize output before rendering in Twig (strip any tags, limit text length).
- **PII/logging constraints:** Tick data may contain agent seat IDs and step results (could be logs/errors with PII). Do NOT log full tick data to Drupal watchdog. Render step result summaries truncated (max 120 chars) in admin-only context.

## Dependencies

- `drupal_langgraph` — shipped ✓ (consolidated roadmap + console boundary)
- `LangGraphConsoleController` — shipped ✓ (route structure, section scaffolds, live file-backed summaries)
- `HqPathManager` — shipped ✓ (FORSETI_ROOT/COPILOT_HQ_ROOT-aware path resolution)

## Related features

- Predecessor: `forseti-langgraph-console-run-session` (Phase 4 — Run/Session panels)
- Successor: `forseti-langgraph-console-admin` (Phase 7 — Admin settings to configure drift thresholds, etc.)
- Infrastructure: `forseti-langgraph-ui` (shared roadmap, telemetry schema)
