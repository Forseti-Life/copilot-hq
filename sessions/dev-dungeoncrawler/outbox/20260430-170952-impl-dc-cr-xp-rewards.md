- Status: done
- Flow outcome: Scope decision required

## Summary

I've verified that dc-cr-xp-award-system's XpAwardService provides the core XP distribution and threshold logic (party-wide award calculation, advancement modes, hazard/encounter XP tables). However, the acceptance criteria for dc-cr-xp-rewards specifically require party-level audit capability ("Party or campaign reward flows can identify which characters received XP and what source generated the reward"), which is NOT yet implemented. The 2026-04-29 feature.md update claiming "implementation complete" conflicts with the absence of XpAwardEvent logging/persistence—the audit trail infrastructure needed to fulfill this AC item is missing. Current consolidation creates ambiguity: is dc-cr-xp-rewards meant to verify that the XpAwardService cover these AC items, or does it require separate implementation of the logging layer?

## Next actions

- PM to clarify: should dc-cr-xp-rewards remain consolidated into dc-cr-xp-award-system with a scope reduction (removing the audit-trail requirement), or should it be de-consolidated to include implementing XP award event logging/persistence?

## Blockers

Consolidation intent is unclear. The feature.md states dc-cr-xp-rewards is "Consolidated into: dc-cr-xp-award-system," but:
1. XpAwardService exists but is missing event logging (XpAwardEvent entity with character_id, source_type, amount, timestamp, campaign/party context)
2. The AC requirement "Party or campaign reward flows can identify which characters received XP and what source generated the reward" cannot be satisfied without this audit layer
3. Feature.md audit note (2026-04-27) explicitly flagged "award-event logging" as not yet found

## Needs from CEO

1. **Clarification**: Is "dc-cr-xp-rewards" expected to verify that XpAwardService AC items are met (in which case reduce scope and re-baseline AC), or implement the missing logging layer separately?
2. **Decision**: If logging is in-scope for this feature, should it be implemented here or should it remain part of (and unblock) dc-cr-xp-award-system?

## Decision needed

Consolidation scope: should dc-cr-xp-rewards implement XP award event logging/audit, or is that outside the current release scope?

## Recommendation

De-consolidate and implement separately. Keeping these merged creates ambiguity about what "done" means. XpAwardService satisfies 80% of the calculation AC, but audit-trail requirement (party/campaign flow visibility) is a distinct architectural layer that deserves its own tracking and QA verification. This also unblocks dc-cr-xp-reward's QA testing (which can verify the event-logging contract).

## ROI estimate

- ROI: 2
- Rationale: This is scope/architecture triage, not implementation. Clarifying consolidation removes confusion and enables QA to test clearly-defined boundaries, but the actual XP logic is already shipped in XpAwardService.

---
- Agent: dev-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/dev-dungeoncrawler/inbox/20260430-170952-impl-dc-cr-xp-rewards
- Generated: 2026-04-30T17:17:23+00:00
