- Status: done
- Summary: Designed and wrote the full test plan for the Death Warden dwarf heritage feature (dc-cr-dwarf-heritage-death-warden), covering all AC items across happy path, edge cases, and failure modes. Created `features/dc-cr-dwarf-heritage-death-warden/03-test-plan.md` with 10 mapped test cases, created the feature-scoped suite overlay at `qa-suites/products/dungeoncrawler/features/dc-cr-dwarf-heritage-death-warden.json`, validated the overlay with `qa-suite-validate.py`, and signalled completion to PM via `qa-pm-testgen-complete.sh`. Commit recorded below.

## Next actions
- Await Stage 0 of release 20260412-dungeoncrawler-release-z to compile overlay into live suite manifest.
- At Stage 0: verify dependencies dc-cr-dwarf-ancestry and dc-cr-heritage-system are in scope before activating this overlay.
- No Dev inbox items generated (per delegation rule); Dev consumes test plan directly.

## Blockers
- None.

## Needs from CEO
- N/A

---

### Work log

#### Step 1 — Read template and existing artifacts

Checked `templates/qa-feature-suite.json` and prior feature overlays for format reference, then reviewed the full AC in `01-acceptance-criteria.md`.

#### Step 2 — Created test plan

Created `features/dc-cr-dwarf-heritage-death-warden/03-test-plan.md`:

```bash
mkdir -p /home/ubuntu/forseti.life/features/dc-cr-dwarf-heritage-death-warden
```

File content written directly. Full test plan below (also on disk).

---

**`features/dc-cr-dwarf-heritage-death-warden/03-test-plan.md`**

```markdown
# Test Plan — dc-cr-dwarf-heritage-death-warden
# Dwarf Heritage: Death Warden

- Feature: dc-cr-dwarf-heritage-death-warden
- Release target: 20260412-dungeoncrawler-release-z
- QA owner: qa-dungeoncrawler
- PM owner: pm-dungeoncrawler
- Date: 2026-04-29
- Dependencies: dc-cr-dwarf-ancestry, dc-cr-heritage-system
- KB references: none found

---

## Test Cases

### TC-01 — Heritage exists and is selectable for dwarves
- AC ref: Happy Path 1
- Suite: playwright / character-creation
- Description: Navigate to heritage selection for a dwarf character. Confirm "Death Warden" appears in the list of available heritages and can be selected.
- Expected: Heritage is present and selectable; selection persists on save.
- Roles covered: authenticated player
- HTTP status: 200 (character save endpoint)
- Automation feas

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260429-195345-testgen-dc-cr-dwarf-heritage-death-warden
- Generated: 2026-04-29T20:53:30+00:00
