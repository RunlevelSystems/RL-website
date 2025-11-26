---
title: "File Browser"
description: "Manage game files inside the GSP panel"
weight: 40
---

> **Admin Documentation (not shown on public GSP end-user site).**

The built-in File Browser (`home.php?m=filemanager`) is the primary way for admins to seed configs, verify uploads, and help customers who do not have FTP ready.

## Usage
1. Go to **Tools → File Browser** and select the server/home you wish to inspect.
2. Use the breadcrumb to switch directories; double-click folders to enter them.
3. Actions:
   - **Upload** supports drag-and-drop + single file selection (up to PHP `upload_max_filesize`).
   - **Extract** handles `.zip` archives and preserves permissions.
   - **Edit/View** opens the built-in text editor for quick tweaks; it respects Unix line endings.
   - **Chmod** exposes the octal selector; default 755 for folders, 644 for files.

## Best practices
- Keep uploads under 500 MB; for larger transfers, switch to SFTP/rsync.
- Never leave world-writable files—set `umask 0022` in `ogp_agent.pl` if needed.
- Use the **Audit Log** button to capture who changed what (logs live under `modules/filemanager/logs/`).

## Verification
- After editing, click **Refresh** and confirm the new timestamp/size.
- Launch the corresponding game server to ensure config changes took effect.
- For customer escalations, capture a screenshot of the File Browser showing the resolved issue.
