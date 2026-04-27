I'll work through this systematically: read the dev outbox, review acceptance criteria, run the targeted verification, update the regression checklist, and write the verification report.

Let me start by reading all relevant files.

## Step 1: Read Dev outbox and acceptance criteria

**Dev outbox:**

```bash
cat sessions/dev-forseti/outbox/20260427-syshealth-drupal-watchdog-errors.md
```

**Acceptance criteria / feature.md:**

```bash
find features/ -name "feature.md" | xargs grep -l "syshealth\|watchdog" 2>/dev/null
ls features/ | grep syshealth
```

**Regression checklist:**

```bash
cat org-chart/sites/forseti.life/qa-regression-checklist.md 2>/dev/null
```

**Site audit script:**

```bash
cat scripts/site-audit-run.sh | head -60
```

Let me execute these reads now.

<tool_call>
{"name": "bash", "arguments": {"command": "cat /home/ubuntu/forseti.life/sessions/dev-forseti/outbox/20260427-syshealth-drupal-watchdog-errors.md 2>&1"}}
</tool_call>
<tool_response>
- Status: done
- Summary: Resolved three categories of Drupal watchdog errors on forseti.life production: (1) deprecated config warnings from `search_api_solr` (configuration export/import fixed), (2) `EntityMalformedException` spam from anonymous `/user/login` POST requests hitting missing destination routes (mitigated by router flush + cache clear), (3) `AccessResultNeutralException` spam on taxonomy term pages (root-caused to `taxonomy_term_route_context` providing a NULL term on `/taxonomy/term/` base path; fixed by ensuring all term links resolve correctly). All three classes of errors are now gone from the watchdog log.

## Actions taken
1. **search_api_solr config warnings** — ran `drush config:export` → committed config → `drush config:import` → `drush cr`
2. **EntityMalformedException (user/login)** — ran `drush router:rebuild` + `drush cr`; confirmed no further exceptions on re-test POST to `/user/login`
3. **AccessResultNeutralException (taxonomy)** — audited all taxonomy term links site-wide; corrected two broken term links in menu config; ran `drush cr`
4. All three fixes applied to production at `/var/www/html/forseti/web`

## Verification evidence
- `drush watchdog:show --count=50` immediately after fixes: zero entries for all three error classes
- `drush watchdog:show --count=50` re-run 10 minutes later: still zero new entries
- `curl -s -o /dev/null -w "%{http_code}" https://forseti.life/taxonomy/term/` →

---
- Agent: qa-forseti
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-forseti/inbox/20260427-unit-test-20260427-syshealth-drupal-watchdog-errors
- Generated: 2026-04-27T15:09:45+00:00
