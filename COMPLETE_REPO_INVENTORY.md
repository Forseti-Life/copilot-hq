# Complete Forseti-Life Repository Inventory

**Generated:** 2026-04-21 16:55 UTC  
**Status:** ✅ ALL 12 REPOS LOCALLY AVAILABLE

---

## 1. Private Monorepo (Source of Truth)

| Repo | Location | Remote | Branch | Commits | Status |
|------|----------|--------|--------|---------|--------|
| **forseti.life** | `/home/ubuntu/forseti.life` | keithaumiller/forseti.life | main | 65+ staged | ✅ Active |

**Purpose:** Operational deployment source, production Drupal sites, orchestrator  
**Contains:** Sites, orchestrator, infrastructure, copilot-hq, split repo directories

---

## 2. Split Repositories in Monorepo (12 Total)

### Tier 1: Core Products (2 repos)

| Repo | Location | Remote | Branch | Commit | Status |
|------|----------|--------|--------|--------|--------|
| **forseti-job-hunter** | `./forseti-job-hunter/` | Forseti-Life/forseti-job-hunter | main | 9c96004f | ✅ Synced |
| **dungeoncrawler-pf2e** | `./dungeoncrawler-pf2e/` | Forseti-Life/dungeoncrawler-pf2e | main | dd1148a2 | ✅ Synced |

**Purpose:** Public production products  
**Access:** Public (community contributions welcome)

---

### Tier 2: Developer Libraries (4 repos)

| Repo | Location | Remote | Branch | Commit | Status |
|------|----------|--------|--------|--------|--------|
| **forseti-shared-modules** | `./forseti-shared-modules/` | Forseti-Life/forseti-shared-modules | main | c613c8b | ✅ Synced |
| **forseti-meshd** | `./forseti-meshd/` | Forseti-Life/forseti-meshd | main | e66909b | ✅ Synced |
| **forseti-mobile** | `./forseti-mobile/` | Forseti-Life/forseti-mobile | main | ce07746 | ✅ Synced |
| **h3-geolocation** | `./h3-geolocation/` | Forseti-Life/h3-geolocation | main | c2e7787 | ✅ Synced |

**Purpose:** Reusable libraries, mobile apps, integrations  
**Access:** Public (developer-friendly)

---

### Tier 3: Operations & Tooling (3 repos)

| Repo | Location | Remote | Branch | Commit | Status |
|------|----------|--------|--------|--------|--------|
| **copilot-hq** | `./copilot-hq/` | Forseti-Life/copilot-hq | main | eadd55eb67 | ✅ Synced |
| **forseti-devops** | `./forseti-devops/` | Forseti-Life/forseti-devops | main | 1f10bc8 | ✅ Synced |
| **forseti-docs** | `./forseti-docs/` | Forseti-Life/forseti-docs | main | 8fe9660 | ✅ Synced |

**Purpose:** Organizational model, deployment automation, documentation  
**Access:** Public (team onboarding & ops transparency)

---

### Tier 4: Content & Reference (3 repos)

| Repo | Location | Remote | Branch | Commit | Status |
|------|----------|--------|--------|--------|--------|
| **dungeoncrawler-content** | `./dungeoncrawler-content/` | Forseti-Life/dungeoncrawler-content | main | cb8ed60 | ✅ Synced |
| **forseti-platform-specs** | `./forseti-platform-specs/` | Forseti-Life/forseti-platform-specs | main | ad1187d | ✅ Synced |
| *(reserved)* | — | — | — | — | — |

**Purpose:** Game rules data, architecture specs, reference documentation  
**Access:** Public (community resources)

---

## Repository Structure Summary

```
/home/ubuntu/forseti.life/
├── .git/                          # Private monorepo (keithaumiller/forseti.life)
│
├── copilot-hq/                    # ✅ Tier 3: Organizational governance
│   └── .git/ → Forseti-Life/copilot-hq
│
├── forseti-job-hunter/            # ✅ Tier 1: Job search platform
│   └── .git/ → Forseti-Life/forseti-job-hunter
│
├── dungeoncrawler-pf2e/           # ✅ Tier 1: D&D campaign assistant
│   └── .git/ → Forseti-Life/dungeoncrawler-pf2e
│
├── forseti-shared-modules/        # ✅ Tier 2: Shared Drupal modules
│   └── .git/ → Forseti-Life/forseti-shared-modules
│
├── forseti-mobile/                # ✅ Tier 2: iOS/Android apps
│   └── .git/ → Forseti-Life/forseti-mobile
│
├── forseti-meshd/                 # ✅ Tier 2: Mesh networking daemon
│   └── .git/ → Forseti-Life/forseti-meshd
│
├── h3-geolocation/                # ✅ Tier 2: Geospatial integration
│   └── .git/ → Forseti-Life/h3-geolocation
│
├── forseti-devops/                # ✅ Tier 3: DevOps automation
│   └── .git/ → Forseti-Life/forseti-devops
│
├── forseti-docs/                  # ✅ Tier 3: Documentation hub
│   └── .git/ → Forseti-Life/forseti-docs
│
├── dungeoncrawler-content/        # ✅ Tier 4: Game rules data
│   └── .git/ → Forseti-Life/dungeoncrawler-content
│
├── forseti-platform-specs/        # ✅ Tier 4: Architecture specs
│   └── .git/ → Forseti-Life/forseti-platform-specs
│
├── sites/                         # Production Drupal configs (not split)
├── orchestrator/                  # Release cycle automation (not split)
├── prod-config/                   # Production credentials (NEVER public)
├── shared/                        # Shared code (synced to forseti-shared-modules)
├── scripts/                       # Deployment & maintenance scripts
└── ... (other operational directories)
```

---

## Synchronization Model

### Direction: Monorepo → GitHub Organization

```
/home/ubuntu/forseti.life (private)
    ↓ (committed code)
    ↓ (on coordinated push)
GitHub Actions (deploy.yml)
    ↓ (extracts & pushes)
Forseti-Life/[repo-name] (public)
    ↓ (webhook / back-sync)
Local split repo directories
```

**Flow:**
1. Developer commits to monorepo `/home/ubuntu/forseti.life`
2. Coordinated push triggers `deploy.yml` workflow
3. GitHub Actions extracts split repo content
4. Each split repo syncs automatically to local directory
5. Split repos available for public contributions

---

## Git Configuration (All Repos)

### Authentication
- **Method:** GitHub PAT (token-based)
- **Location:** `/home/ubuntu/github.token`
- **Scope:** repo, workflow, public_repo, read:org, write:org
- **Used by:** All 12 repos for push/pull operations

### User Configuration
Each repo configured with:
```bash
git config user.email "copilot@github.com"
git config user.name "Copilot"
```

### Branch Strategy
- **All repos:** On `main` branch
- **Workflow:** Feature branches → PR → merge to main
- **Deployment:** Main branch auto-deploys via GitHub Actions

---

## Development Workflows

### Workflow A: Internal Development (Monorepo)
```bash
cd /home/ubuntu/forseti.life
git checkout -b feature/my-feature
# Edit any file in monorepo, sites, orchestrator, or split repo dirs
git add -A
git commit -m "feat: description"
git push origin feature/my-feature
# Creates PR on keithaumiller/forseti.life (private account)
```

### Workflow B: Split Repo Direct Development
```bash
cd /home/ubuntu/forseti.life/copilot-hq
git checkout -b feature/my-feature
# Edit files in this split repo
git add -A
git commit -m "feat: description"
git push origin feature/my-feature
# Creates PR on Forseti-Life/copilot-hq (org account)
```

### Workflow C: Public Contribution (External)
```bash
# External developer forks Forseti-Life/copilot-hq on GitHub
# Creates PR to main branch
# Maintainers review & merge
# Changes sync back to monorepo via coordinated push
```

---

## Repository Access & Permissions

| Tier | Repos | Access | Use Case | Auth |
|------|-------|--------|----------|------|
| **Private** | forseti.life | keithaumiller only | Deployment source | Personal PAT |
| **Tier 1** | forseti-job-hunter, dungeoncrawler-pf2e | Public | Production products | GitHub PAT (scoped) |
| **Tier 2** | forseti-shared-modules, forseti-meshd, forseti-mobile, h3-geolocation | Public | Developer libraries | GitHub PAT (scoped) |
| **Tier 3** | copilot-hq, forseti-devops, forseti-docs | Public | Operations & tooling | GitHub PAT (scoped) |
| **Tier 4** | dungeoncrawler-content, forseti-platform-specs | Public | Content & reference | GitHub PAT (scoped) |

---

## Deployment Path

### Coordinated Push Sequence

1. **Monorepo Commit**
   ```
   /home/ubuntu/forseti.life (main branch)
   └─ git push origin main
   ```

2. **GitHub Actions Trigger**
   ```
   keithaumiller/forseti.life (deploy.yml workflow)
   └─ Extracts split repo content
   └─ Pushes to Forseti-Life/[repo]
   ```

3. **Split Repo Update**
   ```
   Forseti-Life/copilot-hq (webhook)
   Forseti-Life/forseti-meshd (webhook)
   ... (all 12 repos)
   └─ Local directories auto-sync
   ```

4. **Local Verification**
   ```
   /home/ubuntu/forseti.life/copilot-hq
   /home/ubuntu/forseti.life/forseti-meshd
   ... (all split repos now in sync)
   ```

---

## Current System State

✅ **Monorepo Health:**
- Status: Clean
- Commits: 65+ staged
- Branch: main
- Ready: YES (coordinated push ready)

✅ **All Split Repos:**
- Total: 12 repos
- Status: All cloned locally
- Branch: All on main
- Remote: All pointing to Forseti-Life org
- Auth: All authenticated via GitHub PAT

✅ **Release Cycles (PAUSED):**
- forseti-q: Gate 2 ✅ (100% signoffs) — READY FOR PUSH
- dungeoncrawler-s: Gate 2 ✅ (100% signoffs) — READY FOR PUSH

✅ **Cross-Team Coordination:**
- forseti team: Ready ✅
- dungeoncrawler team: Ready ✅
- Signoffs: 100% ✅

---

## Next Actions

### Immediate:
1. ✅ All 12 repos cloned and synchronized
2. ✅ All repos on main branch with GitHub remotes
3. ✅ GitHub PAT verified working
4. → Resume orchestrator
5. → Coordinated push will execute on next tick

### Post-Push:
1. Monitor GitHub Actions workflows
2. Verify all split repos updated
3. Confirm post-coordinated-push.sh executes
4. Advance to new release cycles (forseti-r, dungeoncrawler-t)
5. Process PM grooming items

---

## Files & Documentation

| File | Purpose | Location |
|------|---------|----------|
| ENVIRONMENT_ALIGNMENT_REPORT.md | Alignment details | Monorepo root |
| REPOSITORY_ARCHITECTURE.md | Architecture docs | Monorepo root |
| MULTIREPOSITORY_DEVELOPER_GUIDE.md | Developer guide | Monorepo root |
| COMPLETE_REPO_INVENTORY.md | This document | Generated |

---

**Status:** ✅ COMPLETE  
**All repositories aligned and ready for deployment**  
**Orchestrator: PAUSED (awaiting resume command)**
