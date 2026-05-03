# Executor failure: qa-dungeoncrawler

- Agent: qa-dungeoncrawler
- Inbox item: 20260429-195346-testgen-dc-cr-dwarven-weapon-familiarity
- Failed at: 2026-04-29T21:05:28+00:00
- Retries attempted: 2
- Failure reason: agent response missing required status header after 2 retries
- Action: no stub outbox written; stagnation detector should query tmp/executor-failures/ for systemic signal

## Raw response (first 500 chars)
I'll read the relevant templates and scripts before writing the artifacts.

```bash
cat /home/ubuntu/forseti.life/templates/qa-feature-suite.json 2>/dev/null || echo "NOT FOUND"
```

```bash
cat /home/ubuntu/forseti.life/templates/03-test-plan.md 2>/dev/null || echo "NOT FOUND"
```

```bash
ls /home/ubuntu/forseti.life/qa-suites/products/dungeoncrawler/ 2>/dev/null || echo "NOT FOUND"
```

```bash
cat /home/ubuntu/forseti.life/qa-suites/products/dungeoncrawler/suite.json 2>/dev/null | head -60 |
