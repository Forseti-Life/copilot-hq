<?php

namespace Drupal\dungeoncrawler_content\Tests;

use Drupal\dungeoncrawler_content\Service\EquipmentCatalogService;
use Drupal\dungeoncrawler_content\Service\AlchemicalItemService;
use Drupal\dungeoncrawler_content\Service\MagicItemService;

/**
 * Test cases for alchemical items catalog (TC-ALC-01 through TC-ALC-05).
 *
 * Feature: dc-cr-alchemical-items
 * Acceptance Criteria:
 * - AC1: Catalog must include bombs, elixirs, mutagens, poisons, alchemical tools
 * - AC2: All items must have level, price_gp, bulk, activation cost or duration
 * - AC3: No alchemical items must have invest slots or rune attachment flags
 * - AC4: Mutagen items must be compatible with MagicItemService::applyMutagen()
 * - AC5: Tools must not require proficiency; grants +1 circumstance to Crafting
 */
class AlchemicalItemsTest {

  /**
   * TC-ALC-01: Catalog includes all required alchemical item categories.
   *
   * Validates that the EquipmentCatalogService catalog contains at least one item
   * from each required category: bombs, elixirs, mutagens, poisons, tools.
   */
  public static function testAlchemicalCategoriesPresent(): array {
    $test_id = 'TC-ALC-01';
    $test_name = 'Catalog includes all required alchemical item categories';
    
    $alchemical = AlchemicalItemService::getCatalogByAcceptanceCriteria();
    
    $result = [];
    $result['test_id'] = $test_id;
    $result['test_name'] = $test_name;
    $result['checks'] = [];
    
    // Check bombs.
    $result['checks'][] = [
      'name'   => 'Has bombs',
      'pass'   => count($alchemical['bombs']) > 0,
      'found'  => count($alchemical['bombs']),
      'items'  => array_keys($alchemical['bombs']),
    ];
    
    // Check elixirs.
    $result['checks'][] = [
      'name'   => 'Has elixirs',
      'pass'   => count($alchemical['elixirs']) > 0,
      'found'  => count($alchemical['elixirs']),
      'items'  => array_keys($alchemical['elixirs']),
    ];
    
    // Check mutagens.
    $result['checks'][] = [
      'name'   => 'Has mutagens',
      'pass'   => count($alchemical['mutagens']) > 0,
      'found'  => count($alchemical['mutagens']),
      'items'  => array_keys($alchemical['mutagens']),
    ];
    
    // Check poisons.
    $result['checks'][] = [
      'name'   => 'Has poisons',
      'pass'   => count($alchemical['poisons']) > 0,
      'found'  => count($alchemical['poisons']),
      'items'  => array_keys($alchemical['poisons']),
    ];
    
    // Check tools.
    $result['checks'][] = [
      'name'   => 'Has alchemical tools',
      'pass'   => count($alchemical['tools']) > 0,
      'found'  => count($alchemical['tools']),
      'items'  => array_keys($alchemical['tools']),
    ];
    
    $result['passed'] = array_reduce($result['checks'], static function (bool $carry, array $check): bool {
      return $carry && $check['pass'];
    }, TRUE);
    
    return $result;
  }

  /**
   * TC-ALC-02: All alchemical items have required metadata fields.
   *
   * Validates that all items have: id, name, type, price_gp, bulk, activation_cost or duration.
   */
  public static function testAlchemicalMetadataComplete(): array {
    $test_id = 'TC-ALC-02';
    $test_name = 'All alchemical items have required metadata fields';
    
    $all_items = AlchemicalItemService::getAllAlchemicalItems();
    
    $result = [];
    $result['test_id'] = $test_id;
    $result['test_name'] = $test_name;
    $result['checks'] = [];
    
    foreach ($all_items as $id => $item) {
      $validation = AlchemicalItemService::validate($item);
      
      if (!$validation['success']) {
        $result['checks'][] = [
          'item_id' => $id,
          'pass'    => FALSE,
          'errors'  => $validation['errors'],
        ];
      } else {
        $result['checks'][] = [
          'item_id' => $id,
          'pass'    => TRUE,
          'errors'  => [],
        ];
      }
    }
    
    $result['passed'] = array_reduce($result['checks'], static function (bool $carry, array $check): bool {
      return $carry && $check['pass'];
    }, TRUE);
    
    return $result;
  }

  /**
   * TC-ALC-03: No alchemical items have invest slots or rune attachment.
   *
   * Validates that alchemical items are non-magical (no invest slots, no rune slots).
   */
  public static function testAlchemicalNonMagical(): array {
    $test_id = 'TC-ALC-03';
    $test_name = 'No alchemical items have invest slots or rune attachment';
    
    $all_items = AlchemicalItemService::getAllAlchemicalItems();
    
    $result = [];
    $result['test_id'] = $test_id;
    $result['test_name'] = $test_name;
    $result['violations'] = [];
    
    foreach ($all_items as $id => $item) {
      if (isset($item['invest_slots']) && $item['invest_slots'] > 0) {
        $result['violations'][] = "$id has invest_slots: {$item['invest_slots']}";
      }
      if (isset($item['rune_slots']) && $item['rune_slots'] > 0) {
        $result['violations'][] = "$id has rune_slots: {$item['rune_slots']}";
      }
    }
    
    $result['passed'] = empty($result['violations']);
    
    return $result;
  }

  /**
   * TC-ALC-04: Mutagen items are compatible with MagicItemService::applyMutagen().
   *
   * Validates that mutagen items have benefit[] and drawback[] arrays for applyMutagen() integration.
   */
  public static function testMutagenCompatibility(): array {
    $test_id = 'TC-ALC-04';
    $test_name = 'Mutagen items are compatible with MagicItemService::applyMutagen()';
    
    $mutagens = AlchemicalItemService::getByCategory('mutagen');
    
    $result = [];
    $result['test_id'] = $test_id;
    $result['test_name'] = $test_name;
    $result['checks'] = [];
    
    foreach ($mutagens as $id => $item) {
      $stats = $item['alchemical_stats'] ?? [];
      
      $has_benefit = isset($stats['benefit']) && is_array($stats['benefit']);
      $has_drawback = isset($stats['drawback']) && is_array($stats['drawback']);
      $has_duration = isset($stats['duration_rounds']);
      $has_traits = isset($stats['traits']) && in_array('mutagen', $stats['traits']);
      
      $result['checks'][] = [
        'mutagen_id'   => $id,
        'has_benefit'  => $has_benefit,
        'has_drawback' => $has_drawback,
        'has_duration' => $has_duration,
        'has_traits'   => $has_traits,
        'pass'         => $has_benefit && $has_drawback && $has_duration && $has_traits,
      ];
    }
    
    $result['passed'] = count($mutagens) > 0 && array_reduce($result['checks'], static function (bool $carry, array $check): bool {
      return $carry && $check['pass'];
    }, TRUE);
    
    return $result;
  }

  /**
   * TC-ALC-05: Alchemical tools do not require proficiency.
   *
   * Validates that tool items don't have proficiency requirements and can be used for Crafting.
   */
  public static function testAlchemicalToolsNoProficiency(): array {
    $test_id = 'TC-ALC-05';
    $test_name = 'Alchemical tools do not require proficiency';
    
    $tools = AlchemicalItemService::getByCategory('tool');
    
    $result = [];
    $result['test_id'] = $test_id;
    $result['test_name'] = $test_name;
    $result['checks'] = [];
    
    foreach ($tools as $id => $item) {
      $stats = $item['alchemical_stats'] ?? [];
      
      $has_proficiency_req = isset($stats['requires_proficiency']) && $stats['requires_proficiency'] === TRUE;
      $is_tool = isset($stats['is_alchemical_tool']) && $stats['is_alchemical_tool'] === TRUE;
      
      $result['checks'][] = [
        'tool_id'        => $id,
        'requires_prof'  => $has_proficiency_req,
        'is_alchemical'  => $is_tool,
        'pass'           => !$has_proficiency_req && $is_tool,
      ];
    }
    
    $result['passed'] = count($tools) > 0 && array_reduce($result['checks'], static function (bool $carry, array $check): bool {
      return $carry && $check['pass'];
    }, TRUE);
    
    return $result;
  }

  /**
   * Run all test cases and print results.
   */
  public static function runAllTests(): void {
    echo "\n=== ALCHEMICAL ITEMS TEST SUITE ===\n";
    
    $tests = [
      'testAlchemicalCategoriesPresent',
      'testAlchemicalMetadataComplete',
      'testAlchemicalNonMagical',
      'testMutagenCompatibility',
      'testAlchemicalToolsNoProficiency',
    ];
    
    $all_passed = TRUE;
    
    foreach ($tests as $test_method) {
      $result = self::$test_method();
      
      echo "\n{$result['test_id']}: {$result['test_name']}\n";
      echo "Result: " . ($result['passed'] ? 'PASS' : 'FAIL') . "\n";
      
      if (isset($result['checks'])) {
        foreach ($result['checks'] as $check) {
          $status = $check['pass'] ? '✓' : '✗';
          echo "  $status ";
          if (isset($check['name'])) {
            echo "{$check['name']} (found: {$check['found']})";
          } elseif (isset($check['item_id'])) {
            echo "{$check['item_id']}";
          } elseif (isset($check['mutagen_id'])) {
            echo "{$check['mutagen_id']}";
          } elseif (isset($check['tool_id'])) {
            echo "{$check['tool_id']}";
          }
          echo "\n";
        }
      }
      
      if (isset($result['violations'])) {
        foreach ($result['violations'] as $violation) {
          echo "  ✗ $violation\n";
        }
      }
      
      if (!$result['passed']) {
        $all_passed = FALSE;
      }
    }
    
    echo "\n=== SUMMARY ===\n";
    echo $all_passed ? "ALL TESTS PASSED\n" : "SOME TESTS FAILED\n";
    echo "\n";
  }

}

// Run tests if executed directly.
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($GLOBALS['argv'][0] ?? '')) {
  AlchemicalItemsTest::runAllTests();
}
