<?php

namespace Drupal\dungeoncrawler_content\Service;

/**
 * Handles character language validation and assignment.
 */
class LanguageService {

  /**
   * Canonical language catalog.
   */
  private const LANGUAGE_CATALOG = [
    ['id' => 'Common', 'name' => 'Common', 'typical_speakers' => 'Humans, half-elves, half-orcs', 'script' => 'Common'],
    ['id' => 'Elvish', 'name' => 'Elvish', 'typical_speakers' => 'Elves', 'script' => 'Elvish'],
    ['id' => 'Dwarvish', 'name' => 'Dwarvish', 'typical_speakers' => 'Dwarves', 'script' => 'Dwarvish'],
    ['id' => 'Gnomish', 'name' => 'Gnomish', 'typical_speakers' => 'Gnomes', 'script' => 'Gnomish'],
    ['id' => 'Halfling', 'name' => 'Halfling', 'typical_speakers' => 'Halflings', 'script' => 'Common'],
    ['id' => 'Orcish', 'name' => 'Orcish', 'typical_speakers' => 'Orcs, half-orcs', 'script' => 'Orcish'],
    ['id' => 'Sylvan', 'name' => 'Sylvan', 'typical_speakers' => 'Fey creatures', 'script' => 'Sylvan'],
    ['id' => 'Undercommon', 'name' => 'Undercommon', 'typical_speakers' => 'Underground creatures', 'script' => 'Undercommon'],
    ['id' => 'Draconic', 'name' => 'Draconic', 'typical_speakers' => 'Dragons, kobolds, dragon-touched beings', 'script' => 'Draconic'],
    ['id' => 'Jotun', 'name' => 'Jotun', 'typical_speakers' => 'Giants', 'script' => 'Jotun'],
    ['id' => 'Celestial', 'name' => 'Celestial', 'typical_speakers' => 'Celestials, divine scholars', 'script' => 'Celestial'],
    ['id' => 'Gnoll', 'name' => 'Gnoll', 'typical_speakers' => 'Gnolls', 'script' => 'Common'],
    ['id' => 'Goblin', 'name' => 'Goblin', 'typical_speakers' => 'Goblins', 'script' => 'Goblin'],
    ['id' => 'Terran', 'name' => 'Terran', 'typical_speakers' => 'Earth elementals and underground cultures', 'script' => 'Terran'],
    ['id' => 'Amurrun', 'name' => 'Amurrun', 'typical_speakers' => 'Catfolk', 'script' => 'Amurrun'],
    ['id' => 'Ysoki', 'name' => 'Ysoki', 'typical_speakers' => 'Ratfolk', 'script' => 'Ysoki'],
    ['id' => 'Tengu', 'name' => 'Tengu', 'typical_speakers' => 'Tengu', 'script' => 'Tengu'],
  ];

  /**
   * Legacy and lowercase aliases mapped to canonical catalog IDs.
   */
  private const LANGUAGE_ALIASES = [
    'common' => 'Common',
    'elven' => 'Elvish',
    'elvish' => 'Elvish',
    'dwarven' => 'Dwarvish',
    'dwarvish' => 'Dwarvish',
    'gnomish' => 'Gnomish',
    'halfling' => 'Halfling',
    'orcish' => 'Orcish',
    'sylvan' => 'Sylvan',
    'undercommon' => 'Undercommon',
    'draconic' => 'Draconic',
    'jotun' => 'Jotun',
    'celestial' => 'Celestial',
    'gnoll' => 'Gnoll',
    'goblin' => 'Goblin',
    'terran' => 'Terran',
    'amurrun' => 'Amurrun',
    'ysoki' => 'Ysoki',
    'tengu' => 'Tengu',
  ];

  /**
   * Return the public language catalog.
   */
  public static function getLanguageCatalog(): array {
    return self::LANGUAGE_CATALOG;
  }

  /**
   * Normalize a language ID to the canonical catalog form.
   */
  public static function normalizeLanguageId(string $language_id): ?string {
    $normalized = strtolower(trim($language_id));
    return self::LANGUAGE_ALIASES[$normalized] ?? NULL;
  }

  /**
   * Validate a language ID against the catalog.
   */
  public static function isValidLanguageId(string $language_id): bool {
    return self::normalizeLanguageId($language_id) !== NULL;
  }

  /**
   * Process and validate languages for a character payload.
   */
  public function processLanguages(array $character_data, array $request_data, array $existing_languages = []): array {
    $provided_languages = array_key_exists('languages', $request_data) ? ($request_data['languages'] ?? []) : $existing_languages;
    if (!is_array($provided_languages)) {
      return [
        'success' => FALSE,
        'error' => 'Languages must be an array',
      ];
    }

    $ancestry = $character_data['ancestry'] ?? '';
    $abilities = $character_data['abilities'] ?? [];
    $int_score = (int) ($abilities['int'] ?? 10);
    $int_modifier = $this->calculateModifier($int_score);
    $ancestry_languages = $this->getAncestryLanguages($ancestry);

    $normalized_languages = [];
    foreach ($provided_languages as $lang) {
      if (!is_string($lang)) {
        return [
          'success' => FALSE,
          'error' => 'Language IDs must be strings',
        ];
      }

      $canonical_language = self::normalizeLanguageId($lang);
      if ($canonical_language === NULL) {
        return [
          'success' => FALSE,
          'error' => 'unknown language id: ' . $lang,
        ];
      }

      $normalized_languages[] = $canonical_language;
    }

    $bonus_slots = $this->getBonusLanguageSlots($ancestry, $int_modifier);
    $bonus_pool = $this->getBonusLanguagePool($ancestry);
    $non_ancestry_languages = array_values(array_diff($normalized_languages, $ancestry_languages));
    if (count($non_ancestry_languages) > $bonus_slots) {
      return [
        'success' => FALSE,
        'error' => "Too many bonus languages. INT modifier {$int_modifier} allows {$bonus_slots} bonus language(s), but " . count($non_ancestry_languages) . ' were provided.',
      ];
    }

    if ($bonus_pool !== []) {
      $unknown_bonus_languages = array_values(array_diff($non_ancestry_languages, $bonus_pool));
      if ($unknown_bonus_languages !== []) {
        return [
          'success' => FALSE,
          'error' => 'language not available for selected ancestry: ' . $unknown_bonus_languages[0],
        ];
      }
    }

    $final_languages = array_values(array_unique(array_merge($ancestry_languages, $normalized_languages)));
    usort($final_languages, fn(string $left, string $right): int => strcmp($left, $right));

    return [
      'success' => TRUE,
      'languages' => $final_languages,
    ];
  }

  /**
   * Get ancestry default languages.
   */
  private function getAncestryLanguages(string $ancestry): array {
    if ($ancestry === '') {
      return [];
    }

    $canonical = CharacterManager::resolveAncestryCanonicalName($ancestry);
    $ancestries = CharacterManager::ANCESTRIES;
    if (isset($ancestries[$canonical]['languages']) && is_array($ancestries[$canonical]['languages'])) {
      $normalized = [];
      foreach ($ancestries[$canonical]['languages'] as $language_id) {
        $canonical_language = self::normalizeLanguageId((string) $language_id);
        if ($canonical_language !== NULL) {
          $normalized[] = $canonical_language;
        }
      }
      return array_values(array_unique($normalized));
    }

    return [];
  }

  /**
   * Get ancestry-specific bonus language options.
   */
  private function getBonusLanguagePool(string $ancestry): array {
    if ($ancestry === '') {
      return [];
    }

    $canonical = CharacterManager::resolveAncestryCanonicalName($ancestry);
    if ($canonical === '' || !isset(CharacterManager::ANCESTRIES[$canonical])) {
      return [];
    }

    $ancestry_definition = CharacterManager::ANCESTRIES[$canonical];
    $pool = $ancestry_definition['bonus_language_pool']
      ?? $ancestry_definition['special']['bonus_language_options']
      ?? [];

    if (!is_array($pool) || $pool === []) {
      return [];
    }

    $normalized = [];
    foreach ($pool as $language_id) {
      $canonical_language = self::normalizeLanguageId((string) $language_id);
      if ($canonical_language !== NULL) {
        $normalized[] = $canonical_language;
      }
    }

    return array_values(array_diff(array_unique($normalized), $this->getAncestryLanguages($ancestry)));
  }

  /**
   * Get the number of ancestry-granted bonus language slots.
   */
  private function getBonusLanguageSlots(string $ancestry, int $int_modifier): int {
    if ($ancestry === '' || $int_modifier <= 0) {
      return 0;
    }

    $canonical = CharacterManager::resolveAncestryCanonicalName($ancestry);
    if ($canonical === '' || !isset(CharacterManager::ANCESTRIES[$canonical])) {
      return 0;
    }

    $ancestry_definition = CharacterManager::ANCESTRIES[$canonical];
    $per_int = 0;

    if (isset($ancestry_definition['special']['bonus_language_per_int'])) {
      $per_int = (int) $ancestry_definition['special']['bonus_language_per_int'];
    }
    elseif (($ancestry_definition['bonus_language_source'] ?? '') === 'intelligence_modifier') {
      $per_int = 1;
    }

    return max(0, $int_modifier * $per_int);
  }

  /**
   * Calculate ability modifier from score.
   */
  private function calculateModifier(int $score): int {
    return (int) floor(($score - 10) / 2);
  }

}
