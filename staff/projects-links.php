<?php
session_start();
define('WDS_SYSTEM', true);

require_once __DIR__ . '/../includes/db-config.php';
require_once __DIR__ . '/../includes/projects-data.php';
requireAdminLogin();

$allProjects = loadAllProjects();

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated = false;
    foreach ($allProjects as $idx => $project) {
        if (!isset($project['slug'])) {
            continue;
        }
        $slug = $project['slug'];

        // Read more URL
        $fieldRead = 'read_url_' . $slug;
        if (isset($_POST[$fieldRead])) {
            $url = trim($_POST[$fieldRead]);
            if ($url === '') {
                unset($allProjects[$idx]['readMoreUrl']);
            } else {
                $allProjects[$idx]['readMoreUrl'] = $url;
            }
            $updated = true;
        }

        // Discussion URL
        $fieldDisc = 'disc_url_' . $slug;
        if (isset($_POST[$fieldDisc])) {
            $url = trim($_POST[$fieldDisc]);
            if ($url === '') {
                unset($allProjects[$idx]['discussionUrl']);
            } else {
                $allProjects[$idx]['discussionUrl'] = $url;
            }
            $updated = true;
        }

        // Report Issue URL
        $fieldIssue = 'issue_url_' . $slug;
        if (isset($_POST[$fieldIssue])) {
            $url = trim($_POST[$fieldIssue]);
            if ($url === '') {
                unset($allProjects[$idx]['issueUrl']);
            } else {
                $allProjects[$idx]['issueUrl'] = $url;
            }
            $updated = true;
        }
    }
    if ($updated) {
        if (saveAllProjects(array_values($allProjects))) {
            $message = 'Project links updated successfully.';
            $allProjects = loadAllProjects();
        } else {
            $message = 'Failed to save project links. Check file permissions.';
        }
    } else {
        $message = 'No changes detected.';
    }
}

$current_page = 'staff-project-links';
$header_class = 'projects-header inner-header';
$page_subtitle = 'GitHub Links';

function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/assets/images/icon.png">
    <title>Core Loop | Project GitHub Links</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/wds-unified.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@400;500;600;700;800;900&family=Inter:wght@300;400;500;600&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #071228; }
        .staff-card { background:#111827; border-radius:10px; padding:20px; margin-bottom:20px; border:1px solid #374151; }
        .staff-card h3 { color:#eaf3ff; margin-top:0; }
        .project-links-table th, .project-links-table td { vertical-align: middle; }
        .project-links-table th { color:#eaf3ff; border-color:#374151; }
        .project-links-table td { color:#eaf3ff; border-color:#374151; }
        .project-links-table input[type="text"] { width:100%; padding:6px 8px; border-radius:3px; border:1px solid #4b5563; background:#0f172a; color:#eaf3ff; }
        .btn-primary-wds { background:linear-gradient(135deg,#1f4ca3,#2d7fff); border-color:rgba(54,243,255,0.4); color:#f4fbff; }
        .btn-primary-wds:hover { background:linear-gradient(135deg,#2360cc,#36f3ff); border-color:#36f3ff; color:#051225; }
        .alert-info { background:#1f2937; color:#eaf3ff; border-color:#374151; }
        .breadcrumb { background:transparent; padding-left:0; margin-bottom:10px; }
        .breadcrumb > li + li:before { color:#9ca3af; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>
<section class="staff-information">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="title-box">
                    <p>Welcome, <?php echo h($_SESSION['wds_admin_user']); ?></p>
                    <h2 class="title mt0" style="color:#ffd166;">Project GitHub Links</h2>
                    <p style="color:#a8bedc; max-width:720px;">Map each public-facing project to its GitHub or wiki page used by the main Projects list.</p>
                </div>
            </div>
        </div>

        <?php if ($message !== ''): ?>
        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-info"><?php echo h($message); ?></div>
            </div>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-sm-12">
                <div class="staff-card">
                    <h3>Per-Project GitHub / Wiki URLs</h3>
                    <form method="post">
                        <div class="table-responsive">
                            <table class="table project-links-table">
                                <thead>
                                    <tr>
                                        <th style="width:16%;">Project</th>
                                        <th style="width:16%;">Category</th>
                                        <th>Read more URL</th>
                                        <th>Discussion URL</th>
                                        <th>Report Issue URL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($allProjects as $p): ?>
                                    <tr>
                                        <td><strong><?php echo h($p['title'] ?? $p['slug']); ?></strong></td>
                                        <td><?php echo h($p['category'] ?? ''); ?></td>
                                        <td>
                                            <?php
                                                $slug = $p['slug'];
                                                $fieldRead = 'read_url_' . $slug;
                                                $fieldDisc = 'disc_url_' . $slug;
                                                $fieldIssue = 'issue_url_' . $slug;
                                                $defaultRead = 'https://github.com/World-Domination-Software/Projects/wiki';
                                                $defaultDisc = 'https://github.com/World-Domination-Software/Projects/discussions';
                                                $defaultIssue = 'https://github.com/World-Domination-Software/Projects/issues';
                                                $readVal = $p['readMoreUrl'] ?? ($p['githubUrl'] ?? $defaultRead);
                                                $discVal = $p['discussionUrl'] ?? $defaultDisc;
                                                $issueVal = $p['issueUrl'] ?? $defaultIssue;
                                            ?>
                                            <input type="text" name="<?php echo h($fieldRead); ?>" value="<?php echo h($readVal); ?>" placeholder="<?php echo h($defaultRead); ?>">
                                        </td>
                                        <td>
                                            <input type="text" name="<?php echo h($fieldDisc); ?>" value="<?php echo h($discVal); ?>" placeholder="<?php echo h($defaultDisc); ?>">
                                        </td>
                                        <td>
                                            <input type="text" name="<?php echo h($fieldIssue); ?>" value="<?php echo h($issueVal); ?>" placeholder="<?php echo h($defaultIssue); ?>">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <button type="submit" class="btn btn-primary-wds">Save Links</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="../assets/js/jquery-1.12.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>
