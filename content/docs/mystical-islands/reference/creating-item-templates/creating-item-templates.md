<!-- Developed by World Domination Software LLC -->
---
title: "Creating Item Templates"
description: "Atavism reference import"
weight: 100
---

> **Admin Documentation (not shown on public WDS site).**
> GSP is a heavily customized fork of OGP maintained by WDS.

## Workflow Summary

1. Prepare references in the Mystical Islands Unity project (prefabs, icons, effects).
2. Author the item template in Atavism Editor → **Content → Items → Item Templates**.
3. Associate crafting, loot tables, and vendors where appropriate.
4. Sync changes to the database and export a JSON snapshot for version control.

## Required Data Fields

| Field | Notes | Mystical Islands Convention |
| --- | --- | --- |
| **Name** | Player-facing string | Prefix with island code for internal search, e.g. `MI-VERDANT Longbow` |
| **Template ID** | Auto-generated | Record in design bible `items.csv` |
| **Category** | Drives equip slots & restrictions | Use `Equipment`, `Consumable`, `Quest`, or `Housing` |
| **Icon** | 64×64 PNG | Stored under `Assets/MysticalIslands/UI/Icons/Items/` |
| **Equip Slot** | Weapon, Armor, Accessory, etc. | Align with the `stat_definitions` table |
| **Stats** | JSON map | Use the Mystical Islands stat keys (`Power`, `Ward`, `Sailing`, etc.) |
| **Abilities** | Optional trigger ability | Reference ability ID from `abilities` table |

## Step-by-Step

1. **Create Unity Assets**
	- Generate prefabs or ScriptableObjects as needed.
	- Export icon using `MI → Tools → Capture Icon` to ensure consistent padding.

2. **Open Atavism Editor**
	- Navigate to **Content → Items → Item Templates**.
	- Click **Add New**; supply name, icon, category.

3. **Configure Stats**
	- Under the **Stats** tab, click **Add Stat** and choose from the drop-down.
	- For percentage stats, set `Is Percent` to true and store values such as `0.05` for +5%.

4. **Attach Abilities** (if required)
	- Set **Trigger Ability** to the relevant ability ID (see `abilities` doc) for use-on-equip or activated items.

5. **Durability & Sockets**
	- Enable durability for items that degrade. Use the `MI_DEFAULT_DURABILITY` constant from the design bible unless the item is explicitly resilient.
	- Configure socket counts for gem systems when applicable and ensure the `item_enchantments` table includes the referenced gems.

6. **Save & Publish**
	- Click **Save** to push to the database.
	- Run `Content → Tools → Export Selected` to generate a JSON export stored in `~/AtavismExports`.

## Version Control of Items

- Store JSON exports in the private Mystical Islands design repository under `data/atavism/items/`.
- Commit changes alongside gameplay notes describing balance adjustments.
- Update the shared `items.csv` spreadsheet with template ID, rarity, and drop sources.

## Integration Points

- **Loot Tables:** Use `Content → Combat → Loot Tables` to add the new item to mobs or containers.
- **Crafting Recipes:** `Content → Crafting → Recipes` to create recipes that output the new item.
- **Vendors:** `Content → NPC → Merchants` for vendors selling the item.

## Quality Checks

- [ ] Icon displays correctly in Atavism Editor preview.
- [ ] Equip/unequip scripts fire expected abilities.
- [ ] Item appears in the Mystical Islands client after database sync.
- [ ] JSON export committed with descriptive changelog entry.

Refer to `advanced-editing` for UMA gear assignment, animation overrides, and conditional visibility rules tied to item states.


