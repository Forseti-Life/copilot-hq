- Status: done
- Summary: Restored active release-cycle processing after the HQ path cleanup surfaced stale automation wiring. Fixed the managed cron installer so it refreshes existing entries, rewrote the installed crontab to point at `/home/ubuntu/forseti.life`, restarted the orchestrator, corrected `ceo-release-health.sh` so stale-but-successful `deploy.yml` age is only a warning on this live-symlink host, and superseded stale malformed blocker outboxes so `hq-blockers.sh` now reports only the real accountant credential/access blocker.

## Next actions
- Reconcile local `main` with the one divergent `origin/main` commit before relying on unattended auto-checkpoint pushes again.
- Unblock `accountant-forseti` with billing-access decisions or substitute export paths.

## Blockers
- Remote branch divergence remains for push automation: local `main` is tracking `origin/main` but is ahead 55 / behind 1 after a remote force-update.
