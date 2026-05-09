<!-- Developed by Core Loop Development LLC -->
---
title: "Advanced Editing"
description: "Atavism reference import"
weight: 100
---

> **Admin Documentation (not shown on public Core Loop site).**
> GSP is a heavily customized fork of OGP maintained by Core Loop.

## Purpose

Use these notes when pushing beyond stock Atavism capabilities—custom UMA equipment rules, scripting hooks, and server-side behaviors tailored to Mystical Islands.

## UMA Equipment Displays

1. Create UMA-compatible meshes and slot data in Unity (`Assets/MysticalIslands/UMA/`).
2. In Atavism Editor, assign the display to the item template under **Equipment Display**.
3. For gender-specific or race-specific variants, create separate display entries and attach via the **Conditional Display** tab.
4. Test by spawning the item in-game (`/spawnitem <templateId>`) and verifying visibility across armor layers.

## Ability Script Hooks

- **Server Scripts:** Place custom scripts in `/opt/atavism/server/inline_scripts/` on the shard.
- **Event Types:** Common hooks include `ABILITY_ACTIVATED`, `ABILITY_RESOLVED`, `ITEM_EQUIPPED`.
- **Mystical Islands Toolkit:** Use helper functions defined in `mi_common.py` (deployed with the server bundle) for applying faction reputation, ship buffs, or environment effects.

### Example Pattern

```python
# pseudo-code for the Ability Activated hook
from mi_common import grant_sailing_mastery

def ability_activated(player, ability, context):
		if ability.id == 4021:  # Tempest Navigator's Chant
				grant_sailing_mastery(player, bonus=15, duration=300)
		return context
```

Remember to restart the **World** server after modifying inline scripts.

## Conditional States

- Configure **State Requirements** on abilities to gate usage by weather, biome, or ship status.
- Mystical Islands custom states include:
	- `STATE_IS_ON_SHIP`
	- `STATE_IS_IN_STORM`
	- `STATE_HAS_ANCIENT_KEY`

Assign these via `Content → Combat → States` and coordinate with quest designers to toggle them at runtime.

## Quest and Dialogue Extensions

- Dialogue nodes support custom Lua snippets stored under `quests/scripts/`.
- Use the shared library `mi_dialogue.lua` for common utility functions (quest flag checks, sailing timers, crew reputation).
- When referencing new quest variables, document them in the quest design sheet so analytics dashboards remain accurate.

## Client-Side Overrides

- Place custom shader variants in `Assets/MysticalIslands/Shaders/` and register them with Atavism's asset bundle manifest.
- UI adjustments should update both the Unity prefab and the Atavism GUI XML definitions found under `Assets/AtavismUnity/Content/UI/`.

## Testing Matrix

| Scenario | Steps | Expected Result |
| --- | --- | --- |
| UMA gear swap | Equip/unequip while sprinting, casting | Avatar swaps meshes without animation pop |
| Ability hook | Trigger ability in different biomes | Hook fires only under configured states |
| Dialogue script | Complete quest path | Variables set, rewards granted, no Lua errors |

## Deployment Notes

- Custom server scripts are part of the Atavism bundle; update `ops-tools/atavism/migrations/scripts_manifest.json` after adding new files.
- Unity asset changes require rebuilding the client bundle distributed to QA; coordinate via the weekly build cadence documented in `ops-tools/builds.md`.
- Always update `projects/mystical-islands/index.php` Table of Contents when adding new advanced workflows here.


