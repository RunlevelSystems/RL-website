<?php
session_start();
define('WDS_SYSTEM', true);

require_once 'includes/db-config.php';
require_once 'includes/staff-data.php';
requireAdminLogin();

$passwordEntries = staff_passwords();
$coreServers = staff_core_servers();
$otherServers = staff_other_servers();
$toolCatalog = staff_tool_catalog();

$current_page = 'staff-info';
$header_class = 'login-header inner-header';
$page_subtitle = 'Staff Control Room';
$page_description = 'Centralize every password, host, tool and operating procedure in one place.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WDS | Staff Control Room</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/magnific-popup.css" rel="stylesheet">
    <link href="assets/css/owl.carousel.css" rel="stylesheet">
    <link href="assets/css/owl.carousel.theme.min.css" rel="stylesheet">
    <link href="assets/css/ionicons.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
    <link href="assets/css/wds-unified.css" rel="stylesheet">
    <link href="assets/css/readability-improvements.css" rel="stylesheet">
    <style>
        .staff-grid { display: flex; flex-wrap: wrap; gap: 20px; }
        .staff-card { flex: 1 1 320px; background: rgba(0,0,0,0.5); border: 1px solid rgba(139,69,19,0.4); border-radius: 14px; padding: 25px; box-shadow: 0 10px 25px rgba(0,0,0,0.35); }
        .staff-card h3 { margin-top: 0; color: #FFD699; }
        .staff-credentials-table th {
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.08em;
            border-color: rgba(255,255,255,0.2);
        }
        .staff-credentials-table td {
            border-color: rgba(255,255,255,0.1);
        }
        .tag { display: inline-block; padding: 3px 8px; border-radius: 10px; margin: 2px; color: #8B4513; background: rgba(0,200,81,0.2); border: 1px solid rgba(0,200,81,0.2); font-size: 11px; }
        .link-tile { display: flex; align-items: center; justify-content: space-between; background: rgba(255,255,255,0.08); border-radius: 10px; padding: 16px 20px; margin-bottom: 12px; border: 1px solid rgba(139,69,19,0.3); color: #FFD699; cursor: pointer; transition: background 0.2s; }
        .link-tile span { color: #FFD699; font-weight: 600; }
        .link-tile:hover { background: rgba(255,255,255,0.15); }
        a.link-tile { text-decoration: none; }
        pre.inline { background: rgba(0,0,0,0.65); padding: 10px; border-radius: 8px; color: #9ae6b4; }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navigation.php'; ?>
<section class="staff-information">
    <div class="container page-bgc">
        <div class="row">
            <div class="col-sm-12">
                <div class="title-box">
                    <p>Welcome, <?php echo htmlspecialchars($_SESSION['wds_admin_user']); ?></p>
                    <h2 class="title mt0" style="color:#8B4513;">Operations Control Room</h2>
                    <p style="color:#DDD; max-width:720px;">Everything our co-op team needs: current passwords, infrastructure baselines, runbooks, tool downloads, and wiki procedures.</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-8">
                <div class="staff-card">
                    <h3><i class="ion-key"></i> Critical Credentials</h3>
                    <?php if (!empty($passwordEntries)): ?>
                        <div class="table-responsive">
                            <table class="table staff-credentials-table">
                                <thead>
                                    <tr>
                                        <th>Service</th>
                                        <th>Username</th>
                                        <th>Password</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($passwordEntries as $entry): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($entry['service']); ?></strong></td>
                                            <td><code><?php echo htmlspecialchars($entry['username']); ?></code></td>
                                            <td><code><?php echo htmlspecialchars($entry['password']); ?></code></td>
                                            <td><?php echo htmlspecialchars($entry['note']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p style="margin-bottom: 0;">No credentials found. Add entries to <code>content/staff-passwords.txt</code> using <code>service username password notes</code>.</p>
                    <?php endif; ?>
                    <p style="color:#fcd34d; font-size:12px;">Need to update credentials? Edit <code>content/staff-passwords.txt</code> and commit. Run <code>ops-tools/scripts/check_servers.sh --password &lt;NewPass!&gt;</code> on <strong>core</strong> to rotate server passwords.</p>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="staff-card" style="background:rgba(0,0,0,0.65);">
                    <h3><i class="ion-flash"></i> Quick Links</h3>
                    <a href="staff/server-status.php" class="link-tile" style="text-decoration:none;"><span>Server Fleet Status</span><i class="ion-ios-pulse" style="color:#FFD699;"></i></a>
                    <a href="staff/operations.php" class="link-tile" style="text-decoration:none;"><span>Infrastructure / Install Guides</span><i class="ion-ios-arrow-right" style="color:#FFD699;"></i></a>
                    <a href="staff/tools.php" class="link-tile" style="text-decoration:none;"><span>Toolbox & Downloads</span><i class="ion-ios-arrow-right" style="color:#FFD699;"></i></a>
                    <a href="staff/wiki/index.php" class="link-tile" style="text-decoration:none;"><span>Processes Wiki</span><i class="ion-ios-arrow-right" style="color:#FFD699;"></i></a>
                    <a href="ops-tools/" target="_blank" class="link-tile" style="text-decoration:none;"><span>Raw Tools Folder (GitHub)</span><i class="ion-social-github" style="color:#FFD699;"></i></a>
                    <a href="content/docs/gsp/install-ubuntu-panel.md" target="_blank" class="link-tile" style="text-decoration:none;"><span>Migration Tracker</span><i class="ion-document" style="color:#FFD699;"></i></a>
                </div>
                <div class="staff-card">
                    <h3><i class="ion-shuffle"></i> Password Rotation</h3>
                    <p style="color:#DDD;">Manual process only—no automation runs without us.</p>
                    <ol style="color:#CCC; padding-left:20px;">
                        <li>SSH to <code>gameserver@core.iaregamer.com -p 12322</code>.</li>
                        <li>Run <code>cd /home/gameserver/tools/scripts</code>.</li>
                        <li>Execute <code>./check_servers.sh --password "NewSuperSecret!"</code>.<br>Script updates Linux + MySQL creds on every host in <code>servers.txt</code>.</li>
                        <li>Update <code>content/staff-passwords.txt</code> with the new values and commit.</li>
                    </ol>
                    <p style="color:#fbbf24; font-size:12px;">Need per-host overrides? Use <code>ops-tools/scripts/xfer.sh</code> for ad-hoc file pushes.</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="staff-card">
                    <h3><i class="ion-network"></i> Core Servers</h3>
                    <div class="table-responsive">
                        <table class="table" style="color:#E5E7EB;">
                            <thead><tr><th>Hostname</th><th>Role</th><th>SSH / Console</th></tr></thead>
                            <tbody>
                                <?php foreach ($coreServers as $srv): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($srv['hostname']); ?></td>
                                        <td><?php echo htmlspecialchars($srv['role']); ?></td>
                                        <td><code><?php echo htmlspecialchars($srv['ssh']); ?></code></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($otherServers)): ?>
        <div class="row">
            <div class="col-sm-12">
                <div class="staff-card">
                    <h3><i class="ion-ios-cloud"></i> Other Servers</h3>
                    <div class="table-responsive">
                        <table class="table" style="color:#E5E7EB;">
                            <thead><tr><th>Hostname</th><th>Role</th><th>SSH / Console</th></tr></thead>
                            <tbody>
                                <?php foreach ($otherServers as $srv): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($srv['hostname']); ?></td>
                                        <td><?php echo htmlspecialchars($srv['role']); ?></td>
                                        <td><code><?php echo htmlspecialchars($srv['ssh']); ?></code></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <p style="color:#94a3b8; font-size:13px;">Need to add a node? Update <code>content/staff-credentials.json</code> and commit changes.</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-sm-12">
                <div class="staff-card">
                    <h3><i class="ion-briefcase"></i> Tool Summary</h3>
                    <div class="table-responsive">
                        <table class="table" style="color:#E5E7EB;">
                            <thead><tr><th>Tool</th><th>Description</th><th>Command</th><th>Notes</th></tr></thead>
                            <tbody>
                                <?php foreach ($toolCatalog as $tool): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($tool['name']); ?></strong><br><small><?php echo htmlspecialchars($tool['path']); ?></small></td>
                                        <td><?php echo htmlspecialchars($tool['summary']); ?></td>
                                        <td><code><?php echo htmlspecialchars($tool['usage']); ?></code></td>
                                        <td><?php echo htmlspecialchars($tool['notes']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <p style="color:#c084fc; font-size:13px;">Full instructions + downloads live on the <a href="staff/tools.php" style="color:#c084fc; text-decoration:underline;">Toolbox page</a>.</p>
                </div>
            </div>
        </div>

    </div>
</section>
<?php include 'includes/footer.php'; ?>
<script src="assets/js/jquery-1.12.3.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/jquery.magnific-popup.min.js"></script>
<script src="assets/js/owl.carousel.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>
