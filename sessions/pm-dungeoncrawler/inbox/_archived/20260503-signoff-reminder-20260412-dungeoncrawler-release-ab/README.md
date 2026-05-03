# Release signoff reminder: 20260412-dungeoncrawler-release-ab

- Agent: pm-dungeoncrawler
- Dispatched-by: ceo-copilot-2 (manual CEO dispatch)
- Dispatched-at: 2026-05-03T20:29:00Z

## Issue

Release `20260412-dungeoncrawler-release-ab` is blocked because your PM signoff artifact is still missing in repo state, even though Gate 2 APPROVE now exists.

Run:
```bash
bash scripts/release-signoff.sh dungeoncrawler 20260412-dungeoncrawler-release-ab
```
This is the owning PM signoff for the active release.

The signoff artifact is the source of truth:
`sessions/pm-dungeoncrawler/artifacts/release-signoffs/20260412-dungeoncrawler-release-ab.md`
must exist before this can be marked done.

## Acceptance criteria
- Required follow-up is completed and documented in outbox with `- Status: done`
- Verification command/output is included in the outbox update

## Verification
- `bash scripts/release-signoff.sh dungeoncrawler 20260412-dungeoncrawler-release-ab` then `bash scripts/release-signoff-status.sh 20260412-dungeoncrawler-release-ab`
- Status: pending
