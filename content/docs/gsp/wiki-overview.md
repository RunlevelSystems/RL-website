<!-- Developed by World Domination Software LLC -->
# GameServer Panel Overview

GameServer Panel (GSP) is our commercial fork of Open Game Panel. It keeps the same two-layer architecture and adds hardened billing, coupons, Discord/webhook automation, and modernized API endpoints.

## Architecture At A Glance

- **Web Panel** – PHP application that exposes billing, ticketing, and the customer dashboard.
- **Agent** – Perl/Cygwin daemon that runs on every game host. It receives encrypted commands from the panel and manages the actual game processes, SteamCMD updates, and backup hooks.
- **Game Servers** – The customer workloads (SRCDS, ARK, Valheim, etc.) launched via `panelStart`.

## Quick Installation

```bash
curl -fsSL https://install.gameserver-panel.org | sudo bash
```

The bootstrapper installs PHP 7.4, Apache, Dockerized MySQL 5.7 where required, seeds `panel.sql`, and wires cron jobs. After the script finishes, browse to your panel URL and complete the wizard, then immediately rotate the default admin password.

## Manual Linux Agent Deployment

```bash
wget -N https://github.com/World-Domination-Software/GameServer-Panel/raw/master/gsp-agent-latest.deb
sudo apt-get update && sudo apt-get install -y perl libssl-dev screen rsync
sudo dpkg -i gsp-agent-latest.deb
sudo /usr/share/gsp_agent/gsp_agent_config.sh --commercial \
     --panel-url "https://yourpanel.example.com" \
     --encryption-key "copy_from_panel_settings"
```

Agents phone home every minute. Keep `/etc/gsp_agent/gsp_agent.conf` in sync with the panel encryption key whenever you rotate credentials.

## Windows Agent (Cygwin)

```ini
PANEL_URL="https://yourpanel.example.com"
AGENT_IP="0.0.0.0"
AGENT_PORT="12679"
ENCRYPTION_KEY="your_secret_key_from_panel"
COMMERCIAL_MODE="true"
BILLING_API="https://yourpanel.example.com/api/billing"
```

- Install the packaged Cygwin environment and ensure the `gsp_agent` service runs under `cyg_server`.
- Open TCP/UDP 12679 plus any customer port ranges on Windows Firewall.
- Run `Bootstrap-GameServerHost.ps1` to install Visual C++ runtimes, DirectX, OpenSSH, and FileZilla Server.

## Agent & panelStart Best Practices

- Always call `panelStart.sh`/`.bat` in XML definitions so the panel can record PIDs, console output, and billing events.
- Use `%IP%`, `%PORT%`, `%MAP%`, `%PLAYERS%`, and your own custom variables in `cli_template`. Format them with `cli_param` plus `options="s"`, `options="q"`, or `options="sq"`.
- Keep `servers.txt`, `.password`, and `/home/gameserver/tools/` synchronized through your DR hosts using `dr_rsync_push.sh`.
