<!-- Developed by Core Loop Development LLC -->
# Windows Agent + WINE Notes

GameServer Panel supports Windows workloads through two paths:

1. Native Windows Server hosts running the Cygwin-based agent.
2. Linux hosts running WINE/Proton for specific titles (legacy OGP technique).

## Native Windows Hosts

1. Install the packaged Cygwin bundle from the private downloads share.
2. Run `Bootstrap-GameServerHost.ps1` to install VC++ runtimes, DirectX, OpenSSH, and FileZilla Server.
3. Create or update `C:\cygwin64\home\gsp\gsp_agent.conf`:

```ini
PANEL_URL="https://yourpanel.example.com"
AGENT_IP="0.0.0.0"
AGENT_PORT="12679"
ENCRYPTION_KEY="your_secret_key_from_panel"
COMMERCIAL_MODE="true"
BILLING_API="https://yourpanel.example.com/api/billing"
```

4. Ensure the `gsp_agent` Windows service runs under `cyg_server` with “Log on as a service” rights.
5. Open TCP/UDP 12679 plus any customer ranges on Windows Firewall.

## Running Windows Games Under Linux (WINE)

The legacy WINE workflow from the original OGP project still works when you must consolidate on Linux hardware:

1. Install WINE, Winetricks, and Xvfb on your Debian/Ubuntu host.
2. Create a dedicated WINE prefix per game under `/home/gameserver/wineprefixes/<game>`.
3. Use SteamCMD + WINE to install the Windows dedicated server binaries inside that prefix.
4. Update the XML definition to call a wrapper script that exports `WINEPREFIX`, starts `Xvfb`, and finally runs the Windows binary via `wine64`.

> ⚠️ WINE hosting adds CPU overhead and complicates anti-cheat compatibility. Use it only when native Linux builds do not exist.

## Troubleshooting Checklist

- Verify the panel encryption key matches the agent file each time you rotate credentials.
- On Windows, check `C:\cygwin64\var\log\gsp_agent.log` for TLS or file permission issues.
- On Linux/WINE, inspect `panelStart.log` for `DISPLAY` and `WINEPREFIX` errors.
