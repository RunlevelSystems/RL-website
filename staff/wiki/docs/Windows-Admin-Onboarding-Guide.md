# Windows Server Basics

A simple guide to managing Windows servers for new team members.

---

## PowerShell

PowerShell is the command-line for Windows. You'll use it for quick tasks.

Open it: **Start → Search → PowerShell → Right-click → Run as Administrator**

Useful commands:
```powershell
Get-Process       # Show running programs
Get-Service       # List services
Restart-Computer  # Restart the server
```

---

## Task Manager

Shows what's running and how busy the server is.

Open with **Ctrl+Shift+Esc** or right-click the taskbar.

Important tabs:
- **Processes** - Active programs
- **Performance** - CPU, memory, disk usage
- **Services** - Background programs

---

## Task Scheduler

Runs tasks automatically (like backups or maintenance).

Open: **Start → Task Scheduler**

To create a task:
1. Click **Action → Create Basic Task**
2. Give it a name
3. Choose when to run it (daily, weekly, etc.)
4. Choose what to run (a script or program)
5. Click Finish

---

## Windows Firewall

Controls what network traffic is allowed.

Open: **Start → Windows Defender Firewall with Advanced Security**

To allow a port (example: game server port 27015):
1. Click **Inbound Rules → New Rule**
2. Select **Port**
3. Enter the port number
4. Allow the connection
5. Give it a name

---

## Server Manager

The main dashboard for configuring the server.

Key sections:
- **Dashboard** - Server overview
- **Local Server** - Settings
- **Manage → Add Roles** - Install features like web servers

---

## Useful Tips

1. Always run PowerShell as **Administrator** when changing settings
2. Document every firewall rule you add
3. Check Task Manager before rebooting
4. Use Server Manager for installing features
5. Test scheduled tasks manually first
