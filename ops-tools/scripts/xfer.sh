#!/usr/bin/env bash


## nohup sshpass -p 'Inc0rrect!' rsync -avh --partial --progress -e 'ssh -p 12322' /sdb1/ gameserver@kc.iaregamer.com:/sdb1/ >> ~/rsync-sdb1-to-kc.log 2>&1 &
## tail -f ~/rsync-sdb1-to-kc.log
#
# Generic rsync transfer helper
# - Interactive mode: ask for source, destination, password, port
# - Runs rsync in background with nohup and logs to ~/xfer.log
# - --check: tail -f the log
#

set -euo pipefail

LOGFILE="${HOME}/xfer.log"

# ----- --check mode: follow the log -----
if [[ "${1:-}" == "--check" ]]; then
  if [[ -f "$LOGFILE" ]]; then
    echo "Tailing log: $LOGFILE"
    echo "Press Ctrl+C to stop."
    tail -f "$LOGFILE"
  else
    echo "No log file found at: $LOGFILE"
  fi
  exit 0
fi

# ----- Ensure sshpass is installed -----
if ! command -v sshpass >/dev/null 2>&1; then
  echo "ERROR: sshpass is not installed."
  echo "Install it, e.g.:"
  echo "  sudo apt-get install -y sshpass"
  exit 1
fi

# ----- Ask for source folder -----
read -rp "Local source folder to send (e.g. /sdb1/): " SRC

if [[ -z "$SRC" || ! -d "$SRC" ]]; then
  echo "ERROR: Source folder '$SRC' does not exist or is empty." >&2
  exit 1
fi

# ----- Ask for remote destination -----

