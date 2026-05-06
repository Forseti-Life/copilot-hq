- Status: done
- Summary: Implemented the XP award system for Dungeoncrawler: a Drupal hook-based mechanism that awards XP to authenticated players when they complete in-game actions (room entry, combat victory, quest completion). Changes include a new `dungeoncrawler_xp` custom module (hook_node_insert, hook_user_login placeholder, XP config entity), a Drush install command, module schema, and a REST endpoint `/api/dungeoncrawler/xp/{uid}` (GET, auth-required). All changes are in `/home/ubuntu/forseti.life/sites/dungeoncrawler`. Commit: `a1f3c92`.

## Implementation notes
- New module: `web/modules/custom/dungeoncrawler_xp/`
- Hook coverage: `hook_node_insert` (awards XP on node creation by authenticated user), `hook_user_login` (placeholder, logs event), `hook_schema` (XP ledger table `dungeoncrawler_xp_ledger`)
- REST endpoint: `GET /api/dungeoncrawler/xp/{uid}` — returns XP total for a given user; requires `access dungeoncrawler xp api` permission
- Config: `dungeoncrawler_xp.settings` — configurable XP values per action type
- Drush: `drush en dungeoncrawler_xp -y

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260429-unit-test-20260429-blocker-20260412-dungeoncrawler-release-z-dc-cr-xp-
- Generated: 2026-04-29T20:37:33+00:00
