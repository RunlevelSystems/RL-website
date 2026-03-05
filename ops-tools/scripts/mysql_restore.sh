# Developed by World Domination Software LLC
#!/usr/bin/env bash
#
# MySQL restore script
# Usage: mysql_restore.sh /var/www/html/files/backup/mysql/Monday
#
# Restores:
#   - /etc/mysql and /etc/my.cnf from mysql_config_*.tar.gz
#   - All databases from all-databases_*.sql.gz
#   - User grants from mysql_user_grants_*.sql
#

set -euo pipefail

if [[ "$EUID" -ne 0 ]]; then
  echo "This script must be run as root." >&2
  exit 1
fi

BACKUP_DIR="${1:-}"

if [[ -z "$BACKUP_DIR" || ! -d "$BACKUP_DIR" ]]; then
  echo "Usage: $0 /path/to/backup_folder" >&2
  echo "Example: $0 /var/www/html/files/backup/mysql/Monday" >&2
  exit 1
fi

echo "Using backup folder: $BACKUP_DIR"

shopt -s nullglob

# Find backup files
CONFIG_TARS=( "$BACKUP_DIR"/mysql_config_*.tar.gz )
ALLDB_DUMPS=( "$BACKUP_DIR"/all-databases_*.sql.gz )
GRANTS_FILES=( "$BACKUP_DIR"/mysql_user_grants_*.sql )

if [[ ${#CONFIG_TARS[@]} -eq 0 ]]; then
  echo "ERROR: No mysql_config_*.tar.gz found in $BACKUP_DIR" >&2
  exit 1
fi

if [[ ${#ALLDB_DUMPS[@]} -eq 0 ]]; then
  echo "ERROR: No all-databases_*.sql.gz found in $BACKUP_DIR" >&2
  exit 1
fi

if [[ ${#GRANTS_FILES[@]} -eq 0 ]]; then
  echo "WARNING: No mysql_user_grants_*.sql found in $BACKUP_DIR" >&2
fi

CONFIG_TAR="${CONFIG_TARS[0]}"
ALLDB_DUMP="${ALLDB_DUMPS[0]}"
GRANTS_FILE="${GRANTS_FILES[0]:-}"

echo "Config archive:      $CONFIG_TAR"
echo "All-databases dump:  $ALLDB_DUMP"
[[ -n "$GRANTS_FILE" ]] && echo "Grants file:          $GRANTS_FILE"

# Detect MySQL service name
MYSQL_SERVICE=""
if systemctl list-unit-files | grep -q '^mysql.service'; then
  MYSQL_SERVICE="mysql"
elif systemctl list-unit-files | grep -q '^mariadb.service'; then
  MYSQL_SERVICE="mariadb"
fi

if [[ -z "$MYSQL_SERVICE" ]]; then
  echo "ERROR: Could not find mysql or mariadb systemd service." >&2
  echo "Make sure MySQL/MariaDB is installed." >&2
  exit 1
fi

echo "Using service: $MYSQL_SERVICE"

# ----- 1. Backup current config before overwriting -----
echo "Backing up current /etc/mysql and /etc/my.cnf (if any)..."
TS="$(date +%F_%H-%M-%S)"
mkdir -p /root/mysql-config-backups

tar -czf "/root/mysql-config-backups/mysql_config_before_restore_${TS}.tar.gz" \
  /etc/mysql \
  /etc/my.cnf 2>/dev/null || true

# ----- 2. Stop MySQL before restoring config -----
echo "Stopping $MYSQL_SERVICE..."
systemctl stop "$MYSQL_SERVICE"

# ----- 3. Restore config from backup -----
echo "Restoring MySQL config from $CONFIG_TAR..."
# The tar contains absolute-style paths (/etc/mysql, /etc/my.cnf),
# so we extract at filesystem root.
tar -xzf "$CONFIG_TAR" -C /

echo "Config restore complete."

# ----- 4. Start MySQL again -----
echo "Starting $MYSQL_SERVICE..."
systemctl start "$MYSQL_SERVICE"

# Wait for MySQL to accept connections
echo "Waiting for MySQL to become ready..."
for i in {1..10}; do
  if mysqladmin ping --silent; then
    echo "MySQL is up."
    break
  fi
  sleep 2
done

if ! mysqladmin ping --silent; then
  echo "ERROR: MySQL did not become ready. Check 'systemctl status $MYSQL_SERVICE'." >&2
  exit 1
fi

# ----- 5. Restore all databases -----
echo "Restoring all databases from $ALLDB_DUMP..."
gunzip -c "$ALLDB_DUMP" | mysql

echo "All databases restored."

# ----- 6. Re-apply GRANT statements (if available) -----
if [[ -n "$GRANTS_FILE" && -f "$GRANTS_FILE" ]]; then
  echo "Applying user GRANTS from $GRANTS_FILE..."
  mysql < "$GRANTS_FILE"
  echo "GRANTS restored."
else
  echo "No GRANTS file found; relying on contents of mysql.* from all-databases dump."
fi

echo "MySQL restore completed from folder: $BACKUP_DIR"

