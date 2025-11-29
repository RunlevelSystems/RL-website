# Linux Server Basics

A simple guide to managing Linux servers for new team members.

---

## Connecting to a Server

Use SSH to connect remotely:
```bash
ssh gameserver@server-address
```

Type **yes** when asked about the host key, then enter your password.

---

## Running Commands as Administrator

- `sudo` before a command runs it with full permissions
- `sudo su` gives you a root shell for multiple commands

Example:
```bash
sudo apt update    # Run one command as admin
sudo su            # Become root user
```

---

## File Locations

Important folders on Linux:

| Folder | What's in it |
|--------|--------------|
| `/home/gameserver` | Your files and settings |
| `/etc` | System configuration files |
| `/var/log` | System logs |
| `/tmp` | Temporary files (auto-deleted) |
| `/opt` | Optional software |

---

## Managing Services

Services are programs that run in the background (like game servers).

```bash
systemctl status nginx     # Check if a service is running
sudo systemctl start nginx # Start a service
sudo systemctl stop nginx  # Stop a service
sudo systemctl restart nginx # Restart a service
```

---

## File Permissions

View who can access a file:
```bash
ls -l filename.txt
```

Change permissions:
```bash
chmod 755 script.sh      # Make a script executable
chown gameserver file.txt # Change the owner
```

---

## Creating Backups

To back up a folder:
```bash
tar -czvf backup.tar.gz /folder-to-backup/
```

To restore from backup:
```bash
tar -xzvf backup.tar.gz
```

---

## Useful Commands

| Command | What it does |
|---------|--------------|
| `ls` | List files |
| `cd folder` | Change directory |
| `pwd` | Show current location |
| `cat file` | View file contents |
| `top` | View running processes |
| `df -h` | Check disk space |
| `grep "text" file` | Search in a file |

---

## Golden Rules

1. **Double-check** before using `sudo`
2. **Never run** `rm -rf /` (deletes everything!)
3. **Make backups** before changing config files
4. **Ask for help** if you're not sure
