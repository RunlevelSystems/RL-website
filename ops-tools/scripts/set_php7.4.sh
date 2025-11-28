#!/usr/bin/env bash
#
# Switch Apache + CLI from PHP 8.5 to PHP 7.4
# Run as root (or via sudo)

set -euo pipefail

if [[ "$EUID" -ne 0 ]]; then
  echo "This script must be run as root (use sudo)." >&2
  exit 1
fi

echo "Switching PHP CLI to 7.4..."
if command -v update-alternatives >/dev/null 2>&1; then
  if [[ -x /usr/bin/php7.4 ]]; then
    update-alternatives --set php /usr/bin/php7.4 || echo "WARN: couldn't switch CLI php alternative"
  else
    echo "ERROR: /usr/bin/php7.4 not found. Is PHP 7.4 installed?" >&2
  fi
else
  echo "WARN: update-alternatives not found; skipping CLI switch."
fi

echo "Switching Apache to PHP 7.4 module..."

# Disable PHP 8.5 Apache module if present
a2dismod php8.5  >/dev/null 2>&1 || echo "php8.5 module not enabled or not present."

# Enable PHP 7.4 Apache module
a2enmod php7.4  >/dev/null 2>&1 || echo "ERROR: could not enable php7.4 module; check that libapache2-mod-php7.4 is installed."

# Ensure correct MPM for mod_php
echo "Ensuring mpm_prefork is enabled (required for mod_php)..."
a2dismod mpm_event  >/dev/null 2>&1 || true
a2dismod mpm_worker >/dev/null 2>&1 || true
a2enmod mpm_prefork >/dev/null 2>&1 || echo "WARN: could not enable mpm_prefork."

echo "Testing Apache config..."
apache2ctl configtest

echo "Reloading Apache..."
systemctl reload apache2

echo "Current PHP version:"
php -v | head -n 3
echo "Done: switched to PHP 7.4."

