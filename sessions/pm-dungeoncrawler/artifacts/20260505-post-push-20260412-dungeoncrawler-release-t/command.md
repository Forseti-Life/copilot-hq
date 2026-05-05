- Status: done
- Completed: 2026-05-05T13:30:42Z

# Post-push steps: dungeoncrawler release

The dungeoncrawler deploy for `20260412-dungeoncrawler-release-t` was triggered automatically after PM signoff.

## 1. Wait for deploy workflow to finish
```bash
gh run list --repo keithaumiller/forseti.life --workflow deploy.yml --limit 3
```

## 2. Production config import / cache rebuild
```bash
cd /var/www/html/dungeoncrawler && vendor/bin/drush config:import -y && vendor/bin/drush cr
```

## 3. Advance this team's release cycle
```bash
bash scripts/post-coordinated-push.sh dungeoncrawler 20260412-dungeoncrawler-release-t
```

## 4. Gate R5 — post-release production QA
```bash
ALLOW_PROD_QA=1 bash scripts/site-audit-run.sh dungeoncrawler
```

Record clean/unclean signal in your outbox.
