<?php

namespace Drupal\Tests\dungeoncrawler_content\Unit\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Tests\UnitTestCase;
use Drupal\dungeoncrawler_content\Service\GeminiImageGenerationService;
use Drupal\dungeoncrawler_content\Service\ImageGenerationIntegrationService;
use Drupal\dungeoncrawler_content\Service\VertexImageGenerationService;

/**
 * Tests for ImageGenerationIntegrationService.
 *
 * @group dungeoncrawler_content
 * @group ai
 * @coversDefaultClass \Drupal\dungeoncrawler_content\Service\ImageGenerationIntegrationService
 */
class ImageGenerationIntegrationServiceTest extends UnitTestCase {

  /**
   * Tests Gemini test requests wrap the prompt and delegate to Gemini.
   *
   * @covers ::testProvider
   * @covers ::getGeminiSystemContextPrompt
   * @covers ::wrapGeminiPrompt
   */
  public function testGeminiProviderTestUsesWrappedPrompt(): void {
    $config_factory = $this->createMock(ConfigFactoryInterface::class);
    $config = $this->createMock(ImmutableConfig::class);
    $gemini = $this->createMock(GeminiImageGenerationService::class);
    $vertex = $this->createMock(VertexImageGenerationService::class);

    $config_factory->method('get')
      ->with('dungeoncrawler_content.settings')
      ->willReturn($config);

    $config->method('get')
      ->willReturnMap([
        ['gemini_system_context_prompt', 'Configured Gemini system prompt'],
      ]);

    $gemini->expects($this->once())
      ->method('testLiveConnection')
      ->with(
        $this->callback(function (array $payload): bool {
          return str_contains((string) ($payload['wrapped_prompt'] ?? ''), 'Configured Gemini system prompt')
            && str_contains((string) ($payload['wrapped_prompt'] ?? ''), 'User Request:')
            && (string) ($payload['campaign_context'] ?? '') === 'admin_integration_test';
        }),
        $this->callback(function (array $settings): bool {
          return (int) ($settings['requested_by_uid'] ?? 0) === 17;
        })
      )
      ->willReturn([
        'success' => TRUE,
        'provider' => 'gemini',
      ]);

    $vertex->expects($this->never())->method('testLiveConnection');

    $service = new ImageGenerationIntegrationService($config_factory, $gemini, $vertex);
    $result = $service->testProvider('gemini', [
      'requested_by_uid' => 17,
    ]);

    $this->assertTrue($result['success']);
    $this->assertSame('gemini', $result['provider']);
  }

  /**
   * Tests Vertex test requests delegate to Vertex directly.
   *
   * @covers ::testProvider
   */
  public function testVertexProviderTestDelegatesToVertex(): void {
    $config_factory = $this->createMock(ConfigFactoryInterface::class);
    $config = $this->createMock(ImmutableConfig::class);
    $gemini = $this->createMock(GeminiImageGenerationService::class);
    $vertex = $this->createMock(VertexImageGenerationService::class);

    $config_factory->method('get')
      ->with('dungeoncrawler_content.settings')
      ->willReturn($config);

    $config->method('get')->willReturn(NULL);

    $vertex->expects($this->once())
      ->method('testLiveConnection')
      ->with(
        $this->callback(function (array $payload): bool {
          return !isset($payload['wrapped_prompt'])
            && (string) ($payload['campaign_context'] ?? '') === 'admin_integration_test';
        }),
        $this->callback(function (array $settings): bool {
          return (string) ($settings['vertex_image_project_id'] ?? '') === 'forseti-live';
        })
      )
      ->willReturn([
        'success' => TRUE,
        'provider' => 'vertex',
      ]);

    $gemini->expects($this->never())->method('testLiveConnection');

    $service = new ImageGenerationIntegrationService($config_factory, $gemini, $vertex);
    $result = $service->testProvider('vertex', [
      'vertex_image_project_id' => 'forseti-live',
    ]);

    $this->assertTrue($result['success']);
    $this->assertSame('vertex', $result['provider']);
  }

}
