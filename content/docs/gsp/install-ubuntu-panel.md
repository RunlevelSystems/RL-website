<!-- Developed by Core Loop Development LLC -->
---
title: "Install: Ubuntu 24.04 Panel"
description: "Bootstrap the GSP panel stack on Ubuntu 24.04 LTS"
weight: 20
---

> **Admin Documentation (not shown on public GSP end-user site).** Internal deployment checklist for Core Loop ops.

GSP is deployed on Ubuntu 24.04 LTS hosts via the idempotent script at `GSP/bootstrap/ubuntu-24.04/install_panel.sh`. The script adheres to these workspace guardrails:

- `ssh` listens on port **12322** (pre-created in `panel.conf` and firewall rules).
- Database accounts: `localuser@localhost` and `remoteuser@<reporter-ip>` share the password stored in `/home/gameserver/tools/.password`.
- Heritage note: GSP is a heavily customized fork of OGP maintained by Core Loop; upstream wiki references are for context only.

## Prerequisites
1. Ubuntu 24.04 LTS with sudo access.
2. DNS pointed at the host (panel + optionally billing storefront).
3. `/home/gameserver/tools/.password` populated with the shared secret synced via the DR toolkit.
4. Local copy of the GSP repo (`Panel-unstable` branch) and this workspace's DR scripts.

## Fresh host install (script-driven)
```bash
ssh -p 12322 admin@panel-host
sudo apt update
sudo apt install -y git
cd /opt
sudo git clone https://github.com/GameServerPanel/GSP.git gsp
cd gsp/bootstrap/ubuntu-24.04
sudo ./install_panel.sh --config ../config/panel.yml \
  --db-host localhost --db-name gsp_panel \
  --db-user localuser --db-pass "$(sudo cat /home/gameserver/tools/.password)"
```

What the script does:
- Installs Apache, PHP 8.3+, MariaDB client, Adminer, required PHP extensions, and firewall rules for 80/443/12322.
- Creates/updates the `gsp_panel` database using `panel.sql` (idempotent migrations live under `scripts/migrations/`).
- Deploys the panel code under `/var/www/gsp`, adjusts `panel.conf`, and enables the cron entry for billing automation.
- Registers systemd services or timers defined under `bootstrap/ubuntu-24.04/templates/` (Adminer, DR sync, optional watchdogs).

## Existing host upgrade path
1. Pull latest `Panel-unstable` branch to `/var/www/gsp`.
2. Run the bootstrap script with `--upgrade-only --keep-config` so it refreshes dependencies, database migrations, and cron entries without nuking `panel.conf`.
3. Update `modules/billing/timestamp.txt` to reflect the deployment time.
4. Re-run `tools/setup_mysql_users.sh` from `Core Loop-Team` if `servers.txt` changed.

## Post-install verification
- Login to `https://panel-host/` as the seeded admin account and confirm the footer shows `Last updated at YYYY-MM-DD HH:MM:SS`.
- Confirm Adminer is reachable at `/adminer.php` using `localuser@localhost` and the `.password` secret.
- Use the File Browser to upload a test file and verify permissions.
- Launch the scheduler (cron job) test: add a dummy task, force-run it, and see logs in `modules/scheduler/logs/`.
- Register at least one remote agent (Linux or Windows) and confirm its heartbeat inside `home.php?m=adminserverlist`.
- Deploy a sample game server via the provisioning wizard, start it, and check `server_status.php`.

## Rollback / disable
- `sudo systemctl stop apache2 php*-fpm` to take the panel offline.
- Disable cron jobs by removing the `gsp_cron` entry under `/etc/cron.d/` (the bootstrap creates a labelled file).
- Restore `/var/www/gsp` from the latest DR snapshot and re-run the bootstrap script with `--upgrade-only` to reapply permissions.
- If the host must be removed entirely, revoke DNS, run `mysql DROP DATABASE gsp_panel`, and rotate the `.password` secret via `Core Loop-Team/tools/check_servers.sh --password NEW`.
