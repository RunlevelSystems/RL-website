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
$requestIds = array_map(function ($r) { return portalGetRequestDisplayId((array)$r); }, $requests);

$proposals = array_values(array_filter(portalLoadProposals(), function ($p) use ($requestIds, $username) {
    return in_array((string)($p['request_id'] ?? ''), $requestIds, true)
        || (string)($p['client_username'] ?? '') === $username;
}));

usort($proposals, function ($a, $b) {
    return strcmp((string)($b['updated_at'] ?? $b['created_at'] ?? ''), (string)($a['updated_at'] ?? $a['created_at'] ?? ''));
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
    <title>My Proposals | Client Portal | Runlevel Systems</title>
    <link href="../assets/css/coreloop.css" rel="stylesheet">
    <style>
        .portal-wrap{padding:30px 0 70px;}
        .card{background:#0c1729;border:1px solid rgba(54,243,255,.18);border-radius:10px;padding:16px;}
        .item{background:#09111d;border:1px solid rgba(54,243,255,.14);border-radius:8px;padding:12px;margin-bottom:10px;}
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>
<section class="portal-wrap">
<div class="container">
    <a href="/dashboard.php" style="color:#36f3ff;font-size:.84rem;">← Back to Dashboard</a>
    <div class="card" style="margin-top:10px;">
        <h2 style="margin:0 0 12px;color:#ffc600;font-size:1.1rem;">My Proposals</h2>
        <?php if (empty($proposals)): ?>
            <p style="color:#7a9ac0;">No proposals available yet.</p>
        <?php else: ?>
            <?php foreach ($proposals as $proposal): ?>
                <div class="item">
                    <div style="display:flex;justify-content:space-between;gap:8px;flex-wrap:wrap;">
                        <div style="color:#ffc600;font-family:monospace;font-weight:700;"><?php echo pe($proposal['proposal_id'] ?? 'Proposal'); ?></div>
                        <div style="color:#a8bedc;font-size:.8rem;"><?php echo pe(ucfirst((string)($proposal['status'] ?? 'draft'))); ?></div>
                    </div>
                    <div style="color:#eaf3ff;font-size:.9rem;margin-top:4px;"><?php echo pe($proposal['project_title'] ?? 'Project Proposal'); ?></div>
                    <div style="color:#7a9ac0;font-size:.78rem;margin-top:4px;">Request ID: <?php echo pe($proposal['request_id'] ?? ''); ?></div>
                    <a style="display:inline-block;margin-top:6px;color:#36f3ff;font-size:.82rem;" href="/client/proposal.php?proposal_id=<?php echo urlencode((string)($proposal['proposal_id'] ?? '')); ?>">View proposal</a>
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
