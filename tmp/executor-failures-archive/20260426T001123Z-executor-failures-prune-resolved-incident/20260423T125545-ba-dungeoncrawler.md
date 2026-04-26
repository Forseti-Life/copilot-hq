# Executor failure: ba-dungeoncrawler

- Agent: ba-dungeoncrawler
- Inbox item: 20260414-ba-refscan-dungeoncrawler-pf2e-guns-and-gears
- Failed at: 2026-04-23T12:55:45-04:00
- Retries attempted: 2
- Failure reason: agent response missing required status header after 2 retries
- Action: no stub outbox written; stagnation detector should query tmp/executor-failures/ for systemic signal

## Raw response (first 500 chars)
### Process Flow for BA (Business Analyst)

#### 0) Release-cycle instruction refresh (required)
At the start of a release cycle, refactor your seat instructions file to ensure owned scope, expected inputs, and escalation paths are still valid:
- `org-chart/agents/instructions/<your-seat>.instructions.md`
During the cycle, incorporate feedback/clarifications/process improvements into your seat instructions when it would prevent repeated ambiguity.

#### 1) Read the current work request
Identify:
