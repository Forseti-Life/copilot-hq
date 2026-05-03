import json
import tempfile
import unittest
from pathlib import Path
from unittest.mock import patch

import orchestrator.run as run


class TestRuntimeAgentPause(unittest.TestCase):
    def test_runtime_pause_agent_writes_pause_file(self):
        with tempfile.TemporaryDirectory() as td:
            runtime_dir = Path(td) / "tmp" / "agent-pauses"
            original_dir = run._RUNTIME_AGENT_PAUSE_DIR
            try:
                run._RUNTIME_AGENT_PAUSE_DIR = runtime_dir
                with patch.object(run, "_now_ts", return_value=1000):
                    pause_file = run._runtime_pause_agent(
                        "qa-dungeoncrawler",
                        failure_count=9,
                        reason="test pause",
                        source="unit-test",
                        ttl_seconds=600,
                    )
                self.assertIsNotNone(pause_file)
                self.assertTrue(pause_file.exists())
                payload = json.loads(pause_file.read_text(encoding="utf-8"))
                self.assertEqual(payload["agent_id"], "qa-dungeoncrawler")
                self.assertEqual(payload["failure_count_24h"], 9)
                self.assertEqual(payload["expires_at_ts"], 1600)
            finally:
                run._RUNTIME_AGENT_PAUSE_DIR = original_dir

    def test_runtime_pause_agent_skips_ceo(self):
        with tempfile.TemporaryDirectory() as td:
            runtime_dir = Path(td) / "tmp" / "agent-pauses"
            original_dir = run._RUNTIME_AGENT_PAUSE_DIR
            try:
                run._RUNTIME_AGENT_PAUSE_DIR = runtime_dir
                pause_file = run._runtime_pause_agent(
                    "ceo-copilot-2",
                    failure_count=99,
                    reason="should skip ceo",
                    source="unit-test",
                    ttl_seconds=600,
                )
                self.assertIsNone(pause_file)
                self.assertFalse(runtime_dir.exists())
            finally:
                run._RUNTIME_AGENT_PAUSE_DIR = original_dir

    def test_is_inbox_item_done_ignores_template_status_line(self):
        with tempfile.TemporaryDirectory() as td:
            item_dir = Path(td) / "item"
            item_dir.mkdir(parents=True, exist_ok=True)
            (item_dir / "command.md").write_text(
                "- Status: done | in_progress | blocked | needs-info\n",
                encoding="utf-8",
            )
            self.assertFalse(run._is_inbox_item_done(item_dir))

    def test_is_inbox_item_done_accepts_exact_done(self):
        with tempfile.TemporaryDirectory() as td:
            item_dir = Path(td) / "item"
            item_dir.mkdir(parents=True, exist_ok=True)
            (item_dir / "command.md").write_text(
                "- Status: done\n",
                encoding="utf-8",
            )
            self.assertTrue(run._is_inbox_item_done(item_dir))


if __name__ == "__main__":
    unittest.main()
