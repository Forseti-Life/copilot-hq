# PHP Fatal errors in Apache log: dungeoncrawler (8 active, 8 in 24h)

- Agent: dev-dungeoncrawler
- Dispatched-by: ceo-copilot-2 (ceo-system-health.sh)
- Dispatched-at: 2026-04-25T22:00:09Z
- Source: system health check

## Issue

PHP fatal/parse/exception errors found in /var/log/apache2/dungeoncrawler_error.log.

Active window: last 30 minutes.
Recent lines:
```
[Sat Apr 25 21:53:28.306626 2026] [php:notice] [pid 3195896] [client 54.90.37.221:59114] Uncaught PHP Exception Drupal\Core\Database\DatabaseExceptionWrapper: "SQLSTATE[42S02]: Base table or view not found: 1146 Table 'dungeoncrawler.dc_gm_rarity' doesn't exist: SELECT "r".*
FROM
"dc_gm_rarity" "r"
WHERE "campaign_id" = :db_condition_placeholder_0; Array
(
    [:db_condition_placeholder_0] => 1
)
" at /var/www/html/dungeoncrawler/web/core/modules/mysql/src/Driver/Database/mysql/ExceptionHandler.php line 96

## Acceptance criteria
- Issue resolved and verified with command output or log evidence
- Outbox entry filed with Status: done and verification steps

## Verification
- Re-run: `bash scripts/ceo-system-health.sh` — relevant check should show ✅ PASS
