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
    <title>WDS | Staff Wiki</title>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/ionicons.css" rel="stylesheet">
    <link href="../../assets/css/main.css" rel="stylesheet">
    <link href="../../assets/css/wds-unified.css" rel="stylesheet">
    <link href="../../assets/css/readability-improvements.css" rel="stylesheet">
    <style>
        .wiki-card { background: rgba(0,0,0,0.55); border-radius: 10px; border: 1px solid rgba(139,69,19,0.3); padding: 20px; margin-bottom: 15px; }
        .wiki-card a { color:#FDE68A; font-weight:600; text-decoration:none; }
        .wiki-card a:hover { text-decoration:underline; }
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
                    <p>WDS Team Wiki</p>
                    <h2 class="title mt0" style="color:#8B4513;">Procedures & Process</h2>
                    <p style="color:#DDD;">This is a live copy of the legacy WDS-Team wiki so we no longer need a separate site.</p>
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
                        <p style="color:#9CA3AF; font-size:13px; margin:5px 0 0;">Updated on <?php echo date('Y-m-d', filemtime($docsDir . '/' . $file)); ?></p>
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
