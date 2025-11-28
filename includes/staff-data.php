<?php
if (!defined('WDS_SYSTEM')) {
    die('Access denied');
}

/**
 * Load staff credentials and server data from JSON file for easy editing
 */
function load_staff_data() {
    static $data = null;
    if ($data === null) {
        $jsonPath = __DIR__ . '/../content/staff-credentials.json';
        if (file_exists($jsonPath)) {
            $json = file_get_contents($jsonPath);
            $data = json_decode($json, true);
        } else {
            $data = ['credentials' => [], 'core_servers' => [], 'other_servers' => []];
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
            'name' => 'report_server_status.sh',
            'path' => 'ops-tools/scripts/report_server_status.sh',
            'summary' => 'Reports server CPU, memory, disk usage and top 5 processes to the MySQL peer_status database.',
            'usage' => './report_server_status.sh [--mysql-host core.iaregamer.com] [--mysql-port 3306]',
            'notes' => 'Auto-creates database and tables on first run. Add to cron for regular reporting: */5 * * * *'
        ],
        [
            'name' => 'check_servers.sh',
            'path' => 'ops-tools/scripts/check_servers.sh',
            'summary' => 'Rotates the gameserver Linux password across all hosts in servers.txt and updates MySQL credentials.',
            'usage' => './check_servers.sh --password "NewSuperSecret!" (run on core host only)',
            'notes' => 'Requires passwordless sudo on the core node plus sshpass if keys are missing.'
        ],
        [
            'name' => 'setup_mysql_users.sh',
            'path' => 'ops-tools/scripts/setup_mysql_users.sh',
            'summary' => 'Recreates MySQL accounts for every monitoring IP listed in servers.txt.',
            'usage' => 'sudo ./setup_mysql_users.sh (run on the MySQL host after rotations)',
            'notes' => 'Also seeds the peer_status database used by /ops-tools/www/status.'
        ],
        [
            'name' => 'deploy_gsp.sh',
            'path' => 'ops-tools/scripts/deploy_gsp.sh',
            'summary' => 'Stages the GSP repo, rsyncs it into the live panel directory, and fixes permissions.',
            'usage' => 'WEB_ROOT=/var/www/gsp OWNER=www-data GROUP=www-data ./deploy_gsp.sh',
            'notes' => 'Respects includes/config.inc.php and modules/billing/includes/config.inc.php (never overwrites them). Use DRY_RUN=1 to verify before releasing.'
        ],
        [
            'name' => 'hourly_backups.sh + mysql_backup.sh',
            'path' => 'ops-tools/scripts/hourly_backups.sh',
            'summary' => 'Tar/gzip snapshots of /etc, /var/www, tools/, and MySQL dumps per host.',
            'usage' => 'Triggered from cron on core + DR. Edit vars inside the scripts for destination paths.',
            'notes' => 'Produces /sdb1/backups/<host>/<timestamp>.tgz ready for rsync to DR.'
        ],
        [
            'name' => 'peer_watch.sh + www/status',
            'path' => 'ops-tools/scripts/peer_watch.sh',
            'summary' => 'Feeds the lightweight /status dashboard so we can see which hosts are online and when backups last ran.',
            'usage' => 'Configure /ops-tools/www/status/config.php then host the PHP files on any Apache/PHP box.',
            'notes' => 'The status site reads the peer_status database created by setup_mysql_users.sh.'
        ],
        [
            'name' => 'Bootstrap-GameServerHost.ps1',
            'path' => 'ops-tools/Bootstrap-GameServerHost.ps1',
            'summary' => 'Windows Server bootstrapper for installing dependencies, Cygwin, and the Windows agent.',
            'usage' => 'Run in elevated PowerShell on a fresh Windows Server 2019 host.',
            'notes' => 'Pairs with the installation steps documented on the Staff Operations page.'
        ],
        [
            'name' => 'dr_rsync_push.sh / xfer.sh',
            'path' => 'ops-tools/scripts/dr_rsync_push.sh',
            'summary' => 'Pushes the latest tarballs + peer_status data from core to the DR node using rsync/ssh.',
            'usage' => './dr_rsync_push.sh --target core-dr.iaregamer.com --path /sdb1/backups',
            'notes' => 'Respects ssh keys if present; otherwise uses password: Inc0rrect!'
        ]
    ];
}

function staff_wiki_docs_dir() {
    return __DIR__ . '/../staff/wiki/docs';
}
