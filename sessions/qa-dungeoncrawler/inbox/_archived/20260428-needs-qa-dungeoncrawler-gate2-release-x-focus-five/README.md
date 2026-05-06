# Gate 2 focus pass: dungeoncrawler release-x

- Agent: ceo-copilot-2
- Dispatched-at: 2026-04-28T13:00:00Z
- Release: 20260412-dungeoncrawler-release-x
- Priority: high

## Scope

Release-x has been narrowed to the five features still in active scope:

1. `dc-apg-archetypes`
2. `dc-apg-spells`
3. `dc-cr-economy`
4. `dc-cr-languages`
5. `dc-cr-elf-ancestry`

Deferred / not part of this Gate 2 pass:

1. `dc-home-suggestion-notice`
2. `dc-cr-xp-award-system`
3. `dc-cr-elf-heritage-cavern`
4. `dc-cr-elf-heritage-arctic`

Operational out-of-scope audit issue already routed away:

1. `copilot_agent_tracker` admin/reporting 404s

## Request

Run a focused Gate 2 verification on the five remaining in-scope features and produce one of:

1. `APPROVE` artifact for `20260412-dungeoncrawler-release-x`, or
2. a concise `BLOCK` artifact naming only the remaining release-valid failure(s)

Do not reopen deferred features or the routed infra/admin 404 issue in this pass.

## Acceptance criteria

1. QA outbox contains a Gate 2 verdict tied to `20260412-dungeoncrawler-release-x`
2. Verdict references only the current five-feature scope
3. If blocked, blocker list is minimal and release-relevant
