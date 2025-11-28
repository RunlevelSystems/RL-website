<?php
if (!defined('WDS_SYSTEM')) {
    die('Access denied');
}

function staff_credentials() {
    return [
        [
            'name' => 'Panel Database (remote access)',
            'details' => [
                'Host' => 'mysql.iaregamer.com',
                'Port' => '3306 (MySQL 5.7 Docker)',
                'Database' => 'panel',
                'Username' => 'remoteuser',
                'Password' => 'Pkloyn7yvpht!',
            ],
            'notes' => 'Used by the WDS website, reporting jobs, and any off-panel tooling. Read/write; restrict access to WDS systems only.'
        ],
        [
            'name' => 'Panel Database (local Adminer/CLI)',
            'details' => [
                'Host' => '127.0.0.1',
                'Port' => '3306',
                'Database' => 'panel',
                'Username' => 'localuser',
                'Password' => 'Pkloyn7yvpht!',
            ],
            'notes' => 'Login through Adminer (`https://panel-host/adminer.php`) or the MySQL socket. Password is rotated via check_servers.sh.'
        ],
        [
            'name' => 'Shared Linux / SFTP login',
            'details' => [
                'Username' => 'gameserver',
                'SSH Port' => '12322 on every host',
                'Password' => 'Inc0rrect!',
            ],
            'notes' => 'Works on every core + DR node listed in servers.txt. Prefer SSH keys; keep password synced with the rotation script.'
        ],
        [
            'name' => 'Windows Servers',
            'details' => [
                'Username' => 'cyg_server / gameserver',
                'Password' => 'C37p7wzkkhcn!',
                'RDP Port' => '13389',
                'SSH Port' => '12322',
            ],
            'notes' => 'Administrator is assigned by the server host. Use these credentials for cyg_server/gameserver access.'
        ],
        [
            'name' => 'Gmail Account',
            'details' => [
                'Email' => 'iaregamer.com@gmail.com',
                'Password' => 'Inc0rrect',
            ],
            'notes' => 'Team email account for official correspondence.'
        ],
        [
            'name' => 'Gameservers World Panel',
            'details' => [
                'Username' => 'iaregamer',
                'Password' => 'Inc0rrect',
            ],
            'notes' => 'Game server control panel login.'
        ],
        [
            'name' => 'cPanel Webhost and Email',
            'details' => [
                'Username' => 'domainpl',
                'Password' => 'Inc0rrect',
            ],
            'notes' => 'cPanel access for website hosting and email management.'
        ],
        [
            'name' => 'WDS Staff Portal',
            'details' => [
                'URL' => 'https://worlddomination.software/login.php',
                'Auth source' => 'gsp_users / ogp_users admin records',
            ],
            'notes' => 'Any panel admin can sign in. Promote/demote users inside the panel admin module.'
        ],
        [
            'name' => 'Learning Platforms',
            'details' => [
                'Zenva' => 'wds_team_account / ZenvaAccess2024!',
                'Mammoth Interactive' => 'wds_coop_login / MammothDev2024#',
                'Udemy Business' => 'worlddomsoftware@business.udemy.com / UdemyBiz2024$'
            ],
            'notes' => 'Shared learning platform accounts. Update here when rotated.'
        ]
    ];
}

function staff_core_servers() {
    return [
        ['hostname' => 'core.iaregamer.com', 'role' => 'Primary panel + cron', 'ssh' => 'gameserver@core.iaregamer.com:12322'],
        ['hostname' => 'core-dr.iaregamer.com', 'role' => 'Disaster-recovery replica + peer status DB', 'ssh' => 'gameserver@core-dr.iaregamer.com:12322'],
        ['hostname' => 'kc.iaregamer.com', 'role' => 'Kansas City agent / backup node', 'ssh' => 'gameserver@kc.iaregamer.com:12322'],
    ];
}

function staff_tool_catalog() {
    return [
        [
            'name' => 'report_server_status.sh',
            'path' => 'ops-tools/scripts/report_server_status.sh',
            'summary' => 'Reports server CPU, memory, disk usage and top 5 processes to the MySQL server_status database.',
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
