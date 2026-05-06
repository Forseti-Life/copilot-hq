<?php

namespace Drupal\dungeoncrawler_content\Service;

use Drupal\dungeoncrawler_content\Service\EquipmentCatalogService;

/**
 * Service for managing alchemical items.
 *
 * Handles alchemical item validation, access, and category filtering.
 * Integrates with EquipmentCatalogService for catalog lookups.
 */
class AlchemicalItemService {

  /**
   * Alchemical item categories (subtypes).
   */
  const ALCHEMICAL_CATEGORIES = [
    'bomb',
    'elixir',
    'mutagen',
    'poison',
    'tool',
  ];

  /**
   * Required metadata fields for alchemical items.
   */
  const REQUIRED_METADATA = [
    'id',
    'name',
    'type',
    'price_gp',
    'bulk',
    'alchemical_stats',
  ];

  /**
   * Required alchemical_stats fields.
   */
  const REQUIRED_ALCHEMICAL_STATS = [
    'subtype',
    'traits',
    'description',
  ];

  /**
   * Get all alchemical items from catalog.
   *
   * @return array
   *   Alchemical items keyed by ID.
   */
  public static function getAllAlchemicalItems(): array {
    $catalog = new EquipmentCatalogService();
    $all_items = $catalog->getByType('alchemical');
    
    return $all_items ?? [];
  }

  /**
   * Get alchemical items by category.
   *
   * @param string $category
   *   Category: bomb, elixir, mutagen, poison, tool.
   *
   * @return array
   *   Items in the category.
   */
  public static function getByCategory(string $category): array {
    $items = self::getAllAlchemicalItems();
    
    return array_filter($items, static function (array $item) use ($category): bool {
      $subtype = $item['alchemical_stats']['subtype'] ?? NULL;
      return $subtype === $category;
    });
  }

  /**
   * Get all categories represented in the catalog.
   *
   * @return array
   *   List of categories present.
   */
  public static function getCategoriesPresent(): array {
    $items = self::getAllAlchemicalItems();
    $categories = [];
    
    foreach ($items as $item) {
      $subtype = $item['alchemical_stats']['subtype'] ?? NULL;
      if ($subtype && !in_array($subtype, $categories)) {
        $categories[] = $subtype;
      }
    }
    
    return $categories;
  }

  /**
   * Validate an alchemical item record.
   *
   * @param array $item
   *   Item data.
   *
   * @return array
   *   Validation result: {success: bool, errors: []}
   */
  public static function validate(array $item): array {
    $errors = [];
    
    // Check required top-level fields.
    foreach (self::REQUIRED_METADATA as $field) {
      if (!isset($item[$field]) || empty($item[$field])) {
        $errors[] = "Missing required field: $field";
      }
    }
    
    // Check alchemical_stats structure.
    if (!isset($item['alchemical_stats']) || !is_array($item['alchemical_stats'])) {
      $errors[] = "alchemical_stats must be an array";
    } else {
      foreach (self::REQUIRED_ALCHEMICAL_STATS as $field) {
        if (!isset($item['alchemical_stats'][$field])) {
          $errors[] = "Missing alchemical_stats.$field";
        }
      }
      
      // Validate subtype.
      $subtype = $item['alchemical_stats']['subtype'] ?? NULL;
      if (!in_array($subtype, self::ALCHEMICAL_CATEGORIES)) {
        $errors[] = "Invalid alchemical_stats.subtype: $subtype (must be one of: " . implode(', ', self::ALCHEMICAL_CATEGORIES) . ")";
      }
    }
    
    // Validate non-magical (no invest, no runes).
    if (isset($item['invest_slots']) && $item['invest_slots'] > 0) {
      $errors[] = "Alchemical items must not have invest slots (these are non-magical)";
    }
    if (isset($item['rune_slots']) && $item['rune_slots'] > 0) {
      $errors[] = "Alchemical items must not have rune slots (these are non-magical)";
    }
    
    return [
      'success' => empty($errors),
      'errors'  => $errors,
    ];
  }

  /**
   * Check if item is consumable.
   *
   * @param array $item
   *   Item data.
   *
   * @return bool
   *   TRUE if item is consumable.
   */
  public static function isConsumable(array $item): bool {
    $traits = $item['alchemical_stats']['traits'] ?? [];
    return in_array('consumable', $traits);
  }

  /**
   * Check if item is a bomb (thrown alchemical).
   *
   * @param array $item
   *   Item data.
   *
   * @return bool
   *   TRUE if item is a bomb.
   */
  public static function isBomb(array $item): bool {
    $subtype = $item['alchemical_stats']['subtype'] ?? NULL;
    return $subtype === 'bomb';
  }

  /**
   * Check if item is a poison.
   *
   * @param array $item
   *   Item data.
   *
   * @return bool
   *   TRUE if item is a poison.
   */
  public static function isPoison(array $item): bool {
    $subtype = $item['alchemical_stats']['subtype'] ?? NULL;
    return $subtype === 'poison';
  }

  /**
   * Check if item is a mutagen.
   *
   * @param array $item
   *   Item data.
   *
   * @return bool
   *   TRUE if item is a mutagen.
   */
  public static function isMutagen(array $item): bool {
    $subtype = $item['alchemical_stats']['subtype'] ?? NULL;
    return $subtype === 'mutagen';
  }

  /**
   * Check if item is an alchemical tool.
   *
   * @param array $item
   *   Item data.
   *
   * @return bool
   *   TRUE if item is a tool.
   */
  public static function isTool(array $item): bool {
    $subtype = $item['alchemical_stats']['subtype'] ?? NULL;
    return $subtype === 'tool';
  }

  /**
   * Get catalog items matching acceptance criteria.
   *
   * @return array
   *   Result: {
   *     'bombs': [...],
   *     'elixirs': [...],
   *     'mutagens': [...],
   *     'poisons': [...],
   *     'tools': [...]
   *   }
   */
  public static function getCatalogByAcceptanceCriteria(): array {
    return [
      'bombs'    => self::getByCategory('bomb'),
      'elixirs'  => self::getByCategory('elixir'),
      'mutagens' => self::getByCategory('mutagen'),
      'poisons'  => self::getByCategory('poison'),
      'tools'    => self::getByCategory('tool'),
    ];
  }

}
