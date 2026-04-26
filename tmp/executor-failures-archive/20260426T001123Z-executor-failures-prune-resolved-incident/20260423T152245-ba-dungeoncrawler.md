# Executor failure: ba-dungeoncrawler

- Agent: ba-dungeoncrawler
- Inbox item: 20260414-ba-refscan-dungeoncrawler-pf2e-guns-and-gears
- Failed at: 2026-04-23T15:22:45-04:00
- Retries attempted: 2
- Failure reason: agent response missing required status header after 2 retries
- Action: no stub outbox written; stagnation detector should query tmp/executor-failures/ for systemic signal

## Raw response (first 500 chars)
### Process Flow for BA Task Execution

#### 0) Release-cycle instruction refresh (required)
**Action:** At the start of a release cycle, refactor your seat instructions file to ensure owned scope, expected inputs, and escalation paths are still valid.
- **File to update:** `org-chart/agents/instructions/ba-dungeoncrawler.instructions.md`
- **Verification Method:** Review the updated instructions with the primary owner (`pm-dungeoncrawler`) and methodology owner (`ceo-copilot`).

#### 1) Read th
