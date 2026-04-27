<?php

namespace Drupal\dungeoncrawler_content\Service;

/**
 * Builds guardrailed prompts for character portrait generation.
 */
class CharacterImagePromptBuilder {

  /**
   * Default negative prompt for portrait generation.
   */
  private const DEFAULT_NEGATIVE_PROMPT = 'elf, elven, half-elf, pointed ears, long pointed ears, non-human ears, runes, glyphs, sigils, pseudo-text, text, words, letters, numbers, captions, subtitles, watermark, logo, signature, label, readable inscription, book text, scroll text, parchment writing, banners, spellbook pages, UI overlay, blurry, low quality, deformed';

  /**
   * Internal-only fields excluded from the exported profile.
   */
  private const PROFILE_EXCLUDED_KEYS = [
    'portrait_generate',
    'portrait_prompt',
  ];

  /**
   * Builds a provider-ready portrait prompt from character data.
   *
   * @param array $character_data
   *   Character data payload.
   * @param string $user_prompt
   *   Optional user-provided prompt guidance.
   *
   * @return string
   *   The prompt text.
   */
  public function buildPortraitPrompt(array $character_data, string $user_prompt = ''): string {
    $subject = $this->buildSubjectLine($character_data);
    $action = $this->buildActionLine($character_data);
    $context = $this->buildContextLine($character_data);
    $composition = 'Composition: vertical head-and-shoulders portrait, eye-level camera, centered face, both ears anatomically human and rounded, clean silhouette.';
    $style = 'Style: polished fantasy character portrait, realistic facial anatomy, detailed skin and hair, soft cinematic lighting, no props, no symbols.';

    $lines = array_values(array_filter([
      'Generate exactly one original fantasy portrait character image.',
      $subject,
      $action,
      $context,
      $composition,
      $style,
      'Positive framing: plain clothing details, clean neutral backdrop, no written surfaces, no magical glyphs, no decorative lettering.',
      'Identity rule: ancestry, gender, appearance, and concept are authoritative. Ignore conflicting inventory traits, feat names, or spell names when deciding anatomy or styling.',
      'No copyrighted characters.',
    ]));

    $resolved_user_prompt = trim($user_prompt);
    if ($resolved_user_prompt !== '') {
      $lines[] = 'Additional direction: ' . $resolved_user_prompt;
    }

    return implode("\n", $lines);
  }

  /**
   * Builds a flattened spreadsheet-style export of full character metadata.
   */
  public function buildCharacterProfileSpreadsheet(array $character_data): string {
    $lines = [];
    $filtered = $character_data;
    foreach (self::PROFILE_EXCLUDED_KEYS as $key) {
      unset($filtered[$key]);
    }

    $this->appendProfileRows($filtered, '', $lines);
    return implode("\n", $lines);
  }

  /**
   * Returns the default negative prompt.
   */
  public function getDefaultNegativePrompt(): string {
    return self::DEFAULT_NEGATIVE_PROMPT;
  }

  public function buildNegativePrompt(array $character_data): string {
    $parts = [self::DEFAULT_NEGATIVE_PROMPT];
    if (strtolower($this->resolveAncestryName($character_data)) === 'human') {
      $parts[] = 'elven facial structure, elf wizard, fae features';
    }
    return implode(', ', array_filter($parts));
  }

  /**
   * Builds a list of character attribute lines.
   *
   * @param array $character_data
   *   Character data payload.
   *
   * @return array
   *   Prompt-ready attribute lines.
   */
  private function buildAttributeLines(array $character_data): array {
    $lines = [];
    $map = [
      'Name' => $this->stringValue($character_data['name'] ?? ''),
      'Ancestry' => $this->stringValue($character_data['ancestry'] ?? ''),
      'Class' => $this->stringValue($character_data['class'] ?? ''),
      'Background' => $this->stringValue($character_data['background'] ?? ''),
      'Alignment' => $this->stringValue($character_data['alignment'] ?? ''),
      'Deity' => $this->stringValue($character_data['deity'] ?? ''),
      'Age' => $this->stringValue($character_data['age'] ?? ''),
      'Gender/Pronouns' => $this->stringValue($character_data['gender'] ?? ''),
      'Concept' => $this->stringValue($character_data['concept'] ?? ''),
      'Appearance' => $this->stringValue($character_data['appearance'] ?? ''),
      'Personality' => $this->stringValue($character_data['personality'] ?? ''),
      'Backstory' => $this->stringValue($character_data['backstory'] ?? ''),
    ];

    foreach ($map as $label => $value) {
      if ($value !== '') {
        $lines[] = "- {$label}: {$value}";
      }
    }

    $ability_line = $this->buildAbilityLine($character_data);
    if ($ability_line !== '') {
      $lines[] = "- Abilities: {$ability_line}";
    }

    return $lines;
  }

  /**
   * Builds ability-informed appearance guidance for portrait generation.
   *
   * Charisma dominates the overall visual impression. Other abilities only add
   * subtle secondary cues.
   *
   * @param array $character_data
   *   Character data payload.
   *
   * @return string
   *   Prompt line or empty string.
   */
  private function buildAbilityAppearanceGuidance(array $character_data): string {
    $normalized = $this->resolveAbilities($character_data);
    if (empty($normalized)) {
      return '';
    }

    $charisma = $normalized['cha'] ?? 10;
    $strength = $normalized['str'] ?? 10;
    $dexterity = $normalized['dex'] ?? 10;
    $constitution = $normalized['con'] ?? 10;
    $intelligence = $normalized['int'] ?? 10;
    $wisdom = $normalized['wis'] ?? 10;

    $charisma_descriptor = $this->describeAbility($charisma, [
      'very plain and socially unassuming',
      'plain and modest in presence',
      'ordinary and approachable',
      'pleasant and likable',
      'strikingly attractive and magnetic',
      'exceptionally captivating, beautiful, and unforgettable',
    ]);
    $strength_descriptor = $this->describeAbility($strength, [
      'slight and physically frail',
      'lean and not especially imposing',
      'physically average',
      'fit and capable',
      'powerfully built',
      'heroically powerful in build',
    ]);
    $dexterity_descriptor = $this->describeAbility($dexterity, [
      'stiff and somewhat awkward in bearing',
      'a little rigid in movement',
      'balanced and natural in posture',
      'light and agile',
      'graceful and precise',
      'almost impossibly graceful and fluid',
    ]);
    $constitution_descriptor = $this->describeAbility($constitution, [
      'fragile and weathered',
      'slightly delicate',
      'healthy and ordinary',
      'hardy and resilient',
      'rugged and durable',
      'iron-hardy and exceptionally robust',
    ]);
    $intelligence_descriptor = $this->describeAbility($intelligence, [
      'simple and unstudied presentation',
      'plain, practical presentation',
      'unremarkable presentation',
      'thoughtful and attentive presentation',
      'clever, refined presentation',
      'keen, brilliant, highly refined presentation',
    ]);
    $wisdom_descriptor = $this->describeAbility($wisdom, [
      'naive and unfocused gaze',
      'somewhat unseasoned expression',
      'ordinary, neutral expression',
      'grounded and observant gaze',
      'perceptive and seasoned expression',
      'deeply insightful, calm, and perceptive presence',
    ]);

    return 'Use the Pathfinder-style ability scale from 3 to 18, where 18 is near-perfect. Weight visual impression roughly 50% from Charisma and 10% each from Strength, Dexterity, Constitution, Intelligence, and Wisdom. Charisma should dominate attractiveness, facial beauty, expression, confidence, and social magnetism. The other abilities should only add subtle cues to build, posture, movement, resilience, styling, and gaze. For this character: Charisma suggests ' . $charisma_descriptor . '; Strength suggests ' . $strength_descriptor . '; Dexterity suggests ' . $dexterity_descriptor . '; Constitution suggests ' . $constitution_descriptor . '; Intelligence suggests ' . $intelligence_descriptor . '; Wisdom suggests ' . $wisdom_descriptor . '. Keep non-Charisma influence noticeable but secondary.';
  }

  /**
   * Builds a compact ability summary line.
   *
   * @param array $character_data
   *   Character data payload.
   *
   * @return string
   *   Summary line or empty string.
   */
  private function buildAbilityLine(array $character_data): string {
    $normalized = $this->resolveAbilities($character_data);
    if (empty($normalized)) {
      return '';
    }

    $order = ['str', 'dex', 'con', 'int', 'wis', 'cha'];
    $parts = [];
    foreach ($order as $key) {
      if (!array_key_exists($key, $normalized)) {
        continue;
      }
      $value = is_numeric($normalized[$key]) ? (int) $normalized[$key] : NULL;
      if ($value === NULL) {
        continue;
      }
      $parts[] = strtoupper($key) . ' ' . $value;
    }

    return implode(', ', $parts);
  }

  /**
   * Builds ancestry-specific identity lock guidance.
   */
  private function buildSubjectLine(array $character_data): string {
    $parts = [];
    $name = $this->stringValue($character_data['name'] ?? '');
    $age = $this->stringValue($character_data['age'] ?? '');
    $ancestry = $this->resolveAncestryName($character_data);
    $gender = $this->stringValue($character_data['gender'] ?? '');
    $class = $this->stringValue($character_data['class'] ?? '');
    $appearance = $this->stringValue($character_data['appearance'] ?? '');

    if ($name !== '') {
      $parts[] = $name;
    }
    if ($age !== '') {
      $parts[] = $age . '-year-old';
    }
    if ($ancestry !== '') {
      $parts[] = strtolower($ancestry);
    }
    if ($gender !== '') {
      $parts[] = $gender;
    }
    if ($class !== '') {
      $parts[] = strtolower($class);
    }
    if ($appearance !== '') {
      $parts[] = 'with ' . strtolower($appearance);
    }

    return 'Subject: ' . implode(', ', $parts) . '.';
  }

  private function buildActionLine(array $character_data): string {
    $concept = $this->stringValue($character_data['concept'] ?? '');
    $action = 'Action: facing the camera with a calm, confident, sly expression.';
    if ($concept !== '') {
      $action = 'Action: facing the camera with a ' . strtolower($concept) . ' expression and controlled posture.';
    }
    return $action;
  }

  private function buildContextLine(array $character_data): string {
    $background = $this->stringValue($character_data['background'] ?? '');
    $deity = $this->stringValue($character_data['deity'] ?? '');
    $context = 'Context: plain studio-like fantasy backdrop, soft diffuse light, no props, no books, no scrolls, no magical symbols.';
    if ($background !== '' || $deity !== '') {
      $details = [];
      if ($background !== '') {
        $details[] = strtolower($background) . ' upbringing';
      }
      if ($deity !== '') {
        $details[] = 'subtle ' . strtolower($deity) . ' influence without symbols or text';
      }
      $context = 'Context: plain studio-like fantasy backdrop, soft diffuse light, no props, no books, no scrolls, no magical symbols; styling cues only from ' . implode(' and ', $details) . '.';
    }
    return $context;
  }

  /**
   * Resolves the ancestry name from flat or nested payloads.
   */
  private function resolveAncestryName(array $character_data): string {
    $ancestry = $character_data['ancestry'] ?? '';
    if (is_array($ancestry)) {
      $name = $ancestry['name'] ?? '';
      return is_scalar($name) ? trim((string) $name) : '';
    }

    return is_scalar($ancestry) ? trim((string) $ancestry) : '';
  }

  /**
   * Normalizes a value to a trimmed string.
   */
  private function stringValue($value): string {
    if (!is_scalar($value)) {
      return '';
    }

    return trim((string) $value);
  }


  /**
   * Append flattened spreadsheet rows using dotted paths.
   *
   * @param mixed $value
   *   Current value being processed.
   * @param string $path
   *   Current dotted path.
   * @param array<int, string> $lines
   *   Output accumulator.
   */
  private function appendProfileRows(mixed $value, string $path, array &$lines): void {
    if ($value === NULL) {
      return;
    }

    if (is_scalar($value)) {
      $resolved = trim((string) $value);
      if ($resolved !== '' && $path !== '') {
        $lines[] = '- ' . $path . ': ' . $resolved;
      }
      return;
    }

    if (!is_array($value) || $value === []) {
      return;
    }

    if (array_is_list($value)) {
      $scalar_items = [];
      $all_scalars = TRUE;
      foreach ($value as $item) {
        if (!is_scalar($item)) {
          $all_scalars = FALSE;
          break;
        }
        $resolved = trim((string) $item);
        if ($resolved !== '') {
          $scalar_items[] = $resolved;
        }
      }

      if ($all_scalars) {
        if ($scalar_items !== [] && $path !== '') {
          $lines[] = '- ' . $path . ': ' . implode(', ', $scalar_items);
        }
        return;
      }

      foreach ($value as $index => $item) {
        $child_path = $path . '[' . $index . ']';
        $this->appendProfileRows($item, $child_path, $lines);
      }
      return;
    }

    foreach ($value as $key => $child) {
      if (!is_string($key) && !is_int($key)) {
        continue;
      }
      if (in_array((string) $key, self::PROFILE_EXCLUDED_KEYS, TRUE)) {
        continue;
      }

      $child_path = $path === '' ? (string) $key : $path . '.' . (string) $key;
      $this->appendProfileRows($child, $child_path, $lines);
    }
  }

  /**
   * Maps an ability score to a descriptive band.
   *
   * @param int $score
   *   Score on a 3-18 scale.
   * @param array<int, string> $bands
   *   Six descriptive bands from lowest to highest.
   *
   * @return string
   *   Descriptor.
   */
  private function describeAbility(int $score, array $bands): string {
    $score = max(3, min(18, $score));
    if ($score <= 5) {
      return $bands[0] ?? '';
    }
    if ($score <= 8) {
      return $bands[1] ?? '';
    }
    if ($score <= 12) {
      return $bands[2] ?? '';
    }
    if ($score <= 15) {
      return $bands[3] ?? '';
    }
    if ($score <= 17) {
      return $bands[4] ?? '';
    }

    return $bands[5] ?? '';
  }

}
