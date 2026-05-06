- Status: done
<<<<<<<< HEAD:sessions/qa-dungeoncrawler/artifacts/20260423-unit-test-20260423-1776962948-impl-dungeoncrawler-pf2e-automation-vali-1777854397/command.md
- Completed: 2026-05-04T00:26:37Z
========
- Completed: 2026-04-24T03:45:41Z
>>>>>>>> reconcile/copilot-hq-local-priority-main:sessions/qa-dungeoncrawler/artifacts/20260423-unit-test-20260423-1776962948-impl-dungeoncrawler-pf2e-automation-vali-1777002341/command.md

- command: |
    Targeted QA unit test for completed Dev item.

    - Completed item: 20260423-1776962948-impl-dungeoncrawler-pf2e-automation-validation
    - Dev seat: dev-dungeoncrawler
    - Dev outbox: sessions/dev-dungeoncrawler/outbox/20260423-1776962948-impl-dungeoncrawler-pf2e-automation-validation.md

    Required actions:
    1) Run a targeted verification for *this item* (derive steps from Dev outbox + acceptance criteria).
    2) Ensure this check exists in the regression checklist and keep it evergreen:
       - org-chart/sites/dungeoncrawler/qa-regression-checklist.md
    3) Run the automated URL validation + role-based permission checks for this site (requires ALLOW_PROD_QA=1):
       - scripts/site-audit-run.sh (see runbooks/role-based-url-audit.md)

    Deliverable:
    - Write a Verification Report with explicit APPROVE/BLOCK and evidence.
