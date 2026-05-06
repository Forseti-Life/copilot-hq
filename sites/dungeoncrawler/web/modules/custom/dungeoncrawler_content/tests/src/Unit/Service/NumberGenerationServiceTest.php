<?php

namespace Drupal\Tests\dungeoncrawler_content\Unit\Service;

use Drupal\Tests\UnitTestCase;
use Drupal\dungeoncrawler_content\Service\NumberGenerationService;

/**
 * Tests for NumberGenerationService.
 *
 * @group dungeoncrawler_content
 * @group dice
 * @coversDefaultClass \Drupal\dungeoncrawler_content\Service\NumberGenerationService
 */
class NumberGenerationServiceTest extends UnitTestCase {

  /**
   * Number generation service.
   *
   * @var \Drupal\dungeoncrawler_content\Service\NumberGenerationService
   */
  protected $service;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->service = new NumberGenerationService();
  }

  /**
   * Tests Pathfinder die ranges.
   *
   * @covers ::rollPathfinderDie
   */
  public function testRollPathfinderDieRange(): void {
    foreach (NumberGenerationService::PATHFINDER_DICE as $sides) {
      $roll = $this->service->rollPathfinderDie($sides);
      $this->assertGreaterThanOrEqual(1, $roll);
      $this->assertLessThanOrEqual($sides, $roll);
    }
  }

  /**
   * Tests percentile range.
   *
   * @covers ::rollPercentile
   */
  public function testRollPercentileRange(): void {
    $roll = $this->service->rollPercentile();
    $this->assertGreaterThanOrEqual(1, $roll);
    $this->assertLessThanOrEqual(100, $roll);
  }

  /**
   * Tests generic inclusive range rolling.
   *
   * @covers ::rollRange
   */
  public function testRollRange(): void {
    $roll = $this->service->rollRange(-1, 1);
    $this->assertGreaterThanOrEqual(-1, $roll);
    $this->assertLessThanOrEqual(1, $roll);
  }

  /**
   * Tests rolling multiple dice in generic range.
   *
   * @covers ::rollMultiple
   */
  public function testRollMultiple(): void {
    $rolls = $this->service->rollMultiple(20, 5);
    $this->assertCount(5, $rolls);
    foreach ($rolls as $roll) {
      $this->assertGreaterThanOrEqual(1, $roll);
      $this->assertLessThanOrEqual(20, $roll);
    }
  }

  /**
   * Tests rollExpression with basic NdX notation.
   *
   * @covers ::rollExpression
   */
  public function testRollExpressionBasicNdX(): void {
    $result = $this->service->rollExpression('2d6');
    
    $this->assertArrayHasKey('dice', $result);
    $this->assertArrayHasKey('total', $result);
    $this->assertArrayHasKey('error', $result);
    $this->assertNull($result['error'], 'Should not have error');
    $this->assertCount(2, $result['dice']);
    foreach ($result['dice'] as $roll) {
      $this->assertGreaterThanOrEqual(1, $roll);
      $this->assertLessThanOrEqual(6, $roll);
    }
    $this->assertSame(array_sum($result['dice']), $result['total']);
  }

  /**
   * Tests rollExpression with modifier notation.
   *
   * @covers ::rollExpression
   */
  public function testRollExpressionWithModifier(): void {
    $result = $this->service->rollExpression('1d20+5');
    
    $this->assertNull($result['error']);
    $this->assertCount(1, $result['dice']);
    $this->assertSame(5, $result['modifier']);
    $this->assertSame(array_sum($result['dice']) + 5, $result['total']);
  }

  /**
   * Tests rollExpression with d% (percentile).
   *
   * @covers ::rollExpression
   */
  public function testRollExpressionPercentile(): void {
    $result = $this->service->rollExpression('d%');
    
    $this->assertNull($result['error']);
    $this->assertCount(2, $result['dice']);
    $this->assertGreaterThanOrEqual(1, $result['total']);
    $this->assertLessThanOrEqual(100, $result['total']);
  }

  /**
   * Tests rollExpression with keep-highest modifier.
   *
   * @covers ::rollExpression
   */
  public function testRollExpressionKeepHighest(): void {
    $result = $this->service->rollExpression('4d6kh3');
    
    $this->assertNull($result['error']);
    $this->assertCount(4, $result['dice']);
    $this->assertCount(3, $result['kept']);
    // Kept should be the 3 highest values
    $sorted = $result['dice'];
    sort($sorted);
    $expected_kept = array_slice($sorted, -3);
    sort($result['kept']);
    sort($expected_kept);
    $this->assertSame($expected_kept, $result['kept']);
  }

  /**
   * Tests rollExpression with keep-lowest modifier.
   *
   * @covers ::rollExpression
   */
  public function testRollExpressionKeepLowest(): void {
    $result = $this->service->rollExpression('4d6kl3');
    
    $this->assertNull($result['error']);
    $this->assertCount(4, $result['dice']);
    $this->assertCount(3, $result['kept']);
    // Kept should be the 3 lowest values
    $sorted = $result['dice'];
    sort($sorted);
    $expected_kept = array_slice($sorted, 0, 3);
    sort($result['kept']);
    sort($expected_kept);
    $this->assertSame($expected_kept, $result['kept']);
  }

  /**
   * Tests notation parsing and totals.
   *
   * @covers ::rollNotation
   */
  public function testRollNotation(): void {
    $result = $this->service->rollNotation('2d6+3');

    $this->assertSame('2d6+3', $result['notation']);
    $this->assertSame(2, $result['count']);
    $this->assertSame(6, $result['sides']);
    $this->assertSame(3, $result['modifier']);
    $this->assertCount(2, $result['rolls']);
    $this->assertSame($result['subtotal'] + 3, $result['total']);
  }

  /**
   * Tests unsupported Pathfinder die rejection.
   *
   * @covers ::rollPathfinderDie
   */
  public function testRollPathfinderDieRejectsUnsupportedSides(): void {
    $this->expectException(\InvalidArgumentException::class);
    $this->service->rollPathfinderDie(30);
  }

  /**
   * Tests rollExpression rejects invalid dice count.
   *
   * @covers ::rollExpression
   */
  public function testRollExpressionRejectsZeroDiceCount(): void {
    $result = $this->service->rollExpression('0d6');
    $this->assertNotNull($result['error']);
    $this->assertStringContainsString('Dice count must be a positive integer', $result['error']);
  }

  /**
   * Tests rollExpression rejects negative dice count.
   *
   * @covers ::rollExpression
   */
  public function testRollExpressionRejectsNegativeDiceCount(): void {
    $result = $this->service->rollExpression('-1d6');
    $this->assertNotNull($result['error']);
    $this->assertStringContainsString('Dice count must be a positive integer', $result['error']);
  }

  /**
   * Tests rollExpression handles +0 modifier gracefully.
   *
   * @covers ::rollExpression
   */
  public function testRollExpressionZeroModifierHandled(): void {
    $result = $this->service->rollExpression('2d6+0');
    $this->assertNull($result['error']);
    $this->assertSame(0, $result['modifier']);
    $this->assertSame(array_sum($result['dice']), $result['total']);
  }

  /**
   * Tests rollExpression rejects invalid format.
   *
   * @covers ::rollExpression
   */
  public function testRollExpressionRejectsInvalidFormat(): void {
    $result = $this->service->rollExpression('invalid');
    $this->assertNotNull($result['error']);
    $this->assertStringContainsString('Invalid dice expression', $result['error']);
  }

  /**
   * Tests rollExpression rejects empty expression.
   *
   * @covers ::rollExpression
   */
  public function testRollExpressionRejectsEmptyExpression(): void {
    $result = $this->service->rollExpression('');
    $this->assertNotNull($result['error']);
  }

  /**
   * Tests rollExpression rejects keep count out of range.
   *
   * @covers ::rollExpression
   */
  public function testRollExpressionRejectsKeepOutOfRange(): void {
    $result = $this->service->rollExpression('4d6kh5');
    $this->assertNotNull($result['error']);
    $this->assertStringContainsString('Keep count', $result['error']);
  }

  /**
   * Tests invalid dice notation rejection.
   *
   * @covers ::rollNotation
   */
  public function testRollNotationRejectsInvalidFormat(): void {
    $this->expectException(\InvalidArgumentException::class);
    $this->service->rollNotation('not-a-roll');
  }

  /**
   * Tests invalid range arguments.
   *
   * @covers ::rollRange
   */
  public function testRollRangeRejectsInvalidBounds(): void {
    $this->expectException(\InvalidArgumentException::class);
    $this->service->rollRange(10, 1);
  }

}
