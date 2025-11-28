#!/usr/bin/env bash
#
# MySQL backup script
# - Runs as root
# - Backs up all DBs (per-db + all-databases)
# - Captures users/privileges via SHOW GRANTS
# - Archives MySQL config from /etc
# - Keeps a "recent" set and a rotating set by weekday

set -euo pipefail

# ----- CONFIG -----
BACKUP_ROOT="/var/www/html/files/backup/mysql"
RECENT_DIR="${BACKUP_ROOT}/recent"
WEEKDAY_DIR="${BACKUP_ROOT}/$(date +%A)"   # Monday, Tuesday, etc.
TIMESTAMP="$(date +%F_%H-%M-%S)"          # e.g. 2025-11-24_20-15-00

# Make sure MySQL tools are in PATH for cron
PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"

# Require root (so root@localhost unix_socket auth works and can read /etc)
if [[ "$EUID" -ne 0 ]]; then
  echo "This script must be run as root." >&2
  exit 1
fi

# Create base directories
mkdir -p "$RECENT_DIR"
mkdir -p "$BACKUP_ROOT"

# Clean out "recent" before writing new backups
rm -rf "${RECENT_DIR:?}/"*

echo "[$(date)] Starting MySQL backup..."

# ----- 1. Per-database dumps (compressed) -----
# Uses root socket auth; if you use a password, set up /root/.my.cnf instead.
DATABASES=$(mysql -N -e "SHOW DATABASES;")

for DB in $DATABASES; do
  case "$DB" in
    information_schema|performance_schema|sys)
      # usually not needed; skip these schemas
      continue
      ;;
  esac

  OUTFILE="${RECENT_DIR}/${DB}_${TIMESTAMP}.sql.gz"
  echo "  Dumping database: $DB  ->  $(basename "$OUTFILE")"
  mysqldump \
    --single-transaction \
    --routines \
    --events \
    --triggers \
    "$DB" | gzip > "$OUTFILE"
done

# ----- 2. Full "all-databases" dump (includes mysql.* with users/password hashes) -----
ALLDB_OUT="${RECENT_DIR}/all-databases_${TIMESTAMP}.sql.gz"
echo "  Dumping ALL databases (including mysql.*) -> $(basename "$ALLDB_OUT")"

mysqldump \
  --all-databases \
  --single-transaction \
  --routines \
  --events \
  --triggers | gzip > "$ALLDB_OUT"

# ----- 3. Explicit user / grants dump -----
# This gives you replayable GRANT statements to restore accounts and privileges.
GRANTS_OUT="${RECENT_DIR}/mysql_user_grants_${TIMESTAMP}.sql"
echo "  Dumping user GRANTS -> $(basename "$GRANTS_OUT")"

# Build SHOW GRANTS commands for every non-empty user
mysql -N -e "SELECT CONCAT('SHOW GRANTS FOR ''', user, '''@''', host, ''';')
             FROM mysql.user
             WHERE user <> '';" \
  | while read -r GRANT_CMD; do
      # For each account, run SHOW GRANTS and add semicolons
      mysql -N -e "$GRANT_CMD" | sed 's/$/;/' >> "$GRANTS_OUT"
      echo "" >> "$GRANTS_OUT"
    done

# ----- 4. MySQL config backup (/etc) -----
CONFIG_OUT="${RECENT_DIR}/mysql_config_${TIMESTAMP}.tar.gz"
echo "  Archiving MySQL config -> $(basename "$CONFIG_OUT")"

# /etc/mysql plus /etc/my.cnf if it exists
if [[ -d /etc/mysql ]] || [[ -f /etc/my.cnf ]]; then
  tar -czf "$CONFIG_OUT" \
    /etc/mysql \
    /etc/my.cnf 2>/dev/null || true
else
  echo "  WARNING: No /etc/mysql or /etc/my.cnf found to archive." >&2
fi

# ----- 5. Rotate weekday folders -----
# We always refresh the folder for today's weekday
echo "  Rotating weekday backup folder: $(basename "$WEEKDAY_DIR")"

rm -rf "${WEEKDAY_DIR:?}/"*
mkdir -p "$WEEKDAY_DIR"

# Copy contents of recent -> weekday
cp -a "${RECENT_DIR}/." "$WEEKDAY_DIR/"

echo "[$(date)] MySQL backup completed successfully."

