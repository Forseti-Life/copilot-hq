# Forseti-Life Repository Issues & Work Items

**Generated:** 2026-04-21  
**Status:** Ready for team assignment

---

## Critical Issues (MUST FIX BEFORE RELEASE)

### #CRITICAL-1: forseti.life - Missing README & LICENSE
**Repository:** forseti.life  
**Status:** ⛔ CRITICAL  
**Impact:** Main platform repo has no orientation for new users or legal framework  

**Tasks:**
- [ ] Create comprehensive README.md explaining:
  - Platform overview and mission
  - Getting started guide
  - Architecture overview
  - Local development setup
  - Testing and deployment
- [ ] Add LICENSE file (recommend MIT or Apache 2.0)
- [ ] Add LICENSE reference to CONTRIBUTING.md

**Acceptance Criteria:**
- README is >500 words, covers all major topics
- LICENSE file matches organization standard
- README has table of contents and sections
- Repo displays properly on GitHub

---

### #CRITICAL-2: copilot-hq - Missing README
**Repository:** copilot-hq  
**Status:** ⛔ CRITICAL  
**Impact:** No documentation for governance/release cycle model  

**Tasks:**
- [ ] Create README.md explaining:
  - Organizational structure
  - Release cycle process
  - Gates and governance model
  - How to use orchestrator
  - Role definitions
- [ ] Add quick-start guide for new team leads
- [ ] Link to detailed documentation files

**Acceptance Criteria:**
- README is >400 words
- Explains org model and release cycles clearly
- Has "Quick Start" section
- Links to related docs in repo

---

## High Priority Issues (SHOULD COMPLETE)

### #HIGH-1: Add CONTRIBUTING.md to 17 Repos
**Status:** 📋 BULK WORK  
**Affected Repos:** forseti-devops, forseti-docs, forseti-mobile, forseti-meshd, h3-geolocation, forseti-agent-evaluation, forseti-amisafe, forseti-community-incident-report, forseti-company-research, forseti-content, forseti-copilot-agent-tracker, forseti-institutional-management, forseti-jobhunter-tester, forseti-nfr, forseti-safety-calculator, forseti-safety-content, dungeoncrawler-content  

**Pattern for Each Repo:**
- [ ] Create CONTRIBUTING.md with:
  - Development environment setup
  - Branch naming conventions
  - Commit message standards
  - Testing requirements
  - Pull request process
  - Code style guide
  - Issue reporting guidelines

**Acceptance Criteria:**
- Each repo has consistent CONTRIBUTING.md (can be template-based)
- Covers language-specific setup (PHP for Drupal, Python for Python projects, etc.)
- Links to CODE_OF_CONDUCT.md

---

### #HIGH-2: Add LICENSE to 3 Repos
**Status:** 📋 MISSING LICENSE  
**Affected Repos:** forseti-ai-conversation, dungeoncrawler-content, dungeoncrawler-tester

**Tasks for Each:**
- [ ] Add LICENSE file (match organization standard)
- [ ] Update repo description/README to reference license
- [ ] Add license badge to README

**Acceptance Criteria:**
- LICENSE file present in each repo
- License type matches organization standard
- Matches other Forseti repos

---

## Medium Priority Issues (IMPLEMENTATION WORK)

### #MED-1: forseti-agent-evaluation - Implement Agent Evaluation Framework
**Repository:** forseti-agent-evaluation  
**Status:** 🟡 EMPTY REPO  
**Description:** Copilot agent performance evaluation and telemetry  

**Required Implementation:**
- [ ] Create module structure:
  - src/Services/AgentEvaluator.php
  - src/Services/MetricsCollector.php
  - src/Controllers/EvaluationDashboard.php
- [ ] Implement metrics collection:
  - Task completion rate
  - Response time
  - Error rates
  - User satisfaction
- [ ] Create telemetry export (JSON/CSV)
- [ ] Add unit tests (PHPUnit)
- [ ] Update README with examples

**Acceptance Criteria:**
- Code is in src/ directory
- Has tests/ directory with >=80% coverage
- README shows usage examples
- Has contributing.md

---

### #MED-2: forseti-amisafe - AMISAFE Integration
**Repository:** forseti-amisafe  
**Status:** 🟡 EMPTY REPO  
**Description:** AMISAFE integration and incident classification  

**Required Implementation:**
- [ ] Create AMISAFE API client (src/AmiSafeClient.php)
- [ ] Implement incident classification logic
- [ ] Create Drupal hooks for incident submission
- [ ] Add configuration forms
- [ ] Write tests
- [ ] Document API integration

**Acceptance Criteria:**
- AMISAFE API integration functional
- Tests cover core workflows
- Configuration is documented
- README includes setup instructions

---

### #MED-3: forseti-community-incident-report - Incident Reporting Module
**Repository:** forseti-community-incident-report  
**Status:** 🟡 EMPTY REPO  
**Type:** Drupal Module

**Required Implementation:**
- [ ] Create incident_report.info.yml
- [ ] Implement incident submission form
- [ ] Create moderation queue UI
- [ ] Add notification workflow
- [ ] Implement status tracking
- [ ] Write tests

**Acceptance Criteria:**
- Module installs on Drupal 9.5+
- Can submit incidents via form
- Moderators can review/approve/reject
- README includes installation steps

---

### #MED-4: forseti-company-research - Company Research & BI Module
**Repository:** forseti-company-research  
**Status:** 🟡 EMPTY REPO  
**Description:** Company research, background checks, and BI  

**Required Implementation:**
- [ ] Design data model for company records
- [ ] Create external data source integrations (if applicable)
- [ ] Implement search functionality
- [ ] Add reporting/analytics views
- [ ] Create background check workflow
- [ ] Write tests and documentation

**Acceptance Criteria:**
- Can store company research data
- Search functional
- Reports generate correctly
- API documented

---

### #MED-5: forseti-content - Content Types & Editorial Workflow Module
**Repository:** forseti-content  
**Status:** 🟡 EMPTY REPO  
**Type:** Drupal Module

**Required Implementation:**
- [ ] Create content_types.info.yml
- [ ] Define custom content types
- [ ] Implement editorial workflow (draft → review → publish)
- [ ] Add taxonomy management
- [ ] Create access control rules
- [ ] Implement content versioning

**Acceptance Criteria:**
- Module installs cleanly
- Custom content types functional
- Workflow states enforced
- README documents content types

---

### #MED-6: forseti-institutional-management - Organization & Institution Management
**Repository:** forseti-institutional-management  
**Status:** 🟡 EMPTY REPO  
**Type:** Drupal Module

**Required Implementation:**
- [ ] Create institutional_management.info.yml
- [ ] Implement org/institution entity
- [ ] Create role hierarchy system
- [ ] Add permission matrix
- [ ] Implement org structure UI
- [ ] Write tests

**Acceptance Criteria:**
- Can create institutions
- Can define roles and permissions
- Can manage user org assignments
- API documented

---

### #MED-7: forseti-jobhunter-tester - Job Hunter Test Utilities
**Repository:** forseti-jobhunter-tester  
**Status:** 🟡 EMPTY REPO  
**Description:** Testing utilities for job_hunter module

**Required Implementation:**
- [ ] Create test fixtures (sample jobs, profiles, etc.)
- [ ] Implement test helpers for common scenarios
- [ ] Create performance testing utilities
- [ ] Add integration test helpers
- [ ] Document usage patterns

**Acceptance Criteria:**
- Tests use fixtures
- Helpers reduce test boilerplate
- Documentation with examples
- Utilities reusable across test suite

---

### #MED-8: forseti-nfr - Non-Functional Requirements Tracking
**Repository:** forseti-nfr  
**Status:** 🟡 EMPTY REPO  
**Type:** Drupal Module

**Required Implementation:**
- [ ] Create nfr.info.yml
- [ ] Implement NFR entity with properties (performance, security, availability, etc.)
- [ ] Create compliance tracking
- [ ] Add reporting dashboards
- [ ] Implement metric collection
- [ ] Write tests

**Acceptance Criteria:**
- Can track NFR requirements
- Compliance metrics displayed
- Reports generate correctly
- Documentation complete

---

### #MED-9: forseti-safety-calculator - Safety Scoring Algorithm
**Repository:** forseti-safety-calculator  
**Status:** 🟡 EMPTY REPO  
**Description:** Risk assessment and safety score calculations

**Required Implementation:**
- [ ] Design safety scoring algorithm
- [ ] Create factor analysis system
- [ ] Implement risk assessment engine
- [ ] Add weighting system for different risk factors
- [ ] Create visualization utilities
- [ ] Write comprehensive tests

**Acceptance Criteria:**
- Algorithm documented with examples
- Produces scores 0-100
- Handles edge cases
- Tests verify scoring logic
- README explains methodology

---

### #MED-10: forseti-safety-content - Safety Content Types & Incident Tracking
**Repository:** forseti-safety-content  
**Status:** 🟡 EMPTY REPO  
**Type:** Drupal Module

**Required Implementation:**
- [ ] Create safety_content.info.yml
- [ ] Define safety incident content type
- [ ] Create incident tracking workflow
- [ ] Implement categorization system
- [ ] Add search/filter functionality
- [ ] Create reporting views

**Acceptance Criteria:**
- Module installs cleanly
- Can create/track incidents
- Search and filtering work
- Reports are accessible
- Documentation complete

---

## Low Priority Issues (NICE TO HAVE)

### #LOW-1: dungeoncrawler-content - PF2E Game Data Repository
**Repository:** dungeoncrawler-content  
**Status:** ⚠️ STUB  
**Description:** Structured Pathfinder 2E rules data  

**Suggested Implementation:**
- Create comprehensive game data structure
- Spell lists, feats, classes, items
- Rules references
- JSON or YAML data format
- Documentation and examples

**Note:** Can start after core modules are complete

---

### #LOW-2: dungeoncrawler-tester - DungeonCrawler Test Utilities
**Repository:** dungeoncrawler-tester  
**Status:** ⚠️ STUB  
**Required First:** Add LICENSE, README, CONTRIBUTING

**Suggested Implementation:**
- Test fixtures for D&D campaigns
- Character generation utilities
- Combat simulation helpers

---

### #LOW-3: forseti-cluster - Cluster Management & Orchestration
**Repository:** forseti-cluster  
**Status:** ⚠️ STUB  
**Required First:** Add README, LICENSE, CONTRIBUTING

**Suggested Implementation:**
- Kubernetes/Docker orchestration
- Service mesh integration
- Load balancing configuration
- Scaling policies

---

### #LOW-4: forseti-ai-conversation - AI Conversation Module (INCOMPLETE)
**Repository:** forseti-ai-conversation  
**Status:** 🟡 PARTIAL (has README only)
**Missing:** LICENSE, CONTRIBUTING.md, module code

**Required Completion:**
- [ ] Add LICENSE file
- [ ] Add CONTRIBUTING.md
- [ ] Implement AI conversation service
- [ ] Add LLM integration (OpenAI, Anthropic, etc.)
- [ ] Create conversation history storage
- [ ] Write tests and documentation

---

### #LOW-5: forseti-platform-specs - Expand Specifications
**Repository:** forseti-platform-specs  
**Status:** 📄 MINIMAL  
**Current:** README + LICENSE only

**Suggested Expansion:**
- API endpoint documentation (OpenAPI/Swagger)
- Architecture diagrams
- Database schema specifications
- Integration patterns
- Security specifications

---

### #LOW-6: forseti-shared-modules - Expand Shared Module Library
**Repository:** forseti-shared-modules  
**Status:** 📦 MINIMAL  
**Current:** Basic structure with modules/ directory

**Suggested Expansion:**
- Reusable ORM modules
- Authentication helpers
- Content pattern libraries
- UI component library
- Form builders
- Well-documented examples

---

## Summary by Assignee Type

### For Product Managers:
- Review #CRITICAL-1 and #CRITICAL-2 - ensure readability for stakeholders
- Prioritize Medium-priority issues (#MED-1 through #MED-10)
- Schedule implementation across quarters

### For Developers:
- High-priority: Add CONTRIBUTING.md to 17 repos (#HIGH-1)
- High-priority: Add LICENSE to 3 repos (#HIGH-2)
- Medium-priority: Implement one of the empty modules (#MED-1 through #MED-10)

### For Tech Lead:
- Review critical README/LICENSE issues first (#CRITICAL-1, #CRITICAL-2)
- Ensure template consistency for CONTRIBUTING.md
- Coordinate module implementation roadmap

### For DevOps:
- Focus on forseti-devops CONTRIBUTING.md (#HIGH-1)
- Assist with deployment/infra specs in forseti-platform-specs (#LOW-5)
- Help with forseti-cluster implementation (#LOW-3)

---

## Timeline Estimate

| Phase | Items | Duration | Priority |
|-------|-------|----------|----------|
| Phase 1: Critical | #CRITICAL-1, #CRITICAL-2 | 1 week | MUST |
| Phase 2: Documentation | #HIGH-1, #HIGH-2 | 2 weeks | SHOULD |
| Phase 3: Core Implementation | #MED-1 through #MED-10 | 6-8 weeks | SHOULD |
| Phase 4: Enhancement | #LOW-1 through #LOW-6 | Ongoing | NICE |

---

## Verification Checklist

Use this to verify repo quality before marking issues complete:

- [ ] README.md exists and is comprehensive
- [ ] LICENSE file present and matches org standard
- [ ] CONTRIBUTING.md complete with dev setup
- [ ] CODE_OF_CONDUCT.md present
- [ ] Source code in appropriate directories (src/, lib/, etc.)
- [ ] Tests present with >70% coverage
- [ ] No debug code or commented-out blocks
- [ ] Secrets not committed (no .env files with values)
- [ ] Dependencies documented (requirements.txt, composer.json, package.json)
- [ ] GitHub Actions/CI configured if needed

