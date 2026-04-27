# PHP Fatal errors in Apache log: dungeoncrawler (4 active, 80 in 24h)

- Agent: dev-dungeoncrawler
- Dispatched-by: ceo-copilot-2 (ceo-system-health.sh)
- Dispatched-at: 2026-04-27T19:10:08Z
- Source: system health check

## Issue

PHP fatal/parse/exception errors found in /var/log/apache2/dungeoncrawler_error.log.

Active window: last 30 minutes.
Recent lines:
```
[Mon Apr 27 18:41:02.718870 2026] [php:notice] [pid 1487206] [client 50.232.247.82:40168] Uncaught PHP Exception ParseError: "syntax error, unexpected token "const"" at /var/www/html/dungeoncrawler/vendor/drupal/core/includes/install.inc line 29, referer: https://dungeoncrawler.forseti.life/admin/reports

## Acceptance criteria
- Issue resolved and verified with command output or log evidence
- Outbox entry filed with Status: done and verification steps

## Verification
- Re-run: `bash scripts/ceo-system-health.sh` — relevant check should show ✅ PASS
- Status: pending
