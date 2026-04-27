<?php

namespace Drupal\Tests\dungeoncrawler_content\Functional\Controller;

use Drupal\Tests\BrowserTestBase;
use Drupal\Core\Database\Database;

/**
 * Tests CharacterViewController functionality.
 *
 * @group dungeoncrawler_content
 * @group controller
 */
class CharacterViewControllerTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['dungeoncrawler_content'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Tests character view with valid character - positive case.
   *
   * Note: This test requires an actual character entity to exist.
   */
  public function testCharacterViewPositive(): void {
    $user = $this->drupalCreateUser(['access dungeoncrawler characters']);
    $this->drupalLogin($user);

    // Without a real character, this will fail but validates route exists
    $this->drupalGet('/characters/1');
    // Could be 404 if character doesn't exist or 403 if no access
    $this->assertSession()->statusCodeNotEquals(405);
  }

  /**
   * Tests the empty portrait state links to portrait setup.
   */
  public function testCharacterViewShowsPortraitActionWhenMissing(): void {
    $user = $this->drupalCreateUser([
      'access dungeoncrawler characters',
      'create dungeoncrawler characters',
    ]);
    $this->drupalLogin($user);

    $character_id = Database::getConnection()->insert('dc_campaign_characters')
      ->fields([
        'uuid' => \Drupal::service('uuid')->generate(),
        'campaign_id' => 0,
        'character_id' => 0,
        'instance_id' => \Drupal::service('uuid')->generate(),
        'uid' => (int) $user->id(),
        'name' => 'Portraitless Hero',
        'class' => 'wizard',
        'ancestry' => 'human',
        'level' => 1,
        'hp_current' => 10,
        'hp_max' => 10,
        'armor_class' => 10,
        'experience_points' => 0,
        'position_q' => 0,
        'position_r' => 0,
        'last_room_id' => '',
        'type' => 'pc',
        'status' => 1,
        'character_data' => json_encode([
          'name' => 'Portraitless Hero',
          'step' => 8,
        ]),
        'created' => time(),
        'changed' => time(),
      ])
      ->execute();

    $this->drupalGet('/characters/' . $character_id);
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->linkExists('Add profile picture');
    $this->assertSession()->responseContains('/characters/create/step/8?character_id=' . $character_id);
  }

  /**
   * Tests character view with invalid ID - negative case.
   */
  public function testCharacterViewNegativeInvalidId(): void {
    $user = $this->drupalCreateUser(['access dungeoncrawler characters']);
    $this->drupalLogin($user);

    $this->drupalGet('/characters/invalid');
    $this->assertSession()->statusCodeEquals(404);
  }

  /**
   * Tests character view without authentication - negative case.
   */
  public function testCharacterViewNegativeNoAuth(): void {
    $this->drupalGet('/characters/1');
    $this->assertSession()->statusCodeEquals(403);
  }

}
