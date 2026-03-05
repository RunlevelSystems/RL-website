# Developed by World Domination Software LLC
#!/usr/bin/env bash
# =============================================================================
# xfer.sh - Simple Folder Transfer Script
# =============================================================================
# Transfers a folder from this server to another using rsync over SSH.
# Designed for quick ad-hoc transfers between WDS infrastructure hosts.
#
# Usage:
#   ./xfer.sh <SOURCE_PATH> <DEST_HOST> [OPTIONS]
#
# Options:
#   --dest-path PATH     Destination path (default: same as source)
#   --port PORT          SSH port (default: 12322)
#   --delete             Delete files on destination that don't exist in source
#   --dry-run            Show what would be transferred without doing it
#   --background         Run transfer in background with nohup
#   --check              Check status of background transfer
#   --help               Show this help message
#
# Examples:
#   ./xfer.sh /sdb1/backups kc.iaregamer.com
#   ./xfer.sh /var/www/html core-dr --dest-path /var/www/html
#   ./xfer.sh /sdb1/ backup.server.com --port 22 --delete
#   ./xfer.sh /data remote.host --background
#   ./xfer.sh --check
#
# Security:
#   Uses SSH keys if available, falls back to password from .password file.
#   SSH strict host key checking is disabled for infrastructure hosts.
# =============================================================================

set -euo pipefail

# =============================================================================
# Configuration
# =============================================================================
TOOLS_DIR="/home/gameserver/tools"
PASSFILE="${TOOLS_DIR}/.password"
DEFAULT_SSH_PORT=12322
LOGFILE="${HOME}/xfer.log"

# Flags
DEST_PATH=""
SSH_PORT="$DEFAULT_SSH_PORT"
DO_DELETE=false
DRY_RUN=false
BACKGROUND=false

# SSH options - simplified for infrastructure use
SSH_OPTS="-o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -o BatchMode=yes -o ConnectTimeout=30"

# =============================================================================
# Helpers
# =============================================================================
die() { echo "ERROR: $*" >&2; exit 1; }
say() { printf '[%s] %s\n' "$(date '+%F %T')" "$*"; }
warn() { echo "WARNING: $*" >&2; }

# =============================================================================
# Parse Arguments
# =============================================================================
show_help() {
    head -n 30 "$0" | tail -n 28
    exit 0
}

# Handle --check mode first
if [[ "${1:-}" == "--check" ]]; then
    if [[ -f "$LOGFILE" ]]; then
        echo "Transfer log: $LOGFILE"
        echo "Press Ctrl+C to stop viewing."
        echo "----------------------------------------"
        tail -f "$LOGFILE"
    else
        echo "No transfer log found at: $LOGFILE"
        echo "Start a transfer first with: $0 <source> <dest>"
    fi
    exit 0
fi

# Handle --help
if [[ "${1:-}" == "--help" ]] || [[ "${1:-}" == "-h" ]]; then
    show_help
fi

# Need at least 2 arguments
if [[ $# -lt 2 ]]; then
    echo "Usage: $0 <SOURCE_PATH> <DEST_HOST> [OPTIONS]"
    echo "       $0 --check  (to view background transfer progress)"
    echo ""
    echo "Use --help for full documentation."
    exit 1
fi

SOURCE_PATH="$1"
DEST_HOST="$2"
shift 2

# Parse remaining options
while [[ $# -gt 0 ]]; do
    case "$1" in
        --dest-path)
            DEST_PATH="$2"
            shift 2
            ;;
        --port)
            SSH_PORT="$2"
            shift 2
            ;;
        --delete)
            DO_DELETE=true
            shift
            ;;
        --dry-run)
            DRY_RUN=true
            shift
            ;;
        --background)
            BACKGROUND=true
            shift
            ;;
        --help|-h)
            show_help
            ;;
        *)
            die "Unknown option: $1. Use --help for usage."
            ;;
    esac
done

# Default destination path to source path
[[ -z "$DEST_PATH" ]] && DEST_PATH="$SOURCE_PATH"

# =============================================================================
# Validate Source
# =============================================================================
if [[ ! -e "$SOURCE_PATH" ]]; then
    die "Source path does not exist: $SOURCE_PATH"
fi

# =============================================================================
# Build Rsync Command
# =============================================================================
build_rsync_cmd() {
    local rsync_opts="-avh --partial --progress --stats"
    
    if [[ "$DO_DELETE" == "true" ]]; then
        rsync_opts="$rsync_opts --delete"
    fi
    
    if [[ "$DRY_RUN" == "true" ]]; then
        rsync_opts="$rsync_opts --dry-run"
    fi
    
    # Build SSH command
    local ssh_cmd="ssh -p ${SSH_PORT} ${SSH_OPTS}"
    
    # Check for sshpass
    local sshpass_prefix=""
    if command -v sshpass >/dev/null 2>&1 && [[ -s "${PASSFILE}" ]]; then
        sshpass_prefix="sshpass -f ${PASSFILE}"
    fi
    
    # Ensure source path ends with / for directory contents
    local src="$SOURCE_PATH"
    local last_char="${SOURCE_PATH: -1:1}"
    if [[ -d "$SOURCE_PATH" ]] && [[ "$last_char" != "/" ]]; then
        src="${SOURCE_PATH}/"
    fi
    
    # Build full command
    if [[ -n "$sshpass_prefix" ]]; then
        echo "$sshpass_prefix rsync $rsync_opts -e '$ssh_cmd' '$src' 'gameserver@${DEST_HOST}:${DEST_PATH}/'"
    else
        echo "rsync $rsync_opts -e '$ssh_cmd' '$src' 'gameserver@${DEST_HOST}:${DEST_PATH}/'"
    fi
}

# =============================================================================
# Run Transfer
# =============================================================================
run_transfer() {
    local cmd
    cmd="$(build_rsync_cmd)"
    
    say "Transfer starting..."
    say "  Source:      $SOURCE_PATH"
    say "  Destination: gameserver@${DEST_HOST}:${DEST_PATH}"
    say "  SSH Port:    $SSH_PORT"
    say "  Delete:      $DO_DELETE"
    say "  Dry Run:     $DRY_RUN"
    say ""
    
    if [[ "$BACKGROUND" == "true" ]]; then
        say "Running in background. Log: $LOGFILE"
        say "Use '$0 --check' to monitor progress."
        say ""
        
        # Run in background with nohup
        nohup bash -c "$cmd" >> "$LOGFILE" 2>&1 &
        local pid=$!
        say "Background PID: $pid"
        echo "$pid" > "${HOME}/.xfer_pid"
    else
        # Run in foreground
        eval "$cmd"
        
        say ""
        say "Transfer complete."
    fi
}

# =============================================================================
# Main
# =============================================================================

# Check for sshpass if password file exists
if [[ -s "${PASSFILE}" ]] && ! command -v sshpass >/dev/null 2>&1; then
    warn "Password file exists but sshpass is not installed."
    warn "Install sshpass or configure SSH keys."
    warn "Attempting transfer with SSH keys..."
fi

run_transfer
