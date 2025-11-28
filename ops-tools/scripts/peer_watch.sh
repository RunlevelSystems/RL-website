#!/usr/bin/env bash
set -euo pipefail
# =============================================================================
# peer_watch.sh - DR Peer Monitoring Script
# =============================================================================
# Monitors peer host connectivity and sends Discord alerts on failures.
# Uses a state file to track consecutive failures (alerts on 2nd failure).
#
# Usage:
#   ./peer_watch.sh <PEER_HOST> [SSH_PORT]
#
# Example:
#   ./peer_watch.sh core-dr.iaregamer.com 12322
#
# Configuration:
#   Create /home/gameserver/tools/.discord_webhook with webhook URL
# =============================================================================

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
