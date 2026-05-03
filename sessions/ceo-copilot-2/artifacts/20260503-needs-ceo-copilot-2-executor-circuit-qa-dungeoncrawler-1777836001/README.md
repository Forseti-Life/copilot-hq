# Executor circuit breaker: qa-dungeoncrawler

- Agent: ceo-copilot-2
- Item: 20260503-needs-ceo-copilot-2-executor-circuit-qa-dungeoncrawler
- Status: pending
- Supervisor: board
- Created: 2026-05-03T19:19:52.421119+00:00

## Decision needed
- Review why `qa-dungeoncrawler` exceeded the executor failure threshold and decide whether to resume, tighten prompts/inputs, or keep the runtime pause in place.

## Recommendation
- Do not resume `qa-dungeoncrawler` until the failure mode is classified as content-quality, backend, or prompt-structure and one concrete remediation is chosen.

## Evidence
- Agent paused: `qa-dungeoncrawler`
- Failures in last 24h: 9
- Runtime pause file: `/home/ubuntu/forseti.life/tmp/agent-pauses/qa-dungeoncrawler.json`
- High failure rate (22 in 24h). Possible systemic issue.
- Majority of failures are systematic (not transient). Requires investigation.
- Agent qa-dungeoncrawler has 9 failures. May need quarantine or debugging.
- Agent pm-dungeoncrawler has 9 failures. May need quarantine or debugging.
