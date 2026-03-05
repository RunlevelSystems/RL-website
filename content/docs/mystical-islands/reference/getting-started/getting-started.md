<!-- Developed by World Domination Software LLC -->
---
title: "Getting Started"
description: "Atavism reference import"
weight: 100
---

> **Admin Documentation (not shown on public WDS site).**
> GSP is a heavily customized fork of OGP maintained by WDS.

## Prerequisites

- **Unity Editor:** 2021.3 LTS (matching Atavism 10.7+). Install via Unity Hub with Android/iOS build modules unchecked to keep install size small.
- **Atavism Package:** Download the licensed Atavism client/editor bundle from the WDS secure mirror (`ops-tools/atavism/`).
- **Database Access:** Read/write credentials for the shared `world_content` database (`atavism_admin@db.wds.internal`).
- **Server Credentials:** SSH access to staging Atavism nodes (documented in `ops-tools/ssh-inventory.md`).
- **Panel Integration:** Active account on the GSP panel with `Content Designer` role.

## Workspace Setup

1. **Clone Project Repos**
	- `GameServerPanel/GSP` (panel control)
	- `GSP_Agent_Linux` and `GSP-Agent-Windows` (remote agents)
	- `WDS_Website` (this repo for documentation and marketing site).
2. **Install Unity Dependencies**
	- Launch Unity Hub, add the specified LTS version, and confirm the **WebGL** module is installed (used for lightweight preview builds).
3. **Configure Atavism Editor**
	- Extract the editor bundle to `~/AtavismEditor`.
	- Copy the Mystical Islands override configuration from `ops-tools/atavism/mi-editor-config.json` into the editor root and rename to `CustomSettings.json`.
4. **Set Environment Variables** (append to `~/.bashrc`):

	```bash
	export MI_ATAVISM_DB_HOST=db.wds.internal
	export MI_ATAVISM_DB_USER=atavism_admin
	export MI_ATAVISM_DB_PASS='<vault lookup>'
	```

	Reload your shell (`source ~/.bashrc`).

## Initial Database Sync

1. Request a fresh dump from the DBA team (`world_content` + `master`).
2. Restore locally using `mysql --host $MI_ATAVISM_DB_HOST --user $MI_ATAVISM_DB_USER -p world_content < path/to/world_content.sql`.
3. Run the Mystical Islands delta script (`ops-tools/atavism/migrations/latest.sql`) to apply custom tables and enums introduced by WDS.
4. Confirm new data with quick sanity checks:
	- `SELECT COUNT(*) FROM abilities;`
	- `SELECT COUNT(*) FROM item_templates WHERE category='MysticalIslands';`

## Connecting the Editor

1. Launch the Atavism Editor and log in with your Atavism account (contact production for credentials).
2. Open **Edit → Preferences → Server** and verify:
	- **Master Server** points to the current staging endpoint.
	- Database settings match the environment variables above.
3. In **File → Open Project**, navigate to the Mystical Islands Unity project folder stored in the private repo (`MysticalIslands/Client`).
4. Let Unity import the packages; expect 10–15 minutes on a fresh machine.

## Validation Checklist

- [ ] Unity compiles with no console errors.
- [ ] Atavism Editor connects and lists Mystical Islands item templates.
- [ ] `world_content` tables show Mystical Islands categories.
- [ ] Git access confirmed for all repos.

Once everything is green, move on to `setting-up-atavism` to provision or refresh shards, followed by `creating-item-templates` for day-to-day content authoring.


