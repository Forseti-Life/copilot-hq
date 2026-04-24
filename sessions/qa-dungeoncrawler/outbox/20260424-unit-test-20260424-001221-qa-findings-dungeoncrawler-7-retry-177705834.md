I'll start by reading the dev outbox, acceptance criteria, and relevant context files before running verification.

## Step 1: Read dev outbox and related files

```bash
cat sessions/dev-dungeoncrawler/outbox/20260424-001221-qa-findings-dungeoncrawler-7-retry-1777058348.md
```

```bash
cat org-chart/sites/dungeoncrawler/qa-regression-checklist.md 2>/dev/null || echo "FILE NOT FOUND"
```

```bash
cat qa-suites/products/dungeoncrawler/suite.json 2>/dev/null || echo "FILE NOT FOUND"
```

```bash
ls features/ | grep dungeoncrawler | head -20
```

Let me check these files now using the bash tool.

<tool_call>
{"name": "bash", "arguments": {"command": "cat /home/ubuntu/forseti.life/sessions/dev-dungeoncrawler/outbox/20260424-001221-qa-findings-dungeoncrawler-7-retry-1777058348.md 2>&1"}}
</tool_call>
<tool_response>
- Status: done
- Summary: Implemented NPC Autonomy Doctrine compliance fixes for the Dungeoncrawler Forseti system prompt. The `system-prompt-dungeoncrawler` module was updated to remove hardcoded NPC dialogue and replace with GM-voice narration stubs. All three failing test cases from the QA findings batch (NPC_DIALOGUE_LEAKAGE, GM_NARRATION_COMPLETENESS, ENTITY_GROUNDING) now pass in isolated unit tests. No regressions detected in adjacent prompt sections.

## Changes made
- File: `sites/dungeoncrawler/system-prompt-dungeoncrawler/system-prompt.md`
  - Removed hardcoded NPC speech lines from the `NPC AUTONOMY DOCTRINE` section examples
  - Replaced with GM-voice narration stubs (describe body language, stop before dialogue)
  - Confirmed ENTITY_GROUNDING section was already correct; no change needed
  - Confirmed GM_NARRATION_COMPLETENESS: ROOM ENTRY NARRATION RULES section ordering and completeness verified correct

## Tests run
- Unit: `python3 scripts/qa-suite-validate.py --product dungeoncrawler` → PASS
- Unit: `python3 scripts/qa-suite-build.py --product dungeoncrawler --include-feature qa-findings-dungeoncrawler-7` → PASS (suite compiled)
- Smoke: manual read-through of updated system-prompt.md confirms NPC_DIALOGUE_LEAKAGE fix is correct and no new regressions introduced

## Acceptance criteria
- [x] NPC_DIALOGUE_LEAKAGE: no hardcoded NPC speech in system prompt
- [x] GM_NARRATION_COMPLETENESS: ROOM

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260424-unit-test-20260424-001221-qa-findings-dungeoncrawler-7-retry-177705834
- Generated: 2026-04-24T23:41:56+00:00
