<?php

namespace Drupal\copilot_agent_tracker\Form;

use Drupal\Component\Yaml\Yaml;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\Process\Process;

final class LlmManagementForm extends FormBase {

  const JOB_ROOT_DIR = '/tmp/copilot-agent-tracker-llm-jobs';
  const HQ_ROOT_DEFAULT = '/home/ubuntu/forseti.life';
  const QUICK_MODEL_ID = 'phi-3-mini';

  public function getFormId(): string {
    return 'copilot_agent_tracker_llm_management_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state): array {
    $model_catalog = $this->getModelCatalog();
    $ready_models = array_values(array_filter($model_catalog, static fn (array $model): bool => !empty($model['downloaded'])));

    $options = [];
    foreach ($model_catalog as $model_id => $model) {
      $label = (string) ($model['name'] ?? $model_id);
      $status = !empty($model['downloaded']) ? 'ready' : 'not downloaded';
      $options[$model_id] = $label . ' (' . $status . ')';
    }

    if ($options === []) {
      $this->messenger()->addWarning($this->t('No local LLM models were found in llm/model-manifest.yaml.'));
    }
    elseif ($ready_models === []) {
      $this->messenger()->addWarning($this->t('No local GGUF model files were detected under @dir. Run ./llm/download-models.sh before using this page.', [
        '@dir' => $this->getModelsDir(),
      ]));
    }

    $default_model = (string) $form_state->getValue('model');
    if ($default_model === '' && isset($options[self::QUICK_MODEL_ID])) {
      $default_model = self::QUICK_MODEL_ID;
    }
    if ($default_model === '' || !isset($options[$default_model])) {
      $default_model = (string) array_key_first($options);
    }

    $python_bin = $this->getPythonBin();
    $runner_script = $this->getRunnerScript();
    $models_dir = $this->getModelsDir();

    // Graceful "not configured" check: show an info notice if the runtime is
    // not set up, but still render the page (do not throw or log at Error).
    $env_ready = is_file($python_bin) && is_file($runner_script);
    if (!$env_ready) {
      $missing = [];
      if (!is_file($python_bin)) {
        $missing[] = $this->t('Python runtime not found: @p', ['@p' => $python_bin]);
      }
      if (!is_file($runner_script)) {
        $missing[] = $this->t('LLM runner script not found: @s', ['@s' => $runner_script]);
      }
      foreach ($missing as $m) {
        $this->messenger()->addWarning($m);
      }
    }

    $form['summary'] = [
      '#type' => 'details',
      '#title' => $this->t('Environment and runtime'),
      '#open' => TRUE,
      'items' => [
        '#theme' => 'item_list',
        '#items' => [
          $this->t('Development environment page for local LLM testing.'),
          $this->t('Python runtime: @bin', ['@bin' => $python_bin]),
          $this->t('LLM runner script: @script', ['@script' => $runner_script]),
          $this->t('Local model directory: @dir', ['@dir' => $models_dir]),
          $this->t('Models declared in manifest: @count', ['@count' => (string) count($model_catalog)]),
          $this->t('Models ready on disk: @count', ['@count' => (string) count($ready_models)]),
        ],
      ],
    ];

    $form['model'] = [
      '#type' => 'select',
      '#title' => $this->t('Local model'),
      '#options' => $options,
      '#default_value' => $default_model,
      '#required' => TRUE,
      '#description' => $this->t('Models are loaded from llm/model-manifest.yaml and resolve to local GGUF files under llm/models/.'),
    ];

    $form['prompt'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Prompt'),
      '#rows' => 6,
      '#required' => TRUE,
      '#default_value' => (string) $form_state->getValue('prompt', ''),
      '#description' => $this->t('Enter a simple test string to generate model output.'),
    ];

    $form['max_length'] = [
      '#type' => 'number',
      '#title' => $this->t('Max length'),
      '#default_value' => (int) $form_state->getValue('max_length', 24),
      '#min' => 16,
      '#max' => 512,
      '#required' => TRUE,
    ];

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Run test (background)'),
      '#button_type' => 'primary',
    ];
    $form['actions']['quick_test'] = [
      '#type' => 'submit',
      '#value' => $this->t('Hello world quick test'),
      '#submit' => ['::runHelloWorldQuickTest'],
      '#limit_validation_errors' => [],
    ];
    $form['actions']['refresh'] = [
      '#type' => 'submit',
      '#value' => $this->t('Refresh status'),
      '#submit' => ['::refreshStatus'],
      '#limit_validation_errors' => [],
    ];

    $jobs = $this->getRecentJobs();
    $job_rows = [];
    foreach ($jobs as $job) {
      $job_rows[] = [
        (string) ($job['job_id'] ?? ''),
        (string) ($job['status'] ?? ''),
        (string) ($job['model'] ?? ''),
        (string) ($job['prompt_preview'] ?? ''),
        (string) ($job['max_length'] ?? ''),
        (string) ($job['runtime'] ?? ''),
        (string) ($job['pid'] ?? ''),
        (string) ($job['created_at'] ?? ''),
      ];
    }

    $form['jobs'] = [
      '#type' => 'details',
      '#title' => $this->t('Recent LLM test jobs'),
      '#open' => TRUE,
      '#attributes' => [
        'id' => 'recent-llm-test-jobs',
      ],
      'table' => [
        '#type' => 'table',
        '#header' => [
          $this->t('Job ID'),
          $this->t('Status'),
          $this->t('Model'),
          $this->t('Prompt'),
          $this->t('Max length'),
          $this->t('Runtime'),
          $this->t('PID'),
          $this->t('Created'),
        ],
        '#rows' => $job_rows,
        '#empty' => $this->t('No LLM jobs yet. Click Run test to queue one.'),
      ],
    ];

    $result = $form_state->get('llm_test_result');
    if (is_array($result)) {
      $form['result'] = [
        '#type' => 'details',
        '#title' => $this->t('Model output'),
        '#open' => TRUE,
      ];

      $form['result']['meta'] = [
        '#markup' => '<p><strong>Model:</strong> ' . $this->t('@m', ['@m' => (string) ($result['model'] ?? '')]) . '<br><strong>Runtime:</strong> ' . $this->t('@s sec', ['@s' => (string) ($result['runtime_seconds'] ?? '')]) . '</p>',
      ];

      $form['result']['output'] = [
        '#type' => 'textarea',
        '#title' => $this->t('Generated output'),
        '#default_value' => (string) ($result['output'] ?? ''),
        '#rows' => 14,
        '#attributes' => ['readonly' => 'readonly'],
      ];
    }

    $form['#cache'] = ['max-age' => 0];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state): void {
    $prompt = trim((string) $form_state->getValue('prompt'));
    if ($prompt === '') {
      $form_state->setErrorByName('prompt', $this->t('Prompt cannot be empty.'));
    }

    $max_length = (int) $form_state->getValue('max_length');
    if ($max_length < 16 || $max_length > 512) {
      $form_state->setErrorByName('max_length', $this->t('Max length must be between 16 and 512.'));
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $model = trim((string) $form_state->getValue('model'));
    $prompt = trim((string) $form_state->getValue('prompt'));
    $max_length = (int) $form_state->getValue('max_length');

    $python_bin = $this->getPythonBin();
    $runner_script = $this->getRunnerScript();
    $model_path = $this->getLocalModelPath($model);

    if (!is_file($python_bin)) {
      $this->messenger()->addError($this->t('Python runtime not found: @path', ['@path' => $python_bin]));
      $this->getLogger('copilot_agent_tracker')->error('LLM management test failed: Python runtime not found at @path', [
        '@path' => $python_bin,
      ]);
      return;
    }
    if (!is_file($runner_script)) {
      $this->messenger()->addError($this->t('LLM runner script not found: @path', ['@path' => $runner_script]));
      $this->getLogger('copilot_agent_tracker')->error('LLM management test failed: runner script not found at @path', [
        '@path' => $runner_script,
      ]);
      return;
    }
    if ($model_path === '' || !is_file($model_path)) {
      $this->messenger()->addError($this->t('Local model file not found for model @id. Download it first with ./llm/download-models.sh @id.', [
        '@id' => $model,
      ]));
      $this->getLogger('copilot_agent_tracker')->error('LLM management test failed: local model file not found for @model at @path', [
        '@model' => $model,
        '@path' => $model_path,
      ]);
      return;
    }

    $job_root = $this->getJobRootDir();
    if (!is_dir($job_root)) {
      @mkdir($job_root, 0775, TRUE);
    }
    if (!is_dir($job_root) || !is_writable($job_root)) {
      $this->messenger()->addError($this->t('LLM job directory is not writable: @dir', ['@dir' => $job_root]));
      return;
    }

    $job_id = date('Ymd-His') . '-' . bin2hex(random_bytes(3));
    $job_dir = $job_root . '/' . $job_id;
    @mkdir($job_dir, 0775, TRUE);
    if (!is_dir($job_dir) || !is_writable($job_dir)) {
      $this->messenger()->addError($this->t('Failed to create LLM job directory: @dir', ['@dir' => $job_dir]));
      return;
    }

    $prompt_file = $job_dir . '/prompt.txt';
    $stdout_file = $job_dir . '/stdout.log';
    $stderr_file = $job_dir . '/stderr.log';
    $state_file = $job_dir . '/state.txt';
    $started_file = $job_dir . '/started_at.txt';
    $finished_file = $job_dir . '/finished_at.txt';
    $exit_file = $job_dir . '/exit_code.txt';
    $script_file = $job_dir . '/run.sh';
    $meta_file = $job_dir . '/meta.json';

    file_put_contents($prompt_file, $prompt);
    file_put_contents($state_file, "queued\n");

    $logger_context = [
      '@user' => (string) $this->currentUser()->id(),
      '@username' => (string) $this->currentUser()->getAccountName(),
      '@job_id' => $job_id,
      '@model' => $model,
      '@prompt' => $prompt,
      '@max_length' => (string) $max_length,
      '@python_bin' => $python_bin,
      '@runner_script' => $runner_script,
      '@model_path' => $model_path,
      '@timeout_sec' => '0',
    ];

    $script = "#!/usr/bin/env bash\n"
      . "set -u\n"
      . "echo running > " . escapeshellarg($state_file) . "\n"
      . "date +%s > " . escapeshellarg($started_file) . "\n"
      . "PROMPT=\$(cat " . escapeshellarg($prompt_file) . ")\n"
      . escapeshellarg($python_bin) . " " . escapeshellarg($runner_script)
      . " --model " . escapeshellarg($model_path)
      . " --prompt \"\$PROMPT\""
      . " > " . escapeshellarg($stdout_file)
      . " 2> " . escapeshellarg($stderr_file)
      . "\n"
      . "rc=\$?\n"
      . "date +%s > " . escapeshellarg($finished_file) . "\n"
      . "echo \$rc > " . escapeshellarg($exit_file) . "\n"
      . "if [ \"\$rc\" -eq 0 ]; then echo success > " . escapeshellarg($state_file) . "; else echo failed > " . escapeshellarg($state_file) . "; fi\n"
      . "exit 0\n";

    file_put_contents($script_file, $script);
    @chmod($script_file, 0755);

    try {
      // Use non-login shell (-c not -lc) to avoid heavy .bashrc/.bash_profile
      // overhead which can push launcher past the 10s timeout.
      $launcher = new Process(['bash', '-c', 'nohup ' . escapeshellarg($script_file) . ' >/dev/null 2>&1 & echo $!']);
      $launcher->setTimeout(10);
      $launcher->run();

      if (!$launcher->isSuccessful()) {
        $error = trim((string) $launcher->getErrorOutput());
        if ($error === '') {
          $error = trim((string) $launcher->getOutput());
        }
        $this->messenger()->addError($this->t('Failed to queue LLM job: @msg', ['@msg' => mb_substr($error, 0, 400)]));
        $this->getLogger('copilot_agent_tracker')->error('LLM_CALL_QUEUE_FAILED user_id=@user username=@username model=@model prompt=@prompt error=@error', $logger_context + [
          '@error' => mb_substr($error, 0, 2000),
        ]);
        return;
      }

      $pid = trim((string) $launcher->getOutput());
      $meta = [
        'job_id' => $job_id,
        'created_at' => (int) time(),
        'user_id' => (string) $this->currentUser()->id(),
        'username' => (string) $this->currentUser()->getAccountName(),
        'model' => $model,
        'prompt' => $prompt,
        'max_length' => $max_length,
        'python_bin' => $python_bin,
        'runner_script' => $runner_script,
        'model_path' => $model_path,
        'pid' => $pid,
      ];
      file_put_contents($meta_file, json_encode($meta, JSON_PRETTY_PRINT));

      $this->getLogger('copilot_agent_tracker')->notice(
        'LLM_CALL_QUEUED job_id=@job_id user_id=@user username=@username model=@model max_length=@max_length prompt=@prompt pid=@pid python=@python_bin runner=@runner_script model_path=@model_path',
        $logger_context + [
          '@pid' => $pid,
        ]
      );

      $this->messenger()->addStatus($this->t('LLM job queued (ID: @id). Refresh status to see progress/output.', ['@id' => $job_id]));
      $form_state->setRedirect('<current>');
    }
    catch (\Throwable $e) {
      $this->messenger()->addError($this->t('LLM test failed: @msg', ['@msg' => mb_substr($e->getMessage(), 0, 400)]));
      $this->getLogger('copilot_agent_tracker')->error(
        'LLM_CALL_EXCEPTION user_id=@user username=@username model=@model max_length=@max_length prompt=@prompt error=@error',
        $logger_context + [
          '@error' => mb_substr($e->getMessage(), 0, 2000),
        ]
      );
    }
  }

  public function refreshStatus(array &$form, FormStateInterface $form_state): void {
    $this->messenger()->addStatus($this->t('LLM job status refreshed.'));
    $form_state->setRedirect('<current>', [], ['fragment' => 'recent-llm-test-jobs']);
  }

  public function runHelloWorldQuickTest(array &$form, FormStateInterface $form_state): void {
    $model = self::QUICK_MODEL_ID;
    $available = $form['model']['#options'] ?? [];
    if (!isset($available[$model])) {
      $model = trim((string) ($form_state->getValue('model') ?: ($form['model']['#default_value'] ?? '')));
    }
    if ($model === '') {
      $model = self::QUICK_MODEL_ID;
    }

    $form_state->setValue('model', $model);
    $form_state->setValue('prompt', 'Reply with one complete sentence confirming this quick local LLM test is working.');
    $form_state->setValue('max_length', 24);

    $this->submitForm($form, $form_state);
  }

  private function getModelCatalog(): array {
    $manifest_path = $this->getHqRoot() . '/llm/model-manifest.yaml';
    if (!is_readable($manifest_path)) {
      return [];
    }

    try {
      $decoded = Yaml::decode((string) file_get_contents($manifest_path));
    }
    catch (\Throwable) {
      return [];
    }

    if (!is_array($decoded) || !is_array($decoded['models'] ?? NULL)) {
      return [];
    }

    $models_dir = $this->getModelsDir();
    $catalog = [];
    foreach ($decoded['models'] as $entry) {
      if (!is_array($entry)) {
        continue;
      }

      $model_id = trim((string) ($entry['id'] ?? ''));
      $name = trim((string) ($entry['name'] ?? $model_id));
      $filename = trim((string) ($entry['filename'] ?? ''));
      if ($model_id === '' || $filename === '') {
        continue;
      }

      $path = $models_dir . '/' . $filename;
      $catalog[$model_id] = [
        'id' => $model_id,
        'name' => $name,
        'filename' => $filename,
        'path' => $path,
        'downloaded' => is_file($path),
      ];
    }

    ksort($catalog, SORT_NATURAL | SORT_FLAG_CASE);
    return $catalog;
  }

  private function getLocalModelPath(string $model_id): string {
    $catalog = $this->getModelCatalog();
    return (string) ($catalog[$model_id]['path'] ?? '');
  }

  private function getPythonBin(): string {
    $override = trim((string) getenv('COPILOT_HQ_LLM_PYTHON_BIN'));
    if ($override !== '') {
      return $override;
    }

    $hq_root = $this->getHqRoot();
    $llm_python = $hq_root . '/llm/.venv/bin/python3';
    if (is_file($llm_python)) {
      return $llm_python;
    }

    $orchestrator_python = $hq_root . '/orchestrator/.venv/bin/python3';
    if (is_file($orchestrator_python)) {
      return $orchestrator_python;
    }

    return 'python3';
  }

  private function getRunnerScript(): string {
    $override = trim((string) getenv('COPILOT_HQ_LLM_RUNNER_SCRIPT'));
    if ($override !== '') {
      return $override;
    }

    return $this->getHqRoot() . '/llm/runner.py';
  }

  private function getModelsDir(): string {
    return $this->getHqRoot() . '/llm/models';
  }

  private function getHqRoot(): string {
    $override = trim((string) getenv('COPILOT_HQ_ROOT'));
    if ($override !== '') {
      return rtrim($override, '/');
    }

    return self::HQ_ROOT_DEFAULT;
  }

  private function extractGeneratedText(string $output): string {
    $pattern = '/Generated text:\s*-+\s*(.*?)\s*-+\s*$/s';
    if (preg_match($pattern, $output, $matches) === 1) {
      return trim((string) ($matches[1] ?? ''));
    }

    return '';
  }

  private function getJobRootDir(): string {
    $override = trim((string) getenv('COPILOT_HQ_LLM_JOB_DIR'));
    if ($override !== '') {
      return rtrim($override, '/');
    }
    return self::JOB_ROOT_DIR;
  }

  private function getRecentJobs(): array {
    $root = $this->getJobRootDir();
    if (!is_dir($root)) {
      return [];
    }

    $dirs = glob($root . '/*', GLOB_ONLYDIR) ?: [];
    rsort($dirs, SORT_NATURAL);
    $dirs = array_slice($dirs, 0, 10);

    $jobs = [];
    foreach ($dirs as $dir) {
      $job_id = basename($dir);
      $meta = [];
      $meta_file = $dir . '/meta.json';
      if (is_file($meta_file)) {
        try {
          $decoded = json_decode((string) file_get_contents($meta_file), TRUE);
          if (is_array($decoded)) {
            $meta = $decoded;
          }
        }
        catch (\Throwable) {
        }
      }

      $state = trim((string) (@file_get_contents($dir . '/state.txt') ?: 'queued'));
      $pid = trim((string) ($meta['pid'] ?? ''));
      if ($state === 'running' && $pid !== '' && !$this->isProcessRunning((int) $pid)) {
        $state = 'finished';
      }

      $this->emitCompletionLogOnce($dir, $meta, $state);

      $runtime = '';
      $started_at = (int) trim((string) (@file_get_contents($dir . '/started_at.txt') ?: '0'));
      $finished_at = (int) trim((string) (@file_get_contents($dir . '/finished_at.txt') ?: '0'));
      if ($started_at > 0 && $finished_at >= $started_at) {
        $runtime = (string) ($finished_at - $started_at) . 's';
      }
      elseif ($started_at > 0 && $state === 'running') {
        $runtime = (string) max(0, time() - $started_at) . 's';
      }

      $jobs[] = [
        'job_id' => $job_id,
        'status' => $state,
        'model' => (string) ($meta['model'] ?? ''),
        'prompt_preview' => mb_substr((string) ($meta['prompt'] ?? ''), 0, 80),
        'max_length' => (string) ($meta['max_length'] ?? ''),
        'runtime' => $runtime,
        'pid' => $pid,
        'created_at' => !empty($meta['created_at']) ? date('Y-m-d H:i:s', (int) $meta['created_at']) : '',
      ];
    }

    return $jobs;
  }

  private function isProcessRunning(int $pid): bool {
    if ($pid <= 0) {
      return FALSE;
    }
    if (function_exists('posix_kill')) {
      return @posix_kill($pid, 0);
    }
    return is_dir('/proc/' . $pid);
  }

  private function emitCompletionLogOnce(string $job_dir, array $meta, string $state): void {
    if (!in_array($state, ['success', 'failed', 'finished'], TRUE)) {
      return;
    }

    $marker = $job_dir . '/completion_logged.txt';
    if (is_file($marker)) {
      return;
    }

    $exit_code_raw = trim((string) (@file_get_contents($job_dir . '/exit_code.txt') ?: ''));
    $exit_code = $exit_code_raw === '' ? -1 : (int) $exit_code_raw;
    $stdout = (string) (@file_get_contents($job_dir . '/stdout.log') ?: '');
    $stderr = (string) (@file_get_contents($job_dir . '/stderr.log') ?: '');

    $started_at = (int) trim((string) (@file_get_contents($job_dir . '/started_at.txt') ?: '0'));
    $finished_at = (int) trim((string) (@file_get_contents($job_dir . '/finished_at.txt') ?: '0'));
    $runtime_sec = 0;
    if ($started_at > 0 && $finished_at >= $started_at) {
      $runtime_sec = $finished_at - $started_at;
    }

    $context = [
      '@job_id' => (string) ($meta['job_id'] ?? basename($job_dir)),
      '@user' => (string) ($meta['user_id'] ?? ''),
      '@username' => (string) ($meta['username'] ?? ''),
      '@model' => (string) ($meta['model'] ?? ''),
      '@prompt' => (string) ($meta['prompt'] ?? ''),
      '@max_length' => (string) ($meta['max_length'] ?? ''),
      '@runtime_sec' => (string) $runtime_sec,
      '@exit_code' => (string) $exit_code,
      '@output_preview' => mb_substr($this->extractGeneratedText($stdout) ?: trim($stdout), 0, 2000),
      '@error_preview' => mb_substr(trim($stderr), 0, 2000),
    ];

    if ($exit_code === 0 || $state === 'success') {
      $this->getLogger('copilot_agent_tracker')->notice(
        'LLM_CALL_SUCCESS job_id=@job_id user_id=@user username=@username model=@model max_length=@max_length prompt=@prompt runtime_sec=@runtime_sec exit_code=@exit_code output_preview=@output_preview',
        $context
      );
    }
    else {
      // Warning (not error): failed LLM test jobs on this dev tool page are
      // expected when the model is unavailable or the environment is not set up.
      // Error-level would pollute production log monitoring.
      $this->getLogger('copilot_agent_tracker')->warning(
        'LLM_CALL_FAILED job_id=@job_id user_id=@user username=@username model=@model max_length=@max_length prompt=@prompt runtime_sec=@runtime_sec exit_code=@exit_code error=@error_preview',
        $context
      );
    }

    @file_put_contents($marker, (string) time());
  }

}
