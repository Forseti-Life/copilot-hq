import unittest
from pathlib import Path


SCRIPT = Path(__file__).resolve().parents[2] / "scripts" / "agent-exec-next.sh"


class TestAgentExecBedrockWiring(unittest.TestCase):
    def test_run_bedrock_uses_llm_bedrock_runner(self):
        source = SCRIPT.read_text(encoding="utf-8")
        self.assertIn('BEDROCK_RUNNER="$ROOT_DIR/llm/bedrock_runner.py"', source)
        self.assertIn('"$py" "$BEDROCK_RUNNER"', source)
        self.assertNotIn('"$BEDROCK_ASSIST_SCRIPT" "$site"', source)

    def test_prompt_does_not_unconditionally_promise_tool_access(self):
        source = SCRIPT.read_text(encoding="utf-8")
        self.assertIn("Do NOT assume live tool access.", source)
        self.assertNotIn("You have full read/write tool access (--allow-all)", source)


if __name__ == "__main__":
    unittest.main(verbosity=2)
