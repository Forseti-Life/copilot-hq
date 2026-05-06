<?php

namespace Drupal\dungeoncrawler_content\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\dungeoncrawler_content\Service\ImageGenerationIntegrationService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configuration form for Dungeon Crawler Content settings.
 */
class DungeonCrawlerSettingsForm extends ConfigFormBase {

  /**
   * Provider-agnostic integration testing service.
   */
  protected ImageGenerationIntegrationService $imageGenerationIntegrationService;

  /**
   * Constructs the settings form.
   */
  public function __construct(ConfigFactoryInterface $config_factory, TypedConfigManagerInterface $typed_config_manager, ImageGenerationIntegrationService $image_generation_integration_service) {
    parent::__construct($config_factory, $typed_config_manager);
    $this->imageGenerationIntegrationService = $image_generation_integration_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('config.typed'),
      $container->get('dungeoncrawler_content.image_generation_integration'),
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['dungeoncrawler_content.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dungeoncrawler_content_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('dungeoncrawler_content.settings');

    $form['page_intro'] = [
      '#type' => 'item',
      '#markup' => '<p>' . $this->t('Configure core dungeon behavior and each external integration separately. Image-generation provider routing is controlled independently from Gemini and Vertex connection details.') . '</p>',
    ];

    $form['settings_tabs'] = [
      '#type' => 'vertical_tabs',
      '#title' => $this->t('DungeonCrawler configuration'),
      '#default_tab' => 'edit-core-settings',
    ];

    $form['core_settings'] = $this->buildCoreSettingsSection($config);
    $form['encounter_ai_settings'] = $this->buildEncounterAiSettingsSection($config);
    $form['image_provider_settings'] = $this->buildImageProviderSettingsSection($config);
    $form['gemini_integration'] = $this->buildGeminiIntegrationSection($config);
    $form['vertex_integration'] = $this->buildVertexIntegrationSection($config);
    $form['display_settings'] = $this->buildDisplaySettingsSection($config);

    return parent::buildForm($form, $form_state);
  }

  /**
   * Build core dungeon settings section.
   */
  private function buildCoreSettingsSection($config): array {
    $section = [
      '#type' => 'details',
      '#title' => $this->t('⚔️ Core dungeon settings'),
      '#group' => 'settings_tabs',
      '#open' => TRUE,
      '#description' => $this->t('Game-wide rules and content-generation defaults that apply regardless of external AI providers.'),
    ];

    $section['max_level'] = [
      '#type' => 'number',
      '#title' => $this->t('Maximum Adventurer Level'),
      '#default_value' => $config->get('max_level') ?? 100,
      '#min' => 1,
      '#max' => 999,
      '#description' => $this->t('The maximum level an adventurer can reach in the dungeon.'),
    ];

    $section['difficulty_levels'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Dungeon Depth Tiers'),
      '#default_value' => $config->get('difficulty_levels') ?? "Shallow Halls\nTwisting Corridors\nDeep Caverns\nThe Underdark\nThe Abyss",
      '#description' => $this->t('One dungeon depth tier per line. Deeper tiers have stronger AI-generated monsters and better loot.'),
    ];

    $section['rarity_tiers'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Item Rarity Tiers'),
      '#default_value' => $config->get('rarity_tiers') ?? "Common\nUncommon\nRare\nEpic\nLegendary",
      '#description' => $this->t('One rarity tier per line, from lowest to highest. Determines loot drop colors and AI generation parameters.'),
    ];

    $section['room_persistence'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Rooms are permanent after first generation'),
      '#default_value' => $config->get('room_persistence') ?? TRUE,
      '#description' => $this->t('When enabled, AI-generated rooms become permanent world fixtures after first exploration.'),
    ];

    $section['monster_permadeath'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable monster permadeath for mortal creatures'),
      '#default_value' => $config->get('monster_permadeath') ?? TRUE,
      '#description' => $this->t('When enabled, mortal monsters that are slain stay dead permanently. Respawning creatures are unaffected.'),
    ];

    return $section;
  }

  /**
   * Build encounter AI settings section.
   */
  private function buildEncounterAiSettingsSection($config): array {
    $section = [
      '#type' => 'details',
      '#title' => $this->t('🧠 Encounter AI integration'),
      '#group' => 'settings_tabs',
      '#description' => $this->t('Settings for encounter narration and NPC auto-play. These controls are separate from image-generation providers.'),
    ];

    $section['encounter_ai_npc_autoplay_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable AI-driven NPC auto-play in encounters'),
      '#default_value' => $config->get('encounter_ai_npc_autoplay_enabled') ?? FALSE,
      '#description' => $this->t('When enabled, non-player turns can use validated AI recommendations and deterministic fallback behavior. Disabled by default.'),
    ];

    $section['encounter_ai_narration_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Persist AI narration events in encounter timeline'),
      '#default_value' => $config->get('encounter_ai_narration_enabled') ?? FALSE,
      '#description' => $this->t('When enabled, AI narration snippets are logged as encounter timeline events (`ai_narration`) during NPC auto-play.'),
    ];

    $section['encounter_ai_retry_attempts'] = [
      '#type' => 'number',
      '#title' => $this->t('Encounter AI retry attempts'),
      '#default_value' => $config->get('encounter_ai_retry_attempts') ?? 2,
      '#min' => 1,
      '#max' => 3,
      '#description' => $this->t('Maximum Bedrock invocation attempts per encounter recommendation/narration request before deterministic fallback.'),
    ];

    $section['encounter_ai_recommendation_max_tokens'] = [
      '#type' => 'number',
      '#title' => $this->t('Encounter AI recommendation max tokens'),
      '#default_value' => $config->get('encounter_ai_recommendation_max_tokens') ?? 800,
      '#min' => 200,
      '#max' => 2000,
      '#description' => $this->t('Token budget passed to Bedrock recommendation calls.'),
    ];

    $section['encounter_ai_narration_max_tokens'] = [
      '#type' => 'number',
      '#title' => $this->t('Encounter AI narration max tokens'),
      '#default_value' => $config->get('encounter_ai_narration_max_tokens') ?? 500,
      '#min' => 120,
      '#max' => 1200,
      '#description' => $this->t('Token budget passed to Bedrock narration calls.'),
    ];

    return $section;
  }

  /**
   * Build image provider routing section.
   */
  private function buildImageProviderSettingsSection($config): array {
    $section = [
      '#type' => 'details',
      '#title' => $this->t('🖼️ Image generation routing'),
      '#group' => 'settings_tabs',
      '#description' => $this->t('Choose which provider is used by default. Provider-specific credentials and endpoints are configured in their own sections below.'),
    ];

    $section['generated_image_provider'] = [
      '#type' => 'select',
      '#title' => $this->t('Default image provider'),
      '#options' => [
        'gemini' => $this->t('Gemini'),
        'vertex' => $this->t('Vertex'),
      ],
      '#default_value' => $config->get('generated_image_provider') ?? 'gemini',
      '#description' => $this->t('This only controls which provider is chosen by default when no override is supplied elsewhere in the application.'),
    ];

    return $section;
  }

  /**
   * Build Gemini integration section.
   */
  private function buildGeminiIntegrationSection($config): array {
    $section = [
      '#type' => 'details',
      '#title' => $this->t('Gemini integration'),
      '#group' => 'settings_tabs',
      '#description' => $this->t('Gemini-specific live-mode settings. Changes here do not affect Vertex.'),
    ];

    $section['gemini_image_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Gemini image generation live mode'),
      '#default_value' => $config->get('gemini_image_enabled') ?? FALSE,
      '#description' => $this->t('When enabled, dashboard image requests attempt a live Gemini API call when an API key is available.'),
    ];

    $section['gemini_image_model'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Gemini image model'),
      '#default_value' => $config->get('gemini_image_model') ?? 'gemini-2.0-flash-exp',
      '#maxlength' => 255,
      '#description' => $this->t('Model name used for image generation requests. Example: gemini-2.0-flash-exp.'),
    ];

    $section['gemini_image_endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Gemini endpoint template'),
      '#default_value' => $config->get('gemini_image_endpoint') ?? 'https://generativelanguage.googleapis.com/v1beta/models/{model}:generateContent',
      '#maxlength' => 512,
      '#description' => $this->t('Endpoint template for Gemini requests. Use {model} as placeholder for the selected model.'),
    ];

    $section['gemini_image_timeout'] = [
      '#type' => 'number',
      '#title' => $this->t('Gemini request timeout (seconds)'),
      '#default_value' => $config->get('gemini_image_timeout') ?? 30,
      '#min' => 5,
      '#max' => 120,
    ];

    $section['gemini_image_api_key'] = [
      '#type' => 'password',
      '#title' => $this->t('Gemini API key (optional)'),
      '#description' => $this->t('Prefer environment variable GEMINI_API_KEY. If set here, this value is stored in Drupal configuration.'),
      '#maxlength' => 255,
      '#attributes' => [
        'autocomplete' => 'new-password',
      ],
    ];

    $section['gemini_system_context_prompt'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Gemini system context prompt'),
      '#default_value' => $config->get('gemini_system_context_prompt') ?? '',
      '#rows' => 10,
      '#description' => $this->t('System prompt automatically wrapped around user input for Gemini requests from the Gemini interface.'),
    ];

    $section['gemini_test_help'] = [
      '#type' => 'item',
      '#markup' => '<p>' . $this->t('Run a live Gemini request using the values currently entered on this form. This does not save configuration.') . '</p>',
    ];

    $section['gemini_test_button'] = [
      '#type' => 'submit',
      '#value' => $this->t('Test Gemini integration'),
      '#submit' => ['::submitGeminiIntegrationTest'],
      '#button_type' => 'secondary',
    ];

    return $section;
  }

  /**
   * Build Vertex integration section.
   */
  private function buildVertexIntegrationSection($config): array {
    $section = [
      '#type' => 'details',
      '#title' => $this->t('Vertex integration'),
      '#group' => 'settings_tabs',
      '#description' => $this->t('Vertex-specific live-mode settings. Changes here do not affect Gemini. Authenticate with either a Vertex API key or GOOGLE_APPLICATION_CREDENTIALS service-account auth.'),
    ];

    $section['vertex_image_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Vertex image generation live mode'),
      '#default_value' => $config->get('vertex_image_enabled') ?? FALSE,
      '#description' => $this->t('When enabled, dashboard image requests can use Vertex live API calls when configured credentials are available.'),
    ];

    $section['vertex_image_project_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Vertex project ID'),
      '#default_value' => $config->get('vertex_image_project_id') ?? '',
      '#maxlength' => 255,
      '#description' => $this->t('Google Cloud project used for Vertex image generation requests.'),
    ];

    $section['vertex_image_location'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Vertex location'),
      '#default_value' => $config->get('vertex_image_location') ?? 'us-central1',
      '#maxlength' => 64,
      '#description' => $this->t('Vertex region for the configured image model.'),
    ];

    $section['vertex_image_model'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Vertex image model'),
      '#default_value' => $config->get('vertex_image_model') ?? 'imagen-3.0-generate-002',
      '#maxlength' => 255,
      '#description' => $this->t('Model name used for Vertex image requests. Example: imagen-3.0-generate-002.'),
    ];

    $section['vertex_image_endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Vertex endpoint template'),
      '#default_value' => $config->get('vertex_image_endpoint') ?? 'https://{location}-aiplatform.googleapis.com/v1/projects/{project_id}/locations/{location}/publishers/google/models/{model}:predict',
      '#maxlength' => 512,
      '#description' => $this->t('Endpoint template. Supports placeholders: {project_id}, {location}, {model}.'),
    ];

    $section['vertex_image_timeout'] = [
      '#type' => 'number',
      '#title' => $this->t('Vertex request timeout (seconds)'),
      '#default_value' => $config->get('vertex_image_timeout') ?? 30,
      '#min' => 5,
      '#max' => 120,
    ];

    $section['vertex_image_api_key'] = [
      '#type' => 'password',
      '#title' => $this->t('Vertex API key (optional)'),
      '#description' => $this->t('Prefer environment variable VERTEX_API_KEY. If set here, this value is stored in Drupal configuration. Leave blank when using GOOGLE_APPLICATION_CREDENTIALS service-account auth instead.'),
      '#maxlength' => 255,
      '#attributes' => [
        'autocomplete' => 'new-password',
      ],
    ];

    $section['vertex_test_help'] = [
      '#type' => 'item',
      '#markup' => '<p>' . $this->t('Run a live Vertex request using the values currently entered on this form. This bypasses the cache so the result confirms real connectivity.') . '</p>',
    ];

    $section['vertex_test_button'] = [
      '#type' => 'submit',
      '#value' => $this->t('Test Vertex integration'),
      '#submit' => ['::submitVertexIntegrationTest'],
      '#button_type' => 'secondary',
    ];

    return $section;
  }

  /**
   * Build display settings section.
   */
  private function buildDisplaySettingsSection($config): array {
    $section = [
      '#type' => 'details',
      '#title' => $this->t('🗺️ Display settings'),
      '#group' => 'settings_tabs',
      '#description' => $this->t('Presentation settings for content lists and game statistics.'),
    ];

    $section['items_per_page'] = [
      '#type' => 'number',
      '#title' => $this->t('Items per page'),
      '#default_value' => $config->get('items_per_page') ?? 12,
      '#min' => 4,
      '#max' => 100,
      '#description' => $this->t('Number of dungeon rooms, items, or creatures to display per page in listings.'),
    ];

    $section['show_game_stats'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show adventure statistics on content pages'),
      '#default_value' => $config->get('show_game_stats') ?? TRUE,
    ];

    return $section;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('dungeoncrawler_content.settings')
      ->set('max_level', $form_state->getValue('max_level'))
      ->set('difficulty_levels', $form_state->getValue('difficulty_levels'))
      ->set('rarity_tiers', $form_state->getValue('rarity_tiers'))
      ->set('room_persistence', $form_state->getValue('room_persistence'))
      ->set('monster_permadeath', $form_state->getValue('monster_permadeath'))
      ->set('encounter_ai_npc_autoplay_enabled', $form_state->getValue('encounter_ai_npc_autoplay_enabled'))
      ->set('encounter_ai_narration_enabled', $form_state->getValue('encounter_ai_narration_enabled'))
      ->set('encounter_ai_retry_attempts', (int) $form_state->getValue('encounter_ai_retry_attempts'))
      ->set('encounter_ai_recommendation_max_tokens', (int) $form_state->getValue('encounter_ai_recommendation_max_tokens'))
      ->set('encounter_ai_narration_max_tokens', (int) $form_state->getValue('encounter_ai_narration_max_tokens'))
      ->set('generated_image_provider', (string) $form_state->getValue('generated_image_provider'))
      ->set('gemini_image_enabled', $form_state->getValue('gemini_image_enabled'))
      ->set('gemini_image_model', trim((string) $form_state->getValue('gemini_image_model')))
      ->set('gemini_image_endpoint', trim((string) $form_state->getValue('gemini_image_endpoint')))
      ->set('gemini_image_timeout', (int) $form_state->getValue('gemini_image_timeout'))
      ->set('gemini_system_context_prompt', trim((string) $form_state->getValue('gemini_system_context_prompt')))
      ->set('vertex_image_enabled', $form_state->getValue('vertex_image_enabled'))
      ->set('vertex_image_project_id', trim((string) $form_state->getValue('vertex_image_project_id')))
      ->set('vertex_image_location', trim((string) $form_state->getValue('vertex_image_location')))
      ->set('vertex_image_model', trim((string) $form_state->getValue('vertex_image_model')))
      ->set('vertex_image_endpoint', trim((string) $form_state->getValue('vertex_image_endpoint')))
      ->set('vertex_image_timeout', (int) $form_state->getValue('vertex_image_timeout'))
      ->set('items_per_page', $form_state->getValue('items_per_page'))
      ->set('show_game_stats', $form_state->getValue('show_game_stats'))
      ->save();

    $submitted_key = trim((string) $form_state->getValue('gemini_image_api_key'));
    if ($submitted_key !== '') {
      $this->config('dungeoncrawler_content.settings')
        ->set('gemini_image_api_key', $submitted_key)
        ->save();
    }

    $submitted_vertex_key = trim((string) $form_state->getValue('vertex_image_api_key'));
    if ($submitted_vertex_key !== '') {
      $this->config('dungeoncrawler_content.settings')
        ->set('vertex_image_api_key', $submitted_vertex_key)
        ->save();
    }

    parent::submitForm($form, $form_state);
  }

  /**
   * Runs a live Gemini integration test with current form values.
   */
  public function submitGeminiIntegrationTest(array &$form, FormStateInterface $form_state): void {
    $result = $this->imageGenerationIntegrationService->testProvider('gemini', $this->buildGeminiIntegrationTestSettings($form_state));
    $this->displayIntegrationTestResult('Gemini', $result);
    $form_state->setRebuild(TRUE);
  }

  /**
   * Runs a live Vertex integration test with current form values.
   */
  public function submitVertexIntegrationTest(array &$form, FormStateInterface $form_state): void {
    $result = $this->imageGenerationIntegrationService->testProvider('vertex', $this->buildVertexIntegrationTestSettings($form_state));
    $this->displayIntegrationTestResult('Vertex', $result);
    $form_state->setRebuild(TRUE);
  }

  /**
   * Build settings overrides for a Gemini integration test.
   *
   * @return array<string, mixed>
   *   Unsaved settings.
   */
  private function buildGeminiIntegrationTestSettings(FormStateInterface $form_state): array {
    $settings = [
      'gemini_image_enabled' => (bool) $form_state->getValue('gemini_image_enabled'),
      'gemini_image_model' => trim((string) $form_state->getValue('gemini_image_model')),
      'gemini_image_endpoint' => trim((string) $form_state->getValue('gemini_image_endpoint')),
      'gemini_image_timeout' => (int) $form_state->getValue('gemini_image_timeout'),
      'gemini_system_context_prompt' => trim((string) $form_state->getValue('gemini_system_context_prompt')),
      'requested_by_uid' => (int) $this->currentUser()->id(),
    ];

    $api_key = trim((string) $form_state->getValue('gemini_image_api_key'));
    if ($api_key !== '') {
      $settings['gemini_image_api_key'] = $api_key;
    }

    return $settings;
  }

  /**
   * Build settings overrides for a Vertex integration test.
   *
   * @return array<string, mixed>
   *   Unsaved settings.
   */
  private function buildVertexIntegrationTestSettings(FormStateInterface $form_state): array {
    $settings = [
      'vertex_image_enabled' => (bool) $form_state->getValue('vertex_image_enabled'),
      'vertex_image_project_id' => trim((string) $form_state->getValue('vertex_image_project_id')),
      'vertex_image_location' => trim((string) $form_state->getValue('vertex_image_location')),
      'vertex_image_model' => trim((string) $form_state->getValue('vertex_image_model')),
      'vertex_image_endpoint' => trim((string) $form_state->getValue('vertex_image_endpoint')),
      'vertex_image_timeout' => (int) $form_state->getValue('vertex_image_timeout'),
      'requested_by_uid' => (int) $this->currentUser()->id(),
    ];

    $api_key = trim((string) $form_state->getValue('vertex_image_api_key'));
    if ($api_key !== '') {
      $settings['vertex_image_api_key'] = $api_key;
    }

    return $settings;
  }

  /**
   * Display the result of a provider integration test.
   *
   * @param string $provider_label
   *   Human-readable provider name.
   * @param array<string, mixed> $result
   *   Provider response.
   */
  private function displayIntegrationTestResult(string $provider_label, array $result): void {
    $request_id = isset($result['request_id']) ? (string) $result['request_id'] : 'n/a';
    $message = isset($result['message']) ? (string) $result['message'] : $this->t('No response message was returned.');

    if (!empty($result['success'])) {
      $this->messenger()->addStatus($this->t('@provider test succeeded. Request ID: @request_id. @message', [
        '@provider' => $provider_label,
        '@request_id' => $request_id,
        '@message' => $message,
      ]));
      return;
    }

    $this->messenger()->addError($this->t('@provider test failed. Request ID: @request_id. @message', [
      '@provider' => $provider_label,
      '@request_id' => $request_id,
      '@message' => $message,
    ]));
  }

}
