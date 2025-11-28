# SSH Key Setup and Authentication Guide

**Last Updated:** 2025-01-09  
**Purpose:** Comprehensive guide for SSH key generation, setup, and authentication across Windows and Linux systems

---

## Table of Contents

1. [SSH-Keygen Command Options](#ssh-keygen-command-options)
2. [Setup Scenarios](#setup-scenarios)
   - [Linux to Linux](#linux-to-linux)
   - [Windows to Windows](#windows-to-windows)
   - [Windows to Linux](#windows-to-linux)
   - [Linux to Windows](#linux-to-windows)
3. [Platform-Specific Notes](#platform-specific-notes)
4. [Security Best Practices](#security-best-practices)
5. [Troubleshooting](#troubleshooting)

---

## SSH-Keygen Command Options

`ssh-keygen` is the standard tool for generating SSH keys for authentication. Below are the essential options available on both Linux and Windows platforms.

### Core Options

- **`-t <type>`**: Specifies key type (e.g., `ed25519`, `rsa`, `ecdsa`)  
  Example: `ssh-keygen -t ed25519` (modern default, strongest security)

- **`-b <bits>`**: Sets key length (only for `rsa`/`ecdsa`)  
  Example: `ssh-keygen -t rsa -b 4096` (recommended RSA strength)

- **`-C "<comment>"`**: Adds a label (often email or hostname)  
  Example: `ssh-keygen -C "user@host"`

- **`-f <filename>`**: Custom output path/filename  
  Example: `ssh-keygen -f ~/.ssh/custom_key`

- **`-N "<passphrase>"`**: Sets a passphrase (empty for none)  
  Example: `ssh-keygen -N "my_secret"`

- **`-P "<old_passphrase>"`**: Existing passphrase (for key conversions)

- **`-p`**: Changes passphrase of existing key  
  Example: `ssh-keygen -p -f ~/.ssh/id_rsa`

### Key Management & Conversions

```bash
ssh-keygen -p -f ~/.ssh/id_rsa          # Change passphrase
ssh-keygen -e -f key.pub > key.openssh  # Convert OpenSSH → RFC4716
ssh-keygen -i -f key.rfc > key.pub      # Convert RFC4716 → OpenSSH
ssh-keygen -l -f key.pub                # Show fingerprint
ssh-keygen -B -f key.pub                # Bubble Babble fingerprint
ssh-keygen -y -f private_key            # Extract public key from private key
```

### Security & Host Keys

```bash
ssh-keygen -R example.com               # Remove host from known_hosts
ssh-keygen -F example.com               # Find host in known_hosts
ssh-keygen -H                           # Hash known_hosts (obfuscate)
ssh-keygen -s ca_key -I ID user_key.pub # Sign user key with CA
```

### Advanced Options

- **`-q`**: Quiet mode (suppresses prompts)
- **`-a <rounds>`**: KDF rounds for passphrase hardening (use with ed25519)
- **`-o`**: Use new OpenSSH format instead of PEM
- **`-m <format>`**: Key format (PEM, RFC4716, PKCS8)

```bash
ssh-keygen -r hostname                    # Generate SSHFP DNS records
ssh-keygen -G moduli.cand -b 4096         # Generate DH moduli candidates
ssh-keygen -T moduli.safe -f moduli.cand  # Test/verify moduli
ssh-keygen -Y sign -f key -n file file    # Sign file (SSH agent)
ssh-keygen -k -f revoked_keys             # Revoke key via KRL (Key Revocation List)
```

### Example Workflows

**Generate ED25519 key (Linux/Windows):**
```bash
# Linux
ssh-keygen -t ed25519 -a 100 -C "work-laptop"

# Windows PowerShell
ssh-keygen -t ed25519 -C "laptop@work" -f ~/.ssh/work_key -N "strong_pass"
```

**Convert old RSA key to new format:**
```bash
ssh-keygen -p -o -f ~/.ssh/id_rsa_legacy
```

**Verify key integrity:**
```bash
ssh-keygen -y -f ~/.ssh/id_ecdsa > test.pub && diff test.pub id_ecdsa.pub
```

**Check SSH key fingerprint:**
```bash
ssh-keygen -l -f ~/.ssh/work_key.pub
```

---

## Setup Scenarios

### Linux to Linux

Set up passwordless SSH authentication between two Linux machines.

#### Step 1: Generate Key Pair on Client Machine
```bash
ssh-keygen -t ed25519 -C "your_email@example.com"
```
- Press Enter to save in `~/.ssh/id_ed25519`
- Set a strong passphrase (recommended)
- Creates two files: `id_ed25519` (private) and `id_ed25519.pub` (public)

#### Step 2: Copy Public Key to Host
```bash
ssh-copy-id -i ~/.ssh/id_ed25519.pub username@host_ip
```
- Enter host password when prompted
- Installs public key in host's `~/.ssh/authorized_keys`

**Alternative Manual Method:**
```bash
# View public key
cat ~/.ssh/id_ed25519.pub

# Manually append to host's authorized_keys
ssh username@host_ip "mkdir -p ~/.ssh && chmod 700 ~/.ssh && echo '$(cat ~/.ssh/id_ed25519.pub)' >> ~/.ssh/authorized_keys && chmod 600 ~/.ssh/authorized_keys"
```

#### Step 3: Test Connection
```bash
ssh -i ~/.ssh/id_ed25519 username@host_ip
```
- Should connect without password prompt

#### Step 4: Disable Password Auth (Optional)
On host machine edit `/etc/ssh/sshd_config`:
```bash
PasswordAuthentication no
ChallengeResponseAuthentication no
```
Then restart SSH: `sudo systemctl restart sshd`

---

### Windows to Windows

Set up SSH keys from a Windows client to a Windows host using OpenSSH.

#### Step 1: Verify SSH Installation

**On both client and host:**
- Open PowerShell as **Administrator**
- Run:
  ```powershell
  Get-WindowsCapability -Online | Where-Object Name -like 'OpenSSH*'
  ```

- If not installed, run:
  ```powershell
  # Install OpenSSH Client (on client machine)
  Add-WindowsCapability -Online -Name OpenSSH.Client~~~~0.0.1.0

  # Install OpenSSH Server (on host machine)
  Add-WindowsCapability -Online -Name OpenSSH.Server~~~~0.0.1.0
  ```

- Start/restart the SSH service on the **host**:
  ```powershell
  Start-Service sshd
  Set-Service -Name sshd -StartupType Automatic
  ```

#### Step 2: Generate SSH Key Pair on Client

**On your Windows client machine:**
- Open PowerShell
- Run:
  ```powershell
  ssh-keygen -t ed25519
  ```
  - Press `Enter` to save keys in the default location (`C:\Users\YourUser\.ssh\id_ed25519`)
  - Optionally set a passphrase for security

#### Step 3: Copy Public Key to Host

**On the client:**
```powershell
# Replace 'user@host-ip' with your host credentials
ssh-copy-id -i ~\.ssh\id_ed25519.pub user@host-ip
```

**If `ssh-copy-id` fails:**
1. Manually copy the public key (`id_ed25519.pub`) to the host
2. On the **host**, create `.ssh` folder in your user profile:
   ```powershell
   mkdir C:\Users\YourUser\.ssh
   ```
3. Append the public key to `authorized_keys`:
   ```powershell
   Add-Content -Path C:\Users\YourUser\.ssh\authorized_keys -Value (Get-Content C:\Path\To\id_ed25519.pub)
   ```
4. Set permissions:
   ```powershell
   icacls C:\Users\YourUser\.ssh /remove "Everyone"
   icacls C:\Users\YourUser\.ssh\authorized_keys /remove "Everyone"
   ```

#### Step 4: Configure SSH Daemon on Host

**On the host:**
- Edit the SSH config:
  ```powershell
  notepad C:\ProgramData\ssh\sshd_config
  ```
- Ensure these lines exist/uncommented:
  ```config
  PubkeyAuthentication yes
  AuthorizedKeysFile .ssh/authorized_keys
  PasswordAuthentication no  # Disable password login for security
  ```
- Restart SSH service:
  ```powershell
  Restart-Service sshd
  ```

#### Step 5: Test Connection

**On the client:**
```powershell
ssh user@host-ip
```
- If prompted for a passphrase (set during keygen), enter it
- You should connect without a password

---

### Windows to Linux

Set up SSH keys from Windows to a Linux host.

#### Step 1: Generate Key Pair on Windows

- Open PowerShell or Command Prompt
- Run:
  ```powershell
  ssh-keygen -t ed25519
  ```
- Press Enter to accept default save location (`C:\Users\YourUser\.ssh\id_ed25519`)
- Set a passphrase (recommended) or leave empty

#### Step 2: Copy Public Key to Linux Host

Use this command (replace `user@host` with your credentials):
```powershell
ssh-copy-id -i ~\.ssh\id_ed25519.pub user@host_ip
```
- Enter your Linux password when prompted

#### Step 3: Test Connection

```bash
ssh user@host_ip
```
- Should connect without password prompt

#### Step 4: Disable Password Auth (Optional)

On Linux host:
```bash
sudo nano /etc/ssh/sshd_config
```
Change to:
```conf
PasswordAuthentication no
ChallengeResponseAuthentication no
```
Restart SSH:
```bash
sudo systemctl restart sshd
```

---

### Linux to Windows

Set up SSH keys from a Linux client to a Windows host using OpenSSH.

#### Step 1: Prepare Windows Host

1. **Enable OpenSSH Server** (Windows 10/11):
   - Open **Settings** → **Apps** → **Optional Features**
   - Click "Add a feature" → Install **OpenSSH Server**
   - Run PowerShell as admin:
     ```powershell
     Start-Service sshd
     Set-Service -Name sshd -StartupType Automatic
     ```

2. **Configure Firewall**:
   - Allow port 22 inbound (TCP) in Windows Defender Firewall

#### Step 2: Generate SSH Keys on Linux Client

1. **Create Key Pair**:
   ```bash
   ssh-keygen -t ed25519 -C "your_email@example.com"
   ```
   - Press Enter to save keys in `~/.ssh/id_ed25519` (default)
   - Optional: Add a passphrase for security

2. **Verify Keys**:
   ```bash
   ls ~/.ssh/id_ed25519*  # Should show private/public keys
   ```

#### Step 3: Copy Public Key to Windows Host

1. **Install `ssh-copy-id` on Linux** (if missing):
   ```bash
   sudo apt install openssh-client  # Debian/Ubuntu
   sudo dnf install openssh-clients # Fedora
   ```

2. **Copy Key**:
   ```bash
   ssh-copy-id -i ~/.ssh/id_ed25519.pub username@windows_host_ip
   ```
   - Enter your Windows password when prompted

   **Alternative (manual):**
   - Copy public key content: `cat ~/.ssh/id_ed25519.pub`
   - On Windows, create `C:\Users\YourUsername\.ssh\authorized_keys`
   - Paste the key into this file

#### Step 4: Test Connection

```bash
ssh username@windows_host_ip
```
- First connection prompts trust confirmation → type `yes`
- Should log in **without a password**

---

## Platform-Specific Notes

### Windows-Specific Considerations

1. **File Paths**: Use forward slashes or escaped backslashes:
   - `-f C:/Users/name/.ssh/key` or `-f C:\\Users\\name\\.ssh\\key`

2. **Passphrases**: Avoid spaces/special characters unless quoted:
   - `-N "pass with spaces"`

3. **Integration**: Works in PowerShell, CMD, or Git Bash

4. **File Locations**:
   - Private key: `C:\Users\YourUser\.ssh\id_ed25519`
   - Public key: `C:\Users\YourUser\.ssh\id_ed25519.pub`

5. **Permissions**: Windows handles permissions differently than Linux
   - Use `icacls` to set proper permissions on `.ssh` folder and `authorized_keys`

6. **SSH Agent**: Ensure `ssh-agent` is running:
   ```powershell
   Get-Service ssh-agent
   Start-Service ssh-agent
   Set-Service -Name ssh-agent -StartupType Automatic
   ```

7. **Add Key to Agent**:
   ```powershell
   ssh-add ~\.ssh\id_ed25519
   ```

### Linux-Specific Considerations

1. **Permissions**: Critical for SSH security
   - `~/.ssh` directory should be `700` (rwx------)
   - `~/.ssh/authorized_keys` should be `600` (rw-------)
   - Private keys should be `600`
   - Public keys can be `644`

   ```bash
   chmod 700 ~/.ssh
   chmod 600 ~/.ssh/id_ed25519
   chmod 600 ~/.ssh/authorized_keys
   chmod 644 ~/.ssh/id_ed25519.pub
   ```

2. **SELinux Context**: If SELinux is enabled:
   ```bash
   restorecon -Rv ~/.ssh
   ```

3. **SSH Agent**: For passphrase management:
   ```bash
   eval $(ssh-agent)
   ssh-add ~/.ssh/id_ed25519
   ```

---

## Security Best Practices

### Key Algorithm Selection

1. **Recommended**: `ed25519` (modern, secure, fast)
   ```bash
   ssh-keygen -t ed25519
   ```

2. **Alternative**: `rsa` with 4096-bit (if ed25519 not supported)
   ```bash
   ssh-keygen -t rsa -b 4096
   ```

3. **Avoid**: `ecdsa` when possible (potential security concerns)

### Key Protection

1. **Always use a strong passphrase** for private keys
   - Minimum 12 characters
   - Mix of letters, numbers, and special characters

2. **Never share private keys** - they should remain on the client machine only

3. **Use SSH Agent** to avoid repeatedly typing passphrases:
   ```bash
   # Linux
   eval $(ssh-agent)
   ssh-add ~/.ssh/id_ed25519

   # Windows PowerShell
   Start-Service ssh-agent
   ssh-add ~\.ssh\id_ed25519
   ```

4. **Keep private keys secure**:
   - Store only on trusted devices
   - Use proper file permissions (600 on Linux)
   - Consider hardware security keys for critical systems

### Server Security

1. **Disable Password Authentication** after key setup:
   ```conf
   PasswordAuthentication no
   ChallengeResponseAuthentication no
   ```

2. **Disable Root Login**:
   ```conf
   PermitRootLogin no
   ```

3. **Use Key-Based Authentication Only**:
   ```conf
   PubkeyAuthentication yes
   ```

4. **Consider Multi-Factor Authentication** for critical systems

### Key Management

1. **Rotate keys regularly** (annually or after security incidents)

2. **Remove old keys** from `authorized_keys` when no longer needed

3. **Use different keys** for different purposes/systems

4. **Backup keys securely** (encrypted storage only)

5. **Use KDF rounds** for additional passphrase protection:
   ```bash
   ssh-keygen -t ed25519 -a 100
   ```

---

## Troubleshooting

### Connection Issues

**Problem: "Permission denied (publickey)"**

- **Linux Host**:
  ```bash
  # Verify permissions
  chmod 700 ~/.ssh
  chmod 600 ~/.ssh/authorized_keys
  
  # Check SELinux context
  restorecon -Rv ~/.ssh
  
  # Verify key in authorized_keys
  cat ~/.ssh/authorized_keys
  ```

- **Windows Host**:
  ```powershell
  # Check permissions
  icacls C:\Users\YourUser\.ssh\authorized_keys /inheritance:r
  
  # Verify SSH config
  notepad C:\ProgramData\ssh\sshd_config
  
  # Check logs
  Get-Content C:\ProgramData\ssh\logs\sshd.log -Tail 50
  ```

**Problem: "Host key verification failed"**

```bash
# Remove old host key
ssh-keygen -R hostname_or_ip

# Reconnect (will prompt to trust new key)
ssh user@host
```

**Problem: Key not being used**

```bash
# Test SSH connection with verbose output
ssh -v user@host

# Verify key is added to agent
ssh-add -l

# Add key to agent if missing
ssh-add ~/.ssh/id_ed25519
```

### Windows-Specific Issues

**Problem: `ssh-copy-id` not found on Windows**

- Install Git for Windows (includes ssh-copy-id)
- Or manually copy public key as shown in Windows-to-Windows section

**Problem: Firewall blocking connections**

```powershell
# Allow SSH through firewall
New-NetFirewallRule -Name "OpenSSH-Server" -DisplayName "OpenSSH Server" -Enabled True -Direction Inbound -Protocol TCP -Action Allow -LocalPort 22
```

**Problem: SSH service not starting**

```powershell
# Check service status
Get-Service sshd

# Start service
Start-Service sshd

# Check for errors
Get-EventLog -LogName Application -Source sshd -Newest 20
```

### Linux-Specific Issues

**Problem: ssh-copy-id fails**

```bash
# Install openssh-client if missing
sudo apt install openssh-client  # Debian/Ubuntu
sudo dnf install openssh-clients # Fedora/RHEL

# Try manual method
cat ~/.ssh/id_ed25519.pub | ssh user@host "mkdir -p ~/.ssh && cat >> ~/.ssh/authorized_keys"
```

**Problem: SSH config syntax errors**

```bash
# Test SSH daemon config
sudo sshd -t

# View SSH daemon logs
sudo journalctl -u sshd -n 50
```

### General Debugging

**Enable verbose SSH output:**
```bash
ssh -vvv user@host
```

**Check SSH daemon configuration:**
```bash
# Linux
sudo cat /etc/ssh/sshd_config | grep -v '^#' | grep -v '^$'

# Windows PowerShell
Get-Content C:\ProgramData\ssh\sshd_config | Where-Object {$_ -notmatch '^\s*#' -and $_ -notmatch '^\s*$'}
```

**Test SSH authentication:**
```bash
ssh -T git@github.com  # Test GitHub authentication
ssh -T user@host       # Test server authentication
```

### Permission Issues Summary

| Platform | Directory | File | Permissions |
|----------|-----------|------|-------------|
| Linux | `~/.ssh` | - | 700 (drwx------) |
| Linux | - | `authorized_keys` | 600 (-rw-------) |
| Linux | - | `id_ed25519` (private) | 600 (-rw-------) |
| Linux | - | `id_ed25519.pub` (public) | 644 (-rw-r--r--) |
| Windows | `.ssh` | - | Remove "Everyone" access |
| Windows | - | `authorized_keys` | Remove "Everyone" access |
| Windows | - | `id_ed25519` (private) | User-only access |

---

## Additional Resources

- **Manual Pages**: `man ssh-keygen`, `man ssh`, `man sshd_config`
- **OpenSSH Documentation**: https://www.openssh.com/manual.html
- **SSH.com Guide**: https://www.ssh.com/academy/ssh/keygen

---

**Security Reminder**: SSH keys provide powerful access. Always use strong passphrases, protect private keys, and regularly audit authorized_keys on your servers. Never commit private keys to version control or share them via insecure channels.

🔑 You're now ready for secure, passwordless SSH authentication across all platforms!
