I'll start by reading the Dev outbox and relevant acceptance criteria before executing the verification.

<tool_call>
{"name": "bash", "arguments": {"command": "cat /home/ubuntu/forseti.life/sessions/dev-forseti/outbox/20260423-1776962948-impl-forseti-shared-modules-automation-validation.md 2>&1"}}
</tool_call>
<tool_response>
# Dev Outbox: 20260423-1776962948-impl-forseti-shared-modules-automation-validation

- Status: done
- Summary: Implemented and enabled the `forseti_shared_modules` automation-validation hook in `hook_requirements()`. The hook now validates all expected shared modules (forseti_core, forseti_ai, forseti_ai_config, forseti_social, forseti_media) and reports any missing or disabled ones as errors at install/update time. A PHP unit test (KernelTest) was written to verify the hook logic directly, and the test was confirmed to run successfully via `vendor/bin/phpunit`.

## What was done
1. Added `hook_requirements()` to `forseti_shared_modules.module` that iterates over expected shared module machine names and uses the module handler to confirm each is installed and enabled.
2. Wrote `ForsetiSharedModulesRequirementsTest` (KernelTest) covering:
   - Happy path: all expected modules installed → requirements met
   - Failure path: one module disabled/uninstalled → requirement reported as error
3. Ran the test: `vendor/bin/phpunit --filter ForsetiSharedModulesRequirementsTest`
4. Implemented `hook_update_N()` (`forseti_shared_modules_update_10001`) to trigger the requirements check during `drush updb`, ensuring the validation runs in CI/deploy pipelines.

## Acceptance criteria mapping
- [x] `hook_requirements()` implemented in `forseti_shared_modules.module`
- [x] Reports REQUIREMENT_ERROR for any missing/disabled shared module
- [x] KernelTest covers happy path and failure path
- [x] Test passes via `vendor/bin/phpunit`
- [x] `hook_update_N()` added for pipeline integration

## Files changed
- `sites/forseti/web/modules/custom/forseti_shared_modules/forseti_shared_modules.module`
- `sites/forseti/web/modules/custom/forseti_shared_modules/tests/src/Kernel/ForsetiSharedModulesRequirementsTest.php`

## Evidence
- Test command: `vendor/bin/phpunit --filter ForsetiSharedModulesRequirementsTest`
- Test output (last run):

```
PHPUnit 9.6.22 by Sebastian Bergmann and contributors.
...
K                                                                   2 / 2 (100%)

Time: 00:00.246, Memory: 

---
- Agent: qa-forseti
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-forseti/inbox/20260424-unit-test-20260423-1776962948-impl-forseti-shared-modules-automation-v
- Generated: 2026-04-25T02:43:13+00:00
