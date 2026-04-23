# Drupal Module README Open-Source Review & Update Plan

**Scope:** 20 custom Drupal modules across 3 repositories  
**Goal:** Ensure all READMEs are OSS-ready with consistent formatting and documentation  
**Status:** In Progress

## Repository Breakdown

### forseti-job-hunter (15 modules)
- **web/modules/custom/** directory
- 14 existing READMEs (need updates)
- 1 missing README (community_incident_report)

### dungeoncrawler-pf2e (3 modules)
- **web/modules/custom/** directory
- 3 existing READMEs (need updates)

### forseti-shared-modules (2 modules)
- **modules/** directory
- 0 existing READMEs (both need creation)

## Key Open-Source Standards to Apply

✅ **Sections Required:**
1. Module name & description
2. Purpose/use case
3. Installation instructions (with prerequisites)
4. Configuration guide
5. Usage examples
6. API documentation (if exposed)
7. Dependencies listing
8. Development/testing info
9. Contributing guidelines
10. License statement & badges
11. Troubleshooting section
12. Support/contact information

✅ **Formatting Standards:**
- Consistent heading hierarchy
- Code blocks with syntax highlighting
- Examples that work
- Clear prerequisites
- Explicit license (OSI-approved)
- Standard badges (license, version, status)

✅ **Security/Privacy Review:**
- Remove internal references
- Anonymize example data
- Document credential management
- Note data retention policies
- Add security considerations section

## Module Status & Actions

### FORSETI-JOB-HUNTER (15 modules)

| # | Module | Status | Priority | Action |
|---|--------|--------|----------|--------|
| 1 | agent_evaluation | 60% | HIGH | Add install, license, contrib |
| 2 | ai_conversation | 65% | HIGH | Split into multiple docs, add AWS setup |
| 3 | amisafe | 75% | MEDIUM | Clarify data, simplify for users |
| 4 | community_incident_report | MISSING | CRITICAL | Create from scratch |
| 5 | company_research | 80% | MEDIUM | Clarify license, add contrib |
| 6 | copilot_agent_tracker | 70% | HIGH | Add telemetry auth, config guide |
| 7 | forseti_cluster | MISSING | CRITICAL | Create from scratch |
| 8 | forseti_content | 50% | MEDIUM | Complete features, add examples |
| 9 | forseti_games | 50% | MEDIUM | Complete features, add examples |
| 10 | forseti_safety_content | DEPRECATED | LOW | Keep as ref, link to replacement |
| 11 | institutional_management | 85% | LOW | Minor refinements |
| 12 | job_hunter | 90% | LOW | Minor refinements |
| 13 | jobhunter_tester | UTILITY | LOW | Brief description |
| 14 | nfr | UTILITY | LOW | Brief description |
| 15 | safety_calculator | UTILITY | LOW | Brief description |

### DUNGEONCRAWLER-PF2E (3 modules)

| # | Module | Status | Priority | Action |
|---|--------|--------|----------|--------|
| 1 | ai_conversation | 65% | HIGH | Sync with job-hunter version |
| 2 | dungeoncrawler_content | 90% | LOW | Minor refinements |
| 3 | dungeoncrawler_tester | UTILITY | LOW | Brief description |

### FORSETI-SHARED-MODULES (2 modules)

| # | Module | Status | Priority | Action |
|---|--------|--------|----------|--------|
| 1 | ai_conversation | MISSING | CRITICAL | Create from scratch |
| 2 | amisafe | MISSING | CRITICAL | Create from scratch |

## README Template (OSS Standard)

```markdown
# [Module Name]

> [One-sentence description]

[![License: GPL-3.0](badge-url)](LICENSE.md)
[![Drupal Version: 10.3+](badge-url)]()
[![Status: Production](badge-url)]()

## Overview

[Detailed description of what the module does and why it exists]

## Features

- Feature 1
- Feature 2
- Feature 3

## Installation

### Prerequisites
- Drupal 10.3+ or 11
- PHP 8.1+
- Additional requirements

### Quick Start
\`\`\`bash
composer require drupal/[module-name]
drush pm:enable [module-name]
drush cim
drush cache:rebuild
\`\`\`

## Configuration

[Step-by-step admin configuration guide with screenshots/examples]

## Usage

### Basic Example
[Code examples showing common usage patterns]

### API Documentation

[If module exposes APIs, document them here]

## Dependencies

- drupal/core (^10.3 or ^11)
- External libraries (list any)

## Development

[How to extend, debug, test the module]

## Contributing

We welcome contributions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

## License

[GPL-3.0 or MIT - specify clearly]

## Support

[How to report issues, get help]

## Security

[Any security considerations]

---

**Maintainer:** Forseti Team
**Last Updated:** [Date]
```

## Implementation Plan

### Phase 1: Create Missing READMEs (Modules with 0%)
- community_incident_report
- forseti_cluster
- forseti-shared-modules/ai_conversation
- forseti-shared-modules/amisafe

### Phase 2: Update High-Priority READMEs (60-80%)
- agent_evaluation
- ai_conversation (both repos)
- copilot_agent_tracker

### Phase 3: Refine Medium-Priority READMEs (50-85%)
- amisafe
- company_research
- forseti_content
- forseti_games
- institutional_management

### Phase 4: Polish Low-Priority READMEs (85%+)
- job_hunter
- dungeoncrawler_content
- jobhunter_tester, nfr, safety_calculator (utility modules)

### Phase 5: Final Review & Consistency Check
- Verify all follow template
- Check for internal references
- Add badges
- Verify links work
- Commit all changes

## Files to Create/Update

**Create (4 new):**
- forseti-job-hunter/web/modules/custom/community_incident_report/README.md
- forseti-job-hunter/web/modules/custom/forseti_cluster/README.md
- forseti-shared-modules/modules/ai_conversation/README.md
- forseti-shared-modules/modules/amisafe/README.md

**Update (16 existing):**
- [All other module READMEs]

## Success Criteria

✅ All 20 modules have complete README.md files  
✅ All follow consistent template structure  
✅ All include license statement & badges  
✅ All include contributing guidelines  
✅ No internal/sensitive references remain  
✅ All installation instructions work  
✅ All examples are accurate & tested  
✅ Ready for publishing to github.com/Forseti-Life  

## Timeline

- **Phase 1-2:** High-impact modules (CRITICAL & HIGH priority)
- **Phase 3-4:** Complete coverage
- **Phase 5:** Final review & commit

