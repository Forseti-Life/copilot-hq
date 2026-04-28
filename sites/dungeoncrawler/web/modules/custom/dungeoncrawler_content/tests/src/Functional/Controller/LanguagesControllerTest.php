<?php

namespace Drupal\Tests\dungeoncrawler_content\Functional\Controller;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests the public language catalog endpoint.
 *
 * @group dungeoncrawler_content
 * @group languages
 * @group controller
 */
class LanguagesControllerTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['dungeoncrawler_content'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  public function testLanguagesCatalogIsPublic(): void {
    $this->drupalGet('/languages', ['query' => ['_format' => 'json']]);
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->responseContains('Common');
    $this->assertSession()->responseContains('Elvish');
  }

}
