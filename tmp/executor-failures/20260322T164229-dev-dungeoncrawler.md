# Executor failure: dev-dungeoncrawler

- Agent: dev-dungeoncrawler
- Inbox item: 20260320-impl-dc-cr-clan-dagger
- Failed at: 2026-03-22T16:42:29-04:00
- Retries attempted: 2
- Failure reason: agent response missing required status header after 2 retries
- Action: no stub outbox written; stagnation detector should query tmp/executor-failures/ for systemic signal

## Raw response (first 500 chars)
The bug is `removeItemFromInventory` filters on `location_type = 'inventory'` but items are stored with `location_type = 'carried'`. The `ownerTypeToLocationType` maps character→inventory but items are stored as `carried`. The `sellItem` query in my method doesn't apply this filter — it's the remove call that fails. Fix the `sellItem` to call `removeItemFromInventory` without the `ownerTypeToLocationType` filter mismatch by passing the correct owner type, but actually the issue is deeper: `r
