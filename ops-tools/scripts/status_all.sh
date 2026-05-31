#!/usr/bin/env bash
# =============================================================================
# status_all.sh - Display Fleet-Wide Server Status
# =============================================================================
# Shows status of all servers in the fleet by querying the peer_status MySQL
# database. Designed to run on the core host for fleet-wide visibility.
#
# Usage:
#   ./status_all.sh [OPTIONS]
#
# Options:
#   --mysql-host HOST    MySQL host (default: localhost or core.iaregamer.com)
#   --mysql-port PORT    MySQL port (default: 3306)
#   --json               Output status as JSON
#   --summary            Show only summary (no individual servers)
#   --offline-only       Show only offline/degraded servers
#   --help               Show this help message
#
# Examples:
#   ./status_all.sh                    # Display all server status
#   ./status_all.sh --json             # Output as JSON
#   ./status_all.sh --summary          # Show only totals
#   ./status_all.sh --offline-only     # Show problem servers only
# =============================================================================

set -euo pipefail

# =============================================================================
# Configuration
# =============================================================================
TOOLS_DIR="/home/gameserver/tools"
PASSFILE="${TOOLS_DIR}/.password"
MYSQL_HOST="${MYSQL_HOST:-core.iaregamer.com}"
MYSQL_PORT="${MYSQL_PORT:-3306}"
MYSQL_USER="remoteuser"
MYSQL_DB="peer_status"

# Flags
OUTPUT_JSON=false
SUMMARY_ONLY=false
OFFLINE_ONLY=false

# Thresholds for status
OFFLINE_MINUTES=10
CPU_DEGRADED_PCT=90
MEM_DEGRADED_PCT=95
DISK_DEGRADED_PCT=95

# =============================================================================
# Helpers
# =============================================================================
die() { echo "ERROR: $*" >&2; exit 1; }

# =============================================================================
# Parse Arguments
# =============================================================================
while [[ $# -gt 0 ]]; do
    case "$1" in
        --mysql-host)
            MYSQL_HOST="$2"
            shift 2
            ;;
        --mysql-port)
            MYSQL_PORT="$2"
            shift 2
            ;;
        --json)
            OUTPUT_JSON=true
            shift
            ;;
        --summary)
            SUMMARY_ONLY=true
            shift
            ;;
        --offline-only)
            OFFLINE_ONLY=true
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
# Check Requirements
# =============================================================================
if ! command -v mysql >/dev/null 2>&1; then
    die "MySQL client not found. Install mysql-client."
fi

# Get MySQL password
MYSQL_PASS=""
if [ -f /root/.my.cnf ]; then
    # Use root's my.cnf if available (on core host)
    MYSQL_AUTH="--defaults-file=/root/.my.cnf"
elif [ -s "${PASSFILE}" ]; then
    MYSQL_PASS="$(tr -d '\r\n' < "${PASSFILE}")"
    MYSQL_AUTH="-u ${MYSQL_USER} -p${MYSQL_PASS}"
else
    die "No MySQL credentials found. Provide /root/.my.cnf or ${PASSFILE}"
fi

# =============================================================================
# Format Helpers
# =============================================================================
format_bytes() {
    local bytes="$1"
    if [[ $bytes -ge 1073741824 ]]; then
        printf "%.1f GB" "$(echo "$bytes / 1073741824" | bc -l)"
    elif [[ $bytes -ge 1048576 ]]; then
        printf "%.1f MB" "$(echo "$bytes / 1048576" | bc -l)"
    else
        echo "$bytes B"
    fi
}

get_status_color() {
    local status="$1"
    case "$status" in
        healthy) echo "32" ;;   # Green
        degraded) echo "33" ;;  # Yellow
        offline) echo "31" ;;   # Red
        *) echo "0" ;;          # Default
    esac
}

# =============================================================================
# Fetch Data
# =============================================================================
fetch_server_data() {
    # shellcheck disable=SC2086
    mysql -h "${MYSQL_HOST}" -P "${MYSQL_PORT}" ${MYSQL_AUTH} --protocol=TCP -N -B <<SQL 2>/dev/null
USE ${MYSQL_DB};
SELECT 
    m.hostname,
    n.ip,
    m.ts,
    m.cpu_used_pct,
    m.mem_used_bytes,
    m.mem_total_bytes,
    m.disk_used_bytes,
    m.disk_total_bytes,
    TIMESTAMPDIFF(MINUTE, m.ts, NOW()) as mins_since_update
FROM metrics m
JOIN (
    SELECT hostname, MAX(ts) AS max_ts 
    FROM metrics 
    GROUP BY hostname
) x ON m.hostname = x.hostname AND m.ts = x.max_ts
JOIN nodes n ON n.hostname = m.hostname
ORDER BY m.hostname;
SQL
}

# =============================================================================
# Calculate Status
# =============================================================================
get_server_status() {
    local mins_since="$1"
    local cpu="$2"
    local mem_used="$3"
    local mem_total="$4"
    local disk_used="$5"
    local disk_total="$6"
    
    # Check if offline
    if [[ $mins_since -gt $OFFLINE_MINUTES ]]; then
        echo "offline"
        return
    fi
    
    # Calculate percentages
    local mem_pct=0 disk_pct=0
    if [[ $mem_total -gt 0 ]]; then
        mem_pct=$(echo "scale=0; ($mem_used * 100) / $mem_total" | bc)
    fi
    if [[ $disk_total -gt 0 ]]; then
        disk_pct=$(echo "scale=0; ($disk_used * 100) / $disk_total" | bc)
    fi
    
    # Check degraded thresholds
    local cpu_int
    cpu_int=$(printf "%.0f" "$cpu")
    if [[ $cpu_int -gt $CPU_DEGRADED_PCT ]] || [[ $mem_pct -gt $MEM_DEGRADED_PCT ]] || [[ $disk_pct -gt $DISK_DEGRADED_PCT ]]; then
        echo "degraded"
        return
    fi
    
    echo "healthy"
}

# =============================================================================
# Display Output
# =============================================================================
display_status() {
    local data
    data="$(fetch_server_data)"
    
    if [[ -z "$data" ]]; then
        echo "No server data found in peer_status database."
        echo "Run 'status.sh --report' on each server to populate metrics."
        exit 0
    fi
    
    # Count totals
    local total=0 healthy=0 degraded=0 offline=0
    local total_cpu=0 total_mem_used=0 total_mem_total=0 total_disk_used=0 total_disk_total=0
    
    # Parse data and calculate totals
    local servers_json="["
    local first=true
    
    while IFS=$'\t' read -r hostname ip ts cpu mem_used mem_total disk_used disk_total mins_since; do
        ((total++)) || true
        
        local status
        status=$(get_server_status "$mins_since" "$cpu" "$mem_used" "$mem_total" "$disk_used" "$disk_total")
        
        case "$status" in
            healthy) ((healthy++)) || true ;;
            degraded) ((degraded++)) || true ;;
            offline) ((offline++)) || true ;;
        esac
        
        # Aggregate totals (only for online servers)
        if [[ "$status" != "offline" ]]; then
            total_cpu=$(echo "$total_cpu + $cpu" | bc)
            total_mem_used=$((total_mem_used + mem_used))
            total_mem_total=$((total_mem_total + mem_total))
            total_disk_used=$((total_disk_used + disk_used))
            total_disk_total=$((total_disk_total + disk_total))
        fi
        
        # Build JSON array
        if [[ "$first" == "true" ]]; then
            first=false
        else
            servers_json+=","
        fi
        
        local mem_pct disk_pct
        mem_pct=$(echo "scale=1; ($mem_used * 100) / $mem_total" | bc 2>/dev/null || echo "0")
        disk_pct=$(echo "scale=1; ($disk_used * 100) / $disk_total" | bc 2>/dev/null || echo "0")
        
        servers_json+="{\"hostname\":\"$hostname\",\"ip\":\"$ip\",\"status\":\"$status\",\"cpu\":$cpu,\"mem_pct\":$mem_pct,\"disk_pct\":$disk_pct,\"last_update\":\"$ts\",\"mins_since\":$mins_since}"
        
    done <<< "$data"
    
    servers_json+="]"
    
    # Calculate averages
    local online=$((total - offline))
    local avg_cpu=0
    if [[ $online -gt 0 ]]; then
        avg_cpu=$(echo "scale=1; $total_cpu / $online" | bc)
    fi
    
    local total_mem_pct=0 total_disk_pct=0
    if [[ $total_mem_total -gt 0 ]]; then
        total_mem_pct=$(echo "scale=1; ($total_mem_used * 100) / $total_mem_total" | bc)
    fi
    if [[ $total_disk_total -gt 0 ]]; then
        total_disk_pct=$(echo "scale=1; ($total_disk_used * 100) / $total_disk_total" | bc)
    fi
    
    if [[ "$OUTPUT_JSON" == "true" ]]; then
        # JSON output
        cat <<EOF
{
  "summary": {
    "total": $total,
    "healthy": $healthy,
    "degraded": $degraded,
    "offline": $offline,
    "avg_cpu_pct": $avg_cpu,
    "total_memory": {
      "used_bytes": $total_mem_used,
      "total_bytes": $total_mem_total,
      "pct": $total_mem_pct
    },
    "total_disk": {
      "used_bytes": $total_disk_used,
      "total_bytes": $total_disk_total,
      "pct": $total_disk_pct
    }
  },
  "servers": $servers_json,
  "timestamp": "$(date -Iseconds)"
}
EOF
    else
        # Human-readable output
        echo ""
        echo "╔══════════════════════════════════════════════════════════════════════╗"
        echo "║                    Runlevel Server Fleet Status                           ║"
        echo "╠══════════════════════════════════════════════════════════════════════╣"
        echo "║  Servers: $total total | $(printf '\033[32m%d\033[0m' $healthy) healthy | $(printf '\033[33m%d\033[0m' $degraded) degraded | $(printf '\033[31m%d\033[0m' $offline) offline"
        echo "╠══════════════════════════════════════════════════════════════════════╣"
        echo "║  Fleet Totals (online servers):                                      ║"
        echo "║    Average CPU:  ${avg_cpu}%                                         "
        echo "║    Memory:       $(format_bytes $total_mem_used) / $(format_bytes $total_mem_total) (${total_mem_pct}%)"
        echo "║    Disk:         $(format_bytes $total_disk_used) / $(format_bytes $total_disk_total) (${total_disk_pct}%)"
        echo "╚══════════════════════════════════════════════════════════════════════╝"
        
        if [[ "$SUMMARY_ONLY" != "true" ]]; then
            echo ""
            echo "Individual Servers:"
            echo "────────────────────────────────────────────────────────────────────────"
            printf "%-20s %-15s %-10s %8s %8s %8s %12s\n" "Hostname" "IP" "Status" "CPU" "MEM" "DISK" "Last Update"
            echo "────────────────────────────────────────────────────────────────────────"
            
            while IFS=$'\t' read -r hostname ip ts cpu mem_used mem_total disk_used disk_total mins_since; do
                local status
                status=$(get_server_status "$mins_since" "$cpu" "$mem_used" "$mem_total" "$disk_used" "$disk_total")
                
                # Skip healthy if --offline-only
                if [[ "$OFFLINE_ONLY" == "true" ]] && [[ "$status" == "healthy" ]]; then
                    continue
                fi
                
                local mem_pct disk_pct
                mem_pct=$(echo "scale=0; ($mem_used * 100) / $mem_total" | bc 2>/dev/null || echo "0")
                disk_pct=$(echo "scale=0; ($disk_used * 100) / $disk_total" | bc 2>/dev/null || echo "0")
                
                local color
                color=$(get_status_color "$status")
                local status_display
                status_display=$(printf '\033[%sm%-10s\033[0m' "$color" "$status")
                
                local age_display
                if [[ $mins_since -lt 60 ]]; then
                    age_display="${mins_since}m ago"
                elif [[ $mins_since -lt 1440 ]]; then
                    age_display="$((mins_since / 60))h ago"
                else
                    age_display="$((mins_since / 1440))d ago"
                fi
                
                printf "%-20s %-15s %s %7.1f%% %7d%% %7d%% %12s\n" \
                    "$hostname" "$ip" "$status_display" "$cpu" "$mem_pct" "$disk_pct" "$age_display"
                    
            done <<< "$data"
            
            echo "────────────────────────────────────────────────────────────────────────"
        fi
        
        echo ""
        echo "Timestamp: $(date)"
        echo ""
    fi
}

# =============================================================================
# Main
# =============================================================================
display_status
