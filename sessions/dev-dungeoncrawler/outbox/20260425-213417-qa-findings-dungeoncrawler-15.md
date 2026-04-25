- Status: done
- Summary: Investigated all 15 reported non-ACL route failures and confirmed they were false positives, not live dungeoncrawler regressions. On production, `copilot_agent_tracker` is not enabled (`\Drupal::moduleHandler()->getModuleList()` excludes it) and Drupal does not register `copilot_agent_tracker.langgraph_overview` (`RouteNotFoundException`). The failures came from `scripts/drupal-custom-routes-audit.py` scanning disabled module `.routing.yml` files on disk. Fixed the audit script to query enabled modules via collocated Drush and only probe routes for enabled custom modules. Validation: `python3 -m py_compile scripts/drupal-custom-routes-audit.py` passed, and a fresh route audit against `/var/www/html/dungeoncrawler/web` produced 308 checks across `ai_conversation`, `dungeoncrawler_content`, and `dungeoncrawler_tester` only — `copilot_agent_tracker` no longer appears.

## Next actions
- QA notified for retest: re-run the dungeoncrawler route/site audit. Expected result: the 15 `copilot_agent_tracker` 404s disappear from the non-ACL failure bucket.
- Targeted retest command: `python3 scripts/drupal-custom-routes-audit.py --drupal-web-root /var/www/html/dungeoncrawler/web --base-url https://dungeoncrawler.forseti.life --timeout-sec 10 --max-seconds 150 --path-regex '^(?!.*\\{_format\\})(?!.*\\{_content_type_format\\}).*' --max-routes 800 --out tmp/dungeoncrawler-route-audit-validation.json`

## Files changed
- `scripts/drupal-custom-routes-audit.py`

## Evidence
- Production module check: `cd /var/www/html/dungeoncrawler && ./vendor/bin/drush pml --status=enabled --type=module --no-core | grep -E 'copilot_agent_tracker|ai_conversation|dungeoncrawler'`
- Production route check: `cd /var/www/html/dungeoncrawler && ./vendor/bin/drush php:eval 'try { \\Drupal::service("router.route_provider")->getRouteByName("copilot_agent_tracker.langgraph_overview"); echo "route_exists=true\\n"; } catch (Exception $e) { echo "route_exists=false\\n"; echo get_class($e) . ": " . $e->getMessage() . "\\n"; }'`
- Validation result: `checks 308`, `modules ai_conversation,dungeoncrawler_content,dungeoncrawler_tester`, `copilot_agent_tracker_present False`

## Blockers
- None for these 15 findings. The only unrelated environment issue observed was `/home/ubuntu/forseti.life/sites/dungeoncrawler/vendor/bin/drush` failing bootstrap in the dev path; production Drush under `/var/www/html/dungeoncrawler` worked and was used for live verification.
