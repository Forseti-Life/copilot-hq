<?php

namespace Drupal\copilot_agent_tracker\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * LangGraph control-plane UI backed by HQ runtime artifacts.
 */
final class LangGraphConsoleStubController extends ControllerBase {

  private const DEFAULT_FORSETI_ROOT = '/home/ubuntu/forseti.life';

  private const DEFAULT_HQ_ROOTS = [
    '/home/ubuntu/copilot-sessions-hq',
    '/home/ubuntu/forseti.life/copilot-hq',
    '/home/keithaumiller/copilot-sessions-hq',
  ];

  /**
   * Redirect legacy tracker landing page to the LangGraph overview.
   */
  public function redirectToOverview(): RedirectResponse {
    return $this->redirect('copilot_agent_tracker.langgraph_overview');
  }

  /**
   * Redirect legacy console pages to the LangGraph overview.
   */
  public function redirectLegacyConsole(?string $section = NULL, ?string $subsection = NULL): RedirectResponse {
    return $this->redirect('copilot_agent_tracker.langgraph_overview');
  }

  /**
   * Overview page.
   */
  public function overview(): array {
    $latest_tick = $this->readLatestTick();
    $parity = $this->readParity();
    $org_control = $this->readOrgControl();
    $release_control = $this->readReleaseControl();
    $step_results = is_array($latest_tick['step_results'] ?? NULL) ? $latest_tick['step_results'] : [];

    $build = $this->buildPage(
      'LangGraph Control Plane',
      'System-level health and control posture for the HQ LangGraph runtime.'
    );

    $build['status'] = $this->tableDetails(
      'Control plane status',
      ['Signal', 'Current Value', 'Source'],
      [
        [$this->t('Latest tick timestamp'), (string) ($latest_tick['ts'] ?? 'unavailable'), 'inbox/responses/langgraph-ticks.jsonl'],
        [$this->t('Latest tick age'), $this->formatAgeFromTimestamp((string) ($latest_tick['ts'] ?? '')), 'derived from latest tick timestamp'],
        [$this->t('Engine mode'), (string) ($latest_tick['engine_mode'] ?? 'unknown'), 'inbox/responses/langgraph-ticks.jsonl'],
        [$this->t('Provider'), (string) ($latest_tick['provider'] ?? 'unknown'), 'inbox/responses/langgraph-ticks.jsonl'],
        [$this->t('dry_run'), $this->boolLabel($latest_tick['dry_run'] ?? NULL), 'inbox/responses/langgraph-ticks.jsonl'],
        [$this->t('publish_enabled'), $this->boolLabel($latest_tick['publish_enabled'] ?? NULL), 'inbox/responses/langgraph-ticks.jsonl'],
        [$this->t('Org automation enabled'), $this->boolLabel($org_control['enabled'] ?? TRUE), $this->toRelativeHqPath($this->artifactPaths()['org_control'])],
        [$this->t('Release-cycle enabled'), $this->boolLabel($release_control['enabled'] ?? TRUE), $this->toRelativeHqPath($this->artifactPaths()['release_control'])],
        [$this->t('Parity health'), isset($parity['parity_ok']) ? ((bool) $parity['parity_ok'] ? 'PASS' : 'FAIL') : 'unknown', 'inbox/responses/langgraph-parity-latest.json'],
        [$this->t('Latest step count'), (string) count($step_results), 'inbox/responses/langgraph-ticks.jsonl'],
      ]
    );

    $build['controls'] = $this->tableDetails(
      'Management controls',
      ['Control', 'Enabled', 'Updated at', 'Updated by', 'Reason'],
      [
        [
          $this->t('Org automation'),
          $this->boolLabel($org_control['enabled'] ?? TRUE),
          (string) ($org_control['updated_at'] ?? '-'),
          (string) ($org_control['updated_by'] ?? '-'),
          (string) ($org_control['reason'] ?? '-'),
        ],
        [
          $this->t('Release-cycle automation'),
          $this->boolLabel($release_control['enabled'] ?? TRUE),
          (string) ($release_control['updated_at'] ?? '-'),
          (string) ($release_control['updated_by'] ?? '-'),
          (string) ($release_control['reason'] ?? '-'),
        ],
      ]
    );

    $build['decision_flow'] = $this->tableDetails(
      'Recommended operator flow',
      ['Step', 'Intent'],
      [
        ['Overview', 'Confirm top-level health, control state, and parity posture.'],
        ['Session Health', 'Verify tick cadence, runtime freshness, and current execution errors.'],
        ['Parity Health', 'Confirm LangGraph parity is passing.'],
        ['Feature Flow', 'Review execution ownership and work-item movement.'],
        ['Release Control', 'Confirm release-cycle continuity and signoff posture.'],
        ['Release Evidence', 'Read the latest release notes and PM signoff evidence.'],
        ['Release Troubleshooting', 'Triage active inbox pressure, needs-info, and blocker-like work.'],
      ]
    );

    return $build;
  }

  /**
   * Session health page.
   */
  public function sessionHealth(): array {
    $latest_tick = $this->readLatestTick();
    $ticks = array_slice($this->readTicks(), -25);
    $rows = [];
    foreach (array_reverse($ticks) as $tick) {
      $rows[] = [
        (string) ($tick['ts'] ?? ''),
        $this->formatAgeFromTimestamp((string) ($tick['ts'] ?? '')),
        (string) ($tick['engine_mode'] ?? 'unknown'),
        (string) ($tick['provider'] ?? ''),
        (string) ($tick['agent_cap'] ?? ''),
        (string) $this->countTickErrors($tick),
      ];
    }

    $step_rows = [];
    $steps = is_array($latest_tick['step_results'] ?? NULL) ? $latest_tick['step_results'] : [];
    foreach ($steps as $step => $detail) {
      if (!is_array($detail)) {
        continue;
      }
      $status = isset($detail['error']) || !empty($detail['errors']) ? 'error' : (!empty($detail['skipped']) ? 'skipped' : 'ok');
      $step_rows[] = [
        (string) $step,
        $status,
        $this->detailSummary($detail, ['mode', 'rc', 'skipped', 'error']),
      ];
    }

    $build = $this->buildPage(
      'Session Health',
      'Tick cadence, runtime freshness, and current step-level diagnostics.'
    );
    $build['timeline'] = $this->tableDetails(
      'Recent tick timeline',
      ['Timestamp', 'Age', 'Engine mode', 'Provider', 'Agent cap', 'Error count'],
      $rows,
      'inbox/responses/langgraph-ticks.jsonl'
    );
    $build['latest_steps'] = $this->tableDetails(
      'Latest step diagnostics',
      ['Step', 'Status', 'Details'],
      $step_rows,
      'inbox/responses/langgraph-ticks.jsonl'
    );

    return $build;
  }

  /**
   * Parity page.
   */
  public function parityHealth(): array {
    $parity = $this->readParity();
    $errors = is_array($parity['errors'] ?? NULL) ? $parity['errors'] : [];

    $build = $this->buildPage(
      'Parity Health',
      'Correctness checks for selected-agent parity and step-order parity.'
    );
    $build['parity'] = $this->tableDetails(
      'Current parity evidence',
      ['Field', 'Value'],
      [
        ['parity_ok', isset($parity['parity_ok']) ? ((bool) $parity['parity_ok'] ? 'PASS' : 'FAIL') : 'unknown'],
        ['selected_agents.match', isset($parity['selected_agents']['match']) ? ((bool) $parity['selected_agents']['match'] ? 'yes' : 'no') : 'unknown'],
        ['steps.match', isset($parity['steps']['match']) ? ((bool) $parity['steps']['match'] ? 'yes' : 'no') : 'unknown'],
        ['generated_at', (string) ($parity['generated_at'] ?? 'unknown')],
        ['error_count', (string) count($errors)],
      ],
      'inbox/responses/langgraph-parity-latest.json'
    );
    $build['parity_errors'] = $this->tableDetails(
      'Parity mismatches',
      ['Issue'],
      array_map(static fn(string $error): array => [$error], array_map('strval', $errors)),
      'inbox/responses/langgraph-parity-latest.json'
    );

    return $build;
  }

  /**
   * Feature flow page.
   */
  public function featureProgress(): array {
    $feature_progress = $this->readFeatureProgress();
    $rows = [];
    foreach ($feature_progress['rows'] as $row) {
      $rows[] = [
        $row['Work item'] ?? '',
        $row['Website'] ?? '',
        $row['Module'] ?? '',
        $row['Status'] ?? '',
        $row['Priority'] ?? '',
        $row['PM'] ?? '',
        $row['Dev'] ?? '',
        $row['QA'] ?? '',
      ];
    }

    $summary = [];
    foreach ($feature_progress['rows'] as $row) {
      $status = trim((string) ($row['Status'] ?? 'unknown'));
      $summary[$status] = ($summary[$status] ?? 0) + 1;
    }
    ksort($summary);

    $build = $this->buildPage(
      'Feature Flow',
      'Execution-plane snapshot sourced from the HQ feature progress dashboard.'
    );
    $build['summary'] = $this->tableDetails(
      'Status summary',
      ['Status', 'Count'],
      array_map(
        static fn(string $status, int $count): array => [$status !== '' ? $status : 'unknown', (string) $count],
        array_keys($summary),
        $summary
      ),
      $this->toRelativeHqPath($this->artifactPaths()['feature_progress'])
    );
    $build['generated'] = [
      '#markup' => '<p><strong>' . $this->t('Generated') . ':</strong> ' . $this->t('@generated', [
        '@generated' => $feature_progress['generated_at'] !== '' ? $feature_progress['generated_at'] : 'unknown',
      ]) . '</p>',
    ];
    $build['table'] = $this->tableDetails(
      'Feature progress',
      ['Work item', 'Website', 'Module', 'Status', 'Priority', 'PM', 'Dev', 'QA'],
      $rows,
      $this->toRelativeHqPath($this->artifactPaths()['feature_progress'])
    );

    return $build;
  }

  /**
   * Release control page.
   */
  public function releaseStatus(): array {
    $release_control = $this->readReleaseControl();
    $release_rows = $this->readReleaseCycleRows();
    $evidence_rows = $this->buildReleaseCoverageRows($release_rows);

    $build = $this->buildPage(
      'Release Control',
      'Release-cycle posture, continuity state, and evidence coverage for active releases.'
    );
    $build['control'] = $this->tableDetails(
      'Release-cycle control',
      ['Field', 'Value'],
      [
        ['enabled', $this->boolLabel($release_control['enabled'] ?? TRUE)],
        ['updated_at', (string) ($release_control['updated_at'] ?? '-')],
        ['updated_by', (string) ($release_control['updated_by'] ?? '-')],
        ['reason', (string) ($release_control['reason'] ?? '-')],
      ],
      $this->toRelativeHqPath($this->artifactPaths()['release_control'])
    );
    $build['release_cycles'] = $this->tableDetails(
      'Active release-cycle state',
      ['Team', 'Current release', 'Next release', 'Source'],
      $release_rows
    );
    $build['coverage'] = $this->tableDetails(
      'Active release evidence coverage',
      ['Release id', 'Release notes', 'PM signoffs'],
      $evidence_rows
    );

    return $build;
  }

  /**
   * Release evidence page.
   */
  public function releaseNotes(): array {
    $notes = $this->listReleaseNotes();
    $signoffs = $this->listReleaseSignoffs();

    $note_rows = [];
    foreach (array_slice($notes, 0, 10) as $note) {
      $note_rows[] = [
        $note['release_id'],
        $note['seat'],
        $note['site'],
        $note['state'],
        $note['updated_at'],
        $note['path'],
      ];
    }

    $signoff_rows = [];
    foreach (array_slice($signoffs, 0, 10) as $signoff) {
      $signoff_rows[] = [
        $signoff['release_id'],
        $signoff['site'],
        $signoff['seat'],
        $signoff['signed_off_at'],
        $signoff['path'],
      ];
    }

    $build = $this->buildPage(
      'Release Evidence',
      'Latest release-note narratives and PM signoff artifacts.'
    );
    $build['notes'] = $this->tableDetails(
      'Recent release notes',
      ['Release id', 'Seat', 'Site', 'State', 'Updated at', 'Path'],
      $note_rows
    );
    $build['signoffs'] = $this->tableDetails(
      'Recent PM signoffs',
      ['Release id', 'Site', 'PM seat', 'Signed off at', 'Path'],
      $signoff_rows
    );

    if ($notes !== []) {
      $build['latest_note'] = $this->textDetails(
        'Latest release-note excerpt',
        $notes[0]['excerpt'],
        $notes[0]['path']
      );
    }

    return $build;
  }

  /**
   * Release troubleshooting page.
   */
  public function releaseTroubleshooting(): array {
    $items = $this->listActiveInboxItems();
    $rows = [];
    foreach ($items as $item) {
      $rows[] = [
        $item['seat'],
        $item['item_id'],
        $item['triage'],
        $item['status'],
        $item['roi'],
        $item['age'],
        $item['summary'],
        $item['path'],
      ];
    }

    $build = $this->buildPage(
      'Release Troubleshooting',
      'Seat-level triage of live inbox pressure, blocker-like work, and escalation-oriented items.'
    );
    $build['active_work'] = $this->tableDetails(
      'Active inbox items',
      ['Seat', 'Item', 'Triage', 'Status', 'ROI', 'Age', 'Summary', 'Path'],
      $rows
    );

    return $build;
  }

  /**
   * Build a standard control-plane page.
   */
  private function buildPage(string $title, string $description): array {
    return [
      '#type' => 'container',
      '#cache' => ['max-age' => 0],
      'title' => [
        '#markup' => '<h2>' . $this->t($title) . '</h2>',
      ],
      'description' => [
        '#markup' => '<p>' . $this->t($description) . '</p>',
      ],
    ];
  }

  /**
   * Render a table inside a details element.
   */
  private function tableDetails(string $title, array $header, array $rows, ?string $source = NULL): array {
    $build = [
      '#type' => 'details',
      '#title' => $this->t($title),
      '#open' => TRUE,
      'table' => [
        '#type' => 'table',
        '#header' => $header,
        '#rows' => $rows,
        '#empty' => $this->t('No data available.'),
      ],
    ];

    if ($source !== NULL) {
      $build['source'] = [
        '#markup' => '<p><strong>' . $this->t('Source') . ':</strong> ' . $this->t('@source', ['@source' => $source]) . '</p>',
      ];
    }

    return $build;
  }

  /**
   * Render preformatted text inside a details element.
   */
  private function textDetails(string $title, string $text, ?string $source = NULL): array {
    $build = [
      '#type' => 'details',
      '#title' => $this->t($title),
      '#open' => FALSE,
      'preview' => [
        '#type' => 'html_tag',
        '#tag' => 'pre',
        '#value' => $text !== '' ? $text : $this->t('No text available.'),
      ],
    ];

    if ($source !== NULL) {
      $build['source'] = [
        '#markup' => '<p><strong>' . $this->t('Source') . ':</strong> ' . $this->t('@source', ['@source' => $source]) . '</p>',
      ];
    }

    return $build;
  }

  /**
   * Human-readable boolean label.
   */
  private function boolLabel(mixed $value): string {
    return isset($value) ? ((bool) $value ? 'yes' : 'no') : 'unknown';
  }

  /**
   * Build a compact detail summary from a structured array.
   */
  private function detailSummary(array $detail, array $keys): string {
    $parts = [];
    foreach ($keys as $key) {
      if (isset($detail[$key])) {
        $parts[] = $key . '=' . (string) $detail[$key];
      }
    }
    return $parts !== [] ? implode('; ', $parts) : 'ok';
  }

  /**
   * Resolve the forseti root.
   */
  private function forsetiRoot(): string {
    $configured = trim((string) getenv('FORSETI_ROOT'));
    if ($configured !== '') {
      return rtrim($configured, '/');
    }
    return self::DEFAULT_FORSETI_ROOT;
  }

  /**
   * Resolve the HQ runtime root.
   */
  private function hqRoot(): string {
    $configured = trim((string) getenv('COPILOT_HQ_ROOT'));
    if ($configured !== '' && is_dir($configured)) {
      return rtrim($configured, '/');
    }

    foreach (self::DEFAULT_HQ_ROOTS as $candidate) {
      if (is_dir($candidate)) {
        return $candidate;
      }
    }

    return rtrim($this->forsetiRoot() . '/copilot-hq', '/');
  }

  /**
   * Artifact locations used by the control plane.
   */
  private function artifactPaths(): array {
    $hq_root = $this->hqRoot();
    $forseti_root = $this->forsetiRoot();
    return [
      'ticks' => $hq_root . '/inbox/responses/langgraph-ticks.jsonl',
      'parity' => $hq_root . '/inbox/responses/langgraph-parity-latest.json',
      'release_cycle_dir' => $hq_root . '/tmp/release-cycle-active',
      'release_control' => $this->firstReadablePath([
        (string) getenv('RELEASE_CYCLE_CONTROL_FILE'),
        '/var/tmp/copilot-sessions-hq/release-cycle-control.json',
        $hq_root . '/tmp/release-cycle-control.json',
      ]),
      'org_control' => $this->firstReadablePath([
        (string) getenv('ORG_CONTROL_FILE'),
        '/var/tmp/copilot-sessions-hq/org-control.json',
        $hq_root . '/tmp/org-control.json',
      ]),
      'feature_progress' => $this->firstReadablePath([
        $hq_root . '/dashboards/FEATURE_PROGRESS.md',
        $forseti_root . '/dashboards/FEATURE_PROGRESS.md',
      ]),
      'sessions_dir' => $hq_root . '/sessions',
    ];
  }

  /**
   * Return the first readable path from a list.
   */
  private function firstReadablePath(array $paths): string {
    foreach ($paths as $path) {
      $path = trim((string) $path);
      if ($path !== '' && (is_readable($path) || is_dir($path))) {
        return $path;
      }
    }
    return trim((string) ($paths[0] ?? ''));
  }

  /**
   * Read a JSON file.
   */
  private function readJsonFile(string $path): array {
    if ($path === '' || !is_readable($path)) {
      return [];
    }
    $raw = @file_get_contents($path);
    if ($raw === FALSE) {
      return [];
    }
    $decoded = json_decode((string) $raw, TRUE);
    return is_array($decoded) ? $decoded : [];
  }

  /**
   * Read a text file.
   */
  private function readTextFile(string $path): string {
    if ($path === '' || !is_readable($path)) {
      return '';
    }
    $raw = @file_get_contents($path);
    return $raw === FALSE ? '' : trim((string) $raw);
  }

  /**
   * Read full JSONL ticks array.
   */
  private function readTicks(): array {
    $path = $this->artifactPaths()['ticks'];
    if (!is_readable($path)) {
      return [];
    }

    $lines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    $rows = [];
    foreach ($lines as $line) {
      $decoded = json_decode(trim((string) $line), TRUE);
      if (is_array($decoded)) {
        $rows[] = $decoded;
      }
    }
    return $rows;
  }

  /**
   * Read latest tick object.
   */
  private function readLatestTick(): array {
    $ticks = $this->readTicks();
    if ($ticks === []) {
      return [];
    }
    $latest = end($ticks);
    return is_array($latest) ? $latest : [];
  }

  /**
   * Read parity payload.
   */
  private function readParity(): array {
    return $this->readJsonFile($this->artifactPaths()['parity']);
  }

  /**
   * Read org control payload.
   */
  private function readOrgControl(): array {
    return $this->readJsonFile($this->artifactPaths()['org_control']);
  }

  /**
   * Read release-cycle control payload.
   */
  private function readReleaseControl(): array {
    return $this->readJsonFile($this->artifactPaths()['release_control']);
  }

  /**
   * Parse the feature progress markdown dashboard.
   */
  private function readFeatureProgress(): array {
    $path = $this->artifactPaths()['feature_progress'];
    $raw = $this->readTextFile($path);
    if ($raw === '') {
      return ['generated_at' => '', 'rows' => []];
    }

    $generated_at = '';
    $lines = preg_split('/\R/', $raw) ?: [];
    foreach ($lines as $line) {
      if (preg_match('/^Generated:\s*(.+)$/', trim((string) $line), $matches)) {
        $generated_at = trim((string) $matches[1]);
        break;
      }
    }

    $headers = [];
    $rows = [];
    for ($i = 0; $i < count($lines); $i++) {
      $line = trim((string) $lines[$i]);
      $next = trim((string) ($lines[$i + 1] ?? ''));
      if (!str_starts_with($line, '|') || !str_starts_with($next, '|')) {
        continue;
      }
      if (!preg_match('/^\|\s*-+/', $next)) {
        continue;
      }

      $headers = $this->parseMarkdownTableRow($line);
      for ($j = $i + 2; $j < count($lines); $j++) {
        $row_line = trim((string) $lines[$j]);
        if ($row_line === '' || !str_starts_with($row_line, '|')) {
          break;
        }
        $values = $this->parseMarkdownTableRow($row_line);
        if (count($values) !== count($headers)) {
          continue;
        }
        $rows[] = array_combine($headers, $values) ?: [];
      }
      break;
    }

    return [
      'generated_at' => $generated_at,
      'rows' => $rows,
    ];
  }

  /**
   * Parse a markdown table row.
   */
  private function parseMarkdownTableRow(string $line): array {
    $parts = explode('|', trim($line, '|'));
    return array_map(static fn(string $part): string => trim($part), $parts);
  }

  /**
   * Build release-cycle table rows.
   */
  private function readReleaseCycleRows(): array {
    $dir = $this->artifactPaths()['release_cycle_dir'];
    if (!is_dir($dir)) {
      return [];
    }

    $files = glob($dir . '/*.release_id') ?: [];
    sort($files);
    $rows = [];
    foreach ($files as $file) {
      $team = basename((string) $file, '.release_id');
      $current = trim((string) @file_get_contents((string) $file));
      $next_file = $dir . '/' . $team . '.next_release_id';
      $next = is_readable($next_file) ? trim((string) @file_get_contents($next_file)) : '';
      $rows[] = [
        (string) $team,
        $current !== '' ? $current : '-',
        $next !== '' ? $next : '-',
        $this->sourcePath('tmp/release-cycle-active/' . $team . '.release_id'),
      ];
    }

    return $rows;
  }

  /**
   * Build active release-evidence coverage rows.
   */
  private function buildReleaseCoverageRows(array $release_rows): array {
    $notes = $this->listReleaseNotes();
    $signoffs = $this->listReleaseSignoffs();
    $note_ids = [];
    $signoff_counts = [];

    foreach ($notes as $note) {
      $note_ids[$note['release_id']] = TRUE;
    }
    foreach ($signoffs as $signoff) {
      $signoff_counts[$signoff['release_id']] = ($signoff_counts[$signoff['release_id']] ?? 0) + 1;
    }

    $rows = [];
    foreach ($release_rows as $row) {
      $release_id = (string) ($row[1] ?? '');
      if ($release_id === '' || $release_id === '-') {
        continue;
      }
      $rows[] = [
        $release_id,
        isset($note_ids[$release_id]) ? 'present' : 'missing',
        (string) ($signoff_counts[$release_id] ?? 0),
      ];
    }

    return $rows;
  }

  /**
   * List release note artifacts.
   */
  private function listReleaseNotes(): array {
    $sessions_dir = $this->artifactPaths()['sessions_dir'];
    if (!is_dir($sessions_dir)) {
      return [];
    }

    $files = glob($sessions_dir . '/*/artifacts/release-candidates/*/05-release-notes.md') ?: [];
    usort($files, static fn(string $a, string $b): int => filemtime($b) <=> filemtime($a));

    $rows = [];
    foreach ($files as $file) {
      $text = $this->readTextFile($file);
      $rows[] = [
        'release_id' => $this->extractFrontMatterValue($text, ['**Release id**', 'Release id']) ?: basename(dirname($file)),
        'seat' => basename(dirname(dirname(dirname($file)))),
        'site' => $this->extractFrontMatterValue($text, ['**Site**', 'Site']),
        'state' => $this->extractFrontMatterValue($text, ['**State**', 'State']),
        'updated_at' => gmdate('c', (int) filemtime($file)),
        'path' => $this->toRelativeHqPath($file),
        'excerpt' => implode("\n", array_slice(preg_split('/\R/', $text) ?: [], 0, 40)),
      ];
    }
    return $rows;
  }

  /**
   * List PM signoff artifacts.
   */
  private function listReleaseSignoffs(): array {
    $sessions_dir = $this->artifactPaths()['sessions_dir'];
    if (!is_dir($sessions_dir)) {
      return [];
    }

    $files = glob($sessions_dir . '/*/artifacts/release-signoffs/*.md') ?: [];
    usort($files, static fn(string $a, string $b): int => filemtime($b) <=> filemtime($a));

    $rows = [];
    foreach ($files as $file) {
      $text = $this->readTextFile($file);
      $rows[] = [
        'release_id' => $this->extractFrontMatterValue($text, ['Release id']),
        'site' => $this->extractFrontMatterValue($text, ['Site']),
        'seat' => $this->extractFrontMatterValue($text, ['PM seat']) ?: basename(dirname(dirname(dirname($file)))),
        'signed_off_at' => $this->extractFrontMatterValue($text, ['Signed off at']) ?: gmdate('c', (int) filemtime($file)),
        'path' => $this->toRelativeHqPath($file),
      ];
    }
    return $rows;
  }

  /**
   * List active inbox items for release troubleshooting.
   */
  private function listActiveInboxItems(): array {
    $sessions_dir = $this->artifactPaths()['sessions_dir'];
    if (!is_dir($sessions_dir)) {
      return [];
    }

    $items = [];
    foreach (glob($sessions_dir . '/*/inbox/*') ?: [] as $path) {
      if (!is_dir($path) || basename($path) === '_archived') {
        continue;
      }

      $seat = basename(dirname(dirname($path)));
      $item_id = basename($path);
      $roi = $this->readTextFile($path . '/roi.txt');
      $summary = $this->readItemSummary($path);
      $last_progress = is_file($path . '/.last-progress-at') ? filemtime($path . '/.last-progress-at') : filemtime($path);
      $status = is_file($path . '/.inwork') ? 'in_progress' : 'queued';
      $triage = $this->inferTriageLabel($item_id, $summary);

      $items[] = [
        'seat' => $seat,
        'item_id' => $item_id,
        'triage' => $triage,
        'status' => $status,
        'roi' => $roi !== '' ? $roi : '-',
        'age' => $this->formatAgeFromEpoch((int) $last_progress),
        'summary' => $summary,
        'path' => $this->toRelativeHqPath($path),
        'sort_epoch' => (int) $last_progress,
      ];
    }

    usort($items, static function (array $a, array $b): int {
      if ($a['triage'] !== $b['triage']) {
        return strcmp((string) $a['triage'], (string) $b['triage']);
      }
      return ($a['sort_epoch'] ?? 0) <=> ($b['sort_epoch'] ?? 0);
    });

    return array_map(static function (array $item): array {
      unset($item['sort_epoch']);
      return $item;
    }, $items);
  }

  /**
   * Read a concise item summary from command/readme files.
   */
  private function readItemSummary(string $item_dir): string {
    foreach (['command.md', 'README.md', '00-problem-statement.md'] as $candidate) {
      $path = $item_dir . '/' . $candidate;
      if (!is_readable($path)) {
        continue;
      }
      $lines = preg_split('/\R/', $this->readTextFile($path)) ?: [];
      foreach ($lines as $line) {
        $line = trim((string) $line);
        if ($line === '' || str_starts_with($line, '**From:**') || str_starts_with($line, '**To:**')) {
          continue;
        }
        return ltrim($line, "# \t");
      }
    }
    return '(no summary found)';
  }

  /**
   * Infer a triage label from the item id and summary.
   */
  private function inferTriageLabel(string $item_id, string $summary): string {
    $haystack = strtolower($item_id . ' ' . $summary);
    if (str_contains($haystack, 'needs-') || str_contains($haystack, 'needs info')) {
      return 'needs-info';
    }
    if (str_contains($haystack, 'blocked') || str_contains($haystack, 'stagnation') || str_contains($haystack, 'awaiting')) {
      return 'blocked';
    }
    return 'active';
  }

  /**
   * Extract a markdown bullet front-matter value.
   */
  private function extractFrontMatterValue(string $text, array $labels): string {
    foreach ($labels as $label) {
      $quoted = preg_quote($label, '/');
      if (preg_match('/^-\\s*' . $quoted . '\s*:\s*(.+)$/mi', $text, $matches)) {
        return trim((string) $matches[1], " \t`");
      }
    }
    return '';
  }

  /**
   * Count errors on a tick row.
   */
  private function countTickErrors(array $tick): int {
    $count = count((array) ($tick['errors'] ?? []));
    $steps = is_array($tick['step_results'] ?? NULL) ? $tick['step_results'] : [];
    foreach ($steps as $step) {
      if (!is_array($step)) {
        continue;
      }
      if (($step['status'] ?? '') === 'error' || !empty($step['errors']) || isset($step['error'])) {
        $count++;
      }
    }
    return $count;
  }

  /**
   * Human-readable age from timestamp string.
   */
  private function formatAgeFromTimestamp(string $ts): string {
    if ($ts === '') {
      return 'unknown';
    }
    $value = strtotime($ts);
    if ($value === FALSE) {
      return 'unknown';
    }
    return $this->formatAgeFromEpoch((int) $value);
  }

  /**
   * Human-readable age from epoch.
   */
  private function formatAgeFromEpoch(int $epoch): string {
    return (string) max(0, time() - $epoch) . 's';
  }

  /**
   * Convert absolute paths to HQ-relative labels when possible.
   */
  private function toRelativeHqPath(string $path): string {
    $root = rtrim($this->hqRoot(), '/') . '/';
    if (str_starts_with($path, $root)) {
      return substr($path, strlen($root));
    }

    $forseti_root = rtrim($this->forsetiRoot(), '/') . '/';
    if (str_starts_with($path, $forseti_root)) {
      return substr($path, strlen($forseti_root));
    }

    return $path;
  }

  /**
   * Return a readable source path label.
   */
  private function sourcePath(string $relative): string {
    return $relative;
  }

}
