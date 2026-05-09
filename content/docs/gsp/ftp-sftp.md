<!-- Developed by Core Loop Development LLC -->
---
title: "FTP & SFTP"
description: "File transfer options for GSP customers"
weight: 60
---

> **Admin Documentation (not shown on public GSP end-user site).**

## Defaults
- **SFTP (recommended):** Port `12322`, username = panel username, password = panel password. Backed by the same `opengamepanel_web` session logic.
- **FTP (legacy):** ProFTPD runs chrooted per home, port `21` with TLS required. Accounts are provisioned via `ogp_api.php` when a server/home is created.

## Bringing a customer online
1. Ensure the remote agent has `ftp` support enabled (`ogp_agent.conf → ftp = yes`).
2. In the panel, open **User → My Servers → FTP Details**; verify the auto-generated credentials.
3. If the customer needs multiple logins, clone them via **Administration → FTP Accounts** and associate with the same home.
4. Share the **SFTP first** instructions: `sftp -oPort=12322 user@panel-host` (keys supported).

## Troubleshooting
- Port blocked: confirm `ufw` or upstream firewall exposes `12322` and `21`.
- Password out of sync: re-save the user in **Administration → Users** to force a sync of `users_pass_hash` and FTP credentials.
- Transfer stuck: check `/home/ogp_agent/OGP_User_Files/<home>/.ftpquota` and increase the quota in the panel if needed.

## Rollback
- Disable an account by setting **Active = No** inside FTP Accounts; this keeps audit history.
- Remove stale chroots under `/home/ogp_agent/OGP_User_Files/` only after confirming backups exist.
