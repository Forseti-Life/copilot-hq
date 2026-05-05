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
- Live versioned custom-code/config source: `/home/ubuntu/forseti.life/sites/dungeoncrawler`
- Apache serves the live site from `/var/www/html/dungeoncrawler/web`, with these symlinked back into `sites/dungeoncrawler`:
  - `web/modules/custom`
  - `web/themes/custom`
  - `config/sync`
- `/home/ubuntu/forseti.life/dungeoncrawler-pf2e` is a separate product-repo checkout, not the live website root.

## Working convention
- Treat `/home/ubuntu/forseti.life/sites/dungeoncrawler` as the canonical source for live custom module, theme, and config changes on this host.
- Use `/var/www/html/dungeoncrawler` when commands must run against the full live Drupal install (for example `drush cr`).
- Do not assume `dungeoncrawler-pf2e` and `sites/dungeoncrawler` are mirrored; verify before copying changes between them.

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
