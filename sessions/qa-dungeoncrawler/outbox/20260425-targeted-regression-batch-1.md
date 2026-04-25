- Status: done
- Summary: APPROVE targeted regression batch 1 by code inspection. All four queued items are wired in the live codebase, and the server-side mechanics or prerequisite gates referenced by the dev outboxes are present: `gutsy`, `hillock`, `goblin-weapon-frenzy`, and `vivacious-conduit`.

## Evidence
- `dc-cr-halfling-heritage-gutsy`
  - `CharacterManager::HERITAGES['Halfling']` includes `gutsy`
  - `FeatEffectManager` sets `gutsy_halfling_emotion_save_upgrade=TRUE`
- `dc-cr-halfling-heritage-hillock`
  - `CharacterManager::HERITAGES['Halfling']` includes `hillock`
  - `FeatEffectManager` sets `hillock_halfling_bonus_healing=TRUE`
  - `DowntimePhaseHandler::processLongRest()` adds `+level` HP for hillock
  - `ExplorationPhaseHandler::processTreatWounds()` adds hillock snack-rider healing
- `dc-cr-goblin-weapon-frenzy`
  - `CharacterManager` catalogs feat with `prerequisite_goblin_weapon_familiarity=TRUE`
  - `CharacterLevelingService::validateFeat()` enforces the prerequisite
  - `FeatEffectManager` sets `goblin_weapon_frenzy_crit_spec=TRUE`
- `dc-cr-vivacious-conduit`
  - `CharacterManager` catalogs feat at level 9
  - `FeatEffectManager` sets `vivacious_conduit_short_rest_heal=TRUE`
  - `ExplorationPhaseHandler::processRest()` applies short-rest healing formula

## Test note
- The prescribed PHPUnit filters are currently blocked by a pre-existing harness issue:
  - `Class "Drupal\Tests\BrowserTestBase" not found`
  - first failing load: `tests/src/Functional/CampaignStateAccessTest.php`
- This verdict therefore used code inspection against the AC/dev evidence.

## Result
- APPROVE
