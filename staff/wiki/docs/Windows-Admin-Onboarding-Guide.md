# 🪟 Windows Server System Administrator Onboarding Guide

This guide introduces new system administrators to Windows Server (2022 and similar versions).  
We focus on graphical tools like Task Manager, Task Scheduler, Firewall, and Server Manager,  
with PowerShell used only when it is simpler.  
All explanations are step-by-step and beginner friendly.  

---

## 1. PowerShell Basics
PowerShell is Windows’ command-line and scripting environment.  
It is more powerful than the old Command Prompt.  
You will mostly use graphical tools, but PowerShell is handy for quick checks or bulk operations.  

Open it: **Start → Search → PowerShell → Right-click → Run as Administrator**  

Example commands:  
```powershell
Get-Process       # show running processes
Get-Service       # list services
Restart-Computer  # restart the server
```

---

## 2. Task Manager
Task Manager shows running apps, background services, and resource usage.  

Open it with **Ctrl+Shift+Esc** or **right-click the taskbar → Task Manager**.  

**Important Tabs:**  
- Processes → shows active apps and background services  
- Performance → CPU, Memory, Disk, Network usage  
- Users → who is logged in  
- Details → advanced process view (like Linux top)  
- Services → control background services  

---

## 3. Task Scheduler
Task Scheduler lets you run jobs automatically, similar to cron in Linux.  

Open it: **Start → Task Scheduler → Run as Administrator**  

**To create a task:**  
1. Click **Action → Create Basic Task**  
2. Enter a name (e.g., Nightly Backup)  
3. Choose a trigger (daily, weekly, etc.)  
4. Choose an action (run script, launch program)  
5. Finish → the task is ready  

---

## 4. Windows Firewall
The Windows Firewall controls which network traffic is allowed or blocked.  

Open it: **Start → Windows Defender Firewall with Advanced Security**  

**Example: Allow inbound port 27015 (for a game server):**  
- Inbound Rules → New Rule  
- Select **Port** → enter 27015  
- Allow the connection  
- Name it (e.g., CS Server Port)  

---

## 5. Server Manager
Server Manager is the main dashboard for configuring roles and features.  

It usually opens on login, but can also be launched from **Start → Server Manager**.  

**Key sections:**  
- Dashboard → overview of the server  
- Local Server → server properties and settings  
- Manage → Add Roles and Features → install services like IIS, DNS, File Server  

**Example: Installing IIS Web Server:**  
1. Open Server Manager  
2. Manage → Add Roles and Features  
3. Select Role-based installation  
4. Check IIS Web Server  
5. Next → Install  

---

## 6. System Monitoring Tools
- Event Viewer → check system/application logs  
- Resource Monitor → detailed CPU/Disk/Network usage  
- Performance Monitor → track usage over time  

---

## 7. Glossary of Windows Terms

| Term          | Meaning |
|---------------|---------|
| PowerShell    | Command-line + scripting environment |
| Task Manager  | Monitors processes and resources |
| Task Scheduler| Automates tasks and scripts |
| Firewall Rule | Controls network traffic permissions |
| Role          | Server’s main function (ex: Web, File, DNS) |
| Feature       | Additional capability (ex: .NET, Telnet) |

---

## 8. Best Practices for New Admins
- Always run PowerShell as **Administrator** if changing settings  
- Document every firewall rule you add  
- Check Task Manager before rebooting to confirm what’s stuck  
- Use Server Manager for installs, not random downloads  
- Test scheduled tasks manually once before relying on them  
