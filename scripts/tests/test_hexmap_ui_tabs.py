from pathlib import Path


ROOT = Path(__file__).resolve().parents[2]
TEMPLATE = ROOT / "sites" / "dungeoncrawler" / "web" / "modules" / "custom" / "dungeoncrawler_content" / "templates" / "hexmap-demo.html.twig"
CSS = ROOT / "sites" / "dungeoncrawler" / "web" / "modules" / "custom" / "dungeoncrawler_content" / "css" / "hexmap.css"


def test_hexmap_template_exposes_three_player_surface_tabs():
    source = TEMPLATE.read_text(encoding="utf-8")

    assert 'id="play-surface-tabs"' in source
    assert 'data-surface="map"' in source
    assert 'data-surface="chat"' in source
    assert 'data-surface="character"' in source
    assert "Character Sheet" in source


def test_hexmap_template_persists_player_surface_selection():
    source = TEMPLATE.read_text(encoding="utf-8")

    assert "dc_player_surface_tab" in source
    assert "hexmap-container--surface-" in source
    assert "activateCharacterSheet" in source


def test_hexmap_css_defines_surface_visibility_rules():
    source = CSS.read_text(encoding="utf-8")

    assert ".play-surface-tabs" in source
    assert ".hexmap-container--surface-map .hexmap-chat" in source
    assert ".hexmap-container--surface-chat #hexmap-canvas-container" in source
    assert ".hexmap-container--surface-character #sidebar-panel-character" in source
