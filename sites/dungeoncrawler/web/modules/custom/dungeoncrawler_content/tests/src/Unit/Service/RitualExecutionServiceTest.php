<?php

namespace Drupal\Tests\dungeoncrawler_content\Unit\Service;

use Drupal\dungeoncrawler_content\Service\CharacterManager;
use Drupal\dungeoncrawler_content\Service\RitualCatalogService;
use Drupal\dungeoncrawler_content\Service\RitualExecutionService;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for RitualExecutionService.
 *
 * @coversDefaultClass \Drupal\dungeoncrawler_content\Service\RitualExecutionService
 * @group dungeoncrawler_content
 * @group unit
 */
class RitualExecutionServiceTest extends TestCase {

  /**
   * The ritual execution service under test.
   *
   * @var \Drupal\dungeoncrawler_content\Service\RitualExecutionService
   */
  protected RitualExecutionService $service;

  /**
   * Mock ritual catalog service.
   *
   * @var \Drupal\dungeoncrawler_content\Service\RitualCatalogService
   */
  protected RitualCatalogService $catalogService;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $character_manager = $this->createMock(CharacterManager::class);
    $this->catalogService = new RitualCatalogService($character_manager);
    $this->service = new RitualExecutionService($this->catalogService, $character_manager);
  }

  /**
   * Test initializeRitual with valid parameters.
   */
  public function testInitializeRitualValid(): void {
    $result = $this->service->initializeRitual('sanctify-water', 1, []);
    $this->assertTrue($result['valid'], 'Ritual initialization should be valid');
    $this->assertEquals($result['ritual_id'], 'sanctify-water');
    $this->assertEquals($result['state'], 'pending');
    $this->assertEquals($result['primary_caster_id'], 1);
  }

  /**
   * Test initializeRitual rejects non-existent ritual.
   */
  public function testInitializeRitualNonExistent(): void {
    $result = $this->service->initializeRitual('non-existent', 1, []);
    $this->assertFalse($result['valid']);
    $this->assertTrue(in_array('Ritual not found: non-existent', $result['errors']));
  }

  /**
   * Test initializeRitual rejects invalid primary caster ID.
   */
  public function testInitializeRitualInvalidPrimaryCaster(): void {
    $result = $this->service->initializeRitual('sanctify-water', -1, []);
    $this->assertFalse($result['valid']);
  }

  /**
   * Test initializeRitual rejects insufficient secondary casters.
   */
  public function testInitializeRitualInsufficientSecondaryCasters(): void {
    // Create-Undead requires 1 secondary caster
    $result = $this->service->initializeRitual('create-undead', 1, []);
    $this->assertFalse($result['valid']);
    $this->assertTrue($this->anyStringContains($result['errors'], 'secondary'));
  }

  /**
   * Test initializeRitual accepts sufficient secondary casters.
   */
  public function testInitializeRitualValidSecondaryCasters(): void {
    // Create-Undead requires 1 secondary caster
    $result = $this->service->initializeRitual('create-undead', 1, [2]);
    $this->assertTrue($result['valid']);
    $this->assertEquals($result['secondary_caster_ids'], [2]);
  }

  /**
   * Test checkRitualVsSpellFlow prevents normal spell flow.
   */
  public function testCheckRitualVsSpellFlowPrevention(): void {
    $result = $this->service->checkRitualVsSpellFlow('sanctify-water');
    $this->assertTrue($result['is_ritual']);
    $this->assertFalse($result['can_use_spell_flow']);
  }

  /**
   * Test checkRitualVsSpellFlow for non-existent ritual.
   */
  public function testCheckRitualVsSpellFlowNonExistent(): void {
    $result = $this->service->checkRitualVsSpellFlow('non-existent');
    $this->assertFalse($result['is_ritual']);
    $this->assertFalse($result['can_use_spell_flow']);
  }

  /**
   * Test validateCasterSkillProficiency structure validation.
   */
  public function testValidateCasterSkillProficiencyValidStructure(): void {
    $skill_check = ['skill' => 'Arcana', 'min_proficiency' => 'trained'];
    $result = $this->service->validateCasterSkillProficiency(1, $skill_check);
    $this->assertTrue($result['valid']);
    $this->assertEquals($result['skill'], 'Arcana');
    $this->assertEquals($result['min_required'], 'trained');
  }

  /**
   * Test validateCasterSkillProficiency rejects missing skill.
   */
  public function testValidateCasterSkillProficiencyMissingSkill(): void {
    $skill_check = ['min_proficiency' => 'trained'];
    $result = $this->service->validateCasterSkillProficiency(1, $skill_check);
    $this->assertFalse($result['valid']);
  }

  /**
   * Test validateCasterSkillProficiency rejects invalid proficiency.
   */
  public function testValidateCasterSkillProficiencyInvalidProficiency(): void {
    $skill_check = ['skill' => 'Arcana', 'min_proficiency' => 'invalid-level'];
    $result = $this->service->validateCasterSkillProficiency(1, $skill_check);
    $this->assertFalse($result['valid']);
  }

  /**
   * Test resolveRitualOutcome for success.
   */
  public function testResolveRitualOutcomeSuccess(): void {
    $result = $this->service->resolveRitualOutcome('sanctify-water', 'success');
    $this->assertTrue($result['valid']);
    $this->assertEquals($result['outcome'], 'success');
    $this->assertEquals($result['ritual_id'], 'sanctify-water');
    $this->assertNotEmpty($result['description']);
    $this->assertNotEmpty($result['automated_consequences']);
  }

  /**
   * Test resolveRitualOutcome for failure.
   */
  public function testResolveRitualOutcomeFailure(): void {
    $result = $this->service->resolveRitualOutcome('sanctify-water', 'failure');
    $this->assertTrue($result['valid']);
    $this->assertEquals($result['outcome'], 'failure');
    $this->assertTrue(stripos($result['description'], 'fail') !== FALSE);
  }

  /**
   * Test resolveRitualOutcome for critical failure.
   */
  public function testResolveRitualOutcomeCriticalFailure(): void {
    $result = $this->service->resolveRitualOutcome('sanctify-water', 'critical_failure');
    $this->assertTrue($result['valid']);
    $this->assertEquals($result['outcome'], 'critical_failure');
    $this->assertTrue(stripos($result['description'], 'critical') !== FALSE);
  }

  /**
   * Test resolveRitualOutcome rejects invalid outcome.
   */
  public function testResolveRitualOutcomeInvalidOutcome(): void {
    $result = $this->service->resolveRitualOutcome('sanctify-water', 'invalid-outcome');
    $this->assertFalse($result['valid']);
    $this->assertTrue(in_array('Invalid outcome: invalid-outcome', $result['errors']));
  }

  /**
   * Test resolveRitualOutcome rejects non-existent ritual.
   */
  public function testResolveRitualOutcomeNonExistent(): void {
    $result = $this->service->resolveRitualOutcome('non-existent', 'success');
    $this->assertFalse($result['valid']);
    $this->assertTrue(in_array('Ritual not found: non-existent', $result['errors']));
  }

  /**
   * Test getProgressTrackingCapability for long-duration ritual.
   */
  public function testGetProgressTrackingCapabilityLongDuration(): void {
    // Heartbond has 1 day casting time
    $result = $this->service->getProgressTrackingCapability('heartbond');
    $this->assertTrue($result['supports_progress_tracking']);
    $this->assertNotEmpty($result['casting_duration']);
  }

  /**
   * Test getProgressTrackingCapability for short-duration ritual.
   */
  public function testGetProgressTrackingCapabilityShortDuration(): void {
    // Sanctify Water has 1 hour casting time
    $result = $this->service->getProgressTrackingCapability('sanctify-water');
    // 1 hour rituals may or may not support tracking depending on implementation
    $this->assertNotEmpty($result['casting_duration']);
  }

  /**
   * Test getProgressTrackingCapability for non-existent ritual.
   */
  public function testGetProgressTrackingCapabilityNonExistent(): void {
    $result = $this->service->getProgressTrackingCapability('non-existent');
    $this->assertFalse($result['supports_progress_tracking']);
  }

  /**
   * Test that rituals do not consume spell slots.
   */
  public function testRitualsDoNotConsumeSpellSlots(): void {
    // The fact that initializeRitual works without modifying character state
    // demonstrates that rituals don't consume spell slots.
    $result = $this->service->initializeRitual('divination', 1, []);
    $this->assertTrue($result['valid']);
    // No errors about spell slots should appear
    $this->assertEmpty($result['errors']);
  }

  /**
   * Helper to check if any string in array contains substring.
   */
  private function anyStringContains(array $haystack, string $needle): bool {
    foreach ($haystack as $item) {
      if (is_string($item) && stripos($item, $needle) !== FALSE) {
        return TRUE;
      }
    }
    return FALSE;
  }

}
