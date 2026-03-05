# Developed by World Domination Software LLC
#!/usr/bin/env bash
# =============================================================================
# change_passwd.sh - Unified Password Rotation Script
# =============================================================================
# Rotates passwords across the WDS infrastructure:
#   - Linux gameserver user password on all hosts
#   - MySQL localuser and remoteuser accounts on core and core-dr
#   - Updates .password file on all hosts
#
# Usage:
#   ./change_passwd.sh <NEW_PASSWORD> [OPTIONS]
#
# Options:
#   --core-only           Only rotate on core/core-dr (skip game servers)
#   --mysql-only          Only rotate MySQL passwords
#   --linux-only          Only rotate Linux passwords
#   --servers-file FILE   Path to servers.txt (default: /home/gameserver/tools/servers.txt)
#   --dry-run             Show what would be done without making changes
#   --help                Show this help message
#
# Examples:
#   ./change_passwd.sh "NewSuperSecret123!"              # Full rotation
#   ./change_passwd.sh "NewPass!" --core-only            # Core hosts only
#   ./change_passwd.sh "NewPass!" --mysql-only           # MySQL only
#   ./change_passwd.sh "NewPass!" --dry-run              # Preview changes
#
# Requirements:
#   - Must be run from the core host (verified by DNS check)
#   - sshpass for remote SSH (or SSH keys configured)
#   - MySQL root access on core for MySQL rotation
#
# After running:
#   1. Update content/staff-credentials.json with the new password
#   2. Run dr_rsync_push.sh to sync to core-dr
#   3. Commit and push the updated credentials file
# =============================================================================

set -euo pipefail

# =============================================================================
# Configuration
# =============================================================================
TOOLS_DIR="/home/gameserver/tools"
PASSFILE="${TOOLS_DIR}/.password"
SERVERS_FILE="${TOOLS_DIR}/servers.txt"
DEFAULT_SSH_PORT=12322
MYSQL_DB_NAME="peer_status"
LINUX_USER="gameserver"

# Core hosts for MySQL rotation
CORE_HOST="core.iaregamer.com"
CORE_DR_HOST="core-dr.iaregamer.com"

# Flags
DO_MYSQL=true
DO_LINUX=true
CORE_ONLY=false
DRY_RUN=false

# =============================================================================
# Helpers
# =============================================================================
die() { echo "ERROR: $*" >&2; exit 1; }
say() { printf '[%s] %s\n' "$(date '+%F %T')" "$*"; }
warn() { echo "WARNING: $*" >&2; }

# =============================================================================
# Parse Arguments
# =============================================================================
if [[ $# -lt 1 ]]; then
    head -n 35 "$0" | tail -n 33
    exit 1
fi

NEW_PASSWORD="$1"
shift

while [[ $# -gt 0 ]]; do
    case "$1" in
        --core-only)
            CORE_ONLY=true
            shift
            ;;
        --mysql-only)
            DO_LINUX=false
            shift
            ;;
        --linux-only)
            DO_MYSQL=false
            shift
            ;;
        --servers-file)
            SERVERS_FILE="$2"
            shift 2
            ;;
        --dry-run)
            DRY_RUN=true
            shift
            ;;
        --help|-h)
            head -n 35 "$0" | tail -n 33
            exit 0
            ;;
        *)
            die "Unknown option: $1. Use --help for usage."
            ;;
    esac
done

# Validate password
if [[ ${#NEW_PASSWORD} -lt 8 ]]; then
    die "Password must be at least 8 characters long"
fi

# Warn about weak passwords (but don't enforce - user may have specific requirements)
has_upper=false
has_lower=false
has_digit=false
has_special=false
[[ "$NEW_PASSWORD" =~ [A-Z] ]] && has_upper=true
[[ "$NEW_PASSWORD" =~ [a-z] ]] && has_lower=true
[[ "$NEW_PASSWORD" =~ [0-9] ]] && has_digit=true
[[ "$NEW_PASSWORD" =~ [^a-zA-Z0-9] ]] && has_special=true

if ! $has_upper || ! $has_lower || ! $has_digit || ! $has_special; then
    warn "Password may be weak. Consider using uppercase, lowercase, numbers, and special characters."
fi

# =============================================================================
# Verify Core Identity
# =============================================================================
verify_core_identity() {
    say "Verifying this host is the core..."
    
    local dns_ip
    dns_ip="$(getent ahostsv4 "$CORE_HOST" 2>/dev/null | awk '{print $1; exit}' || true)"
    
    if [[ -z "$dns_ip" ]]; then
        die "Cannot resolve $CORE_HOST"
    fi
    
    local local_ips
    local_ips="$(hostname -I 2>/dev/null || echo '')"
    
    if printf "%s\n" $local_ips | grep -qE "\b${dns_ip}\b"; then
        say "Verified: Running on core host"
        return 0
    fi
    
    # Try public IP check
    if command -v curl >/dev/null 2>&1; then
        local pub
        pub="$(curl -s --connect-timeout 5 https://ipinfo.io/ip 2>/dev/null || true)"
        if [[ -n "$pub" ]] && [[ "$pub" == "$dns_ip" ]]; then
            say "Verified: Running on core host (via public IP)"
            return 0
        fi
    fi
    
    die "This script must be run from the core host ($CORE_HOST)"
}

# =============================================================================
# Resolve IPv4 Address
# =============================================================================
resolve_ipv4() {
    local host="$1"
    getent ahostsv4 "$host" 2>/dev/null | awk '{print $1; exit}'
}

# =============================================================================
# SSH Functions
# =============================================================================
ssh_exec() {
    local host="$1"
    local port="$2"
    local cmd="$3"
    
    local SSH_OPTS="-o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -o ConnectTimeout=10"
    
    if command -v sshpass >/dev/null 2>&1 && [[ -s "${PASSFILE}" ]]; then
        sshpass -f "${PASSFILE}" ssh -p "$port" $SSH_OPTS "${LINUX_USER}@${host}" "$cmd"
    else
        ssh -p "$port" $SSH_OPTS "${LINUX_USER}@${host}" "$cmd"
    fi
}

scp_file() {
    local src="$1"
    local host="$2"
    local port="$3"
    local dest="$4"
    
    local SSH_OPTS="-o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -o ConnectTimeout=10"
    
    if command -v sshpass >/dev/null 2>&1 && [[ -s "${PASSFILE}" ]]; then
        sshpass -f "${PASSFILE}" scp -P "$port" $SSH_OPTS "$src" "${LINUX_USER}@${host}:${dest}"
    else
        scp -P "$port" $SSH_OPTS "$src" "${LINUX_USER}@${host}:${dest}"
    fi
}

# =============================================================================
# Update Local Password File
# =============================================================================
update_local_passfile() {
    say "Updating local password file: ${PASSFILE}"
    
    if [[ "$DRY_RUN" == "true" ]]; then
        say "[DRY-RUN] Would update ${PASSFILE}"
        return 0
    fi
    
    printf '%s\n' "$NEW_PASSWORD" | sudo tee "${PASSFILE}" > /dev/null
    sudo chmod 600 "${PASSFILE}"
    sudo chown "${LINUX_USER}:${LINUX_USER}" "${PASSFILE}"
}

# =============================================================================
# Rotate MySQL Passwords on Core
# =============================================================================
rotate_mysql_core() {
    say "Rotating MySQL passwords on local host..."
    
    if ! command -v mysql >/dev/null 2>&1; then
        warn "MySQL client not found, skipping MySQL rotation"
        return 0
    fi
    
    if [[ ! -f /root/.my.cnf ]]; then
        warn "/root/.my.cnf not found, cannot rotate MySQL passwords"
        return 0
    fi
    
    if [[ "$DRY_RUN" == "true" ]]; then
        say "[DRY-RUN] Would rotate MySQL localuser password"
        say "[DRY-RUN] Would rotate MySQL remoteuser passwords for all servers"
        return 0
    fi
    
    # Rotate localuser
    say "  Rotating localuser@localhost"
    mysql --defaults-file=/root/.my.cnf <<SQL || warn "Failed to rotate localuser"
CREATE USER IF NOT EXISTS 'localuser'@'localhost' IDENTIFIED BY '${NEW_PASSWORD}';
ALTER USER 'localuser'@'localhost' IDENTIFIED BY '${NEW_PASSWORD}';
GRANT ALL PRIVILEGES ON *.* TO 'localuser'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;
SQL
    
    # Rotate remoteuser for each server
    if [[ -s "${SERVERS_FILE}" ]]; then
        while IFS= read -r line; do
            [[ -z "$line" ]] && continue
            [[ "$line" =~ ^# ]] && continue
            
            local host="${line%%:*}"
            local ip
            ip="$(resolve_ipv4 "$host")"
            
            if [[ -z "$ip" ]]; then
                warn "Cannot resolve $host, skipping MySQL grant"
                continue
            fi
            
            say "  Rotating remoteuser@${ip} (${host})"
            mysql --defaults-file=/root/.my.cnf <<SQL || warn "Failed to rotate remoteuser@${ip}"
CREATE USER IF NOT EXISTS 'remoteuser'@'${ip}' IDENTIFIED BY '${NEW_PASSWORD}';
ALTER USER 'remoteuser'@'${ip}' IDENTIFIED BY '${NEW_PASSWORD}';
CREATE DATABASE IF NOT EXISTS ${MYSQL_DB_NAME} DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
GRANT INSERT ON ${MYSQL_DB_NAME}.metrics TO 'remoteuser'@'${ip}';
GRANT INSERT, UPDATE ON ${MYSQL_DB_NAME}.nodes TO 'remoteuser'@'${ip}';
GRANT SELECT ON ${MYSQL_DB_NAME}.* TO 'remoteuser'@'${ip}';
FLUSH PRIVILEGES;
SQL
        done < "${SERVERS_FILE}"
    fi
}

# =============================================================================
# Rotate Linux Password on Remote Host
# =============================================================================
rotate_linux_remote() {
    local host="$1"
    local port="$2"
    
    say "Rotating Linux password on ${host}:${port}"
    
    if [[ "$DRY_RUN" == "true" ]]; then
        say "[DRY-RUN] Would change ${LINUX_USER} password on ${host}"
        say "[DRY-RUN] Would update ${PASSFILE} on ${host}"
        return 0
    fi
    
    # Change Linux password
    if ! ssh_exec "$host" "$port" "echo '${LINUX_USER}:${NEW_PASSWORD}' | sudo chpasswd" 2>/dev/null; then
        warn "Failed to change Linux password on ${host}"
        return 1
    fi
    
    # Update password file on remote
    local tmpfile
    tmpfile="$(mktemp)"
    printf '%s\n' "$NEW_PASSWORD" > "$tmpfile"
    
    if scp_file "$tmpfile" "$host" "$port" "/tmp/.password.new" 2>/dev/null; then
        ssh_exec "$host" "$port" "sudo mv /tmp/.password.new ${PASSFILE} && sudo chmod 600 ${PASSFILE} && sudo chown ${LINUX_USER}:${LINUX_USER} ${PASSFILE}" 2>/dev/null || \
            warn "Failed to update ${PASSFILE} on ${host}"
    else
        warn "Failed to copy password file to ${host}"
    fi
    
    rm -f "$tmpfile"
}

# =============================================================================
# Main
# =============================================================================
say "=============================================="
say "  WDS Password Rotation"
say "=============================================="
say ""
say "Password length: ${#NEW_PASSWORD} characters"
say "Rotate MySQL: $DO_MYSQL"
say "Rotate Linux: $DO_LINUX"
say "Core only: $CORE_ONLY"
say "Dry run: $DRY_RUN"
say ""

# Verify we're on core
verify_core_identity

# Update local password file first
update_local_passfile

# Rotate MySQL on core
if [[ "$DO_MYSQL" == "true" ]]; then
    rotate_mysql_core
fi

# Rotate Linux passwords on all servers
if [[ "$DO_LINUX" == "true" ]] && [[ "$CORE_ONLY" != "true" ]]; then
    if [[ ! -s "${SERVERS_FILE}" ]]; then
        warn "servers.txt not found at ${SERVERS_FILE}"
    else
        say ""
        say "Rotating Linux passwords on remote servers..."
        
        while IFS= read -r line; do
            [[ -z "$line" ]] && continue
            [[ "$line" =~ ^# ]] && continue
            
            local host="${line%%:*}"
            local port="${line##*:}"
            [[ "$host" == "$port" ]] && port="$DEFAULT_SSH_PORT"
            
            rotate_linux_remote "$host" "$port" || true
            
        done < "${SERVERS_FILE}"
    fi
fi

say ""
say "=============================================="
say "  Password Rotation Complete"
say "=============================================="
say ""
say "Next steps:"
say "  1. Update content/staff-credentials.json with the new password"
say "  2. Run: ./dr_rsync_push.sh core-dr.iaregamer.com"
say "  3. Commit and push the updated credentials file"
say ""

if [[ "$DRY_RUN" == "true" ]]; then
    say "NOTE: This was a dry run. No changes were made."
fi
