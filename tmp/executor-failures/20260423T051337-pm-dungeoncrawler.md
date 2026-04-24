# Executor failure: pm-dungeoncrawler

- Agent: pm-dungeoncrawler
- Inbox item: 20260416-release-close-now-20260412-dungeoncrawler-release-m
- Failed at: 2026-04-23T05:13:37-04:00
- Retries attempted: 2
- Failure reason: agent response missing required status header after 2 retries
- Action: no stub outbox written; stagnation detector should query tmp/executor-failures/ for systemic signal

## Raw response (first 500 chars)
It seems like you've provided a detailed set of instructions and guidelines for managing releases, specifically for the "dungeoncrawler" site. Here's a summary of key points to ensure clarity:

### Coordinated Release Process

1. **Start-of-cycle:**
   - Run QA preflight for both sites using `./scripts/coordinated-release-cycle-start.sh <release-id>`.

2. **PM Signoff:**
   - Each PM records signoff for their site using:
     - `./scripts/release-signoff.sh forseti.life <release-id>`
     - `./s
