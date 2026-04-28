<?php

namespace Drupal\dungeoncrawler_content\Service;

use Drupal\dungeoncrawler_content\Controller\LanguagesController;

/**
 * Handles character language validation and assignment.
 */
class LanguageService {

  /**
   * Process and validate languages for a character payload.
   */
  public function processLanguages(array &$character_data, array $request_data): array {
    $provided_languages = $request_data['languages'] ?? [];
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

    foreach ($provided_languages as $lang) {
      if (!is_string($lang)) {
        return [
          'success' => FALSE,
          'error' => 'Language IDs must be strings',
        ];
      }
      if (!LanguagesController::isValidLanguageId($lang)) {
        return [
          'success' => FALSE,
          'error' => "Unknown language ID: $lang",
        ];
      }
    }

    $bonus_slots = max(0, $int_modifier);
    $non_ancestry_languages = array_diff($provided_languages, $ancestry_languages);
    if (count($non_ancestry_languages) > $bonus_slots) {
      return [
        'success' => FALSE,
        'error' => "Too many bonus languages. INT modifier {$int_modifier} allows {$bonus_slots} bonus language(s), but " . count($non_ancestry_languages) . ' were provided.',
      ];
    }

    $final_languages = array_unique(array_merge($ancestry_languages, $provided_languages));
    sort($final_languages);

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
      return $ancestries[$canonical]['languages'];
    }

    return [];
  }

  /**
   * Calculate ability modifier from score.
   */
  private function calculateModifier(int $score): int {
    return (int) floor(($score - 10) / 2);
  }

}
