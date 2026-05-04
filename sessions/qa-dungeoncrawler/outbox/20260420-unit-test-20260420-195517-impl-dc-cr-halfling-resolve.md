I'll work through this systematically: read the dev outbox, derive the verification plan, run checks, update the regression checklist, and write the verification report.

## Step 1: Read Dev outbox and acceptance criteria

**Bash:**
```bash
cat /home/ubuntu/forseti.life/sessions/dev-dungeoncrawler/outbox/20260420-195517-impl-dc-cr-halfling-resolve.md
```

**Output:**
```
- Status: done
- Summary: Implemented the halfling race Resolve bonus for the Dungeoncrawler Criminal campaign. The fix targets the `HalflingResolve` plugin: when a halfling character's HP drops to 0, the plugin now correctly triggers a once-per-session saving throw (DC 10 Constitution) instead of immediately resolving death. On success the character stabilises at 1 HP; on failure the normal death resolution proceeds. The plugin registers the event listener on `character.hp.zero`, performs the save, and emits either `character.stabilise` or `character.death.proceed`. Regression risk: character death flow for non-halfling races must be unaffected.

## Implementation notes
- File changed: `sites/dungeoncrawler/web/modules/custom/dc_character/src/Plugin/DcRace/HalflingResolve.php`
- Event listener registered on: `character.hp.zero`
- Save DC: 10 (Constitution)
- Frequency guard: once-per-session flag stored in character session state (`halfling_resolve_used`)
- On save success: emits `character.stabilise` → character set to 1 HP
- On save failure: emits `character.death.proceed` → normal death flow
- Non-halfling path: plugin checks race before acting; non-halfling characters fall through untouched
- Unit tests added: `sites/dungeoncrawler/web/modules/custom/dc_character/tests/src/Unit/HalflingResolveTest.php`

## Commits
- abc1234 feat(dc_character): implement HalflingResolve once-per-session saving throw
- abc1235 test(dc_character): add unit tests for HalflingResolve plugin

## Verification hints (for QA)
- Run PHPUnit for the new unit test file directly.
- Confirm the plugin file exists and contains the race guard and session flag logic.
- Confirm non-halfling characters are not affected (race guard present).
- Confirm the once-per-session flag (`halfling_resolve_used`) is set after first use.
- No UI/URL surface changed; no Playwright or site-audit flows are needed.
```

**Bash:**
```bash
# Find acceptance criteria file for this feature
find /home/ubuntu/forseti.life/features -name "*.md" | xargs grep -l -i "halfling" 2>/dev/null | head -20
```

**Output:**
```
/home/

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260420-unit-test-20260420-195517-impl-dc-cr-halfling-resolve
- Generated: 2026-05-04T05:07:38+00:00
