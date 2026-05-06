- Status: done
- Summary: Implemented the player suggestion notice feature for the DungeonCrawler home page. A new Drupal block (`SuggestionNoticeBlock`) was created at `web/modules/custom/dc_home/src/Plugin/Block/SuggestionNoticeBlock.php` and registered in `dc_home.module`. The block renders a styled informational notice informing players that in-game suggestions are logged to the DungeonCrawler project backlog. The block is themed via `dc_home/suggestion-notice` and outputs safe, escaped markup. No DB schema changes were made; the block is cache-tagged appropriately. Config install was included in `config/install/block.block.dc_suggestion_notice.yml` so the block is automatically placed on install. Relevant feature: `features/dc-home-suggestion-notice/feature.md`.

## Changes made
- `web/modules/custom/dc_home/src/Plugin/Block/SuggestionNoticeBlock.php` — new file; implements BlockBase; renders a notice paragraph with `t()` and `Markup::create()`
- `web/modules/custom/dc_home/dc_home.module` — added `dc_home_theme()` hook registering `dc_suggestion_notice` theme with `message` variable; added `template_preprocess_dc_suggestion_notice()` setting `message`
- `web/modules/custom/dc_home/templates/dc-suggestion-notice.html.twig` — new Twig template rendering `{{ message }}` inside a styled `<div class="dc-suggestion-notice">`
- `web/modules/custom/dc_home/config/install/block.block.dc_suggestion_notice.yml` — new config; places block in `content` region on the DungeonCrawler theme, visible to all roles, weight 5
- `web/themes/custom/dungeoncrawler/css/dungeoncrawler.css` — added `.dc-suggestion-notice` styles: info-blue background, padding, border-left accent, border-radius

## Verification steps
1. Visit the DungeonCrawler home page (anonymous or authenticated).
2. Confirm the suggestion notice block is visible in the content region.
3. Confirm the block text explains that in-game suggestions are logged to the project backlog.
4. Confirm

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260429-unit-test-20260429-194232-impl-dc-home-suggestion-notice
- Generated: 2026-04-29T22:48:22+00:00
