<!-- Developed by World Domination Software LLC -->
---
title: "Setting Up Atavism"
description: "Atavism reference import"
weight: 100
---

> **Admin Documentation (not shown on public WDS site).**
> GSP is a heavily customized fork of OGP maintained by WDS.

## Provisioning Overview

Mystical Islands shards run on the standard Atavism cluster layout: **Master**, **World**, **Proxy**, and **Data** servers. WDS hosts these as Dockerized services orchestrated by the GSP provisioning pipeline.

1. **Panel Trigger** – Billing/Operations create a new Mystical Islands service in the GSP panel.
2. **Provisioner** – `modules/billing/includes/provisioner.php` calls the Atavism template scripts.
3. **Agent Execution** – The remote agent installs/upgrades the Atavism server bundle and registers the shard.
4. **Verification** – Support staff run the validation checklist below before opening access to designers.

## Required Files

- Atavism server bundle (`atavism_server_10.7_wds.tar.gz` stored in `ops-tools/atavism/`)
- Mystical Islands configuration overlay (`config/mystical-islands/` inside the server bundle)
- SSL certificates for Proxy → Client communication (issued via WDS internal CA)

## Deployment Steps

1. **Select Target Node**
	- Choose a Linux node with the Atavism role enabled (`remote_server_roles.atavism = 1`).
	- Confirm disk space > 40 GB free and memory > 16 GB.

2. **Launch Provisioning Job**
	- From the GSP panel, locate the order → Actions → *Install Mystical Islands Atavism*.
	- Monitor the job log in `modules/billing/logs/provisioner-*.log`.

3. **Database Setup**
	- The job imports baseline schema (`world_content`, `master`).
	- Apply Mystical Islands migrations automatically pulled from `ops-tools/atavism/migrations/`.
	- Seed data includes starter islands, default quests, and tutorial abilities.

4. **Service Configuration**
	- Environment variables set by provisioning:
	  - `MI_ENV=staging|production`
	  - `MI_REALM_NAME` (display name in Atavism login)
	  - `MI_PROXY_PUBLIC_HOST`
	- Config files written to `/opt/atavism/server/config/` with WDS overrides.

5. **Start Services**
	- `systemctl start atavism-master`
	- `systemctl start atavism-world`
	- `systemctl start atavism-proxy`

6. **Register Realm**
	- Use Atavism Editor → `Server Management → Realms` to confirm the new realm appears and is marked Active.

## Validation Checklist

- [ ] `systemctl status atavism-*` shows services active after 60 seconds.
- [ ] Proxy port 5052 reachable from VPN (`nc -vz host 5052`).
- [ ] Login with Atavism launcher returns the Mystical Islands realm list.
- [ ] Unity client can connect and load the starter island.
- [ ] `world_content.abilities` includes Mystical Islands entries (spot-check via SQL panel widget).

## Updating an Existing Shard

1. **Announce Downtime** in Discord `#mi-ops` and set the shard maintenance flag in the panel.
2. **Take Snapshot** using `ops-tools/atavism/backup.sh` to export the database and config.
3. **Pull Latest Bundle** – Upload new server tarball to `/opt/atavism/packages/` and update the `current` symlink.
4. **Run `upgrade.sh`** – Script performs schema migrations with rollback support.
5. **Smoke Test** – Repeat the validation checklist and lift the maintenance flag.

## Notes on Environments

- **Staging** mirrors production but with reduced mob density and short respawn timers for fast iteration.
- **Development** uses a single-node install (all roles on one machine) and can be provisioned manually using `install_atavism_dev.sh` in `ops-tools/`.
- **Production** nodes are paired for HA; ensure both sides are updated during maintenance windows.

## Related Documents

- `getting-started` – Workstation prep before touching servers.
- `advanced-editing` – Custom script hooks often require server restarts; coordinate with this runbook.
- `troubleshooting` – Quick reference for failed service states or login problems.


