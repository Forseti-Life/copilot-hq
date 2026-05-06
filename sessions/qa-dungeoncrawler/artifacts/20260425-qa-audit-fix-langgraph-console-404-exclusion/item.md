# QA Fix: Suppress authenticated admin routes from anonymous crawl

- Owning team: qa-dungeoncrawler
- Inbox item: 20260425-qa-audit-fix-langgraph-console-404-exclusion
- Related: dev-dungeoncrawler audit 20260424-001221 (7 repeated 404 failures)
- Priority: High (blocking KPI improvement, ROI 50)

## Problem Statement

KPI stagnation: dungeoncrawler release audit shows 7 persistent 404 failures, all on authenticated admin routes that ARE implemented and functional:
- `/admin/reports/copilot-agent-tracker/langgraph-console/*` (all 8 routes)
- These routes require `administer copilot agent tracker` permission
- Currently failing because anonymous crawl cannot access them (expected behavior)

Dev investigation completed and verified: **no code defects, no dev fixes needed**.

## Acceptance Criteria

- [ ] Choose ONE of the two options below and implement it:

**Option A (Recommended): Suppress from anonymous crawl**
- Add the 7 routes to anonymous crawl exclusion list in `/home/ubuntu/forseti.life/org-chart/sites/dungeoncrawler/qa-permissions.json`
- Routes to suppress: `/admin/reports/copilot-agent-tracker/langgraph-console/*`
- Result: Anonymous audit will skip these routes, preventing false 404s

**Option B: Test with authenticated user**
- Configure QA audit to include authenticated admin user test
- Use credentials with `administer copilot agent tracker` permission
- Result: Routes tested with proper auth, PASS verified

## Verification Method

After implementation, run:
```bash
cd /var/www/html/dungeoncrawler && \
vendor/bin/drush eval "
\$module_handler = \Drupal::service('module_handler');
if (\$module_handler->moduleExists('copilot_agent_tracker')) {
  \$routes = \Drupal::service('route_provider')->getAllRoutes();
  foreach (\$routes as \$name => \$route) {
    if (strpos(\$name, 'copilot_agent_tracker.langgraph') === 0) {
      echo \$name . ': ' . \$route->getPath() . PHP_EOL;
    }
  }
}
"
```

Then run audit:
```bash
bash scripts/qa-full-site-audit.sh dungeoncrawler 2>&1 | grep "404\|langgraph-console" || echo "No 404s on langgraph routes"
```

Expected: No 404 failures on langgraph routes (either suppressed or passing with auth).

## Next Actions

1. Choose Option A or B
2. Implement the configuration change
3. Run verification command
4. Update outbox with PASS/BLOCK result

## ROI Estimate

- **ROI: 50**
- **Rationale**: Resolves KPI stagnation (129 unanswered alerts will clear), eliminates repeated false-positive audit failures, and unblocks dev-dungeoncrawler from investigation loop. One-time configuration change with immediate impact on org visibility.

---

**Background evidence:**
- Dev investigation: `/home/ubuntu/forseti.life/sessions/dev-dungeoncrawler/outbox/20260424-001221-qa-findings-dungeoncrawler-7-retry-1777058348` (Status: done)
- Routes confirmed at: `/home/ubuntu/forseti.life/sites/dungeoncrawler/web/modules/custom/copilot_agent_tracker/copilot_agent_tracker.routing.yml`
- Permission defined at: `copilot_agent_tracker.permissions.yml`
