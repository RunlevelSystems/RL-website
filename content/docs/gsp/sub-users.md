---
title: "Sub-users & Permissions"
description: "Delegate panel access safely"
weight: 80
---

> **Admin Documentation (not shown on public GSP end-user site).**

## Creating a sub-user
1. As an admin, visit **Administration → Users → Add sub-user**.
2. Select the parent (owning) account and define the username + email for the delegate.
3. Assign the target servers/homes and check the allowed modules (File Browser, Task Scheduler, FTP, etc.).
4. Save—the sub-user inherits billing status but only sees what you selected.

## Permissions model
- Rights are additive; checking “Edit Server” implies start/stop + configuration access.
- FTP credentials follow the parent account; revoke FTP separately if needed.
- Audit logs store both the sub-user ID and parent user ID for traceability.

## Lifecycle
- Temporarily suspend by toggling **Active = No**.
- Promote by editing the user and removing the parent link—this makes them a full account.
- Delete only when the parent confirms; this wipes stored permissions but leaves server ownership intact.

## Verification
- Use an incognito browser to log in as the sub-user and ensure only the intended servers/modules show up.
- Attempt unauthorized actions to confirm the UI hides them and the backend rejects direct URL access.
