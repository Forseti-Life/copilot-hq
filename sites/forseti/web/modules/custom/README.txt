Forseti custom modules live here as a deployment symlink farm.

Do not assume the code is edited in this directory.

Canonical source rule:
- /var/www/html/forseti/web/modules/custom -> /home/ubuntu/forseti.life/sites/forseti/web/modules/custom
- Entries inside this directory are usually symlinks to the real source tree
- Some modules are standalone repos under /home/ubuntu/forseti.life/<repo>
- Some modules are sourced from /home/ubuntu/forseti.life/forseti-job-hunter/web/modules/custom/<module>
- To remove that inconsistency, use the normalized namespace:

  /home/ubuntu/forseti.life/module-sources/<module>

That path is now the canonical operator/developer entry point for every custom module.

Use this command to resolve the real source location:

  bash /home/ubuntu/forseti.life/scripts/forseti-module-map.sh

Or for a single module:

  bash /home/ubuntu/forseti.life/scripts/forseti-module-map.sh job_hunter

To rebuild the normalized namespace:

  bash /home/ubuntu/forseti.life/scripts/refresh-forseti-module-sources.sh

Examples from this environment:
- /home/ubuntu/forseti.life/module-sources/job_hunter -> /home/ubuntu/forseti.life/forseti-job-hunter/web/modules/custom/job_hunter
- /home/ubuntu/forseti.life/module-sources/amisafe -> /home/ubuntu/forseti.life/forseti-job-hunter/web/modules/custom/amisafe
- /home/ubuntu/forseti.life/module-sources/forseti_games -> /home/ubuntu/forseti.life/forseti-job-hunter/web/modules/custom/forseti_games
- /home/ubuntu/forseti.life/module-sources/forseti_content -> /home/ubuntu/forseti.life/forseti-content

If /roadmap or another route looks wrong, resolve the serving module with the map above before editing anything.
