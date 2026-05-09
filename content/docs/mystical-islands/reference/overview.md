<!-- Developed by Core Loop Development LLC -->
---
title: "Overview"
description: "Atavism reference import"
weight: 100
---

> **Admin Documentation (not shown on public Core Loop site).**
> GSP is a heavily customized fork of OGP maintained by Core Loop.

## Scope

Mystical Islands relies on the Atavism MMO platform for server-side gameplay systems, instancing, and data-driven content. These admin notes consolidate the most frequently referenced Atavism guides in a single place so design, engineering, and live-ops staff can keep Copilot grounded in the same vocabulary when producing Unity behaviors or panel-side automations.

- **Audience:** Internal Core Loop designers, engineers, and toolsmiths.
- **Source:** Sanitized from the public Atavism wiki exports as of 2025-11-30 and cross-checked against our `world_content` database snapshot.
- **Focus:** Item, ability, and prefab authoring workflows required for rapid content iteration.

## Directory Map

```
content/docs/mystical-islands/reference/
├── overview.md (this page)
├── getting-started/
├── setting-up-atavism/
├── atavism-client-unity-installation/
├── atavism-windows-manager/
├── external-packages/
├── game-settings-plugin/
├── creating-item-templates/
├── advanced-editing/
└── troubleshooting/
```

Each folder contains a single Markdown guide with the original slug preserved so in-editor search and Copilot embeddings stay stable between exports.

## How to Use These Notes

1. **Read `getting-started` first** to configure local prerequisites (Unity version, Atavism editor, database credentials).
2. **Follow `setting-up-atavism`** when bootstrapping a new Mystical Islands shard or refreshing staging configurations.
3. **Use `creating-item-templates` and `advanced-editing`** as quick references while authoring items, abilities, and UMA gear prefabs.
4. **Reference `external-packages` and `game-settings-plugin`** when pulling optional modules into the game server.
5. **Keep `atavism-windows-manager` close** for Windows-based test rigs that need the Atavism launcher lifecycle.
6. **Bookmark `troubleshooting`** for common issues seen during daily content iteration.

## Cross-System Dependencies

- **GSP Panel Integration:** Content created here must match the provisioning templates inside `GameServerPanel/GSP/modules/config_games/server_configs/atavism.xml`. Any new server role or port range requires mirrored updates between repos.
- **Database Schema:** See `world_content` SQL snapshots (kept in the secure SharePoint vault) when validating column names or migrating data. The key tables referenced in these docs are:
	- `abilities`, `ability_effects`, `effects`
	- `item_templates`, `item_enchantments`
	- `stat_definitions`, `skill_definitions`
- **Remote Agent Hooks:** Ensure the Linux/Windows agents include the latest Atavism start/stop scripts referenced by the provisioning pipeline.

## Maintenance Expectations

- Re-run the `projects/mystical-islands/scripts/convert_raw_docs.py` tool after every significant Atavism release and manually review the output for markup drift.
- Update `modules/billing/timestamp.txt` in the GSP repo whenever documentation changes require a public-announcement sync.
- Log major doc revisions in the Core Loop operations weekly digest so support and community teams know which gameplay systems changed.

## Related Resources

- Core Loop Knowledge Base → `content/docs/gsp/` for panel administration specifics.
- Atavism release notes (licensed mirror on internal Confluence).
- Mystical Islands design bible (`content/projects/mystical-islands/design/` in the private repo).

Need to add a new topic? Mimic the folder structure above, keep the admin banner, and cross-link it from the project Table of Contents described on `projects/mystical-islands/index.php`.


