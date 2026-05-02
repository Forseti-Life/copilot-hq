from pathlib import Path


ROOT = Path(__file__).resolve().parents[2]
ROOM_CHAT_SERVICE = ROOT / "sites" / "dungeoncrawler" / "web" / "modules" / "custom" / "dungeoncrawler_content" / "src" / "Service" / "RoomChatService.php"
PROMPT_MANAGER = ROOT / "sites" / "dungeoncrawler" / "web" / "modules" / "custom" / "ai_conversation" / "src" / "Service" / "PromptManager.php"


def test_room_chat_service_attaches_reference_excerpt_to_backlog_suggestions():
    source = ROOM_CHAT_SERVICE.read_text(encoding="utf-8")

    assert "buildSuggestionReferenceExcerpt" in source
    assert "array_slice($lines, -150)" in source
    assert "Recent conversation reference (last " in source
    assert "$reference_excerpt" in source
    assert "createBacklogSuggestion(" in source


def test_prompt_manager_requires_actionable_confirmed_suggestion_summaries():
    source = PROMPT_MANAGER.read_text(encoding="utf-8")

    assert "implementation-ready" in source
    assert "ask 1-2 concise clarifying questions" in source
    assert "developer or content designer can act without guessing" in source
