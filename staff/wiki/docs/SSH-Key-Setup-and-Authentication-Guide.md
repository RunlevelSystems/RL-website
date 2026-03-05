<!-- Developed by World Domination Software LLC -->
# SSH Key Setup Guide

This guide helps you set up secure login to servers without typing passwords every time.

---

## What is an SSH Key?

An SSH key is like a digital ID card. Instead of typing a password, your computer shows its ID to the server. This is both more secure and more convenient.

You create two files:
- **Private key** - stays on your computer (never share this!)
- **Public key** - goes on the server you want to access

---

## Quick Setup

### Step 1: Create Your Keys

**On Windows or Linux**, open a terminal and run:
```bash
ssh-keygen -t ed25519
```

- Press Enter to accept the default save location
- Enter a passphrase (like a password for your key) when prompted

### Step 2: Copy Your Key to the Server

**The easy way:**
```bash
ssh-copy-id username@server-address
```

**If that doesn't work**, manually copy your public key:
1. View your public key: `cat ~/.ssh/id_ed25519.pub`
2. Log into the server with your password
3. Add the key to `~/.ssh/authorized_keys`

### Step 3: Test It

```bash
ssh username@server-address
```

You should connect without entering a password!

---

## Platform-Specific Notes

### Windows

- Use PowerShell to run commands
- Keys are stored in `C:\Users\YourName\.ssh\`
- If ssh-copy-id doesn't work, install Git for Windows

### Linux

- Keys are stored in `~/.ssh/`
- Set proper permissions:
  - `chmod 700 ~/.ssh` (folder)
  - `chmod 600 ~/.ssh/id_ed25519` (private key - important!)
  - `chmod 644 ~/.ssh/id_ed25519.pub` (public key)

---

## Common Problems

| Problem | Solution |
|---------|----------|
| "Permission denied" | Check that your public key is in `authorized_keys` on the server |
| Key not being used | Run `ssh-add ~/.ssh/id_ed25519` to load your key |
| "Host key verification failed" | Run `ssh-keygen -R server-address` and try again |

---

## Security Tips

1. **Always use a passphrase** on your private key
2. **Never share your private key** with anyone
3. **Remove old keys** from servers when staff leave
4. Keep your private key only on trusted devices
