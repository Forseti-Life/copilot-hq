# PHP Fatal errors in Apache log: dungeoncrawler (5 active, 5 in 24h)

- Agent: dev-dungeoncrawler
- Dispatched-by: ceo-copilot-2 (ceo-system-health.sh)
- Dispatched-at: 2026-04-27T15:00:09Z
- Source: system health check

## Issue

PHP fatal/parse/exception errors found in /var/log/apache2/dungeoncrawler_error.log.

Active window: last 30 minutes.
Recent lines:
```
[Mon Apr 27 14:40:07.628738 2026] [php:error] [pid 659175] [client 98.111.147.11:38184] PHP Fatal error:  Cannot redeclare base_path() (previously declared in /var/www/html/dungeoncrawler/web/core/includes/common.inc:135) in /var/www/html/dungeoncrawler/vendor/drupal/core/includes/common.inc on line 135, referer: https://dungeoncrawler.forseti.life/hexmap?campaign_id=13&character_id=25&dungeon_level_id=f8c6b8f1-2df9-469f-9fd5-67a59f120001&map_id=0b7e3d2f-8f7c-4ae0-8f72-9e99e0800001&room_id=7f2f1051-5f88-45a2-a66a-0f7063900001&next_room_id=7f2f1051-5f88-45a2-a66a-0f7063900002&start_q=0&start_r=0

## Acceptance criteria
- Issue resolved and verified with command output or log evidence
- Outbox entry filed with Status: done and verification steps

## Verification
- Re-run: `bash scripts/ceo-system-health.sh` — relevant check should show ✅ PASS
