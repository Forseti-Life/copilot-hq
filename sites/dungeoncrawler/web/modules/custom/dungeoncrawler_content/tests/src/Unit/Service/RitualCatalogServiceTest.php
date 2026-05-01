<?php

namespace Drupal\Tests\dungeoncrawler_content\Unit\Service;

use Drupal\dungeoncrawler_content\Service\CharacterManager;
use Drupal\dungeoncrawler_content\Service\RitualCatalogService;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for RitualCatalogService.
 *
 * @coversDefaultClass \Drupal\dungeoncrawler_content\Service\RitualCatalogService
 * @group dungeoncrawler_content
 * @group unit
 */
class RitualCatalogServiceTest extends TestCase {

  /**
   * The ritual catalog service under test.
   *
   * @var \Drupal\dungeoncrawler_content\Service\RitualCatalogService
   */
  protected RitualCatalogService $service;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $character_manager = $this->createMock(CharacterManager::class);
    $this->service = new RitualCatalogService($character_manager);
  }

  /**
   * Test that ritual catalog is seeded with data from CharacterManager.
   */
  public function testRitualCatalogSeeded(): void {
    // The catalog should contain rituals after initialization.
    $sanctify_water = $this->service->getRitual('sanctify-water');
    $this->assertNotNull($sanctify_water, 'sanctify-water ritual should exist');
    $this->assertEquals($sanctify_water['name'], 'Sanctify Water');
  }

  /**
   * Test getRitual returns NULL for non-existent ritual.
   */
  public function testGetRitualNonExistent(): void {
    $ritual = $this->service->getRitual('non-existent-ritual-id');
    $this->assertNull($ritual);
  }

  /**
   * Test getRitual returns correct ritual by ID.
   */
  public function testGetRitualById(): void {
    $ritual = $this->service->getRitual('divination');
    $this->assertNotNull($ritual);
    $this->assertEquals($ritual['name'], 'Divination');
    $this->assertEquals($ritual['level'], 2);
  }

  /**
   * Test listRituals returns all rituals when no filters applied.
   */
  public function testListRitualsNoFilters(): void {
    $all_rituals = $this->service->listRituals();
    $this->assertGreaterThan(0, count($all_rituals), 'Catalog should contain rituals');
  }

  /**
   * Test listRituals filters by level.
   */
  public function testListRitualsFilterByLevel(): void {
    $level_1_rituals = $this->service->listRituals(['level' => 1]);
    foreach ($level_1_rituals as $ritual) {
      $this->assertEquals($ritual['level'], 1, 'All returned rituals should be level 1');
    }
  }

  /**
   * Test listRituals filters by rarity.
   */
  public function testListRitualsFilterByRarity(): void {
    $common_rituals = $this->service->listRituals(['rarity' => 'common']);
    foreach ($common_rituals as $ritual) {
      $this->assertEquals($ritual['rarity'], 'common', 'All returned rituals should be common');
    }
  }

  /**
   * Test listRituals filters by traits (partial match).
   */
  public function testListRitualsFilterByTraits(): void {
    $divine_rituals = $this->service->listRituals(['traits' => ['Divine']]);
    foreach ($divine_rituals as $ritual) {
      $this->assertTrue(in_array('Divine', $ritual['traits'] ?? []), 'Ritual should have Divine trait');
    }
  }

  /**
   * Test listRituals filters by search string.
   */
  public function testListRitualsFilterBySearch(): void {
    $create_rituals = $this->service->listRituals(['search' => 'create']);
    $this->assertGreaterThan(0, count($create_rituals), 'Should find rituals containing "create"');
    foreach ($create_rituals as $ritual) {
      $name_contains = stripos($ritual['name'], 'create') !== FALSE;
      $desc_contains = stripos($ritual['description'] ?? '', 'create') !== FALSE;
      $this->assertTrue($name_contains || $desc_contains, 'Ritual should match search term');
    }
  }

  /**
   * Test validateRitual for a valid ritual.
   */
  public function testValidateRitualValid(): void {
    $ritual = [
      'id' => 'test-ritual',
      'name' => 'Test Ritual',
      'level' => 2,
      'casting_time' => '1 hour',
      'primary_check' => ['skill' => 'Arcana', 'min_proficiency' => 'trained'],
      'description' => 'A test ritual',
    ];
    $result = $this->service->validateRitual($ritual);
    $this->assertTrue($result['valid'], 'Ritual should be valid');
    $this->assertEmpty($result['errors']);
  }

  /**
   * Test validateRitual rejects missing required field.
   */
  public function testValidateRitualMissingRequired(): void {
    $ritual = [
      'id' => 'test-ritual',
      'name' => 'Test Ritual',
      // Missing 'level'
      'casting_time' => '1 hour',
      'primary_check' => ['skill' => 'Arcana', 'min_proficiency' => 'trained'],
      'description' => 'A test ritual',
    ];
    $result = $this->service->validateRitual($ritual);
    $this->assertFalse($result['valid']);
    $this->assertNotEmpty($result['errors']);
    $this->assertTrue(in_array('Missing required field: level', $result['errors']));
  }

  /**
   * Test validateRitual rejects invalid level.
   */
  public function testValidateRitualInvalidLevel(): void {
    $ritual = [
      'id' => 'test-ritual',
      'name' => 'Test Ritual',
      'level' => 11, // Invalid - greater than max
      'casting_time' => '1 hour',
      'primary_check' => ['skill' => 'Arcana', 'min_proficiency' => 'trained'],
      'description' => 'A test ritual',
    ];
    $result = $this->service->validateRitual($ritual);
    $this->assertFalse($result['valid']);
    $this->assertTrue($this->anyContains($result['errors'], 'Level must be'));
  }

  /**
   * Test validateRitual validates skill check structure.
   */
  public function testValidateRitualInvalidSkillCheck(): void {
    $ritual = [
      'id' => 'test-ritual',
      'name' => 'Test Ritual',
      'level' => 2,
      'casting_time' => '1 hour',
      'primary_check' => ['skill' => 'Invalid Skill'], // Missing min_proficiency
      'description' => 'A test ritual',
    ];
    $result = $this->service->validateRitual($ritual);
    $this->assertFalse($result['valid']);
  }

  /**
   * Test getAvailableSkills returns list of skills.
   */
  public function testGetAvailableSkills(): void {
    $skills = $this->service->getAvailableSkills();
    $this->assertNotEmpty($skills);
    $this->assertTrue(in_array('Arcana', $skills));
    $this->assertTrue(in_array('Religion', $skills));
  }

  /**
   * Test getAvailableProficiencies returns list of proficiency levels.
   */
  public function testGetAvailableProficiencies(): void {
    $proficiencies = $this->service->getAvailableProficiencies();
    $this->assertNotEmpty($proficiencies);
    $this->assertTrue(in_array('trained', $proficiencies));
    $this->assertTrue(in_array('expert', $proficiencies));
  }

  /**
   * Helper to check if array contains substring.
   */
  private function anyContains(array $haystack, string $needle): bool {
    foreach ($haystack as $item) {
      if (stripos($item, $needle) !== FALSE) {
        return TRUE;
      }
    }
    return FALSE;
  }

}
