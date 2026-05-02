<?php

namespace Drupal\dungeoncrawler_content\Service;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;

/**
 * Generates and persists sprite images for dungeon objects on demand.
 *
 * Checks GeneratedImageRepository for existing sprites keyed by sprite_id.
 * When none exist, generates one via the image generation integration layer,
 * persists the result, and returns the URL.
 */
class SpriteGenerationService {

  /**
   * Image generation integration service.
   */
  protected ImageGenerationIntegrationService $integrationService;

  /**
   * Generated image repository.
   */
  protected GeneratedImageRepository $generatedImageRepository;

  /**
   * Logger channel.
   */
  protected $logger;

  /**
   * Constructs the service.
   */
  public function __construct(
    ImageGenerationIntegrationService $integration_service,
    GeneratedImageRepository $generated_image_repository,
    LoggerChannelFactoryInterface $logger_factory
  ) {
    $this->integrationService = $integration_service;
    $this->generatedImageRepository = $generated_image_repository;
    $this->logger = $logger_factory->get('dungeoncrawler_content');
  }

  /**
   * Looks up an existing sprite URL without generating.
   *
   * Central lookup method — all sprite read paths should delegate here
   * instead of querying GeneratedImageRepository directly.
   *
   * @param string $sprite_id
   *   The sprite identifier.
   * @param int|null $campaign_id
   *   Campaign scope (falls back to global if not found).
   *
   * @return string|null
   *   Resolved client URL, or NULL if no sprite exists.
   */
  public function lookupSprite(string $sprite_id, ?int $campaign_id = NULL): ?string {
    if (trim($sprite_id) === '') {
      return NULL;
    }
    return $this->findExistingSprite($sprite_id, $campaign_id);
  }

  /**
   * Bulk lookup of existing sprite URLs (no generation).
   *
   * Resolves all provided sprite_ids in one query per scope
   * (campaign + global fallback). Callers that need lookup-only
   * semantics should use this instead of resolveBatch().
   *
   * @param array<string> $sprite_ids
   *   Sprite identifiers to look up.
   * @param int|null $campaign_id
   *   Campaign scope (falls back to global for missing sprites).
   *
   * @return array<string, string|null>
   *   Map of sprite_id => URL (null when no image exists).
   */
  public function lookupSprites(array $sprite_ids, ?int $campaign_id = NULL): array {
    $clean = array_values(array_unique(array_filter(array_map('trim', $sprite_ids))));
    if (empty($clean)) {
      return [];
    }

    $urls = [];

    // Bulk query: campaign-scoped.
    $rows = $this->generatedImageRepository->loadImagesForObjects(
      'dc_dungeon_sprites', $clean, $campaign_id, 'sprite', 'original'
    );
    foreach ($clean as $sid) {
      if (isset($rows[$sid])) {
        $urls[$sid] = $this->generatedImageRepository->resolveClientUrl($rows[$sid]);
      }
    }

    // Bulk query: global fallback for any still-missing IDs.
    $missing = array_values(array_diff($clean, array_keys($urls)));
    if (!empty($missing) && $campaign_id !== NULL) {
      $global = $this->generatedImageRepository->loadImagesForObjects(
        'dc_dungeon_sprites', $missing, NULL, 'sprite', 'original'
      );
      foreach ($missing as $sid) {
        if (isset($global[$sid])) {
          $urls[$sid] = $this->generatedImageRepository->resolveClientUrl($global[$sid]);
        }
      }
    }

    return $urls;
  }

  /**
   * Resolves a sprite URL for an object, generating if needed.
   *
   * @param string $sprite_id
   *   The sprite identifier (e.g. "door_wood_tavern").
   * @param array $object_definition
   *   Full object definition from dungeon data.
   * @param int|null $campaign_id
   *   Campaign context.
   * @param int $owner_uid
   *   Requesting user id.
   * @param array $options
   *   Optional overrides: provider, style, aspect_ratio, force_regenerate.
   *
   * @return array{url: string|null, generated: bool, cached: bool, error: string|null}
   *   Result with URL and status flags.
   */
  public function resolveSprite(string $sprite_id, array $object_definition = [], ?int $campaign_id = NULL, int $owner_uid = 0, array $options = []): array {
    if (trim($sprite_id) === '') {
      return ['url' => NULL, 'generated' => FALSE, 'cached' => FALSE, 'error' => 'empty_sprite_id'];
    }

    $force = !empty($options['force_regenerate']);

    // Check for existing sprite image.
    if (!$force) {
      $existing_url = $this->findExistingSprite($sprite_id, $campaign_id);
      if ($existing_url !== NULL) {
        return ['url' => $existing_url, 'generated' => FALSE, 'cached' => TRUE, 'error' => NULL];
      }
    }

    // Generate a new sprite.
    return $this->generateAndPersist($sprite_id, $object_definition, $campaign_id, $owner_uid, $options);
  }

  /**
   * Resolves sprite URLs for multiple objects in batch with status flags.
   *
   * Supports both the primary visual.sprite_id and directional variant maps
   * such as visual.sprite_variants, visual.directional_sprites,
   * visual.orientation_sprites, and visual.facing_sprites.
   *
   * @param array $object_definitions
   *   Keyed by object_id, each containing sprite metadata.
   * @param int|null $campaign_id
   *   Campaign context.
   * @param int $owner_uid
   *   Requesting user id.
   *
   * @return array<string, array{url: string|null, generated: bool, cached: bool, error: string|null}>
   *   Map of sprite_id => status payload.
   */
  public function resolveBatchDetailed(array $object_definitions, ?int $campaign_id = NULL, int $owner_uid = 0): array {
    $requests = [];
    foreach ($object_definitions as $object_id => $def) {
      foreach ($this->extractSpriteRequests($def) as $sprite_id => $request) {
        if (!isset($requests[$sprite_id])) {
          $requests[$sprite_id] = $request;
        }
      }
    }

    if (empty($requests)) {
      return [];
    }

    $results = [];
    $existing = $this->lookupSprites(array_keys($requests), $campaign_id);
    foreach ($existing as $sid => $url) {
      if ($url !== NULL) {
        $results[$sid] = [
          'url' => $url,
          'generated' => FALSE,
          'cached' => TRUE,
          'error' => NULL,
        ];
      }
    }

    foreach ($requests as $sid => $request) {
      if (isset($results[$sid])) {
        continue;
      }

      $results[$sid] = $this->generateAndPersist(
        $sid,
        $request['definition'],
        $campaign_id,
        $owner_uid,
        $request['options']
      );
    }

    return $results;
  }

  /**
   * Resolves sprite URLs for multiple objects in batch.
   *
   * Returns a map of sprite_id => url. Only generates missing sprites.
   *
   * @param array $object_definitions
   *   Keyed by object_id, each containing visual.sprite_id.
   * @param int|null $campaign_id
   *   Campaign context.
   * @param int $owner_uid
   *   Requesting user id.
   *
   * @return array<string, string|null>
   *   Map of sprite_id => URL (null if generation failed).
   */
  public function resolveBatch(array $object_definitions, ?int $campaign_id = NULL, int $owner_uid = 0): array {
    $resolved = $this->resolveBatchDetailed($object_definitions, $campaign_id, $owner_uid);
    $sprite_map = [];
    foreach ($resolved as $sprite_id => $result) {
      $sprite_map[$sprite_id] = $result['url'];
    }
    return $sprite_map;
  }

  /**
   * Looks up an existing sprite in the repository.
   */
  protected function findExistingSprite(string $sprite_id, ?int $campaign_id): ?string {
    $images = $this->generatedImageRepository->loadImagesForObject(
      'dc_dungeon_sprites',
      $sprite_id,
      $campaign_id,
      'sprite',
      'original'
    );

    if (empty($images)) {
      // Also check without campaign scope (global sprites).
      $images = $this->generatedImageRepository->loadImagesForObject(
        'dc_dungeon_sprites',
        $sprite_id,
        NULL,
        'sprite',
        'original'
      );
    }

    if (empty($images)) {
      return NULL;
    }

    return $this->generatedImageRepository->resolveClientUrl($images[0]);
  }

  /**
   * Generates a sprite and persists it.
   */
  protected function generateAndPersist(string $sprite_id, array $object_definition, ?int $campaign_id, int $owner_uid, array $options): array {
    $prompt = $this->buildSpritePrompt($sprite_id, $object_definition, $options);

    $payload = [
      'prompt' => $prompt,
      'style' => (string) ($options['style'] ?? 'fantasy'),
      'aspect_ratio' => (string) ($options['aspect_ratio'] ?? '1:1'),
      'negative_prompt' => 'text, watermark, logo, signature, blurry, low quality, deformed, 3D render, photograph, realistic photo',
      'campaign_context' => 'dungeon_sprite',
      'requested_by_uid' => $owner_uid,
    ];

    try {
      $result = $this->integrationService->generateImage($payload, $options['provider'] ?? NULL);

      if (empty($result['success'])) {
        $this->logger->warning('Sprite generation failed for @sprite_id: @msg', [
          '@sprite_id' => $sprite_id,
          '@msg' => $result['message'] ?? 'unknown',
        ]);
        return ['url' => NULL, 'generated' => TRUE, 'cached' => FALSE, 'error' => $result['message'] ?? 'generation_failed'];
      }

      $storage = $this->generatedImageRepository->persistGeneratedImage($result, [
        'owner_uid' => $owner_uid,
        'scope_type' => $campaign_id !== NULL ? 'campaign' : 'global',
        'campaign_id' => $campaign_id,
        'table_name' => 'dc_dungeon_sprites',
        'object_id' => $sprite_id,
        'slot' => 'sprite',
        'variant' => 'original',
        'visibility' => 'public',
        'is_primary' => 1,
      ]);

      $url = $storage['url'] ?? NULL;

      $this->logger->notice('Sprite generated for @sprite_id: stored=@stored', [
        '@sprite_id' => $sprite_id,
        '@stored' => !empty($storage['stored']) ? 'yes' : 'no',
      ]);

      return ['url' => $url, 'generated' => TRUE, 'cached' => FALSE, 'error' => NULL];
    }
    catch (\Throwable $e) {
      $this->logger->error('Sprite generation exception for @sprite_id: @msg', [
        '@sprite_id' => $sprite_id,
        '@msg' => $e->getMessage(),
      ]);
      return ['url' => NULL, 'generated' => FALSE, 'cached' => FALSE, 'error' => $e->getMessage()];
    }
  }

  /**
   * Builds a generation prompt from sprite_id and object data.
   */
  protected function buildSpritePrompt(string $sprite_id, array $object_definition, array $options = []): string {
    $custom_prompt = trim((string) ($object_definition['visual']['prompt'] ?? $object_definition['prompt'] ?? ''));
    if ($custom_prompt !== '') {
      return $custom_prompt;
    }

    $lines = [
      'Create a top-down sprite token for a hexmap tactical RPG.',
      'The image should be a single game object viewed from above, suitable for placement on a hex grid.',
      'Style: high-fantasy hand-painted token art with clean edges.',
      'Requirements: transparent background (PNG), strong silhouette at 64x64 to 128x128 pixels, no text or labels.',
    ];

    $label = trim((string) ($object_definition['label'] ?? ''));
    $category = trim((string) ($object_definition['category'] ?? ''));
    $description = trim((string) ($object_definition['description'] ?? ''));
    $size = trim((string) ($object_definition['size'] ?? ''));
    $color = trim((string) ($object_definition['visual']['color'] ?? ''));

    if ($label !== '') {
      $lines[] = 'Object: ' . $label;
    }
    else {
      // Derive label from sprite_id.
      $derived = str_replace(['_', '-'], ' ', $sprite_id);
      $lines[] = 'Object: ' . ucwords($derived);
    }

    if ($category !== '') {
      $lines[] = 'Category: ' . $category;
    }

    if ($description !== '') {
      // Truncate long descriptions to keep prompt focused.
      $short_desc = strlen($description) > 200 ? substr($description, 0, 197) . '...' : $description;
      $lines[] = 'Description: ' . $short_desc;
    }

    if ($size !== '') {
      $lines[] = 'Size: ' . $size;
    }

    if ($color !== '') {
      $lines[] = 'Dominant color: ' . $color;
    }

    $direction = $this->normalizeDirectionToken($options['direction'] ?? NULL);
    if ($direction !== NULL) {
      $lines[] = 'Facing direction: ' . $direction . '.';
      $lines[] = 'Keep the subject oriented consistently for that facing direction while preserving top-down token readability.';
    }

    // Add category-specific guidance.
    $category_guidance = $this->getCategoryGuidance($category);
    if ($category_guidance !== '') {
      $lines[] = $category_guidance;
    }

    return implode("\n", $lines);
  }

  /**
   * Returns category-specific prompt guidance.
   */
  protected function getCategoryGuidance(string $category): string {
    return match (strtolower($category)) {
      'wall' => 'Show as a wall segment from above aligned for hex-grid placement, with clear material texture and edge readability.',
      'door' => 'Show as a door viewed from above: a rectangular wooden or metal panel with a visible handle/knob. Clear directional orientation.',
      'bar' => 'Show as a bar counter section from above: a long rectangular wooden counter surface. Polished wood grain visible.',
      'table' => 'Show as a table from above: a round or rectangular wooden surface, possibly with items on it. Woodgrain or cloth texture.',
      'stool', 'chair' => 'Show as a small round seat from above: a circular wooden stool or chair with visible seat and maybe legs peeking out.',
      'crate', 'barrel' => 'Show as a crate or barrel from above: square wooden planks with cross-bracing for a crate, or a round top with metal bands for a barrel.',
      'decor' => 'Show as a decorative item from above: could be a rug, lantern, or wall hanging. Keep it recognizable in silhouette.',
      'weapon_rack', 'shelf' => 'Show as a storage furniture piece from above: a rack or shelf with items visible on it.',
      'fireplace', 'hearth' => 'Show as a fireplace from above: a stone or brick surround with orange-red glow at center indicating fire.',
      default => '',
    };
  }

  /**
   * Extracts sprite_id from an object definition.
   */
  private function extractSpriteId(array $def): string {
    return trim((string) ($def['visual']['sprite_id'] ?? ''));
  }

  /**
   * Collect all sprite generation requests from an object definition.
   *
   * @param array $def
   *   Object definition payload.
   *
   * @return array<string, array{definition: array, options: array}>
   *   Map of sprite_id => request payload.
   */
  private function extractSpriteRequests(array $def): array {
    $requests = [];

    $primary = $this->extractSpriteId($def);
    if ($primary !== '') {
      $requests[$primary] = [
        'definition' => $def,
        'options' => [],
      ];
    }

    $variant_sources = [
      $def['visual']['sprite_variants'] ?? NULL,
      $def['visual']['directional_sprites'] ?? NULL,
      $def['visual']['orientation_sprites'] ?? NULL,
      $def['visual']['facing_sprites'] ?? NULL,
    ];

    foreach ($variant_sources as $source) {
      if (!is_array($source)) {
        continue;
      }

      foreach ($source as $direction => $value) {
        $sprite_id = $this->extractDirectionalSpriteId($value);
        $normalized_direction = $this->normalizeDirectionToken($direction);
        if ($sprite_id === '' || $normalized_direction === NULL || isset($requests[$sprite_id])) {
          continue;
        }

        $requests[$sprite_id] = [
          'definition' => $def,
          'options' => ['direction' => $normalized_direction],
        ];
      }
    }

    return $requests;
  }

  /**
   * Extract a sprite identifier from a directional sprite entry.
   *
   * @param mixed $value
   *   String sprite id or map containing sprite_id.
   *
   * @return string
   *   Sprite identifier or empty string when unsupported.
   */
  private function extractDirectionalSpriteId(mixed $value): string {
    if (is_string($value)) {
      $trimmed = trim($value);
      if ($trimmed === '' || $this->looksLikeUrl($trimmed)) {
        return '';
      }
      return $trimmed;
    }

    if (is_array($value)) {
      $candidate = $value['sprite_id'] ?? $value['spriteId'] ?? $value['id'] ?? $value['key'] ?? '';
      return is_string($candidate) ? trim($candidate) : '';
    }

    return '';
  }

  /**
   * Normalize a direction token into a human-readable phrase.
   */
  private function normalizeDirectionToken(mixed $direction): ?string {
    if (!is_scalar($direction)) {
      return NULL;
    }

    $token = strtolower(preg_replace('/[\s_-]+/', '', trim((string) $direction)));
    return match ($token) {
      'n', 'north' => 'north',
      'ne', 'northeast' => 'northeast',
      'e', 'east' => 'east',
      'se', 'southeast' => 'southeast',
      's', 'south' => 'south',
      'sw', 'southwest' => 'southwest',
      'w', 'west' => 'west',
      'nw', 'northwest' => 'northwest',
      default => NULL,
    };
  }

  /**
   * Detect whether a string looks like a URL/path instead of a sprite id.
   */
  private function looksLikeUrl(string $value): bool {
    return preg_match('@^(https?:)?//@i', $value) === 1 || str_starts_with($value, '/');
  }

}
