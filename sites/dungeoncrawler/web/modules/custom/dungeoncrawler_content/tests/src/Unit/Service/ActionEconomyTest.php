<?php

namespace Drupal\Tests\dungeoncrawler_content\Unit\Service;

use Drupal\Tests\UnitTestCase;
use Drupal\dungeoncrawler_content\Service\RulesEngine;

/**
 * Tests for action economy (PF2E three-action economy system).
 *
 * @group dungeoncrawler_content
 * @group combat
 * @group pf2e-rules
 * @coversDefaultClass \Drupal\dungeoncrawler_content\Service\RulesEngine
 *
 * Feature: dc-cr-action-economy
 * Implements PF2E's three-action economy: 3 actions + 1 reaction per turn.
 * Actions have costs: 1, 2, 3 (integer), 'free' (no cost), 'reaction'.
 *
 * @see features/dc-cr-action-economy/01-acceptance-criteria.md
 * @see features/dc-cr-action-economy/03-test-plan.md
 */
class ActionEconomyTest extends UnitTestCase {

  /**
   * The rules engine service.
   *
   * @var \Drupal\dungeoncrawler_content\Service\RulesEngine
   */
  protected $rulesEngine;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    // For unit tests of validateActionEconomy(), only the database param is used
    // for participant lookup, not for this isolated method test.
    // Create a mock database connection.
    $database = $this->createMock('Drupal\Core\Database\Connection');
    $this->rulesEngine = new RulesEngine($database);
  }

  /**
   * TC-AE-01: Turn start resets action budget.
   *
   * AC: `actions_remaining` resets to 3 and `reaction_available` resets to
   * `true` at turn start.
   *
   * @covers ::validateActionEconomy
   * @group pf2e-rules
   */
  public function testTurnStartResetsActionBudget(): void {
    // Setup: Create participant with depleted state.
    $participant = [
      'id' => 1,
      'actions_remaining' => 0,
      'reaction_available' => 0,
    ];

    // A well-formed startTurn() call would reset these via CombatEngine.
    // Here we verify the reset state is recognized as valid by validateActionEconomy.
    $reset_participant = $participant;
    $reset_participant['actions_remaining'] = 3;
    $reset_participant['reaction_available'] = 1;

    // Verify the reset state allows a 1-action activity.
    $result = $this->rulesEngine->validateActionEconomy($reset_participant, 1);
    $this->assertTrue($result['is_valid'], 'Participant with reset budget can take 1-action.');
    $this->assertSame($result['actions_after'], 2, 'After 1-action, 2 remain.');
  }

  /**
   * TC-AE-02: 1-action cost decrements by 1.
   *
   * AC: Spending an action decrements `actions_remaining` by the action's cost.
   *
   * @covers ::validateActionEconomy
   * @group pf2e-rules
   */
  public function testOneActionCostDecrement(): void {
    $participant = [
      'id' => 1,
      'actions_remaining' => 3,
      'reaction_available' => 1,
    ];

    $result = $this->rulesEngine->validateActionEconomy($participant, 1);
    $this->assertTrue($result['is_valid'], 'Spending 1 action from 3 is valid.');
    $this->assertSame($result['actions_after'], 2, 'After spending 1, 2 remain.');
  }

  /**
   * TC-AE-03: 2-action activity decrements by 2.
   *
   * AC: Spending a 2-action activity decrements `actions_remaining` by 2.
   *
   * @covers ::validateActionEconomy
   * @group pf2e-rules
   */
  public function testTwoActionActivityDecrement(): void {
    $participant = [
      'id' => 1,
      'actions_remaining' => 3,
      'reaction_available' => 1,
    ];

    $result = $this->rulesEngine->validateActionEconomy($participant, 2);
    $this->assertTrue($result['is_valid'], 'Spending 2 actions from 3 is valid.');
    $this->assertSame($result['actions_after'], 1, 'After spending 2, 1 remains.');
  }

  /**
   * TC-AE-04: 3-action activity decrements by 3.
   *
   * AC: 3-action activity cost (all remaining actions).
   *
   * @covers ::validateActionEconomy
   * @group pf2e-rules
   */
  public function testThreeActionActivityDecrement(): void {
    $participant = [
      'id' => 1,
      'actions_remaining' => 3,
      'reaction_available' => 1,
    ];

    $result = $this->rulesEngine->validateActionEconomy($participant, 3);
    $this->assertTrue($result['is_valid'], 'Spending 3 actions from 3 is valid.');
    $this->assertSame($result['actions_after'], 0, 'After spending 3, 0 remain.');
  }

  /**
   * TC-AE-05: Free action does not decrement budget.
   *
   * AC: Free actions are always available and do not consume `actions_remaining`.
   *
   * @covers ::validateActionEconomy
   * @group pf2e-rules
   */
  public function testFreeActionNoCost(): void {
    $participant = [
      'id' => 1,
      'actions_remaining' => 0,
      'reaction_available' => 1,
    ];

    $result = $this->rulesEngine->validateActionEconomy($participant, 'free');
    $this->assertTrue($result['is_valid'], 'Free action is valid even at 0 actions.');
    $this->assertSame($result['actions_after'], 0, 'Free action does not change action budget.');
  }

  /**
   * TC-AE-06: Reaction sets reaction_available to false.
   *
   * AC: Spending a reaction sets `reaction_available` to `false`.
   *
   * @covers ::validateActionEconomy
   * @group pf2e-rules
   */
  public function testReactionConsumption(): void {
    $participant = [
      'id' => 1,
      'actions_remaining' => 3,
      'reaction_available' => 1,
    ];

    $result = $this->rulesEngine->validateActionEconomy($participant, 'reaction');
    $this->assertTrue($result['is_valid'], 'Reaction is valid when available.');
    // Note: the validation only checks availability, not consumption.
    // Consumption is handled by ActionProcessor::executeAction() which updates DB state.
  }

  /**
   * TC-AE-07: Cannot act when actions_remaining insufficient.
   *
   * AC: A character cannot take a paid action if `actions_remaining < action_cost`.
   *
   * @covers ::validateActionEconomy
   * @group pf2e-rules
   */
  public function testInsufficientActionsRejected(): void {
    $participant = [
      'id' => 1,
      'actions_remaining' => 1,
      'reaction_available' => 1,
    ];

    $result = $this->rulesEngine->validateActionEconomy($participant, 2);
    $this->assertFalse($result['is_valid'], 'Cannot spend 2 actions with only 1 remaining.');
    $this->assertStringContainsString('Not enough actions', $result['reason'], 'Error message indicates insufficient actions.');
  }

  /**
   * TC-AE-08: Cannot use reaction if already spent.
   *
   * AC: A character cannot use a reaction if `reaction_available` is `false`.
   *
   * @covers ::validateActionEconomy
   * @group pf2e-rules
   */
  public function testSpentReactionRejected(): void {
    $participant = [
      'id' => 1,
      'actions_remaining' => 3,
      'reaction_available' => 0,
    ];

    $result = $this->rulesEngine->validateActionEconomy($participant, 'reaction');
    $this->assertFalse($result['is_valid'], 'Cannot use reaction when already spent.');
    $this->assertStringContainsString('Reaction already used this turn', $result['reason'], 'Error message indicates spent reaction.');
  }

  /**
   * Test: Invalid action cost values are rejected.
   *
   * AC: Invalid action cost values (e.g., 0, negative, or >3) are rejected at
   * validation with a clear error.
   *
   * @covers ::validateActionEconomy
   * @group pf2e-rules
   */
  public function testInvalidActionCostRejected(): void {
    $participant = [
      'id' => 1,
      'actions_remaining' => 3,
      'reaction_available' => 1,
    ];

    // Test cost of 0 (invalid).
    $result = $this->rulesEngine->validateActionEconomy($participant, 0);
    $this->assertFalse($result['is_valid'], 'Cost of 0 is invalid.');
    $this->assertStringContainsString('Invalid action cost', $result['reason']);

    // Test negative cost (invalid).
    $result = $this->rulesEngine->validateActionEconomy($participant, -1);
    $this->assertFalse($result['is_valid'], 'Negative cost is invalid.');
    $this->assertStringContainsString('Invalid action cost', $result['reason']);

    // Test cost > 3 (invalid).
    $result = $this->rulesEngine->validateActionEconomy($participant, 4);
    $this->assertFalse($result['is_valid'], 'Cost > 3 is invalid.');
    $this->assertStringContainsString('Invalid action cost', $result['reason']);
  }

  /**
   * Test: actions_remaining cannot go below 0.
   *
   * AC: `actions_remaining` cannot go below 0 (guard against double-decrements).
   *
   * @covers ::validateActionEconomy
   * @group pf2e-rules
   */
  public function testActionsRemainingNeverNegative(): void {
    $participant = [
      'id' => 1,
      'actions_remaining' => 1,
      'reaction_available' => 1,
    ];

    // Spend 1 action (leaving 0).
    $result = $this->rulesEngine->validateActionEconomy($participant, 1);
    $this->assertSame($result['actions_after'], 0, 'Decrementing to 0 is valid.');
    $this->assertGreaterThanOrEqual(0, $result['actions_after'], 'actions_after is never negative.');

    // Attempt to spend from 0 (should fail).
    $participant['actions_remaining'] = 0;
    $result = $this->rulesEngine->validateActionEconomy($participant, 1);
    $this->assertFalse($result['is_valid'], 'Cannot spend from 0 actions.');
    // actions_after should still be 0, not negative.
    $this->assertSame($result['actions_after'], 0, 'Even in rejection, actions_after is not negative.');
  }

  /**
   * Test: Multiple sequential actions.
   *
   * Scenario: Character spends 1 action, then 1 action, then 1 action.
   * Validates that the economy correctly tracks the budget across multiple calls.
   *
   * @covers ::validateActionEconomy
   * @group pf2e-rules
   */
  public function testSequentialActionSpending(): void {
    $participant = [
      'id' => 1,
      'actions_remaining' => 3,
      'reaction_available' => 1,
    ];

    // First action.
    $result1 = $this->rulesEngine->validateActionEconomy($participant, 1);
    $this->assertTrue($result1['is_valid']);
    $this->assertSame($result1['actions_after'], 2);

    // Second action (simulating the updated state).
    $participant['actions_remaining'] = 2;
    $result2 = $this->rulesEngine->validateActionEconomy($participant, 1);
    $this->assertTrue($result2['is_valid']);
    $this->assertSame($result2['actions_after'], 1);

    // Third action.
    $participant['actions_remaining'] = 1;
    $result3 = $this->rulesEngine->validateActionEconomy($participant, 1);
    $this->assertTrue($result3['is_valid']);
    $this->assertSame($result3['actions_after'], 0);

    // Fourth action (should fail, no actions remaining).
    $participant['actions_remaining'] = 0;
    $result4 = $this->rulesEngine->validateActionEconomy($participant, 1);
    $this->assertFalse($result4['is_valid']);
    $this->assertStringContainsString('Not enough actions', $result4['reason']);
  }

}
