#!/usr/bin/env bash
set -euo pipefail
# CONFIG
BACKUP_ROOT="/sdb1/backups"; MYSQL_LOCAL_DEFAULTS="/root/.my.cnf"; MYSQL_EXCLUDE_REGEX='^(information_schema|performance_schema|mysql|sys)$'
WEB_DIR1="/var/www/html"; WEB_DIR2="/var/www/files"; TOOLS_DIR="/home/gameserver/tools"
EXCLUDE_ETC=( 'ssh_host_*' 'hostname' 'machine-id' 'netplan/**' 'fstab' )
# END CONFIG
HOST="$(hostname -s)"; NOW="$(date +%F_%H%M)"; BASE="${BACKUP_ROOT}/${HOST}/${NOW}"; mkdir -p "${BASE}"/{mysql,etc,web,tools}
say(){ printf '[%s] %s\n' "$(date '+%F %T')" "$*"; }
if command -v mysql >/dev/null 2>&1; then
  say "Dumping MySQL per-DB -> ${BASE}/mysql"
  AUTH=(); [ -f "${MYSQL_LOCAL_DEFAULTS}" ] && AUTH=(--defaults-file="${MYSQL_LOCAL_DEFAULTS}")
  mapfile -t DBS < <(mysql "${AUTH[@]}" --batch --skip-column-names -e "SHOW DATABASES" | grep -Ev "${MYSQL_EXCLUDE_REGEX}")
  for db in "${DBS[@]}"; do mysqldump "${AUTH[@]}" --single-transaction --routines --triggers --events --hex-blob "$db" | gzip -c > "${BASE}/mysql/${db}.sql.gz"; done
fi
[ -d "${WEB_DIR1}" ] && rsync -aH --delete "${WEB_DIR1}/" "${BASE}/web/html/"
[ -d "${WEB_DIR2}" ] && rsync -aH --delete "${WEB_DIR2}/" "${BASE}/web/files/"
[ -d "${TOOLS_DIR}" ] && rsync -aH --delete "${TOOLS_DIR}/" "${BASE}/tools/"
RSYNC_EXC=(); for e in "${EXCLUDE_ETC[@]}"; do RSYNC_EXC+=(--exclude="${e}"); done; rsync -aH --delete "${RSYNC_EXC[@]}" /etc/ "${BASE}/etc/"
tar -C "$(dirname "${BASE}")" -czf "${BASE}.tgz" "$(basename "${BASE}")" && rm -rf "${BASE}"
say "Backup done: ${BASE}.tgz"
