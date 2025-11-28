# Accounts and Access

Quick reference for all team credentials. This page is only accessible to logged-in staff members.

---

## 🖥️ Windows Servers

| Setting | Value |
|---------|-------|
| Username | cyg_server / gameserver |
| Password | `C37p7wzkkhcn!` |
| RDP Port | 13389 |
| SSH Port | 12322 |

**Note:** Administrator is assigned by the server host.

---

## 🐧 Linux Servers

| Setting | Value |
|---------|-------|
| Username | gameserver |
| Password | `Inc0rrect!` |
| SSH Port | 12322 on every host |

**Note:** Root access is denied but on new systems usually doesn't require a password.

---

## 🗄️ MySQL Database

| Setting | Value |
|---------|-------|
| Remote Host | mysql.iaregamer.com |
| Port | 3306 |
| Database | panel |
| Local User | localuser / `Pkloyn7yvpht!` |
| Remote User | remoteuser / `Pkloyn7yvpht!` |

---

## 📧 Email & Webhost

| Service | Credentials |
|---------|-------------|
| Gmail | iaregamer.com@gmail.com / `Inc0rrect` |
| cPanel | domainpl / `Inc0rrect` |

---

## 🎮 Game Servers World Panel

| Setting | Value |
|---------|-------|
| Username | iaregamer |
| Password | `Inc0rrect` |

---

## 📚 Learning Platforms

| Platform | Credentials |
|----------|-------------|
| Zenva | wds_team_account / `ZenvaAccess2024!` |
| Mammoth Interactive | wds_coop_login / `MammothDev2024#` |
| Udemy Business | worlddomsoftware@business.udemy.com / `UdemyBiz2024$` |

---

## 🛡️ Password Rotation

When rotating passwords, use `ops-tools/scripts/check_servers.sh --password "NewPass!"` on core.iaregamer.com and update this page.