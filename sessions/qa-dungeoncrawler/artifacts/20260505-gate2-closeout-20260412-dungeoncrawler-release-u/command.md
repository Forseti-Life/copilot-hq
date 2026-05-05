Review and close out Dungeoncrawler release 20260412-dungeoncrawler-release-u.

Context:
- CEO re-scoped all Dungeoncrawler features currently at Status: done into release-u so they can be confirmed and shipped through a real release instead of remaining unscoped.
- The prior empty-release self-cert for release-u has been retired and is no longer valid.
- The bundled file feature-ids.txt is the authoritative list of in-scope features for this QA pass.

Your task:
1. Review the in-scope done features for release-u.
2. Confirm whether release-u should receive Gate 2 APPROVE or BLOCK.
3. Write one outbox artifact for release-u with explicit decision and evidence.
4. If BLOCK, name the blocking features and exact missing evidence.
5. If APPROVE, state that release-u is ready for PM signoff and ship.

Required output:
- A single outbox markdown file for 20260412-dungeoncrawler-release-u
- Decision line must clearly say APPROVE or BLOCK
- Reference feature-ids.txt in the evidence section
