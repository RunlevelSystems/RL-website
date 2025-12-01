---
title: "Game Settings Plugin"
description: "Atavism reference import"
weight: 100
---

> **Admin Documentation (not shown on public WDS site).**
> GSP is a heavily customized fork of OGP maintained by WDS.

## Purpose

The Game Settings plugin exposes runtime configuration flags (XP rates, loot modifiers, sailing penalties) that designers can tweak without redeploying the server. Mystical Islands extends the stock Atavism plugin with additional categories for island-specific gameplay.

## Accessing the Plugin

- **Atavism Editor:** `Server → Game Settings`
- **Database Tables:** `game_settings` and `game_setting_categories`
- **Runtime Console:** `/gamesetting set <key> <value>` (GM only)

## Key Mystical Islands Settings

| Key | Default | Description |
| --- | --- | --- |
| `MI_Global_XP_Modifier` | `1.0` | Multiplier for all XP rewards |
| `MI_Sailing_Speed_Modifier` | `1.0` | Applied to ship movement calculations |
| `MI_Storm_Frequency` | `0.25` | Percent chance storms spawn per interval |
| `MI_Loot_Rarity_Bonus` | `0.0` | Additional drop-rate bonus for rare loot |
| `MI_Guild_Housing_Upkeep` | `500` | Gold per week charged to guild islands |

## Editing Workflow

1. Open the Atavism Editor and select the **Game Settings** module.
2. Find the category (e.g., `MysticalIslands`) and select the setting to edit.
3. Adjust the value and add a descriptive comment noting the reason for change and Jira ticket.
4. Click **Save** to write to the database. Changes apply immediately to connected clients.

## Change Management

- Record all adjustments in the `MI Game Settings` Confluence page, including date, operator, and expected impact.
- For high-risk changes (XP, loot), coordinate with analytics to monitor live metrics for 24 hours.
- Use the `--dry-run` flag on the Copilot `gs-settings` script before applying wide adjustments.

## Scripting Access

Mystical Islands scripts can read settings through helper functions:

```python
from mi_common import get_game_setting

def calculate_storm_spawn():
	freq = get_game_setting("MI_Storm_Frequency", default=0.25)
	return random.random() < freq
```

Place helper calls in Atavism server scripts or Unity client code that needs to respect live-tuned values.

## Testing

- [ ] Modify a setting in staging and confirm immediate effect (e.g., temporarily double XP and clear a mob).
- [ ] Verify `/gamesetting list` reflects new values.
- [ ] Review server logs (`/opt/atavism/server/logs/world.log`) for confirmation messages.
- [ ] Reset settings to defaults after testing to keep staging baseline predictable.


