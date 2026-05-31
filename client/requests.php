<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';

portalRequireClient();
$client = portalGetClientUser();

$allRequests = portalLoadRequests();
$myRequests  = array_values(array_filter($allRequests, function($r) use ($client) {
    return ($r['client_username'] ?? '') === ($client['username'] ?? '');
}));

// Newest first
usort($myRequests, function($a, $b) {
    return strcmp($b['created_at'] ?? '', $a['created_at'] ?? '');
});

$statusLabels = [
    'new'        => ['label' => 'New',         'color' => '#36f3ff'],
    'reviewing'  => ['label' => 'Reviewing',   'color' => '#ffc600'],
    'quoted'     => ['label' => 'Quoted',      'color' => '#a78bfa'],
    'approved'   => ['label' => 'Approved',    'color' => '#22c55e'],
    'in_progress'=> ['label' => 'In Progress', 'color' => '#0a84ff'],
    'completed'  => ['label' => 'Completed',   'color' => '#6ee7b7'],
    'closed'     => ['label' => 'Closed',      'color' => '#5a7a9e'],
];

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
    <title>My Requests | Client Portal | Runlevel Systems</title>
    <link href="../assets/css/coreloop.css" rel="stylesheet">
    <style>
        .portal-wrap { padding: 40px 0 80px; }
        .portal-back { color: #36f3ff; font-size: 0.9rem; margin-bottom: 18px; display: inline-block; }
        .portal-back:hover { color: #ffc600; }
        .portal-card { background: #0c1729; border: 1px solid rgba(54,243,255,0.18); border-radius: 10px; padding: 28px; margin-bottom: 20px; }
        .portal-card h2 { color: #ffc600; margin-top: 0; }
        .req-item { background: #09111d; border: 1px solid rgba(54,243,255,0.15); border-radius: 8px; padding: 18px 20px; margin-bottom: 14px; }
        .req-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; flex-wrap: wrap; margin-bottom: 8px; }
        .req-title { color: #eaf3ff; font-weight: 700; font-size: 1rem; }
        .req-type { font-size: 0.8rem; color: #5a7a9e; margin-top: 2px; }
        .req-status { font-size: 0.8rem; font-weight: 700; padding: 3px 10px; border-radius: 20px; background: rgba(54,243,255,0.1); white-space: nowrap; }
        .req-desc { color: #7a9ac0; font-size: 0.875rem; line-height: 1.5; margin-bottom: 8px; }
        .req-meta { font-size: 0.78rem; color: #3a5a7e; }
        .req-meta span { margin-right: 16px; }
        .empty-state { text-align: center; padding: 40px 20px; color: #5a7a9e; }
        .empty-state .icon { font-size: 2.5rem; margin-bottom: 12px; }
        .portal-btn { background: #ffc600; color: #08111f; border: none; border-radius: 6px; padding: 10px 22px; font-weight: 700; font-size: 0.95rem; cursor: pointer; text-decoration: none; display: inline-block; }
        .portal-btn:hover { background: #36f3ff; color: #08111f; text-decoration: none; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>

<section class="portal-wrap">
    <div class="container">
        <a href="/client/dashboard.php" class="portal-back">← Back to Dashboard</a>

        <div class="portal-card">
            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
                <h2 style="margin:0;">📥 My Requests</h2>
                <a href="/client/new-request.php" class="portal-btn">+ New Request</a>
            </div>

            <?php if (empty($myRequests)): ?>
                <div class="empty-state">
                    <div class="icon">📭</div>
                    <p>You haven't submitted any requests yet.</p>
                    <a href="/client/new-request.php" class="portal-btn">Submit Your First Request</a>
                </div>
            <?php else: ?>
                <p style="color:#5a7a9e; font-size:0.875rem; margin-bottom:20px;">
                    <?php echo count($myRequests); ?> request<?php echo count($myRequests) !== 1 ? 's' : ''; ?> submitted
                </p>

                <?php foreach ($myRequests as $req):
                    $statusKey  = $req['status'] ?? 'new';
                    $statusInfo = $statusLabels[$statusKey] ?? ['label' => ucfirst($statusKey), 'color' => '#5a7a9e'];
                    $createdAt  = $req['created_at'] ?? '';
                    $dateStr    = $createdAt ? date('M j, Y', strtotime($createdAt)) : '';
                ?>
                <div class="req-item">
                    <div class="req-header">
                        <div>
                            <div class="req-title"><?php echo pe($req['project_title'] ?? 'Untitled'); ?></div>
                            <div class="req-type"><?php echo pe($req['request_type'] ?? ''); ?></div>
                        </div>
                        <div class="req-status" style="color:<?php echo pe($statusInfo['color']); ?>; border: 1px solid <?php echo pe($statusInfo['color']); ?>;">
                            <?php echo pe($statusInfo['label']); ?>
                        </div>
                    </div>
                    <div class="req-desc">
                        <?php echo pe(mb_strimwidth($req['description'] ?? '', 0, 300, '…')); ?>
                    </div>
                    <div class="req-meta">
                        <?php if ($dateStr): ?>
                            <span>📅 <?php echo pe($dateStr); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($req['budget_range'])): ?>
                            <span>💰 <?php echo pe($req['budget_range']); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($req['timeline'])): ?>
                            <span>⏱ <?php echo pe($req['timeline']); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($req['repo_link'])): ?>
                            <span>🔗 <a href="<?php echo pe($req['repo_link']); ?>" target="_blank" rel="noopener noreferrer" style="color:#36f3ff;">Link</a></span>
                        <?php endif; ?>
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
