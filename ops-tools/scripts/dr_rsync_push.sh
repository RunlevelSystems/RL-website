#!/usr/bin/env bash
set -euo pipefail
<<<<<<< HEAD
# CONFIG
DEFAULT_SSH_PORT=12322
TOOLS_DIR="/home/gameserver/tools"
PASSFILE="${TOOLS_DIR}/.password"
DATA_DIRS=( "/home/gameserver/tools" "/var/www/html/panel" "/var/www/html/adminer" "/var/www/files/backup" "/var/www/files/installers" )
ETC_EXCLUDES=( "--exclude=/etc/hostname" "--exclude=/etc/machine-id" "--exclude=/etc/hosts" "--exclude=/etc/netplan/**" "--exclude=/etc/fstab" "--exclude=/etc/ssh/ssh_host_*" "--exclude=/etc/letsencrypt/accounts/**" "--exclude=/etc/letsencrypt/**/csr/**" "--exclude=/etc/letsencrypt/**/keys/**" "--exclude=/etc/letsencrypt/**/renewal-hooks/**" )
MYSQL_BIN="${MYSQL_BIN:-mysql}"; MYSQLDUMP_BIN="${MYSQLDUMP_BIN:-mysqldump}"; MYSQL_EXCLUDE_REGEX='^(information_schema|performance_schema|mysql|sys)$'; MYSQL_LOCAL_DEFAULTS="/root/.my.cnf"
RSYNC_DELETE="--delete --delete-excluded"; SSH_OPTS="-o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null"
# END CONFIG
if [ $# -lt 1 ]; then echo "Usage: $0 <TARGET_HOST> [SSH_PORT]" >&2; exit 1; fi
TARGET="$1"; SSH_PORT="${2:-$DEFAULT_SSH_PORT}"
RSYNC_BASE_OPTS="-aHAX --numeric-ids $RSYNC_DELETE --info=stats2,progress2 --rsync-path='sudo rsync'"
RSYNC_SSH="ssh -p $SSH_PORT $SSH_OPTS"
if command -v sshpass >/dev/null 2>&1 && [ -s "${PASSFILE}" ]; then SSHPRE=(sshpass -f "${PASSFILE}"); else SSHPRE=(); fi
say(){ printf '\n[%s] %s\n' "$(date '+%F %T')" "$*"; }
push_dir(){ local src="$1" dest="$2"; say "Rsync: $src -> $TARGET:$dest"; rsync $RSYNC_BASE_OPTS -e "$RSYNC_SSH" "$src/" "$TARGET:$dest/"; }
push_etc(){ say "Rsync: selected /etc"; rsync $RSYNC_BASE_OPTS -e "$RSYNC_SSH" "${ETC_EXCLUDES[@]}" /etc/ "$TARGET:/etc/"; }
ensure_remote_mysql(){ ${SSHPRE[@]} $RSYNC_SSH "$TARGET" "command -v mysql >/dev/null 2>&1" || { echo "ERROR: mysql not found on $TARGET"; exit 1; }; }
stream_databases(){ say "MySQL: streaming per-DB (routines/triggers/events)"; AUTH=(); [ -f "$MYSQL_LOCAL_DEFAULTS" ] && AUTH=(--defaults-file="$MYSQL_LOCAL_DEFAULTS"); mapfile -t DBS < <($MYSQL_BIN "${AUTH[@]}" --batch --skip-column-names -e "SHOW DATABASES;" | grep -Ev "$MYSQL_EXCLUDE_REGEX"); for db in "${DBS[@]}"; do say "  dump $db -> remote"; $MYSQLDUMP_BIN "${AUTH[@]}" --single-transaction --routines --triggers --events --hex-blob "$db" | gzip -c | ${SSHPRE[@]} $RSYNC_SSH "$TARGET" "gunzip -c | sudo $MYSQL_BIN"; done; }
replicate_users_and_grants(){ say "MySQL: replicating users & grants"; AUTH=(); [ -f "$MYSQL_LOCAL_DEFAULTS" ] && AUTH=(--defaults-file="$MYSQL_LOCAL_DEFAULTS"); tmpdir="$(mktemp -d)"; trap 'rm -rf "$tmpdir"' EXIT; $MYSQL_BIN "${AUTH[@]}" --batch --skip-column-names -e "SELECT CONCAT("'", user, "'@'", host, "'") FROM mysql.user WHERE user NOT IN ('mysql.sys','mysql.session','mysql.infoschema');" > "$tmpdir/accts.txt"; : > "$tmpdir/users_grants.sql"; while IFS= read -r acct; do [ -z "$acct" ] && continue; $MYSQL_BIN "${AUTH[@]}" --batch --skip-column-names -e "SHOW CREATE USER $acct\G" | awk -F': ' '/CREATE USER/ {print $2";"}' | sed 's/^CREATE USER /CREATE USER IF NOT EXISTS /' >> "$tmpdir/users_grants.sql"; $MYSQL_BIN "${AUTH[@]}" --batch --skip-column-names -e "SHOW GRANTS FOR $acct;" | sed 's/$/;/' >> "$tmpdir/users_grants.sql"; done < "$tmpdir/accts.txt"; ${SSHPRE[@]} $RSYNC_SSH "$TARGET" "cat > /tmp/users_grants.sql"; ${SSHPRE[@]} $RSYNC_SSH "$TARGET" "sudo $MYSQL_BIN < /tmp/users_grants.sql && rm -f /tmp/users_grants.sql"; }
say "Starting DR push to $TARGET (ssh port $SSH_PORT)"; ensure_remote_mysql; for d in "${DATA_DIRS[@]}"; do [ -d "$d" ] && push_dir "$d" "$d" || echo "WARN: missing $d"; done; push_etc; stream_databases; replicate_users_and_grants; say "DR push complete."
=======

if [ $# -lt 1 ]; then
  echo "Usage: $0 <TARGET_HOST> [SSH_PORT]" >&2; exit 1
fi
TARGET="$1"
SSH_PORT="${2:-12322}"

# Where your shared secrets live
TOOLS_DIR="/home/gameserver/tools"
PASSFILE="${TOOLS_DIR}/.password"     # optional; used if ssh keys not set
SSH_OPTS="-p ${SSH_PORT} -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null"
RSYNC_BASE_OPTS="-aHAX --numeric-ids --delete --delete-excluded --info=stats2,progress2 --rsync-path='sudo rsync'"

# Prefer keys; fall back to sshpass if .password exists
if command -v sshpass >/dev/null 2>&1 && [ -s "${PASSFILE}" ]; then
  SSHPRE=(sshpass -f "${PASSFILE}")
  RSYNC_SSH="ssh ${SSH_OPTS}"
else
  SSHPRE=()
  RSYNC_SSH="ssh ${SSH_OPTS}"
fi

say(){ printf '\n[%s] %s\n' "$(date '+%F %T')" "$*"; }

# ---------- WHAT TO REPLICATE ----------
# App/content
DATA_DIRS=(
  "/home/gameserver/tools"
  "/var/www/html/panel"
  "/var/www/html/adminer"
  "/var/www/files/backup"
  "/var/www/files/installers"
)
# System configs (safe subset)
ETC_DIRS=(
  "/etc/apache2"
  "/etc/php"
  "/etc/mysql"
  "/etc/systemd/system"
  "/etc/cron.d"
  "/etc/cron.daily"
  "/etc/cron.hourly"
  "/etc/letsencrypt"
  "/etc/ssh"          # EXCLUDES host private keys below
)
# Rsync excludes to avoid breaking identity or copying secrets that must differ
ETC_EXCLUDES=(
  "--exclude=/etc/hostname"
  "--exclude=/etc/machine-id"
  "--exclude=/etc/hosts"
  "--exclude=/etc/netplan/**"
  "--exclude=/etc/fstab"
  "--exclude=/etc/ssh/ssh_host_*"
  "--exclude=/etc/letsencrypt/accounts/**"
  "--exclude=/etc/letsencrypt/**/csr/**"
  "--exclude=/etc/letsencrypt/**/keys/**"
  "--exclude=/etc/letsencrypt/**/renewal-hooks/**"
)

# ---------- MySQL settings ----------
MYSQL_BIN="${MYSQL_BIN:-mysql}"
MYSQLDUMP_BIN="${MYSQLDUMP_BIN:-mysqldump}"
MYSQL_EXCLUDE_REGEX='^(information_schema|performance_schema|mysql|sys)$'

# Try to use /root/.my.cnf; otherwise fall back to password in ${PASSFILE} if it looks like a MySQL password
MYSQL_LOCAL_AUTH=()
[ -f /root/.my.cnf ] && MYSQL_LOCAL_AUTH=(--defaults-file=/root/.my.cnf)

MYSQL_REMOTE_AUTH=()
REMOTE_CHECK_CMD="command -v mysql >/dev/null 2>&1"
"${SSHPRE[@]}" bash -lc "${RSYNC_SSH} ${TARGET} '${REMOTE_CHECK_CMD}'" 2>/dev/null || {
  echo "ERROR: mysql not found on remote ${TARGET}. Install client/server first." >&2; exit 1;
}

# ---------- Functions ----------
push_dir(){
  local src="$1"
  local dest="$2"
  say "Rsync: ${src} -> ${TARGET}:${dest}"
  # shellcheck disable=SC2029
  rsync ${RSYNC_BASE_OPTS} -e "${RSYNC_SSH}" "${src}/" "${TARGET}:${dest}/"
}

push_etc(){
  say "Rsync: selected /etc trees"
  rsync ${RSYNC_BASE_OPTS} -e "${RSYNC_SSH}" "${ETC_EXCLUDES[@]}" /etc/ "${TARGET}:/etc/"
}

sync_mysql_stream(){
  say "MySQL: streaming logical dumps -> ${TARGET}"
  # Build DB list
  mapfile -t DBS < <( ${MYSQL_BIN} "${MYSQL_LOCAL_AUTH[@]}" --batch --skip-column-names -e "SHOW DATABASES;" | grep -Ev "${MYSQL_EXCLUDE_REGEX}" )
  for db in "${DBS[@]}"; do
    say "  dump ${db} -> remote import"
    # shellcheck disable=SC2029
    ${MYSQLDUMP_BIN} "${MYSQL_LOCAL_AUTH[@]}" --single-transaction --routines --triggers --events --hex-blob "$db" \
      | gzip -c \
      | ${SSHPRE[@]} ${RSYNC_SSH} "${TARGET}" "gunzip -c | sudo ${MYSQL_BIN} ${MYSQL_REMOTE_AUTH[@]} -e 'CREATE DATABASE IF NOT EXISTS \\\`${db}\\\\\\` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci; SET sql_log_bin=0;' && gunzip -c 2>/dev/null || cat" \
      >/dev/null
      # second gunzip is a no-op cat fallback for POSIX shells w/o process substitution; harmless
  done
}

# ---------- Main ----------
say "Starting DR push to ${TARGET} (ssh port ${SSH_PORT})"
for d in "${DATA_DIRS[@]}"; do
  [ -d "$d" ] && push_dir "$d" "$d" || echo "WARN: missing ${d}, skipping"
done
push_etc
sync_mysql_stream
say "DR push complete."

>>>>>>> b63f4ed (updated content)
