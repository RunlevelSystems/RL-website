---
title: "Troubleshooting"
description: "Atavism reference import"
weight: 100
---

> **Admin Documentation (not shown on public WDS site).**
> GSP is a heavily customized fork of OGP maintained by WDS.

## Quick Reference Table

| Symptom | Likely Cause | Resolution |
| --- | --- | --- |
| Cannot log in, launcher times out | Proxy service down or firewall block | Check `systemctl status atavism-proxy`; ensure port 5052 open; restart service if needed |
| Realm list empty | Master service not registering realm | Restart `atavism-master`; verify `master.properties` realm entries |
| Items missing stats in game | Item template not synced or stat key mismatch | Re-open template, confirm stat keys match `stat_definitions`; save and export |
| UMA armor invisible | Equipment display missing or asset bundle stale | Rebuild UMA asset bundle; verify template display assignment |
| XP rewards wrong | Game setting changed | Check `MI_Global_XP_Modifier`; reset to 1.0 |

## Diagnostic Commands

```bash
# Check service status
sudo systemctl status atavism-master atavism-world atavism-proxy

# Tail server logs
sudo tail -f /opt/atavism/server/logs/world.log

# Verify database connectivity
mysql -h $MI_ATAVISM_DB_HOST -u $MI_ATAVISM_DB_USER -p -e "SELECT NOW();"
```

## Database Integrity Checks

- Run `ops-tools/atavism/scripts/validate_world_content.py` to confirm key tables (`abilities`, `item_templates`, `skill_definitions`) have required entries.
- Use Atavism Editor → `Tools → Validate Content` for UI-driven validation before pushing to production.

## Client Issues

- Clear the client cache located at `%APPDATA%\Atavism\Cache` (Windows) or `~/Library/Application Support/Atavism/Cache` (macOS).
- Delete `PlayerPrefs` entries if UI settings get corrupted (`MI` prefix).
- Re-run AWM with `--repair` flag to force clean install.

## Networking Problems

- Confirm VPN connection for internal environments.
- Use `mtr <proxy-host>` to trace packet loss when encountering high latency.
- Check CDN status page when client updates fail (`https://status.wds.dev`).

## Escalation Paths

1. Attempt self-service fixes listed above.
2. Open ticket in Jira (`MI-SUPPORT`) with logs and reproduction steps.
3. Page on-call engineer for production outages using the WDS Ops rotation.

## Preventative Maintenance

- Rotate Atavism logs weekly using the `logrotate` policy in `ops-tools/atavism/logrotate.conf`.
- Keep OS packages updated (`sudo apt-get update && sudo apt-get upgrade` during maintenance windows).
- Monitor metrics dashboards (`Grafana → Atavism → Realm Overview`) for early warning signs.

Cross-reference `setting-up-atavism` for service restart procedures and `game-settings-plugin` when tuning fixes require temporary configuration changes.


