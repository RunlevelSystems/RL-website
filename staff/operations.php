<?php
session_start();
define('WDS_SYSTEM', true);

require_once '../includes/db-config.php';
requireAdminLogin();

$current_page = 'staff-ops';
$header_class = 'projects-header inner-header';
$page_subtitle = 'Infra Runbooks';
$page_description = 'Ubuntu 24.04 baselines, PHP 7.4 requirement, MySQL 5.7 Docker, and agent installs.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/assets/images/icon.png">
    <title>Core Loop | Infrastructure Runbooks</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/ionicons.css" rel="stylesheet">
    <link href="../assets/css/main.css" rel="stylesheet">
    <link href="../assets/css/wds-unified.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@400;500;600;700;800;900&family=Inter:wght@300;400;500;600&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Ops page keeps dark code blocks for shell readability; card styling lives in wds-unified.css */
        pre {
            background: #111827 !important;
            color: #E5E7EB !important;
            padding: 12px;
            border-radius: 8px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navigation.php'; ?>
<section class="staff-login">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="title-box">
                    <p>Infrastructure</p>
                    <h2 class="title mt0" style="color:#ffd166;">Panel, Agents, Websites</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="ops-card">
                    <h3><i class="ion-social-tux"></i> Ubuntu 24.04 Panel Baseline</h3>
                    <p>Every panel host runs Ubuntu 24.04 LTS with PHP 7.4 (for xmlrpc) and MySQL 5.7 inside Docker.</p>
<pre>apt update && apt install -y software-properties-common gnupg lsb-release git curl docker.io docker-compose-plugin
add-apt-repository ppa:ondrej/php -y
apt install -y php7.4 php7.4-{mysql,xml,gd,mbstring,zip,curl} libapache2-mod-php7.4
systemctl enable --now docker</pre>
                    <p>Deploy MySQL 5.7 in Docker with the standard password:</p>
<pre>mkdir -p /srv/mysql57/{data,conf}
cat >/srv/mysql57/docker-compose.yml <<'YML'
version: '3.8'
services:
  mysql57:
    image: mysql:5.7
    restart: unless-stopped
    environment:
      MYSQL_ROOT_PASSWORD: Pkloyn7yvpht!
      MYSQL_DATABASE: panel
    ports:
      - "3306:3306"
    volumes:
      - /srv/mysql57/data:/var/lib/mysql
      - /srv/mysql57/conf:/etc/mysql/conf.d
YML
docker compose -f /srv/mysql57/docker-compose.yml up -d</pre>
                    <p>Clone the panel and deploy:</p>
<pre>git clone https://github.com/GameServerPanel/GSP.git /opt/gsp
cd /opt/gsp/bootstrap/ubuntu-24.04
./install_panel.sh --db-host mysql.iaregamer.com --db-name panel --db-user remoteuser --db-pass "Pkloyn7yvpht!"</pre>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="ops-card">
                    <h3><i class="ion-android-desktop"></i> Linux Agent Install (Ubuntu 24.04)</h3>
                    <ol style="color:#E5E7EB;">
                        <li><code>apt update && apt install -y perl libssl-dev libxml2-utils screen rsync</code></li>
                        <li>Clone <code>https://github.com/GameServerPanel/GSP-Agent-Linux.git</code> to <code>/opt/gsp-agent</code>.</li>
                        <li>Run <code>cd /opt/gsp-agent && ./install.sh</code> to build dependencies.</li>
                        <li>Edit <code>/usr/local/etc/ogp_agent.conf</code>: set <code>remote_host</code>, <code>remote_port</code>, and <code>encryption_key</code> (match panel entry).</li>
                        <li>Install systemd service:<br><code>cp systemd/ogp_agent.service /etc/systemd/system/ && systemctl enable --now ogp_agent</code></li>
                        <li>Add host to the panel (Administration → Servers) and confirm heartbeat.</li>
                    </ol>
                    <p>Ubuntu 24.04 ships Perl 5.38+; the agent scripts are compatible (tested against the latest <code>main</code> branch of <code>GSP-Agent-Linux</code>).</p>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="ops-card">
                    <h3><i class="ion-social-windows"></i> Windows Server 2019 Agent (Cygwin)</h3>
                    <ol style="color:#E5E7EB;">
                        <li>Log in as Administrator, install the latest updates, and download the repo: <code>git clone https://github.com/GameServerPanel/GSP-Agent-Windows.git C:\gsp-agent</code>.</li>
                        <li>Run PowerShell as admin and execute <code>powershell.exe -ExecutionPolicy Bypass -File ops-tools/Bootstrap-GameServerHost.ps1</code>. Script installs Cygwin, rsync, screen, and registers the service.</li>
                        <li>Open Cygwin terminal: <code>cd /cygdrive/c/gsp-agent && ./install.sh</code>.</li>
                        <li>Edit <code>agent_conf.sh</code> with panel IP/port and encryption key, then run <code>./ogp_agent.pl --config agent_conf.sh --register</code>.</li>
                        <li>Use Windows Services to ensure “OGP Agent” is set to Automatic (Delayed Start).</li>
                    </ol>
                    <p>Firewall: open TCP/UDP ports used by assigned games plus the agent port configured in the panel.</p>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="ops-card">
                    <h3><i class="ion-wand"></i> Website Deployments</h3>
                    <ul style="color:#E5E7EB;">
                        <li><strong>Core Loop Website:</strong> standard Apache/PHP host. Clone this repo, update <code>includes/db-config.php</code>, and run <code>deploy_gsp.sh</code> if panel files are co-located.</li>
                        <li><strong>Gameservers World:</strong> static marketing pages plus billing module. Keep content synced with <code>GSP/modules/billing</code>.</li>
                        <li><strong>Status site:</strong> copy <code>ops-tools/www/status</code> to an internal Apache host, update <code>config.php</code>, and point it to the <code>peer_status</code> MySQL database.</li>
                    </ul>
                    <p>Always update <code>staff-info.php</code> and the wiki whenever steps change.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include '../includes/footer.php'; ?>
<script src="../assets/js/jquery-1.12.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>
