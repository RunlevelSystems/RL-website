#!/usr/bin/env bash
# =============================================================================
# install.sh - Unified Prerequisites Installer
# =============================================================================
# Installs all required software for WDS game server hosts.
# Supports Linux (Debian/Ubuntu, RHEL/CentOS), Cygwin, and Windows.
#
# Usage:
#   sudo ./install.sh [OPTIONS]
#
# Options:
#   --ssh-port PORT       SSH port to configure (default: 12322)
#   --gameserver-pass     Password for gameserver user (prompts if not set)
#   --skip-firewall       Skip firewall configuration
#   --skip-ssh            Skip SSH reconfiguration
#   --skip-user           Skip gameserver user creation
#   --minimal             Install only essential packages
#   --dry-run             Show what would be done without making changes
#   --help                Show this help message
#
# Examples:
#   sudo ./install.sh                           # Full installation
#   sudo ./install.sh --ssh-port 22222          # Custom SSH port
#   sudo ./install.sh --minimal --dry-run       # Preview minimal install
#   ./install.sh --dry-run                      # Preview on any platform
#
# What gets installed:
#   Linux/Cygwin: curl, wget, git, rsync, mysql-client, sshpass, python3, etc.
#   Windows: VC++ runtimes, .NET Framework, DirectX, OpenSSH
#
# After installation:
#   1. Set the shared password in /home/gameserver/tools/.password
#   2. Update /home/gameserver/tools/servers.txt with your server list
#   3. Run status.sh --report to verify connectivity
# =============================================================================

set -euo pipefail

# =============================================================================
# Configuration
# =============================================================================
SSH_PORT="${SSH_PORT:-12322}"
GAMESERVER_USER="gameserver"
TOOLS_DIR="/home/${GAMESERVER_USER}/tools"
SCRIPTS_DIR="${TOOLS_DIR}/scripts"

# Flags
SKIP_FIREWALL=false
SKIP_SSH=false
SKIP_USER=false
MINIMAL=false
DRY_RUN=false
GAMESERVER_PASS=""

# Port ranges for game servers
GAME_PORTS_TCP="2000:12000"
GAME_PORTS_UDP="2000:12000"
GSP_PORT="12679"
FTP_PORT="21"
FTP_PASSIVE_PORTS="50000:51000"

# =============================================================================
# Helpers
# =============================================================================
die() { echo "ERROR: $*" >&2; exit 1; }
say() { printf '[%s] %s\n' "$(date '+%F %T')" "$*"; }
warn() { echo "WARNING: $*" >&2; }

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

run_cmd() {
    if [[ "$DRY_RUN" == "true" ]]; then
        say "[DRY-RUN] Would run: $*"
        return 0
    fi
    say "Running: $*"
    "$@"
}

# =============================================================================
# Parse Arguments
# =============================================================================
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
        --skip-user)
            SKIP_USER=true
            shift
            ;;
        --minimal)
            MINIMAL=true
            shift
            ;;
        --dry-run)
            DRY_RUN=true
            shift
            ;;
        --help|-h)
            head -n 40 "$0" | tail -n 38
            exit 0
            ;;
        *)
            die "Unknown option: $1. Use --help for usage."
            ;;
    esac
done

# =============================================================================
# Package Installation - Debian/Ubuntu
# =============================================================================
install_debian() {
    say "Installing packages via apt (Debian/Ubuntu)..."
    
    run_cmd apt-get update
    
    if [[ "$MINIMAL" == "true" ]]; then
        run_cmd apt-get install -y \
            curl wget rsync openssh-server mysql-client sshpass sudo python3
    else
        run_cmd apt-get install -y \
            curl wget git rsync openssh-server mysql-client sshpass \
            sudo vim htop net-tools python3 unzip tar gzip bc
    fi
}

# =============================================================================
# Package Installation - RHEL/CentOS
# =============================================================================
install_rhel() {
    say "Installing packages via dnf/yum (RHEL/CentOS)..."
    
    local pkg_cmd="dnf"
    command -v dnf >/dev/null 2>&1 || pkg_cmd="yum"
    
    run_cmd $pkg_cmd install -y epel-release 2>/dev/null || true
    
    if [[ "$MINIMAL" == "true" ]]; then
        run_cmd $pkg_cmd install -y \
            curl wget rsync openssh-server mysql sshpass sudo python3
    else
        run_cmd $pkg_cmd install -y \
            curl wget git rsync openssh-server mysql sshpass \
            sudo vim htop net-tools python3 unzip tar gzip bc
    fi
}

# =============================================================================
# Package Check - Cygwin
# =============================================================================
install_cygwin() {
    say "Cygwin detected - checking for required packages..."
    
    local essential=(curl wget rsync ssh mysql python3)
    local optional=(git vim bc)
    local missing=()
    
    for cmd in "${essential[@]}"; do
        if ! command -v "$cmd" >/dev/null 2>&1; then
            missing+=("$cmd")
        fi
    done
    
    if [[ "$MINIMAL" != "true" ]]; then
        for cmd in "${optional[@]}"; do
            if ! command -v "$cmd" >/dev/null 2>&1; then
                missing+=("$cmd")
            fi
        done
    fi
    
    if [[ ${#missing[@]} -gt 0 ]]; then
        warn "Missing packages in Cygwin: ${missing[*]}"
        say ""
        say "To install missing packages:"
        say "  1. Run Cygwin setup.exe"
        say "  2. Search for and install: ${missing[*]}"
        say ""
    else
        say "All required Cygwin packages are installed"
    fi
}

# =============================================================================
# User Setup
# =============================================================================
setup_user() {
    if [[ "$SKIP_USER" == "true" ]]; then
        say "Skipping user setup (--skip-user)"
        return 0
    fi
    
    if is_cygwin; then
        say "Cygwin: User management handled by Windows"
        return 0
    fi
    
    say "Setting up ${GAMESERVER_USER} user..."
    
    # Create user if doesn't exist
    if ! id "${GAMESERVER_USER}" >/dev/null 2>&1; then
        run_cmd useradd -m -s /bin/bash "${GAMESERVER_USER}"
    else
        say "User ${GAMESERVER_USER} already exists"
    fi
    
    # Set password if provided (using chpasswd with stdin to avoid process list exposure)
    if [[ -n "${GAMESERVER_PASS}" ]]; then
        if [[ "$DRY_RUN" != "true" ]]; then
            # Use printf to avoid password in process list
            printf '%s:%s\n' "${GAMESERVER_USER}" "${GAMESERVER_PASS}" | chpasswd
            say "Password set for ${GAMESERVER_USER}"
        else
            say "[DRY-RUN] Would set password for ${GAMESERVER_USER}"
        fi
    fi
    
    # Add to sudo group
    if is_debian; then
        run_cmd usermod -aG sudo "${GAMESERVER_USER}" 2>/dev/null || true
    elif is_rhel; then
        run_cmd usermod -aG wheel "${GAMESERVER_USER}" 2>/dev/null || true
    fi
    
    # Configure sudoers
    local sudoers_file="/etc/sudoers.d/${GAMESERVER_USER}"
    if [[ "$DRY_RUN" != "true" ]]; then
        cat > "${sudoers_file}" <<EOF
# WDS gameserver user sudo rules
${GAMESERVER_USER} ALL=(ALL) NOPASSWD: /usr/sbin/service
${GAMESERVER_USER} ALL=(ALL) NOPASSWD: /bin/systemctl
${GAMESERVER_USER} ALL=(ALL) NOPASSWD: /usr/bin/chpasswd
EOF
        chmod 440 "${sudoers_file}"
        say "Created sudoers file: ${sudoers_file}"
    else
        say "[DRY-RUN] Would create sudoers file: ${sudoers_file}"
    fi
}

# =============================================================================
# Directory Setup
# =============================================================================
setup_directories() {
    say "Setting up tools directory structure..."
    
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
    if ! is_cygwin && [[ "$DRY_RUN" != "true" ]]; then
        chown -R "${GAMESERVER_USER}:${GAMESERVER_USER}" "${TOOLS_DIR}" 2>/dev/null || true
    fi
    
    # Create password file placeholder
    local passfile="${TOOLS_DIR}/.password"
    if [[ ! -f "${passfile}" ]]; then
        if [[ "$DRY_RUN" != "true" ]]; then
            touch "${passfile}"
            chmod 600 "${passfile}"
            if ! is_cygwin; then
                chown "${GAMESERVER_USER}:${GAMESERVER_USER}" "${passfile}" 2>/dev/null || true
            fi
            say "Created password file: ${passfile}"
        else
            say "[DRY-RUN] Would create password file: ${passfile}"
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
                chown "${GAMESERVER_USER}:${GAMESERVER_USER}" "${servers_file}" 2>/dev/null || true
            fi
            say "Created servers file template: ${servers_file}"
        else
            say "[DRY-RUN] Would create servers file: ${servers_file}"
        fi
    fi
}

# =============================================================================
# SSH Configuration
# =============================================================================
configure_ssh() {
    if [[ "$SKIP_SSH" == "true" ]]; then
        say "Skipping SSH configuration (--skip-ssh)"
        return 0
    fi
    
    if is_cygwin; then
        say "Cygwin: Configure SSH via Windows OpenSSH or Cygwin sshd"
        say "  Ensure sshd_config has: Port ${SSH_PORT}"
        return 0
    fi
    
    say "Configuring SSH on port ${SSH_PORT}..."
    
    local sshd_config="/etc/ssh/sshd_config"
    
    if [[ ! -f "${sshd_config}" ]]; then
        warn "SSH config not found: ${sshd_config}"
        return 0
    fi
    
    if [[ "$DRY_RUN" != "true" ]]; then
        # Backup config
        cp "${sshd_config}" "${sshd_config}.bak.$(date '+%Y%m%d')" 2>/dev/null || true
        
        # Update port
        if grep -qE '^\s*#?\s*Port\s+' "${sshd_config}"; then
            sed -i "s/^[[:space:]]*#*[[:space:]]*Port[[:space:]]\+.*/Port ${SSH_PORT}/" "${sshd_config}"
        else
            echo "Port ${SSH_PORT}" >> "${sshd_config}"
        fi
        
        # Enable password authentication
        sed -i "s/^[[:space:]]*#*[[:space:]]*PasswordAuthentication[[:space:]]\+no/PasswordAuthentication yes/" "${sshd_config}"
        
        # Restart SSH
        if command -v systemctl >/dev/null 2>&1; then
            systemctl restart sshd 2>/dev/null || systemctl restart ssh 2>/dev/null || true
        else
            service sshd restart 2>/dev/null || service ssh restart 2>/dev/null || true
        fi
        
        say "SSH configured on port ${SSH_PORT}"
    else
        say "[DRY-RUN] Would configure SSH on port ${SSH_PORT}"
    fi
}

# =============================================================================
# Firewall Configuration
# =============================================================================
configure_firewall() {
    if [[ "$SKIP_FIREWALL" == "true" ]]; then
        say "Skipping firewall configuration (--skip-firewall)"
        return 0
    fi
    
    if is_cygwin; then
        say "Cygwin: Configure Windows Firewall manually"
        say "  Required ports: SSH=${SSH_PORT}, Games=${GAME_PORTS_TCP}, GSP=${GSP_PORT}"
        return 0
    fi
    
    say "Configuring firewall..."
    
    # UFW (Ubuntu/Debian)
    if command -v ufw >/dev/null 2>&1; then
        say "Configuring UFW firewall..."
        run_cmd ufw allow "${SSH_PORT}/tcp" 2>/dev/null || true
        run_cmd ufw allow "${GAME_PORTS_TCP}/tcp" 2>/dev/null || true
        run_cmd ufw allow "${GAME_PORTS_UDP}/udp" 2>/dev/null || true
        run_cmd ufw allow "${GSP_PORT}/tcp" 2>/dev/null || true
        run_cmd ufw allow "${FTP_PORT}/tcp" 2>/dev/null || true
        run_cmd ufw allow "${FTP_PASSIVE_PORTS}/tcp" 2>/dev/null || true
        if [[ "$DRY_RUN" != "true" ]]; then
            ufw --force enable 2>/dev/null || true
        fi
        return 0
    fi
    
    # Firewalld (RHEL/CentOS)
    if command -v firewall-cmd >/dev/null 2>&1; then
        say "Configuring firewalld..."
        run_cmd firewall-cmd --permanent --add-port="${SSH_PORT}/tcp" 2>/dev/null || true
        run_cmd firewall-cmd --permanent --add-port="${GAME_PORTS_TCP}/tcp" 2>/dev/null || true
        run_cmd firewall-cmd --permanent --add-port="${GAME_PORTS_UDP}/udp" 2>/dev/null || true
        run_cmd firewall-cmd --permanent --add-port="${GSP_PORT}/tcp" 2>/dev/null || true
        run_cmd firewall-cmd --reload 2>/dev/null || true
        return 0
    fi
    
    warn "No supported firewall found. Configure manually."
}

# =============================================================================
# Copy Scripts
# =============================================================================
copy_scripts() {
    say "Installing WDS ops-tools scripts..."
    
    local script_dir
    script_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
    
    if [[ ! -d "${script_dir}" ]] || [[ ! -f "${script_dir}/status.sh" ]]; then
        warn "Scripts not found in ${script_dir}"
        say "Copy scripts manually from WDS-website/ops-tools/scripts/"
        return 0
    fi
    
    if [[ "$DRY_RUN" != "true" ]]; then
        cp "${script_dir}"/*.sh "${SCRIPTS_DIR}/" 2>/dev/null || true
        chmod +x "${SCRIPTS_DIR}"/*.sh 2>/dev/null || true
        if ! is_cygwin; then
            chown -R "${GAMESERVER_USER}:${GAMESERVER_USER}" "${SCRIPTS_DIR}" 2>/dev/null || true
        fi
        say "Scripts installed to ${SCRIPTS_DIR}"
    else
        say "[DRY-RUN] Would copy scripts to ${SCRIPTS_DIR}"
    fi
}

# =============================================================================
# Print Summary
# =============================================================================
print_summary() {
    say ""
    say "=============================================="
    say "  Installation Complete"
    say "=============================================="
    say ""
    say "Platform:    $(uname -s)"
    say "SSH Port:    ${SSH_PORT}"
    say "User:        ${GAMESERVER_USER}"
    say "Tools Dir:   ${TOOLS_DIR}"
    say "Scripts Dir: ${SCRIPTS_DIR}"
    say ""
    say "Next steps:"
    say "  1. Set the shared password:"
    say "     echo 'YourPassword' > ${TOOLS_DIR}/.password"
    say "     chmod 600 ${TOOLS_DIR}/.password"
    say ""
    say "  2. Update servers.txt:"
    say "     vi ${TOOLS_DIR}/servers.txt"
    say ""
    say "  3. Test status reporting:"
    say "     ${SCRIPTS_DIR}/status.sh"
    say "     ${SCRIPTS_DIR}/status.sh --report"
    say ""
    
    if [[ "$DRY_RUN" == "true" ]]; then
        say "NOTE: This was a dry run. No changes were made."
    fi
}

# =============================================================================
# Main
# =============================================================================
say "=============================================="
say "  WDS Game Server Host Installer"
say "=============================================="
say ""
say "Platform: $(uname -s)"
say "SSH Port: ${SSH_PORT}"
say "Minimal:  ${MINIMAL}"
say "Dry Run:  ${DRY_RUN}"
say ""

require_root

# Install packages based on platform
if is_cygwin; then
    install_cygwin
elif is_debian; then
    install_debian
elif is_rhel; then
    install_rhel
else
    warn "Unsupported platform. Install packages manually."
fi

# Setup user and directories
setup_user
setup_directories

# Configure SSH and firewall
configure_ssh
configure_firewall

# Copy scripts
copy_scripts

# Print summary
print_summary
