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
    <title>Level X | Wiki - <?php echo htmlspecialchars($requested); ?></title>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/main.css" rel="stylesheet">
    <link href="../../assets/css/wds-unified.css" rel="stylesheet">
    <style>
        .staff-login .page-bgc {
            background-color: #E8E4D8 !important;
        }

        /* Override rust gradient wiki content from readability-improvements.css */
        .staff-login .wiki-content {
            background: #D4CFC0 !important;
            border-radius: 12px !important;
            padding: 25px;
            color: #1a1a1a !important;
            box-shadow: 0 6px 16px rgba(0,0,0,0.18) !important;
        }

        .staff-login .wiki-content :is(p, li, span, td, th, label, small, strong, em, ul, ol) {
            color: #1a1a1a !important;
        }

        .staff-login .wiki-content h1,
        .staff-login .wiki-content h2,
        .staff-login .wiki-content h3 {
            color: #8B4513 !important;
        }

        .staff-login .wiki-content pre {
            background: #111827 !important;
            padding: 12px;
            border-radius: 8px;
            color: #E5E7EB !important;
        }

        .staff-login .wiki-content a {
            text-decoration: underline;
            color: #8B4513 !important;
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
                    <p>Level X Wiki</p>
                    <h2 class="title mt0" style="color:#8B4513;"><?php echo htmlspecialchars(str_replace('-', ' ', $requested)); ?></h2>
                    <p><a href="index.php" style="color:#DDD; text-decoration:underline;">Back to index</a></p>
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
