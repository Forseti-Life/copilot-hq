<?php

namespace Drupal\Tests\dungeoncrawler_content\Unit\Service;

use Drupal\dungeoncrawler_content\Service\CharacterManager;
use Drupal\Tests\UnitTestCase;

/**
 * Tests stored-record helpers used by character creation APIs.
 *
 * @group dungeoncrawler_content
 * @group character-creation
 * @coversDefaultClass \Drupal\dungeoncrawler_content\Service\CharacterManager
 */
class CharacterManagerStoredRecordTest extends UnitTestCase {

  /**
   * Completed characters are treated as heritage-locked.
   *
   * @covers ::isWizardCompleteRecord
   * @covers ::getStoredHeritage
   * @covers ::getStoredCharacterData
   */
  public function testCompletedRecordExposesStoredHeritage(): void {
    $record = (object) [
      'status' => 1,
      'character_data' => json_encode([
        'ancestry' => 'Dwarf',
        'heritage' => 'ancient-blooded-dwarf',
        'wizard_complete' => TRUE,
      ]),
    ];

    $this->assertTrue(CharacterManager::isWizardCompleteRecord($record));
    $this->assertSame('ancient-blooded-dwarf', CharacterManager::getStoredHeritage($record));
    $this->assertSame('Dwarf', CharacterManager::getStoredCharacterData($record)['ancestry']);
  }

  /**
   * Missing or malformed stored data degrades safely to empty defaults.
   *
   * @covers ::isWizardCompleteRecord
   * @covers ::getStoredHeritage
   * @covers ::getStoredCharacterData
   */
  public function testMalformedStoredRecordReturnsSafeDefaults(): void {
    $record = (object) [
      'status' => 0,
      'character_data' => '{invalid',
    ];

    $this->assertFalse(CharacterManager::isWizardCompleteRecord($record));
    $this->assertSame('', CharacterManager::getStoredHeritage($record));
    $this->assertSame([], CharacterManager::getStoredCharacterData($record));
  }

}
