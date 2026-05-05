- command: |
    Re-evaluate Gate 2 for active release `20260412-forseti-release-r`.

    Use the bundled inbox evidence as your primary working set.
    Do not assume additional repo-read or shell capabilities are available.

    Required actions:
    1. Review the bundled feature brief(s), acceptance criteria, and dev evidence.
    2. Review the bundled prior QA/Gate 2 evidence, if present.
    3. Write exactly one release-scoped APPROVE or BLOCK verdict for `20260412-forseti-release-r`.
    4. Cite the bundled evidence directly in your verdict.

## Bundled release evidence

### Scoped features
- `forseti-langgraph-console-admin`

### Feature brief — forseti-langgraph-console-admin
- Source: `features/forseti-langgraph-console-admin/feature.md`

````text
# Feature Brief

- Work item id: forseti-langgraph-console-admin
- Website: forseti.life
- Module: copilot_agent_tracker
- Project: PROJ-001
- Group Order: 5
- Group: console-ui
- Group Title: Console Routes & UI
- Group Sort: 5
- Status: in_progress
- Release: 20260412-forseti-release-r
- Feature type: enhancement
- PM owner: pm-forseti-agent-tracker
- Dev owner: dev-forseti-agent-tracker
- QA owner: qa-forseti-agent-tracker
- Priority: P2
- Source: LangGraph UI roadmap (PROJ-001, Phase 7: Admin & Configuration)

## Summary

The LangGraph Console Admin section (`/langgraph-console/admin`) provides operators with configuration, permission, and audit controls for the console and orchestration system. This feature adds: Admin Settings form (configurable thresholds, retention policies); Permissions & Team Assignment UI (role matrix, seat scoping); Audit Log Viewer (mutation history with filtering); Health & Status Dashboard (orchestrator status, agent pool health, data freshness); Console Navigation controls (show/hide sections, set landing page). All configuration changes are logged and require `administer console settings` permission. Access is admin-only.

## Goal

- Operators can tune console behavior (thresholds, retention, display options) without code changes
- Full audit trail of all console mutations for compliance and troubleshooting
- Real-time visibility into system health and agent execution state
- Permission matrix for controlling which roles can access which console sections

## Deferred decision

- 2026-05-04: Deferred out of `20260412-forseti-release-q` and returned to `ready` for `20260412-forseti-release-r`.
- Reason: the current dev outbox for release-q remains `needs-info`, while `forseti-langgraph-console-observe` has a completed dev outbox. Deferring admin keeps the active release internally consistent and aligns with the original dev recommendation to ship Observe first and move Admin to the next cycle.

## Acceptance criteria

### AC-1: Admin Settings Form
- `/langgraph-console/admin/settings` displays form for tunable parameters:
  - **Max tick history:** how many ticks to retain in display (range: 10–1000, default: 100)
  - **Metrics trend window:** ticks to include in trend calculation (range: 5–50, default: 10)
  - **Drift threshold %:** variance threshold to trigger alert (range: 1–100, default: 50)
  - **Alert retention days:** how long to keep incident records (range: 1–30, default: 7)
  - **Canary default duration (hours):** suggested canary duration for Phase 6 (range: 0.5–24, default: 1)

### AC-2: Admin Settings Persistence
- Settings saved to: `$COPILOT_HQ_ROOT/admin/settings.json` (JSON format) AND Drupal Config (via `config_factory`)
- On form submit: validate input ranges, save to both backends, log to audit table
- Settings loaded on page load from Drupal config (fallback to JSON if config not available)
- If both backends differ: config wins; re-sync JSON from config on next load

### AC-3: Admin Settings Validation
- All numeric fields: validate min/max ranges (return form error if out of range)
- Drift threshold: cannot be 0 or negative
- Retention: cannot be less than 1 day
- Submit button disabled until form is valid

### AC-4: Permissions Matrix
- `/langgraph-console/admin/permissions` displays read-only matrix:
  - Rows: console sections (Home, Build, Test, Run, Observe, Release, Admin)
  - Columns: Drupal roles (administrator, authenticated user, anonymous)
  - Cell value: permission required for that role to access that section
  - Example: "Run section requires: administrator" (grey box for non-admin roles)

### AC-5: Team Assignment
- Section: "Team Assignment" on permissions page
- Allow admin to scope agent tracking to specific seat IDs (future: multi-agent filtering)
- List of seat IDs with: seat name, module, current status (idle/working)
- Checkbox to "Assign this seat to my team view" (multi-select)
- Selected seats are stored in Drupal user preferences (`user.settings` field)

### AC-6: Audit Log Viewer
- `/langgraph-console/admin/audit-log` displays all console mutations:
  - Columns: timestamp (ISO 8601), operator (Drupal user ID + name), action (version_created / promote_staging / promote_prod / settings_changed / permission_updated), resource ID, before value, after value, CSRF verified
  - Rows: last 100 audit entries (configurable via settings)

### AC-7: Audit Log Filtering
- Filters: operator (dropdown), action (dropdown), date range (start/end date inputs), resource ID (text search)
- Apply filters client-side (if <1000 entries) or server-side query (if DB-backed)
- Clear button resets all filters
- Export button: download filtered results as CSV (timestamp, operator, action, resource, before, after)

### AC-8: Audit Log Retention
- Audit table (`copilot_agent_tracker_audit`) keeps entries for last 30 days
- Cron job (or orchestrator phase): purge entries older than 30 days daily
- If purged entries exist in UI: show "Note: entries older than 30 days are archived" hint

### AC-9: Health & Status Dashboard
- `/langgraph-console/admin/health` displays system health:
  - **Orchestrator status:** green ✓ (last tick < 5 min ago) / yellow ⚠️ (5–15 min ago) / red ✗ (> 15 min ago or unknown)
  - **Last tick:** timestamp (ISO 8601, human-readable local time), tick sequence number
  - **Tick frequency:** 2 minutes (expected), measured from last 10 ticks' time deltas
  - **Parity status:** `parity_ok` from latest tick (green if true, red if false)
  - **LangGraph provider:** provider name (e.g., `ShellProvider`)

### AC-10: Per-Agent Status
- Subsection: "Agent Pool Status"
- Table with columns: seat ID, module, current status (idle / working / error), last action, inbox size, last modified time
- Data source: `sessions/*/inbox/*/command.md` (parse `Status:` line), `sessions/*/outbox/` (last file mtime)
- Click agent row → drill-down to recent inbox/outbox items

### AC-11: Data Freshness Indicators
- Subsection: "Data Freshness"
- Show: `langgraph-ticks.jsonl` mtime (green if < 5 min ago), `FEATURE_PROGRESS.md` mtime (green if < 1 hour ago), `executor-failures/` count (green if 0)
- If any stale: display yellow warning "Data may be out of sync. Check orchestrator status."

### AC-12: Health Auto-Refresh
- Health dashboard auto-refreshes every 30 seconds (AJAX fetch to `/langgraph-console/admin/health.json`)
- Show "Last refreshed" timestamp with manual refresh button
- No spinner or loading state (silent update)

### AC-13: Console Navigation Controls
- `/langgraph-console/admin/navigation` allows customizing console UI:
  - **Landing page:** dropdown to set which section loads on `/langgraph-console` (default: home)
  - **Visible sections:** checkboxes for each section (Home, Build, Test, Run, Observe, Release, Admin) — uncheck to hide from nav
  - **Theme:** radio buttons for light / dark mode (applied via `data-theme` attribute on body)
  - Settings saved to Drupal Config per user

### AC-14: Auth & Permissions (All Admin Routes)
- All Admin routes (`/langgraph-console/admin*`) require `administer console settings` permission (new permission, added via hook_permission)
- Additionally, mutations (form submits) require valid CSRF token
- Authenticated non-admin → 403 Forbidden
- Anonymous → 303 redirect to login

### AC-15: COPILOT_HQ_ROOT Env Availability
- All Admin routes verify `COPILOT_HQ_ROOT` is set before rendering
- If unset: display yellow warning banner, do not crash
- Health dashboard: gracefully handle missing health files

### AC-16: Audit Logging of Admin Actions
- Every form submit on admin pages (settings, permissions, navigation) logged to audit table:
  - Timestamp, operator ID, action (e.g., `settings_changed`), resource ID (e.g., `drift_threshold`), before value, after value, CSRF verified flag
- Log even if validation fails (log the failed attempt)
- Do not log sensitive data (e.g., API keys, tokens)

## Out of scope
- Role-based access control (RBAC) beyond current admin/non-admin
- Multi-team or org-level permission scoping (future phase)
- Integration with external auth systems (Okta, etc.) — future
- Alert notification setup (Slack, email) — Phase 8+

## Technical notes

- **DB Schema:**
  ```sql
  CREATE TABLE copilot_agent_tracker_audit (
    id INT PRIMARY KEY AUTO_INCREMENT,
    timestamp DATETIME NOT NULL,
    operator_id INT NOT NULL,  -- Drupal user ID
    action VARCHAR(255) NOT NULL,  -- 'version_created', 'promote_staging', 'settings_changed', etc.
    resource_id VARCHAR(255),
    before_value LONGTEXT,
    after_value LONGTEXT,
    csrf_verified BOOLEAN DEFAULT 1,
    INDEX (timestamp),
    INDEX (operator_id),
    FOREIGN KEY (operator_id) REFERENCES users(uid)
  );
  ```

- **New Permissions (in .module):**
  - `administer console settings`
  - `administer release cycle`

- **Controllers:**
  - New `LangGraphConsoleAdminController` with methods: `settings()`, `permissions()`, `auditLog()`, `health()`, `navigation()`
  - New form: `AdminSettingsForm` extending `ConfigFormBase`

- **Helper Services:**
  - New `AuditLogger` service: log mutations to audit table
  - New `HealthAggregator` service: collect orchestrator status, agent status, data freshness

- **Rendering:**
  - Twig templates in `templates/langgraph-console/admin/` (settings.html.twig, permissions.html.twig, audit-log.html.twig, health.html.twig, navigation.html.twig)

- **AJAX Endpoints:**
  - New JSON route: `/langgraph-console/admin/health.json` (returns `{status, timestamp, agents[], fresh}`)

## Verification

```bash
# Smoke test: load all Admin routes as admin
curl -s -b admin_cookies.txt https://forseti.life/langgraph-console/admin | grep -i "Settings\|Permissions"
curl -s -b admin_cookies.txt https://forseti.life/langgraph-console/admin/settings | grep -i "Drift threshold\|Retention"
curl -s -b admin_cookies.txt https://forseti.life/langgraph-console/admin/permissions | grep -i "administrator\|authenticated"
curl -s -b admin_cookies.txt https://forseti.life/langgraph-console/admin/audit-log | grep -i "Action\|Operator"
curl -s -b admin_cookies.txt https://forseti.life/langgraph-console/admin/health | grep -i "Orchestrator\|Status"

# Verify audit table created
mysql forseti -e "DESCRIBE copilot_agent_tracker_audit;"

# Verify settings persisted
curl -s -b admin_cookies.txt https://forseti.life/langgraph-console/admin/settings | grep -o "value=\"[0-9]*\"" | head -5

# Verify auth: non-admin should get 403
curl -s -b user_cookies.txt https://forseti.life/langgraph-console/admin/settings | grep -i "403\|forbidden"
```

## Security acceptance criteria

- **Authentication/permission surface:** All Admin routes require `administer console settings` permission (enforced via `_permission` in routing.yml). Form submits additionally require valid CSRF token. No unauthenticated access.
- **CSRF expectations:** All form-based routes (settings, permissions, navigation) require CSRF token validation on POST. Health dashboard (GET-only) does not require CSRF.
- **Input validation:** Settings form validates numeric ranges before save. Team assignment list validated against existing seat IDs. Audit log filters (e.g., user ID, date) validated before DB query (prevent SQL injection).
- **PII/logging constraints:** Audit log may contain config values (e.g., threshold settings) but NOT sensitive values (passwords, API keys). Do not log raw settings JSON if it contains secrets. Truncate operator ID to integer (no PII). Audit table retention: 30 days (compliant with typical audit policies).

## Dependencies

- `forseti-copilot-agent-tracker` — shipped ✓ (DB tables, telemetry API, DashboardController helpers)
- `LangGraphConsoleStubController` — shipped ✓ (route structure, 7 console sections)

## Related features

- Predecessor: `forseti-langgraph-console-observe` (Phase 5 — Observe section, uses settings from Admin)
- Infrastructure: `forseti-langgraph-ui` (shared roadmap)
- Future: Phase 6 `forseti-langgraph-console-release` (will add `administer_release_cycle` permission here)
````

### Acceptance criteria — forseti-langgraph-console-admin
- Source: `features/forseti-langgraph-console-admin/01-acceptance-criteria.md`

````text
# Acceptance Criteria — Phase 7: Admin & Configuration

## Route & Auth

### AC-Route-1: All admin routes exist and return 200 for admin
```
GET /langgraph-console/admin → LangGraphConsoleAdminController::index() → 200 OK
GET /langgraph-console/admin/settings → LangGraphConsoleAdminController::settings() → 200 OK
POST /langgraph-console/admin/settings → AdminSettingsForm → 303 (redirect to GET)
GET /langgraph-console/admin/permissions → LangGraphConsoleAdminController::permissions() → 200 OK
GET /langgraph-console/admin/audit-log → LangGraphConsoleAdminController::auditLog() → 200 OK
GET /langgraph-console/admin/health → LangGraphConsoleAdminController::health() → 200 OK
GET /langgraph-console/admin/health.json → REST endpoint → 200 OK (JSON)
GET /langgraph-console/admin/navigation → LangGraphConsoleAdminController::navigation() → 200 OK
POST /langgraph-console/admin/navigation → save navigation prefs → 303
```

### AC-Route-2: Routes require administer console settings permission
```
GET /langgraph-console/admin/settings (no auth) → 303 redirect to /user/login
GET /langgraph-console/admin/settings (authenticated, non-admin) → 403 Forbidden
GET /langgraph-console/admin/settings (authenticated, admin with perm) → 200 OK
```

### AC-Route-3: Form submits require CSRF token
```
POST /langgraph-console/admin/settings (no token) → 403 Access Denied (CSRF validation)
POST /langgraph-console/admin/settings (valid token) → 303 Redirect + flash message "Settings saved"
```

## Admin Settings Form

### AC-Settings-1: Settings form displays all configurable fields
```
Form Fields:
  - Max tick history (numeric, 10–1000, default: 100)
  - Metrics trend window (numeric, 5–50, default: 10)
  - Drift threshold % (numeric, 1–100, default: 50)
  - Alert retention days (numeric, 1–30, default: 7)
  - Canary default duration hours (numeric, 0.5–24, default: 1)
```

### AC-Settings-2: Form validation
- For each numeric field:
  - If value < min or > max: show form error "Must be between X and Y"
  - If not numeric: show error "Must be a number"
  - If blank: show error "This field is required"
- Submit button: disabled until all fields valid (client-side + server-side validation)

### AC-Settings-3: Settings saved to Drupal Config
- On form submit: save to `config_factory` at key `copilot_agent_tracker.observe_settings`
- Stored as: `{"max_tick_history": 100, "metrics_trend_window": 10, "drift_threshold_pct": 50, ...}`

### AC-Settings-4: Settings saved to filesystem JSON (fallback)
- Also save to: `$COPILOT_HQ_ROOT/admin/settings.json`
- Format: JSON with same structure as Drupal config
- If Drupal config unavailable, read from this file

### AC-Settings-5: Form pre-fill from config
- On page load: read current config values
- Pre-fill form fields with saved values (or defaults if first time)

### AC-Settings-6: Form submit success feedback
- After POST submit: redirect to GET /langgraph-console/admin/settings
- Display Drupal system message: "✓ Settings saved successfully."
- Form fields pre-filled with just-saved values

### AC-Settings-7: Audit log entry for settings changes
- On each form submit (success or validation failure): create audit table entry
  - `action`: "settings_changed"
  - `resource_id`: field name (e.g., "drift_threshold_pct")
  - `before_value`: old value (JSON)
  - `after_value`: new value (JSON)
  - `operator_id`: current user ID
  - `timestamp`: now
  - `csrf_verified`: 1 if token was valid

## Permissions & Team Assignment

### AC-Perms-1: Permissions matrix displayed
```
| Section | Anonymous | Authenticated | Admin |
|---|---|---|---|
| Home | public | view_langgraph_console | administrator |
| Build | — | administrator | administrator |
| Test | — | administrator | administrator |
| Run | — | administrator | administrator |
| Observe | — | administrator | administrator |
| Release | — | administer_release_cycle | administer_release_cycle |
| Admin | — | — | administer_console_settings |
```

### AC-Perms-2: Matrix is read-only
- No form inputs on this page
- Display only (reference information for operators)
- If future RBAC added: convert to form with checkboxes

### AC-Perms-3: Team Assignment list
- Display: list of available seat IDs (from `sessions/*/inbox/` directories)
- For each seat: checkbox "Assign to my view"
- Save button: POST to admin/permissions/team-assign
- On save: store checked seat IDs in Drupal user data field (`user.settings['langgraph_seats']`)

### AC-Perms-4: Team assignment persistence
- On next login: pre-check boxes for seats saved in user.settings
- If no seats assigned: show message "No seats assigned. You will see all agents by default."

## Audit Log Viewer

### AC-Audit-1: Audit table schema created
```sql
CREATE TABLE copilot_agent_tracker_audit (
  id INT PRIMARY KEY AUTO_INCREMENT,
  timestamp DATETIME NOT NULL,
  operator_id INT NOT NULL,
  action VARCHAR(255) NOT NULL,  -- 'version_created', 'promote_staging', 'settings_changed', etc.
  resource_id VARCHAR(255),
  before_value LONGTEXT,
  after_value LONGTEXT,
  csrf_verified BOOLEAN DEFAULT 1,
  INDEX idx_timestamp (timestamp),
  INDEX idx_operator (operator_id),
  FOREIGN KEY (operator_id) REFERENCES users(uid)
);
```

### AC-Audit-2: Audit log displayed as table
```
| Timestamp | Operator | Action | Resource | Before | After | CSRF |
|---|---|---|---|---|---|---|
| 2026-04-20 14:30:15 | admin (uid: 1) | settings_changed | drift_threshold_pct | "50" | "75" | ✓ |
| 2026-04-20 14:15:00 | architect (uid: 2) | version_created | engine.py | — | "20260420-fd79af60-v1" | ✓ |
```

### AC-Audit-3: Audit log filters
- Dropdown: "Operator" (list of users who have made changes)
- Dropdown: "Action" (settings_changed, version_created, promote_staging, etc.)
- Date range: "From" and "To" (date-time inputs)
- Text search: "Resource ID" (partial match)
- Clear button resets all

### AC-Audit-4: Applied filters
- Filters applied on form change (or when user clicks "Filter" button)
- URL updated with query params: `?operator=1&action=settings_changed&from=2026-04-15&to=2026-04-20&resource=drift`
- Page load with params pre-fills filters + displays filtered results

### AC-Audit-5: Pagination
- Display: last 100 entries (configurable via settings form in Phase 7)
- If > 100: pagination controls (← Previous, Next →)
- Default sort: timestamp DESC (most recent first)

### AC-Audit-6: Audit log export
- Button: "Export as CSV"
- Download: `langgraph-audit-export-{timestamp}.csv`
- Columns: timestamp, operator_id, operator_name, action, resource_id, before_value, after_value, csrf_verified

### AC-Audit-7: Audit log retention
- Cron job (or orchestrator phase) purges entries older than 30 days daily
- If purge occurs: show info message on audit-log page "Note: audit entries older than 30 days are automatically archived."

### AC-Audit-8: Empty audit log
- If no entries: display "No audit log entries yet."

## Health & Status Dashboard

### AC-Health-1: Orchestrator status indicator
- Load latest tick timestamp from `langgraph-ticks.jsonl`
- Compare to now():
  - If < 5 min ago: green ✓ "Orchestrator OK (last tick: 2 min ago)"
  - If 5–15 min ago: yellow ⚠️ "Orchestrator slow (last tick: 12 min ago)"
  - If > 15 min ago: red ✗ "Orchestrator down (last tick: 45 min ago)"

### AC-Health-2: Tick frequency check
- Read last 10 ticks from `langgraph-ticks.jsonl`
- Calculate: avg time delta between ticks
- Display: "Expected: 2 min, Actual: 2.1 min, Variance: +5%"
- If > 5% variance: yellow warning

### AC-Health-3: Parity status
- Read `parity_ok` from latest tick
- Display: "Parity: ✓ OK" (green) or "Parity: ✗ MISMATCH" (red)
- If missing: "Parity: ? UNKNOWN"

### AC-Health-4: LangGraph provider
- Read `provider` from latest tick (e.g., "ShellProvider")
- Display: "Provider: ShellProvider"

### AC-Health-5: Per-agent status table
```
| Seat ID | Module | Status | Last Action | Inbox Size | Last Modified |
|---|---|---|---|---|---|
| dev-forseti | forseti | working | impl-feature-X | 3 items | 2 min ago |
| qa-forseti | forseti | idle | — | 0 items | 5 min ago |
| pm-forseti | forseti | error | — | 1 item | 30 min ago |
```

### AC-Health-6: Per-agent status derivation
- Read `sessions/*/inbox/*/command.md` for current status and last-modified time
- Read `sessions/*/outbox/` for last completed action
- Calculate: inbox_size = count files in inbox
- Status color: green (idle), blue (working), yellow (error), grey (unknown)

### AC-Health-7: Data freshness section
```
Data Freshness:
  ✓ langgraph-ticks.jsonl (2 min ago)
  ✓ FEATURE_PROGRESS.md (32 min ago)
  ✓ executor-failures/ (0 items)
```

### AC-Health-8: Stale data warning
- If `langgraph-ticks.jsonl` mtime > 5 min: yellow ⚠️
- If `FEATURE_PROGRESS.md` mtime > 60 min: yellow ⚠️
- If executor-failures/ has items: orange warning "Executor errors detected"

### AC-Health-9: Auto-refresh via AJAX
- Every 30 seconds: fetch `/langgraph-console/admin/health.json`
- Update displayed values silently (no spinner, no page reload)
- Show: "Last refreshed: 14:32:45" with manual "Refresh now" button

### AC-Health-10: AJAX endpoint schema
```json
GET /langgraph-console/admin/health.json
Response:
{
  "orchestrator_status": "ok",  // ok, slow, down
  "last_tick_timestamp": "2026-04-20T14:30:15Z",
  "tick_frequency_variance": 5,  // %
  "parity_ok": true,
  "provider": "ShellProvider",
  "agents": [
    {"seat_id": "dev-forseti", "status": "working", "inbox_size": 3, "last_modified": "2026-04-20T14:30:00Z"},
    ...
  ],
  "data_freshness": {
    "ticks_mtime": "2026-04-20T14:30:15Z",
    "feature_progress_mtime": "2026-04-20T13:30:00Z",
    "executor_failures_count": 0
  }
}
```

## Console Navigation Controls

### AC-Nav-1: Navigation form
- Dropdown: "Landing page" (options: home, build, test, run, observe, release, admin)
- Default: "home"
- Checkboxes: "Visible sections" (check to show, uncheck to hide)
- Radio buttons: "Theme" (light / dark)
- Save button

### AC-Nav-2: Settings persist per user
- On save: POST to `/langgraph-console/admin/navigation`
- Store in Drupal user data: `user.settings['langgraph_nav']` = JSON
- On next load: apply stored preferences

### AC-Nav-3: Theme applied to body
- If dark mode selected: add `data-theme="dark"` to `<body>` tag
- Provide CSS variable: `--theme-bg: #1a1a1a` (dark) / `#ffffff` (light)

### AC-Nav-4: Landing page redirect
- When user navigates to `/langgraph-console` (no subsection):
- If landing_page set to "observe": redirect to `/langgraph-console/observe`
- Otherwise: show "home" section

### AC-Nav-5: Visible sections applied to nav menu
- Build navigation menu based on selected visible sections
- Hide nav items for unchecked sections
- If all hidden: show message "No sections visible. Edit navigation settings to show sections."

## Error Handling

### AC-Error-1: Missing COPILOT_HQ_ROOT
- Display yellow banner: "⚠️ Live data unavailable: COPILOT_HQ_ROOT not configured."
- Do not crash; show static admin forms only

### AC-Error-2: Missing tick data
- If `langgraph-ticks.jsonl` missing: health dashboard shows "Orchestrator status: UNKNOWN"
- Do not show red error, show yellow warning

### AC-Error-3: Invalid JSON in tick files
- If parsing fails: log to watchdog, display safe fallback
- Show: "Could not parse tick data. Last known good data: {timestamp}"

### AC-Error-4: DB errors
- If audit table missing: create it (via hook_schema)
- If audit write fails: log to watchdog, display message "Failed to log this action. Contact admin."

## Performance

### AC-Perf-1: Settings form load < 1s
- Simple form, minimal data loaded

### AC-Perf-2: Audit log load < 2s
- Query with index on timestamp + operator_id
- Limit to last 100 entries (configurable)

### AC-Perf-3: Health dashboard load < 2s
- Tick file read + agent status glob should be fast
- Cache per-request if needed

### AC-Perf-4: Health AJAX endpoint < 500ms
- Lightweight JSON response
- No heavy computation

## Security

### AC-Sec-1: No hardcoded paths
- All paths via `DashboardController::langgraphPath()`

### AC-Sec-2: Settings values sanitized
- No shell metacharacters in numeric fields (int validation)
- Before/after values in audit log: truncate if > 1KB

### AC-Sec-3: Operator ID in audit log
- Store uid only (no PII); name resolved from users table at display time
- Do not log actual user email in audit table

### AC-Sec-4: CSRF validation on all forms
- POST to settings, permissions, navigation: require valid CSRF token
- Use Drupal form API (automatic)

### AC-Sec-5: Permission checks
- All routes: `_permission: 'administer console settings'` in routing.yml
- Form submits: additional check via form submit handler

## Testing Checklist

- [ ] All 8 routes return 200 for admin user
- [ ] Non-admin users get 403 on all routes
- [ ] Settings form displays 5 fields
- [ ] Settings validation: out-of-range values rejected
- [ ] Settings save persists to Drupal config + JSON file
- [ ] Settings form pre-fill works on page reload
- [ ] Audit log entry created on form submit
- [ ] Permissions matrix displays correctly
- [ ] Team assignment checkboxes work
- [ ] Team assignment persists to user.settings
- [ ] Audit log table populated with ≥ 2 sample entries
- [ ] Audit log filters work (operator, action, date, resource)
- [ ] Audit log export CSV downloads
- [ ] Audit log pagination works (if > 100 entries)
- [ ] Health dashboard shows orchestrator status
- [ ] Health dashboard shows agent table with ≥ 2 agents
- [ ] Health dashboard shows data freshness
- [ ] Health AJAX endpoint returns valid JSON
- [ ] Health auto-refresh every 30s works
- [ ] Navigation landing page dropdown works
- [ ] Navigation visible sections checkboxes work
- [ ] Navigation theme radio buttons work
- [ ] Navigation settings persist per user
- [ ] Theme applied to body (dark mode CSS)
- [ ] Landing page redirect works
- [ ] Nav menu respects hidden sections
- [ ] Missing COPILOT_HQ_ROOT shows warning
- [ ] Invalid JSON shows error, no crash
- [ ] All form submits have CSRF tokens
- [ ] No hardcoded paths found in grep
- [ ] Performance: all pages load < 2s
- [ ] Performance: AJAX endpoint < 500ms

## Audit Log Sample Entries (for manual testing)

```sql
INSERT INTO copilot_agent_tracker_audit (timestamp, operator_id, action, resource_id, before_value, after_value, csrf_verified)
VALUES
  (NOW(), 1, 'settings_changed', 'drift_threshold_pct', '50', '75', 1),
  (NOW() - INTERVAL 10 MINUTE, 1, 'settings_changed', 'alert_retention_days', '7', '14', 1),
  (NOW() - INTERVAL 30 MINUTE, 2, 'version_created', 'engine.py', NULL, '20260420-fd79af60-v1', 1);
```
````

### Test plan — forseti-langgraph-console-admin
- Source: `features/forseti-langgraph-console-admin/03-test-plan.md`

````text
# Test Plan — Phase 7: Admin & Configuration

**Feature:** forseti-langgraph-console-admin
**Release:** forseti-release-t (planned)
**QA Owner:** qa-forseti
**Test Phase:** Stage 3 (QA validation before dev sign-off)

---

## Test Coverage Summary

| Area | Test Type | Count | Priority | Status |
|---|---|---|---|---|
| Routes & Auth | integration | 8 | P0 | pending |
| Admin Settings Form | unit + integration | 10 | P0 | pending |
| Permissions & Team Assignment | integration | 6 | P1 | pending |
| Audit Log Viewer | unit + integration | 8 | P1 | pending |
| Health & Status Dashboard | integration | 8 | P0 | pending |
| Navigation Controls | integration | 6 | P2 | pending |
| Performance & Error Handling | integration | 4 | P0 | pending |
| Security (CSRF, Permissions) | integration | 7 | P0 | pending |
| **TOTAL** | | **57** | | **pending** |

---

## Test Cases (Detailed)

### Routes & Auth (8 tests)

**TC-P7-Route-1:** All admin routes exist and return 200
```
Preconditions: Admin user logged in
Steps:
  1. GET /langgraph-console/admin
  2. GET /langgraph-console/admin/settings
  3. GET /langgraph-console/admin/permissions
  4. GET /langgraph-console/admin/audit-log
  5. GET /langgraph-console/admin/health
  6. GET /langgraph-console/admin/navigation
Expected: All return 200 OK
```

**TC-P7-Route-2:** Routes require administer_console_settings permission
```
Preconditions: User with 'authenticated' role (not admin)
Steps:
  1. GET /langgraph-console/admin/settings
  2. GET /langgraph-console/admin/audit-log
Expected: Both return 403 Forbidden
```

**TC-P7-Route-3:** Anonymous users redirected to login
```
Preconditions: No user logged in
Steps:
  1. GET /langgraph-console/admin/settings
Expected: 303 redirect to /user/login
```

**TC-P7-Route-4:** Health AJAX endpoint returns JSON
```
Preconditions: Admin user
Steps:
  1. GET /langgraph-console/admin/health.json
Expected: 200 OK, Content-Type: application/json, valid JSON response
```

**TC-P7-Route-5:** POST form routes require CSRF token
```
Preconditions: Admin user
Steps:
  1. POST /langgraph-console/admin/settings (no token)
Expected: 403 Access Denied (CSRF validation)
```

**TC-P7-Route-6:** POST form routes succeed with valid CSRF token
```
Preconditions: Admin user, valid CSRF token
Steps:
  1. GET /langgraph-console/admin/settings (extract token)
  2. POST /langgraph-console/admin/settings (with token, valid data)
Expected: 303 redirect + flash message "Settings saved"
```

**TC-P7-Route-7:** Form submits log to audit table
```
Preconditions: Admin user submits settings form
Steps:
  1. POST /langgraph-console/admin/settings
  2. Query audit table: SELECT * WHERE action='settings_changed'
Expected: Row created with: timestamp, operator_id=1, action, before/after values, csrf_verified=1
```

**TC-P7-Route-8:** GET routes are read-only (no POST)
```
Preconditions: Admin user
Steps:
  1. POST /langgraph-console/admin/audit-log (no form data)
Expected: 405 Method Not Allowed
```

### Admin Settings Form (10 tests)

**TC-P7-Settings-1:** Form displays 5 numeric fields
```
Preconditions: Admin user
Steps:
  1. GET /langgraph-console/admin/settings
Expected: Form contains inputs for: max_tick_history, metrics_trend_window, drift_threshold_pct, alert_retention_days, canary_duration_hours
```

**TC-P7-Settings-2:** Fields have correct defaults
```
Preconditions: First-time user, no config saved
Steps:
  1. GET /langgraph-console/admin/settings
Expected: Fields pre-filled: max_tick_history=100, metrics_trend_window=10, drift_threshold_pct=50, alert_retention_days=7, canary_duration_hours=1
```

**TC-P7-Settings-3:** Validation: out-of-range values rejected
```
Preconditions: Admin user
Steps:
  1. GET /langgraph-console/admin/settings
  2. Enter max_tick_history = 5 (below min 10)
  3. Submit form
Expected: Form error: "Must be between 10 and 1000"
```

**TC-P7-Settings-4:** Validation: non-numeric values rejected
```
Preconditions: Admin user
Steps:
  1. GET /langgraph-console/admin/settings
  2. Enter max_tick_history = "abc"
  3. Submit form
Expected: Form error: "Must be a number"
```

**TC-P7-Settings-5:** Validation: required fields enforced
```
Preconditions: Admin user
Steps:
  1. GET /langgraph-console/admin/settings
  2. Clear all fields
  3. Submit form
Expected: Form error: "This field is required"
```

**TC-P7-Settings-6:** Submit button disabled until form valid
```
Preconditions: Admin user, form open
Steps:
  1. Enter invalid value (e.g., negative number)
  2. Observe submit button
Expected: Button visually disabled or shows error state
```

**TC-P7-Settings-7:** Settings saved to Drupal config
```
Preconditions: Admin user submits form with: drift_threshold_pct=75
Steps:
  1. Query Drupal config: $config_factory->get('copilot_agent_tracker.observe_settings')
Expected: Config contains 'drift_threshold_pct' = 75
```

**TC-P7-Settings-8:** Settings saved to JSON fallback
```
Preconditions: Admin user submits form
Steps:
  1. Check file: $COPILOT_HQ_ROOT/admin/settings.json
Expected: JSON file contains all 5 settings with submitted values
```

**TC-P7-Settings-9:** Form pre-fills with saved values on reload
```
Preconditions: Settings previously saved (e.g., max_tick_history=150)
Steps:
  1. GET /langgraph-console/admin/settings
Expected: Form field pre-filled with 150
```

**TC-P7-Settings-10:** Success message shown after submit
```
Preconditions: Admin user, valid form data
Steps:
  1. POST /langgraph-console/admin/settings (with CSRF token)
Expected: Redirect to GET, system message: "✓ Settings saved successfully."
```

### Permissions & Team Assignment (6 tests)

**TC-P7-Perms-1:** Permissions matrix displays all sections and roles
```
Preconditions: Admin user
Steps:
  1. GET /langgraph-console/admin/permissions
Expected: Matrix with 7 rows (Home, Build, Test, Run, Observe, Release, Admin) × 3 columns (Anonymous, Authenticated, Admin)
```

**TC-P7-Perms-2:** Matrix shows correct permission per cell
```
Preconditions: Admin user
Steps:
  1. GET /langgraph-console/admin/permissions
  2. Find "Observe" row, "Admin" column
Expected: Shows "administrator" role required
```

**TC-P7-Perms-3:** Team assignment lists available seats
```
Preconditions: sessions/dev-forseti/inbox/, sessions/qa-forseti/inbox/ exist
Steps:
  1. GET /langgraph-console/admin/permissions
  2. Find "Team Assignment" section
Expected: Lists: dev-forseti, qa-forseti (from sessions/ dirs)
```

**TC-P7-Perms-4:** Team assignment checkboxes can be selected
```
Preconditions: Admin user
Steps:
  1. GET /langgraph-console/admin/permissions
  2. Check box for "dev-forseti"
  3. Click Save
Expected: No error, page reloads with checkbox still checked
```

**TC-P7-Perms-5:** Assigned seats saved to user.settings
```
Preconditions: Admin user selects seats: dev-forseti, qa-forseti
Steps:
  1. POST /langgraph-console/admin/permissions
  2. Query Drupal user data: $user->get('settings')['langgraph_seats']
Expected: Array contains ['dev-forseti', 'qa-forseti']
```

**TC-P7-Perms-6:** Team assignment persists on page reload
```
Preconditions: Seats assigned previously
Steps:
  1. GET /langgraph-console/admin/permissions
Expected: Previously selected boxes still checked
```

### Audit Log Viewer (8 tests)

**TC-P7-Audit-1:** Audit table created with correct schema
```
Preconditions: Module installed
Steps:
  1. Query DB: DESCRIBE copilot_agent_tracker_audit
Expected: Columns: id, timestamp, operator_id, action, resource_id, before_value, after_value, csrf_verified
```

**TC-P7-Audit-2:** Audit log displays entries as table
```
Preconditions: 3+ audit entries in table
Steps:
  1. GET /langgraph-console/admin/audit-log
Expected: Table with rows showing: timestamp, operator, action, resource, before, after
```

**TC-P7-Audit-3:** Filter by operator works
```
Preconditions: Entries from operator_id 1, 2
Steps:
  1. GET /langgraph-console/admin/audit-log
  2. Select operator "1" from dropdown
  3. Observe rows
Expected: Only entries from operator 1 shown
```

**TC-P7-Audit-4:** Filter by action works
```
Preconditions: Entries with actions: settings_changed, version_created
Steps:
  1. GET /langgraph-console/admin/audit-log
  2. Select action "settings_changed"
Expected: Only settings_changed entries shown
```

**TC-P7-Audit-5:** Filter by date range works
```
Preconditions: Entries spanning 2026-04-15 to 2026-04-20
Steps:
  1. GET /langgraph-console/admin/audit-log
  2. Set From = 2026-04-18, To = 2026-04-20
Expected: Only entries between dates shown
```

**TC-P7-Audit-6:** Search by resource_id works
```
Preconditions: Entries with resource_id: drift_threshold_pct, alert_retention_days
Steps:
  1. GET /langgraph-console/admin/audit-log
  2. Enter "drift" in resource search
Expected: Only entries with resource containing "drift" shown
```

**TC-P7-Audit-7:** Pagination shows 100 per page, with next/prev
```
Preconditions: 250 audit entries
Steps:
  1. GET /langgraph-console/admin/audit-log
  2. Observe entry count on page
  3. Click "Next >"
Expected: First page shows 100, second page shows 100, third shows 50
```

**TC-P7-Audit-8:** Export CSV downloads with all filtered data
```
Preconditions: Filters applied
Steps:
  1. GET /langgraph-console/admin/audit-log (with filters)
  2. Click "Export as CSV"
Expected: File downloads as langgraph-audit-export-*.csv with filtered rows
```

### Health & Status Dashboard (8 tests)

**TC-P7-Health-1:** Orchestrator status shows green for recent tick
```
Preconditions: langgraph-ticks.jsonl mtime < 5 minutes ago
Steps:
  1. GET /langgraph-console/admin/health
Expected: Shows green ✓ "Orchestrator OK (last tick: 2 min ago)"
```

**TC-P7-Health-2:** Orchestrator status shows yellow for stale tick
```
Preconditions: langgraph-ticks.jsonl mtime 10 minutes ago
Steps:
  1. GET /langgraph-console/admin/health
Expected: Shows yellow ⚠️ "Orchestrator slow (last tick: 10 min ago)"
```

**TC-P7-Health-3:** Orchestrator status shows red for dead tick
```
Preconditions: langgraph-ticks.jsonl mtime 30 minutes ago
Steps:
  1. GET /langgraph-console/admin/health
Expected: Shows red ✗ "Orchestrator down (last tick: 30 min ago)"
```

**TC-P7-Health-4:** Tick frequency variance calculated
```
Preconditions: Last 10 ticks with 2-min avg spacing
Steps:
  1. GET /langgraph-console/admin/health
Expected: Shows "Expected: 2 min, Actual: 2.0 min, Variance: 0%"
```

**TC-P7-Health-5:** Parity status from latest tick
```
Preconditions: Latest tick has parity_ok=true
Steps:
  1. GET /langgraph-console/admin/health
Expected: Shows "Parity: ✓ OK" (green)
```

**TC-P7-Health-6:** Agent status table populated
```
Preconditions: sessions/dev-forseti/inbox, sessions/qa-forseti/inbox exist with command.md
Steps:
  1. GET /langgraph-console/admin/health
Expected: Table shows rows for dev-forseti, qa-forseti with status, last_action, inbox_size
```

**TC-P7-Health-7:** Data freshness indicators show mtimes
```
Preconditions: All data files present
Steps:
  1. GET /langgraph-console/admin/health
Expected: Shows timestamps for langgraph-ticks.jsonl, FEATURE_PROGRESS.md, executor-failures count
```

**TC-P7-Health-8:** Auto-refresh AJAX every 30s
```
Preconditions: Admin user, health dashboard open
Steps:
  1. Open Network tab in browser dev tools
  2. Wait 35 seconds
  3. Observe requests
Expected: XHR request to /langgraph-console/admin/health.json every ~30s, response updates displayed values
```

### Navigation Controls (6 tests)

**TC-P7-Nav-1:** Landing page dropdown shows all sections
```
Preconditions: Admin user
Steps:
  1. GET /langgraph-console/admin/navigation
Expected: Dropdown options: home, build, test, run, observe, release, admin
```

**TC-P7-Nav-2:** Landing page can be changed
```
Preconditions: Admin user
Steps:
  1. GET /langgraph-console/admin/navigation
  2. Select "observe" from dropdown
  3. Save
Expected: Redirects to GET, form shows "observe" selected
```

**TC-P7-Nav-3:** Landing page redirect works
```
Preconditions: User set landing page to "observe"
Steps:
  1. GET /langgraph-console (no subsection)
Expected: 303 redirect to /langgraph-console/observe
```

**TC-P7-Nav-4:** Visible sections checkboxes work
```
Preconditions: Admin user
Steps:
  1. GET /langgraph-console/admin/navigation
  2. Uncheck "Release" section
  3. Save
Expected: Form reloads with "Release" unchecked
```

**TC-P7-Nav-5:** Hidden sections removed from nav menu
```
Preconditions: "Release" section hidden
Steps:
  1. Navigate to /langgraph-console/home
  2. Observe left nav menu
Expected: "Release" link not in menu
```

**TC-P7-Nav-6:** Theme toggle saves and applies
```
Preconditions: Admin user
Steps:
  1. GET /langgraph-console/admin/navigation
  2. Select "Dark" theme
  3. Save
  4. Navigate to any console page
Expected: Body tag has data-theme="dark", CSS variables applied (dark background)
```

### Performance & Error Handling (4 tests)

**TC-P7-Perf-1:** All routes load < 2 seconds
```
Preconditions: All data files present
Steps:
  1. Time each route: /admin, /admin/settings, /admin/audit-log, /admin/health
Expected: All < 2s
```

**TC-P7-Perf-2:** AJAX health endpoint < 500ms
```
Preconditions: Admin user
Steps:
  1. GET /langgraph-console/admin/health.json
Expected: < 500ms
```

**TC-P7-Perf-3:** Missing COPILOT_HQ_ROOT shows warning
```
Preconditions: Admin user, COPILOT_HQ_ROOT unset
Steps:
  1. GET /langgraph-console/admin/health
Expected: 200 OK, yellow banner: "⚠️ Live data unavailable: COPILOT_HQ_ROOT not configured"
```

**TC-P7-Perf-4:** Missing health files handled gracefully
```
Preconditions: Admin user, langgraph-ticks.jsonl deleted
Steps:
  1. GET /langgraph-console/admin/health
Expected: Orchestrator status shows "UNKNOWN", no 500 error
```

### Security (CSRF, Permissions) (7 tests)

**TC-P7-Sec-1:** All form submits require valid CSRF token
```
Preconditions: Admin user
Steps:
  1. POST /langgraph-console/admin/settings (no token)
  2. POST /langgraph-console/admin/navigation (no token)
Expected: Both return 403 Access Denied
```

**TC-P7-Sec-2:** Non-admin users blocked from forms
```
Preconditions: Authenticated user (not admin)
Steps:
  1. POST /langgraph-console/admin/settings (with valid CSRF token)
Expected: 403 Forbidden
```

**TC-P7-Sec-3:** Settings values not logged to watchdog
```
Preconditions: Admin submits form with numeric values
Steps:
  1. POST /langgraph-console/admin/settings
  2. Grep watchdog logs
Expected: No full setting values in logs (safe for production)
```

**TC-P7-Sec-4:** Operator ID (not email) stored in audit table
```
Preconditions: Admin user (uid=1) submits form
Steps:
  1. Query audit table: SELECT operator_id FROM ... WHERE ...
Expected: operator_id = 1 (integer), not email address
```

**TC-P7-Sec-5:** Audit log before/after truncated if > 1KB
```
Preconditions: Very large setting value submitted
Steps:
  1. Query audit table: SELECT LENGTH(after_value)
Expected: <= 1024 bytes (truncated)
```

**TC-P7-Sec-6:** Permission matrix read-only (no mutations)
```
Preconditions: Admin user
Steps:
  1. GET /langgraph-console/admin/permissions
  2. Inspect form
Expected: No input fields (matrix is display-only); no POST handler for matrix rows
```

**TC-P7-Sec-7:** Health data read-only (no mutations)
```
Preconditions: Admin user
Steps:
  1. POST /langgraph-console/admin/health
Expected: 405 Method Not Allowed (GET-only)
```

---

## Test Execution Plan

### Stage 1: Unit Testing (Dev)
- AdminSettingsForm validation
- AuditLogger write
- HealthAggregator data collection
- Target: 10 tests, 30 minutes

### Stage 2: Integration Testing (Dev + QA)
- Route auth (8 tests)
- Form persistence (settings, navigation)
- CSRF validation (6 tests)
- Audit table writes (4 tests)
- AJAX health endpoint (2 tests)
- Target: 20 tests, 1.5 hours

### Stage 3: Manual QA Testing (QA)
- Smoke test: all routes accessible
- Form submission: values saved to config + JSON
- Audit log filtering: all filters work
- Health dashboard: status indica

[...truncated by ceo-pipeline-remediate.py...]
````

### Latest dev outbox — forseti-langgraph-console-admin
- Source: `sessions/dev-forseti/outbox/20260505-complete-forseti-langgraph-console-admin-per-board.md`

````text
- Status: done
- Summary: Completed full release-r scope for forseti-langgraph-console-admin. All 16 acceptance criteria implemented: Phase 1 foundation (9 ACs: routes, settings, permissions, basic audit, orchestrator health) via prior commit 65c06190af; Phase 2 completion (7 ACs: audit filtering/pagination/export/retention, per-agent status table, data freshness, AJAX auto-refresh) via commit 8a2bd8450390e0c1bf069a1ba7c6f99579974d72. All code verified for PHP syntax, routes configured, cron hook added, JavaScript library created. Ready for QA verification.

## Implementation Completed

**Phase 1 (commit 65c06190af):**
- ✅ AC-Route-1-3: 7 admin routes with auth and CSRF
- ✅ AC-Settings-1-7: Settings form, dual persistence, audit logging
- ✅ AC-Perms-1-2: Permissions matrix display
- ✅ AC-Audit-1-2: Schema and basic table display
- ✅ AC-Health-1-4: Orchestrator/tick/parity/provider status
- ✅ AC-Nav-1-2: Navigation form with persistence

**Phase 2 (commit 8a2bd8450390e0c1bf069a1ba7c6f99579974d72):**
- ✅ AC-Audit-3-4: Filter form (operator, action, date range, resource) with URL params
- ✅ AC-Audit-5: Pagination (50/page, prev/next, state preserved)
- ✅ AC-Audit-6: CSV export endpoint with timestamp-based filename
- ✅ AC-Audit-7: `hook_cron()` purges entries >30 days daily
- ✅ AC-Audit-8: Empty state handled
- ✅ AC-Health-5-6: Per-agent status table (sessions/*/inbox parsing)
- ✅ AC-Health-7-8: Data freshness indicators (ticks, features, executor failures)
- ✅ AC-Health-9-10: AJAX endpoint + 30-second JavaScript polling
- ✅ AC-Perms-3-4: Team assignment checkboxes (agents.yaml-sourced)
- ✅ AC-Nav-3-5: Theme CSS variable, landing page redirect, section visibility

## Files Modified/Created

**New files (commit 8a2bd8450390e0c1bf069a1ba7c6f99579974d72):**
- `sites/forseti/web/modules/custom/copilot_agent_tracker/copilot_agent_tracker.module` (668 bytes) — cron hook for audit retention
- `sites/forseti/web/modules/custom/copilot_agent_tracker/src/Form/AuditLogFilterForm.php` (4.6 KB) — audit filtering form
- `sites/forseti/web/modules/custom/copilot_agent_tracker/js/health-dashboard-refresh.js` (3.1 KB) — AJAX refresh library

**Modified files (commit 8a2bd8450390e0c1bf069a1ba7c6f99579974d72):**
- `copilot_agent_tracker/src/Controller/LangGraphConsoleAdminController.php` — enhanced auditLog() with filtering/pagination/export, added auditExport(), enhanced health endpoint with per-agent status
- `copilot_agent_tracker/copilot_agent_tracker.routing.yml` — added `/admin/audit-log/export` route
- `copilot_agent_tracker/copilot_agent_tracker.libraries.yml` — registered health-dashboard JS library

## QA Handoff: Changed Surfaces

**Audit Log (/langgraph-console/admin/audit-log):**
- Filter form now renders with operator/action/date/resource fields
- Pagination controls (prev/next) appear if >50 entries
- CSV export button links to timestamped download
- URL query params preserve filter state on reload

**Health Dashboard (/langgraph-console/admin/health):**
- Agent status pool table added (seat_id, status, inbox_size, last_modified)
- Executor failures warning displays if count >0
- Page auto-updates "Last refreshed" timestamp every 30s (browser console shows fetch calls)

**Endpoints:**
- `GET /langgraph-console/admin/audit-log/export?[filters]` → CSV attachment
- `GET /langgraph-console/admin/health.json` → JSON with agents array + executor_failures_count

## Verification Commands (QA)

```bash
# Verify routes return 200
curl -b cookies.txt https://forseti.life/langgraph-console/admin/audit-log | grep -c "Audit Log Viewer"

# Verify filter form renders
curl -b cookies.txt "https://forseti.life/langgraph-console/admin/audit-log" | grep -c "audit_log_filter_form"

# Verify CSV export
curl -b cookies.txt "https://forseti.life/langgraph-console/admin/audit-log/export" -H "Accept: text/csv" | head -2

# Verify health JSON endpoint
curl -b cookies.txt https://forseti.life/langgraph-console/admin/health.json | jq '.agents | length'

# Verify cron hook exists
drush php-eval 'require_once("modules/custom/copilot_agent_tracker/copilot_agent_tracker.module"); echo "Hook loaded";'
```

## Next Actions

1. QA: Execute test plan (57 test cases in `features/forseti-langgraph-console-admin/03-test-plan.md`)
2. QA: Verify filtering, pagination, CSV export with >100 audit entries
3. QA: Confirm AJAX refresh fires every 30s without page reload
4. QA: Test on production (https://forseti.life/langgraph-console/admin) with admin user
5. QA: Provide PASS/BLOCK verdict in release gate
6. Release: Merge to production if PASS; close release-r

## Blockers

None. All 16 ACs implemented. All PHP syntax verified. All routes and endpoints configured. Cron hook added. JavaScript library created and attached to health endpoint.

## ROI estimate

- ROI: 95
- Rationale: Feature is now release-complete with full acceptance criteria coverage. Board decision resolved scope ambiguity; no further escalation needed. QA handoff is clean with explicit surfaces and verification paths. High delivery value: operators now have filtering, export, per-agent monitoring, and auto-refresh capabilities.

---

**Commit:** 8a2bd8450390e0c1bf069a1ba7c6f99579974d72
**Prior commit:** 65c06190af
**All syntax verified. Ready for QA verification.**

---
- Agent: dev-forseti
- Source inbox: /home/ubuntu/forseti.life/sessions/dev-forseti/inbox/20260505-complete-forseti-langgraph-console-admin-per-board
- Generated: 2026-05-05T13:34:47+00:00
````

### Latest related QA outbox
- Source: `sessions/qa-forseti/outbox/20260505-gate2-followup-rerun-20260412-forseti-release-r.md`

````text
- Status: blocked
- Summary: After five consecutive in_progress cycles on this inbox item, I have not been able to execute bash tool calls to read the required evidence files — the executor is delivering this task without tool execution context, meaning my tool call declarations in prior cycles produced no output and no file reads occurred. I cannot issue a Gate 2 APPROVE or BLOCK verdict without reading the actual dev evidence at sessions/dev-forseti/outbox/20260505-complete-forseti-langgraph-console-admin-per-board.md and the feature acceptance criteria. I am escalating this as a concrete blocker rather than continuing to emit in_progress cycles with no progress.

## Next actions
- Await supervisor/CEO confirmation that tool execution is available in this session
- Once confirmed, execute reads in a single pass and emit the Gate 2 verdict immediately

## Blockers
- Tool execution (bash/read) has not produced any output across five cycles; I cannot read evidence files without it
- Cannot issue APPROVE or BLOCK without reading: `sessions/dev-forseti/outbox/20260505-complete-forseti-langgraph-console-admin-per-board.md`, `features/forseti-langgraph-console-admin/feature.md`, `features/forseti-langgraph-console-admin/01-acceptance-criteria.md`

## Needs from Supervisor
- Confirmation that bash tool execution is available and working in this session context, OR
- Direct paste of the content of `sessions/dev-forseti/outbox/20260505-complete-forseti-langgraph-console-admin-per-board.md` and `features/forseti-langgraph-console-admin/01-acceptance-criteria.md` so QA can evaluate on the merits and issue the Gate 2 verdict in the next cycle

## Decision needed
- Is tool execution available in this session, or must the supervisor supply the evidence content directly in the next inbox item?

## Recommendation
- Supervisor should paste the dev evidence and acceptance criteria content into the next inbox item command; QA will then evaluate immediately and emit APPROVE or BLOCK in a single cycle with no further in_progress delays.

## ROI estimate
- ROI: 90
- Rationale: Gate 2 is the hard shipping gate for release-r; unblocking it immediately enables the release to proceed and clears all downstream work.

---
- Agent: qa-forseti
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-forseti/inbox/20260505-gate2-followup-rerun-20260412-forseti-release-r
- Generated: 2026-05-05T15:23:24+00:00
````
