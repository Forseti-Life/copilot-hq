import unittest

import orchestrator.run as run


class TestReleaseCycleControl(unittest.TestCase):
    def test_release_cycle_step_is_retired_noop(self):
        log = []
        run._release_cycle_step(log)
        self.assertEqual(
            log,
            [
                {
                    "step": "release_cycle",
                    "status": "retired",
                    "reason": "legacy flow removed",
                }
            ],
        )

    def test_coordinated_push_step_is_retired_noop(self):
        log = []
        run._coordinated_push_step(log)
        self.assertEqual(
            log,
            [
                {
                    "step": "coordinated_push",
                    "status": "retired",
                    "reason": "legacy flow removed",
                }
            ],
        )


if __name__ == "__main__":
    unittest.main()
