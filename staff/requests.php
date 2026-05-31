<?php
/**
 * Staff view of submitted client requests.
 * TODO: Add filtering, status updates, and assignment when moving to database.
 */
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';

portalRequireStaff();

$requests = portalLoadRequests();
// Newest first
usort($requests, function($a, $b) {
    return strcmp($b['created_at'] ?? '', $a['created_at'] ?? '');
});

$current_page = 'staff-portal';
$header_class = 'inner-header';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../assets/images/RL-icon.png">
    <title>Client Requests | Staff Portal | Runlevel Systems</title>
    <link href="../assets/css/coreloop.css" rel="stylesheet">
    <style>
        .portal-wrap { padding: 40px 0 80px; }
        .portal-back { color: #36f3ff; font-size: 0.9rem; margin-bottom: 18px; display: inline-block; }
        .portal-back:hover { color: #ffc600; }
        .req-card { background: #0c1729; border: 1px solid rgba(54,243,255,0.18); border-radius: 10px; padding: 22px; margin-bottom: 18px; }
        .req-card h3 { color: #ffc600; margin-top: 0; margin-bottom: 8px; font-size: 1.1rem; }
        .req-meta { color: #5a7a9e; font-size: 0.85rem; margin-bottom: 12px; }
        .req-badge { display: inline-block; padding: 2px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 700; background: rgba(54,243,255,0.15); color: #36f3ff; margin-left: 8px; }
        .req-badge.new { background: rgba(255,198,0,0.15); color: #ffc600; }
        .req-field { margin-bottom: 8px; }
        .req-field strong { color: #a8bedc; }
        .req-field span { color: #c7d7e8; }
        .empty-state { text-align: center; padding: 60px 20px; color: #5a7a9e; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>

<section class="portal-wrap">
    <div class="container">
        <a href="/staff/dashboard.php" class="portal-back">← Back to Dashboard</a>
        <h1 style="color:#ffc600; margin-bottom: 24px;">📥 Client Requests</h1>

        <?php if (empty($requests)): ?>
            <div class="empty-state">
                <div style="font-size:3rem; margin-bottom:12px;">📭</div>
                <p>No client requests yet.</p>
            </div>
        <?php else: ?>
            <?php foreach ($requests as $req): ?>
                <div class="req-card">
                    <h3>
                        <?php echo pe($req['project_title'] ?? 'Untitled Request'); ?>
                        <span class="req-badge <?php echo ($req['status'] ?? '') === 'new' ? 'new' : ''; ?>">
                            <?php echo pe(ucfirst($req['status'] ?? 'new')); ?>
                        </span>
                    </h3>
                    <div class="req-meta">
                        Submitted <?php echo pe($req['created_at'] ?? '—'); ?>
                        &nbsp;·&nbsp; Client: <?php echo pe($req['client_username'] ?? 'Unknown'); ?>
                    </div>
                    <div class="req-field"><strong>Type:</strong> <span><?php echo pe($req['request_type'] ?? '—'); ?></span></div>
                    <div class="req-field"><strong>Budget:</strong> <span><?php echo pe($req['budget_range'] ?? '—'); ?></span></div>
                    <div class="req-field"><strong>Timeline:</strong> <span><?php echo pe($req['timeline'] ?? '—'); ?></span></div>
                    <div class="req-field"><strong>Contact:</strong> <span><?php echo pe($req['contact_method'] ?? '—'); ?></span></div>
                    <?php if (!empty($req['repo_link'])): ?>
                        <div class="req-field"><strong>Link:</strong> <span><a href="<?php echo pe($req['repo_link']); ?>" target="_blank" rel="noopener noreferrer"><?php echo pe($req['repo_link']); ?></a></span></div>
                    <?php endif; ?>
                    <div class="req-field" style="margin-top:12px;"><strong>Description:</strong><br>
                        <span style="white-space:pre-wrap; display:block; margin-top:4px; background:#09111d; border-radius:6px; padding:10px;"><?php echo pe($req['description'] ?? '—'); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="../assets/js/jquery-1.12.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>
