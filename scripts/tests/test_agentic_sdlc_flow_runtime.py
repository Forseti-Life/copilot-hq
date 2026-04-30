from importlib.util import module_from_spec, spec_from_file_location
import json
from pathlib import Path


ROOT = Path(__file__).resolve().parents[2]
PM_SCOPE_ACTIVATE = ROOT / "scripts" / "pm-scope-activate.sh"
AGENT_EXEC_NEXT = ROOT / "scripts" / "agent-exec-next.sh"
ROUTE_FLOW_TRANSITIONS = ROOT / "scripts" / "route-flow-transitions.py"


def _load_route_flow_module():
    spec = spec_from_file_location("route_flow_transitions", ROUTE_FLOW_TRANSITIONS)
    module = module_from_spec(spec)
    assert spec and spec.loader
    spec.loader.exec_module(module)
    return module


def test_pm_scope_activate_seeds_agentic_sdlc_runtime_and_flow_managed_handoffs():
    source = PM_SCOPE_ACTIVATE.read_text(encoding="utf-8")

    assert 'tmp/flow-runs/agentic_sdlc/${FEATURE_ID}' in source
    assert "- Flow id: agentic_sdlc" in source
    assert "- Flow node: Generate Code" in source
    assert "- Flow node: Test Cases Review" in source
    assert "- Available flow outcomes: Scope decision required" in source
    assert "- Flow direct route available: yes" in source
    assert "- Available flow outcomes: Approved | Changes requested" in source
    assert 'org-chart/sites/${SITE}/qa-permissions.json' in source
    assert "already-scoped 'in_progress' item for the active release" in source


def test_agent_exec_next_skips_legacy_dev_to_qa_handoff_for_flow_managed_items():
    source = AGENT_EXEC_NEXT.read_text(encoding="utf-8")

    assert "Flow-branch completion rule (required for flow-managed items)" in source
    assert "Flow-managed SDLC items rely on route-flow-transitions" in source
    assert "grep -qiE '^\\- Flow id:' \"$inbox_item/command.md\"" in source


def test_route_flow_transitions_prefers_default_direct_edge_without_flow_outcome():
    module = _load_route_flow_module()
    outgoing = [
        {"from_node": "Generate Code", "to_node": "Code Review", "condition": "", "kind": "direct"},
        {
            "from_node": "Generate Code",
            "to_node": "PM Scope Rebaseline",
            "condition": "Scope decision required",
            "kind": "conditional",
        },
    ]

    assert module.selected_transitions(outgoing, []) == [outgoing[0]]
    assert module.selected_transitions(outgoing, ["Scope decision required"]) == [outgoing[1]]


def test_load_flow_falls_back_when_live_registry_is_missing_required_transitions(monkeypatch):
    module = _load_route_flow_module()

    class DummyResult:
        returncode = 0
        stdout = json.dumps(
            {
                "id": "agentic_sdlc",
                "default_entrypoint": "User Requirements",
                "transitions": [
                    {
                        "from_node": "Generate Code",
                        "to_node": "Code Review",
                        "kind": "direct",
                        "condition": "",
                    }
                ],
                "node_breakdown": [
                    {"parent_node": "Generate Code", "owner_binding": "product_team.dev_agent"}
                ],
            }
        )

    monkeypatch.setattr(module, "DRUSH_ROOT", Path("/tmp/fake-drupal-root"))
    monkeypatch.setattr(module, "run", lambda *args, **kwargs: DummyResult())

    flow = module.load_flow("agentic_sdlc")
    outgoing = module.outgoing_transitions(flow, "Generate Code")

    assert {"from_node": "Generate Code", "to_node": "PM Scope Rebaseline", "kind": "conditional", "condition": "Scope decision required"} in outgoing
