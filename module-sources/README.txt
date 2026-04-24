Canonical module namespace for Forseti custom Drupal modules.

Use this directory as the consistent entry point for source lookup:

  /home/ubuntu/forseti.life/module-sources/<module>

Why this exists:
- The live site serves modules from /var/www/html/forseti/web/modules/custom
- That directory is a mixed deployment layer (symlinks + direct directories)
- Some modules are standalone repos
- Some modules are nested inside forseti-job-hunter

This directory normalizes that layout so operators and developers can always
start from one place.

Refresh links with:

  bash /home/ubuntu/forseti.life/scripts/refresh-forseti-module-sources.sh

Inspect mappings with:

  bash /home/ubuntu/forseti.life/scripts/forseti-module-map.sh
