# Executor failure: pm-dungeoncrawler

- Agent: pm-dungeoncrawler
- Inbox item: 20260505-groom-20260412-dungeoncrawler-release-v
- Failed at: 2026-05-05T15:19:26+00:00
- Retries attempted: 2
- Failure reason: agent response missing required status header after 2 retries
- Action: no stub outbox written; stagnation detector should query tmp/executor-failures/ for systemic signal

## Raw response (first 500 chars)
ERROR: Bedrock inference failed: An error occurred (ValidationException) when calling the InvokeModel operation: messages.0.content.0.type: Field required
