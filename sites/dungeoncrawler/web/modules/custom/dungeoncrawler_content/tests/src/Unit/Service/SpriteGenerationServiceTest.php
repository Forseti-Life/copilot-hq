<?php

namespace Drupal\Tests\dungeoncrawler_content\Unit\Service;

require_once dirname(__DIR__, 8) . '/vendor/drupal/core/lib/Drupal/Core/Logger/LoggerChannelFactoryInterface.php';

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\dungeoncrawler_content\Service\GeneratedImageRepository;
use Drupal\dungeoncrawler_content\Service\ImageGenerationIntegrationService;
use Drupal\dungeoncrawler_content\Service\SpriteGenerationService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Tests for SpriteGenerationService.
 *
 * @group dungeoncrawler_content
 * @group sprites
 * @coversDefaultClass \Drupal\dungeoncrawler_content\Service\SpriteGenerationService
 */
class SpriteGenerationServiceTest extends TestCase {

  /**
   * Tests directional sprite variants are included in batch generation.
   *
   * @covers ::resolveBatchDetailed
   */
  public function testResolveBatchDetailedGeneratesDirectionalVariants(): void {
    $integration = $this->createMock(ImageGenerationIntegrationService::class);
    $repository = $this->createMock(GeneratedImageRepository::class);
    $logger_factory = $this->createMock(LoggerChannelFactoryInterface::class);
    $logger = $this->createMock(LoggerInterface::class);

    $logger_factory->method('get')
      ->with('dungeoncrawler_content')
      ->willReturn($logger);

    $repository->expects($this->once())
      ->method('loadImagesForObjects')
      ->with(
        'dc_dungeon_sprites',
        $this->callback(function (array $sprite_ids): bool {
          sort($sprite_ids);
          return $sprite_ids === ['goblin_base', 'goblin_ne', 'goblin_sw'];
        }),
        NULL,
        'sprite',
        'original'
      )
      ->willReturn([]);

    $prompts = [];
    $integration->expects($this->exactly(3))
      ->method('generateImage')
      ->willReturnCallback(function (array $payload) use (&$prompts): array {
        $prompts[] = (string) ($payload['prompt'] ?? '');
        return [
          'success' => TRUE,
          'provider' => 'gemini',
        ];
      });

    $repository->expects($this->exactly(3))
      ->method('persistGeneratedImage')
      ->willReturnCallback(function (array $result, array $storage): array {
        return [
          'url' => '/generated/' . $storage['object_id'] . '.png',
          'stored' => TRUE,
        ];
      });

    $service = new SpriteGenerationService($integration, $repository, $logger_factory);
    $resolved = $service->resolveBatchDetailed([
      'goblin-scout' => [
        'label' => 'Goblin Scout',
        'category' => 'creature',
        'description' => 'A sneaky goblin skirmisher.',
        'visual' => [
          'sprite_id' => 'goblin_base',
          'sprite_variants' => [
            'ne' => 'goblin_ne',
            'south_west' => ['sprite_id' => 'goblin_sw'],
          ],
        ],
      ],
    ], NULL, 9);

    $this->assertSame(['/generated/goblin_base.png', TRUE, FALSE], [$resolved['goblin_base']['url'], $resolved['goblin_base']['generated'], $resolved['goblin_base']['cached']]);
    $this->assertSame('/generated/goblin_ne.png', $resolved['goblin_ne']['url']);
    $this->assertSame('/generated/goblin_sw.png', $resolved['goblin_sw']['url']);
    $this->assertCount(3, $prompts);
    $this->assertTrue($this->containsPromptSnippet($prompts, 'Facing direction: northeast.'));
    $this->assertTrue($this->containsPromptSnippet($prompts, 'Facing direction: southwest.'));
  }

  /**
   * Tests URL-only directional entries are not treated as generation requests.
   *
   * @covers ::resolveBatchDetailed
   */
  public function testResolveBatchDetailedIgnoresUrlOnlyDirectionalEntries(): void {
    $integration = $this->createMock(ImageGenerationIntegrationService::class);
    $repository = $this->createMock(GeneratedImageRepository::class);
    $logger_factory = $this->createMock(LoggerChannelFactoryInterface::class);
    $logger = $this->createMock(LoggerInterface::class);

    $logger_factory->method('get')
      ->willReturn($logger);

    $integration->expects($this->never())->method('generateImage');
    $repository->expects($this->never())->method('loadImagesForObjects');
    $repository->expects($this->never())->method('persistGeneratedImage');

    $service = new SpriteGenerationService($integration, $repository, $logger_factory);
    $resolved = $service->resolveBatchDetailed([
      'hero' => [
        'label' => 'Hero',
        'visual' => [
          'sprite_variants' => [
            'north' => 'https://cdn.example.com/hero-north.png',
            'south' => ['url' => '/images/hero-south.png'],
          ],
        ],
      ],
    ], NULL, 1);

    $this->assertSame([], $resolved);
  }

  /**
   * Determine whether the captured prompts include a substring.
   *
   * @param array<int, string> $prompts
   *   Captured prompts.
   * @param string $needle
   *   Substring to find.
   *
   * @return bool
   *   TRUE when any prompt contains the substring.
   */
  private function containsPromptSnippet(array $prompts, string $needle): bool {
    foreach ($prompts as $prompt) {
      if (str_contains($prompt, $needle)) {
        return TRUE;
      }
    }

    return FALSE;
  }

}
