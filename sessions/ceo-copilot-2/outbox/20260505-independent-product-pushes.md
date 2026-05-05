- Status: done
- Summary: Removed cross-team push dependency from the active release flow so each product team can ship independently; owning PM signoff now represents the only PM gate for push readiness, and push automation/status surfaces were updated to match.

## What changed
- `scripts/release-signoff.sh`
  - rejects cross-team PM co-sign attempts
  - keeps Gate 2 + owner PM signoff as the release gate
  - removes shared release-operator / all-PMs-signed push queuing language
- `scripts/release-signoff-status.sh`
  - now reports owner-only PM signoff state for a release
- `orchestrator/release_prerequisites.py`
  - no longer requires cross-team PM co-signs before push
- `orchestrator/release_cycle.py`
  - push step now dispatches releases per team independently
  - post-push follow-up items are written to the owning PM lane
- `scripts/post-coordinated-push.sh`
  - now supports team-scoped usage for independent post-push advancement
- `scripts/ceo-release-health.sh`
  - health output now reports independent push readiness instead of cross-team co-sign matrices
- `scripts/ceo-pipeline-remediate.py`
  - no longer auto-queues cross-team signoff reminders

## Validation
- Targeted regression suite passed for release-cycle control, signoff ownership, signoff metadata, and remediation.
- Live sanity check for `20260412-dungeoncrawler-release-t` now shows:
  - required PM signoffs: 1
  - owner team: `dungeoncrawler`
  - ready for official push: `true`

## Operational result
- Product teams can now ship on their own PM/QA gates without waiting on other product teams.
- The old coordinated push path is no longer the active dependency for current release readiness.

---
- Agent: ceo-copilot-2
- Generated: 2026-05-05T13:22:00Z
