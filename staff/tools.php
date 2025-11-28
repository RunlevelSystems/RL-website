<?php
session_start();
define('WDS_SYSTEM', true);

require_once '../includes/db-config.php';
require_once '../includes/staff-data.php';
requireAdminLogin();

$toolCatalog = staff_tool_catalog();
$current_page = 'staff-tools';
$header_class = 'login-header inner-header';
$page_subtitle = 'Toolbox + Downloads';
$page_description = 'Download scripts and deploy to servers via wget or manual copy.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WDS | Staff Toolbox</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/ionicons.css" rel="stylesheet">
    <link href="../assets/css/main.css" rel="stylesheet">
    <link href="../assets/css/readability-improvements.css" rel="stylesheet">
    <style>
        .tool-card { background: rgba(0,0,0,0.55); border: 1px solid rgba(139,69,19,0.3); border-radius: 12px; padding: 25px; margin-bottom: 25px; box-shadow: 0 6px 18px rgba(0,0,0,0.35); }
        .tool-card h3 { margin-top: 0; color: #FDE68A; }
        .tool-meta { color:#9CA3AF; font-size:13px; }
        .tool-meta code { background: rgba(15,23,42,0.7); padding: 2px 6px; border-radius: 6px; }
        .tool-card a.btn-download { margin-top: 15px; display: inline-block; background: #8B4513; color: #F1E3C8; padding: 8px 18px; border-radius: 6px; text-decoration: none; margin-right: 10px; }
        .tool-card a.btn-download:hover { background: #A0522D; }
        .dir-notice { background: rgba(255,255,255,0.08); padding: 18px; border-radius: 10px; border-left: 4px solid #8B4513; color:#E5E7EB; }
        .download-all-box { background: linear-gradient(135deg, rgba(139,69,19,0.3) 0%, rgba(0,0,0,0.5) 100%); border: 2px solid #8B4513; border-radius: 14px; padding: 30px; margin-bottom: 30px; text-align: center; }
        .download-all-box h3 { color: #FFD699; margin-top: 0; }
        .download-all-box .btn-download-all { background: #8B4513; color: #F1E3C8; padding: 15px 40px; border-radius: 8px; text-decoration: none; font-size: 1.1rem; display: inline-block; margin-top: 10px; border: none; cursor: pointer; }
        .download-all-box .btn-download-all:hover { background: #A0522D; }
        .wget-command { background: rgba(0,0,0,0.6); padding: 15px; border-radius: 8px; font-family: monospace; color: #9AE6B4; margin-top: 15px; word-break: break-all; }
        .wget-command code { background: transparent; padding: 0; }
    </style>
</head>
<body>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navigation.php'; ?>
<section class="staff-login">
    <div class="container page-bgc">
        <div class="row">
            <div class="col-sm-12">
                <div class="title-box">
                    <p>Staff Toolbox</p>
                    <h2 class="title mt0" style="color:#8B4513;">Operations Scripts & Docs</h2>
                </div>
            </div>
        </div>

        <!-- Download All Tools Section -->
        <div class="row">
            <div class="col-sm-12">
                <div class="download-all-box">
                    <h3><i class="ion-android-download"></i> Download All Tools</h3>
                    <p style="color:#E5E7EB;">Get the complete toolkit in a single ZIP file for easy deployment.</p>
                    <a href="../ops-tools/wds-tools.zip" class="btn-download-all" download><i class="ion-ios-cloud-download"></i> Download wds-tools.zip</a>
                    <div class="wget-command">
                        <strong style="color:#FDE68A;">Quick Deploy via wget:</strong><br>
                        <code>cd /tmp && wget -q https://core.iaregamer.com/ops-tools/wds-tools.zip && unzip -o wds-tools.zip -d /home/gameserver/tools/ && rm wds-tools.zip</code>
                    </div>
                    <p style="color:#9CA3AF; font-size:0.9rem; margin-top:15px;">
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
        <div class="row">
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
                    <ol style="color:#DDD; padding-left:20px;">
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
