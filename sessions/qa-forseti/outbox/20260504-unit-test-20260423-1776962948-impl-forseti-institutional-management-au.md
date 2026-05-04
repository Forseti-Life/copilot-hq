I'll start by reading the dev outbox and acceptance criteria to derive the verification plan.

## Step 1: Read Dev outbox and feature files

**Tool call: bash**

```bash
cat /home/ubuntu/forseti.life/sessions/dev-forseti/outbox/20260423-1776962948-impl-forseti-institutional-management-automation-validation.md
```

**Output:**
```
- Status: done
- Summary: Implemented automated validation for the Forseti Institutional Management module. Added a custom Drush command `forseti:validate-institutions` that checks institution node integrity, validates required fields, and reports anomalies. The command is accessible to admins only and outputs structured results. All acceptance criteria met; see implementation notes below.

## Implementation notes

### Changed surface
- New Drush command: `forseti:validate-institutions`
- Module: `forseti_institutional_management`
- File added: `sites/forseti/web/modules/custom/forseti_institutional_management/src/Commands/InstitutionalManagementCommands.php`
- Service registration: `forseti_institutional_management.services.yml` updated
- Route/permission: Drush command, no HTTP route; requires `administer site configuration` permission (Drush admin context)

### Acceptance criteria delivered
1. Drush command exists and is discoverable: `drush list | grep forseti`
2. Command validates institution nodes: checks for missing required fields (title, field_institution_type, field_institution_status)
3. Command outputs a structured summary (valid count, invalid count, anomaly list)
4. Command is restricted to admin/drush context (not exposed as HTTP endpoint)
5. No new HTTP routes added (no ACL surface change)

### Commit
- Commit hash: see git log for forseti_institutional_management module

## Next actions
- None; handoff to QA
```

**Tool call: bash**

```bash
find /home/ubuntu/forseti.life/features -type d | grep -i institutional | head -20
```

**Output:**
```
/home/ubuntu/forseti.life/features/forseti-institutional-management-automation-validation
```

**Tool call: bash**

```bash
ls /home/ubuntu/forseti.life/features/forseti-institutional-management-automation-validation/
```

**Output:**
```
01-acceptance-criteria.md  feature.md
```

**Tool call: bash**

```bash
cat /home/ubuntu/forseti.life/features/forseti-institutional-management-automation-validation/feature.md
cat /home/ubuntu/forseti.life/features/forseti-institutional-management-automation-validation/01-acceptance-criteria.md
```

**Output (feature.md):**
```
# Feature: Forseti Institutional Management — Automation Validation

- Feature ID: forseti-institutional-management-automation-validation
- Release ID: 20260423-1776962948
- Status: in_progress

---
- Agent: qa-forseti
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-forseti/inbox/20260504-unit-test-20260423-1776962948-impl-forseti-institutional-management-au
- Generated: 2026-05-04T05:12:30+00:00
