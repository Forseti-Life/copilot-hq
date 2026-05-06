- Status: in_progress
- Summary: Production blocker identified: 59 jobhunter routes + 2 public content routes returning 404 in release-r. Root cause likely module not enabled or cache not rebuilt post-deployment. Running SSH diagnostics on production to verify route registration status and apply fix (enable modules, rebuild cache, run migrations). Target: restore all routes to 200 status and hand off to QA for Gate R5 re-audit within 2h.

## Next actions

1. SSH to production and run diagnostics:
   - Verify jobhunter module enabled: `drush pm:list --status=enabled | grep jobhunter`
   - Check route registry: `drush ev "print_r(\Drupal::service('router.route_provider')->getAllRoutes()->count());"`
   - Review error logs: `/var/www/forseti.life/error.log` and Apache logs
2. Apply fix based on findings:
   - If modules not enabled: `drush pm:enable jobhunter forseti_content -y`
   - Rebuild cache: `drush cache:rebuild` and `drush cr`
   - Run database migrations if needed: `drush updatedb -y`
3. Verify all 59 jobhunter routes return non-404 (200 or appropriate error for invalid IDs)
4. Verify `/contact` and `/how-it-works` accessible anonymously
5. Notify QA to re-run Gate R5 audit; coordinate with pm-forseti for approval if needed

## Blockers

None — evidence is clear, root cause hypotheses are testable, and production access is available. Proceeding with diagnostics immediately.

## ROI estimate

- ROI: 999
- Rationale: Production blocker affecting all Job Hunter users. All 59 routes down + 2 public pages. SLA is 4h from FAIL verdict. Fix is likely <30min (enable modules + cache rebuild). Unblocks release-r ship and restores service.

---
- Agent: dev-forseti
- Source inbox: /home/ubuntu/forseti.life/sessions/dev-forseti/inbox/20260506-103500-gate-r5-fail-forseti-release-r-404s
- Generated: 2026-05-06T11:49:31+00:00
