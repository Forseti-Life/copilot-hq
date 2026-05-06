<?php

namespace Drupal\Tests\dungeoncrawler_content\Unit\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Query\SelectInterface;
use Drupal\Core\Database\StatementInterface;
use Drupal\dungeoncrawler_content\Service\CombatCalculator;
use Drupal\dungeoncrawler_content\Service\CombatEncounterStore;
use Drupal\dungeoncrawler_content\Service\ConditionManager;
use Drupal\dungeoncrawler_content\Service\HPManager;
use Drupal\dungeoncrawler_content\Service\NumberGenerationService;
use Drupal\dungeoncrawler_content\Service\ReactionHandler;
use Drupal\Tests\UnitTestCase;

/**
 * Tests Ancient-Blooded runtime reaction handling.
 *
 * @group dungeoncrawler_content
 * @group encounter
 * @coversDefaultClass \Drupal\dungeoncrawler_content\Service\ReactionHandler
 */
class ReactionHandlerAncientBloodTest extends UnitTestCase {

  /**
   * A magical saving throw exposes Call on Ancient Blood for eligible dwarves.
   *
   * @covers ::checkForSavingThrowReaction
   */
  public function testCheckForSavingThrowReactionFindsAncientBloodOpportunity(): void {
    $handler = $this->buildHandler($this->buildParticipant());

    $result = $handler->checkForSavingThrowReaction(42, 7, [
      'type' => 'saving_throw',
      'traits' => ['magical'],
    ]);

    $this->assertNotNull($result);
    $this->assertSame('call-on-ancient-blood', $result['reaction_type']);
    $this->assertSame(1, $result['effect']['value']);
  }

  /**
   * Executing the reaction persists the temporary save bonus and spends it.
   *
   * @covers ::executeReaction
   */
  public function testExecuteReactionAppliesAncientBloodBonus(): void {
    $store = $this->createMock(CombatEncounterStore::class);
    $updates = [];
    $store->method('updateParticipant')
      ->willReturnCallback(function (int $participant_id, array $fields) use (&$updates): bool {
        $updates[] = ['participant_id' => $participant_id, 'fields' => $fields];
        return TRUE;
      });
    $store->method('logAction')->willReturn(1);

    $handler = $this->buildHandler($this->buildParticipant(), $store);

    $result = $handler->executeReaction(7, 'call-on-ancient-blood', [
      'trigger' => 'saving_throw_before_roll_magical',
      'target_id' => 7,
    ], 42);

    $this->assertSame('applied', $result['status']);
    $this->assertSame(1, $result['bonus']);
    $this->assertCount(2, $updates);
    $this->assertSame(['reaction_available' => 0], $updates[0]['fields']);
    $entity_ref = json_decode($updates[1]['fields']['entity_ref'], TRUE);
    $this->assertSame(
      'saving_throw_before_roll_magical',
      $entity_ref['active_reaction_bonuses']['call-on-ancient-blood']['trigger']
    );
    $this->assertSame(1, $entity_ref['saving_throw_circumstance_bonus']);
  }

  /**
   * Build a lightweight handler with a fixed participant row.
   */
  private function buildHandler(array $participant, ?CombatEncounterStore $store = NULL): ReactionHandler {
    $statement = $this->createMock(StatementInterface::class);
    $statement->method('fetchAssoc')->willReturn($participant);

    $select = $this->createMock(SelectInterface::class);
    $select->method('fields')->willReturnSelf();
    $select->method('condition')->willReturnSelf();
    $select->method('execute')->willReturn($statement);

    $database = $this->createMock(Connection::class);
    $database->method('select')->willReturn($select);

    return new ReactionHandler(
      $database,
      $this->createMock(CombatCalculator::class),
      $this->createMock(HPManager::class),
      $store ?? $this->createMock(CombatEncounterStore::class),
      $this->createMock(NumberGenerationService::class),
      $this->createMock(ConditionManager::class),
    );
  }

  /**
   * Canonical participant payload for Ancient-Blooded tests.
   */
  private function buildParticipant(): array {
    return [
      'id' => 7,
      'encounter_id' => 42,
      'name' => 'Balin',
      'reaction_available' => 1,
      'entity_ref' => json_encode([
        'character' => [
          'ancestry' => [
            'heritage' => 'ancient-blooded-dwarf',
          ],
        ],
        'reactions' => [
          ['id' => 'call-on-ancient-blood'],
        ],
      ]),
    ];
  }

}
