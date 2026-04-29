import unittest
from unittest.mock import patch

import orchestrator.run as run


class TestReleaseCycleControl(unittest.TestCase):
    def test_release_cycle_step_delegates_to_release_cycle_module(self):
        log = []
        with patch.object(run.release_cycle, "run_release_cycle_step") as delegated:
            run._release_cycle_step(log)
        delegated.assert_called_once_with(log, run.REPO_ROOT)

    def test_coordinated_push_step_delegates_to_release_cycle_module(self):
        log = []
        with patch.object(run.release_cycle, "run_coordinated_push_step") as delegated:
            run._coordinated_push_step(log)
        delegated.assert_called_once_with(log, run.REPO_ROOT)


if __name__ == "__main__":
    unittest.main()
