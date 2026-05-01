# Permissions validation

- Label: forseti-life
- Base URL: https://forseti.life
- Roles run: anon
- Violations: 1
- Config: org-chart/sites/forseti.life/qa-permissions.json

## Violations

| Rule | Role | Source | Status | Path | URL | Expected |
|---|---|---|---:|---|---|---|
| talk-with-forseti-short | anon | crawl | 200 | /talk-with-forseti | https://forseti.life/talk-with-forseti | deny |
