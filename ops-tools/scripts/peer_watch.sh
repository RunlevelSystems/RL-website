#!/usr/bin/env bash
set -euo pipefail
<<<<<<< HEAD
# CONFIG
DEFAULT_SSH_PORT=12322
DISCORD_WEBHOOK="https://discord.com/api/webhooks/1126630860217659413/DGjPp_qDtCl82slcvCUzqpV4GCfFwnNSaPFHid4yBzRrWRasW6107NfwibolM81Rn3dB"
STATE_DIR="/var/lib/dr-monitor"
SSH_OPTS="-o BatchMode=yes -o ConnectTimeout=6 -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null"
# END CONFIG
if [ $# -lt 1 ]; then echo "Usage: $0 <PEER_HOST> [SSH_PORT]" >&2; exit 1; fi
PEER="$1"; SSH_PORT="${2:-$DEFAULT_SSH_PORT}"; STATE_FILE="${STATE_DIR}/last_${PEER}.state"
mkdir -p "${STATE_DIR}"
check_once(){ ssh -p "${SSH_PORT}" ${SSH_OPTS} "${PEER}" "true" >/dev/null 2>&1; }
notify_discord(){ local msg="$1"; [ -n "$DISCORD_WEBHOOK" ] || return 0; curl -sS -X POST -H "Content-Type: application/json" -d "{\"content\":\"$msg\"}" "$DISCORD_WEBHOOK" >/dev/null || true; }
LAST="$( [ -f "${STATE_FILE}" ] && cat "${STATE_FILE}" || echo "up" )"
if check_once; then echo "up" > "${STATE_FILE}"; if [ "$LAST" = "down" ]; then notify_discord "✅ Peer *${PEER}* is reachable again."; fi; exit 0
else if [ "$LAST" = "down" ]; then echo "down" > "${STATE_FILE}"; notify_discord "🚨 DR Alert: *${PEER}* is unreachable from *$(hostname -s)* twice in a row. Consider switching **core.iaregamer.com** to the healthy node."
     else echo "down" > "${STATE_FILE}"; fi
fi
=======

if [ $# -lt 1 ]; then
  echo "Usage: $0 <PEER_HOST> [SSH_PORT]" >&2; exit 1
fi
PEER="$1"
SSH_PORT="${2:-12322}"
STATE_DIR="/var/lib/dr-monitor"
STATE_FILE="${STATE_DIR}/last_${PEER}.state"
TOOLS_DIR="/home/gameserver/tools"
WEBHOOK_FILE="${TOOLS_DIR}/.discord_webhook"

mkdir -p "${STATE_DIR}"
[ -s "${WEBHOOK_FILE}" ] || { echo "WARN: ${WEBHOOK_FILE} missing; cannot notify Discord." >&2; }

SSH_OPTS="-p ${SSH_PORT} -o BatchMode=yes -o ConnectTimeout=6 -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null"

check_once(){
  ssh ${SSH_OPTS} "${PEER}" "true" >/dev/null 2>&1
}

notify_discord(){
  local msg="$1"
  [ -s "${WEBHOOK_FILE}" ] || return 0
  curl -sS -X POST -H "Content-Type: application/json" \
    -d "{\"content\":\"${msg}\"}" "$(cat "${WEBHOOK_FILE}")" >/dev/null || true
}

# Two consecutive failures logic using state file
LAST="$( [ -f "${STATE_FILE}" ] && cat "${STATE_FILE}" || echo "up" )"

if check_once; then
  echo "up" > "${STATE_FILE}"
  if [ "${LAST}" = "down" ]; then
    notify_discord "✅ Peer *${PEER}* is reachable again."
  fi
  exit 0
else
  if [ "${LAST}" = "down" ]; then
    echo "down" > "${STATE_FILE}"
    notify_discord "🚨 DR Alert: *${PEER}* is unreachable from *$(hostname -s)* twice in a row. Consider switching **core.iaregamer.com** to the healthy node."
  else
    echo "down" > "${STATE_FILE}"
    # first failure: no alert yet
  fi
fi

>>>>>>> b63f4ed (updated content)
