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
$header_class = 'login-header inner-header';
$page_subtitle = 'Wiki Viewer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WDS | Wiki - <?php echo htmlspecialchars($requested); ?></title>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/main.css" rel="stylesheet">
    <link href="../../assets/css/wds-unified.css" rel="stylesheet">
    <link href="../../assets/css/readability-improvements.css" rel="stylesheet">
    <style>
        .wiki-content { background: rgba(0,0,0,0.55); border-radius: 12px; padding: 25px; color:#E5E7EB; }
        .wiki-content h1,.wiki-content h2,.wiki-content h3 { color:#FDE68A; }
        .wiki-content pre { background: rgba(15,23,42,0.85); padding:12px; border-radius:8px; }
        .wiki-content a { color:#93C5FD; text-decoration:underline; }
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
                    <p>WDS Wiki</p>
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
