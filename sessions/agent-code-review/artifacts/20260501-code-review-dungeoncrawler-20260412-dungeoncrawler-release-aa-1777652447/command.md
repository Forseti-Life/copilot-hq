- Flow id: release_shipping_flow
- Flow run id: 20260412-dungeoncrawler-release-aa
- Flow node: Release Code Review
- Flow owner seat: agent-code-review
- Flow previous node: Seed Release Cycle
- Product team id: dungeoncrawler
- Product team label: Dungeoncrawler
- Available flow outcomes: MEDIUM+ findings present | No MEDIUM+ findings

# Flow handoff: release_shipping_flow / Release Code Review

Treat `scripts/release-cycle-start.sh` as the system-owned completion of `Seed Release Cycle`.
Your task is to execute the first routed release gate step for the active release below.

- Site: dungeoncrawler
- Product team: dungeoncrawler
- Release id: 20260412-dungeoncrawler-release-aa
- Release started at: 2026-05-01T16:19:10+00:00

## Release scope artifacts
- `dc-cr-rituals`
  - Feature brief: `features/dc-cr-rituals/feature.md`
  - Acceptance criteria: `features/dc-cr-rituals/01-acceptance-criteria.md`
  - Test plan: `features/dc-cr-rituals/03-test-plan.md`

## Required evidence controls
1. Treat this `command.md` as the authoritative release handoff artifact for Gate 1b.
2. Cite the exact reviewed feature/outbox artifact path(s) in your outbox summary or findings.
3. If the scoped feature list or supporting artifacts are incomplete, do not clear the gate; record a MEDIUM finding for missing release evidence and use `- Flow outcome: MEDIUM+ findings present`.
4. If you apply the data-only fast-path, say so explicitly and name the reviewed file set.

## Required action
1. Perform the pre-ship release code review for `20260412-dungeoncrawler-release-aa`.
2. Review all commits in this release cycle against `org-chart/agents/instructions/agent-code-review.instructions.md`.
3. Focus on: CSRF protection on new POST routes, authorization bypass risks, schema hook pairing, stale private duplicates of canonical data, and hardcoded paths.
4. Produce one finding per issue with severity (CRITICAL/HIGH/MEDIUM/LOW), file path, and recommended fix pattern.
5. Include exactly one `- Flow outcome:` line in your outbox:
   - `- Flow outcome: MEDIUM+ findings present` when any MEDIUM/HIGH/CRITICAL findings require PM triage
   - `- Flow outcome: No MEDIUM+ findings` when Gate 1b is clear for QA verification
