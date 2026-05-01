<?php

namespace Drupal\Tests\dungeoncrawler_content\Unit\Service;

use Drupal\dungeoncrawler_content\Service\RoadmapPipelineStatusResolver;
use Drupal\Tests\UnitTestCase;

/**
 * Tests roadmap pipeline status resolution.
 *
 * @group dungeoncrawler_content
 * @coversDefaultClass \Drupal\dungeoncrawler_content\Service\RoadmapPipelineStatusResolver
 */
class RoadmapPipelineStatusResolverTest extends UnitTestCase {

  /**
   * Temporary feature directory.
   */
  private string $featuresPath;

  /**
   * Temporary release-state directory.
   */
  private string $releaseStatePath;

  /**
   * Temporary push-marker directory.
   */
  private string $pushStatePath;

  /**
   * Temporary PM inbox directory.
   */
  private string $pmInboxPath;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->featuresPath = sys_get_temp_dir() . '/dc-roadmap-pipeline-' . uniqid('', TRUE);
    $this->releaseStatePath = sys_get_temp_dir() . '/dc-roadmap-release-state-' . uniqid('', TRUE);
    $this->pushStatePath = sys_get_temp_dir() . '/dc-roadmap-push-state-' . uniqid('', TRUE);
    $this->pmInboxPath = sys_get_temp_dir() . '/dc-roadmap-pm-inbox-' . uniqid('', TRUE);
    mkdir($this->featuresPath, 0777, TRUE);
    mkdir($this->releaseStatePath, 0777, TRUE);
    mkdir($this->pushStatePath, 0777, TRUE);
    mkdir($this->pmInboxPath, 0777, TRUE);
  }

  /**
   * {@inheritdoc}
   */
  protected function tearDown(): void {
    $this->deleteDirectory($this->featuresPath);
    $this->deleteDirectory($this->releaseStatePath);
    $this->deleteDirectory($this->pushStatePath);
    $this->deleteDirectory($this->pmInboxPath);
    parent::tearDown();
  }

  /**
   * @covers ::resolveRoadmapStatus
   * @covers ::getPipelineStatus
   */
  public function testResolveRoadmapStatusUsesPipelineStatusWhenFeatureExists(): void {
    // 'done' should remain visible as done on the roadmap.
    $this->writeFeatureStatus('dc-cr-example', 'done');
    $resolver = new RoadmapPipelineStatusResolver($this->featuresPath);

    $this->assertSame('done', $resolver->resolveRoadmapStatus('dc-cr-example', 'pending'));
  }

  /**
   * @covers ::resolveRoadmapStatus
   */
  public function testShippedMapsToImplemented(): void {
    // 'shipped' = QA-verified and released → implemented.
    $this->writeFeatureStatus('dc-cr-shipped-example', 'shipped');
    $resolver = new RoadmapPipelineStatusResolver($this->featuresPath);

    $this->assertSame('implemented', $resolver->resolveRoadmapStatus('dc-cr-shipped-example', 'pending'));
  }

  /**
   * @covers ::resolveRoadmapStatus
   */
  public function testResolveRoadmapStatusFallsBackToDatabaseStatus(): void {
    $resolver = new RoadmapPipelineStatusResolver($this->featuresPath);

    $this->assertSame('in_progress', $resolver->resolveRoadmapStatus('dc-cr-missing', 'in_progress'));
    $this->assertSame('pending', $resolver->resolveRoadmapStatus(NULL, 'pending'));
  }

  /**
   * @covers ::resolveRoadmapStatus
   */
  public function testReadyDeferredAndBacklogMapToPending(): void {
    $this->writeFeatureStatus('dc-cr-ready', 'ready');
    $this->writeFeatureStatus('dc-cr-deferred', 'deferred');
    $this->writeFeatureStatus('dc-cr-backlog', 'backlog');
    $resolver = new RoadmapPipelineStatusResolver($this->featuresPath);

    $this->assertSame('pending', $resolver->resolveRoadmapStatus('dc-cr-ready', 'implemented'));
    $this->assertSame('pending', $resolver->resolveRoadmapStatus('dc-cr-deferred', 'implemented'));
    $this->assertSame('pending', $resolver->resolveRoadmapStatus('dc-cr-backlog', 'implemented'));
  }

  /**
   * @covers ::getFeatureBacklogGroups
   */
  public function testGetFeatureBacklogGroupsSurfacesGroupedReadyBacklogFeatures(): void {
    $this->writeFeature(
      'dc-ui-map-first-player-shell',
      'ready',
      [
        'Website' => 'dungeoncrawler',
        'Priority' => 'P1',
        'Roadmap' => 'Dungeoncrawler UI modernization',
      ],
      'Feature Brief: Map-First Player Shell'
    );
    $this->writeFeature(
      'dc-ui-sidebar-drawers',
      'ready',
      [
        'Website' => 'dungeoncrawler',
        'Priority' => 'P2',
        'Roadmap' => 'Dungeoncrawler UI modernization',
      ],
      'Feature Brief: Sidebar Drawers'
    );
    $this->writeFeature(
      'dc-gmg-running-guide',
      'in_progress',
      [
        'Website' => 'dungeoncrawler',
        'Priority' => 'P1',
        'Roadmap' => 'GMG implementation',
      ],
      'Feature Brief: GMG Running Guide'
    );
    $this->writeFeature(
      'forseti-ignore-me',
      'ready',
      [
        'Website' => 'forseti.life',
        'Priority' => 'P1',
        'Roadmap' => 'Wrong product',
      ],
      'Feature Brief: Wrong Product'
    );
    $this->writeFeature(
      'dc-hidden-deferred',
      'deferred',
      [
        'Website' => 'dungeoncrawler',
        'Priority' => 'P1',
        'Roadmap' => 'Deferred bucket',
      ],
      'Feature Brief: Deferred Feature'
    );

    $resolver = new RoadmapPipelineStatusResolver($this->featuresPath, $this->releaseStatePath, $this->pushStatePath, $this->pmInboxPath);
    $groups = $resolver->getFeatureBacklogGroups('dungeoncrawler');

    $this->assertCount(2, $groups);
    $this->assertSame('Dungeoncrawler UI modernization', $groups[0]['title']);
    $this->assertSame(['queued' => 2, 'in_progress' => 0], $groups[0]['counts']);
    $this->assertSame('Map-First Player Shell', $groups[0]['features'][0]['title']);
    $this->assertSame('queued', $groups[0]['features'][0]['display_status']);
    $this->assertSame('P1', $groups[0]['features'][0]['priority']);
    $this->assertSame('Sidebar Drawers', $groups[0]['features'][1]['title']);
    $this->assertSame('GMG implementation', $groups[1]['title']);
    $this->assertSame(['queued' => 0, 'in_progress' => 1], $groups[1]['counts']);
    $this->assertSame('in_progress', $groups[1]['features'][0]['display_status']);
  }

  /**
   * @covers ::getFeatureBacklogGroups
   */
  public function testDeferredFeatureWithActivePmInboxShowsAsQueuedBacklog(): void {
    $this->writeFeature(
      'dc-cr-xp-award-system',
      'deferred',
      [
        'Website' => 'dungeoncrawler',
        'Priority' => 'P3',
        'Category' => 'rule-system',
      ],
      'Feature Brief: XP Award System'
    );
    $this->writeInboxItem('dc-cr-xp-award-system');

    $resolver = new RoadmapPipelineStatusResolver($this->featuresPath, $this->releaseStatePath, $this->pushStatePath, $this->pmInboxPath);
    $groups = $resolver->getFeatureBacklogGroups('dungeoncrawler');

    $this->assertCount(1, $groups);
    $this->assertSame('Rule System', $groups[0]['title']);
    $this->assertSame(['queued' => 1, 'in_progress' => 0], $groups[0]['counts']);
    $this->assertSame('dc-cr-xp-award-system', $groups[0]['features'][0]['feature_id']);
    $this->assertSame('queued', $groups[0]['features'][0]['display_status']);
  }

  /**
   * @covers ::getReleaseCycleSnapshot
   */
  public function testGetReleaseCycleSnapshotSurfacesActiveAndNextReleaseFeatures(): void {
    file_put_contents($this->releaseStatePath . '/dungeoncrawler.release_id', "20260412-dungeoncrawler-release-s\n");
    file_put_contents($this->releaseStatePath . '/dungeoncrawler.next_release_id', "20260412-dungeoncrawler-release-t\n");
    file_put_contents($this->releaseStatePath . '/dungeoncrawler.started_at', "2026-04-20T13:27:41+00:00\n");

    $this->writeFeature(
      'dc-cr-dwarf-ancestry',
      'done',
      [
        'Website' => 'dungeoncrawler',
        'Priority' => 'P1',
        'Release' => '20260412-dungeoncrawler-release-s',
      ],
      'Feature Brief: Dwarf Ancestry'
    );
    $this->writeFeature(
      'dc-cr-halfling-resolve',
      'in_progress',
      [
        'Website' => 'dungeoncrawler',
        'Priority' => 'P3',
        'Release' => '20260412-dungeoncrawler-release-s',
      ],
      'Feature Brief: Halfling Resolve'
    );
    $this->writeFeature(
      'dc-cr-elf-heritage-arctic',
      'ready',
      [
        'Website' => 'dungeoncrawler',
        'Priority' => 'P2',
        'Release' => '20260412-dungeoncrawler-release-t',
      ],
      'Feature Brief: Arctic Elf Heritage'
    );

    $resolver = new RoadmapPipelineStatusResolver($this->featuresPath, $this->releaseStatePath, $this->pushStatePath);
    $snapshot = $resolver->getReleaseCycleSnapshot('dungeoncrawler');

    $this->assertSame('20260412-dungeoncrawler-release-s', $snapshot['active_release']);
    $this->assertSame('20260412-dungeoncrawler-release-t', $snapshot['next_release']);
    $this->assertSame('in_progress', $snapshot['active_release_status']);
    $this->assertSame('In Progress', $snapshot['active_release_status_label']);
    $this->assertCount(2, $snapshot['active_features']);
    $this->assertSame('dc-cr-halfling-resolve', $snapshot['active_features'][0]['feature_id']);
    $this->assertSame('In Progress', $snapshot['active_features'][0]['status_label']);
    $this->assertCount(1, $snapshot['next_features']);
    $this->assertSame('dc-cr-elf-heritage-arctic', $snapshot['next_features'][0]['feature_id']);
    $this->assertSame('Queued', $snapshot['next_features'][0]['status_label']);
  }

  /**
   * @covers ::getReleaseCycleSnapshot
   */
  public function testGetReleaseCycleSnapshotShowsPushedStateWhenMarkerExists(): void {
    file_put_contents($this->releaseStatePath . '/dungeoncrawler.release_id', "20260412-dungeoncrawler-release-w\n");
    file_put_contents($this->releaseStatePath . '/dungeoncrawler.next_release_id', "20260412-dungeoncrawler-release-x\n");
    file_put_contents($this->releaseStatePath . '/dungeoncrawler.started_at', "2026-04-26T21:07:34+00:00\n");
    file_put_contents(
      $this->pushStatePath . '/20260412-dungeoncrawler-release-w__20260412-forseti-release-u.pushed',
      "2026-04-27T12:37:40+00:00\n"
    );

    $resolver = new RoadmapPipelineStatusResolver($this->featuresPath, $this->releaseStatePath, $this->pushStatePath);
    $snapshot = $resolver->getReleaseCycleSnapshot('dungeoncrawler');

    $this->assertSame('pushed_pending_advance', $snapshot['active_release_status']);
    $this->assertSame('implemented', $snapshot['active_release_status_display']);
    $this->assertSame('Pushed — awaiting cycle advance', $snapshot['active_release_status_label']);
    $this->assertNotSame('', $snapshot['active_release_pushed_at']);
    $this->assertStringContainsString('release-cycle files have not advanced yet', $snapshot['release_sync_note']);
  }

  /**
   * @covers ::getReleaseCycleSnapshot
   */
  public function testGetReleaseCycleSnapshotDoesNotTreatUnassignedFeaturesAsActiveRelease(): void {
    file_put_contents($this->releaseStatePath . '/dungeoncrawler.next_release_id', "20260412-dungeoncrawler-release-aa\n");

    $this->writeFeature(
      'dc-cr-skill-feats',
      'deferred',
      [
        'Website' => 'dungeoncrawler',
        'Priority' => 'P1',
      ],
      'Feature Brief: Skill Feats'
    );
    $this->writeFeature(
      'dc-cr-focus-spells',
      'ready',
      [
        'Website' => 'dungeoncrawler',
        'Priority' => 'P1',
        'Release' => '20260412-dungeoncrawler-release-aa',
      ],
      'Feature Brief: Focus Spells'
    );

    $resolver = new RoadmapPipelineStatusResolver($this->featuresPath, $this->releaseStatePath, $this->pushStatePath);
    $snapshot = $resolver->getReleaseCycleSnapshot('dungeoncrawler');

    $this->assertSame('', $snapshot['active_release']);
    $this->assertCount(0, $snapshot['active_features']);
    $this->assertCount(1, $snapshot['next_features']);
    $this->assertSame('dc-cr-focus-spells', $snapshot['next_features'][0]['feature_id']);
  }

  /**
   * @covers ::getReleaseCycleSnapshot
   */
  public function testGetReleaseCycleSnapshotShowsIdleAdvancedStateAfterBoundaryAdvance(): void {
    file_put_contents($this->releaseStatePath . '/dungeoncrawler.next_release_id', "20260412-dungeoncrawler-release-aa\n");
    file_put_contents($this->pushStatePath . '/dungeoncrawler.advanced', "20260412-dungeoncrawler-release-z\n");
    file_put_contents(
      $this->pushStatePath . '/20260412-dungeoncrawler-release-z.pushed',
      "2026-05-01T13:06:02+00:00\n"
    );

    $resolver = new RoadmapPipelineStatusResolver($this->featuresPath, $this->releaseStatePath, $this->pushStatePath, $this->pmInboxPath);
    $snapshot = $resolver->getReleaseCycleSnapshot('dungeoncrawler');

    $this->assertSame('', $snapshot['active_release']);
    $this->assertSame('20260412-dungeoncrawler-release-z', $snapshot['last_completed_release']);
    $this->assertSame('idle_advanced', $snapshot['active_release_status']);
    $this->assertSame('implemented', $snapshot['active_release_status_display']);
    $this->assertSame('Idle — waiting for scoped work', $snapshot['active_release_status_label']);
    $this->assertNotSame('', $snapshot['active_release_pushed_at']);
    $this->assertStringContainsString('cycle boundary already advanced', $snapshot['release_sync_note']);
  }

  /**
   * @covers ::getPipelineStatus
   * @dataProvider pathTraversalProvider
   */
  public function testGetPipelineStatusRejectsPathTraversal(string $malicious_id): void {
    $resolver = new RoadmapPipelineStatusResolver($this->featuresPath);
    $this->assertNull($resolver->getPipelineStatus($malicious_id));
  }

  /**
   * Data provider for path traversal test cases.
   */
  public static function pathTraversalProvider(): array {
    return [
      'double dot'           => ['..'],
      'double dot slash'     => ['../etc/passwd'],
      'nested traversal'     => ['foo/../bar'],
      'forward slash'        => ['foo/bar'],
      'backslash'            => ['foo\\bar'],
      'empty string'         => [''],
    ];
  }

  /**
   * Writes a minimal feature file for testing.
   */
  private function writeFeatureStatus(string $feature_id, string $status): void {
    $this->writeFeature($feature_id, $status);
  }

  /**
   * Writes a feature file with optional metadata for testing.
   */
  private function writeFeature(string $feature_id, string $status, array $fields = [], string $heading = 'Feature Brief: Example'): void {
    $dir = $this->featuresPath . '/' . $feature_id;
    mkdir($dir, 0777, TRUE);

    $lines = ["# {$heading}", '', "- Status: {$status}"];
    foreach ($fields as $label => $value) {
      $lines[] = sprintf('- %s: %s', $label, $value);
    }

    file_put_contents($dir . '/feature.md', implode("\n", $lines) . "\n");
  }

  /**
   * Writes a minimal PM inbox item for a feature.
   */
  private function writeInboxItem(string $feature_id): void {
    $dir = $this->pmInboxPath . '/20260428-backlog-intake-' . $feature_id;
    mkdir($dir, 0777, TRUE);
    file_put_contents($dir . '/command.md', "- Feature: `{$feature_id}`\n");
  }

  /**
   * Recursively deletes a temporary directory.
   */
  private function deleteDirectory(string $path): void {
    if (!is_dir($path)) {
      return;
    }

    $items = scandir($path);
    if ($items === FALSE) {
      return;
    }

    foreach ($items as $item) {
      if ($item === '.' || $item === '..') {
        continue;
      }
      $item_path = $path . '/' . $item;
      if (is_dir($item_path)) {
        $this->deleteDirectory($item_path);
      }
      elseif (file_exists($item_path)) {
        unlink($item_path);
      }
    }

    rmdir($path);
  }

}
