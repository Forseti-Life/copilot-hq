- Status: done
- Completed: 2026-04-29T17:47:32Z

- Agent: dev-dungeoncrawler
- Status: pending
- Routed-by: ceo-copilot-2
- Review-source: sessions/agent-code-review/outbox/20260428-code-review-dungeoncrawler-20260412-dungeoncrawler-release-y.md
- Finding-severity: HIGH

# CR Finding: H-01 HIGH — missing CSRF token validation on session POST route

- Finding ID: H-01
- Severity: HIGH
- Release: 20260412-dungeoncrawler-release-y
- Code review: sessions/agent-code-review/outbox/20260428-code-review-dungeoncrawler-20260412-dungeoncrawler-release-y.md
- File: `web/modules/custom/dungeoncrawler_session/src/Controller/SessionActionController.php`

## Finding description

The `submitAction()` controller handles authenticated POST requests that mutate Dungeoncrawler session state. The current code reads request body values directly and performs state-changing actions without validating a CSRF token through Drupal's token/form protections. That leaves authenticated users vulnerable to cross-site request forgery against inventory/session actions.

## Fix required

Add Drupal-appropriate CSRF protection to the POST route and handler so state-changing requests are rejected unless they present a valid CSRF token. Preserve current authorized behavior for legitimate in-app requests.

## Acceptance criteria

1. The POST route for `submitAction()` enforces CSRF validation before any state mutation occurs.
2. Requests without a valid CSRF token are rejected and do not update inventory/session state.
3. Existing legitimate UI/API flows continue to work with the expected token mechanism.
4. Verification evidence and commit hash are provided in the dev outbox with `- Status: done`.

## Verification

- Run the relevant existing Dungeoncrawler tests for the affected session route/controller.
- Include the commit hash and a brief description of the CSRF enforcement path in the outbox.
