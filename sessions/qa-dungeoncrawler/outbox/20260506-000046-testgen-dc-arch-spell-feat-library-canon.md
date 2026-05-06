<<TOOL:bash>>
cat > /home/ubuntu/forseti.life/features/dc-arch-spell-feat-library-canon/03-test-plan.md << 'TESTPLAN'
# Test Plan: dc-arch-spell-feat-library-canon — Canonical Spell and Feat Library Migration

- Feature ID: dc-arch-spell-feat-library-canon
- Website: dungeoncrawler
- QA owner: qa-dungeoncrawler
- PM owner: pm-dungeoncrawler
- Status: grooming
- Test plan created: 2026-05-06
- KB reference: None found in knowledgebase/ for spell/feat DB migration patterns on this codebase. First-of-kind migration; lessons learned to be recorded post-ship.

## Scope note

Phased epic — 6 phases. Phases 1-3 are the minimum viable migration (target release-v or later). Phases 4-6 follow in subsequent releases. Each phase is independently verifiable. Phase-scoped child features will reference the relevant TC-* IDs at activation time. This plan covers the full epic.

---

## Phase 1: Canonical schema contract (AC-1)

### TC-1.1 — Registry spell record existence
- Description: dungeoncrawler_content_registry contains content_type='spell' rows after Phase 1 schema work
- Suite: dc-arch-spell-feat-library-canon-db
- Command: SELECT content_type, COUNT(*) FROM dungeoncrawler_content_registry WHERE content_type IN ('spell','feat') GROUP BY content_type
- Expected: Non-zero counts for both spell and feat matching expected catalog size
- Roles covered: system/DB
- Automation type: DB assertion via drush script

### TC-1.2 — Registry feat record existence
- Description: dungeoncrawler_content_registry contains content_type='feat' rows with stable content_id
- Suite: dc-arch-spell-feat-library-canon-db
- Expected: All feat records have non-null content_id
- Roles covered: system/DB
- Automation type: DB assertion

### TC-1.3 — Spell schema_data field completeness
- Description: Sampled spell records contain all required schema_data fields: name, level, tradition, cast_time, range, area, duration, description, traits
- Suite: dc-arch-spell-feat-library-canon-db
- Expected: All required keys present and non-null for sampled spell records
- Roles covered: system/DB
- Automation type: DB assertion

### TC-1.4 — Feat schema_data field completeness
- Description: Sampled feat records contain all required schema_data fields: prerequisites, benefits, traits, actions, special_rules, repeatability
- Suite: dc-arch-spell-feat-library-canon-db
- Expected: All required keys present for sampled feat records
- Roles covered: system/DB
- Automation type: DB assertion

---

## Phase 2: Library import and backfill (AC-1, AC-5)

### TC-2.1 — Backfill completeness
- Description: After import/backfill, spell and feat counts in registry match expected catalog size from packaged source files
- Suite: dc-arch-spell-feat-library-canon-db
- Expected: COUNT matches expected spell total and feat total
- Roles covered: system/DB
- Automation type: DB assertion

### TC-2.2 — Stable content_id uniqueness
- Description: No duplicate content_id values exist for spell or feat content types
- Suite: dc-arch-spell-feat-library-canon-db
- Command: SELECT content_id, COUNT(*) FROM dungeoncrawler_content_registry WHERE content_type IN ('spell','feat') GROUP BY content_id HAVING COUNT(*) > 1
- Expected: Zero rows returned
- Roles covered: system/DB
- Automation type: DB assertion

### TC-2.3 — Re-import idempotency
- Description: Running the import/backfill script twice does not create duplicate records or corrupt existing ones
- Suite: dc-arch-spell-feat-library-canon-db
- Expected: Record counts unchanged after second run; no duplicate content_ids
- Roles covered: system/DB
- Automation type: DB assertion + script run

---

## Phase 3: Read-path cutover (AC-2, AC-3)

### TC-3.1 — /api/spells returns registry-backed data (authenticated)
- Description: GET /api/spells returns spell list sourced from dungeoncrawler_content_registry
- Suite: dc-arch-spell-feat-library-canon-api
- Expected: HTTP 200; response contains spells with content_id fields matching registry records
- Roles covered: authenticated user (character owner)
- Automation type: HTTP/API assertion

### TC-3.2 — /api/spells/{id} resolves canonical record
- Description: GET /api/spells/{id} for a known content_id returns full spell detail from registry
- Suite: dc-arch-spell-feat-library-canon-api
- Expected: HTTP 200; all schema_data fields present; no hardcoded fallback data
- Roles covered: authenticated user
- Automation type: HTTP/API assertion

### TC-3.3 — /api/spells/{id} unknown ID returns 404
- Description: GET /api/spells/nonexistent-id returns 404, not a hardcoded fallback spell
- Suite: dc-arch-spell-feat-library-canon-api
- Expected: HTTP 404
- Roles covered: authenticated user
- Automation type: HTTP/API assertion

### TC-3.4 — /api/spells anonymous access blocked
- Description: GET /api/spells without authentication returns 403 or redirect to login
- Suite: dc-arch-spell-feat-library-canon-api
- Expected: HTTP 403 or 302
- Roles covered: anonymous
- Automation type: role-url-audit

### TC-3.5 — SpellCatalogService no longer contains hardcoded array (code audit)
- Description: SpellCatalogService.php does not contain a hardcoded spell definition array
- Suite: dc-arch-spell-feat-library-canon-code-audit
- Command: grep -n "SPELL_DATA" dungeoncrawler-pf2e/web/modules/custom/dungeoncrawler_content/src/Service/SpellCatalogService.php
- Expected: Zero matches for hardcoded definition arrays
- Roles covered: system/code
- Automation type: grep/static analysis
- Note to PM: Partial automation only until Phase 6 cleanup is complete; manual code review required for Phases 3-5.

### TC-3.6 — CharacterManager constants removed or aliased only (code audit)
- Description: CharacterManager.php ANCESTRY_FEATS, CLASS_FEATS, GENERAL_FEATS constants are removed or reduced to migration compatibility aliases
- Suite: dc-arch-spell-feat-library-canon-code-audit
- Command: grep -rn "ANCESTRY_FEATS\|CLASS_FEATS\|GENERAL_FEATS" dungeoncrawler-pf2e/web/modules/custom/
- Expected: Zero primary definition usages; only migration shim references remain
- Roles covered: system/code
- Automation type: grep/static analysis
- Note to PM: Full pass only verifiable at Phase 6; earlier phases verify partial reduction only.

### TC-3.7 — Feat sheet renders from library record (E2E)
- Description: Load a character sheet with feats; confirm feat name, prerequisites, and description render from registry-backed data
- Suite: dc-arch-spell-feat-library-canon-e2e
- Expected: Feat tooltip/sheet data matches registry record for sampled feat
- Roles covered: authenticated user (character owner)
- Automation type: Playwright E2E

### TC-3.8 — Spell tooltip renders from library record (E2E)
- Description: Load a character sheet; expand a spell; confirm tooltip data matches registry record
- Suite: dc-arch-spell-feat-library-canon-e2e
- Expected: Spell tooltip data matches registry record for sampled spell
- Roles covered: authenticated user
- Automation type: Playwright E2E

---

## Phase 4: Write-path normalization (AC-4)

### TC-4.1 — Character save does not write canonical definition text
- Description: Save a character with spells and feats; confirm character_data and state_data do not

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260506-000046-testgen-dc-arch-spell-feat-library-canon
- Generated: 2026-05-06T00:01:32+00:00
