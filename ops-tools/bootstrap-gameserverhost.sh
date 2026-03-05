# Developed by World Domination Software LLC
#!/usr/bin/env bash
# =============================================================================
# bootstrap-gameserverhost.sh - Game Server Host Bootstrap Script
# =============================================================================
# Bootstraps a Linux or Cygwin host for game server hosting. Installs required
# dependencies, configures SSH on port 12322, sets up the gameserver user,
# and prepares the tools directory structure.
#
# Works on:
#   - Ubuntu/Debian Linux
#   - RHEL/CentOS/Rocky Linux
#   - Cygwin (Windows)
#
# Usage:
#   sudo ./bootstrap-gameserverhost.sh [OPTIONS]
#
# Options:
#   --ssh-port PORT      SSH port to configure (default: 12322)
#   --gameserver-pass    Password for gameserver user (prompts if not set)
#   --skip-firewall      Skip firewall configuration
#   --skip-ssh           Skip SSH reconfiguration
#   --dry-run            Show what would be done without making changes
#   --help               Show this help message
#
# Examples:
#   sudo ./bootstrap-gameserverhost.sh
#   sudo ./bootstrap-gameserverhost.sh --ssh-port 22222 --skip-firewall
#   ./bootstrap-gameserverhost.sh --dry-run
#
# Requirements:
#   - Root/sudo access (except for --dry-run)
#   - Internet access for package installation
# =============================================================================

set -euo pipefail

# =============================================================================
# Configuration
# =============================================================================
SSH_PORT="${SSH_PORT:-12322}"
GAMESERVER_USER="gameserver"
TOOLS_DIR="/home/${GAMESERVER_USER}/tools"
SCRIPTS_DIR="${TOOLS_DIR}/scripts"
LOG_DIR="/var/log/wds-bootstrap"
LOG_FILE="${LOG_DIR}/bootstrap-$(date '+%Y%m%d-%H%M%S').log"
SKIP_FIREWALL=false
SKIP_SSH=false
DRY_RUN=false
GAMESERVER_PASS=""

# Game server port ranges
GAME_PORTS_TCP="2000:12000"
GAME_PORTS_UDP="2000:12000"
GSP_PORT="12679"
FTP_PORT="21"
FTP_PASSIVE_PORTS="50000:51000"

# =============================================================================
# Helper Functions
# =============================================================================
die() { echo "ERROR: $*" >&2; exit 1; }
warn() { echo "WARNING: $*" >&2; }
say() { printf '[%s] %s\n' "$(date '+%F %T')" "$*"; }
log() { say "$*" | tee -a "${LOG_FILE}" 2>/dev/null || say "$*"; }

require_root() {
    if [[ $EUID -ne 0 ]] && [[ "$DRY_RUN" != "true" ]]; then
        die "This script must be run as root (use sudo)"
    fi
}

is_cygwin() {
    [[ "$(uname -s)" == CYGWIN* ]] || [[ "$(uname -s)" == MINGW* ]] || [[ "$(uname -s)" == MSYS* ]]
}

is_debian() {
    [[ -f /etc/debian_version ]] || command -v apt-get >/dev/null 2>&1
}

is_rhel() {
    [[ -f /etc/redhat-release ]] || command -v dnf >/dev/null 2>&1 || command -v yum >/dev/null 2>&1
}

get_pkg_manager() {
    if is_cygwin; then
        echo "cygwin"
    elif command -v apt-get >/dev/null 2>&1; then
        echo "apt"
    elif command -v dnf >/dev/null 2>&1; then
        echo "dnf"
    elif command -v yum >/dev/null 2>&1; then
        echo "yum"
    else
        echo "unknown"
    fi
}

# Convert colon-separated port range to iptables format (start:end)
port_range_to_iptables() {
    local range="$1"
    # If range contains colon, use as-is; otherwise it's a single port
    if [[ "$range" == *:* ]]; then
        echo "${range}"
    else
        echo "${range}"
    fi
}

run_cmd() {
    if [[ "$DRY_RUN" == "true" ]]; then
        log "[DRY-RUN] Would run: $*"
        return 0
    fi
    log "Running: $*"
    # Use PIPESTATUS to properly propagate exit code through pipeline
    "$@" 2>&1 | tee -a "${LOG_FILE}"
    local exit_code=${PIPESTATUS[0]}
    return $exit_code
}

# =============================================================================
# Parse Arguments
# =============================================================================
parse_args() {
    while [[ $# -gt 0 ]]; do
        case "$1" in
            --ssh-port)
                SSH_PORT="$2"
                shift 2
                ;;
            --gameserver-pass)
                GAMESERVER_PASS="$2"
                shift 2
                ;;
            --skip-firewall)
                SKIP_FIREWALL=true
                shift
                ;;
            --skip-ssh)
                SKIP_SSH=true
                shift
                ;;
            --dry-run)
                DRY_RUN=true
                shift
                ;;
            --help|-h)
                show_help
                exit 0
                ;;
            *)
                die "Unknown option: $1. Use --help for usage."
                ;;
        esac
    done
}

show_help() {
    cat <<'EOF'
bootstrap-gameserverhost.sh - Game Server Host Bootstrap Script

Bootstraps a Linux or Cygwin host for game server hosting.

USAGE:
    sudo ./bootstrap-gameserverhost.sh [OPTIONS]

OPTIONS:
    --ssh-port PORT        SSH port to configure (default: 12322)
    --gameserver-pass PWD  Password for gameserver user (prompts if not set)
    --skip-firewall        Skip firewall configuration
    --skip-ssh             Skip SSH reconfiguration
    --dry-run              Show what would be done without making changes
    --help, -h             Show this help message

EXAMPLES:
    # Standard installation with defaults
    sudo ./bootstrap-gameserverhost.sh

    # Custom SSH port, skip firewall changes
    sudo ./bootstrap-gameserverhost.sh --ssh-port 22222 --skip-firewall

    # Preview changes without applying them
    ./bootstrap-gameserverhost.sh --dry-run

SUPPORTED PLATFORMS:
    - Ubuntu/Debian Linux (apt)
    - RHEL/CentOS/Rocky Linux (dnf/yum)
    - Cygwin on Windows

WHAT IT DOES:
    1. Installs required packages (curl, wget, git, rsync, mysql-client, etc.)
    2. Creates the gameserver user with sudo privileges
    3. Sets up /home/gameserver/tools directory structure
    4. Configures SSH on custom port (default 12322)
    5. Opens firewall ports for game servers and management
    6. Installs the WDS ops-tools scripts

For more information, see: https://worlddomination.software/staff/operations.php
EOF
}

# =============================================================================
# Package Installation
# =============================================================================
install_packages_apt() {
    log "Installing packages via apt..."
    run_cmd apt-get update
    run_cmd apt-get install -y \
        curl \
        wget \
        git \
        rsync \
        openssh-server \
        mysql-client \
        sshpass \
        sudo \
        vim \
        htop \
        net-tools \
        python3 \
        unzip \
        tar \
        gzip
}

install_packages_dnf() {
    log "Installing packages via dnf..."
    run_cmd dnf install -y \
        curl \
        wget \
        git \
        rsync \
        openssh-server \
        mysql \
        sshpass \
        sudo \
        vim \
        htop \
        net-tools \
        python3 \
        unzip \
        tar \
        gzip
}

install_packages_yum() {
    log "Installing packages via yum..."
    run_cmd yum install -y epel-release || true
    run_cmd yum install -y \
        curl \
        wget \
        git \
        rsync \
        openssh-server \
        mysql \
        sshpass \
        sudo \
        vim \
        htop \
        net-tools \
        python3 \
        unzip \
        tar \
        gzip
}

install_packages_cygwin() {
    log "Cygwin detected - checking for required packages..."
    log "Note: Use Cygwin setup to install: curl, wget, git, rsync, openssh, mysql-client, python3"
    
    # Check for essential commands
    local missing=()
    for cmd in curl wget git rsync ssh mysql python3; do
        if ! command -v "$cmd" >/dev/null 2>&1; then
            missing+=("$cmd")
        fi
    done
    
    if [[ ${#missing[@]} -gt 0 ]]; then
        warn "Missing packages in Cygwin: ${missing[*]}"
        warn "Please install via Cygwin setup.exe"
    else
        log "All required Cygwin packages appear to be installed"
    fi
}

install_packages() {
    local pkg_mgr
    pkg_mgr="$(get_pkg_manager)"
    
    log "Detected package manager: ${pkg_mgr}"
    
    case "$pkg_mgr" in
        apt)
            install_packages_apt
            ;;
        dnf)
            install_packages_dnf
            ;;
        yum)
            install_packages_yum
            ;;
        cygwin)
            install_packages_cygwin
            ;;
        *)
            warn "Unknown package manager. Please install packages manually."
            ;;
    esac
}

# =============================================================================
# User Setup
# =============================================================================
setup_gameserver_user() {
    log "Setting up gameserver user..."
    
    if is_cygwin; then
        log "Cygwin: User management handled by Windows. Ensure 'gameserver' user exists."
        return 0
    fi
    
    # Create user if doesn't exist
    if ! id "${GAMESERVER_USER}" >/dev/null 2>&1; then
        log "Creating user: ${GAMESERVER_USER}"
        run_cmd useradd -m -s /bin/bash "${GAMESERVER_USER}"
    else
        log "User ${GAMESERVER_USER} already exists"
    fi
    
    # Set password if provided
    if [[ -n "${GAMESERVER_PASS}" ]]; then
        log "Setting password for ${GAMESERVER_USER}"
        if [[ "$DRY_RUN" != "true" ]]; then
            echo "${GAMESERVER_USER}:${GAMESERVER_PASS}" | chpasswd
        else
            log "[DRY-RUN] Would set password for ${GAMESERVER_USER}"
        fi
    fi
    
    # Add to sudo group
    if is_debian; then
        run_cmd usermod -aG sudo "${GAMESERVER_USER}" || true
    elif is_rhel; then
        run_cmd usermod -aG wheel "${GAMESERVER_USER}" || true
    fi
    
    # Configure passwordless sudo for specific commands
    local sudoers_file="/etc/sudoers.d/${GAMESERVER_USER}"
    if [[ "$DRY_RUN" != "true" ]]; then
        cat > "${sudoers_file}" <<EOF
# WDS gameserver user sudo rules
${GAMESERVER_USER} ALL=(ALL) NOPASSWD: /usr/sbin/service
${GAMESERVER_USER} ALL=(ALL) NOPASSWD: /bin/systemctl
${GAMESERVER_USER} ALL=(ALL) NOPASSWD: /usr/bin/chpasswd
EOF
        chmod 440 "${sudoers_file}"
        log "Created sudoers file: ${sudoers_file}"
    else
        log "[DRY-RUN] Would create sudoers file: ${sudoers_file}"
    fi
}

# =============================================================================
# Directory Setup
# =============================================================================
setup_tools_directory() {
    log "Setting up tools directory structure..."
    
    local dirs=(
        "${TOOLS_DIR}"
        "${SCRIPTS_DIR}"
        "${TOOLS_DIR}/backups"
        "${TOOLS_DIR}/logs"
    )
    
    for dir in "${dirs[@]}"; do
        if [[ ! -d "$dir" ]]; then
            run_cmd mkdir -p "$dir"
        fi
    done
    
    # Set ownership
    if ! is_cygwin; then
        run_cmd chown -R "${GAMESERVER_USER}:${GAMESERVER_USER}" "${TOOLS_DIR}"
    fi
    
    # Create empty password file with secure permissions
    local passfile="${TOOLS_DIR}/.password"
    if [[ ! -f "${passfile}" ]]; then
        if [[ "$DRY_RUN" != "true" ]]; then
            touch "${passfile}"
            chmod 600 "${passfile}"
            if ! is_cygwin; then
                chown "${GAMESERVER_USER}:${GAMESERVER_USER}" "${passfile}"
            fi
            log "Created password file: ${passfile}"
        else
            log "[DRY-RUN] Would create password file: ${passfile}"
        fi
    fi
    
    # Create servers.txt template
    local servers_file="${TOOLS_DIR}/servers.txt"
    if [[ ! -f "${servers_file}" ]]; then
        if [[ "$DRY_RUN" != "true" ]]; then
            cat > "${servers_file}" <<EOF
# WDS Server List
# Format: hostname:port (port defaults to 12322 if omitted)
# Example:
# core.iaregamer.com:12322
# core-dr.iaregamer.com:12322
EOF
            if ! is_cygwin; then
                chown "${GAMESERVER_USER}:${GAMESERVER_USER}" "${servers_file}"
            fi
            log "Created servers file template: ${servers_file}"
        else
            log "[DRY-RUN] Would create servers file: ${servers_file}"
        fi
    fi
}

# =============================================================================
# SSH Configuration
# =============================================================================
configure_ssh() {
    if [[ "$SKIP_SSH" == "true" ]]; then
        log "Skipping SSH configuration (--skip-ssh)"
        return 0
    fi
    
    log "Configuring SSH on port ${SSH_PORT}..."
    
    if is_cygwin; then
        log "Cygwin: SSH configuration should be done via Windows OpenSSH or Cygwin sshd"
        log "Ensure sshd_config has: Port ${SSH_PORT}"
        return 0
    fi
    
    local sshd_config="/etc/ssh/sshd_config"
    
    if [[ ! -f "${sshd_config}" ]]; then
        warn "SSH config not found: ${sshd_config}"
        return 1
    fi
    
    if [[ "$DRY_RUN" != "true" ]]; then
        # Backup original config
        cp "${sshd_config}" "${sshd_config}.bak.$(date '+%Y%m%d')" 2>/dev/null || true
        
        # Update port - use consistent quoting with double quotes for variable expansion
        if grep -qE '^\s*#?\s*Port\s+' "${sshd_config}"; then
            sed -i "s/^[[:space:]]*#*[[:space:]]*Port[[:space:]]\+.*/Port ${SSH_PORT}/" "${sshd_config}"
        else
            echo "Port ${SSH_PORT}" >> "${sshd_config}"
        fi
        
        # Enable password authentication (for initial setup) - consistent quoting
        sed -i "s/^[[:space:]]*#*[[:space:]]*PasswordAuthentication[[:space:]]\+no/PasswordAuthentication yes/" "${sshd_config}"
        
        # Restart SSH service
        if command -v systemctl >/dev/null 2>&1; then
            systemctl restart sshd || systemctl restart ssh || true
        else
            service sshd restart || service ssh restart || true
        fi
        
        log "SSH configured on port ${SSH_PORT}"
    else
        log "[DRY-RUN] Would configure SSH on port ${SSH_PORT}"
    fi
}

# =============================================================================
# Firewall Configuration
# =============================================================================
configure_firewall() {
    if [[ "$SKIP_FIREWALL" == "true" ]]; then
        log "Skipping firewall configuration (--skip-firewall)"
        return 0
    fi
    
    log "Configuring firewall..."
    
    if is_cygwin; then
        log "Cygwin: Firewall should be configured via Windows Firewall"
        log "Required ports: SSH=${SSH_PORT}, Games=${GAME_PORTS_TCP}, GSP=${GSP_PORT}, FTP=${FTP_PORT}"
        return 0
    fi
    
    # UFW (Ubuntu/Debian)
    if command -v ufw >/dev/null 2>&1; then
        log "Configuring UFW firewall..."
        run_cmd ufw allow "${SSH_PORT}/tcp" comment "SSH Custom Port"
        run_cmd ufw allow "${GAME_PORTS_TCP}/tcp" comment "Game Servers TCP"
        run_cmd ufw allow "${GAME_PORTS_UDP}/udp" comment "Game Servers UDP"
        run_cmd ufw allow "${GSP_PORT}/tcp" comment "GSP Agent"
        run_cmd ufw allow "${FTP_PORT}/tcp" comment "FTP"
        run_cmd ufw allow "${FTP_PASSIVE_PORTS}/tcp" comment "FTP Passive"
        if [[ "$DRY_RUN" != "true" ]]; then
            ufw --force enable || true
        fi
        return 0
    fi
    
    # Firewalld (RHEL/CentOS)
    if command -v firewall-cmd >/dev/null 2>&1; then
        log "Configuring firewalld..."
        run_cmd firewall-cmd --permanent --add-port="${SSH_PORT}/tcp"
        run_cmd firewall-cmd --permanent --add-port="${GAME_PORTS_TCP}/tcp"
        run_cmd firewall-cmd --permanent --add-port="${GAME_PORTS_UDP}/udp"
        run_cmd firewall-cmd --permanent --add-port="${GSP_PORT}/tcp"
        run_cmd firewall-cmd --permanent --add-port="${FTP_PORT}/tcp"
        run_cmd firewall-cmd --permanent --add-port="${FTP_PASSIVE_PORTS}/tcp"
        run_cmd firewall-cmd --reload
        return 0
    fi
    
    # iptables fallback
    if command -v iptables >/dev/null 2>&1; then
        log "Configuring iptables..."
        run_cmd iptables -A INPUT -p tcp --dport "${SSH_PORT}" -j ACCEPT
        run_cmd iptables -A INPUT -p tcp --dport "$(port_range_to_iptables "${GAME_PORTS_TCP}")" -j ACCEPT
        run_cmd iptables -A INPUT -p udp --dport "$(port_range_to_iptables "${GAME_PORTS_UDP}")" -j ACCEPT
        run_cmd iptables -A INPUT -p tcp --dport "${GSP_PORT}" -j ACCEPT
        run_cmd iptables -A INPUT -p tcp --dport "${FTP_PORT}" -j ACCEPT
        run_cmd iptables -A INPUT -p tcp --dport "$(port_range_to_iptables "${FTP_PASSIVE_PORTS}")" -j ACCEPT
        
        # Save rules
        if command -v iptables-save >/dev/null 2>&1; then
            if [[ "$DRY_RUN" != "true" ]]; then
                iptables-save > /etc/iptables.rules 2>/dev/null || true
            fi
        fi
        return 0
    fi
    
    warn "No supported firewall found. Please configure manually."
}

# =============================================================================
# Scripts Installation
# =============================================================================
install_scripts() {
    log "Installing WDS ops-tools scripts..."
    
    # Determine script source directory
    local script_dir
    script_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
    local src_scripts="${script_dir}/scripts"
    
    if [[ ! -d "${src_scripts}" ]]; then
        warn "Scripts directory not found: ${src_scripts}"
        log "Scripts can be copied manually from the WDS-website repository"
        return 0
    fi
    
    # Copy scripts
    if [[ "$DRY_RUN" != "true" ]]; then
        cp -r "${src_scripts}"/* "${SCRIPTS_DIR}/" 2>/dev/null || true
        chmod +x "${SCRIPTS_DIR}"/*.sh 2>/dev/null || true
        if ! is_cygwin; then
            chown -R "${GAMESERVER_USER}:${GAMESERVER_USER}" "${SCRIPTS_DIR}"
        fi
        log "Scripts installed to ${SCRIPTS_DIR}"
    else
        log "[DRY-RUN] Would copy scripts from ${src_scripts} to ${SCRIPTS_DIR}"
    fi
}

# =============================================================================
# Summary
# =============================================================================
print_summary() {
    log ""
    log "=============================================="
    log "  Bootstrap Complete"
    log "=============================================="
    log ""
    log "Platform: $(uname -s) ($(get_pkg_manager))"
    log "SSH Port: ${SSH_PORT}"
    log "User: ${GAMESERVER_USER}"
    log "Tools Dir: ${TOOLS_DIR}"
    log "Scripts Dir: ${SCRIPTS_DIR}"
    log ""
    log "Next Steps:"
    log "  1. Set the shared password in ${TOOLS_DIR}/.password"
    log "  2. Update ${TOOLS_DIR}/servers.txt with your server list"
    log "  3. Test SSH: ssh -p ${SSH_PORT} ${GAMESERVER_USER}@<hostname>"
    log "  4. Run status reporter: ${SCRIPTS_DIR}/report_server_status.sh"
    log ""
    if [[ "$DRY_RUN" == "true" ]]; then
        log "NOTE: This was a dry run. No changes were made."
    else
        log "Log file: ${LOG_FILE}"
    fi
    log ""
}

# =============================================================================
# Main
# =============================================================================
main() {
    parse_args "$@"
    
    # Create log directory
    if [[ "$DRY_RUN" != "true" ]]; then
        mkdir -p "${LOG_DIR}" 2>/dev/null || true
    fi
    
    log "=============================================="
    log "  WDS Game Server Host Bootstrap"
    log "=============================================="
    log "Starting bootstrap at $(date)"
    log "Platform: $(uname -s)"
    
    require_root
    
    install_packages
    setup_gameserver_user
    setup_tools_directory
    configure_ssh
    configure_firewall
    install_scripts
    
    print_summary
}

main "$@"
