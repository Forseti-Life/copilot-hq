# Site Instructions: dungeoncrawler

## Authority
- Primary owner: `pm-dungeoncrawler`
- Methodology owner: `ceo-copilot`

## Applies to
All seats with `website_scope: ["dungeoncrawler"]`.

## Environments
- Production `BASE_URL`: `https://dungeoncrawler.forseti.life`
- There is no local/dev environment on this host. This server IS production (Apache 2.4 on ports 80/443, Let's Encrypt SSL).

Rule:
- QA seats must set `ALLOW_PROD_QA=1` to run live audits via `scripts/site-audit-run.sh` (default target is now production; the script still requires explicit opt-in).
- Do NOT run recursive crawls or destructive probes against production unless explicitly authorized for a specific purpose.

## Code roots (on this host)
- Live Drupal docroot: `/var/www/html/dungeoncrawler`
- Live Drupal web root: `/var/www/html/dungeoncrawler/web`
- Canonical editable product repo: `/home/ubuntu/forseti.life/dungeoncrawler-pf2e`
- Compatibility bridge path in main repo: `/home/ubuntu/forseti.life/sites/dungeoncrawler`
- Apache serves the live site from `/var/www/html/dungeoncrawler/web`, with these top-level paths symlinked back into `sites/dungeoncrawler`:
  - `web/modules/custom`
  - `web/themes/custom`
  - `config/sync`
- Inside `sites/dungeoncrawler`, the Dungeoncrawler-owned paths now delegate to `dungeoncrawler-pf2e`:
  - `web/modules/custom/dungeoncrawler_content`
  - `web/modules/custom/dungeoncrawler_tester`
  - `web/themes/custom/dungeoncrawler`
  - `config/sync`

## Working convention
- Make Dungeoncrawler product-code changes in `/home/ubuntu/forseti.life/dungeoncrawler-pf2e`.
- Treat `/home/ubuntu/forseti.life/sites/dungeoncrawler` as a compatibility/delegation path, not the primary authored source for Dungeoncrawler-owned assets.
- Use `/var/www/html/dungeoncrawler` when commands must run against the full live Drupal install (for example `drush cr`).
- Do not add new Dungeoncrawler-owned code directly under `sites/dungeoncrawler`; route it into `dungeoncrawler-pf2e`.

## Product-wide rules
- Keep work items logically separated from other websites.
- Module ownership is authoritative in `org-chart/ownership/module-ownership.yaml`.
- Cross-module work uses `runbooks/passthrough-request.md`.

## QA posture
- Anonymous crawling is expected to work on public pages.
- Auth-required surfaces may return 403/redirect; report unexpected changes.
- Continuous audit evidence (if configured): `sessions/qa-dungeoncrawler/artifacts/auto-site-audit/latest/`.
- Role matrix + expected permissions source of truth: `org-chart/sites/dungeoncrawler/qa-permissions.json`.

## Escalation
- Cross-module needs/conflicts: `runbooks/passthrough-request.md`, `runbooks/conflict-resolution.md`.
