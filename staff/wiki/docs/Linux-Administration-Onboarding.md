# 🖥️ Linux System Administrator Onboarding Guide

## 📂 Linux File System Structure

![Linux Filesystem Structure](Linux_Filesystem_Structure.png)



```bash
/
├── bin    -> Essential user binaries (commands)
├── boot   -> Boot loader files        # (purple)
├── dev    -> Device files             # (brown)
├── etc    -> Configuration files      # (red - CRITICAL configs)
├── home   -> User home directories    # (blue - users)
├── lib    -> Shared libraries         # (green)
├── media  -> Removable media          # (orange)
├── mnt    -> Mounted filesystems      # (orange)
├── opt    -> Optional software        # (indigo)
├── proc   -> Process information      # (grey - system virtual fs)
├── root   -> Root user home directory # (firebrick red - root only)
├── sbin   -> System binaries          # (green)
├── srv    -> Service data             # (teal)
├── tmp    -> Temporary files          # (gold/orange - auto cleared)
├── usr    -> User programs
└── var    -> Variable data (logs, spool files) # (forest green - logs!)
```


Below is a typical Linux filesystem layout. Each folder has a specific purpose:

- `/` (root) - The base of the filesystem  
- `/bin` - Essential user binaries (commands)  
- `/boot` - Boot loader files  
- `/dev` - Device files  
- `/etc` - Configuration files  
- `/home` - User home directories  
- `/lib` - Shared libraries  
- `/media` - Removable media  
- `/mnt` - Mounted filesystems  
- `/opt` - Optional software  
- `/proc` - Process information  
- `/root` - Root user home directory  
- `/sbin` - System binaries  
- `/srv` - Service data  
- `/tmp` - Temporary files  
- `/usr` - User programs  
- `/var` - Variable data (logs, spool files)  

---

## 1. Getting Started
Linux servers are controlled through the **command line**.  
You’ll use SSH to connect, then run commands to manage files, services, and users.  

---

## 2. Logging In with SSH
To connect to a server:

```bash
ssh username@server-address
```

Example:
```bash
ssh gameserver@gs-kansascity-01.iaregamer.com
```

Type **yes** the first time, then enter your password.

---

## 3. Sudo, su, and Root Access
**Root** is the superuser with unlimited control.  

- `sudo <command>` runs a single command as root  
- `sudo su` opens a root shell  
- `su username` switches to another user  
- `su - username` switches user *and* loads their environment  

---

## 4. File Permissions and Ownership
View permissions:
```bash
ls -l
```

Change permissions:
```bash
chmod 755 script.sh
```

Change ownership:
```bash
chown newuser file.txt
```

Change group:
```bash
chgrp newgroup file.txt
```

Change both:
```bash
chown newuser:newgroup file.txt
```

---

## 5. Archiving and Compressing with TAR
Create an archive:
```bash
tar -cvf backup.tar /folder/
```

Extract:
```bash
tar -xvf backup.tar
```

With compression:
```bash
tar -czvf backup.tar.gz /folder/   # gzip
tar -cjvf backup.tar.bz2 /folder/  # bzip2
tar -cJvf backup.tar.xz /folder/   # xz
```

Extract compressed:
```bash
tar -xzvf backup.tar.gz
```

---

## 6. Managing Services with systemctl
Check status:
```bash
systemctl status nginx
```

Start:
```bash
sudo systemctl start nginx
```

Stop:
```bash
sudo systemctl stop nginx
```

Restart:
```bash
sudo systemctl restart nginx
```

Enable at boot:
```bash
sudo systemctl enable nginx
```

Disable at boot:
```bash
sudo systemctl disable nginx
```

---

## 7. Other Common Commands
- `uptime` → show how long the server has been running  
- `top` → monitor CPU/memory usage  
- `df -h` → check disk space  
- `who` → see logged-in users  
- `grep "word" filename.txt` → search inside files  

---

## 8. Glossary of Symbols

| Symbol | Meaning |
|--------|---------|
| `.`    | current directory |
| `..`   | parent directory |
| `~`    | home directory |
| `/`    | root of the filesystem |
| `*`    | wildcard (matches anything) |
| `?`    | single-character wildcard |
| `|`    | pipe (send output of one command to another) |
| `>`    | redirect output to file (overwrite) |
| `>>`   | redirect output to file (append) |
| `<`    | take input from a file |
| `&`    | run process in background |
| `#`    | comment in a script |

---

## ✅ Golden Rules for New Admins
1. Double-check before using `sudo`.  
2. Never run `rm -rf /`.  
3. Use `ls` before `rm` to confirm files.  
4. Keep backups before changes (`tar`, `rsync`).  
5. Ask for help if unsure.  
