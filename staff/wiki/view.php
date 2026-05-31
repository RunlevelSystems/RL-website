<?php
session_start();
define('WDS_SYSTEM', true);

require_once '../../includes/db-config.php';
require_once '../../includes/staff-data.php';
require_once '../../includes/markdown.php';
requireAdminLogin();

$docsDir = __DIR__ . '/docs';
$requested = isset($_GET['doc']) ? preg_replace('/[^A-Za-z0-9-_]/', '', $_GET['doc']) : 'Home';
$file = $docsDir . '/' . $requested . '.md';
if (!file_exists($file)) {
    $requested = 'Home';
    $file = $docsDir . '/Home.md';
}
$content = file_get_contents($file);
$current_page = 'staff-wiki';
$header_class = 'projects-header inner-header';
$page_subtitle = 'Wiki Viewer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/assets/images/RL-icon.png">
    <title>Runlevel Systems | Wiki - <?php echo htmlspecialchars($requested); ?></title>
    <link href="../../assets/css/runlevel.css" rel="stylesheet">
    <style>
        .staff-login .page-bgc {
            background-color: #071228 !important;
        }

        /* Override rust gradient wiki content */
        .staff-login .wiki-content {
            background: #0d1a33 !important;
            border-radius: 12px !important;
            padding: 25px;
            color: #eaf3ff !important;
            box-shadow: 0 6px 16px rgba(0,0,0,0.18) !important;
        }

        .staff-login .wiki-content :is(p, li, span, td, th, label, small, strong, em, ul, ol) {
            color: #a8bedc !important;
        }

        .staff-login .wiki-content h1,
        .staff-login .wiki-content h2,
        .staff-login .wiki-content h3 {
            color: #ffd166 !important;
        }

        .staff-login .wiki-content pre {
            background: #111827 !important;
            padding: 12px;
            border-radius: 8px;
            color: #eaf3ff !important;
        }

        .staff-login .wiki-content a {
            text-decoration: underline;
            color: #ffd166 !important;
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
                    <p>Runlevel Systems Wiki</p>
                    <h2 class="title mt0" style="color:#ffd166;"><?php echo htmlspecialchars(str_replace('-', ' ', $requested)); ?></h2>
                    <p><a href="index.php" style="color:#a8bedc; text-decoration:underline;">Back to index</a></p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="wiki-content">
                    <?php echo render_markdown($content); ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include '../../includes/footer.php'; ?>
<script src="../../assets/js/jquery-1.12.3.min.js"></script>
<script src="../../assets/js/bootstrap.min.js"></script>
</body>
</html>
