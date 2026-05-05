<?php

namespace Drupal\copilot_agent_tracker\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * LangGraph Console Admin Controller — settings, audit log, health, permissions.
 *
 * Implements Phase 7: Admin & Configuration features for operator control of console behavior
 * and real-time visibility into system health.
 */
final class LangGraphConsoleAdminController extends ControllerBase {

  /**
   * Admin index page — navigation to subsections.
   */
  public function index(): array {
    $sections = [
      [
        'title' => $this->t('Admin Settings'),
        'description' => $this->t('Configure console behavior: tick history, metrics window, drift threshold, alert retention, canary duration.'),
        'path' => '/langgraph-console/admin/settings',
      ],
      [
        'title' => $this->t('Permissions & Team Assignment'),
        'description' => $this->t('View permission matrix for console sections by role. Assign seats to your team view.'),
        'path' => '/langgraph-console/admin/permissions',
      ],
      [
        'title' => $this->t('Audit Log'),
        'description' => $this->t('View all console mutations (settings changes, permission updates, navigation config). Filter and export audit entries.'),
        'path' => '/langgraph-console/admin/audit-log',
      ],
      [
        'title' => $this->t('Health & Status Dashboard'),
        'description' => $this->t('Real-time system health: orchestrator status, tick frequency, parity health, agent pool status, data freshness.'),
        'path' => '/langgraph-console/admin/health',
      ],
      [
        'title' => $this->t('Navigation Controls'),
        'description' => $this->t('Customize console UI: set landing page, toggle section visibility, choose theme.'),
        'path' => '/langgraph-console/admin/navigation',
      ],
    ];

    $build = [
      '#type' => 'container',
      '#cache' => ['max-age' => 0],
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'h1',
        '#value' => $this->t('LangGraph Console Admin'),
      ],
      'description' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Configure console behavior, view audit logs, monitor system health, and manage permissions.'),
        '#attributes' => ['style' => 'margin-bottom: 1.5rem;'],
      ],
      'sections' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#attributes' => ['class' => 'admin-sections-grid'],
      ],
    ];

    foreach ($sections as $section) {
      $build['sections']['section_' . str_replace('-', '_', basename($section['path']))] = [
        '#type' => 'container',
        '#attributes' => ['class' => 'admin-section-card', 'style' => 'border: 1px solid #ccc; padding: 1rem; margin: 0.5rem 0; background: #fafafa;'],
        'title' => [
          '#type' => 'link',
          '#title' => $section['title'],
          '#url' => \Drupal\Core\Url::fromUserInput($section['path']),
          '#attributes' => ['class' => 'admin-section-title'],
        ],
        'description' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $section['description'],
          '#attributes' => ['style' => 'margin: 0.5rem 0 0 0; font-size: 0.9rem; color: #666;'],
        ],
      ];
    }

    return $build;
  }

  /**
   * Permissions & Team Assignment page.
   */
  public function permissions(): array {
    $hq_root = rtrim((string) (getenv('COPILOT_HQ_ROOT') ?: '/home/ubuntu/forseti.life/copilot-hq'), '/');

    $permission_matrix = [
      'header' => ['Role', 'Home', 'Build', 'Test', 'Run', 'Observe', 'Release', 'Admin'],
      'rows' => [
        ['administrator', '✓', '✓', '✓', '✓', '✓', '✓', '✓'],
        ['authenticated user', '✓', '—', '—', '—', '—', '—', '—'],
        ['anonymous', '✓', '—', '—', '—', '—', '—', '—'],
      ],
    ];

    $current_user = $this->currentUser();
    $user_entity = User::load($current_user->id());
    $team_seats = [];
    if ($user_entity && $user_entity->get('settings')) {
      $settings = $user_entity->get('settings')->getValue();
      if (isset($settings[0]['value'])) {
        $team_data = json_decode($settings[0]['value'], TRUE);
        $team_seats = $team_data['team_seats'] ?? [];
      }
    }

    $build = [
      '#type' => 'container',
      '#cache' => ['max-age' => 0],
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'h1',
        '#value' => $this->t('Permissions & Team Assignment'),
      ],

      'matrix_section' => [
        '#type' => 'container',
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#value' => $this->t('Permission Matrix'),
        ],
        'description' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->t('Read-only view of which Drupal roles have access to each console section. ✓ = granted, — = denied.'),
        ],
        'table' => [
          '#type' => 'table',
          '#header' => $permission_matrix['header'],
          '#rows' => $permission_matrix['rows'],
          '#attributes' => ['style' => 'width: 100%; border-collapse: collapse;'],
        ],
      ],

      'team_section' => [
        '#type' => 'container',
        '#attributes' => ['style' => 'margin-top: 2rem;'],
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#value' => $this->t('Team Assignment'),
        ],
        'description' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->t('Select seats to track in your team view. These assignments are stored in your user profile.'),
        ],
        'info_box' => [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#value' => $this->t('Your assigned seats: @seats', ['@seats' => !empty($team_seats) ? implode(', ', $team_seats) : $this->t('(none selected)')]),
          '#attributes' => ['style' => 'background: #e8f4f8; padding: 1rem; border: 1px solid #b3d9e6; border-radius: 4px; margin: 0.5rem 0;'],
        ],
        'note' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->t('Team assignment form is in the Navigation Controls page (to avoid duplicate form handling).'),
          '#attributes' => ['style' => 'font-size: 0.9rem; color: #666; margin-top: 1rem;'],
        ],
      ],
    ];

    return $build;
  }

  /**
   * Audit Log page.
   */
  public function auditLog(): array {
    $db = Database::getConnection();
    $hq_root = rtrim((string) (getenv('COPILOT_HQ_ROOT') ?: '/home/ubuntu/forseti.life/copilot-hq'), '/');
    $request = \Drupal::request();
    $query_params = $request->query->all();

    // Query builder with filters.
    $query = $db->select('copilot_agent_tracker_audit', 'a')->fields('a');

    // Apply filters from query params.
    if (!empty($query_params['operator'])) {
      $query->condition('a.operator_id', (int) $query_params['operator']);
    }
    if (!empty($query_params['action'])) {
      $query->condition('a.action', $query_params['action']);
    }
    if (!empty($query_params['from'])) {
      try {
        $from_time = strtotime($query_params['from']);
        if ($from_time) {
          $query->condition('a.timestamp', date('Y-m-d H:i:s', $from_time), '>=');
        }
      } catch (\Exception $e) {
        // Silently ignore invalid date.
      }
    }
    if (!empty($query_params['to'])) {
      try {
        $to_time = strtotime($query_params['to']);
        if ($to_time) {
          $query->condition('a.timestamp', date('Y-m-d H:i:s', $to_time), '<=');
        }
      } catch (\Exception $e) {
        // Silently ignore invalid date.
      }
    }
    if (!empty($query_params['resource'])) {
      $query->condition('a.resource_id', '%' . $db->escapeLike($query_params['resource']) . '%', 'LIKE');
    }

    // Get total count for pagination.
    $count_query = $db->select('copilot_agent_tracker_audit', 'a');
    $count_query->addExpression('COUNT(*)', 'cnt');
    foreach ($query->conditions() as $condition) {
      $count_query->condition($condition['field'], $condition['value'], $condition['operator'] ?? '=');
    }
    $total_count = $count_query->execute()->fetchField();

    // Pagination setup (default 50 per page).
    $per_page = 50;
    $current_page = (int) ($query_params['page'] ?? 0);
    $offset = $current_page * $per_page;

    // Fetch filtered and paginated entries.
    $query->orderBy('a.timestamp', 'DESC')
      ->range($offset, $per_page);

    $entries = $query->execute()->fetchAll();

    $rows = [];
    foreach ($entries as $entry) {
      $user = User::load($entry->operator_id);
      $operator_name = $user ? $user->getDisplayName() : 'Unknown';

      $rows[] = [
        $this->fmtTimestamp($entry->timestamp),
        $operator_name . ' (' . $entry->operator_id . ')',
        $entry->action,
        $entry->resource_id ?? '—',
        !empty($entry->before_value) ? substr($entry->before_value, 0, 50) . (strlen($entry->before_value) > 50 ? '...' : '') : '—',
        !empty($entry->after_value) ? substr($entry->after_value, 0, 50) . (strlen($entry->after_value) > 50 ? '...' : '') : '—',
        $entry->csrf_verified ? '✓' : '✗',
      ];
    }

    // Build pagination query string (preserve filters).
    $pagination_query = array_filter($query_params, function ($k) {
      return $k !== 'page';
    }, ARRAY_FILTER_USE_KEY);

    // Pagination links.
    $pagination_html = '';
    if ($current_page > 0) {
      $prev_query = array_merge($pagination_query, ['page' => $current_page - 1]);
      $pagination_html .= '<a href="' . \Drupal\Core\Url::fromRoute('copilot_agent_tracker.admin_audit_log', [], ['query' => $prev_query])->toString() . '">&larr; Previous</a> ';
    }
    if (($current_page + 1) * $per_page < $total_count) {
      $next_query = array_merge($pagination_query, ['page' => $current_page + 1]);
      $pagination_html .= '<a href="' . \Drupal\Core\Url::fromRoute('copilot_agent_tracker.admin_audit_log', [], ['query' => $next_query])->toString() . '">Next &rarr;</a>';
    }

    // Export CSV link.
    $export_query = array_filter($query_params, function ($k) {
      return $k !== 'page';
    }, ARRAY_FILTER_USE_KEY);
    $export_url = \Drupal\Core\Url::fromRoute('copilot_agent_tracker.admin_audit_export', [], ['query' => $export_query])->toString();

    $build = [
      '#type' => 'container',
      '#cache' => ['max-age' => 0],
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'h1',
        '#value' => $this->t('Audit Log Viewer'),
      ],
      'description' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('All console mutations (settings changes, permission updates, navigation config). Showing @count of @total entries.', [
          '@count' => count($rows),
          '@total' => $total_count,
        ]),
      ],
      'info' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $this->t('Note: Entries older than 30 days are purged daily.'),
        '#attributes' => ['style' => 'background: #e8f5e9; padding: 0.5rem; border: 1px solid #c8e6c9; border-radius: 4px; margin: 0.5rem 0 1rem 0;'],
      ],
      'filter_form' => [
        '#type' => 'container',
      ],
      'export_link' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => '<a href="' . $export_url . '" style="margin: 1rem 0; display: inline-block; padding: 0.5rem 1rem; background: #0066cc; color: white; text-decoration: none; border-radius: 4px;">📥 Export as CSV</a>',
        '#attributes' => ['style' => 'margin-bottom: 1rem;'],
      ],
      'table' => [
        '#type' => 'table',
        '#header' => [
          $this->t('Timestamp'),
          $this->t('Operator'),
          $this->t('Action'),
          $this->t('Resource ID'),
          $this->t('Before'),
          $this->t('After'),
          $this->t('CSRF'),
        ],
        '#rows' => $rows,
        '#empty' => $this->t('No audit entries found.'),
      ],
      'pagination' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $pagination_html ?: '',
        '#attributes' => ['style' => 'margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #ddd;'],
      ],
    ];

    // Include the filter form via form builder.
    $form_builder = \Drupal::formBuilder();
    $build['filter_form']['#children'] = $form_builder->getForm(\Drupal\copilot_agent_tracker\Form\AuditLogFilterForm::class);

    return $build;
  }

  /**
   * Health & Status Dashboard page.
   */
  public function health(): array {
    $hq_root = rtrim((string) (getenv('COPILOT_HQ_ROOT') ?: '/home/ubuntu/forseti.life/copilot-hq'), '/');
    $health_data = $this->loadHealthData($hq_root);

    $orch_status_color = 'red';
    $orch_status_icon = '✗';
    $orch_status_text = $this->t('Unknown / offline');

    if ($health_data['last_tick_time']) {
      $age_sec = time() - $health_data['last_tick_time'];
      if ($age_sec < 300) { // 5 minutes
        $orch_status_color = 'green';
        $orch_status_icon = '✓';
        $orch_status_text = $this->t('Online (last tick @ago)', ['@ago' => $this->fmtAge($age_sec)]);
      } elseif ($age_sec < 900) { // 15 minutes
        $orch_status_color = 'orange';
        $orch_status_icon = '⚠';
        $orch_status_text = $this->t('Slow (last tick @ago)', ['@ago' => $this->fmtAge($age_sec)]);
      } else {
        $orch_status_color = 'red';
        $orch_status_icon = '✗';
        $orch_status_text = $this->t('Offline (last tick @ago)', ['@ago' => $this->fmtAge($age_sec)]);
      }
    }

    $build = [
      '#type' => 'container',
      '#cache' => ['max-age' => 0],
      '#attached' => [
        'library' => ['copilot_agent_tracker/health-dashboard'],
      ],
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'h1',
        '#value' => $this->t('Health & Status Dashboard'),
      ],
      'refresh_info' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Auto-refreshes every 30 seconds. Last refreshed: @time', [
          '@time' => $this->fmtTimestamp(time()),
        ]),
        '#attributes' => ['id' => 'health-refresh-time', 'style' => 'font-size: 0.85rem; color: #666;'],
      ],

      'orchestrator_section' => [
        '#type' => 'container',
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#value' => $this->t('Orchestrator Status'),
        ],
        'status_box' => [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#value' => $orch_status_icon . ' ' . $orch_status_text,
          '#attributes' => [
            'style' => "background: #f0f0f0; color: $orch_status_color; padding: 1rem; border: 2px solid $orch_status_color; border-radius: 4px; font-size: 1.1rem; font-weight: bold;",
          ],
        ],
        'details' => [
          '#type' => 'html_tag',
          '#tag' => 'ul',
          '#value' => '<li>' . $this->t('Last tick: @time', ['@time' => $health_data['last_tick_time'] ? $this->fmtTimestamp($health_data['last_tick_time']) : $this->t('(unknown)')]) . '</li>'
            . '<li>' . $this->t('Tick frequency: @freq minutes (expected 2 min)', ['@freq' => number_format($health_data['tick_frequency'], 1)]) . '</li>'
            . '<li>' . $this->t('Parity: @parity', ['@parity' => $health_data['parity_ok'] ? '<span style="color:green;">✓ OK</span>' : '<span style="color:red;">✗ NOT OK</span>']) . '</li>'
            . '<li>' . $this->t('Provider: @provider', ['@provider' => $health_data['provider'] ?? 'Unknown']) . '</li>',
          '#attributes' => ['style' => 'margin: 0.5rem 0; padding-left: 1.5rem;'],
        ],
      ],

      'data_freshness_section' => [
        '#type' => 'container',
        '#attributes' => ['style' => 'margin-top: 2rem;'],
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#value' => $this->t('Data Freshness'),
        ],
        'items' => [
          '#type' => 'html_tag',
          '#tag' => 'ul',
          '#value' => '<li>' . $this->t('Ticks JSONL: @status (updated @time ago)', [
            '@status' => $health_data['ticks_fresh'] ? '<span style="color:green;">✓</span>' : '<span style="color:red;">✗</span>',
            '@time' => $health_data['ticks_age_text'],
          ]) . '</li>'
          . '<li>' . $this->t('Feature Progress: @status (updated @time ago)', [
            '@status' => $health_data['feature_progress_fresh'] ? '<span style="color:green;">✓</span>' : '<span style="color:red;">✗</span>',
            '@time' => $health_data['feature_progress_age_text'],
          ]) . '</li>'
          . ($health_data['executor_failures_count'] > 0 ? '<li style="color:orange;"><strong>Executor errors detected: ' . $health_data['executor_failures_count'] . ' items</strong></li>' : ''),
          '#attributes' => ['style' => 'margin: 0.5rem 0; padding-left: 1.5rem;'],
        ],
      ],

      'agents_section' => [
        '#type' => 'container',
        '#attributes' => ['style' => 'margin-top: 2rem;'],
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#value' => $this->t('Agent Status Pool'),
        ],
      ],
    ];

    // Build agent status table.
    if (!empty($health_data['agents'])) {
      $agent_rows = [];
      foreach ($health_data['agents'] as $agent) {
        $status_color = match($agent['status']) {
          'working' => 'blue',
          'error' => 'red',
          default => 'green',
        };
        $status_icon = match($agent['status']) {
          'working' => '●',
          'error' => '✗',
          default => '✓',
        };

        $agent_rows[] = [
          $agent['seat_id'],
          'forseti',
          '<span style="color:' . $status_color . ';">' . $status_icon . ' ' . ucfirst($agent['status']) . '</span>',
          $agent['inbox_size'],
          $agent['last_modified'] ? $this->fmtAge(time() - $agent['last_modified']) : 'N/A',
        ];
      }

      $build['agents_section']['table'] = [
        '#type' => 'table',
        '#header' => [
          $this->t('Seat ID'),
          $this->t('Module'),
          $this->t('Status'),
          $this->t('Inbox Size'),
          $this->t('Last Modified'),
        ],
        '#rows' => $agent_rows,
      ];
    } else {
      $build['agents_section']['empty'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('No agent inbox directories found.'),
      ];
    }

    return $build;
  }

  /**
   * Health JSON endpoint (for AJAX refresh).
   */
  public function healthJson(): JsonResponse {
    $hq_root = rtrim((string) (getenv('COPILOT_HQ_ROOT') ?: '/home/ubuntu/forseti.life/copilot-hq'), '/');
    $health_data = $this->loadHealthData($hq_root);
    return new JsonResponse($health_data);
  }

  /**
   * Load health status from telemetry files.
   */
  private function loadHealthData(string $hq_root): array {
    $ticks_path = $hq_root . '/inbox/responses/langgraph-ticks.jsonl';
    $parity_path = $hq_root . '/inbox/responses/langgraph-parity-latest.json';
    $progress_path = $hq_root . '/dashboards/FEATURE_PROGRESS.md';

    $last_tick_time = 0;
    $tick_frequency = 0;
    $parity_ok = FALSE;
    $provider = 'Unknown';
    $ticks_fresh = FALSE;
    $ticks_age_text = 'N/A';
    $feature_progress_fresh = FALSE;
    $feature_progress_age_text = 'N/A';

    // Read last tick time from JSONL.
    if (is_readable($ticks_path)) {
      $lines = @file($ticks_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      if (is_array($lines) && count($lines) > 0) {
        $last_line = end($lines);
        $tick = json_decode($last_line, TRUE);
        if (is_array($tick) && isset($tick['ts'])) {
          $last_tick_time = strtotime($tick['ts']) ?: 0;
          $provider = $tick['provider'] ?? 'Unknown';

          // Calculate tick frequency from last 10 ticks.
          $ticks_to_sample = array_slice($lines, -10);
          if (count($ticks_to_sample) > 1) {
            $times = [];
            foreach ($ticks_to_sample as $line) {
              $t = json_decode($line, TRUE);
              if (is_array($t) && isset($t['ts'])) {
                $times[] = strtotime($t['ts']) ?: 0;
              }
            }
            if (count($times) > 1) {
              $diffs = [];
              for ($i = 1; $i < count($times); $i++) {
                $diffs[] = $times[$i] - $times[$i - 1];
              }
              $tick_frequency = array_sum($diffs) / count($diffs) / 60; // Convert to minutes.
            }
          }
        }
      }

      $ticks_mtime = @filemtime($ticks_path);
      if ($ticks_mtime) {
        $age_sec = time() - $ticks_mtime;
        $ticks_fresh = $age_sec < 300; // 5 minutes.
        $ticks_age_text = $this->fmtAge($age_sec);
      }
    }

    // Read parity status from JSON.
    if (is_readable($parity_path)) {
      $parity = json_decode(@file_get_contents($parity_path), TRUE);
      if (is_array($parity)) {
        $parity_ok = $parity['parity_ok'] ?? FALSE;
      }
    }

    // Load per-agent status from sessions/*/inbox/ directories.
    $agents = [];
    $sessions_root = dirname(rtrim($hq_root, '/')) . '/sessions';
    if (is_dir($sessions_root)) {
      $agent_dirs = array_filter(glob($sessions_root . '/*'), 'is_dir');
      foreach ($agent_dirs as $agent_dir) {
        $agent_id = basename($agent_dir);
        $inbox_dir = $agent_dir . '/inbox';

        if (is_dir($inbox_dir)) {
          $inbox_items = array_filter(glob($inbox_dir . '/*'), 'is_dir');
          $inbox_count = count($inbox_items);

          // Get most recent inbox item's last modified time.
          $last_modified = 0;
          foreach ($inbox_items as $item) {
            $item_mtime = @filemtime($item);
            if ($item_mtime > $last_modified) {
              $last_modified = $item_mtime;
            }
          }

          // Determine agent status: check for 'working' or 'error' indicators.
          $agent_status = 'idle';
          if ($inbox_count > 0) {
            // Check most recent item for status file.
            usort($inbox_items, function ($a, $b) {
              return @filemtime($b) - @filemtime($a);
            });
            $latest_item = reset($inbox_items);
            $command_file = $latest_item . '/command.md';
            if (is_readable($command_file)) {
              $command = @file_get_contents($command_file);
              if (strpos($command, 'Status: blocked') !== FALSE) {
                $agent_status = 'error';
              } elseif ($inbox_count > 0) {
                $agent_status = 'working';
              }
            }
          }

          $agents[] = [
            'seat_id' => $agent_id,
            'status' => $agent_status,
            'inbox_size' => $inbox_count,
            'last_modified' => $last_modified,
          ];
        }
      }
    }

    return [
      'last_tick_time' => $last_tick_time,
      'tick_frequency' => $tick_frequency,
      'parity_ok' => $parity_ok,
      'provider' => $provider,
      'ticks_fresh' => $ticks_fresh,
      'ticks_age_text' => $ticks_age_text,
      'feature_progress_fresh' => $feature_progress_fresh,
      'feature_progress_age_text' => $feature_progress_age_text,
      'agents' => $agents,
      'executor_failures_count' => count(array_filter(glob($hq_root . '/inbox/executor-failures/*'), 'is_file')),
    ];
  }

  /**
   * Audit log CSV export endpoint.
   */
  public function auditExport(): Response {
    $db = Database::getConnection();
    $request = \Drupal::request();
    $query_params = $request->query->all();

    // Build same filtered query as auditLog().
    $query = $db->select('copilot_agent_tracker_audit', 'a')->fields('a');

    // Apply filters from query params.
    if (!empty($query_params['operator'])) {
      $query->condition('a.operator_id', (int) $query_params['operator']);
    }
    if (!empty($query_params['action'])) {
      $query->condition('a.action', $query_params['action']);
    }
    if (!empty($query_params['from'])) {
      try {
        $from_time = strtotime($query_params['from']);
        if ($from_time) {
          $query->condition('a.timestamp', date('Y-m-d H:i:s', $from_time), '>=');
        }
      } catch (\Exception $e) {
        // Silently ignore invalid date.
      }
    }
    if (!empty($query_params['to'])) {
      try {
        $to_time = strtotime($query_params['to']);
        if ($to_time) {
          $query->condition('a.timestamp', date('Y-m-d H:i:s', $to_time), '<=');
        }
      } catch (\Exception $e) {
        // Silently ignore invalid date.
      }
    }
    if (!empty($query_params['resource'])) {
      $query->condition('a.resource_id', '%' . $db->escapeLike($query_params['resource']) . '%', 'LIKE');
    }

    $query->orderBy('a.timestamp', 'DESC');
    $entries = $query->execute()->fetchAll();

    // Generate CSV.
    $output = fopen('php://memory', 'r+');
    fputcsv($output, ['Timestamp', 'Operator ID', 'Operator Name', 'Action', 'Resource ID', 'Before Value', 'After Value', 'CSRF Verified']);

    foreach ($entries as $entry) {
      $user = User::load($entry->operator_id);
      $operator_name = $user ? $user->getDisplayName() : 'Unknown';

      fputcsv($output, [
        $entry->timestamp,
        $entry->operator_id,
        $operator_name,
        $entry->action,
        $entry->resource_id ?? '',
        $entry->before_value ?? '',
        $entry->after_value ?? '',
        $entry->csrf_verified ? '1' : '0',
      ]);
    }

    rewind($output);
    $csv_content = stream_get_contents($output);
    fclose($output);

    $timestamp = date('Ymd-His');
    $response = new Response($csv_content);
    $response->headers->set('Content-Type', 'text/csv');
    $response->headers->set('Content-Disposition', "attachment; filename=\"langgraph-audit-export-$timestamp.csv\"");

    return $response;
  }

  /**
   * Format timestamp as ISO 8601 + human-readable local time.
   */
  private function fmtTimestamp(int $timestamp): string {
    return gmdate('Y-m-d H:i:s', $timestamp) . ' UTC';
  }

  /**
   * Format time duration as human-readable text.
   */
  private function fmtAge(int $seconds): string {
    if ($seconds < 60) {
      return "$seconds seconds ago";
    } elseif ($seconds < 3600) {
      $minutes = intdiv($seconds, 60);
      return "$minutes minute(s) ago";
    } else {
      $hours = intdiv($seconds, 3600);
      return "$hours hour(s) ago";
    }
  }

}
