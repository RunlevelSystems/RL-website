# Developed by World Domination Software LLC
#!/usr/bin/env bash
# =============================================================================
# dr_rsync_push.sh - Disaster Recovery Sync Script
# =============================================================================
# Syncs data directories, config files, and MySQL databases from this host
# to a DR target. Designed to run from core host to replicate to core-dr.
#
# Usage:
#   ./dr_rsync_push.sh <TARGET_HOST> [SSH_PORT]
#
# Example:
#   ./dr_rsync_push.sh core-dr.iaregamer.com 12322
#
# Requirements:
#   - rsync and ssh on both hosts
#   - MySQL client if syncing databases
#   - /home/gameserver/tools/.password for sshpass (optional, uses keys if not)
# =============================================================================

set -euo pipefail

# =============================================================================
# Configuration
# =============================================================================
DEFAULT_SSH_PORT=12322
TOOLS_DIR="/home/gameserver/tools"
PASSFILE="${TOOLS_DIR}/.password"

# Directories to sync
DATA_DIRS=(
    "/home/gameserver/tools"
    "/var/www/html/panel"
    "/var/www/html/adminer"
    "/var/www/files/backup"
    "/var/www/files/installers"
)

# /etc excludes (host-specific files that should not be copied)
ETC_EXCLUDES=(
    "--exclude=/etc/hostname"
    "--exclude=/etc/machine-id"
    "--exclude=/etc/hosts"
    "--exclude=/etc/netplan/**"
    "--exclude=/etc/fstab"
    "--exclude=/etc/ssh/ssh_host_*"
    "--exclude=/etc/letsencrypt/accounts/**"
    "--exclude=/etc/letsencrypt/**/csr/**"
    "--exclude=/etc/letsencrypt/**/keys/**"
    "--exclude=/etc/letsencrypt/**/renewal-hooks/**"
)

# MySQL settings
MYSQL_BIN="${MYSQL_BIN:-mysql}"
MYSQLDUMP_BIN="${MYSQLDUMP_BIN:-mysqldump}"
MYSQL_EXCLUDE_REGEX='^(information_schema|performance_schema|mysql|sys)$'
MYSQL_LOCAL_DEFAULTS="/root/.my.cnf"

# SSH options (reduce security prompts)
SSH_OPTS="-o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -o BatchMode=yes"

# =============================================================================
# Helpers
# =============================================================================
die() { echo "ERROR: $*" >&2; exit 1; }
say() { printf '\n[%s] %s\n' "$(date '+%F %T')" "$*"; }
warn() { echo "WARNING: $*" >&2; }

# =============================================================================
# Parse Arguments
# =============================================================================
if [ $# -lt 1 ]; then
    echo "Usage: $0 <TARGET_HOST> [SSH_PORT]" >&2
    echo "Example: $0 core-dr.iaregamer.com 12322" >&2
    exit 1
fi

TARGET="$1"
SSH_PORT="${2:-$DEFAULT_SSH_PORT}"

# =============================================================================
# Setup SSH/rsync with optional sshpass
# =============================================================================
RSYNC_SSH="ssh -p $SSH_PORT $SSH_OPTS"
RSYNC_OPTS="-aHAX --numeric-ids --delete --delete-excluded --info=stats2,progress2 --rsync-path='sudo rsync'"

if command -v sshpass >/dev/null 2>&1 && [ -s "${PASSFILE}" ]; then
    # Validate password file contains non-empty content
    PASS_CONTENT="$(tr -d '[:space:]' < "${PASSFILE}")"
    if [[ -n "$PASS_CONTENT" ]]; then
        SSHPRE=(sshpass -f "${PASSFILE}")
    else
        warn "Password file exists but is empty or contains only whitespace"
        SSHPRE=()
    fi
else
    SSHPRE=()
fi

# =============================================================================
# Functions
# =============================================================================
push_dir() {
    local src="$1"
    local dest="$2"
    say "Rsync: $src -> $TARGET:$dest"
    # shellcheck disable=SC2086
    "${SSHPRE[@]}" rsync $RSYNC_OPTS -e "$RSYNC_SSH" "$src/" "${TARGET}:${dest}/"
}

push_etc() {
    say "Rsync: /etc (excluding host-specific files)"
    # shellcheck disable=SC2086
    "${SSHPRE[@]}" rsync $RSYNC_OPTS -e "$RSYNC_SSH" "${ETC_EXCLUDES[@]}" /etc/ "${TARGET}:/etc/"
}

ensure_remote_mysql() {
    "${SSHPRE[@]}" ssh -p "$SSH_PORT" $SSH_OPTS "$TARGET" "command -v mysql >/dev/null 2>&1" || {
        echo "WARN: mysql not found on $TARGET, skipping database sync"
        return 1
    }
    return 0
}

stream_databases() {
    say "MySQL: streaming per-DB dumps (routines/triggers/events)"
    
    local AUTH=()
    [ -f "$MYSQL_LOCAL_DEFAULTS" ] && AUTH=(--defaults-file="$MYSQL_LOCAL_DEFAULTS")
    
    mapfile -t DBS < <($MYSQL_BIN "${AUTH[@]}" --batch --skip-column-names -e "SHOW DATABASES;" | grep -Ev "$MYSQL_EXCLUDE_REGEX")
    
    for db in "${DBS[@]}"; do
        say "  Dumping $db -> $TARGET"
        $MYSQLDUMP_BIN "${AUTH[@]}" --single-transaction --routines --triggers --events --hex-blob "$db" \
            | gzip -c \
            | "${SSHPRE[@]}" ssh -p "$SSH_PORT" $SSH_OPTS "$TARGET" "gunzip -c | sudo $MYSQL_BIN"
    done
}

replicate_users_and_grants() {
    say "MySQL: replicating users & grants"
    
    local AUTH=()
    [ -f "$MYSQL_LOCAL_DEFAULTS" ] && AUTH=(--defaults-file="$MYSQL_LOCAL_DEFAULTS")
    
    local tmpdir
    tmpdir="$(mktemp -d)"
    trap 'rm -rf "$tmpdir"' EXIT
    
    # Get list of accounts
    $MYSQL_BIN "${AUTH[@]}" --batch --skip-column-names \
        -e "SELECT CONCAT(\"'\", user, \"'@'\", host, \"'\") FROM mysql.user WHERE user NOT IN ('mysql.sys','mysql.session','mysql.infoschema');" \
        > "$tmpdir/accts.txt"
    
    : > "$tmpdir/users_grants.sql"
    
    while IFS= read -r acct; do
        [ -z "$acct" ] && continue
        
        # Get CREATE USER statement
        $MYSQL_BIN "${AUTH[@]}" --batch --skip-column-names \
            -e "SHOW CREATE USER $acct\G" \
            | awk -F': ' '/CREATE USER/ {print $2";"}' \
            | sed 's/^CREATE USER /CREATE USER IF NOT EXISTS /' \
            >> "$tmpdir/users_grants.sql"
        
        # Get GRANT statements
        $MYSQL_BIN "${AUTH[@]}" --batch --skip-column-names \
            -e "SHOW GRANTS FOR $acct;" \
            | sed 's/$/;/' \
            >> "$tmpdir/users_grants.sql"
    done < "$tmpdir/accts.txt"
    
    # Transfer and execute grants on remote
    cat "$tmpdir/users_grants.sql" | "${SSHPRE[@]}" ssh -p "$SSH_PORT" $SSH_OPTS "$TARGET" "cat > /tmp/users_grants.sql && sudo $MYSQL_BIN < /tmp/users_grants.sql && rm -f /tmp/users_grants.sql"
}

# =============================================================================
# Main
# =============================================================================
say "Starting DR push to $TARGET (SSH port $SSH_PORT)"

# Sync data directories
for d in "${DATA_DIRS[@]}"; do
    if [ -d "$d" ]; then
        push_dir "$d" "$d"
    else
        echo "WARN: Directory $d not found, skipping"
    fi
done

# Sync /etc (safe subset)
push_etc

# Sync MySQL if available
if ensure_remote_mysql; then
    stream_databases
    replicate_users_and_grants
fi

say "DR push complete."
