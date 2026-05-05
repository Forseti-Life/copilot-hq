from pathlib import Path
import re


ROOT = Path(__file__).resolve().parents[2]


def test_done_maps_to_implemented_in_all_roadmap_resolvers():
    targets = [
        ROOT / "drupal-langgraph" / "src" / "Service" / "PipelineStatusResolver.php",
        ROOT / "sites" / "forseti" / "web" / "modules" / "custom" / "forseti_content" / "src" / "Service" / "ForsetiPipelineStatusResolver.php",
        ROOT / "dungeoncrawler-pf2e" / "web" / "modules" / "custom" / "dungeoncrawler_content" / "src" / "Service" / "RoadmapPipelineStatusResolver.php",
    ]

    for path in targets:
        source = path.read_text(encoding="utf-8")
        assert not re.search(r"'done'\s*=>\s*'in_progress'", source)
        assert re.search(r"'done'\s*=>\s*'implemented'", source)
