<?php

namespace Drupal\dungeoncrawler_content\Service;

/**
 * Ritual catalog and rules service (dc-cr-rituals).
 *
 * Owns:
 * - Ritual data model and validation
 * - Ritual catalog queries and filtering
 * - Ritual structure verification (casting time, caster requirements, skill checks)
 * - Separation from standard spellcasting mechanics (no spell slot consumption)
 *
 * Rituals are long-form magic requiring extended casting times, skill checks,
 * and often multiple participants. They have narrative-scale consequences.
 */
class RitualCatalogService {

  // -----------------------------------------------------------------------
  // Constants
  // -----------------------------------------------------------------------

  /**
   * Ritual casting time values (textual representations).
   * Can range from "1 hour" to "1 day" or longer in extraordinary cases.
   */
  const RITUAL_CASTING_TIMES = [
    '1_hour' => '1 hour',
    '2_hours' => '2 hours',
    '4_hours' => '4 hours',
    '8_hours' => '8 hours',
    '1_day' => '1 day',
    '2_days' => '2 days',
    '3_days' => '3 days',
    '7_days' => '7 days',
    '30_days' => '30 days',
  ];

  /**
   * Proficiency levels for skill checks (PF2e: untrained, trained, expert, master, legendary).
   */
  const PROFICIENCY_LEVELS = ['untrained', 'trained', 'expert', 'master', 'legendary'];

  /**
   * Skill names used in ritual skill checks.
   */
  const SKILLS = [
    'Acrobatics',
    'Arcana',
    'Athletics',
    'Crafting',
    'Deception',
    'Diplomacy',
    'Intimidation',
    'Lore',
    'Medicine',
    'Nature',
    'Occultism',
    'Performance',
    'Religion',
    'Society',
    'Stealth',
    'Survival',
    'Thievery',
  ];

  /**
   * Possible ritual outcomes.
   */
  const OUTCOMES = ['success', 'failure', 'critical_failure'];

  // -----------------------------------------------------------------------
  // Ritual registry
  // -----------------------------------------------------------------------

  /**
   * In-process ritual registry.
   *
   * @var array<string, array>
   */
  protected array $rituals = [];

  /**
   * CharacterManager instance (for access to RITUALS constant).
   *
   * @var \Drupal\dungeoncrawler_content\Service\CharacterManager
   */
  protected CharacterManager $characterManager;

  public function __construct(CharacterManager $character_manager) {
    $this->characterManager = $character_manager;
    $this->seedRitualCatalog();
  }

  // -----------------------------------------------------------------------
  // Public API
  // -----------------------------------------------------------------------

  /**
   * Look up a ritual by ID.
   *
   * @param string $ritual_id
   *   The ritual ID.
   *
   * @return array|null
   *   The ritual definition array, or NULL if not found.
   */
  public function getRitual(string $ritual_id): ?array {
    return $this->rituals[$ritual_id] ?? NULL;
  }

  /**
   * List all rituals, optionally filtered.
   *
   * @param array $filters
   *   Supported keys:
   *   - 'level': exact ritual level
   *   - 'rarity': 'common', 'uncommon', 'rare', or 'unique'
   *   - 'book_id': 'crb', 'apg', etc.
   *   - 'traits': array of trait names (matches if ANY trait matches)
   *   - 'search': case-insensitive substring search on name/description
   *
   * @return array
   *   List of ritual arrays matching the filters.
   */
  public function listRituals(array $filters = []): array {
    $results = $this->rituals;

    if (!empty($filters['level'])) {
      $results = array_filter($results, fn($r) => $r['level'] === $filters['level']);
    }

    if (!empty($filters['rarity'])) {
      $results = array_filter($results, fn($r) => $r['rarity'] === $filters['rarity']);
    }

    if (!empty($filters['book_id'])) {
      $results = array_filter($results, fn($r) => $r['book_id'] === $filters['book_id']);
    }

    if (!empty($filters['traits']) && is_array($filters['traits'])) {
      $required_traits = array_map('strtolower', $filters['traits']);
      $results = array_filter($results, function ($r) use ($required_traits) {
        $ritual_traits = array_map('strtolower', $r['traits'] ?? []);
        return (bool)array_intersect($required_traits, $ritual_traits);
      });
    }

    if (!empty($filters['search'])) {
      $search = strtolower($filters['search']);
      $results = array_filter($results, function ($r) use ($search) {
        $name = strtolower($r['name'] ?? '');
        $desc = strtolower($r['description'] ?? '');
        return strpos($name, $search) !== FALSE || strpos($desc, $search) !== FALSE;
      });
    }

    return array_values($results);
  }

  /**
   * Validate a ritual definition structure.
   *
   * @param array $ritual
   *   The ritual definition to validate.
   *
   * @return array
   *   An array with keys:
   *   - 'valid': bool
   *   - 'errors': array of error messages (if invalid)
   */
  public function validateRitual(array $ritual): array {
    $errors = [];

    // Required fields.
    $required_fields = ['id', 'name', 'level', 'casting_time', 'primary_check', 'description'];
    foreach ($required_fields as $field) {
      if (empty($ritual[$field])) {
        $errors[] = "Missing required field: {$field}";
      }
    }

    // Validate level (should be 1-10 in PF2e).
    if (!empty($ritual['level']) && (!is_int($ritual['level']) || $ritual['level'] < 1 || $ritual['level'] > 10)) {
      $errors[] = "Level must be an integer between 1 and 10.";
    }

    // Validate primary_check structure.
    if (!empty($ritual['primary_check'])) {
      $check_errors = $this->validateSkillCheck($ritual['primary_check']);
      if (!empty($check_errors)) {
        $errors = array_merge($errors, $check_errors);
      }
    }

    // Validate secondary_checks if present.
    if (!empty($ritual['secondary_checks']) && is_array($ritual['secondary_checks'])) {
      foreach ($ritual['secondary_checks'] as $idx => $check) {
        $check_errors = $this->validateSkillCheck($check);
        if (!empty($check_errors)) {
          $errors[] = "Secondary check $idx: " . implode('; ', $check_errors);
        }
      }
    }

    // Validate rarity.
    if (!empty($ritual['rarity']) && !in_array($ritual['rarity'], ['common', 'uncommon', 'rare', 'unique'], TRUE)) {
      $errors[] = "Rarity must be one of: common, uncommon, rare, unique.";
    }

    // Validate casting_time.
    if (!empty($ritual['casting_time']) && !is_string($ritual['casting_time'])) {
      $errors[] = "Casting time must be a string (e.g., '1 hour', '1 day').";
    }

    // Validate secondary_casters.
    if (isset($ritual['secondary_casters']) && (!is_int($ritual['secondary_casters']) || $ritual['secondary_casters'] < 0)) {
      $errors[] = "Secondary casters must be a non-negative integer.";
    }

    return [
      'valid' => empty($errors),
      'errors' => $errors,
    ];
  }

  /**
   * Get all available skills for ritual checks.
   *
   * @return array
   *   List of skill names.
   */
  public function getAvailableSkills(): array {
    return self::SKILLS;
  }

  /**
   * Get all available proficiency levels.
   *
   * @return array
   *   List of proficiency level names.
   */
  public function getAvailableProficiencies(): array {
    return self::PROFICIENCY_LEVELS;
  }

  // -----------------------------------------------------------------------
  // Private Methods
  // -----------------------------------------------------------------------

  /**
   * Seed the ritual catalog with definitions from CharacterManager::RITUALS.
   */
  protected function seedRitualCatalog(): void {
    $ritual_data = CharacterManager::RITUALS;
    foreach ($ritual_data as $ritual) {
      if (!empty($ritual['id'])) {
        $this->rituals[$ritual['id']] = $ritual;
      }
    }
  }

  /**
   * Validate a single skill check structure.
   *
   * @param array $check
   *   A skill check array with keys: skill, min_proficiency.
   *
   * @return array
   *   Error messages, if any.
   */
  protected function validateSkillCheck(array $check): array {
    $errors = [];

    if (empty($check['skill'])) {
      $errors[] = "Skill check missing 'skill' field.";
    } elseif (!in_array($check['skill'], self::SKILLS, TRUE)) {
      $errors[] = "Unknown skill: {$check['skill']}";
    }

    if (empty($check['min_proficiency'])) {
      $errors[] = "Skill check missing 'min_proficiency' field.";
    } elseif (!in_array($check['min_proficiency'], self::PROFICIENCY_LEVELS, TRUE)) {
      $errors[] = "Unknown proficiency level: {$check['min_proficiency']}";
    }

    return $errors;
  }

}
