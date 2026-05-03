# Groom Next Release: 20260412-dungeoncrawler-release-ac

- Site: dungeoncrawler
- Current release (Dev executing): 20260412-dungeoncrawler-release-ab
- Next release (your target): 20260412-dungeoncrawler-release-ac

The org always has two releases defined simultaneously:
- **Current release** — Dev is executing, QA is verifying. You monitor but do not add scope.
- **Next release** — You groom the backlog so Stage 0 of the next release is instant scope selection.

This task does NOT touch the current release. All work here is for 20260412-dungeoncrawler-release-ac only.

## Steps

### 1. Audit the existing next-release backlog first
If there are already next-release features in `planned`, `ready`, `in_progress`, or `deferred`, audit them before treating suggestion intake as done. Deferred features must be explicitly re-evaluated each cycle instead of silently disappearing from backlog review.
```bash
python3 - <<'PY'
import pathlib, re
site = 'dungeoncrawler'
for fm in sorted(pathlib.Path('features').glob('*/feature.md')):
    text = fm.read_text(encoding='utf-8')
    if f'- Website: {site}' not in text:
        continue
    m = re.search(r'^- Status:\s*(.+)$', text, re.MULTILINE)
    if not m:
        continue
    status = m.group(1).strip()
    if status not in {'planned', 'ready', 'in_progress', 'deferred'}:
        continue
    ac = fm.with_name('01-acceptance-criteria.md').exists()
    tp = fm.with_name('03-test-plan.md').exists()
    reason = ''
    if status == 'deferred':
        dm = re.search(r'^- Defer reason:\s*(.*)$', text, re.MULTILINE)
        reason = dm.group(1).strip() if dm else 're-evaluate this deferred backlog item for next release'
    if status == 'deferred' or not (ac and tp):
        msg = f'{fm.parent.name}: status={status} ac={ac} testplan={tp}'
        if reason:
            msg += f' defer_reason={reason}'
        print(msg)
PY
```

### 2. Pull community suggestions
Run `./scripts/suggestion-intake.sh` once for the site.

### 3. Triage valid suggestions
Create or curate feature briefs for the next release only.

### 4. Write or complete acceptance criteria
Any accepted or already-tracked next-release feature missing `01-acceptance-criteria.md` must get a complete AC contract before QA handoff.

### 5. Hand features to QA for test-plan design
Any next-release feature that has AC but is missing `03-test-plan.md` must be handed to QA via `./scripts/pm-qa-handoff.sh`.

### 6. Leave current-release scope unchanged
Activation happens only when the next release becomes current.

## Done when
- The next release `20260412-dungeoncrawler-release-ac` has an actively groomed ready backlog.
- Every existing next-release feature already in `planned`, `ready`, or `in_progress` either has both grooming artifacts (`01-acceptance-criteria.md` + `03-test-plan.md`) or is explicitly deferred/blocked.
- Every deferred next-release feature has been explicitly re-confirmed for deferral or moved back to an actionable status (`planned`/`ready`).
- Any newly accepted feature has acceptance criteria and a QA handoff queued.
