- Status: done
- Summary: BLOCK for `dc-cr-dwarf-heritage-ancient-blooded`. The data/catalog side is present, but the runtime reaction path is still incomplete. `CharacterManager` grants `call-on-ancient-blood`, yet `ReactionHandler` still only supports `attack_of_opportunity` and `shield_block`, so the magical-save reaction flow remains unreachable. Post-creation heritage lock and a dedicated heritage mutation route also remain absent.

## Evidence
- `src/Service/CharacterManager.php`
  - `ancient-blooded-dwarf` is present in `HERITAGES['Dwarf']`
  - granted ability: `call-on-ancient-blood`
  - trigger metadata: `saving_throw_before_roll_magical`
- `src/Service/ReactionHandler.php`
  - `checkForReactions()` only discovers Attack of Opportunity
  - `executeReaction()` switch only handles `attack_of_opportunity` and `shield_block`
  - any other reaction falls through to `Unknown reaction type`
- Routing review:
  - no dedicated heritage POST route present in routing files
- `src/Controller/CharacterApiController.php`
  - sets `status = 1` when `wizard_complete` is true
  - no guard found that prevents heritage mutation after wizard completion

## Test note
- The prescribed PHPUnit command is currently blocked by a pre-existing harness issue:
  - `Class "Drupal\Tests\BrowserTestBase" not found`
  - first failing load: `tests/src/Functional/CampaignStateAccessTest.php`
- This verdict therefore used code inspection and route/runtime surface review.

## Blockers
- Ancient-Blooded reaction runtime not implemented in `ReactionHandler`
- Heritage lock after character creation not enforced
- Dedicated heritage mutation route absent

## Next actions
- Downstream dev follow-up dispatched to `dev-dungeoncrawler`

## Result
- BLOCK
