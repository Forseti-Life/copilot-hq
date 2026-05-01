<?php

namespace Drupal\dungeoncrawler_content\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\PageCache\ResponsePolicy\KillSwitch;
use Drupal\dungeoncrawler_content\Service\RoadmapPipelineStatusResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for the PF2E requirements roadmap page.
 */
class RoadmapController extends ControllerBase {

  const STATUS_LABELS = [
    'pending'     => '❌ Not Started',
    'queued'      => '🗂️ Queued',
    'in_progress' => '🔄 In Progress',
    'done'        => '☑️ Done',
    'implemented' => '✅ Implemented',
  ];

  const DELIVERY_FLOW = [
    'pending' => [
      'step' => 1,
      'title' => 'Not started',
      'badge_label' => 'First stage',
      'badge_status' => 'pending',
      'stage_class' => 'pending',
      'is_final' => FALSE,
      'copy' => 'Features not yet pulled into active implementation work.',
    ],
    'in_progress' => [
      'step' => 2,
      'title' => 'In progress',
      'badge_label' => 'Working',
      'badge_status' => 'in_progress',
      'stage_class' => 'active',
      'is_final' => FALSE,
      'copy' => 'Features actively being built, wired, or validated.',
    ],
    'done' => [
      'step' => 3,
      'title' => 'Done',
      'badge_label' => 'Ready to ship',
      'badge_status' => 'done',
      'stage_class' => 'done',
      'is_final' => FALSE,
      'copy' => 'Feature implementation is complete and awaiting release completion.',
    ],
    'implemented' => [
      'step' => 4,
      'title' => 'Implemented',
      'badge_label' => 'Last stage',
      'badge_status' => 'implemented',
      'stage_class' => 'done',
      'is_final' => TRUE,
      'copy' => 'Features already shipped and reflected in live product behavior.',
    ],
  ];

  const BOOK_ORDER = ['core', 'apg', 'gmg', 'gng', 'som', 'gam', 'b1', 'b2', 'b3'];

  protected Connection $database;

  protected KillSwitch $killSwitch;

  protected RoadmapPipelineStatusResolver $pipelineStatusResolver;

  public function __construct(Connection $database, KillSwitch $kill_switch, RoadmapPipelineStatusResolver $pipeline_status_resolver) {
    $this->database = $database;
    $this->killSwitch = $kill_switch;
    $this->pipelineStatusResolver = $pipeline_status_resolver;
  }

  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('database'),
      $container->get('page_cache_kill_switch'),
      $container->get('dungeoncrawler_content.roadmap_pipeline_status_resolver')
    );
  }

  /**
   * Renders the /roadmap page.
   */
  public function page(): array {
    // The roadmap reads live release state from filesystem artifacts outside
    // Drupal's cache-tag graph, so page cache must be bypassed to keep the
    // release snapshot aligned with the current release cycle.
    $this->killSwitch->trigger();

    // Requirements linked to a feature_id inherit status from the release
    // pipeline automatically. Unlinked requirements still use stored DB status.
    $is_admin = FALSE;
    $release_snapshot = $this->pipelineStatusResolver->getReleaseCycleSnapshot('dungeoncrawler');
    $backlog_groups = $this->pipelineStatusResolver->getFeatureBacklogGroups('dungeoncrawler');
    $active_release_feature_ids = array_fill_keys(array_map(
      static fn(array $feature): string => (string) ($feature['feature_id'] ?? ''),
      $release_snapshot['active_features'] ?? []
    ), TRUE);
    unset($active_release_feature_ids['']);
    $next_release_feature_ids = array_fill_keys(array_map(
      static fn(array $feature): string => (string) ($feature['feature_id'] ?? ''),
      $release_snapshot['next_features'] ?? []
    ), TRUE);
    unset($next_release_feature_ids['']);

    // Fetch all requirements ordered for grouping.
    $rows = $this->database->select('dc_requirements', 'r')
      ->fields('r')
      ->orderBy('r.book_id')
      ->orderBy('r.chapter_key')
      ->orderBy('r.section')
      ->orderBy('r.id')
      ->execute()
      ->fetchAll();

    // Build grouped tree: books → chapters → sections → requirements.
    $books = [];
    $totals = ['pending' => 0, 'in_progress' => 0, 'done' => 0, 'implemented' => 0];
    $release_scope_counts = [
      'active' => [
        'features' => count($release_snapshot['active_features'] ?? []),
        'requirements' => 0,
      ],
      'next' => [
        'features' => count($release_snapshot['next_features'] ?? []),
        'requirements' => 0,
      ],
    ];

    foreach ($rows as $row) {
      $bid = $row->book_id;
      $ck  = $row->chapter_key;
      $sec = $row->section ?: 'General';

      if (!isset($books[$bid])) {
        $books[$bid] = [
          'id'       => $bid,
          'title'    => $row->book_title,
          'chapters' => [],
          'counts'   => ['pending' => 0, 'in_progress' => 0, 'done' => 0, 'implemented' => 0],
        ];
      }
      if (!isset($books[$bid]['chapters'][$ck])) {
        $books[$bid]['chapters'][$ck] = [
          'key'      => $ck,
          'title'    => $row->chapter_title,
          'sections' => [],
          'counts'   => ['pending' => 0, 'in_progress' => 0, 'done' => 0, 'implemented' => 0],
        ];
      }
      if (!isset($books[$bid]['chapters'][$ck]['sections'][$sec])) {
        $books[$bid]['chapters'][$ck]['sections'][$sec] = [];
      }

      $pipeline_status = !empty($row->feature_id)
        ? $this->pipelineStatusResolver->getPipelineStatus((string) $row->feature_id)
        : NULL;
      $resolved_status = $this->pipelineStatusResolver->resolveRoadmapStatus($row->feature_id ?? NULL, $row->status);
      $display_status = $pipeline_status === 'ready' ? 'queued' : $resolved_status;

      $books[$bid]['chapters'][$ck]['sections'][$sec][] = [
        'id'              => $row->id,
        'paragraph_title' => $row->paragraph_title,
        'req_text'        => $row->req_text,
        'status'          => $resolved_status,
        'display_status'  => $display_status,
        'status_label'    => self::STATUS_LABELS[$display_status] ?? $display_status,
        'feature_id'      => $row->feature_id ?? '',
      ];

      $books[$bid]['counts'][$resolved_status]++;
      $books[$bid]['chapters'][$ck]['counts'][$resolved_status]++;
      $totals[$resolved_status]++;

      $feature_id = (string) ($row->feature_id ?? '');
      if ($feature_id !== '') {
        if (isset($active_release_feature_ids[$feature_id])) {
          $release_scope_counts['active']['requirements']++;
        }
        elseif (isset($next_release_feature_ids[$feature_id])) {
          $release_scope_counts['next']['requirements']++;
        }
      }
    }

    // Sort books by canonical order.
    $ordered_books = [];
    foreach (self::BOOK_ORDER as $bid) {
      if (isset($books[$bid])) {
        $ordered_books[] = $books[$bid];
      }
    }
    // Append any books not in the predefined order.
    foreach ($books as $bid => $book) {
      if (!in_array($bid, self::BOOK_ORDER)) {
        $ordered_books[] = $book;
      }
    }

    $feature_counts = $this->pipelineStatusResolver->getFeatureCounts('dungeoncrawler', $release_snapshot);
    $feature_flow_counts = $this->pipelineStatusResolver->getFeatureFlowCounts('dungeoncrawler');
    $delivery_flow = $this->buildDeliveryFlow($totals, $feature_flow_counts);
    $release_process_flow = $this->buildReleaseProcessFlow($release_snapshot, $release_scope_counts);

    $total = array_sum($totals);
    $done_pct = $total > 0 ? round(($totals['done'] / $total) * 100) : 0;
    $implemented_pct = $total > 0 ? round(($totals['implemented'] / $total) * 100) : 0;
    $in_progress_pct = $total > 0 ? round(($totals['in_progress'] / $total) * 100) : 0;

    return [
      '#theme'      => 'dungeoncrawler_roadmap',
      '#books'      => $ordered_books,
      '#totals'     => $totals,
      '#total'      => $total,
      '#done_pct'   => $done_pct,
      '#impl_pct'   => $implemented_pct,
      '#prog_pct'   => $in_progress_pct,
      '#is_admin'   => $is_admin,
      '#feature_counts' => $feature_counts,
      '#delivery_flow' => $delivery_flow,
      '#release_process_flow' => $release_process_flow,
      '#release_snapshot' => $release_snapshot,
      '#backlog_groups' => $backlog_groups,
      '#status_labels' => self::STATUS_LABELS,
      '#attached'   => ['library' => ['dungeoncrawler_content/dungeoncrawler_roadmap']],
      '#cache'      => [
        'max-age' => 0,
        'tags'    => ['dc_requirements'],
      ],
    ];
  }

  /**
   * AJAX handler to update a requirement's status.
   * POST /roadmap/requirement/{req_id}/status
   * Body: { "status": "implemented" }
   */
  public function updateStatus(Request $request, int $req_id): JsonResponse {
    $data = json_decode($request->getContent(), TRUE);
    $status = $data['status'] ?? NULL;

    if (!$status || !isset(self::STATUS_LABELS[$status])) {
      return new JsonResponse(['error' => 'Invalid status value.'], 400);
    }

    $updated = $this->database->update('dc_requirements')
      ->fields([
        'status'     => $status,
        'updated_at' => time(),
        'updated_by' => (int) $this->currentUser()->id(),
      ])
      ->condition('id', $req_id)
      ->execute();

    if (!$updated) {
      return new JsonResponse(['error' => 'Requirement not found.'], 404);
    }

    return new JsonResponse([
      'id'     => $req_id,
      'status' => $status,
        'label'  => self::STATUS_LABELS[$status],
    ]);
  }

  /**
   * Builds the unified feature-led delivery flow for the roadmap dashboard.
   *
   * @param array<string, int> $requirement_counts
   *   Requirement totals keyed by roadmap stage.
   * @param array<string, int> $feature_counts
   *   Feature totals keyed by roadmap stage.
   *
   * @return array<int, array<string, int|string|bool>>
   *   Ordered dashboard stage rows.
   */
  private function buildDeliveryFlow(array $requirement_counts, array $feature_counts): array {
    $flow = [];

    foreach (self::DELIVERY_FLOW as $status => $meta) {
      $flow[] = $meta + [
        'status' => $status,
        'requirements' => (int) ($requirement_counts[$status] ?? 0),
        'features' => (int) ($feature_counts[$status] ?? 0),
      ];
    }

    return $flow;
  }

  /**
   * Builds the ordered release process flow with stage-specific counts.
   *
   * @param array<string, mixed> $release_snapshot
   *   Live release snapshot data.
   * @param array<string, array<string, int>> $release_scope_counts
   *   Requirement/feature counts for active and next release scope.
   *
   * @return array<int, array<string, int|string|bool>>
   *   Ordered release process rows for Twig.
   */
  private function buildReleaseProcessFlow(array $release_snapshot, array $release_scope_counts): array {
    $is_pushed_pending_advance = ($release_snapshot['active_release_status'] ?? '') === 'pushed_pending_advance';
    $is_idle_advanced = ($release_snapshot['active_release_status'] ?? '') === 'idle_advanced';
    $active_features = (int) ($release_scope_counts['active']['features'] ?? 0);
    $active_requirements = (int) ($release_scope_counts['active']['requirements'] ?? 0);
    $next_features = (int) ($release_scope_counts['next']['features'] ?? 0);
    $next_requirements = (int) ($release_scope_counts['next']['requirements'] ?? 0);

    return [
      [
        'step' => 1,
        'badge_label' => $is_pushed_pending_advance ? 'Completed' : ($is_idle_advanced ? 'Idle' : 'Current'),
        'badge_status' => $is_pushed_pending_advance ? 'done' : ($is_idle_advanced ? 'queued' : 'in_progress'),
        'stage_class' => $is_pushed_pending_advance ? 'done' : ($is_idle_advanced ? 'tracked' : 'active'),
        'title' => 'Work active release',
        'value' => (string) ($release_snapshot['active_release'] ?: '—'),
        'features' => $active_features,
        'requirements' => $active_requirements,
        'copy' => $is_idle_advanced
          ? 'No release is currently active; the previous cycle has already been advanced.'
          : 'This is the release currently being built, tested, and scoped.',
      ],
      [
        'step' => 2,
        'badge_label' => ($is_pushed_pending_advance || $is_idle_advanced) ? 'Recorded' : 'Awaiting',
        'badge_status' => ($is_pushed_pending_advance || $is_idle_advanced) ? 'implemented' : 'pending',
        'stage_class' => ($is_pushed_pending_advance || $is_idle_advanced) ? 'done' : 'pending',
        'title' => 'Push to production',
        'value' => $is_idle_advanced
          ? (string) (($release_snapshot['last_completed_release'] ?? '') ?: (($release_snapshot['active_release_pushed_at'] ?? '') ?: 'Already pushed'))
          : (string) (($release_snapshot['active_release_pushed_at'] ?? '') ?: 'Not pushed yet'),
        'features' => $active_features,
        'requirements' => $active_requirements,
        'copy' => $is_idle_advanced
          ? 'The most recent release already landed in production.'
          : 'Production deployment of the active release lands here.',
      ],
      [
        'step' => 3,
        'badge_label' => $is_pushed_pending_advance ? 'Current' : ($is_idle_advanced ? 'Completed' : 'Later'),
        'badge_status' => $is_pushed_pending_advance ? 'in_progress' : ($is_idle_advanced ? 'done' : 'pending'),
        'stage_class' => $is_pushed_pending_advance ? 'active' : ($is_idle_advanced ? 'done' : 'pending'),
        'title' => 'Advance release cycle',
        'value' => $is_pushed_pending_advance
          ? 'Ready to activate next release'
          : ($is_idle_advanced ? 'Boundary advanced — waiting for scoped work' : 'Waiting for production push'),
        'features' => $next_features,
        'requirements' => $next_requirements,
        'copy' => $is_idle_advanced
          ? 'The release boundary has already advanced; the next cycle starts once backlog work is scoped.'
          : 'After push, the boundary advances so the next release can begin.',
      ],
      [
        'step' => 4,
        'badge_label' => 'Queued',
        'badge_status' => 'queued',
        'stage_class' => 'tracked',
        'title' => 'Start next release',
        'value' => (string) ($release_snapshot['next_release'] ?: '—'),
        'features' => $next_features,
        'requirements' => $next_requirements,
        'copy' => 'This release becomes active once the cycle boundary advances.',
      ],
    ];
  }

}
