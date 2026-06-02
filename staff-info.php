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
$header_class = 'projects-header inner-header';
$page_subtitle = 'Staff Control Room';
$page_description = 'Centralize every password, host, tool and operating procedure in one place.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/assets/images/RL-icon.png">
    <title>Runlevel Systems | Staff Control Room</title>
    <!-- CSS -->
    <link href="assets/css/coreloop.css?v=clean-20260602" rel="stylesheet">
    <style>
        .staff-grid { display: flex; flex-wrap: wrap; gap: 20px; }

        .staff-credentials-table th {
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.08em;
        }

        .staff-credentials-table td {
            border-color: rgba(54,243,255,0.18);
        }

        /* Code and inline pre blocks: dark background for contrast */
        .staff-information .staff-card code,
        pre.inline {
            background: #111827 !important;
            padding: 10px;
            border-radius: 8px;
            color: #eaf3ff !important;
        }

        .tag {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 10px;
            margin: 2px;
            color: #ffd166;
            background: rgba(54,243,255,0.2);
            border: 1px solid rgba(54,243,255,0.2);
            font-size: 11px;
        }

        .staff-information .link-tile {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #0f2142 !important;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 12px;
            border: 1px solid rgba(54,243,255,0.25) !important;
            color: #eaf3ff !important;
            cursor: pointer;
            transition: background 0.2s;
        }

        .staff-information .link-tile span {
            color: #eaf3ff !important;
            font-weight: 600;
        }

        .staff-information .link-tile:hover {
            background: #132b57 !important;
        }

        a.link-tile { text-decoration: none; }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navigation.php'; ?>
<section class="staff-information">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="title-box">
                    <p>Welcome, <?php echo htmlspecialchars($_SESSION['wds_admin_user']); ?></p>
                    <h2 class="title mt0" style="color:#ffd166;">Operations Control Room</h2>
                    <p style="color:#a8bedc; max-width:720px;">Everything our co-op team needs: current passwords, infrastructure baselines, runbooks, tool downloads, and wiki procedures.</p>
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
                <div class="staff-card">
                    <h3><i class="ion-flash"></i> Quick Links</h3>
                    <a href="staff/server-status.php" class="link-tile" style="text-decoration:none;"><span>Server Fleet Status</span><i class="ion-ios-pulse" style="color:#FFD699;"></i></a>
                    <a href="staff/operations.php" class="link-tile" style="text-decoration:none;"><span>Infrastructure / Install Guides</span><i class="ion-ios-arrow-right" style="color:#FFD699;"></i></a>
                    <a href="staff/tools.php" class="link-tile" style="text-decoration:none;"><span>Toolbox & Downloads</span><i class="ion-ios-arrow-right" style="color:#FFD699;"></i></a>
                    <a href="staff/wiki/index.php" class="link-tile" style="text-decoration:none;"><span>Processes Wiki</span><i class="ion-ios-arrow-right" style="color:#FFD699;"></i></a>
                    <a href="staff/projects-links.php" class="link-tile" style="text-decoration:none;"><span>Project GitHub Links</span><i class="ion-social-github" style="color:#FFD699;"></i></a>
                    <a href="staff/client-portal.php" class="link-tile" style="text-decoration:none;"><span>Client Portal Admin</span><i class="ion-folder" style="color:#FFD699;"></i></a>
                    <a href="ops-tools/" target="_blank" class="link-tile" style="text-decoration:none;"><span>Raw Tools Folder (GitHub)</span><i class="ion-social-github" style="color:#FFD699;"></i></a>
                    <a href="content/docs/gsp/install-ubuntu-panel.md" target="_blank" class="link-tile" style="text-decoration:none;"><span>Migration Tracker</span><i class="ion-document" style="color:#FFD699;"></i></a>
                </div>
                <div class="staff-card">
                    <h3><i class="ion-android-cart"></i> External Storefront Accounts</h3>
                    <a href="https://play.google.com/console" target="_blank" rel="noopener noreferrer" class="link-tile" style="text-decoration:none;"><span>Google Play Console</span><i class="ion-android-open" style="color:#FFD699;"></i></a>
                    <a href="https://partner.steampowered.com/" target="_blank" rel="noopener noreferrer" class="link-tile" style="text-decoration:none;"><span>Steamworks Partner Portal</span><i class="ion-android-open" style="color:#FFD699;"></i></a>
                    <p style="color:#a8bedc; margin-top:15px;">Use <code>team@worlddomination.dev</code> for invites and access requests; it already satisfies Google Play and Steam contact requirements.</p>
                    <ul style="color:#cbd5f5; padding-left:18px; font-size:13px;">
                        <li>Google Play: send an Admin invite from the Console &rarr; Users &amp; permissions page. Ask ops to approve within 24h.</li>
                        <li>Steamworks: create a new partner account invite via Users &rarr; Manage Users. Assign publishing and marketing roles as needed.</li>
                        <li>Expect MFA: add your authenticator before first login; do <strong>not</strong> store backup codes on shared drives.</li>
                    </ul>
                    <p style="color:#fcd34d; font-size:12px;">Need storefront screenshots or listing assets? Ping <strong>#ops-production</strong> in Discord so we upload sanitized copies to the wiki.</p>
                </div>
                <div class="staff-card">
                    <h3><i class="ion-email"></i> Shared Admin Mailbox</h3>
                    <p style="color:#a8bedc;">Use <strong>team@worlddomination.dev</strong> whenever a vendor, registrar, or platform needs a single administrative contact. Everyone on the ops roster can authenticate to clear login challenges and receive 2FA resets.</p>
                    <div class="table-responsive">
                        <table class="table staff-credentials-table">
                            <tbody>
                                <tr>
                                    <th scope="row" style="width:35%;">Username</th>
                                    <td><code>team@worlddomination.dev</code></td>
                                </tr>
                                <tr>
                                    <th scope="row">Password</th>
                                    <td><code>Inc0rrect!</code> &mdash; rotate it in <code>content/staff-passwords.txt</code> and update cPanel when changed.</td>
                                </tr>
                                <tr>
                                    <th scope="row">Incoming IMAP</th>
                                    <td><code>mail.worlddomination.dev</code> &middot; port <code>993</code> &middot; SSL/TLS required.</td>
                                </tr>
                                <tr>
                                    <th scope="row">Incoming POP3</th>
                                    <td><code>mail.worlddomination.dev</code> &middot; port <code>995</code> &middot; SSL/TLS required (IMAP preferred).</td>
                                </tr>
                                <tr>
                                    <th scope="row">Outgoing SMTP</th>
                                    <td><code>mail.worlddomination.dev</code> &middot; port <code>465</code> &middot; SSL/TLS required with authentication.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <ul style="color:#a8bedc; padding-left:18px; margin-bottom:12px;">
                        <li>Keep it on IMAP so read/deleted flags stay synced across devices.</li>
                        <li>Label vendor threads before archiving so the next person knows the context.</li>
                        <li>If you add a new client, use manual setup with the ports above and ensure SMTP auth is ON.</li>
                    </ul>
                    <p style="font-size:12px;color:#fcd34d;">Need screenshots or auto-config files? Download the sanitized guide: <a href="staff/team-mail-client-setup.pdf" target="_blank" style="color:#fcd34d;text-decoration:underline;">team-mail-client-setup.pdf</a>.</p>
                </div>
                <div class="staff-card">
                    <h3><i class="ion-shuffle"></i> Password Rotation</h3>
                    <p style="color:#a8bedc;">Manual process only—no automation runs without us.</p>
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
                        <table class="table" style="color:#eaf3ff;">
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
                        <table class="table" style="color:#eaf3ff;">
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
                        <table class="table" style="color:#eaf3ff;">
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
