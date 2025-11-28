#!/usr/bin/env bash
# =============================================================================
# report_server_status.sh - Server Resource Status Reporter
# =============================================================================
# Collects CPU, memory, disk usage, and top 5 processes from this server
# and uploads to the MySQL server_status database on core.iaregamer.com.
# Creates DB/tables on first run if they don't exist.
#
# Usage:
#   ./report_server_status.sh [--mysql-host HOST] [--mysql-port PORT]
#
# Defaults:
#   --mysql-host core.iaregamer.com
#   --mysql-port 3306
#
# Requirements:
#   - mysql client
#   - /home/gameserver/tools/.password file with MySQL password
# =============================================================================

set -euo pipefail

# CONFIG
TOOLS_DIR="/home/gameserver/tools"
PASSFILE="${TOOLS_DIR}/.password"
MYSQL_HOST="${MYSQL_HOST:-core.iaregamer.com}"
MYSQL_PORT="${MYSQL_PORT:-3306}"
MYSQL_USER="remoteuser"
MYSQL_DB="server_status"
# END CONFIG

die(){ echo "ERROR: $*" >&2; exit 1; }
say(){ printf '[%s] %s\n' "$(date '+%F %T')" "$*"; }
require_cmd(){ command -v "$1" >/dev/null 2>&1 || die "Missing required command: $1"; }

# Parse arguments
while [[ $# -gt 0 ]]; do
    case "$1" in
        --mysql-host) MYSQL_HOST="$2"; shift 2 ;;
        --mysql-port) MYSQL_PORT="$2"; shift 2 ;;
        --help|-h)
            echo "Usage: $0 [--mysql-host HOST] [--mysql-port PORT]"
            echo "  Collects server stats and uploads to MySQL server_status database"
            echo "  Defaults: --mysql-host core.iaregamer.com --mysql-port 3306"
            exit 0
            ;;
        *) die "Unknown option: $1" ;;
    esac
done

# Get hostname and IP
get_hostname(){ hostname -s 2>/dev/null || hostname; }
get_ip_guess(){ 
    ip -4 -o addr show 2>/dev/null | awk '!/ lo /{sub(/\/.*/,"",$4); print $4; exit}' || \
    hostname -I 2>/dev/null | awk '{print $1}' || echo "0.0.0.0"
}

# Linux resource collection
linux_cpu_pct(){ 
    read -r _ u n s i io irq si st _ < /proc/stat
    t1=$((u+n+s+i+io+irq+si+st))
    id1=$((i+io))
    sleep 1
    read -r _ u2 n2 s2 i2 io2 irq2 si2 st2 _ < /proc/stat
    t2=$((u2+n2+s2+i2+io2+irq2+si2+st2))
    id2=$((i2+io2))
    td=$((t2-t1))
    idd=$((id2-id1))
    python3 - <<PY
td=${td}; idd=${idd}
print(round(((td-idd)*100.0)/td,2) if td>0 else 0)
PY
}

linux_mem_bytes(){ 
    mem_total=$(awk '/MemTotal/ {print $2*1024}' /proc/meminfo)
    mem_avail=$(awk '/MemAvailable/ {print $2*1024}' /proc/meminfo)
    mem_used=$((mem_total-mem_avail))
    echo "${mem_used} ${mem_total}"
}

linux_disk_bytes(){ 
    df -B1 -x tmpfs -x devtmpfs -P | awk 'NR>1 {u+=$3; t+=$2} END{print u, t}'
}

linux_top_procs_json(){ 
    ps -eo pid,comm,pcpu,pmem --no-headers --sort=-pcpu | head -n 5 | \
    awk 'BEGIN{printf "["} {printf "%s{\"pid\":%d,\"name\":\"%s\",\"cpu\":%s,\"mem\":%s}", NR>1?",":"", $1,$2,$3,$4} END{print "]"}'
}

# Windows resource collection (via PowerShell in Cygwin)
win_has_powershell(){ command -v powershell.exe >/dev/null 2>&1; }

win_cpu_pct(){ 
    powershell.exe -NoProfile -Command "(Get-Counter '\Processor(_Total)\% Processor Time').CounterSamples.CookedValue | %{[math]::Round(\$_,2)}"
}

win_mem_bytes(){ 
    powershell.exe -NoProfile -Command "(Get-CimInstance Win32_OperatingSystem) | %{\$t=[int64]\$_.TotalVisibleMemorySize*1024;\$f=[int64]\$_.FreePhysicalMemory*1024; \$u=\$t-\$f; Write-Output \"\$u \$t\" }"
}

win_top_procs_json(){ 
    powershell.exe -NoProfile -Command "Get-Process | Sort-Object CPU -Descending | Select-Object -First 5 Id,ProcessName,CPU,PM | ConvertTo-Json"
}

# Ensure database and tables exist
ensure_schema(){
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

# Insert metrics into database
insert_metrics(){
    local host="$1" port="$2" pass="$3"
    local hn ip cpu mem_used mem_total disk_used disk_total top_json esc_json
    
    hn="$(get_hostname)"
    ip="$(get_ip_guess)"
    
    # Collect metrics based on OS
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
    
    # Escape JSON for MySQL
    esc_json="$(printf "%s" "${top_json}" | sed "s/'/''/g")"
    
    # Insert into database
    mysql -h "$host" -P "$port" -u "${MYSQL_USER}" "-p${pass}" --protocol=TCP <<SQL
USE ${MYSQL_DB};
INSERT INTO nodes (hostname, ip, first_seen, last_seen) 
    VALUES ('${hn}', '${ip}', NOW(), NOW()) 
    ON DUPLICATE KEY UPDATE ip=VALUES(ip), last_seen=NOW();
INSERT INTO metrics (hostname, cpu_used_pct, mem_used_bytes, mem_total_bytes, disk_used_bytes, disk_total_bytes, top_procs) 
    VALUES ('${hn}', ${cpu}+0, ${mem_used}+0, ${mem_total}+0, ${disk_used}+0, ${disk_total}+0, '${esc_json}');
SQL
    say "Reported to ${host}:${port} as ${hn} (${ip}) CPU=${cpu}%"
}

# Main execution
require_cmd mysql

# Get password
if [ ! -s "${PASSFILE}" ]; then
    die "Password file ${PASSFILE} missing or empty"
fi
MYSQL_PASS="$(tr -d '\r\n' < "${PASSFILE}")"

say "Reporting server status to ${MYSQL_HOST}:${MYSQL_PORT}"
ensure_schema "${MYSQL_HOST}" "${MYSQL_PORT}" "${MYSQL_PASS}"
insert_metrics "${MYSQL_HOST}" "${MYSQL_PORT}" "${MYSQL_PASS}"
say "Done."
