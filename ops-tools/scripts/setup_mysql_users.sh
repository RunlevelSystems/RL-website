# Developed by World Domination Software LLC
#!/usr/bin/env bash
set -euo pipefail
# CONFIG
TOOLS_DIR="/home/gameserver/tools"; PASSFILE="${TOOLS_DIR}/.password"; SERVERS_FILE="${TOOLS_DIR}/servers.txt"; MYSQL_DB_NAME="peer_status"
# END CONFIG
die(){ echo "ERROR: $*" >&2; exit 1; }; say(){ printf '[%s] %s\n' "$(date '+%F %T')" "$*"; }
[ -s "${PASSFILE}" ] || die "Password file ${PASSFILE} missing"; pass="$(tr -d '\r\n' < "${PASSFILE}")"
resolve_ipv4(){ local host="$1"; getent ahostsv4 "$host" | awk '{print $1; exit}'; }
say "Ensuring DB and users on local MySQL"
mysql --defaults-file=/root/.my.cnf <<SQL
CREATE DATABASE IF NOT EXISTS ${MYSQL_DB_NAME} DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
CREATE USER IF NOT EXISTS 'localuser'@'localhost' IDENTIFIED BY '${pass}';
ALTER USER 'localuser'@'localhost' IDENTIFIED BY '${pass}';
GRANT ALL PRIVILEGES ON *.* TO 'localuser'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;
SQL
[ -s "${SERVERS_FILE}" ] || { say "No ${SERVERS_FILE} found; skipping remoteuser grants"; exit 0; }
while IFS= read -r line; do [ -z "$line" ] && continue; host="${line%%:*}"; ip="$(resolve_ipv4 "$host")"; [ -z "$ip" ] && { echo "WARN: could not resolve $host"; continue; }
  say "Granting remoteuser@${ip}"
  mysql --defaults-file=/root/.my.cnf <<SQL
CREATE USER IF NOT EXISTS 'remoteuser'@'${ip}' IDENTIFIED BY '${pass}';
ALTER USER 'remoteuser'@'${ip}' IDENTIFIED BY '${pass}';
GRANT INSERT ON ${MYSQL_DB_NAME}.metrics TO 'remoteuser'@'${ip}';
GRANT INSERT, UPDATE ON ${MYSQL_DB_NAME}.nodes TO 'remoteuser'@'${ip}';
GRANT SELECT ON ${MYSQL_DB_NAME}.* TO 'remoteuser'@'${ip}';
FLUSH PRIVILEGES;
SQL
done < "${SERVERS_FILE}"
say "Done."
