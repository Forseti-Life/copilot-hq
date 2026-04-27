import shutil
import stat
import subprocess
import tempfile
import textwrap
import unittest
from pathlib import Path


REPO_ROOT = Path("/home/ubuntu/forseti.life")
HQ_BLOCKERS_SRC = REPO_ROOT / "scripts" / "hq-blockers.sh"
AGENTS_LIB_SRC = REPO_ROOT / "scripts" / "lib" / "agents.sh"


def _write_executable(path: Path, content: str) -> None:
    path.write_text(content, encoding="utf-8")
    path.chmod(path.stat().st_mode | stat.S_IXUSR | stat.S_IXGRP | stat.S_IXOTH)


class TestHQBlockers(unittest.TestCase):
    def test_archived_only_inbox_is_not_counted_as_active_blocker(self):
        with tempfile.TemporaryDirectory() as td:
            root = Path(td)
            (root / "scripts" / "lib").mkdir(parents=True, exist_ok=True)
            (root / "org-chart" / "agents").mkdir(parents=True, exist_ok=True)
            (root / "sessions" / "pm-forseti" / "outbox").mkdir(parents=True, exist_ok=True)
            (root / "sessions" / "pm-forseti" / "inbox" / "_archived").mkdir(parents=True, exist_ok=True)

            shutil.copy2(HQ_BLOCKERS_SRC, root / "scripts" / "hq-blockers.sh")
            shutil.copy2(AGENTS_LIB_SRC, root / "scripts" / "lib" / "agents.sh")
            _write_executable(
                root / "scripts" / "is-agent-paused.sh",
                "#!/usr/bin/env bash\nprintf '%s\n' false\n",
            )
            (root / "org-chart" / "agents" / "agents.yaml").write_text(
                textwrap.dedent(
                    """\
                    agents:
                      - id: pm-forseti
                    """
                ),
                encoding="utf-8",
            )
            (root / "sessions" / "pm-forseti" / "outbox" / "20260427-quarantine.md").write_text(
                textwrap.dedent(
                    """\
                    - Status: needs-info
                    - Summary: Quarantined while executor backend was unhealthy.

                    ## Needs from Supervisor
                    - Confirm whether this still needs action.
                    """
                ),
                encoding="utf-8",
            )

            proc = subprocess.run(
                ["bash", str(root / "scripts" / "hq-blockers.sh"), "count"],
                cwd=root,
                stdout=subprocess.PIPE,
                stderr=subprocess.STDOUT,
                text=True,
                check=False,
            )

            self.assertEqual(proc.returncode, 0, proc.stdout)
            self.assertEqual(proc.stdout.strip(), "0")


if __name__ == "__main__":
    unittest.main()
