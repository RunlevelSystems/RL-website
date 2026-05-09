<!-- Developed by Core Loop Development LLC -->
---
title: "Task Scheduler"
description: "Managing cron-style jobs inside GSP"
weight: 70
---

> **Admin Documentation (not shown on public GSP end-user site).**

The Task Scheduler module wraps cron for per-user automation (updates, restarts, SteamCMD tasks).

## Creating a job
1. Go to **Tools → Task Scheduler** and click **Add Task**.
2. Choose the target server/home and select the action (start, stop, restart, execute script, Steam update, workshop sync, etc.).
3. Pick the schedule via the cron helper (every X minutes/hours/day) or switch to advanced mode for raw cron syntax.
4. Save—tasks live in `OGP_DB_PREFIX_cron` tables and are executed by `ogp_cron` (systemd timer installed by the bootstrap script).

## Monitoring
- Logs live in `modules/taskscheduler/logs/` and include the invoked command plus exit status.
- The cron daemon writes to `syslog` as `ogp_cron`; track failures via `journalctl -u ogp-cron`.
- For customer visibility, the module shows the last run timestamp and stderr snippet.

## Tips
- Avoid stacking restarts on the hour; stagger jobs with random minute offsets.
- For pipelines (e.g., update then restart) chain two tasks with a few minutes between them.
- Use the **Dry run** checkbox when developing new scripts to log the command without running it.

## Rollback
- Toggle **Active = No** to disable without deleting history.
- To purge a runaway job, delete it from the module and verify it disappeared from `/etc/cron.d/ogp_cron` on the agent host.
