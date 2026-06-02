<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';

portalRequireLogin();
if (portalGetRole() !== 'client') {
    header('Location: /dashboard.php');
    exit;
}

$user     = portalGetUser();
$username = (string)($user['username'] ?? '');
$requests = array_values(array_filter(portalLoadProjectRequests(), function ($r) use ($username) {
    return (string)($r['client_username'] ?? '') === $username;
}));
$requestIds = array_map(function ($r) { return portalGetRequestDisplayId((array)$r); }, $requests);

$proposals = array_values(array_filter(portalLoadProposals(), function ($p) use ($requestIds, $username) {
    return in_array((string)($p['request_id'] ?? ''), $requestIds, true)
        || (string)($p['client_username'] ?? '') === $username;
}));

// Only show proposals that have been sent (not draft)
$proposals = array_values(array_filter($proposals, function ($p) {
    $st = (string)($p['proposal_status'] ?? $p['status'] ?? 'draft');
    return !in_array($st, ['draft', 'cancelled'], true);
}));

usort($proposals, function ($a, $b) {
    return strcmp(
        (string)($b['updated_at'] ?? $b['created_at'] ?? ''),
        (string)($a['updated_at'] ?? $a['created_at'] ?? '')
    );
});

$statusLabels = [
    'sent'             => 'Awaiting Your Review',
    'changes_requested'=> 'Changes Requested',
    'accepted'         => 'Accepted',
    'payment_pending'  => 'Payment Pending',
    'paid_start'       => 'Paid — Work Starting',
    'in_progress'      => 'In Progress',
    'completed'        => 'Completed',
];

$statusColors = [
    'sent'             => '#36f3ff',
    'changes_requested'=> '#ffc600',
    'accepted'         => '#86efac',
    'payment_pending'  => '#fbbf24',
    'paid_start'       => '#34d399',
    'in_progress'      => '#60a5fa',
    'completed'        => '#4ade80',
];

$current_page = 'client-portal';
$header_class = 'inner-header';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../assets/images/RL-icon.png">
    <title>My Proposals | Client Portal | Runlevel Systems</title>
    <link href="../assets/css/coreloop.css?v=clean-20260602" rel="stylesheet">
    <style>
        .portal-wrap{padding:30px 0 70px;}
        .card{background:#0c1729;border:1px solid rgba(54,243,255,.18);border-radius:10px;padding:18px;margin-bottom:14px;}
        .how-list{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:6px;}
        .how-list li{display:flex;align-items:flex-start;gap:10px;color:#a8bedc;font-size:.85rem;}
        .how-step{display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;min-width:22px;border-radius:50%;background:rgba(54,243,255,.12);border:1px solid rgba(54,243,255,.3);color:#36f3ff;font-size:.72rem;font-weight:700;}
        .prop-card{background:#09111d;border:1px solid rgba(54,243,255,.14);border-radius:8px;padding:14px;margin-bottom:10px;}
        .status-badge{display:inline-block;padding:2px 10px;border-radius:999px;font-size:.68rem;font-weight:700;}
        .prop-actions{display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;}
        .btn{display:inline-block;border-radius:5px;padding:6px 12px;font-size:.8rem;font-weight:700;text-decoration:none;border:none;cursor:pointer;}
        .btn-teal{background:rgba(54,243,255,.12);border:1px solid rgba(54,243,255,.3);color:#36f3ff;}
        .btn-gold{background:#ffc600;color:#08111f;}
        .btn-green{background:rgba(52,211,153,.12);border:1px solid rgba(52,211,153,.35);color:#34d399;}
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>
<section class="portal-wrap">
<div class="container">
    <a href="/dashboard.php" style="color:#36f3ff;font-size:.84rem;">← Dashboard</a>

    <div class="card" style="margin-top:10px;">
        <h2 style="margin:0 0 12px;color:#ffc600;font-size:1rem;">How This Works</h2>
        <ul class="how-list">
            <li><span class="how-step">1</span> You tell us what you need.</li>
            <li><span class="how-step">2</span> We create a proposal.</li>
            <li><span class="how-step">3</span> You can accept it or request changes.</li>
            <li><span class="how-step">4</span> Once accepted, you pay the amount due to start.</li>
            <li><span class="how-step">5</span> We begin work.</li>
            <li><span class="how-step">6</span> You review the delivered work.</li>
            <li><span class="how-step">7</span> Additional milestones or final payment are handled according to the proposal.</li>
        </ul>
    </div>

    <div class="card">
        <h2 style="margin:0 0 12px;color:#ffc600;font-size:1rem;">My Proposals</h2>
        <?php if (empty($proposals)): ?>
            <p style="color:#7a9ac0;margin:0;">No proposals available yet. Once we review your request and create a proposal, it will appear here.</p>
        <?php else: ?>
            <?php foreach ($proposals as $proposal):
                $pid    = (string)($proposal['proposal_id'] ?? '');
                $status = (string)($proposal['proposal_status'] ?? $proposal['status'] ?? 'sent');
                $label  = $statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status));
                $color  = $statusColors[$status] ?? '#7a9ac0';
                $updTs  = strtotime((string)($proposal['updated_at'] ?? $proposal['created_at'] ?? '')) ?: 0;
            ?>
                <div class="prop-card">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;flex-wrap:wrap;">
                        <div>
                            <div style="color:#ffc600;font-weight:700;font-size:.92rem;"><?php echo pe($proposal['project_title'] ?? 'Project Proposal'); ?></div>
                            <div style="color:#7a9ac0;font-size:.74rem;margin-top:2px;">ID: <?php echo pe($pid); ?></div>
                        </div>
                        <span class="status-badge" style="background:<?php echo pe($color); ?>18;border:1px solid <?php echo pe($color); ?>44;color:<?php echo pe($color); ?>;"><?php echo pe($label); ?></span>
                    </div>
                    <?php if (!empty($proposal['total_price']) || !empty($proposal['amount_due_to_start'])): ?>
                    <div style="display:flex;gap:16px;margin-top:8px;flex-wrap:wrap;">
                        <?php if (!empty($proposal['total_price'])): ?><div style="font-size:.8rem;color:#a8bedc;">Total: <strong style="color:#86efac;">$<?php echo pe($proposal['total_price']); ?></strong></div><?php endif; ?>
                        <?php if (!empty($proposal['amount_due_to_start'])): ?><div style="font-size:.8rem;color:#a8bedc;">Due to Start: <strong style="color:#ffc600;">$<?php echo pe($proposal['amount_due_to_start']); ?></strong></div><?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($updTs > 0): ?><div style="color:#5a7a9e;font-size:.72rem;margin-top:4px;">Updated <?php echo pe(date('M j, Y', $updTs)); ?></div><?php endif; ?>
                    <div class="prop-actions">
                        <a class="btn btn-teal" href="/client/proposal-view.php?proposal_id=<?php echo urlencode($pid); ?>">View Proposal</a>
                        <?php if ($status === 'sent' || $status === 'changes_requested'): ?>
                        <a class="btn btn-green" href="/client/proposal-view.php?proposal_id=<?php echo urlencode($pid); ?>#accept">Accept</a>
                        <a class="btn btn-gold" href="/client/proposal-view.php?proposal_id=<?php echo urlencode($pid); ?>#request-changes">Request Changes</a>
                        <?php endif; ?>
                        <?php if ($status === 'accepted'): ?><span style="color:#86efac;font-size:.78rem;padding:6px 0;">✓ Accepted — awaiting payment</span><?php endif; ?>
                        <?php if ($status === 'payment_pending'): ?><span style="color:#fbbf24;font-size:.78rem;padding:6px 0;">Payment pending</span><?php endif; ?>
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

