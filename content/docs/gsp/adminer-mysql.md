<!-- Developed by Core Loop Development LLC -->
---
title: "MySQL via Adminer"
description: "Secure database access patterns for GSP"
weight: 50
---

> **Admin Documentation (not shown on public GSP end-user site).**

Adminer replaces phpMyAdmin in our stack and lives at `/adminer.php` on every panel host.

## Credentials & ports
- **localuser@localhost** – full privileges for on-box admin tasks (Adminer default login). Password: contents of `/home/gameserver/tools/.password`.
- **remoteuser@<reporter-ip>** – read/reporting-only, IP-restricted via `servers.txt`. Use this for monitoring/reporting servers only.
- MariaDB listens on `127.0.0.1:3306`; remote queries must go through SSH tunnels.

## Login flow
1. Visit `https://panel-host/adminer.php` (TLS required) and accept the GSP certificate.
2. Enter the hostname (`localhost`), database (`gsp_panel`), username (`localuser`), and the shared password.
3. Enable **Permanent login** only on bastion hosts; never on customer browsers.
4. For reporting hosts, open an SSH tunnel: `ssh -p 12322 -L 3307:127.0.0.1:3306 admin@panel-host` then aim Adminer/Desktop client at `127.0.0.1:3307` using `remoteuser`.

## Operations
- Export/import via Adminer’s “Dump” uses per-table toggles; keep SQL dumps in `/home/gameserver/backups/mysql/` and sync via DR toolkit.
- When touching panel tables such as `gsp_remote_servers`, document changes in `docs/wds-gsp-migration.md`.
- Rotate passwords with `Core Loop-Team/tools/check_servers.sh --password NEW`; the script updates MySQL and redistributes `.password`.

## Verification & rollback
- After schema changes, run panel health check: `php scripts/validate_schema.php` inside the GSP repo.
- Keep at least two dumps per host; to roll back a change, restore the latest dump via Adminer and re-run bootstrap migrations with `--upgrade-only`.
