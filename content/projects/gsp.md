---
title: "GSP (GameServer Panel)"
description: "World Domination Software's unified control panel and storefront for GameServers World"
weight: 5
---

GSP (GameServer Panel) is WDS's production control panel and storefront that powers **Gameservers World (GSW)**. The platform is a heavily customized fork of Open Game Panel, maintained and extended exclusively by World Domination Software.

## Why GSP
- Unified billing + provisioning for Linux and Windows agents
- Multi-remote orchestration, XML-driven game catalog, and automation-friendly APIs
- Hardening based on the Disaster Recovery toolkit (`WDS-Team/tools`)

## Project Links
- **Admin documentation hub** (private): `content/docs/gsp/` (see below)
- **Production hosting brand**: [Gameservers World](https://gameservers.world) (links back to this page for deep dives)
- **Source repos**: `GSP/`, `GSP_Agent_Linux/`, `GSP-Agent-Windows/`, `WDS-Team/`

## Admin Docs overview
Each admin guide is marked “Admin Documentation (not shown on public GSP end-user site)” and mirrors the latest bootstrap scripts.

- [Install: Ubuntu 24.04 Panel](../docs/gsp/install-ubuntu-panel.md)
- (coming soon) Ubuntu agent install/upgrade
- (coming soon) Windows/Cygwin agent install/upgrade
- (coming soon) Panel upgrades, Adminer/MySQL access, file browser, FTP/SFTP, scheduler, sub-users, DR/monitoring

## Next steps
1. Finish the remaining admin guides and Docker/dev stack described in the workspace brief.
2. Link this project page from `projects-current.html` and `index.html` once copy is approved.
3. Keep `.github/copilot-instructions.md` (GSP repo) aligned with these goals when new automation is added.
