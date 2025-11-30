---
title: "GSP (GameServer Panel)"
description: "World Domination Software's unified control panel and storefront for GameServers World"
weight: 5
---

GSP (GameServer Panel) is WDS's production control panel and storefront that powers **Gameservers World (GSW)**. The platform is a heavily customized fork of Open Game Panel, maintained and extended exclusively by World Domination Software.

> **Heritage:** GSP is a heavily customized fork of OGP maintained by WDS. We reference upstream docs for history, but this page reflects the live platform.

## Snapshot

| | |
| --- | --- |
| **Primary customer** | [Gameservers World](https://gameservers.world) |
| **Code repos** | `GSP/`, `GSP-Agent-Linux/`, `GSP-Agent-Windows/`, `WDS-Team/` |
| **Security defaults** | SSH port 12322, MySQL `localuser@localhost` / `remoteuser@&lt;reporter-ip&gt;`, shared secret in `/home/gameserver/tools/.password` |
| **Install targets** | Ubuntu 24.04+ (panel + agents), Windows Server 2019 via Cygwin (agents) |

## Why Teams Choose GSP

- **Single pane for billing + provisioning** – storefront orders flow straight into the panel via `BillingProvisioner`.
- **Multi-remote orchestration** – agents span Linux and Windows hosts, all driven by XML metadata under `modules/config_games/server_configs/`.
- **Automation ready** – cron/task scheduler, Adminer access, API hooks, and shipping DR automation (WDS-Team/tools) keep the fleet consistent.
- **Brand alignment** – storefront themes, FAQs, and docs consistently reference WDS, GSP, and GSW terminology.

## Admin Documentation (internal use only)

Every guide below starts with the “Admin Documentation (not shown on public GSP end-user site)” banner. Keep the content in sync with the bootstrap scripts in `GSP/bootstrap/`.

- [Install: Ubuntu 24.04 Panel](../docs/gsp/install-ubuntu-panel.md) – script-driven deployment + upgrade flow.
- [File Browser](../docs/gsp/file-browser.md) – safe editing, perms, and audit logging.
- [MySQL via Adminer](../docs/gsp/adminer-mysql.md) – credential patterns, tunnels, and rollback.
- [FTP & SFTP](../docs/gsp/ftp-sftp.md) – onboarding customers and diagnosing transfer issues.
- [Task Scheduler](../docs/gsp/task-scheduler.md) – creating, monitoring, and disabling jobs.
- [Sub-users & Permissions](../docs/gsp/sub-users.md) – delegate access while keeping ownership clear.
- *(coming soon)* Ubuntu agent install/upgrade, Windows/Cygwin agent install, panel upgrades, DR/monitoring, Docker/dev stack notes.

## Delivery Checklist

1. Finish the remaining admin docs + Docker/dev compose file described in the workspace brief.
2. Publish the Ubuntu/Windows agent bootstrap scripts and reference them from both the docs and `.github/copilot-instructions.md`.
3. Link this project page from `projects.php` and other public navigation once copy is approved.
4. Keep the WDS-Team migration tracker updated with every cross-repo change so ops can trace context quickly.
