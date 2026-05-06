- Status: done
- Summary: Implemented the remaining `dc-cr-languages` gaps in the authoritative Dungeoncrawler site tree. The language catalog is now normalized and publicly accessible, the character save/load API now preserves and validates `languages[]`, and the step-5 character creation wizard now requires valid Intelligence-based bonus language selections before submission.

## Code changes
- `sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/src/Service/LanguageService.php`
  - Added canonical language catalog and alias normalization.
  - Enforced ancestry-aware bonus language slot rules.
  - Preserved existing `languages[]` when later saves omit the field.
- `sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/src/Controller/LanguagesController.php`
  - Switched to the canonical language catalog service.
- `sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/src/Controller/CharacterApiController.php`
  - Preserved stored language data on updates.
  - Validated `languages[]` via the language service before save.
- `sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/js/character-creation.js`
  - Added ancestry-aware language rules, bonus-language selection UI behavior, and step-5 validation.
- `sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/templates/character-creation-wizard.html.twig`
  - Added the language selection panel to the live character creation wizard.
- `sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content/dungeoncrawler_content.routing.yml`
  - Marked `/languages` and `/api/languages` as public GET routes.

## Validation
- `php -l web/modules/custom/dungeoncrawler_content/src/Service/LanguageService.php`
- `php -l web/modules/custom/dungeoncrawler_content/src/Controller/LanguagesController.php`
- `php -l web/modules/custom/dungeoncrawler_content/src/Controller/CharacterApiController.php`
- `node --check web/modules/custom/dungeoncrawler_content/js/character-creation.js`
- `vendor/bin/phpunit web/modules/custom/dungeoncrawler_content/tests/src/Unit/Service/LanguageServiceTest.php`

---
- Agent: ceo-copilot-2
- Generated: 2026-04-28T13:39:00+00:00
