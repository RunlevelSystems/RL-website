<?php
if (!defined('WDS_SYSTEM')) {
    die('Access denied');
}

/**
 * Load staff credentials and server data from JSON file for easy editing
 * 
 * Note: The JSON file contains sensitive credentials and should only be
 * accessible to authenticated staff members through the protected pages.
 */
function load_staff_data() {
    static $data = null;
    if ($data === null) {
        $jsonPath = __DIR__ . '/../content/staff-credentials.json';
        if (!file_exists($jsonPath)) {
            error_log("Staff credentials file not found: $jsonPath");
            $data = ['credentials' => [], 'core_servers' => [], 'other_servers' => []];
            return $data;
        }
        
        $json = file_get_contents($jsonPath);
        if ($json === false) {
            error_log("Failed to read staff credentials file: $jsonPath");
            $data = ['credentials' => [], 'core_servers' => [], 'other_servers' => []];
            return $data;
        }
        
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("Failed to parse staff credentials JSON: " . json_last_error_msg());
            $data = ['credentials' => [], 'core_servers' => [], 'other_servers' => []];
            return $data;
        }
    }
    return $data;
}

function staff_credentials() {
    $data = load_staff_data();
    return $data['credentials'] ?? [];
}

function staff_core_servers() {
    $data = load_staff_data();
    return $data['core_servers'] ?? [];
}

function staff_other_servers() {
    $data = load_staff_data();
    return $data['other_servers'] ?? [];
}

function staff_all_servers() {
    return array_merge(staff_core_servers(), staff_other_servers());
}

function staff_tool_catalog() {
    return [
        [
            'name' => 'install.sh',
            'path' => 'ops-tools/scripts/install.sh',
            'summary' => 'Unified prerequisites installer for Linux (Debian/Ubuntu, RHEL), Cygwin, and Windows game server hosts.',
            'usage' => 'sudo ./install.sh [--ssh-port 12322] [--minimal] [--dry-run]',
            'notes' => 'Installs packages, creates gameserver user, configures SSH/firewall, and sets up tools directory.'
        ],
        [
            'name' => 'backup.sh',
            'path' => 'ops-tools/scripts/backup.sh',
            'summary' => 'Creates local backups of game data, web files, MySQL databases, and configs on each server.',
            'usage' => './backup.sh [--backup-root /sdb1/backups] [--keep-days 7] [--no-mysql]',
            'notes' => 'Run via cron on each game server. Creates timestamped tarballs with automatic cleanup.'
        ],
        [
            'name' => 'status.sh',
            'path' => 'ops-tools/scripts/status.sh',
            'summary' => 'Displays local server CPU, memory, disk usage and top processes. Can report to MySQL.',
            'usage' => './status.sh [--report] [--mysql-host core.iaregamer.com] [--json]',
            'notes' => 'Use --report to send metrics to peer_status database. Add to cron: */5 * * * *'
        ],
        [
            'name' => 'status_all.sh',
            'path' => 'ops-tools/scripts/status_all.sh',
            'summary' => 'Displays fleet-wide status by querying peer_status MySQL database. Run on core host.',
            'usage' => './status_all.sh [--json] [--summary] [--offline-only]',
            'notes' => 'Shows aggregate totals and individual server metrics from all reporting hosts.'
        ],
        [
            'name' => 'change_passwd.sh',
            'path' => 'ops-tools/scripts/change_passwd.sh',
            'summary' => 'Rotates Linux gameserver password and MySQL credentials across all infrastructure hosts.',
            'usage' => './change_passwd.sh "NewPassword!" [--core-only] [--mysql-only] [--dry-run]',
            'notes' => 'Must run from core host. Updates .password file on all servers in servers.txt.'
        ],
        [
            'name' => 'xfer.sh',
            'path' => 'ops-tools/scripts/xfer.sh',
            'summary' => 'Simple folder transfer between servers using rsync over SSH.',
            'usage' => './xfer.sh /path/to/folder hostname [--port 12322] [--delete] [--background]',
            'notes' => 'Use --check to monitor background transfers. Uses SSH keys or .password file.'
        ],
        [
            'name' => 'dr_rsync_push.sh',
            'path' => 'ops-tools/scripts/dr_rsync_push.sh',
            'summary' => 'Syncs data directories, configs, and MySQL databases from core to DR host.',
            'usage' => './dr_rsync_push.sh core-dr.iaregamer.com [SSH_PORT]',
            'notes' => 'Run from core host after password rotation or major changes.'
        ],
        [
            'name' => 'Bootstrap-GameServerHost.ps1',
            'path' => 'ops-tools/Bootstrap-GameServerHost.ps1',
            'summary' => 'Windows Server bootstrapper for installing VC++ runtimes, DirectX, .NET, OpenSSH, and FileZilla.',
            'usage' => 'powershell -ExecutionPolicy Bypass -File Bootstrap-GameServerHost.ps1',
            'notes' => 'Run in elevated PowerShell on a fresh Windows Server 2019/2022 host.'
        ]
    ];
}

function staff_wiki_docs_dir() {
    return __DIR__ . '/../staff/wiki/docs';
}
