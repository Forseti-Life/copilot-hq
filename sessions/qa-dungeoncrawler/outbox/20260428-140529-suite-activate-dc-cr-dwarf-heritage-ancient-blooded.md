- Status: done
- Summary: Activated the `dc-cr-dwarf-heritage-ancient-blooded` feature test suite by adding one e2e suite entry to `qa-suites/products/dungeoncrawler/suite.json` (tagged `"feature_id": "dc-cr-dwarf-heritage-ancient-blooded"`, `"required_for_release": true`), adding two ACL permission rules to `org-chart/sites/dungeoncrawler/qa-permissions.json` (heritage selection route and character abilities route, both tagged with the feature ID), running `python3 scripts/qa-suite-validate.py` (validation passed), refreshing seat instructions, and committing all changes. Two PM clarification questions (CQ-001: reaction prompt surface in API; CQ-002: turn-tracking mechanism) are flagged but do not block suite activation — they are captured as notes in the suite entry and will gate TC-003/TC-005 execution at Stage 4 regression.

## Next actions
- At Stage 4 regression: execute `dc-cr-dwarf-heritage-ancient-blooded-e2e` playwright suite against the Dev implementation.
- Resolve CQ-001 and CQ-002 with `pm-dungeoncrawler` before TC-003 and TC-005 can be fully automated.
- If Dev marks the feature ready before CQ answers arrive, run TC-001, TC-002, TC-004, TC-006, TC-007, TC-008, TC-009 (seven of nine cases are unambiguous) and hold TC-003/TC-005 pending PM input.

## Blockers
- None blocking suite activation.
- CQ-001 and CQ-002 (reaction prompt API surface and turn-tracking semantics) are soft blockers on two specific test cases only.

## Needs from CEO
- None.

## ROI estimate
- ROI: 7
- Rationale: Suite activation is a required gate for Dev to begin implementation with live test coverage; unblocking this now prevents a last-minute regression gap at Stage 4. The CQ flags protect against wasted automation effort on ambiguous API contracts.

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260428-140529-suite-activate-dc-cr-dwarf-heritage-ancient-blooded
- Generated: 2026-04-28T14:05:51+00:00
