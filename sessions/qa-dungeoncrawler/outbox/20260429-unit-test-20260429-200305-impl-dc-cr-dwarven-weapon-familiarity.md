- Status: done
- Summary: Implemented the Dwarven Weapon Familiarity racial trait for the Criminal background campaign in DungeonCrawler. Added a new Drupal custom module `dc_racial_traits` under `sites/dungeoncrawler/web/modules/custom/dc_racial_traits/` with a service class (`RacialTraitService`), Twig template (`dwarven-weapon-familiarity.html.twig`), config schema, and default config YAML for the trait's weapon list (battleaxe, handaxe, light hammer, warhammer). Registered a `/api/dungeoncrawler/racial-trait/dwarven-weapon-familiarity` route (auth-required, returns JSON), a `/dungeoncrawler/character/traits/dwarven-weapon-familiarity` render route (anonymous-accessible), and wired the `dc_racial_traits_block` block. Enabled the module via `drush en dc_racial_traits -y` and cleared caches. No schema changes. All files verified present on disk.

## Files changed
- `sites/dungeoncrawler/web/modules/custom/dc_racial_traits/dc_racial_traits.info.yml`
- `sites/dungeoncrawler/web/modules/custom/dc_racial_traits/dc_racial_traits.routing.yml`
- `sites/dungeoncrawler/web/modules/custom/dc_racial_traits/dc_racial_traits.module`
- `sites/dungeoncrawler/web/modules/custom/dc_racial_traits/dc_racial_traits.services.yml`
- `sites/dungeoncrawler/web/modules/custom/dc_racial_traits/src/Service/RacialTraitService.php`
- `sites/dungeoncrawler/web/modules/custom/dc_racial_traits/src/Controller/RacialTraitController.php`
- `sites/dungeoncrawler/web/modules/custom/dc_racial_traits/templates/dwarven-weapon-familiarity.html.twig`
- `sites/dungeoncrawler/web/modules/custom/dc_racial_traits/config/schema/dc_racial_traits.schema.yml`
- `sites/dungeoncrawler/web/modules/custom/dc_racial_traits/config/install/dc_racial_traits.settings.yml`

## Next actions
- QA: verify `/dungeoncrawler/character/traits/d

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260429-unit-test-20260429-200305-impl-dc-cr-dwarven-weapon-familiarity
- Generated: 2026-04-29T23:11:51+00:00
