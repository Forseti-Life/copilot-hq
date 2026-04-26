<?php

namespace Drupal\Tests\dungeoncrawler_content\Functional\Controller;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests DiceRollController functionality.
 *
 * @group dungeoncrawler_content
 * @group controller
 * @group api
 * @group dice
 */
class DiceRollControllerTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['dungeoncrawler_content'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Tests POST /dice/roll with basic NdX expression.
   */
  public function testDiceRollEndpointBasicExpression(): void {
    $user = $this->drupalCreateUser(['access dungeoncrawler']);
    $this->drupalLogin($user);

    $payload = json_encode([
      'expression' => '2d6',
    ]);

    $this->getSession()->getDriver()->getClient()->request(
      'POST',
      $this->buildUrl('/dice/roll'),
      [],
      [],
      ['CONTENT_TYPE' => 'application/json'],
      $payload
    );

    $this->assertSession()->statusCodeEquals(200);
    $response = json_decode($this->getSession()->getPage()->getContent(), TRUE);
    
    $this->assertIsArray($response);
    $this->assertTrue($response['success']);
    $this->assertArrayHasKey('dice', $response);
    $this->assertArrayHasKey('modifier', $response);
    $this->assertArrayHasKey('total', $response);
    $this->assertIsArray($response['dice']);
    $this->assertCount(2, $response['dice']);
    $this->assertSame(0, $response['modifier']);
    $this->assertSame(array_sum($response['dice']), $response['total']);
  }

  /**
   * Tests POST /dice/roll with modifier.
   */
  public function testDiceRollEndpointWithModifier(): void {
    $user = $this->drupalCreateUser(['access dungeoncrawler']);
    $this->drupalLogin($user);

    $payload = json_encode([
      'expression' => '1d20+5',
    ]);

    $this->getSession()->getDriver()->getClient()->request(
      'POST',
      $this->buildUrl('/dice/roll'),
      [],
      [],
      ['CONTENT_TYPE' => 'application/json'],
      $payload
    );

    $this->assertSession()->statusCodeEquals(200);
    $response = json_decode($this->getSession()->getPage()->getContent(), TRUE);
    
    $this->assertTrue($response['success']);
    $this->assertCount(1, $response['dice']);
    $this->assertSame(5, $response['modifier']);
    $this->assertSame(array_sum($response['dice']) + 5, $response['total']);
  }

  /**
   * Tests POST /dice/roll with d% (percentile).
   */
  public function testDiceRollEndpointPercentile(): void {
    $user = $this->drupalCreateUser(['access dungeoncrawler']);
    $this->drupalLogin($user);

    $payload = json_encode([
      'expression' => 'd%',
    ]);

    $this->getSession()->getDriver()->getClient()->request(
      'POST',
      $this->buildUrl('/dice/roll'),
      [],
      [],
      ['CONTENT_TYPE' => 'application/json'],
      $payload
    );

    $this->assertSession()->statusCodeEquals(200);
    $response = json_decode($this->getSession()->getPage()->getContent(), TRUE);
    
    $this->assertTrue($response['success']);
    $this->assertCount(2, $response['dice']);
    $this->assertGreaterThanOrEqual(1, $response['total']);
    $this->assertLessThanOrEqual(100, $response['total']);
  }

  /**
   * Tests POST /dice/roll with keep-highest modifier.
   */
  public function testDiceRollEndpointKeepHighest(): void {
    $user = $this->drupalCreateUser(['access dungeoncrawler']);
    $this->drupalLogin($user);

    $payload = json_encode([
      'expression' => '4d6kh3',
    ]);

    $this->getSession()->getDriver()->getClient()->request(
      'POST',
      $this->buildUrl('/dice/roll'),
      [],
      [],
      ['CONTENT_TYPE' => 'application/json'],
      $payload
    );

    $this->assertSession()->statusCodeEquals(200);
    $response = json_decode($this->getSession()->getPage()->getContent(), TRUE);
    
    $this->assertTrue($response['success']);
    $this->assertCount(4, $response['dice']);
    $this->assertArrayHasKey('kept', $response);
    $this->assertCount(3, $response['kept']);
  }

  /**
   * Tests POST /dice/roll with keep-lowest modifier.
   */
  public function testDiceRollEndpointKeepLowest(): void {
    $user = $this->drupalCreateUser(['access dungeoncrawler']);
    $this->drupalLogin($user);

    $payload = json_encode([
      'expression' => '4d6kl3',
    ]);

    $this->getSession()->getDriver()->getClient()->request(
      'POST',
      $this->buildUrl('/dice/roll'),
      [],
      [],
      ['CONTENT_TYPE' => 'application/json'],
      $payload
    );

    $this->assertSession()->statusCodeEquals(200);
    $response = json_decode($this->getSession()->getPage()->getContent(), TRUE);
    
    $this->assertTrue($response['success']);
    $this->assertCount(4, $response['dice']);
    $this->assertArrayHasKey('kept', $response);
    $this->assertCount(3, $response['kept']);
  }

  /**
   * Tests POST /dice/roll with invalid expression returns 400.
   */
  public function testDiceRollEndpointInvalidExpressionReturns400(): void {
    $user = $this->drupalCreateUser(['access dungeoncrawler']);
    $this->drupalLogin($user);

    $payload = json_encode([
      'expression' => 'invalid',
    ]);

    $this->getSession()->getDriver()->getClient()->request(
      'POST',
      $this->buildUrl('/dice/roll'),
      [],
      [],
      ['CONTENT_TYPE' => 'application/json'],
      $payload
    );

    $this->assertSession()->statusCodeEquals(400);
    $response = json_decode($this->getSession()->getPage()->getContent(), TRUE);
    
    $this->assertFalse($response['success']);
    $this->assertArrayHasKey('error', $response);
    $this->assertStringContainsString('Invalid dice expression', $response['error']);
  }

  /**
   * Tests POST /dice/roll with missing expression returns 400.
   */
  public function testDiceRollEndpointMissingExpressionReturns400(): void {
    $user = $this->drupalCreateUser(['access dungeoncrawler']);
    $this->drupalLogin($user);

    $payload = json_encode([]);

    $this->getSession()->getDriver()->getClient()->request(
      'POST',
      $this->buildUrl('/dice/roll'),
      [],
      [],
      ['CONTENT_TYPE' => 'application/json'],
      $payload
    );

    $this->assertSession()->statusCodeEquals(400);
    $response = json_decode($this->getSession()->getPage()->getContent(), TRUE);
    
    $this->assertFalse($response['success']);
    $this->assertArrayHasKey('error', $response);
    $this->assertStringContainsString('Missing required field: expression', $response['error']);
  }

  /**
   * Tests POST /dice/roll with invalid JSON returns 400.
   */
  public function testDiceRollEndpointInvalidJsonReturns400(): void {
    $user = $this->drupalCreateUser(['access dungeoncrawler']);
    $this->drupalLogin($user);

    $this->getSession()->getDriver()->getClient()->request(
      'POST',
      $this->buildUrl('/dice/roll'),
      [],
      [],
      ['CONTENT_TYPE' => 'application/json'],
      'not valid json'
    );

    $this->assertSession()->statusCodeEquals(400);
    $response = json_decode($this->getSession()->getPage()->getContent(), TRUE);
    
    $this->assertFalse($response['success']);
    $this->assertArrayHasKey('error', $response);
  }

  /**
   * Tests POST /dice/roll with optional character_id and roll_type.
   */
  public function testDiceRollEndpointWithOptionalFields(): void {
    $user = $this->drupalCreateUser(['access dungeoncrawler']);
    $this->drupalLogin($user);

    $payload = json_encode([
      'expression' => '1d20',
      'character_id' => 42,
      'roll_type' => 'attack',
    ]);

    $this->getSession()->getDriver()->getClient()->request(
      'POST',
      $this->buildUrl('/dice/roll'),
      [],
      [],
      ['CONTENT_TYPE' => 'application/json'],
      $payload
    );

    $this->assertSession()->statusCodeEquals(200);
    $response = json_decode($this->getSession()->getPage()->getContent(), TRUE);
    
    $this->assertTrue($response['success']);
    $this->assertArrayHasKey('dice', $response);
    $this->assertArrayHasKey('total', $response);
  }

  /**
   * Tests POST /dice/roll route exists and is accessible.
   */
  public function testDiceRollRouteExists(): void {
    $user = $this->drupalCreateUser(['access dungeoncrawler']);
    $this->drupalLogin($user);

    $payload = json_encode([
      'expression' => '1d6',
    ]);

    $this->getSession()->getDriver()->getClient()->request(
      'POST',
      $this->buildUrl('/dice/roll'),
      [],
      [],
      ['CONTENT_TYPE' => 'application/json'],
      $payload
    );

    $status_code = $this->getSession()->getStatusCode();
    // Route should exist (not 404) and method should be allowed (not 405).
    $this->assertNotEquals(404, $status_code, 'Route should exist');
    $this->assertNotEquals(405, $status_code, 'Method should be allowed');
  }

}
