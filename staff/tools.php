<?php
session_start();
define('WDS_SYSTEM', true);

require_once '../includes/db-config.php';
require_once '../includes/staff-data.php';
requireAdminLogin();

$toolCatalog = staff_tool_catalog();
$current_page = 'staff-tools';
$header_class = 'projects-header inner-header';
$page_subtitle = 'Toolbox + Downloads';
$page_description = 'Download scripts and deploy to servers via wget or manual copy.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/assets/images/RL-icon.png">
    <title>Runlevel Systems | Staff Toolbox</title>
    <link href="../assets/css/runlevel.css" rel="stylesheet">
    <style>
        .staff-login .download-all-box {
            background: #0d1a33 !important;
            border: 2px solid rgba(54,243,255,0.25) !important;
            border-radius: 14px !important;
            padding: 30px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 6px 16px rgba(0,0,0,0.18) !important;
            color: #eaf3ff !important;
        }

        .staff-login .download-all-box h3 {
            margin-top: 0;
            color: #ffd166 !important;
        }

        .staff-login .download-all-box p {
            color: #a8bedc !important;
        }

        .staff-login .download-all-box .btn-download-all {
            background: linear-gradient(135deg, #1f4ca3, #2d7fff) !important;
            border: 1px solid rgba(54,243,255,0.4) !important;
            color: #f4fbff !important;
        }

        .staff-login .tool-card {
            background: #0d1a33 !important;
            border: 1px solid rgba(54,243,255,0.25) !important;
            border-radius: 12px !important;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.18) !important;
            color: #eaf3ff !important;
        }

        .staff-login .tool-card h3 {
            margin-top: 0;
            color: #ffd166 !important;
        }

        .staff-login .tool-card :is(p, li, span, td, th, label, small, strong, em, ul, ol) {
            color: #a8bedc !important;
        }

        .staff-login .tool-card a:not(.btn-download) {
            color: #ffd166 !important;
        }

        .tool-card a.btn-download {
            color: #f4fbff !important;
        }

        .tool-meta code {
            background: #132b57;
            padding: 2px 6px;
            border-radius: 6px;
            color: #36f3ff;
        }

        .tool-card a.btn-download {
            margin-top: 15px;
            display: inline-block;
        }

        .staff-login .dir-notice {
            background: #0d1a33 !important;
            padding: 18px;
            border-radius: 10px;
            border-left: 4px solid #ffd166 !important;
            color: #a8bedc !important;
        }

        .staff-login .dir-notice :is(p, li, span, strong, code) {
            color: #a8bedc !important;
        }

        .wget-command {
            background: #111827 !important;
            padding: 15px;
            border-radius: 8px;
            font-family: monospace;
            color: #eaf3ff !important;
            margin-top: 15px;
            word-break: break-all;
        }

        .wget-command code {
            background: transparent;
            padding: 0;
        }

        /* Align tool cards in a flexible grid so blocks line up cleanly */
        .tool-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 24px;
        }

        .tool-grid > [class*="col-sm-6"] {
            display: flex;
        }

        .tool-grid .tool-card {
            flex: 1 1 auto;
            height: 100%;
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
                    <p>Staff Toolbox</p>
                    <h2 class="title mt0" style="color:#ffd166;">Operations Scripts & Docs</h2>
                </div>
            </div>
        </div>

        <!-- Download All Tools Section -->
        <div class="row">
            <div class="col-sm-12">
                <div class="download-all-box">
                    <h3><i class="ion-android-download"></i> Download All Tools</h3>
                    <p style="color:#eaf3ff;">Get the complete toolkit in a single ZIP file for easy deployment.</p>
                    <a href="../ops-tools/wds-tools.zip" class="btn-download-all" download><i class="ion-ios-cloud-download"></i> Download wds-tools.zip</a>
                    <div class="wget-command">
                        <strong style="color:#FDE68A;">Quick Deploy via wget:</strong><br>
                        <code>cd /tmp && wget -q https://core.iaregamer.com/ops-tools/wds-tools.zip && unzip -o wds-tools.zip -d /home/gameserver/tools/ && rm wds-tools.zip</code>
                    </div>
                    <p style="color:#7894b9; font-size:0.9rem; margin-top:15px;">
                        For DR failover, change <code>core.iaregamer.com</code> to <code>core-dr.iaregamer.com</code>. 
                        Always verify the source is trusted before deploying scripts.
                    </p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="dir-notice">
                    <p><strong>Repo location:</strong> <code>/ops-tools</code> (bundled with this website). Clone/update from GitHub or download individual files below.</p>
                    <p style="margin:0;">Always run scripts from <code>/home/gameserver/tools</code> on the appropriate host to inherit the expected paths (<code>servers.txt</code>, etc.).</p>
                </div>
            </div>
        </div>
        <div class="row tool-grid">
            <?php foreach ($toolCatalog as $tool): ?>
                <div class="col-sm-6">
                    <div class="tool-card">
                        <h3><?php echo htmlspecialchars($tool['name']); ?></h3>
                        <p><?php echo htmlspecialchars($tool['summary']); ?></p>
                        <p class="tool-meta"><strong>Usage:</strong> <code><?php echo htmlspecialchars($tool['usage']); ?></code></p>
                        <p class="tool-meta"><strong>Notes:</strong> <?php echo htmlspecialchars($tool['notes']); ?></p>
                        <a class="btn-download" href="../<?php echo htmlspecialchars($tool['path']); ?>" download><i class="ion-android-download"></i> Download</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="tool-card">
                    <h3><i class="ion-document-text"></i> Password Rotation Reference</h3>
                    <ol style="color:#a8bedc; padding-left:20px;">
                        <li>Rotate Linux + MySQL creds with <code>check_servers.sh --password "NewPass!"</code> on core.</li>
                        <li>Re-seed database grants via <code>setup_mysql_users.sh</code> on the MySQL host.</li>
                        <li>Update <code>tools/servers.txt</code> if hosts were added/removed.</li>
                        <li>Document the new credentials on <a href="../staff-info.php">Staff Info</a> (git + push).</li>
                    </ol>
                    <p style="color:#F87171;">No cron jobs call these scripts. They only run when a staff member executes them.</p>
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
