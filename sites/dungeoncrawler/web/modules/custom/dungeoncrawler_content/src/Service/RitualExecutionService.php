<?php

namespace Drupal\dungeoncrawler_content\Service;

/**
 * Ritual execution service (dc-cr-rituals).
 *
 * Owns:
 * - Ritual execution state management (started, in_progress, completed)
 * - Caster and secondary caster validation
 * - Skill check resolution (not actual roll mechanics, but setup/validation)
 * - Long-duration ritual progress tracking
 * - Ritual outcome resolution (success, failure, critical_failure)
 * - Prevention of ritual execution through normal spellcasting action flow
 *
 * Does NOT own:
 * - Actual skill check rolls (that is CharacterCalculator or Combat system)
 * - UI presentation (that is Controller/Form layer)
 * - Persistent storage (that is Drupal entity system)
 */
class RitualExecutionService {

  // -----------------------------------------------------------------------
  // Constants
  // -----------------------------------------------------------------------

  /**
   * Ritual execution states.
   */
  const STATES = [
    'pending' => 'pending',           // Not yet started
    'in_progress' => 'in_progress',   // Ritual is actively being cast
    'completed' => 'completed',       // Ritual completed
    'failed' => 'failed',             // Ritual failed
    'cancelled' => 'cancelled',       // Ritual was cancelled
  ];

  /**
   * Ritual outcome types.
   */
  const OUTCOMES = [
    'success' => 'success',
    'failure' => 'failure',
    'critical_failure' => 'critical_failure',
  ];

  /**
   * Ritual consequence types (for tracking what needs manual vs automated verification).
   */
  const CONSEQUENCE_TYPES = [
    'automatic' => 'automatic',       // Automated consequence (e.g., create item, modify stat)
    'narrative' => 'narrative',       // Narrative/manual consequence (requires GM adjudication)
    'mixed' => 'mixed',               // Both automatic and narrative elements
  ];

  // -----------------------------------------------------------------------
  // Service Dependencies
  // -----------------------------------------------------------------------

  /**
   * Ritual catalog service.
   *
   * @var \Drupal\dungeoncrawler_content\Service\RitualCatalogService
   */
  protected RitualCatalogService $ritualCatalog;

  /**
   * Character manager (for accessing character data and validating casters).
   *
   * @var \Drupal\dungeoncrawler_content\Service\CharacterManager
   */
  protected CharacterManager $characterManager;

  public function __construct(RitualCatalogService $ritual_catalog, CharacterManager $character_manager) {
    $this->ritualCatalog = $ritual_catalog;
    $this->characterManager = $character_manager;
  }

  // -----------------------------------------------------------------------
  // Public API
  // -----------------------------------------------------------------------

  /**
   * Initialize a ritual execution.
   *
   * Creates a ritual execution context (not persistent storage).
   * Does NOT consume spell slots or otherwise affect character state.
   *
   * @param string $ritual_id
   *   The ritual ID to execute.
   * @param int $primary_caster_id
   *   The character ID of the primary caster.
   * @param array $secondary_caster_ids
   *   Character IDs of secondary casters (may be empty).
   *
   * @return array
   *   An array with keys:
   *   - 'valid': bool
   *   - 'ritual_id': string
   *   - 'state': 'pending'
   *   - 'primary_caster_id': int
   *   - 'secondary_caster_ids': array
   *   - 'casting_duration': string (e.g., '1 hour')
   *   - 'skill_checks': array of required skill checks
   *   - 'errors': array of validation errors (if invalid)
   */
  public function initializeRitual(string $ritual_id, int $primary_caster_id, array $secondary_caster_ids = []): array {
    $ritual = $this->ritualCatalog->getRitual($ritual_id);
    if (!$ritual) {
      return [
        'valid' => FALSE,
        'errors' => ["Ritual not found: {$ritual_id}"],
      ];
    }

    $errors = [];

    // Validate primary caster exists.
    if ($primary_caster_id <= 0) {
      $errors[] = "Invalid primary caster ID: {$primary_caster_id}";
    }

    // Validate secondary caster count matches ritual requirements.
    $required_secondary = $ritual['secondary_casters'] ?? 0;
    if (count($secondary_caster_ids) < $required_secondary) {
      $errors[] = "Insufficient secondary casters. Required: {$required_secondary}, provided: " . count($secondary_caster_ids);
    }

    if (!empty($errors)) {
      return [
        'valid' => FALSE,
        'ritual_id' => $ritual_id,
        'errors' => $errors,
      ];
    }

    // Build the execution context.
    return [
      'valid' => TRUE,
      'ritual_id' => $ritual_id,
      'state' => 'pending',
      'primary_caster_id' => $primary_caster_id,
      'secondary_caster_ids' => $secondary_caster_ids,
      'ritual_name' => $ritual['name'] ?? '',
      'casting_duration' => $ritual['casting_time'] ?? '',
      'level' => $ritual['level'] ?? 0,
      'rarity' => $ritual['rarity'] ?? 'common',
      'traits' => $ritual['traits'] ?? [],
      'primary_check' => $ritual['primary_check'] ?? NULL,
      'secondary_checks' => $ritual['secondary_checks'] ?? [],
      'skill_checks' => $this->buildSkillCheckList($ritual),
      'errors' => [],
    ];
  }

  /**
   * Validate that a ritual cannot be cast through normal spellcasting flow.
   *
   * This is a preventative check: if a character tries to use a normal
   * spell-action to cast a ritual, this check should fail.
   *
   * @param string $ritual_id
   *   The ritual ID.
   *
   * @return array
   *   An array with:
   *   - 'is_ritual': bool (TRUE)
   *   - 'can_use_spell_flow': bool (FALSE - rituals cannot use normal spell flow)
   *   - 'reason': string explanation
   */
  public function checkRitualVsSpellFlow(string $ritual_id): array {
    $ritual = $this->ritualCatalog->getRitual($ritual_id);
    if (!$ritual) {
      return [
        'is_ritual' => FALSE,
        'can_use_spell_flow' => FALSE,
        'reason' => 'Ritual not found',
      ];
    }

    return [
      'is_ritual' => TRUE,
      'can_use_spell_flow' => FALSE,
      'reason' => 'Rituals use a separate execution flow and do not consume spell slots.',
    ];
  }

  /**
   * Validate caster skill proficiency for a ritual check.
   *
   * @param int $caster_id
   *   The character ID of the caster.
   * @param array $skill_check
   *   The skill check requirement (keys: skill, min_proficiency).
   *
   * @return array
   *   An array with:
   *   - 'valid': bool
   *   - 'caster_skill_level': string or 'untrained'
   *   - 'min_required': string
   *   - 'meets_requirement': bool
   *   - 'errors': array
   */
  public function validateCasterSkillProficiency(int $caster_id, array $skill_check): array {
    if (empty($skill_check['skill']) || empty($skill_check['min_proficiency'])) {
      return [
        'valid' => FALSE,
        'errors' => ['Invalid skill check structure'],
      ];
    }

    $skill = $skill_check['skill'];
    $min_required = $skill_check['min_proficiency'];

    // In a full implementation, we would look up the caster's actual skill proficiency.
    // For now, we validate the structure and return a placeholder.
    $proficiency_levels = RitualCatalogService::PROFICIENCY_LEVELS;
    $min_required_rank = array_search($min_required, $proficiency_levels, TRUE);

    if ($min_required_rank === FALSE) {
      return [
        'valid' => FALSE,
        'errors' => ["Invalid proficiency requirement: {$min_required}"],
      ];
    }

    // Placeholder: In real implementation, fetch caster's actual proficiency.
    $caster_proficiency = 'trained'; // TODO: Look up actual proficiency
    $caster_rank = array_search($caster_proficiency, $proficiency_levels, TRUE);

    return [
      'valid' => TRUE,
      'caster_skill_level' => $caster_proficiency,
      'min_required' => $min_required,
      'meets_requirement' => $caster_rank >= $min_required_rank,
      'skill' => $skill,
      'errors' => [],
    ];
  }

  /**
   * Resolve a ritual outcome and return consequences.
   *
   * @param string $ritual_id
   *   The ritual ID.
   * @param string $outcome
   *   One of 'success', 'failure', 'critical_failure'.
   *
   * @return array
   *   An array with:
   *   - 'valid': bool
   *   - 'ritual_id': string
   *   - 'outcome': string (success/failure/critical_failure)
   *   - 'consequence_type': string (automatic/narrative/mixed)
   *   - 'description': string explaining the outcome
   *   - 'automated_consequences': array of what the system will do
   *   - 'narrative_elements': array of what the GM must adjudicate
   */
  public function resolveRitualOutcome(string $ritual_id, string $outcome): array {
    if (!in_array($outcome, array_values(self::OUTCOMES), TRUE)) {
      return [
        'valid' => FALSE,
        'errors' => ["Invalid outcome: {$outcome}"],
      ];
    }

    $ritual = $this->ritualCatalog->getRitual($ritual_id);
    if (!$ritual) {
      return [
        'valid' => FALSE,
        'errors' => ["Ritual not found: {$ritual_id}"],
      ];
    }

    $consequence_type = $this->determineConsequenceType($ritual, $outcome);

    return [
      'valid' => TRUE,
      'ritual_id' => $ritual_id,
      'ritual_name' => $ritual['name'] ?? '',
      'outcome' => $outcome,
      'consequence_type' => $consequence_type,
      'description' => $this->getOutcomeDescription($ritual, $outcome),
      'automated_consequences' => $this->getAutomatedConsequences($ritual, $outcome),
      'narrative_elements' => $this->getNarrativeElements($ritual, $outcome),
    ];
  }

  /**
   * Check if a ritual can preserve progress across multiple sessions.
   *
   * Long-duration rituals (hours to days) need progress tracking.
   *
   * @param string $ritual_id
   *   The ritual ID.
   *
   * @return array
   *   An array with:
   *   - 'supports_progress_tracking': bool
   *   - 'casting_duration': string
   *   - 'estimated_actions': int (approximate number of in-game actions required)
   */
  public function getProgressTrackingCapability(string $ritual_id): array {
    $ritual = $this->ritualCatalog->getRitual($ritual_id);
    if (!$ritual) {
      return [
        'supports_progress_tracking' => FALSE,
        'casting_duration' => '',
      ];
    }

    $casting_duration = $ritual['casting_time'] ?? '1 hour';
    $supports_tracking = strpos(strtolower($casting_duration), 'hour') !== FALSE
      || strpos(strtolower($casting_duration), 'day') !== FALSE;

    return [
      'supports_progress_tracking' => $supports_tracking,
      'casting_duration' => $casting_duration,
      'ritual_name' => $ritual['name'] ?? '',
    ];
  }

  // -----------------------------------------------------------------------
  // Private Methods
  // -----------------------------------------------------------------------

  /**
   * Build a list of all skill checks required for a ritual.
   *
   * @param array $ritual
   *   The ritual definition.
   *
   * @return array
   *   Array of skill check requirements (primary + secondary).
   */
  protected function buildSkillCheckList(array $ritual): array {
    $checks = [];

    if (!empty($ritual['primary_check'])) {
      $checks[] = array_merge(['type' => 'primary'], $ritual['primary_check']);
    }

    if (!empty($ritual['secondary_checks'])) {
      foreach ($ritual['secondary_checks'] as $idx => $check) {
        $checks[] = array_merge(['type' => 'secondary', 'index' => $idx], $check);
      }
    }

    return $checks;
  }

  /**
   * Determine whether a ritual outcome has automatic, narrative, or mixed consequences.
   *
   * @param array $ritual
   *   The ritual definition.
   * @param string $outcome
   *   The ritual outcome.
   *
   * @return string
   *   One of 'automatic', 'narrative', 'mixed'.
   */
  protected function determineConsequenceType(array $ritual, string $outcome): string {
    // For now, all rituals are assumed to have mixed consequences
    // (some automated effects, some narrative GM decisions).
    // In a full implementation, ritual definitions could specify this.
    return 'mixed';
  }

  /**
   * Get a description of the ritual outcome.
   *
   * @param array $ritual
   *   The ritual definition.
   * @param string $outcome
   *   The ritual outcome.
   *
   * @return string
   *   Description of what happened.
   */
  protected function getOutcomeDescription(array $ritual, string $outcome): string {
    $name = $ritual['name'] ?? 'Unknown Ritual';
    $base_desc = $ritual['description'] ?? '';

    switch ($outcome) {
      case 'success':
        return "The {$name} ritual completes successfully. {$base_desc}";

      case 'failure':
        return "The {$name} ritual fails. The casting time and components are lost.";

      case 'critical_failure':
        return "The {$name} ritual critically fails with serious consequences. The casting time and components are lost; unwanted effects may occur.";

      default:
        return "The {$name} ritual resolves with outcome: {$outcome}";
    }
  }

  /**
   * Get automated consequences of a ritual outcome.
   *
   * @param array $ritual
   *   The ritual definition.
   * @param string $outcome
   *   The ritual outcome.
   *
   * @return array
   *   List of automated actions to take.
   */
  protected function getAutomatedConsequences(array $ritual, string $outcome): array {
    $consequences = [];

    // All outcomes consume casting time and components.
    $consequences[] = "Consume ritual component cost: " . ($ritual['cost'] ?? 'none');
    $consequences[] = "Consume casting time: " . ($ritual['casting_time'] ?? '1 hour');

    // Success-specific consequences.
    if ($outcome === 'success') {
      // Placeholder: specific ritual effects would be defined per ritual.
      $consequences[] = "Apply ritual effect: " . ($ritual['name'] ?? 'Unknown');
    }

    return $consequences;
  }

  /**
   * Get narrative elements that the GM must adjudicate.
   *
   * @param array $ritual
   *   The ritual definition.
   * @param string $outcome
   *   The ritual outcome.
   *
   * @return array
   *   List of narrative decisions for the GM.
   */
  protected function getNarrativeElements(array $ritual, string $outcome): array {
    $elements = [];

    switch ($outcome) {
      case 'success':
        $elements[] = "Describe the successful ritual completion.";
        $elements[] = "Determine whether secondary casters receive any benefit or bonding effect.";
        break;

      case 'failure':
        $elements[] = "Determine what went wrong narratively.";
        $elements[] = "Decide if secondary casters realize the failure.";
        break;

      case 'critical_failure':
        $elements[] = "Describe the critical failure and potential complications.";
        $elements[] = "Determine unintended magical side effects or backlash.";
        break;
    }

    return $elements;
  }

}
