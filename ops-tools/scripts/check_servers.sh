# Developed by World Domination Software LLC
#!/usr/bin/env bash
# =============================================================================
# DEPRECATED: Use change_passwd.sh for password rotation, status.sh for reporting
# This script is kept for backward compatibility only.
# =============================================================================
set -euo pipefail
# CONFIG
TOOLS_DIR="/home/gameserver/tools"; PASSFILE="${TOOLS_DIR}/.password"; SERVERS_FILE="${TOOLS_DIR}/servers.txt"
DEFAULT_SSH_PORT=12322; MYSQL_WRITER_USER="remoteuser"; MYSQL_DB_NAME="peer_status"; LINUX_USER="gameserver"
# END CONFIG
die(){ echo "ERROR: $*" >&2; exit 1; }; say(){ printf '[%s] %s\n' "$(date '+%F %T')" "$*"; }; require_cmd(){ command -v "$1" >/dev/null 2>&1 || die "Missing: $1"; }
linux_cpu_pct(){ read -r _ u n s i io irq si st _ < /proc/stat; t1=$((u+n+s+i+io+irq+si+st)); id1=$((i+io)); sleep 1; read -r _ u2 n2 s2 i2 io2 irq2 si2 st2 _ < /proc/stat; t2=$((u2+n2+s2+i2+io2+irq2+si2+st2)); id2=$((i2+io2)); td=$((t2-t1)); idd=$((id2-id1)); python3 - <<PY
td=${td}; idd=${idd}
print(round(((td-idd)*100.0)/td,2) if td>0 else 0)
PY
}
linux_mem_bytes(){ mem_total=$(awk '/MemTotal/ {print $2*1024}' /proc/meminfo); mem_avail=$(awk '/MemAvailable/ {print $2*1024}' /proc/meminfo); mem_used=$((mem_total-mem_avail)); echo "${mem_used} ${mem_total}"; }
linux_disk_bytes(){ df -B1 -x tmpfs -x devtmpfs -P | awk 'NR>1 {u+=$3; t+=$2} END{print u, t}'; }
linux_top_procs_json(){ ps -eo pid,comm,pcpu,pmem --no-headers --sort=-pcpu | head -n 5 | awk 'BEGIN{printf "["} {printf "%s{\"pid\":%d,\"name\":\"%s\",\"cpu\":%s,\"mem\":%s}", NR>1?",":"", $1,$2,$3,$4} END{print "]"}'; }
win_has_powershell(){ command -v powershell.exe >/dev/null 2>&1; }; win_cpu_pct(){ powershell.exe -NoProfile -Command "(Get-Counter '\Processor(_Total)\% Processor Time').CounterSamples.CookedValue | %%{{[math]::Round($_,2)}}" ; }
win_mem_bytes(){ powershell.exe -NoProfile -Command "(Get-CimInstance Win32_OperatingSystem) | %%{{$t=[int64]$_.TotalVisibleMemorySize*1024;$f=[int64]$_.FreePhysicalMemory*1024; $u=$t-$f; Write-Output \"$u $t\" }}" ; }
win_top_procs_json(){ powershell.exe -NoProfile -Command "Get-Process | Sort-Object CPU -Descending | Select-Object -First 5 Id,ProcessName,CPU,PM | ConvertTo-Json" ; }
get_hostname(){ hostname -s 2>/dev/null || hostname; }
get_ip_guess(){ ip -4 -o addr show 2>/dev/null | awk '!/ lo /{sub(/\/.*/,"",$4); print $4; exit}' || hostname -I 2>/dev/null | awk '{print $1}' || echo "0.0.0.0"; }
ensure_schema(){ local host="$1" port="$2"; [ -s "${PASSFILE}" ] || die "Missing ${PASSFILE}"; local pass; pass="$(tr -d '\r\n' < "${PASSFILE}")"; mysql -h "${host}" -P "${port}" -u "${MYSQL_WRITER_USER}" "-p${pass}" --protocol=TCP <<SQL || true
CREATE DATABASE IF NOT EXISTS ${MYSQL_DB_NAME} DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE ${MYSQL_DB_NAME};
CREATE TABLE IF NOT EXISTS nodes ( hostname VARCHAR(128) PRIMARY KEY, ip VARCHAR(45), first_seen DATETIME DEFAULT CURRENT_TIMESTAMP, last_seen DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS metrics ( id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, hostname VARCHAR(128) NOT NULL, ts DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, cpu_used_pct DECIMAL(5,2) NOT NULL, mem_used_bytes BIGINT UNSIGNED NOT NULL, mem_total_bytes BIGINT UNSIGNED NOT NULL, disk_used_bytes BIGINT UNSIGNED NOT NULL, disk_total_bytes BIGINT UNSIGNED NOT NULL, top_procs JSON NULL, INDEX (hostname, ts), CONSTRAINT fk_host FOREIGN KEY (hostname) REFERENCES nodes(hostname) ON DELETE CASCADE ) ENGINE=InnoDB;
SQL
}
upsert_and_insert(){ local host="$1" port="$2"; [ -s "${PASSFILE}" ] || die "Missing ${PASSFILE}"; local pass; pass="$(tr -d '\r\n' < "${PASSFILE}")"; hn="$(get_hostname)"; ip="$(get_ip_guess)"
  if [ -f /proc/stat ]; then cpu="$(linux_cpu_pct)"; read -r mem_used mem_total <<<"$(linux_mem_bytes)"; read -r disk_used disk_total <<<"$(linux_disk_bytes)"; top_json="$(linux_top_procs_json)"
  elif win_has_powershell; then cpu="$(win_cpu_pct | tr -d '\r\n')"; read -r mem_used mem_total <<<"$(win_mem_bytes | tr -d '\r')"; disk_used="0"; disk_total="0"; top_json="$(win_top_procs_json 2>/dev/null || echo '[]')"
  else cpu="0"; mem_used="0"; mem_total="0"; disk_used="0"; disk_total="0"; top_json="[]"; fi
  esc_json="$(printf "%s" "${top_json}" | sed "s/'/''/g")"
  mysql -h "$host" -P "$port" -u "${MYSQL_WRITER_USER}" "-p${pass}" --protocol=TCP <<SQL
USE ${MYSQL_DB_NAME};
INSERT INTO nodes (hostname, ip, first_seen, last_seen) VALUES ('${hn}', '${ip}', NOW(), NOW()) ON DUPLICATE KEY UPDATE ip=VALUES(ip), last_seen=NOW();
INSERT INTO metrics (hostname, cpu_used_pct, mem_used_bytes, mem_total_bytes, disk_used_bytes, disk_total_bytes, top_procs) VALUES ('${hn}', ${cpu}+0, ${mem_used}+0, ${mem_total}+0, ${disk_used}+0, ${disk_total}+0, '${esc_json}');
SQL
  say "Reported to ${host}:${port} as ${hn} (${ip}) CPU=${cpu}%"
}
resolve_ipv4(){ local host="$1"; getent ahostsv4 "$host" | awk '{print $1; exit}'; }
push_password_file(){ local host="$1" port="$2" newpass="$3"; tmpfile="$(mktemp)"; printf '%s\n' "$newpass" > "$tmpfile"
  if command -v sshpass >/dev/null 2>&1 && [ -s "${PASSFILE}" ]; then
    sshpass -f "${PASSFILE}" scp -P "$port" -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null "$tmpfile" "gameserver@${host}:/home/gameserver/tools/.password"
    sshpass -f "${PASSFILE}" ssh -p "$port" -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null "gameserver@${host}" "chmod 600 /home/gameserver/tools/.password"
  else
    scp -P "$port" -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null "$tmpfile" "gameserver@${host}:/home/gameserver/tools/.password"
    ssh -p "$port" -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null "gameserver@${host}" "chmod 600 /home/gameserver/tools/.password"
  fi
  rm -f "$tmpfile"
}
rotate_mysql_local(){ local newpass="$1"
  mysql -u root --defaults-file=/root/.my.cnf -e "CREATE USER IF NOT EXISTS 'localuser'@'localhost' IDENTIFIED BY '${newpass}'; ALTER USER 'localuser'@'localhost' IDENTIFIED BY '${newpass}'; GRANT ALL PRIVILEGES ON *.* TO 'localuser'@'localhost' WITH GRANT OPTION; FLUSH PRIVILEGES;" || true
  while IFS= read -r line; do [ -z "$line" ] && continue; host="${line%%:*}"; ip="$(resolve_ipv4 "$host")"; [ -z "$ip" ] && continue
    mysql -u root --defaults-file=/root/.my.cnf <<SQL || true
CREATE USER IF NOT EXISTS 'remoteuser'@'${ip}' IDENTIFIED BY '${newpass}';
ALTER USER 'remoteuser'@'${ip}' IDENTIFIED BY '${newpass}';
CREATE DATABASE IF NOT EXISTS ${MYSQL_DB_NAME} DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
GRANT INSERT ON ${MYSQL_DB_NAME}.metrics TO 'remoteuser'@'${ip}';
GRANT INSERT, UPDATE ON ${MYSQL_DB_NAME}.nodes TO 'remoteuser'@'${ip}';
GRANT SELECT ON ${MYSQL_DB_NAME}.* TO 'remoteuser'@'${ip}';
FLUSH PRIVILEGES;
SQL
  done < "${SERVERS_FILE}"
}
verify_core_identity(){ target="core.iaregamer.com"; dns_ip="$(getent ahostsv4 "$target" | awk '{print $1; exit}')"; local_ips="$(hostname -I 2>/dev/null || echo '')"
  if printf "%s\n" ${local_ips} | grep -qE "\b${dns_ip}\b"; then return 0; fi
  if command -v curl >/dev/null 2>&1; then pub="$(curl -s https://ipinfo.io/ip || true)"; [ -n "$pub" ] && [ "$pub" = "$dns_ip" ] && return 0; fi
  return 1
}
rotate_password_all(){ local newpass="$1"; [ -s "${SERVERS_FILE}" ] || die "Missing ${SERVERS_FILE}"; verify_core_identity || die "This is not the DNS-resolved core host."
  say "Updating local .password"; printf '%s\n' "$newpass" | sudo tee "${PASSFILE}" >/dev/null; chmod 600 "${PASSFILE}"
  say "Rotating MySQL accounts on this host"; rotate_mysql_local "$newpass"
  say "Rotating Linux '${LINUX_USER}' and propagating .password to peers"
  while IFS= read -r line; do [ -z "$line" ] && continue; host="${line%%:*}"; port="${line##*:}"; [ "$host" = "$port" ] && port="$DEFAULT_SSH_PORT"
    if command -v sshpass >/dev/null 2>&1 && [ -s "${PASSFILE}" ]; then
      sshpass -f "${PASSFILE}" ssh -p "${port}" -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null "gameserver@${host}" "echo '${LINUX_USER}:${newpass}' | sudo chpasswd"
    else
      ssh -p "${port}" -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null "gameserver@${host}" "echo '${LINUX_USER}:${newpass}' | sudo chpasswd"
    fi
    push_password_file "$host" "$port" "$newpass"
  done < "${SERVERS_FILE}"
  say "Done. Run DR push to replicate users/grants to core-dr."
}
if [ $# -lt 1 ]; then cat >&2 <<USAGE
Usage:
  $0 --report <MYSQL_HOST> [PORT]
  $0 --password <NewPassword!>
USAGE
  exit 1
fi
case "$1" in
  --report) require_cmd mysql; host="${2:-core.iaregamer.com}"; port="${3:-3306}"; ensure_schema "$host" "$port"; upsert_and_insert "$host" "$port" ;;
  --password) [ $# -ge 2 ] || die "Provide new password"; rotate_password_all "$2" ;;
  *) die "Unknown option: $1" ;;
esac
