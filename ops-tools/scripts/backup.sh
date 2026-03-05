# Developed by World Domination Software LLC
#!/usr/bin/env bash
# =============================================================================
# backup.sh - Unified Backup Script for Game Servers
# =============================================================================
# Creates local backups of game data, web files, MySQL databases, and configs.
# Designed to run on each game server host via cron.
#
# Usage:
#   ./backup.sh [OPTIONS]
#
# Options:
#   --backup-root PATH    Root directory for backups (default: /sdb1/backups)
#   --keep-days N         Keep backups for N days (default: 7)
#   --no-mysql            Skip MySQL backup
#   --no-web              Skip web directory backup
#   --no-tools            Skip tools directory backup
#   --no-etc              Skip /etc config backup
#   --no-compress         Skip tarball compression (keep directory)
#   --dry-run             Show what would be done without making changes
#   --help                Show this help message
#
# Examples:
#   ./backup.sh                           # Full backup with defaults
#   ./backup.sh --backup-root /backups    # Custom backup location
#   ./backup.sh --no-mysql --keep-days 3  # Skip MySQL, keep 3 days
#
# Cron example (hourly backups):
#   0 * * * * /home/gameserver/tools/scripts/backup.sh >> /var/log/backup.log 2>&1
# =============================================================================

set -euo pipefail

# =============================================================================
# Configuration Defaults
# =============================================================================
BACKUP_ROOT="${BACKUP_ROOT:-/sdb1/backups}"
KEEP_DAYS="${KEEP_DAYS:-7}"
HOST="$(hostname -s)"
NOW="$(date +%F_%H%M)"

# Directories to backup
WEB_DIRS=("/var/www/html" "/var/www/files")
TOOLS_DIR="/home/gameserver/tools"

# MySQL settings
MYSQL_LOCAL_DEFAULTS="/root/.my.cnf"
MYSQL_EXCLUDE_REGEX='^(information_schema|performance_schema|mysql|sys)$'

# /etc backup excludes (host-specific files)
ETC_EXCLUDES=(
    "--exclude=ssh_host_*"
    "--exclude=hostname"
    "--exclude=machine-id"
    "--exclude=netplan/**"
    "--exclude=fstab"
)

# Flags
DO_MYSQL=true
DO_WEB=true
DO_TOOLS=true
DO_ETC=true
DO_COMPRESS=true
DRY_RUN=false

# =============================================================================
# Helpers
# =============================================================================
die() { echo "ERROR: $*" >&2; exit 1; }
say() { printf '[%s] %s\n' "$(date '+%F %T')" "$*"; }

# =============================================================================
# Parse Arguments
# =============================================================================
while [[ $# -gt 0 ]]; do
    case "$1" in
        --backup-root)
            BACKUP_ROOT="$2"
            shift 2
            ;;
        --keep-days)
            KEEP_DAYS="$2"
            shift 2
            ;;
        --no-mysql)
            DO_MYSQL=false
            shift
            ;;
        --no-web)
            DO_WEB=false
            shift
            ;;
        --no-tools)
            DO_TOOLS=false
            shift
            ;;
        --no-etc)
            DO_ETC=false
            shift
            ;;
        --no-compress)
            DO_COMPRESS=false
            shift
            ;;
        --dry-run)
            DRY_RUN=true
            shift
            ;;
        --help|-h)
            head -n 30 "$0" | tail -n 28
            exit 0
            ;;
        *)
            die "Unknown option: $1. Use --help for usage."
            ;;
    esac
done

# =============================================================================
# Setup
# =============================================================================
BASE="${BACKUP_ROOT}/${HOST}/${NOW}"

say "Starting backup for ${HOST}"
say "Backup destination: ${BASE}"

if [[ "$DRY_RUN" == "true" ]]; then
    say "[DRY-RUN] No changes will be made"
else
    mkdir -p "${BASE}"/{mysql,etc,web,tools}
fi

# =============================================================================
# MySQL Backup
# =============================================================================
backup_mysql() {
    if ! command -v mysql >/dev/null 2>&1; then
        say "MySQL client not found, skipping database backup"
        return 0
    fi
    
    say "Backing up MySQL databases to ${BASE}/mysql"
    
    local AUTH=()
    [ -f "${MYSQL_LOCAL_DEFAULTS}" ] && AUTH=(--defaults-file="${MYSQL_LOCAL_DEFAULTS}")
    
    # Get list of databases
    local DBS
    mapfile -t DBS < <(mysql "${AUTH[@]}" --batch --skip-column-names -e "SHOW DATABASES" 2>/dev/null | grep -Ev "${MYSQL_EXCLUDE_REGEX}" || true)
    
    if [[ ${#DBS[@]} -eq 0 ]]; then
        say "No databases found to backup"
        return 0
    fi
    
    for db in "${DBS[@]}"; do
        if [[ "$DRY_RUN" == "true" ]]; then
            say "[DRY-RUN] Would dump database: $db"
        else
            say "  Dumping: $db"
            mysqldump "${AUTH[@]}" --single-transaction --routines --triggers --events --hex-blob "$db" 2>/dev/null \
                | gzip -c > "${BASE}/mysql/${db}.sql.gz" || say "  WARN: Failed to dump $db"
        fi
    done
}

# =============================================================================
# Web Directory Backup
# =============================================================================
backup_web() {
    say "Backing up web directories"
    
    for dir in "${WEB_DIRS[@]}"; do
        if [[ -d "$dir" ]]; then
            local name
            name="$(basename "$dir")"
            if [[ "$DRY_RUN" == "true" ]]; then
                say "[DRY-RUN] Would backup: $dir -> ${BASE}/web/${name}/"
            else
                say "  Syncing: $dir"
                rsync -aH --delete "$dir/" "${BASE}/web/${name}/"
            fi
        else
            say "  Skipping (not found): $dir"
        fi
    done
}

# =============================================================================
# Tools Directory Backup
# =============================================================================
backup_tools() {
    if [[ -d "${TOOLS_DIR}" ]]; then
        if [[ "$DRY_RUN" == "true" ]]; then
            say "[DRY-RUN] Would backup: ${TOOLS_DIR} -> ${BASE}/tools/"
        else
            say "Backing up tools directory: ${TOOLS_DIR}"
            rsync -aH --delete "${TOOLS_DIR}/" "${BASE}/tools/"
        fi
    else
        say "Tools directory not found: ${TOOLS_DIR}"
    fi
}

# =============================================================================
# /etc Configuration Backup
# =============================================================================
backup_etc() {
    if [[ "$DRY_RUN" == "true" ]]; then
        say "[DRY-RUN] Would backup: /etc -> ${BASE}/etc/"
    else
        say "Backing up /etc configuration"
        rsync -aH --delete "${ETC_EXCLUDES[@]}" /etc/ "${BASE}/etc/"
    fi
}

# =============================================================================
# Compress Backup
# =============================================================================
compress_backup() {
    if [[ "$DRY_RUN" == "true" ]]; then
        say "[DRY-RUN] Would create archive: ${BASE}.tgz"
        return 0
    fi
    
    say "Creating compressed archive: ${BASE}.tgz"
    tar -C "$(dirname "${BASE}")" -czf "${BASE}.tgz" "$(basename "${BASE}")" && rm -rf "${BASE}"
    
    local size
    size="$(du -h "${BASE}.tgz" | cut -f1)"
    say "Archive created: ${BASE}.tgz (${size})"
}

# =============================================================================
# Cleanup Old Backups
# =============================================================================
cleanup_old_backups() {
    local backup_dir="${BACKUP_ROOT}/${HOST}"
    
    if [[ ! -d "$backup_dir" ]]; then
        return 0
    fi
    
    say "Cleaning up backups older than ${KEEP_DAYS} days"
    
    if [[ "$DRY_RUN" == "true" ]]; then
        find "$backup_dir" -maxdepth 1 -name "*.tgz" -mtime +"$KEEP_DAYS" -print | while read -r f; do
            say "[DRY-RUN] Would delete: $f"
        done
    else
        find "$backup_dir" -maxdepth 1 -name "*.tgz" -mtime +"$KEEP_DAYS" -delete -print | while read -r f; do
            say "  Deleted: $f"
        done
    fi
}

# =============================================================================
# Main
# =============================================================================

# Run backups based on flags
[[ "$DO_MYSQL" == "true" ]] && backup_mysql
[[ "$DO_WEB" == "true" ]] && backup_web
[[ "$DO_TOOLS" == "true" ]] && backup_tools
[[ "$DO_ETC" == "true" ]] && backup_etc

# Compress if requested
[[ "$DO_COMPRESS" == "true" ]] && compress_backup

# Cleanup old backups
cleanup_old_backups

say "Backup complete for ${HOST}"
