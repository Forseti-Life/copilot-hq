from pathlib import Path


ROOT = Path(__file__).resolve().parents[2]
TEMPLATE = ROOT / "sites" / "dungeoncrawler" / "web" / "modules" / "custom" / "dungeoncrawler_content" / "templates" / "hexmap-demo.html.twig"
CSS = ROOT / "sites" / "dungeoncrawler" / "web" / "modules" / "custom" / "dungeoncrawler_content" / "css" / "hexmap.css"
JS = ROOT / "sites" / "dungeoncrawler" / "web" / "modules" / "custom" / "dungeoncrawler_content" / "js" / "hexmap.js"
SPRITE_SERVICE = ROOT / "sites" / "dungeoncrawler" / "web" / "modules" / "custom" / "dungeoncrawler_content" / "js" / "SpriteService.js"
RENDER_SYSTEM = ROOT / "sites" / "dungeoncrawler" / "web" / "modules" / "custom" / "dungeoncrawler_content" / "js" / "ecs" / "systems" / "RenderSystem.js"


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


def test_hexmap_template_exposes_game_control_hud():
    source = TEMPLATE.read_text(encoding="utf-8")

    assert 'id="game-controls-hud"' in source
    assert 'id="focus-selected"' in source
    assert "<kbd>1</kbd><kbd>M</kbd>" in source
    assert "<kbd>↑</kbd><kbd>↓</kbd><kbd>←</kbd><kbd>→</kbd>" in source


def test_hexmap_js_supports_hotkeys_and_camera_focus():
    source = JS.read_text(encoding="utf-8")

    assert "focusCameraOnSelectedEntity" in source
    assert "handleKeyboardShortcut" in source
    assert "clickVisibleControl('action-move')" in source
    assert "clickVisibleControl('end-turn')" in source


def test_hexmap_js_supports_floating_combat_feedback():
    source = JS.read_text(encoding="utf-8")

    assert "spawnCombatText" in source
    assert "updateCombatTextEffects" in source
    assert "clearCombatTextEffects" in source
    assert "CRIT!" in source
    assert "'DOWN'" in source


def test_hexmap_js_builds_directional_sprite_variants():
    source = JS.read_text(encoding="utf-8")

    assert "buildDirectionalSpriteConfig" in source
    assert "normalizeSpriteDirectionToken" in source
    assert "renderComp.spriteVariants" in source
    assert "options.spriteVariants = directionalSprites.variants" in source


def test_render_system_prefers_directional_sprite_variants():
    source = RENDER_SYSTEM.read_text(encoding="utf-8")

    assert "getPreferredSpriteKeyForRender" in source
    assert "syncPreferredSpriteTexture" in source
    assert "hasDirectionalSpriteVariants" in source
    assert "return false;" in source and "this.hasDirectionalSpriteVariants(render)" in source


def test_sprite_service_resolves_and_primes_variant_sprite_ids():
    source = SPRITE_SERVICE.read_text(encoding="utf-8")

    assert "resolveRenderSpriteIds" in source
    assert "getRenderSpriteIds" in source
    assert "ensureTextureLoaded" in source
    assert "renderSystem.getPreferredSpriteKeyForEntity(entity)" in source
