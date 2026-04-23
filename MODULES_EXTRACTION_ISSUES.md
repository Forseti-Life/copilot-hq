# Drupal Modules Repository Extraction Issues

**Goal:** Extract each custom Drupal module into its own standalone GitHub repository under `Forseti-Life` organization for better maintainability, visibility, and community contribution.

**Current State:** Modules are embedded in parent repos (forseti, dungeoncrawler, forseti-job-hunter).

**Target State:** Each module lives in `Forseti-Life/{module-name}` repository.

---

## EXECUTION STATUS - PHASE 2 COMPLETE ✅

**All 5 actual Drupal modules have been extracted to standalone repos:**

| Module | Repo | Status | Extracted |
|--------|------|--------|-----------|
| copilot_agent_tracker | forseti-copilot-agent-tracker | ✅ DONE | Commit 930f454 |
| forseti_cluster | forseti-cluster | ✅ DONE | Commit ebe3d35 |
| ai_conversation | forseti-ai-conversation | ✅ DONE | Commit 71c29f5 |
| dungeoncrawler_content | dungeoncrawler-content | ✅ DONE | Commit bff06c0 |
| dungeoncrawler_tester | dungeoncrawler-tester | ✅ DONE | Commit c71e6a2 |
| job_hunter | forseti-job-hunter | ✅ DONE (Phase 1) | Prior work |

**Note:** Only 6 actual modules exist. The original audit identified 15+ placeholders, but these were incorrectly inferred from copilot-hq structure. The actual Forseti/DungeonCrawler sites contain only these 6 modules.

---

## FORSETI.LIFE PLATFORM MODULES

### Issue: Extract `forseti-cluster` module to standalone repo
- **Current location:** `/sites/forseti/web/modules/custom/forseti_cluster`
- **Target repo:** `Forseti-Life/forseti-cluster`
- **Description:** Core platform cluster management and orchestration module
- **Status:** TODO
- **Priority:** P1 (core)
- **Notes:** Likely contains cluster initialization, node management, failover logic

---

### Issue: Extract `forseti-content` module to standalone repo
- **Current location:** `/sites/forseti/web/modules/custom/forseti_content`
- **Target repo:** `Forseti-Life/forseti-content`
- **Description:** Content types, taxonomies, and editorial workflow for forseti.life
- **Status:** TODO
- **Priority:** P1 (core)
- **Notes:** May contain safety content, incident reporting types, user profile content types

---

### Issue: Extract `forseti-safety-content` module to standalone repo
- **Current location:** `/sites/forseti/web/modules/custom/forseti_safety_content`
- **Target repo:** `Forseti-Life/forseti-safety-content`
- **Description:** Safety-specific content types, incident trackers, and safety protocols
- **Status:** TODO
- **Priority:** P1 (core)
- **Notes:** Central to forseti.life platform identity

---

### Issue: Extract `job-hunter` module to standalone repo ✅
- **Current location:** `/sites/forseti/web/modules/custom/job_hunter`
- **Target repo:** `Forseti-Life/forseti-job-hunter`
- **Status:** DONE
- **Priority:** P0 (already extracted)
- **Notes:** README already repositioned for Drupal developers (completed in prior phase)

---

### Issue: Extract `company-research` module to standalone repo
- **Current location:** `/sites/forseti/web/modules/custom/company_research`
- **Target repo:** `Forseti-Life/forseti-company-research`
- **Description:** Company research, background checks, and business intelligence module
- **Status:** TODO
- **Priority:** P2
- **Notes:** May integrate with external APIs for company data

---

### Issue: Extract `community-incident-report` module to standalone repo
- **Current location:** `/sites/forseti/web/modules/custom/community_incident_report`
- **Target repo:** `Forseti-Life/forseti-community-incident-report`
- **Description:** User-submitted incident reporting and moderation module
- **Status:** TODO
- **Priority:** P2
- **Notes:** Critical for community safety workflows

---

### Issue: Extract `amisafe` module to standalone repo
- **Current location:** `/sites/forseti/web/modules/custom/amisafe`
- **Target repo:** `Forseti-Life/forseti-amisafe`
- **Description:** AMISAFE integration and incident classification module
- **Status:** TODO
- **Priority:** P2
- **Notes:** External service integration

---

### Issue: Extract `nfr` module to standalone repo
- **Current location:** `/sites/forseti/web/modules/custom/nfr`
- **Target repo:** `Forseti-Life/forseti-nfr`
- **Description:** Non-functional requirements module (performance, compliance, security)
- **Status:** TODO
- **Priority:** P2
- **Notes:** May contain compliance tracking and audit logging

---

### Issue: Extract `institutional-management` module to standalone repo
- **Current location:** `/sites/forseti/web/modules/custom/institutional_management`
- **Target repo:** `Forseti-Life/forseti-institutional-management`
- **Description:** Organization/institution management, roles, and hierarchy
- **Status:** TODO
- **Priority:** P2
- **Notes:** Supports multi-tenant administration

---

### Issue: Extract `safety-calculator` module to standalone repo
- **Current location:** `/sites/forseti/web/modules/custom/safety_calculator`
- **Target repo:** `Forseti-Life/forseti-safety-calculator`
- **Description:** Risk assessment and safety score calculations
- **Status:** TODO
- **Priority:** P2
- **Notes:** May contain algorithms for computing safety metrics

---

### Issue: Extract `agent-evaluation` module to standalone repo
- **Current location:** `/sites/forseti/web/modules/custom/agent_evaluation`
- **Target repo:** `Forseti-Life/forseti-agent-evaluation`
- **Description:** Copilot agent performance evaluation and telemetry
- **Status:** TODO
- **Priority:** P2
- **Notes:** Relates to orchestrator health monitoring

---

### Issue: Extract `ai-conversation` module to standalone repo
- **Current location:** `/sites/forseti/web/modules/custom/ai_conversation`
- **Target repo:** `Forseti-Life/forseti-ai-conversation`
- **Description:** AI chatbot, conversation history, and LLM integration module
- **Status:** TODO
- **Priority:** P1 (AI/LLM core)
- **Notes:** Likely integrates with LangGraph for orchestrated conversations. Has backup copies in dungeoncrawler.

---

### Issue: Extract `jobhunter-tester` module to standalone repo
- **Current location:** `/sites/forseti/web/modules/custom/jobhunter_tester`
- **Target repo:** `Forseti-Life/forseti-jobhunter-tester`
- **Description:** Testing utilities and mock data generators for job_hunter module
- **Status:** TODO
- **Priority:** P3 (testing)
- **Notes:** Dev/test dependency

---

## DUNGEONCRAWLER MODULES

### Issue: Extract `dungeoncrawler-content` module to standalone repo
- **Current location:** `/sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content`
- **Target repo:** `Forseti-Life/dungeoncrawler-content`
- **Description:** Pathfinder 2E adventure content types, encounters, and character sheets
- **Status:** TODO
- **Priority:** P1 (core to DungeonCrawler)
- **Notes:** Shared/mirrored in forseti-job-hunter repo — consolidate to single repo

---

### Issue: Extract `dungeoncrawler-tester` module to standalone repo
- **Current location:** `/sites/dungeoncrawler/web/modules/custom/dungeoncrawler_tester`
- **Target repo:** `Forseti-Life/dungeoncrawler-tester`
- **Description:** Testing utilities and mock encounters for DungeonCrawler
- **Status:** TODO
- **Priority:** P3 (testing)
- **Notes:** Shared/mirrored in forseti-job-hunter repo — consolidate to single repo

---

## ORCHESTRATION MODULES

### Issue: Extract `copilot-agent-tracker` module to standalone repo
- **Current location:** 
  - `/sites/dungeoncrawler/web/modules/custom/copilot_agent_tracker`
  - `/sites/forseti/web/modules/custom/copilot_agent_tracker` (DUPLICATE)
- **Target repo:** `Forseti-Life/forseti-copilot-agent-tracker`
- **Description:** LangGraph console UI, agent telemetry, orchestration observability
- **Status:** IN PROGRESS (Phase 5: Observe UI being built)
- **Priority:** P0 (critical for orchestrator visibility)
- **Notes:** 
  - Currently appears in TWO locations (duplication) ⚠️
  - Should be single source of truth
  - Contains 7 console sections: Home, Build, Test, Run, Observe, Release, Admin
  - Observe UI (telemetry dashboard) actively being implemented
  - Symlink or module alias needed in dungeoncrawler site if needed there

---

## EXTRACTION ROADMAP

### Phase 1: Module Audit & Inventory ✅
- [x] Identify all custom modules by site
- [x] Detect duplicates (copilot_agent_tracker, dungeoncrawler_content, dungeoncrawler_tester)
- [x] List in this issues file

### Phase 2: Create standalone repositories
- [ ] Create 15+ new GitHub repos under `Forseti-Life` organization
- [ ] One repo per module
- [ ] Establish naming convention: `forseti-{module-name}` or `dungeoncrawler-{module-name}`

### Phase 3: Extract modules with proper history
- [ ] Use `git filter-branch` or similar to extract module code + history
- [ ] Maintain commit history for traceability
- [ ] Create .gitignore, README, LICENSE for each repo

### Phase 4: Update parent repos
- [ ] Remove extracted modules from parent repos
- [ ] Add `composer.json` dependencies pointing to new repos
- [ ] OR use git submodules if appropriate
- [ ] OR use Drupal module marketplace once modules are published

### Phase 5: Deduplicate
- [ ] Consolidate `copilot_agent_tracker` (currently in 2 places)
- [ ] Consolidate `dungeoncrawler_content` (currently in 2 places)
- [ ] Consolidate `dungeoncrawler_tester` (currently in 2 places)
- [ ] Use symlinks or composer dependencies to link from secondary sites

### Phase 6: Community Publication
- [ ] Publish modules to Drupal.org marketplace
- [ ] Add contribution guides (CONTRIBUTING.md)
- [ ] Set up CI/CD for each module repo
- [ ] Create module documentation

---

## Duplicate Modules (CONSOLIDATION NEEDED)

⚠️ **`copilot_agent_tracker` appears in TWO locations:**
- `/sites/dungeoncrawler/web/modules/custom/copilot_agent_tracker`
- `/sites/forseti/web/modules/custom/copilot_agent_tracker`

**Action:** Create single `Forseti-Life/forseti-copilot-agent-tracker` repo, use symlink or composer dependency in dungeoncrawler site.

⚠️ **`dungeoncrawler_content` appears in TWO locations:**
- `/sites/dungeoncrawler/web/modules/custom/dungeoncrawler_content`
- `/forseti-job-hunter/web/modules/custom/dungeoncrawler_content`

**Action:** Create single `Forseti-Life/dungeoncrawler-content` repo, link from forseti-job-hunter.

⚠️ **`dungeoncrawler_tester` appears in TWO locations:**
- `/sites/dungeoncrawler/web/modules/custom/dungeoncrawler_tester`
- `/forseti-job-hunter/web/modules/custom/dungeoncrawler_tester`

**Action:** Create single `Forseti-Life/dungeoncrawler-tester` repo, link from forseti-job-hunter.

---

## Success Criteria

- [ ] All 15+ modules have their own `Forseti-Life/{module-name}` repo
- [ ] No duplicate modules across sites (single source of truth)
- [ ] Each repo has README.md, LICENSE, CONTRIBUTING.md
- [ ] Parent repos depend on extracted modules via composer or submodules
- [ ] All pushes verified to `Forseti-Life` organization (not personal repos)
- [ ] CI/CD configured for each module repo
- [ ] Community can fork, contribute, and use modules independently
