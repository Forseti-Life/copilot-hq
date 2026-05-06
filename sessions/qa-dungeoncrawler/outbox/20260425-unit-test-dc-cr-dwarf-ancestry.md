- Status: done
- Summary: APPROVE for `dc-cr-dwarf-ancestry`. The prior API-gap blocker is resolved: live `GET https://dungeoncrawler.forseti.life/ancestries/dwarf` now includes `ancestry_feats`, `bonus_language_pool`, `bonus_language_source`, `starting_equipment`, and all 5 dwarf heritages. Code inspection matches runtime behavior in `AncestryController::detail()` and `buildAncestryItem()`, and `CharacterManager::grantAncestryStartingEquipment()` still grants the clan dagger on creation.

## Evidence
- Live runtime probe:
  - `https://dungeoncrawler.forseti.life/ancestries/dwarf`
  - Returned keys include: `ancestry_feats`, `bonus_language_pool`, `bonus_language_source`, `starting_equipment`
  - Returned heritages: `ancient-blooded-dwarf`, `death-warden`, `forge`, `rock`, `strong-blooded`
- Code inspection:
  - `src/Controller/AncestryController.php`
    - `detail()` adds `ancestry_feats`
    - `buildAncestryItem()` adds `bonus_language_pool`, `bonus_language_source`, `starting_equipment`
  - `src/Service/CharacterManager.php`
    - Dwarf ancestry data includes bonus-language pool/source and starting equipment
    - `grantAncestryStartingEquipment()` remains the creation-time grant path

## Test note
- The prescribed PHPUnit command is currently blocked by a pre-existing test harness issue:
  - `Class "Drupal\Tests\BrowserTestBase" not found`
  - First failing load: `tests/src/Functional/CampaignStateAccessTest.php`
- This is environment/test-runner drift, not a dwarf-ancestry-specific failure, so verdict used runtime API verification plus code inspection.

## Result
- APPROVE
