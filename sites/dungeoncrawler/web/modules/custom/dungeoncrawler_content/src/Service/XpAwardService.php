<?php

namespace Drupal\dungeoncrawler_content\Service;

use Drupal\dungeoncrawler_content\Service\CharacterManager;

/**
 * Service for awarding XP and handling party-wide advancement.
 *
 * Implements PF2e Core Rulebook Chapter 10: Experience Points and Advancement.
 */
class XpAwardService {

  /**
   * Advancement thresholds (XP required to gain a level).
   * 
   * Source: PF2e CRB Chapter 10.
   */
  const ADVANCEMENT_THRESHOLDS = [
    'fast'     => 800,
    'standard' => 1000,
    'slow'     => 1200,
  ];

  /**
   * Accomplishment XP values.
   * 
   * Source: PF2e CRB Table 10-8.
   */
  const ACCOMPLISHMENT_XP = [
    'minor'    => 30,
    'moderate' => 60,
    'major'    => 120,
  ];

  /**
   * Hazard XP by level delta.
   * 
   * Source: PF2e CRB Table 10-14.
   */
  const HAZARD_XP_TABLE = [
    -4 => 5,
    -3 => 8,
    -2 => 10,
    -1 => 15,
     0 => 20,
     1 => 30,
     2 => 40,
     3 => 60,
     4 => 80,
  ];

  /**
   * Award XP to a character and handle level-up.
   *
   * @param array $character_data
   *   Character record with 'experience_points' and advancement mode.
   * @param int $amount
   *   XP amount to award.
   * @param string $mode
   *   Advancement mode: 'fast', 'standard', 'slow', or 'story'.
   * @param int $current_level
   *   Character's current level (for catch-up logic).
   * @param int $party_level
   *   Party's highest level (for catch-up logic).
   *
   * @return array
   *   Result array: {
   *     'new_xp': int,
   *     'level_gained': bool,
   *     'xp_tracked': bool,
   *     'xp_awarded': int
   *   }
   */
  public static function awardXp(
    array $character_data,
    int $amount,
    string $mode = 'standard',
    int $current_level = 1,
    int $party_level = 1
  ): array {
    // In story-based leveling, XP is not tracked.
    if ($mode === 'story') {
      return [
        'new_xp' => 0,
        'level_gained' => FALSE,
        'xp_tracked' => FALSE,
        'xp_awarded' => 0,
      ];
    }

    // Apply double XP for behind-level catch-up.
    $actual_amount = $amount;
    if ($current_level < $party_level) {
      $actual_amount = $amount * 2;
    }

    $current_xp = (int) ($character_data['experience_points'] ?? 0);
    $new_xp = $current_xp + $actual_amount;
    $threshold = self::ADVANCEMENT_THRESHOLDS[$mode] ?? 1000;
    $level_gained = $new_xp >= $threshold;

    if ($level_gained) {
      $new_xp -= $threshold;
    }

    return [
      'new_xp' => $new_xp,
      'level_gained' => $level_gained,
      'xp_tracked' => TRUE,
      'xp_awarded' => $actual_amount,
    ];
  }

  /**
   * Get advancement threshold for a mode.
   *
   * @param string $mode
   *   Advancement mode: 'fast', 'standard', 'slow'.
   *
   * @return int
   *   XP threshold to level up.
   */
  public static function advancementThreshold(string $mode = 'standard'): int {
    return self::ADVANCEMENT_THRESHOLDS[$mode] ?? 1000;
  }

  /**
   * Award encounter XP to party.
   *
   * Distributes equal XP to all party members.
   *
   * @param int $party_size
   *   Number of party members.
   * @param int $xp
   *   Total XP to distribute.
   *
   * @return array
   *   Per-PC XP amount.
   */
  public static function awardEncounterXp(int $party_size, int $xp): int {
    // Equal distribution: each PC receives the full amount (not divided).
    // Note: Based on TC-XPA-07 interpretation that "same XP" means equal not split.
    return $xp;
  }

  /**
   * Get XP for an encounter threat tier.
   *
   * @param string $threat_tier
   *   Threat tier: 'trivial', 'low', 'moderate', 'severe', 'extreme'.
   * @param int $party_size
   *   Number of party members (for scaling).
   *
   * @return int
   *   XP value (0 for trivial, base value for others).
   */
  public static function encounterXp(string $threat_tier, int $party_size = 4): int {
    $base_xp = [
      'trivial'  => 0,
      'low'      => 60,
      'moderate' => 80,
      'severe'   => 120,
      'extreme'  => 160,
    ];

    if (!isset($base_xp[$threat_tier])) {
      return 0;
    }

    $xp = $base_xp[$threat_tier];
    if ($party_size !== 4) {
      $adjustment = ($party_size - 4) * CharacterManager::CHARACTER_ADJUSTMENT_XP;
      $xp += $adjustment;
      $xp = max(0, $xp);
    }

    return $xp;
  }

  /**
   * Award accomplishment XP.
   *
   * @param string $type
   *   Accomplishment tier: 'minor', 'moderate', 'major'.
   *
   * @return array
   *   Result: {
   *     'xp': int,
   *     'hero_point_flag': bool
   *   }
   */
  public static function awardAccomplishmentXp(string $type): array {
    $xp = self::ACCOMPLISHMENT_XP[$type] ?? 0;
    // Hero Point flag for moderate and major only.
    $hero_point_flag = ($type === 'moderate' || $type === 'major');

    return [
      'xp' => $xp,
      'hero_point_flag' => $hero_point_flag,
    ];
  }

  /**
   * Get creature XP value.
   *
   * Routes through CharacterManager::computeCreatureXp().
   *
   * @param int $creature_level
   * @param int $party_level
   *
   * @return int|null
   */
  public static function creatureXp(int $creature_level, int $party_level): ?int {
    return CharacterManager::computeCreatureXp($creature_level, $party_level);
  }

  /**
   * Get creature XP source identifier.
   *
   * @return string
   */
  public static function creatureXpSource(): string {
    return 'dc-cr-encounter-creature-xp-table';
  }

  /**
   * Get hazard XP value.
   *
   * Routes through HAZARD_XP_TABLE (Table 10-14).
   *
   * @param int $hazard_level
   * @param int $party_level
   *
   * @return int|null
   */
  public static function hazardXp(int $hazard_level, int $party_level): ?int {
    $delta = $hazard_level - $party_level;
    if ($delta > 4 || $delta < -4) {
      return 0;
    }
    return self::HAZARD_XP_TABLE[$delta] ?? 0;
  }

  /**
   * Get hazard XP source identifier.
   *
   * @return string
   */
  public static function hazardXpSource(): string {
    return 'table-10-14';
  }

}
