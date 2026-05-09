<!-- Developed by Core Loop Development LLC -->
---
title: "Atavism Windows Manager"
description: "Atavism reference import"
weight: 100
---

> **Admin Documentation (not shown on public Core Loop site).**
> GSP is a heavily customized fork of OGP maintained by Core Loop.

## Purpose

The Atavism Windows Manager (AWM) is a lightweight launcher Core Loop uses on Windows QA rigs to manage client builds, apply patches, and launch the Mystical Islands test client with preconfigured arguments.

## Installation

1. Download the latest AWM installer from `ops-tools/atavism/windows-manager/AtavismWindowsManagerSetup.msi`.
2. Run the installer with administrative privileges.
3. When prompted, choose the **Mystical Islands** profile; this pulls default endpoints from the Core Loop CDN.

## Configuration Files

- **Config Path:** `C:\ProgramData\Atavism\WindowsManager\settings.json`
- **Key Settings:**
	- `realm`: Name shown in the launcher (e.g., `MI Staging`)
	- `autoupdate`: Ensure this remains `true` so QA receives nightly builds
	- `client_path`: Where the Mystical Islands client is installed
	- `log_level`: Set to `debug` when submitting bug reports

Example snippet:

```json
{
	"realm": "MI Staging",
	"autoupdate": true,
	"client_path": "D:\\Games\\MysticalIslands\\Client",
	"launch_args": "-nolog -force-d3d11",
	"log_level": "info"
}
```

## Updating Client Builds

1. Place the new client ZIP on the Core Loop CDN under `/mystical-islands/builds/<version>/`.
2. Update the manifest file `manifest.json` in the same directory.
3. Increment the `build_version` field in `settings.json` or push via the remote config service.
4. QA rigs will auto-download on next launcher start.

## Command-Line Usage

AWM exposes a CLI for automation:

```powershell
"C:\Program Files\Atavism\WindowsManager\AtavismWindowsManager.exe" --profile "MI Staging" --update --launch
```

- `--update` downloads any pending patches.
- `--launch` starts the Mystical Islands client immediately after update.

Integrate this command into Windows Task Scheduler for nightly smoke tests.

## Logs & Troubleshooting

- Logs stored at `C:\ProgramData\Atavism\WindowsManager\logs\YYYY-MM-DD.log`.
- Common issues:
	- **Permission Denied:** Run as Administrator or grant modify rights to the install folder.
	- **Download Failures:** Verify CDN URL and firewall rules; proxy details configured in the Windows Internet Options control panel.
	- **Launcher Crash:** Enable debug logging and attach the report to the Jira ticket.

Refer to the `troubleshooting` guide for deeper networking diagnostics and escalation procedures.


