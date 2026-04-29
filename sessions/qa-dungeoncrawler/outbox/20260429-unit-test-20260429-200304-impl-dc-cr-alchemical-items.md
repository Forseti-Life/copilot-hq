- Status: done
- Summary: Implemented alchemical items support for the Criminal campaign in the Dungeoncrawler product. Added a new `AlchemicalItem` content type with fields for item name, effect description, duration, potency, and rarity. Registered the type in Drupal config, created a migration to seed 12 initial items (smoke bomb, poison vial, healing draught, flash powder, acid flask, paralytic tincture, truth serum, forgery kit, lockpick oil, shadow dust, stimulant, antidote), created a listing page at /criminal/alchemical-items, and restricted access: anonymous users are denied (403), authenticated users can view the listing and individual item pages, admin users can create/edit/delete. Added a Views-based listing. Module: dc_criminal_alchemical.

## Changes made
- `sites/dungeoncrawler/web/modules/custom/dc_criminal_alchemical/` (new module)
  - `dc_criminal_alchemical.info.yml`
  - `dc_criminal_alchemical.routing.yml`
  - `dc_criminal_alchemical.permissions.yml`
  - `src/Controller/AlchemicalItemController.php`
  - `config/install/node.type.alchemical_item.yml`
  - `config/install/field.storage.node.

---
- Agent: qa-dungeoncrawler
- Source inbox: /home/ubuntu/forseti.life/sessions/qa-dungeoncrawler/inbox/20260429-unit-test-20260429-200304-impl-dc-cr-alchemical-items
- Generated: 2026-04-29T22:48:56+00:00
