<?php

namespace Drupal\Tests\dungeoncrawler_content\Unit;

use Drupal\dungeoncrawler_content\Service\XpAwardService;
use PHPUnit\Framework\TestCase;

/**
 * Tests for XpAwardService.
 *
 * @coversDefaultClass \Drupal\dungeoncrawler_content\Service\XpAwardService
 * @group dungeoncrawler_content
 */
class XpAwardServiceTest extends TestCase {

  /**
   * TC-XPA-01: XP threshold — standard level-up at 1,000 XP.
   */
  public function testXpThresholdStandardLevelUp() {
    $character = ['experience_points' => 950];
    $result = XpAwardService::awardXp($character, 100, 'standard');
    
    $this->assertTrue($result['level_gained']);
    $this->assertEquals(50, $result['new_xp']);
  }

  /**
   * TC-XPA-02: XP carryover — excess XP preserved on level-up.
   */
  public function testXpCarryoverOnLevelUp() {
    $character = ['experience_points' => 980];
    $result = XpAwardService::awardXp($character, 200, 'standard');
    
    $this->assertTrue($result['level_gained']);
    $this->assertEquals(180, $result['new_xp']);
  }

  /**
   * TC-XPA-03: XP threshold — standard mode 1,000 XP.
   */
  public function testAdvancementThresholdStandard() {
    $threshold = XpAwardService::advancementThreshold('standard');
    $this->assertEquals(1000, $threshold);
  }

  /**
   * TC-XPA-04: Advancement speed — Fast mode (800 XP).
   */
  public function testAdvancementThresholdFast() {
    $threshold = XpAwardService::advancementThreshold('fast');
    $this->assertEquals(800, $threshold);
  }

  /**
   * TC-XPA-05: Advancement speed — Slow mode (1,200 XP).
   */
  public function testAdvancementThresholdSlow() {
    $threshold = XpAwardService::advancementThreshold('slow');
    $this->assertEquals(1200, $threshold);
  }

  /**
   * TC-XPA-06: Advancement speed — mode is configurable.
   */
  public function testAdvancementModeConfigurable() {
    $this->assertEquals(800, XpAwardService::advancementThreshold('fast'));
    $this->assertEquals(1000, XpAwardService::advancementThreshold('standard'));
    $this->assertEquals(1200, XpAwardService::advancementThreshold('slow'));
  }

  /**
   * TC-XPA-07: Party-wide XP award — all members receive equal XP.
   */
  public function testPartyWideXpAward() {
    $xp_per_pc = XpAwardService::awardEncounterXp(4, 40);
    $this->assertEquals(40, $xp_per_pc);
  }

  /**
   * TC-XPA-08: Trivial encounter — 0 XP returned.
   */
  public function testTrivialEncounterXp() {
    $xp = XpAwardService::encounterXp('trivial', 4);
    $this->assertEquals(0, $xp);
  }

  /**
   * TC-XPA-09: Trivial encounter — minor accomplishment XP may override.
   */
  public function testMinorAccomplishmentXpOverrideTrivial() {
    $result = XpAwardService::awardAccomplishmentXp('minor');
    
    $this->assertEquals(30, $result['xp']);
    $this->assertFalse($result['hero_point_flag']);
  }

  /**
   * TC-XPA-10: Story-based leveling — XP not tracked.
   */
  public function testStoryBasedLevelingXpNotTracked() {
    $character = ['experience_points' => 0];
    $result = XpAwardService::awardXp($character, 100, 'story');
    
    $this->assertFalse($result['xp_tracked']);
    $this->assertEquals(0, $result['xp_awarded']);
  }

  /**
   * TC-XPA-11: Accomplishment XP table — minor/moderate/major tiers present.
   */
  public function testAccomplishmentXpTableStructure() {
    $minor = XpAwardService::awardAccomplishmentXp('minor');
    $moderate = XpAwardService::awardAccomplishmentXp('moderate');
    $major = XpAwardService::awardAccomplishmentXp('major');
    
    $this->assertNotNull($minor['xp']);
    $this->assertNotNull($moderate['xp']);
    $this->assertNotNull($major['xp']);
    $this->assertLessThan($moderate['xp'], $minor['xp']);
    $this->assertLessThan($major['xp'], $moderate['xp']);
  }

  /**
   * TC-XPA-12: Hero Point flag — moderate accomplishment.
   */
  public function testHeroPointFlagModerate() {
    $result = XpAwardService::awardAccomplishmentXp('moderate');
    
    $this->assertEquals(60, $result['xp']);
    $this->assertTrue($result['hero_point_flag']);
  }

  /**
   * TC-XPA-13: Hero Point flag — major accomplishment.
   */
  public function testHeroPointFlagMajor() {
    $result = XpAwardService::awardAccomplishmentXp('major');
    
    $this->assertEquals(120, $result['xp']);
    $this->assertTrue($result['hero_point_flag']);
  }

  /**
   * TC-XPA-14: Hero Point flag — minor accomplishment does NOT flag.
   */
  public function testHeroPointFlagMinor() {
    $result = XpAwardService::awardAccomplishmentXp('minor');
    
    $this->assertEquals(30, $result['xp']);
    $this->assertFalse($result['hero_point_flag']);
  }

  /**
   * TC-XPA-15: Creature XP source — uses Table 10–2.
   */
  public function testCreatureXpSource() {
    $source = XpAwardService::creatureXpSource();
    $this->assertEquals('dc-cr-encounter-creature-xp-table', $source);
  }

  /**
   * TC-XPA-16: Hazard XP source — uses Table 10–14.
   */
  public function testHazardXpSource() {
    $source = XpAwardService::hazardXpSource();
    $this->assertEquals('table-10-14', $source);
  }

  /**
   * TC-XPA-17: PCs behind party level — double XP.
   */
  public function testBehindLevelDoubleXp() {
    $character = ['experience_points' => 0];
    // PC level 3, party level 5, base XP 40
    $result = XpAwardService::awardXp($character, 40, 'standard', 3, 5);
    
    // Should receive 80 (double XP).
    $this->assertEquals(80, $result['xp_awarded']);
  }

  /**
   * TC-XPA-18: Story-based leveling — XP not tracked is not an error.
   */
  public function testStoryBasedLevelingNotError() {
    $character = ['experience_points' => 100];
    $result = XpAwardService::awardXp($character, 40, 'story');
    
    $this->assertTrue($result['level_gained'] === FALSE);
    $this->assertFalse($result['xp_tracked']);
    $this->assertArrayHasKey('success', $result) ? $this->assertTrue($result['success']) : TRUE;
  }

  /**
   * TC-XPA-19: Trivial encounter — 0 XP is not an error state.
   */
  public function testTrivialEncounterZeroXpNotError() {
    $xp = XpAwardService::encounterXp('trivial', 4);
    
    $this->assertEquals(0, $xp);
    // Not an error or null, just 0.
    $this->assertIsInt($xp);
  }

}
