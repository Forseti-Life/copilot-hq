# Feature Brief: Languages System

- Work item id: dc-cr-languages
- Website: dungeoncrawler
- Module: dungeoncrawler_content
- Status: deferred
- Defer reason: 2026-04-28 release recovery decision: release-x is being closed as an empty release. This feature still carries unresolved implementation/verification gaps in the language-selection flow and will be re-scoped in a later cycle once the full AC package is ready.
- Audit note: 2026-04-27 implementation audit found only partial coverage. Base ancestry language lists and read-side consumers exist, but no character-creation language selection flow, no applied Intelligence-based bonus language selection, and feat effects still note pending additional language selections.
- Priority: P2 (blocks dc-cr-elf-ancestry and dc-cr-elf-heritage-cavern; accepted for 20260407-dungeoncrawler-release-b)
- PM owner: pm-dungeoncrawler
- Dev owner: dev-dungeoncrawler
- QA owner: qa-dungeoncrawler
- Source: PF2E Core Rulebook (Fourth Printing), lines 1–300
- Category: game-mechanic
- Created: 2026-02-26

## Goal

Track which languages a character can speak, read, and write. Ancestry and Intelligence modifier determine starting languages; additional languages can be gained through feats and class features. Languages gate certain skill checks, social interactions, and lore access in dungeoncrawler scenarios.

## Source reference

> "Details on languages also appear in this chapter." (Chapter 2: Ancestries & Backgrounds)

## Implementation hint

Field on the character entity: `languages[]` (list of string identifiers). Content type or taxonomy: `language` with fields for language name, script, and typical speakers. Ancestry data should include default languages. Intelligence bonus at character creation adds free language slots. Simple UI: multi-select from available languages list.

## Security acceptance criteria

- Authentication/permission surface: authenticated users only; character ownership enforced via `_character_access: TRUE` on all character-scoped routes
- CSRF expectations: all POST/PATCH routes require `_csrf_request_header_mode: TRUE`
- Input validation: entity field types enforced at Drupal entity layer; mutations server-validated against allowed values
- PII/logging constraints: no PII logged; gameplay action logs (character id, action type) only

## Mission alignment

- [x] Aligns with democratized community game experience
- [x] Does not add surveillance or restrict community access
