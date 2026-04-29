- Status: done
- Summary: Implemented Half-Elf Heritage trait selection for Dungeoncrawler character creation. Added `HalfElfHeritageSelector` Drupal custom module with a single-field form that appends `[HALF-ELF HERITAGE: <trait>]` to the active character's system prompt and stores the selection in `dungeoncrawler_character_meta` under the key `half_elf_heritage`. All changes are isolated to the new module; no existing modules were modified. Feature is gated behind Drupal permissions so only authenticated users with valid character context can submit.

## Implementation notes
- New module: `web/modules/custom/half_elf_heritage_selector`
- Form class: `HalfElfHeritageForm` at `src/Form/HalfElfHeritageForm.php`
- Route: `GET/POST /dungeoncrawler/character/half-elf-heritage`
- Permission: `select half-elf heritage`
- Menu link registered under Dungeoncrawler character tools
- System prompt append: `[HALF-ELF HERITAGE: <trait>]`
- Meta storage: `dungeoncrawler_character_meta` table, key `half_elf_heritage`
- Trait options (from acceptance criteria): Human Versat

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260429-unit-test-20260429-200305-impl-dc-cr-half-elf-heritage
- Generated: 2026-04-29T23:16:54+00:00
