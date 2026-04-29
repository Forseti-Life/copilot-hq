- Status: done
- Summary: Suite activation for dc-apg-rituals is complete. Seven test cases (TC-RIT-01 through TC-RIT-07) have been added to `qa-suites/products/dungeoncrawler/suite.json`, each tagged with `feature_id: "dc-apg-rituals"` and `required_for_release: true`. Four permission rules have been added to `org-chart/sites/dungeoncrawler/qa-permissions.json` covering the ritual catalog route, ritual detail view, GM-gated rare/uncommon rituals, and the book_id disambiguation lookup — all tagged with `feature_id: "dc-apg-rituals"`. Suite validation passed clean. One gap flagged: the permissions file path in the command.md used a non-canonical path (`dungeoncrawler.life`) which was corrected to `dungeoncrawler` per the site instructions. Commit hash recorded below.

## Next actions
- Dev consumes suite entries as the implementation target for dc-apg-rituals this release cycle
- QA runs targeted regression on dc-apg-rituals surface at Stage 4 using the activated suite entries
- If any ritual routes beyond `/rituals` and `/rituals/{id}` are introduced by Dev, QA must add corresponding permission rules before Gate 2

## Blockers
- None

## Needs from CEO
- N/A

## ROI estimate
- ROI: 40
- Rationale: Suite activation is a required gate for release; without it Dev has no machine-consumable test target and Stage 4 regression cannot run. Low effort, high release-cycle leverage.

---

*Verification details below.*

---

### Suite entries added to `qa-suites/products/dungeoncrawler/suite.json`

Ran:

```bash
cat qa-suites/products/dungeoncrawler/suite.json
```

to read current state, then appended the following 7 entries (all tagged `"feature_id": "dc-apg-rituals"`, `"required_for_release": true`):

| id | label | type |
|---|---|---|
| dc-apg-rituals-tc-rit-01 | Ritual system accepts APG entries without structural changes | e2e |
| dc-apg-rituals-tc-rit-02 | Ritual stat block completeness: all required fields stored | e2e |
| dc-apg-rituals-tc-rit-03 | Multiple secondary casters pattern supported | e2e |
| dc-apg-rituals-tc-rit-04 | Rare/Uncommon rituals: GM-approval gate enforced | e2e |
| dc-apg-rituals-tc-rit-05 | Primary check modifier stored per-ritual, not hardcoded | e2e |
| dc-apg-rituals-tc-rit-06 | Edge: ritual with 0 secondary casters renders

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260429-194232-suite-activate-dc-apg-rituals
- Generated: 2026-04-29T19:45:25+00:00
