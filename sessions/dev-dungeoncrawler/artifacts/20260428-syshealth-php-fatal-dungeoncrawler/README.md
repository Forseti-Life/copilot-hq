# PHP Fatal errors in Apache log: dungeoncrawler (2 active, 2 in 24h)

- Agent: dev-dungeoncrawler
- Dispatched-by: ceo-copilot-2 (ceo-system-health.sh)
- Dispatched-at: 2026-04-28T12:40:09Z
- Source: system health check

## Issue

PHP fatal/parse/exception errors found in /var/log/apache2/dungeoncrawler_error.log.

Active window: last 30 minutes.
Recent lines:
```
[Tue Apr 28 12:37:07.256448 2026] [php:error] [pid 2018395] [client 98.111.147.11:58018] PHP Fatal error:  Uncaught Error: Failed opening required '/var/www/html/dungeoncrawler/vendor/composer/../web/core/includes/bootstrap.inc' (include_path='/var/www/html/dungeoncrawler/vendor/pear/archive_tar:/var/www/html/dungeoncrawler/vendor/pear/console_getopt:/var/www/html/dungeoncrawler/vendor/pear/pear-core-minimal/src:/var/www/html/dungeoncrawler/vendor/pear/pear_exception:.:/usr/share/php') in /var/www/html/dungeoncrawler/vendor/composer/autoload_real.php:45
Stack trace:
#0 /var/www/html/dungeoncrawler/vendor/composer/autoload_real.php(49): {closure}()
#1 /var/www/html/dungeoncrawler/vendor/autoload.php(22): ComposerAutoloaderInitf0a10d7d344845ce652d957387ad7d0e::getLoader()
#2 /var/www/html/dungeoncrawler/web/autoload.php(15): require('...')
#3 /var/www/html/dungeoncrawler/web/index.php(14): require_once('...')
#4 {main}
  thrown in /var/www/html/dungeoncrawler/vendor/composer/autoload_real.php on line 45, referer: https://dungeoncrawler.forseti.life/hexmap?campaign_id=13&character_id=25&dungeon_level_id=f8c6b8f1-2df9-469f-9fd5-67a59f120001&map_id=0b7e3d2f-8f7c-4ae0-8f72-9e99e0800001&room_id=7f2f1051-5f88-45a2-a66a-0f7063900001&next_room_id=7f2f1051-5f88-45a2-a66a-0f7063900002&start_q=0&start_r=0

## Acceptance criteria
- Issue resolved and verified with command output or log evidence
- Outbox entry filed with Status: done and verification steps

## Verification
- Re-run: `bash scripts/ceo-system-health.sh` — relevant check should show ✅ PASS
