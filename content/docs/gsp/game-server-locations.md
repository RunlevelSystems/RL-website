<!-- Developed by World Domination Software LLC -->
---
title: "Game Server Locations & IPs"
description: "Current WDS game server hostnames, IP addresses, and roles"
weight: 20
---

> **Admin Documentation (not shown on public GSP end-user site).**
>
> This page tracks the primary WDS game infrastructure endpoints. Use these
> hostnames when configuring the panel, monitoring, or routing player traffic.

## Production endpoints

Naming pattern: `location-game-#` (for example, `la-game-1`, `kc-game-2`).

| Hostname                    | IP address        | Location            | Role                            |
|-----------------------------|-------------------|---------------------|---------------------------------|
| `la-game-1.iaregamer.com`   | `107.174.126.118` | Los Angeles, USA    | Game server / Matrix host       |
| `dub-game-1.iaregamer.com`  | `23.94.209.15`    | Dublin, Ireland     | Game server                     |
| `kc-game-2.iaregamer.com`   | `192.151.145.162` | Kansas City, USA    | Game server                     |
| `dal-game-1.iaregamer.com`  | `155.94.173.138`  | Dallas, USA         | Game server                     |
| `nyc-game-1.iaregamer.com`  | `75.127.4.194`    | New York City, USA  | Game server                     |
| `chat.iaregamer.com`        | `107.174.126.118` | Los Angeles, USA    | Chat / Matrix client endpoint   |
| `matrix.iaregamer.com`      | `107.174.126.118` | Los Angeles, USA    | Matrix / Element homeserver     |

Notes:

- `matrix.iaregamer.com` and `chat.iaregamer.com` currently run on the same
  node as `la-game-1.iaregamer.com` (`107.174.126.118`).
- SSH management for these hosts typically uses the non-standard port `12322`
  (e.g., `ssh -p 12322 gameserver@la-game-1.iaregamer.com`).
- When adding new locations, update this table and any related monitoring
  configuration (e.g., `ops-tools/servers.txt`).
