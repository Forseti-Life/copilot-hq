<?php

namespace Drupal\Tests\dungeoncrawler_content\Unit\Service;

use Drupal\dungeoncrawler_content\Service\LanguageService;
use Drupal\Tests\UnitTestCase;

/**
 * Tests the language system service.
 *
 * @group dungeoncrawler_content
 * @group languages
 */
class LanguageServiceTest extends UnitTestCase {

  /**
   * The service under test.
   */
  protected LanguageService $languageService;

  protected function setUp(): void {
    parent::setUp();
    $this->languageService = new LanguageService();
  }

  public function testCatalogContainsRequiredLanguages(): void {
    $catalog = LanguageService::getLanguageCatalog();
    $ids = array_column($catalog, 'id');

    foreach (['Common', 'Elvish', 'Dwarvish', 'Gnomish', 'Halfling', 'Orcish', 'Sylvan', 'Undercommon', 'Draconic', 'Jotun'] as $language_id) {
      $this->assertContains($language_id, $ids);
    }
  }

  public function testProcessLanguagesAddsDefaultElfLanguagesAndBonusSelections(): void {
    $result = $this->languageService->processLanguages([
      'ancestry' => 'elf',
      'abilities' => ['int' => 14],
    ], [
      'languages' => ['Draconic', 'Sylvan'],
    ]);

    $this->assertTrue($result['success']);
    $this->assertSame(['Common', 'Draconic', 'Elvish', 'Sylvan'], $result['languages']);
  }

  public function testProcessLanguagesRejectsUnknownLanguage(): void {
    $result = $this->languageService->processLanguages([
      'ancestry' => 'elf',
      'abilities' => ['int' => 14],
    ], [
      'languages' => ['bogus-language'],
    ]);

    $this->assertFalse($result['success']);
    $this->assertSame('unknown language id: bogus-language', $result['error']);
  }

  public function testProcessLanguagesPreservesExistingValuesWhenPatchOmitsLanguages(): void {
    $result = $this->languageService->processLanguages([
      'ancestry' => 'elf',
      'abilities' => ['int' => 14],
    ], [], ['Common', 'Elvish', 'Draconic']);

    $this->assertTrue($result['success']);
    $this->assertSame(['Common', 'Draconic', 'Elvish'], $result['languages']);
  }

}
