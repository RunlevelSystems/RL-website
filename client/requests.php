<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';

portalRequireLogin();
if (portalGetRole() !== 'client') {
    header('Location: /dashboard.php');
    exit;
}

$user = portalGetUser();
$username = (string)($user['username'] ?? '');
$requests = array_values(array_filter(portalLoadProjectRequests(), function ($r) use ($username) {
    return (string)($r['client_username'] ?? '') === $username;
}));

usort($requests, function ($a, $b) {
    return strcmp((string)($b['created_at'] ?? ''), (string)($a['created_at'] ?? ''));
});

$current_page = 'client-portal';
$header_class = 'inner-header';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../assets/images/RL-icon.png">
    <title>My Projects | Client Portal | Runlevel Systems</title>
    <link href="../assets/css/coreloop.css" rel="stylesheet">
    <style>
        .portal-wrap { padding: 30px 0 70px; }
        .portal-card { background:#0c1729;border:1px solid rgba(54,243,255,.18);border-radius:10px;padding:16px; }
        .req-item { background:#09111d;border:1px solid rgba(54,243,255,.15);border-radius:8px;padding:12px;margin-bottom:10px; }
        .req-head { display:flex;justify-content:space-between;gap:8px;flex-wrap:wrap; }
        .req-id { color:#ffc600;font-family:monospace;font-size:.9rem;font-weight:700; }
        .meta { color:#7a9ac0;font-size:.78rem;margin-top:4px; }
        .links a { color:#36f3ff;font-size:.8rem;margin-right:12px; }
        .btn { background:#ffc600;color:#08111f;border:none;border-radius:6px;padding:8px 12px;font-weight:700;font-size:.82rem;text-decoration:none; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>
<section class="portal-wrap">
<div class="container">
    <a href="/dashboard.php" style="color:#36f3ff;font-size:.84rem;">← Back to Dashboard</a>
    <div class="portal-card" style="margin-top:10px;">
        <div style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;align-items:center;margin-bottom:12px;">
            <h2 style="margin:0;color:#ffc600;font-size:1.1rem;">My Projects</h2>
            <a href="/estimate.php" class="btn">Submit New Project Request</a>
        </div>

        <?php if (empty($requests)): ?>
            <p style="color:#7a9ac0;">No project requests yet.</p>
        <?php else: ?>
            <?php foreach ($requests as $req): ?>
                <?php
                    $rid = portalGetRequestDisplayId((array)$req);
                    $workspaceUrl = '/project.php?id=' . urlencode($rid);
                ?>
                <div class="req-item" style="cursor:pointer;" onclick="window.location='<?php echo pe($workspaceUrl); ?>'">
                    <div class="req-head">
                        <div>
                            <div class="req-id"><?php echo pe($rid); ?></div>
                            <div style="color:#eaf3ff;font-size:.9rem;"><?php echo pe($req['project_type'] ?? 'Project Request'); ?></div>
                        </div>
                        <div style="color:#a8bedc;font-size:.8rem;"><?php echo pe(ucfirst((string)($req['status'] ?? 'new'))); ?></div>
                    </div>
                    <div class="meta">Submitted <?php echo pe(date('M j, Y', strtotime((string)($req['created_at'] ?? 'now')))); ?></div>
                    <div class="links" style="margin-top:6px;">
                        <a href="<?php echo pe($workspaceUrl); ?>" onclick="event.stopPropagation()">Open Project Workspace</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="../assets/js/jquery-1.12.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>
