import json
import tempfile
import unittest
from pathlib import Path
from unittest.mock import patch

import orchestrator.dispatch as dispatch


class TestSignoffReminderDispatch(unittest.TestCase):
    """Exercise dependency-aware signoff reminder dispatch."""

    def setUp(self):
        self.old_root = dispatch.REPO_ROOT
        self.old_state = dispatch._SIGNOFF_REMINDER_STATE

    def tearDown(self):
        dispatch.REPO_ROOT = self.old_root
        dispatch._SIGNOFF_REMINDER_STATE = self.old_state

    def _make_test_env(self):
        td = tempfile.TemporaryDirectory()
        root = Path(td.name)

        org_chart = root / "org-chart" / "products"
        org_chart.mkdir(parents=True, exist_ok=True)
        teams_data = {
            "teams": [
                {
                    "id": "forseti",
                    "pm_agent": "pm-forseti",
                    "active": True,
                    "release_preflight_enabled": True,
                    "release_dependencies": ["dungeoncrawler"],
                },
                {
                    "id": "dungeoncrawler",
                    "pm_agent": "pm-dungeoncrawler",
                    "active": True,
                    "release_preflight_enabled": True,
                    "release_dependencies": [],
                },
            ]
        }
        (org_chart / "product-teams.json").write_text(json.dumps(teams_data), encoding="utf-8")

        active_dir = root / "tmp" / "release-cycle-active"
        active_dir.mkdir(parents=True, exist_ok=True)
        (active_dir / "forseti.release_id").write_text("forseti-release\n", encoding="utf-8")
        (active_dir / "dungeoncrawler.release_id").write_text("dungeoncrawler-release\n", encoding="utf-8")

        for pm_id in ["pm-forseti", "pm-dungeoncrawler"]:
            (root / "sessions" / pm_id / "artifacts" / "release-signoffs").mkdir(parents=True, exist_ok=True)
            (root / "sessions" / pm_id / "inbox").mkdir(parents=True, exist_ok=True)

        dispatch.REPO_ROOT = root
        dispatch._SIGNOFF_REMINDER_STATE = root / "tmp" / "dispatch-state" / "signoff-reminder.timestamp"
        return root, td

    def test_no_dependency_signoffs_no_reminder(self):
        root, td = self._make_test_env()
        try:
            dispatch._dispatch_signoff_reminders()

            for pm_id in ["pm-forseti", "pm-dungeoncrawler"]:
                items = list((root / "sessions" / pm_id / "inbox").glob("*signoff-reminder*"))
                self.assertEqual(len(items), 0)
        finally:
            td.cleanup()

    def test_all_dependency_signoffs_present_no_reminder(self):
        root, td = self._make_test_env()
        try:
            (root / "sessions" / "pm-dungeoncrawler" / "artifacts" / "release-signoffs" / "dungeoncrawler-release.md").write_text(
                "# Signoff\n- Status: approved\n",
                encoding="utf-8",
            )

            dispatch._dispatch_signoff_reminders()

            for pm_id in ["pm-forseti", "pm-dungeoncrawler"]:
                items = list((root / "sessions" / pm_id / "inbox").glob("*signoff-reminder*"))
                self.assertEqual(len(items), 0)
        finally:
            td.cleanup()

    def test_existing_dependency_reminder_is_not_overwritten(self):
        root, td = self._make_test_env()
        try:
            (root / "sessions" / "pm-forseti" / "artifacts" / "release-signoffs" / "forseti-release.md").write_text(
                "# Signoff\n- Status: approved\n",
                encoding="utf-8",
            )

            dungeoncrawler_inbox = root / "sessions" / "pm-dungeoncrawler" / "inbox"
            reminder_dir = dungeoncrawler_inbox / "20260420-signoff-reminder-forseti-release"
            reminder_dir.mkdir(parents=True, exist_ok=True)
            (reminder_dir / "README.md").write_text("Existing reminder\n", encoding="utf-8")
            (reminder_dir / "roi.txt").write_text("500", encoding="utf-8")

            with patch("orchestrator.dispatch._now_ts", return_value=int(1e10)):
                dispatch._dispatch_signoff_reminders()

            items = list(dungeoncrawler_inbox.glob("*signoff-reminder*"))
            self.assertEqual(len(items), 1)
            self.assertEqual((reminder_dir / "README.md").read_text(encoding="utf-8"), "Existing reminder\n")
        finally:
            td.cleanup()
