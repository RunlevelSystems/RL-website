<?php
session_start();
define('WDS_SYSTEM', true);

require_once '../../includes/db-config.php';
require_once '../../includes/staff-data.php';
require_once '../../includes/markdown.php';
requireAdminLogin();

$docsDir = __DIR__ . '/docs';
$files = array_values(array_filter(scandir($docsDir), function ($file) {
    return substr($file, -3) === '.md';
}));
$current_page = 'staff-wiki';
$header_class = 'projects-header inner-header';
$page_subtitle = 'Procedures & Process';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/assets/images/icon.png">
    <title>Core Loop | Staff Wiki</title>
    <link href="../../assets/css/coreloop.css" rel="stylesheet">
    <style>
        .staff-login .page-bgc {
            background-color: #071228 !important;
        }

        /* Override rust gradient cards */
        .staff-login .wiki-card {
            background: #0d1a33 !important;
            border-radius: 10px !important;
            border: 1px solid rgba(54,243,255,0.25) !important;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.18) !important;
            color: #eaf3ff !important;
        }

        .staff-login .wiki-card a {
            font-weight: 600;
            color: #ffd166 !important;
        }

        .staff-login .wiki-card p {
            color: #a8bedc !important;
        }
    </style>
</head>
<body>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navigation.php'; ?>
<section class="staff-login">
    <div class="container page-bgc">
        <div class="row">
            <div class="col-sm-12">
                <div class="title-box">
                    <p>Core Loop Team Wiki</p>
                    <h2 class="title mt0" style="color:#ffd166;">Procedures & Process</h2>
                    <p style="color:#a8bedc;">This is a live copy of the legacy Core Loop Team wiki so we no longer need a separate site.</p>
                </div>
            </div>
        </div>
        <div class="row">
            <?php foreach ($files as $file): $base = basename($file, '.md'); ?>
                <div class="col-sm-6">
                    <div class="wiki-card">
                        <a href="view.php?doc=<?php echo urlencode($base); ?>">
                            <i class="ion-document-text"></i> <?php echo htmlspecialchars(str_replace('-', ' ', $base)); ?>
                        </a>
                        <p style="color:#7894b9; font-size:13px; margin:5px 0 0;">Updated on <?php echo date('Y-m-d', filemtime($docsDir . '/' . $file)); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php include '../../includes/footer.php'; ?>
<script src="../../assets/js/jquery-1.12.3.min.js"></script>
<script src="../../assets/js/bootstrap.min.js"></script>
</body>
</html>
