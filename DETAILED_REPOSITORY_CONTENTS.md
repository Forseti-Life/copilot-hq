# Detailed Repository Contents Inventory

**Generated:** 2026-04-21 17:02 UTC  
**Scope:** All 12 Forseti-Life repositories + Private monorepo

---

## 1. FORSETI.LIFE (Private Monorepo - Source of Truth)

**Location:** `/home/ubuntu/forseti.life`  
**Owner:** keithaumiller (private account)  
**Purpose:** Operational deployment source, development hub  
**Branch:** main

### Top-Level Structure

```
forseti.life/
├── orchestrator/              ← ORCHESTRATOR (Organizational governance)
│   ├── run.py                (Main orchestrator script)
│   ├── dispatch.py           (Dispatch logic)
│   ├── .venv/                (Python virtual environment)
│   ├── requirements.txt
│   ├── alembic/              (Database migrations)
│   └── ... (orchestrator code)
│
├── copilot-hq/               ← SPLIT REPO 1 (Governance & release cycles)
│   └── (synced to Forseti-Life/copilot-hq)
│
├── sites/                    ← DRUPAL SITES (Production web applications)
│   ├── forseti/              ← Job Hunter Drupal site
│   │   ├── web/
│   │   │   ├── index.php     (Drupal entry point)
│   │   │   ├── modules/      (Custom Drupal modules)
│   │   │   ├── themes/       (Custom themes)
│   │   │   ├── sites/        (Site configuration)
│   │   │   └── ...
│   │   └── ...
│   │
│   └── dungeoncrawler/       ← D&D Campaign Drupal site
│       ├── web/
│       │   ├── index.php     (Drupal entry point)
│       │   ├── modules/      (Custom Drupal modules)
│       │   ├── themes/       (Custom themes)
│       │   ├── sites/        (Site configuration)
│       │   └── ...
│       └── ...
│
├── shared/                   ← SHARED DRUPAL MODULES (Utilities)
│   └── modules/              (Reusable modules for both sites)
│       ├── ai_conversation/  (AI conversation entity)
│       ├── job_search/       (Job search utilities)
│       ├── authentication/   (Auth utilities)
│       └── ...
│
├── forseti-job-hunter/       ← SPLIT REPO 2 (24,959 files)
│   └── (Job Hunter Drupal module - synced to Forseti-Life/forseti-job-hunter)
│
├── dungeoncrawler-pf2e/      ← SPLIT REPO 3 (28,926 files)
│   └── (D&D Campaign Drupal module - synced to Forseti-Life/dungeoncrawler-pf2e)
│
├── forseti-shared-modules/   ← SPLIT REPO 4 (Shared utilities)
│   └── (Synced to Forseti-Life/forseti-shared-modules)
│
├── forseti-devops/           ← SPLIT REPO 5 (DevOps automation)
│   └── (Synced to Forseti-Life/forseti-devops)
│
├── forseti-docs/             ← SPLIT REPO 6 (Documentation)
│   └── (Synced to Forseti-Life/forseti-docs)
│
├── forseti-meshd/            ← SPLIT REPO 7 (Mesh daemon)
│   ├── main.py               (FastAPI service)
│   ├── models.py             (Database models)
│   ├── routers/              (API endpoints)
│   ├── services/             (Business logic)
│   ├── alembic/              (DB migrations)
│   └── requirements.txt
│
├── forseti-mobile/           ← SPLIT REPO 8 (Mobile apps)
│   ├── ios/                  (iOS app code)
│   ├── android/              (Android app code)
│   ├── shared/               (Shared React Native code)
│   └── ...
│
├── h3-geolocation/           ← SPLIT REPO 9 (Geospatial library)
│   ├── h3_integration.py     (H3 library wrapper)
│   ├── geospatial_utils.py   (Utilities)
│   └── ...
│
├── dungeoncrawler-content/   ← SPLIT REPO 10 (Game data)
│   ├── rules/                (PF2E rules data)
│   ├── content/              (Game content)
│   └── json/                 (Reference data)
│
├── forseti-platform-specs/   ← SPLIT REPO 11 (Architecture specs)
│   ├── api-spec/             (API specifications)
│   ├── architecture/         (System design)
│   └── design-docs/          (Design documents)
│
├── script/                   ← DEPLOYMENT SCRIPTS
│   ├── verify-setup.sh       (Setup verification)
│   ├── deploy.sh             (Deployment script)
│   └── ...
│
├── prod-config/              ← PRODUCTION CREDENTIALS (Never public)
│   ├── database.yml          (DB credentials)
│   ├── secrets.env           (API keys, secrets)
│   └── ...
│
├── orchestrator/             ← ORCHESTRATOR LOGIC
│   ├── run.py                (Main execution)
│   ├── dispatch.py           (Command dispatcher)
│   └── ...
│
├── .github/workflows/        ← GITHUB ACTIONS
│   ├── deploy.yml            (Deployment workflow)
│   └── ...
│
├── org-chart/                ← ORGANIZATIONAL STRUCTURE
│   ├── org-wide.instructions.md
│   ├── roles/                (Role definitions)
│   ├── agents/               (Agent definitions)
│   └── DECISION_OWNERSHIP_MATRIX.md
│
├── sessions/                 ← SESSION STATE (CEO work sessions)
│   └── ceo-copilot-2/        (Current session artifacts)
│       ├── inbox/            (Pending work)
│       ├── outbox/           (Completed work)
│       └── artifacts/        (Session outputs)
│
├── knowledgebase/            ← LESSONS LEARNED
│   ├── lessons/              (KB articles)
│   └── ...
│
├── README.md                 ← MAIN DOCUMENTATION
├── REPOSITORY_ARCHITECTURE.md
├── ENVIRONMENT_ALIGNMENT_REPORT.md
├── COMPLETE_REPO_INVENTORY.md
├── LIVE_INTEGRATION_REPORT.md
└── ... (other documentation)
```

### Key Files in Monorepo

| Path | Purpose | Status |
|------|---------|--------|
| orchestrator/run.py | Main orchestration engine | ACTIVE |
| sites/forseti/ | Job Hunter Drupal site | LIVE |
| sites/dungeoncrawler/ | D&D Campaign Drupal site | LIVE |
| shared/modules/ | Shared utilities | ACTIVE |
| prod-config/ | Production credentials | ACTIVE |
| .github/workflows/deploy.yml | GitHub Actions deployment | READY |

---

## 2. FORSETI-JOB-HUNTER (Split Repo - Tier 1 Core Product)

**Location:** `/home/ubuntu/forseti.life/forseti-job-hunter/`  
**GitHub:** Forseti-Life/forseti-job-hunter  
**Commit:** 9c96004f  
**Files:** 24,959  
**Purpose:** Job search platform - Drupal module  
**Status:** ✅ LIVE (synced to sites/forseti/)

### Contents

```
forseti-job-hunter/
├── .git/                          (Git repository)
├── web/                           (Drupal site)
│   ├── index.php                  (Entry point)
│   ├── core/                      (Drupal core)
│   ├── modules/
│   │   ├── custom/
│   │   │   ├── job_search/        (Job search module)
│   │   │   ├── ai_matching/       (AI job matching)
│   │   │   ├── resume_builder/    (Resume generation)
│   │   │   ├── application_tracking/  (ATS integration)
│   │   │   └── ...
│   │   └── contrib/               (Contributed modules)
│   ├── themes/                    (Drupal themes)
│   ├── sites/
│   │   └── default/
│   │       ├── settings.php       (Site config)
│   │       ├── files/             (Uploaded files)
│   │       └── services.yml       (Services config)
│   └── ...
├── composer.json                  (PHP dependencies)
├── composer.lock
├── README.md
└── ...
```

### Key Modules

| Module | Purpose | Status |
|--------|---------|--------|
| job_search | Core job search functionality | ACTIVE |
| ai_matching | GenAI-powered job matching | ACTIVE |
| resume_builder | AI-powered resume generation | ACTIVE |
| application_tracking | ATS integration & automation | ACTIVE |
| community_features | Community interaction | ACTIVE |

---

## 3. DUNGEONCRAWLER-PF2E (Split Repo - Tier 1 Core Product)

**Location:** `/home/ubuntu/forseti.life/dungeoncrawler-pf2e/`  
**GitHub:** Forseti-Life/dungeoncrawler-pf2e  
**Commit:** dd1148a2  
**Files:** 28,926  
**Purpose:** Pathfinder 2E campaign management system  
**Status:** ✅ LIVE (synced to sites/dungeoncrawler/)

### Contents

```
dungeoncrawler-pf2e/
├── .git/                          (Git repository)
├── web/                           (Drupal site)
│   ├── index.php                  (Entry point)
│   ├── core/                      (Drupal core)
│   ├── modules/
│   │   ├── custom/
│   │   │   ├── dungeoncrawler_pf2e/  (Main module)
│   │   │   ├── character_builder/    (Character creation)
│   │   │   ├── combat_system/        (Combat rules)
│   │   │   ├── encounter_designer/   (Encounter creation)
│   │   │   ├── spell_library/        (Spell database)
│   │   │   ├── inventory_management/ (Item management)
│   │   │   └── ...
│   │   └── contrib/               (Contributed modules)
│   ├── themes/                    (Drupal themes)
│   ├── sites/
│   │   └── default/
│   │       ├── settings.php       (Site config)
│   │       ├── files/             (Uploaded files)
│   │       └── services.yml       (Services config)
│   └── ...
├── composer.json                  (PHP dependencies)
├── composer.lock
├── README.md
└── ...
```

### Key Modules

| Module | Purpose | Status |
|--------|---------|--------|
| character_builder | PF2E character creation | ACTIVE |
| combat_system | Turn-based combat engine | ACTIVE |
| encounter_designer | Encounter creation & management | ACTIVE |
| spell_library | PF2E spells & effects | ACTIVE |
| inventory_management | Item & equipment tracking | ACTIVE |
| party_management | Party coordination | ACTIVE |

---

## 4. FORSETI-SHARED-MODULES (Split Repo - Tier 2 Library)

**Location:** `/home/ubuntu/forseti.life/forseti-shared-modules/`  
**GitHub:** Forseti-Life/forseti-shared-modules  
**Files:** Small core utilities  
**Purpose:** Reusable Drupal modules  
**Status:** ✅ INTEGRATED (used by both job-hunter and dungeoncrawler)

### Contents

```
forseti-shared-modules/
├── .git/                          (Git repository)
├── modules/
│   ├── ai_conversation_entity/    (AI conversation tracking)
│   ├── orm_patterns/              (ORM utilities)
│   ├── authentication_utils/      (Auth helpers)
│   ├── content_patterns/          (Content modeling)
│   ├── ui_components/             (Reusable UI)
│   └── ...
├── composer.json
├── README.md
└── ...
```

### Purpose

Provides shared functionality across all Drupal sites:
- ORM patterns for database queries
- Authentication and user management
- Content entity patterns
- UI component library
- Common utilities

---

## 5. COPILOT-HQ (Split Repo - Tier 3 Operations)

**Location:** `/home/ubuntu/forseti.life/copilot-hq/`  
**GitHub:** Forseti-Life/copilot-hq  
**Files:** Organizational governance  
**Purpose:** Organizational operations model, release cycles  
**Status:** ✅ ORCHESTRATOR (paused, ready to resume)

### Contents

```
copilot-hq/
├── .git/                          (Git repository)
├── orchestrator/                  (Orchestrator code)
│   ├── run.py                     (Main execution)
│   ├── dispatch.py                (Command dispatcher)
│   ├── .venv/                     (Python environment)
│   └── ...
├── org-chart/                     (Organization structure)
│   ├── org-wide.instructions.md   (Company rules)
│   ├── roles/                     (Role definitions)
│   │   ├── ceo.instructions.md
│   │   ├── pm.instructions.md
│   │   └── ...
│   ├── agents/                    (Agent definitions)
│   │   ├── agent-pm-forseti.yaml
│   │   ├── agent-dev-forseti.yaml
│   │   └── ...
│   └── DECISION_OWNERSHIP_MATRIX.md
├── sessions/                      (Session tracking)
│   └── ceo-copilot-2/             (Current CEO session)
│       ├── inbox/                 (Pending work)
│       ├── outbox/                (Completed work)
│       └── artifacts/             (Session outputs)
├── README.md
└── ...
```

### Key Components

| Component | Purpose | Status |
|-----------|---------|--------|
| orchestrator/ | Release cycle engine | CORE |
| org-chart/ | Organizational structure | REFERENCE |
| sessions/ | Session state & work tracking | ACTIVE |
| agents/ | Agent definitions | ACTIVE |

---

## 6. FORSETI-DEVOPS (Split Repo - Tier 3 Operations)

**Location:** `/home/ubuntu/forseti.life/forseti-devops/`  
**GitHub:** Forseti-Life/forseti-devops  
**Purpose:** DevOps automation, deployment scripts  
**Status:** ✅ ACTIVE (used by GitHub Actions)

### Contents

```
forseti-devops/
├── .git/                          (Git repository)
├── ansible/                       (Infrastructure automation)
│   ├── playbooks/
│   │   ├── deploy.yml
│   │   ├── provision.yml
│   │   └── ...
│   └── roles/
├── docker/                        (Container definitions)
│   ├── Dockerfile.drupal
│   ├── Dockerfile.orchestrator
│   └── docker-compose.yml
├── scripts/
│   ├── deploy.sh                  (Deployment automation)
│   ├── post-coordinated-push.sh   (Post-push actions)
│   ├── verify-setup.sh
│   └── ...
├── terraform/                     (Infrastructure as Code)
│   ├── main.tf
│   ├── variables.tf
│   └── ...
├── .github/workflows/
│   └── deploy.yml                 (GitHub Actions)
├── README.md
└── ...
```

### Key Scripts

| Script | Purpose | Used By |
|--------|---------|---------|
| deploy.sh | Deployment automation | GitHub Actions |
| post-coordinated-push.sh | Release cycle advancement | Orchestrator |
| verify-setup.sh | Environment verification | Setup |

---

## 7. FORSETI-DOCS (Split Repo - Tier 3 Operations)

**Location:** `/home/ubuntu/forseti.life/forseti-docs/`  
**GitHub:** Forseti-Life/forseti-docs  
**Purpose:** Central documentation hub  
**Status:** ✅ REFERENCE (team documentation)

### Contents

```
forseti-docs/
├── .git/                          (Git repository)
├── docs/
│   ├── getting-started/           (Onboarding guides)
│   ├── api/                       (API documentation)
│   ├── architecture/              (System design)
│   ├── deployment/                (Deployment guides)
│   ├── troubleshooting/           (Help & issues)
│   └── ...
├── README.md
└── ...
```

### Documentation Sections

| Section | Content | Audience |
|---------|---------|----------|
| getting-started/ | Onboarding & setup | New developers |
| api/ | API reference | Developers |
| architecture/ | System design | Tech leads |
| deployment/ | How to deploy | DevOps |
| troubleshooting/ | Issues & solutions | All |

---

## 8. FORSETI-MESHD (Split Repo - Tier 2 Library)

**Location:** `/home/ubuntu/forseti.life/forseti-meshd/`  
**GitHub:** Forseti-Life/forseti-meshd  
**Language:** Python (FastAPI)  
**Purpose:** Mesh network daemon for P2P communication  
**Status:** ✅ READY (service deployed)

### Contents

```
forseti-meshd/
├── .git/                          (Git repository)
├── main.py                        (FastAPI entry point)
├── models.py                      (Database models)
├── routers/                       (API endpoints)
│   ├── mesh.py                    (Mesh network routes)
│   ├── peer.py                    (Peer management)
│   └── ...
├── services/                      (Business logic)
│   ├── mesh_service.py
│   ├── peer_service.py
│   └── ...
├── alembic/                       (Database migrations)
├── config.py                      (Configuration)
├── database.py                    (DB setup)
├── requirements.txt               (Python dependencies)
├── README.md
└── ...
```

### Purpose

Provides:
- P2P mesh network communication
- Peer discovery and management
- Distributed message routing
- Network resilience

---

## 9. FORSETI-MOBILE (Split Repo - Tier 2 Library)

**Location:** `/home/ubuntu/forseti.life/forseti-mobile/`  
**GitHub:** Forseti-Life/forseti-mobile  
**Language:** TypeScript (React Native)  
**Purpose:** iOS/Android native mobile applications  
**Status:** ✅ READY (apps ready to deploy)

### Contents

```
forseti-mobile/
├── .git/                          (Git repository)
├── ios/                           (iOS app)
│   ├── Podfile
│   ├── forseti-mobile.xcodeproj/
│   └── ... (Xcode project)
├── android/                       (Android app)
│   ├── build.gradle
│   ├── app/
│   └── ... (Android project)
├── shared/                        (Shared React Native code)
│   ├── screens/                   (UI screens)
│   ├── components/                (Reusable components)
│   ├── services/                  (API integration)
│   └── ...
├── package.json                   (NPM dependencies)
├── yarn.lock
├── README.md
└── ...
```

### Purpose

Provides:
- iOS native app
- Android native app
- Shared business logic
- Job search on mobile
- D&D campaign on mobile

---

## 10. H3-GEOLOCATION (Split Repo - Tier 2 Library)

**Location:** `/home/ubuntu/forseti.life/h3-geolocation/`  
**GitHub:** Forseti-Life/h3-geolocation  
**Language:** Python  
**Purpose:** H3 hexagon grid integration for geospatial features  
**Status:** ✅ READY (integration library)

### Contents

```
h3-geolocation/
├── .git/                          (Git repository)
├── h3_integration.py              (H3 wrapper)
├── geospatial_utils.py            (Utilities)
├── location_services.py           (Location features)
├── mapping.py                     (Map integration)
├── requirements.txt               (Python dependencies)
├── tests/                         (Unit tests)
└── README.md
```

### Purpose

Provides:
- H3 hexagon grid integration
- Location-based queries
- Geospatial analytics
- Map visualization
- Distance calculations

---

## 11. DUNGEONCRAWLER-CONTENT (Split Repo - Tier 4 Reference)

**Location:** `/home/ubuntu/forseti.life/dungeoncrawler-content/`  
**GitHub:** Forseti-Life/dungeoncrawler-content  
**Purpose:** Pathfinder 2E rules data and game content  
**Status:** ✅ REFERENCE (data/content)

### Contents

```
dungeoncrawler-content/
├── .git/                          (Git repository)
├── rules/                         (PF2E rule data)
│   ├── ancestry.json              (Character ancestries)
│   ├── background.json            (Character backgrounds)
│   ├── class.json                 (Character classes)
│   ├── spells.json                (Spell database)
│   ├── feats.json                 (Feats & abilities)
│   ├── items.json                 (Equipment)
│   └── ...
├── content/                       (Game content)
│   ├── adventures/                (Pre-made adventures)
│   ├── monsters/                  (Monster statblocks)
│   ├── encounters/                (Pre-made encounters)
│   └── ...
├── json/                          (Reference data)
│   └── (processed game data)
├── README.md
└── ...
```

### Purpose

Provides:
- PF2E character options
- Monster statblocks
- Spell reference
- Equipment database
- Rules reference

---

## 12. FORSETI-PLATFORM-SPECS (Split Repo - Tier 4 Reference)

**Location:** `/home/ubuntu/forseti.life/forseti-platform-specs/`  
**GitHub:** Forseti-Life/forseti-platform-specs  
**Purpose:** System architecture and API specifications  
**Status:** ✅ REFERENCE (design/specs)

### Contents

```
forseti-platform-specs/
├── .git/                          (Git repository)
├── api-spec/                      (API specifications)
│   ├── job-hunter-api.yaml        (OpenAPI spec)
│   ├── campaign-api.yaml          (OpenAPI spec)
│   ├── mesh-api.yaml              (OpenAPI spec)
│   └── ...
├── architecture/                  (System design)
│   ├── system-overview.md
│   ├── deployment-architecture.md
│   ├── data-flow.md
│   └── ...
├── design-docs/                   (Design documentation)
│   ├── database-schema.md
│   ├── security-model.md
│   ├── scalability-plan.md
│   └── ...
├── README.md
└── ...
```

### Purpose

Provides:
- API specifications (OpenAPI/Swagger)
- System architecture diagrams
- Data flow documentation
- Database schema
- Security model
- Deployment topology

---

## Summary Table: All 12 Repositories

| # | Name | Type | Size | Purpose | Status |
|---|------|------|------|---------|--------|
| 0 | forseti.life | Monorepo | Large | Deployment source | ✅ Active |
| 1 | forseti-job-hunter | Tier 1 Product | 24,959 files | Job search platform | ✅ Live |
| 2 | dungeoncrawler-pf2e | Tier 1 Product | 28,926 files | D&D campaign mgmt | ✅ Live |
| 3 | forseti-shared-modules | Tier 2 Library | Small | Shared utilities | ✅ Integrated |
| 4 | forseti-meshd | Tier 2 Library | Medium | Mesh network daemon | ✅ Ready |
| 5 | forseti-mobile | Tier 2 Library | Medium | iOS/Android apps | ✅ Ready |
| 6 | h3-geolocation | Tier 2 Library | Small | Geospatial library | ✅ Ready |
| 7 | copilot-hq | Tier 3 Ops | Medium | Governance & orchestration | ✅ Ready |
| 8 | forseti-devops | Tier 3 Ops | Medium | DevOps automation | ✅ Active |
| 9 | forseti-docs | Tier 3 Ops | Medium | Documentation hub | ✅ Reference |
| 10 | dungeoncrawler-content | Tier 4 Ref | Medium | Game rules data | ✅ Reference |
| 11 | forseti-platform-specs | Tier 4 Ref | Small | Architecture specs | ✅ Reference |

---

## What Each Repository Contains At A Glance

### Core Products (Tier 1) - 2 Repos
- **forseti-job-hunter**: Complete Drupal site with job search, AI matching, resume builder, ATS integration (24,959 files)
- **dungeoncrawler-pf2e**: Complete Drupal site with PF2E character builder, combat system, encounter designer (28,926 files)

### Libraries (Tier 2) - 4 Repos  
- **forseti-shared-modules**: Reusable Drupal modules for ORM, auth, content patterns, UI
- **forseti-meshd**: Python FastAPI mesh network daemon for P2P communication
- **forseti-mobile**: React Native iOS/Android apps for both products
- **h3-geolocation**: Python library for H3 hexagon grid and geospatial features

### Operations (Tier 3) - 3 Repos
- **copilot-hq**: Organizational governance, orchestrator, release cycles
- **forseti-devops**: DevOps automation, deployment scripts, Ansible, Docker, Terraform
- **forseti-docs**: Central documentation hub for all products and systems

### Reference (Tier 4) - 3 Repos  
- **dungeoncrawler-content**: PF2E game data, rules, monsters, equipment (JSON format)
- **forseti-platform-specs**: API specs, architecture diagrams, design documentation
- **(Reserved)**: One slot reserved for future expansion

### Monorepo (Private)
- **forseti.life**: Integration hub containing all split repo directories, deployment scripts, orchestrator, Drupal sites, production config

