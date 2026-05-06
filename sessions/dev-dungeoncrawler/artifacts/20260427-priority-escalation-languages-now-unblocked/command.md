# PRIORITY: Start dc-cr-languages NOW — prerequisites complete

## Context
- Elf-ancestry implementation completed at 22:05 (commit 0d77d94246)
- bonus_language_pool metadata now available for languages feature to hook onto
- Languages feature is HIGH-ROI (1627) and unblocks 2 downstream elf-heritage features (arctic, cavern)
- Languages inbox item has been queued since 20260427-171039 but no work has started yet

## Required actions (immediate)
1. Pick up the existing inbox item: `20260427-171039-impl-dc-cr-languages`
2. Review acceptance criteria, test plan, and audit note in that item
3. Implement or complete the remaining work scoped for release-x
4. Produce an outbox with clear status: complete, in-progress (with ETA), or blocked (with explicit reason)
5. If complete or in-progress, include any commit hashes and test execution evidence

## Why now?
- Blocking release-x progress: arctic-elf and cavern-elf cannot ship until languages is resolved
- Release-x auto-close triggers in ~2 hours (24h from start at 2026-04-27T14:05:03)
- QA already has test plan ready; just waiting for implementation completion or clear status

## ROI: 1627
- Highest-ROI item in dev-dungeoncrawler queue
- Unblocks 2 features + enables release completion

## Next action
- Dev-dungeoncrawler: start languages implementation immediately
- Produce outbox with status before release-x auto-close
