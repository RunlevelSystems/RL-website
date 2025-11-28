#!/usr/bin/env bash
# =============================================================================
# status.sh - Display Local Server Status
# =============================================================================
# Shows current CPU, memory, disk usage, and top processes on this server.
# Can also report status to MySQL database for fleet monitoring.
#
# Usage:
#   ./status.sh [OPTIONS]
#
# Options:
#   --report                  Report status to MySQL database
#   --mysql-host HOST         MySQL host (default: core.iaregamer.com)
#   --mysql-port PORT         MySQL port (default: 3306)
#   --json                    Output status as JSON
#   --quiet                   Only output values, no headers
#   --help                    Show this help message
#
# Examples:
#   ./status.sh                              # Display local status
#   ./status.sh --json                       # Output as JSON
#   ./status.sh --report                     # Report to MySQL
#   ./status.sh --report --mysql-host db1    # Report to specific host
#
# Cron example (every 5 minutes):
#   */5 * * * * /home/gameserver/tools/scripts/status.sh --report
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
DO_REPORT=false
OUTPUT_JSON=false
QUIET=false

# =============================================================================
# Helpers
# =============================================================================
die() { echo "ERROR: $*" >&2; exit 1; }
say() { [[ "$QUIET" == "false" ]] && printf '[%s] %s\n' "$(date '+%F %T')" "$*" || true; }

# =============================================================================
# Parse Arguments
# =============================================================================
while [[ $# -gt 0 ]]; do
    case "$1" in
        --report)
            DO_REPORT=true
            shift
            ;;
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
        --quiet|-q)
            QUIET=true
            shift
            ;;
        --help|-h)
            head -n 32 "$0" | tail -n 30
            exit 0
            ;;
        *)
            die "Unknown option: $1. Use --help for usage."
            ;;
    esac
done

# =============================================================================
# Get Hostname and IP
# =============================================================================
get_hostname() { hostname -s 2>/dev/null || hostname; }

get_ip() {
    ip -4 -o addr show 2>/dev/null | awk '!/ lo /{sub(/\/.*/,"",$4); print $4; exit}' || \
    hostname -I 2>/dev/null | awk '{print $1}' || echo "0.0.0.0"
}

# =============================================================================
# Linux Resource Collection
# =============================================================================
linux_cpu_pct() {
    # Read /proc/stat twice with 1 second delay to calculate CPU usage
    read -r _ u n s i io irq si st _ < /proc/stat
    local t1=$((u+n+s+i+io+irq+si+st))
    local id1=$((i+io))
    sleep 1
    read -r _ u2 n2 s2 i2 io2 irq2 si2 st2 _ < /proc/stat
    local t2=$((u2+n2+s2+i2+io2+irq2+si2+st2))
    local id2=$((i2+io2))
    local td=$((t2-t1))
    local idd=$((id2-id1))
    
    if command -v python3 >/dev/null 2>&1; then
        python3 -c "td=${td}; idd=${idd}; print(round(((td-idd)*100.0)/td,2) if td>0 else 0)"
    elif command -v bc >/dev/null 2>&1; then
        echo "scale=2; ((${td}-${idd})*100)/${td}" | bc
    else
        echo "0"
    fi
}

linux_mem_bytes() {
    local mem_total mem_avail mem_used
    mem_total=$(awk '/MemTotal/ {print $2*1024}' /proc/meminfo)
    mem_avail=$(awk '/MemAvailable/ {print $2*1024}' /proc/meminfo)
    mem_used=$((mem_total-mem_avail))
    echo "${mem_used} ${mem_total}"
}

linux_disk_bytes() {
    df -B1 -x tmpfs -x devtmpfs -P 2>/dev/null | awk 'NR>1 {u+=$3; t+=$2} END{print u, t}'
}

linux_top_procs_json() {
    ps -eo pid,comm,pcpu,pmem --no-headers --sort=-pcpu 2>/dev/null | head -n 5 | \
    awk 'BEGIN{printf "["} {printf "%s{\"pid\":%d,\"name\":\"%s\",\"cpu\":%s,\"mem\":%s}", NR>1?",":"", $1,$2,$3,$4} END{print "]"}'
}

# =============================================================================
# Windows Resource Collection (Cygwin/PowerShell)
# =============================================================================
win_has_powershell() { command -v powershell.exe >/dev/null 2>&1; }

win_cpu_pct() {
    powershell.exe -NoProfile -Command "(Get-Counter '\Processor(_Total)\% Processor Time').CounterSamples.CookedValue | %{[math]::Round(\$_,2)}"
}

win_mem_bytes() {
    powershell.exe -NoProfile -Command "(Get-CimInstance Win32_OperatingSystem) | %{\$t=[int64]\$_.TotalVisibleMemorySize*1024;\$f=[int64]\$_.FreePhysicalMemory*1024; \$u=\$t-\$f; Write-Output \"\$u \$t\" }"
}

win_top_procs_json() {
    powershell.exe -NoProfile -Command "Get-Process | Sort-Object CPU -Descending | Select-Object -First 5 Id,ProcessName,CPU,PM | ConvertTo-Json"
}

# =============================================================================
# Collect Metrics
# =============================================================================
collect_metrics() {
    local hostname ip cpu mem_used mem_total disk_used disk_total top_json
    
    hostname="$(get_hostname)"
    ip="$(get_ip)"
    
    if [ -f /proc/stat ]; then
        # Linux
        cpu="$(linux_cpu_pct)"
        read -r mem_used mem_total <<<"$(linux_mem_bytes)"
        read -r disk_used disk_total <<<"$(linux_disk_bytes)"
        top_json="$(linux_top_procs_json)"
    elif win_has_powershell; then
        # Windows via Cygwin
        cpu="$(win_cpu_pct | tr -d '\r\n')"
        read -r mem_used mem_total <<<"$(win_mem_bytes | tr -d '\r')"
        disk_used="0"
        disk_total="0"
        top_json="$(win_top_procs_json 2>/dev/null || echo '[]')"
    else
        cpu="0"
        mem_used="0"
        mem_total="0"
        disk_used="0"
        disk_total="0"
        top_json="[]"
    fi
    
    # Validate numeric values
    [[ "$cpu" =~ ^[0-9.]+$ ]] || cpu="0"
    [[ "$mem_used" =~ ^[0-9]+$ ]] || mem_used="0"
    [[ "$mem_total" =~ ^[0-9]+$ ]] || mem_total="0"
    [[ "$disk_used" =~ ^[0-9]+$ ]] || disk_used="0"
    [[ "$disk_total" =~ ^[0-9]+$ ]] || disk_total="0"
    
    echo "$hostname|$ip|$cpu|$mem_used|$mem_total|$disk_used|$disk_total|$top_json"
}

# =============================================================================
# Format Output
# =============================================================================
format_bytes() {
    local bytes="$1"
    if [[ $bytes -ge 1073741824 ]]; then
        echo "$(echo "scale=1; $bytes/1073741824" | bc) GB"
    elif [[ $bytes -ge 1048576 ]]; then
        echo "$(echo "scale=1; $bytes/1048576" | bc) MB"
    else
        echo "$bytes B"
    fi
}

display_status() {
    local metrics
    metrics="$(collect_metrics)"
    
    IFS='|' read -r hostname ip cpu mem_used mem_total disk_used disk_total top_json <<< "$metrics"
    
    if [[ "$OUTPUT_JSON" == "true" ]]; then
        # JSON output
        local mem_pct disk_pct
        mem_pct=$(echo "scale=1; ($mem_used * 100) / $mem_total" | bc 2>/dev/null || echo "0")
        disk_pct=$(echo "scale=1; ($disk_used * 100) / $disk_total" | bc 2>/dev/null || echo "0")
        
        cat <<EOF
{
  "hostname": "$hostname",
  "ip": "$ip",
  "cpu_pct": $cpu,
  "memory": {
    "used_bytes": $mem_used,
    "total_bytes": $mem_total,
    "pct": $mem_pct
  },
  "disk": {
    "used_bytes": $disk_used,
    "total_bytes": $disk_total,
    "pct": $disk_pct
  },
  "top_processes": $top_json,
  "timestamp": "$(date -Iseconds)"
}
EOF
    else
        # Human-readable output
        local mem_pct disk_pct
        mem_pct=$(echo "scale=1; ($mem_used * 100) / $mem_total" | bc 2>/dev/null || echo "0")
        disk_pct=$(echo "scale=1; ($disk_used * 100) / $disk_total" | bc 2>/dev/null || echo "0")
        
        echo "========================================"
        echo " Server Status: $hostname ($ip)"
        echo "========================================"
        echo ""
        echo " CPU Usage:     ${cpu}%"
        echo " Memory:        $(format_bytes "$mem_used") / $(format_bytes "$mem_total") (${mem_pct}%)"
        echo " Disk:          $(format_bytes "$disk_used") / $(format_bytes "$disk_total") (${disk_pct}%)"
        echo ""
        echo " Top 5 Processes by CPU:"
        echo " ----------------------------------------"
        echo "$top_json" | python3 -c "
import json, sys
try:
    procs = json.load(sys.stdin)
    for p in procs:
        print(f\"   {p['name']:<20} PID:{p['pid']:<8} CPU:{p['cpu']:.1f}%  MEM:{p['mem']:.1f}%\")
except:
    print('   (Unable to parse process list)')
" 2>/dev/null || echo "   (Unable to parse process list)"
        echo ""
        echo " Timestamp: $(date)"
        echo "========================================"
    fi
}

# =============================================================================
# Report to MySQL
# =============================================================================
ensure_schema() {
    local host="$1" port="$2" pass="$3"
    
    mysql -h "${host}" -P "${port}" -u "${MYSQL_USER}" "-p${pass}" --protocol=TCP <<SQL 2>/dev/null || true
CREATE DATABASE IF NOT EXISTS ${MYSQL_DB} DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE ${MYSQL_DB};
CREATE TABLE IF NOT EXISTS nodes (
    hostname VARCHAR(128) PRIMARY KEY,
    ip VARCHAR(45),
    first_seen DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_seen DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS metrics (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    hostname VARCHAR(128) NOT NULL,
    ts DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    cpu_used_pct DECIMAL(5,2) NOT NULL,
    mem_used_bytes BIGINT UNSIGNED NOT NULL,
    mem_total_bytes BIGINT UNSIGNED NOT NULL,
    disk_used_bytes BIGINT UNSIGNED NOT NULL,
    disk_total_bytes BIGINT UNSIGNED NOT NULL,
    top_procs JSON NULL,
    INDEX (hostname, ts),
    CONSTRAINT fk_host_status FOREIGN KEY (hostname) REFERENCES nodes(hostname) ON DELETE CASCADE
) ENGINE=InnoDB;
SQL
}

report_to_mysql() {
    if ! command -v mysql >/dev/null 2>&1; then
        die "MySQL client not found. Install mysql-client to report status."
    fi
    
    if [ ! -s "${PASSFILE}" ]; then
        die "Password file ${PASSFILE} missing or empty"
    fi
    
    local pass
    pass="$(tr -d '\r\n' < "${PASSFILE}")"
    
    say "Reporting status to ${MYSQL_HOST}:${MYSQL_PORT}"
    
    # Ensure schema exists
    ensure_schema "${MYSQL_HOST}" "${MYSQL_PORT}" "${pass}"
    
    # Collect metrics
    local metrics
    metrics="$(collect_metrics)"
    
    IFS='|' read -r hostname ip cpu mem_used mem_total disk_used disk_total top_json <<< "$metrics"
    
    # Sanitize hostname and IP
    hostname="$(printf '%s' "$hostname" | sed 's/[^a-zA-Z0-9._-]//g')"
    ip="$(printf '%s' "$ip" | sed 's/[^0-9.]//g')"
    
    # Escape JSON for MySQL
    local esc_json
    esc_json="$(printf '%s' "${top_json}" | sed -e 's/\\/\\\\/g' -e "s/'/''/g")"
    
    # Insert into database
    mysql -h "${MYSQL_HOST}" -P "${MYSQL_PORT}" -u "${MYSQL_USER}" "-p${pass}" --protocol=TCP <<SQL
USE ${MYSQL_DB};
INSERT INTO nodes (hostname, ip, first_seen, last_seen) 
    VALUES ('${hostname}', '${ip}', NOW(), NOW()) 
    ON DUPLICATE KEY UPDATE ip=VALUES(ip), last_seen=NOW();
INSERT INTO metrics (hostname, cpu_used_pct, mem_used_bytes, mem_total_bytes, disk_used_bytes, disk_total_bytes, top_procs) 
    VALUES ('${hostname}', ${cpu}+0, ${mem_used}+0, ${mem_total}+0, ${disk_used}+0, ${disk_total}+0, '${esc_json}');
SQL
    
    say "Reported: ${hostname} (${ip}) CPU=${cpu}%"
}

# =============================================================================
# Main
# =============================================================================
if [[ "$DO_REPORT" == "true" ]]; then
    report_to_mysql
else
    display_status
fi
