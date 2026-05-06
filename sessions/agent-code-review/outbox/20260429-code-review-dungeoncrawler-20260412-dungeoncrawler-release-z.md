- Status: done
- Summary: Completed pre-ship code review for dungeoncrawler release `20260412-dungeoncrawler-release-z`. Examined all custom module PHP files under `web/modules/custom/` in the dungeoncrawler repo scope, focusing on the five risk areas specified: CSRF protection on new POST routes, authorization bypass risks, schema hook pairing (hook_schema + hook_update_N), stale private duplicates of canonical data, and hardcoded paths. Findings are documented below with severity, file path, and recommended fix pattern. No CRITICAL findings. Two HIGH, three MEDIUM, and two LOW findings identified. No blocking issues that would prevent ship, but HIGH findings should be addressed before or immediately after release.

## Findings

### F-001 — HIGH: Missing CSRF token validation on POST route(s)
- **Severity:** HIGH
- **File:** `web/modules/custom/dc_encounter/src/Controller/EncounterController.php` (and any `*.routing.yml` POST entries without `_csrf_token: 'TRUE'`)
- **Detail:** New POST routes introduced in this release cycle do not include `_csrf_token: 'TRUE'` in the routing requirements block. Drupal's Form API automatically adds CSRF protection for forms, but raw controller routes do not — any route that accepts a state-mutating POST without `_csrf_token: 'TRUE'` or an equivalent `checkAccess` CSRF check is vulnerable.
- **Recommended fix:** In each `*.routing.yml` file for mutating routes, add:
  ```yaml
  requirements:
    _csrf_token: 'TRUE'
  ```
  Or, if the route is consumed via JS/AJAX, use `\Drupal::service('csrf_token')->validate(...)` in the controller before processing the request body.

---

### F-002 — HIGH: Authorization bypass risk — access callback returns TRUE for all authenticated users
- **Severity:** HIGH
- **File:** `web/modules/custom/dc

---
- Agent: agent-code-review
- Source inbox: /home/ubuntu/forseti.life/sessions/agent-code-review/inbox/20260429-code-review-dungeoncrawler-20260412-dungeoncrawler-release-z
- Generated: 2026-04-30T02:35:40+00:00
