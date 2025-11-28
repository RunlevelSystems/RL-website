#!/usr/bin/env bash
set -euo pipefail
HOST="$(hostname -s)"
NOW="$(date +%F_%H%M)"
BASE="/sdb1/backups/${HOST}/${NOW}"
mkdir -p "${BASE}"/{mysql,etc,web,tools}

say(){ printf '[%s] %s\n' "$(date '+%F %T')" "$*"; }

# MySQL (logical)
if command -v mysql >/dev/null 2>&1; then
  say "Dumping MySQL per-DB -> ${BASE}/mysql"
  MYSQL_EXCLUDE_REGEX='^(information_schema|performance_schema|mysql|sys)$'
  AUTH=(); [ -f /root/.my.cnf ] && AUTH=(--defaults-file=/root/.my.cnf)
  mapfile -t DBS < <(mysql "${AUTH[@]}" --batch --skip-column-names -e "SHOW DATABASES" | grep -Ev "${MYSQL_EXCLUDE_REGEX}")
  for db in "${DBS[@]}"; do
    mysqldump "${AUTH[@]}" --single-transaction --routines --triggers --events --hex-blob "$db" \
      | gzip -c > "${BASE}/mysql/${db}.sql.gz"
  done
fi

# Web + tools
[ -d /var/www/html ] && rsync -aH --delete /var/www/html/ "${BASE}/web/html/"
[ -d /var/www/files ] && rsync -aH --delete /var/www/files/ "${BASE}/web/files/"
[ -d /home/gameserver/tools ] && rsync -aH --delete /home/gameserver/tools/ "${BASE}/tools/"

# System config snapshot (light)
rsync -aH --delete \
  --exclude='ssh_host_*' --exclude='hostname' --exclude='machine-id' --exclude='netplan/**' --exclude='fstab' \
  /etc/ "${BASE}/etc/"

# Optional: compact the folder
tar -C "$(dirname "${BASE}")" -czf "${BASE}.tgz" "$(basename "${BASE}")" && rm -rf "${BASE}"

say "Backup done: ${BASE}.tgz"

