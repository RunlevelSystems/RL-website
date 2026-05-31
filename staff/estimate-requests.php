<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';

portalRequireStaff();

$staff = portalGetStaffUser();
$requests = portalLoadEstimateRequests();

// Handle status update or note save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = trim($_POST['request_id'] ?? '');
    $action = trim($_POST['action'] ?? '');

    if ($id !== '') {
        foreach ($requests as &$r) {
            if (($r['id'] ?? '') === $id) {
                if ($action === 'set_status') {
                    $newStatus = trim($_POST['new_status'] ?? '');
                    $allowed   = ['new', 'reviewed', 'contacted', 'proposal_needed', 'proposal_sent', 'closed'];
                    if (in_array($newStatus, $allowed, true)) {
                        $r['status'] = $newStatus;
                    }
                } elseif ($action === 'save_notes') {
                    $r['notes'] = trim($_POST['notes'] ?? '');
                }
                break;
            }
        }
        unset($r);
        portalSaveEstimateRequests($requests);
        header('Location: /staff/estimate-requests.php' . ($action === 'save_notes' ? '?view=' . urlencode($id) : ''));
        exit;
    }
}

// View a single request
$viewId      = trim($_GET['view'] ?? '');
$viewRequest = null;
if ($viewId !== '') {
    foreach ($requests as $r) {
        if (($r['id'] ?? '') === $viewId) {
            $viewRequest = $r;
            break;
        }
    }
}

// Sort newest first
usort($requests, function($a, $b) {
    return strcmp($b['created_at'] ?? '', $a['created_at'] ?? '');
});

$statusLabels = [
    'new'              => ['label' => 'New',              'color' => '#ffc600'],
    'reviewed'         => ['label' => 'Reviewed',         'color' => '#0a84ff'],
    'contacted'        => ['label' => 'Contacted',        'color' => '#36f3ff'],
    'proposal_needed'  => ['label' => 'Proposal Needed',  'color' => '#a78bfa'],
    'proposal_sent'    => ['label' => 'Proposal Sent',    'color' => '#22c55e'],
    'closed'           => ['label' => 'Closed',           'color' => '#5a7a9e'],
];

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
    <title>Estimate Requests | Staff | Runlevel Systems</title>
    <link href="../assets/css/coreloop.css" rel="stylesheet">
    <style>
        .portal-wrap { padding: 40px 0 80px; }
        .portal-back { color: #36f3ff; font-size: 0.9rem; margin-bottom: 20px; display: inline-block; }
        .portal-back:hover { color: #ffc600; }
        .portal-card { background: #0c1729; border: 1px solid rgba(54,243,255,0.18); border-radius: 10px; padding: 26px; margin-bottom: 20px; }
        .portal-card h2 { color: #ffc600; margin-top: 0; font-size: 1.2rem; }
        .section-title { color: #36f3ff; font-size: 1.1rem; font-weight: 700; margin: 0 0 16px; text-transform: uppercase; letter-spacing: 0.05em; }
        .req-row { background: #0c1729; border: 1px solid rgba(54,243,255,0.12); border-radius: 8px; padding: 14px 18px; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
        .req-row:hover { border-color: rgba(255,198,0,0.3); }
        .req-name { color: #eaf3ff; font-weight: 700; }
        .req-type { color: #7a9ac0; font-size: 0.85rem; }
        .req-date { color: #5a7a9e; font-size: 0.8rem; }
        .status-badge { display: inline-block; border-radius: 4px; padding: 2px 10px; font-size: 0.8rem; font-weight: 700; }
        .btn-view { background: rgba(54,243,255,0.1); color: #36f3ff; border: 1px solid rgba(54,243,255,0.3); border-radius: 5px; padding: 5px 14px; font-size: 0.85rem; text-decoration: none; }
        .btn-view:hover { background: rgba(54,243,255,0.2); color: #36f3ff; text-decoration: none; }
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
        @media (max-width: 600px) { .detail-grid { grid-template-columns: 1fr; } }
        .detail-item label { color: #5a7a9e; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.04em; display: block; margin-bottom: 3px; }
        .detail-item .dval { color: #eaf3ff; font-size: 0.95rem; }
        .notes-area { background: #09111d; color: #eaf3ff; border: 1px solid rgba(54,243,255,0.2); border-radius: 6px; padding: 10px 12px; width: 100%; min-height: 80px; resize: vertical; font-size: 0.9rem; }
        .notes-area:focus { outline: 2px solid #0a84ff; }
        .btn-save { background: #0a84ff; color: #fff; border: none; border-radius: 5px; padding: 8px 20px; font-size: 0.9rem; cursor: pointer; }
        .btn-save:hover { background: #36f3ff; color: #08111f; }
        .status-form select { background: #09111d; color: #eaf3ff; border: 1px solid rgba(54,243,255,0.2); border-radius: 5px; padding: 6px 10px; font-size: 0.9rem; }
        .status-form button { background: rgba(255,198,0,0.15); color: #ffc600; border: 1px solid rgba(255,198,0,0.3); border-radius: 5px; padding: 6px 14px; font-size: 0.85rem; cursor: pointer; margin-left: 6px; }
        .status-form button:hover { background: rgba(255,198,0,0.3); }
        .desc-block { background: rgba(0,0,0,0.3); border: 1px solid rgba(54,243,255,0.1); border-radius: 6px; padding: 14px; color: #a8bedc; font-size: 0.9rem; line-height: 1.7; white-space: pre-wrap; word-break: break-word; }
        .empty-msg { color: #5a7a9e; text-align: center; padding: 32px; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>

<section class="portal-wrap">
    <div class="container">
        <a href="/staff/dashboard.php" class="portal-back">← Staff Dashboard</a>

<?php if ($viewRequest !== null): ?>
        <?php
        $st   = $viewRequest['status'] ?? 'new';
        $info = $statusLabels[$st] ?? ['label' => ucfirst($st), 'color' => '#7a9ac0'];
        ?>
        <div class="portal-card">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:10px; margin-bottom:16px;">
                <h2 style="margin:0;">📋 Estimate Request Detail</h2>
                <span class="status-badge" style="background: rgba(100,100,100,0.2); color: <?php echo pe($info['color']); ?>; border: 1px solid <?php echo pe($info['color']); ?>;">
                    <?php echo pe($info['label']); ?>
                </span>
            </div>

            <form method="post" class="status-form" style="margin-bottom:20px;">
                <input type="hidden" name="request_id" value="<?php echo pe($viewRequest['id']); ?>">
                <input type="hidden" name="action" value="set_status">
                <label style="color:#5a7a9e; font-size:0.8rem; display:block; margin-bottom:4px;">Update Status</label>
                <select name="new_status">
                    <?php foreach ($statusLabels as $key => $lbl): ?>
                        <option value="<?php echo pe($key); ?>" <?php echo ($st === $key) ? 'selected' : ''; ?>><?php echo pe($lbl['label']); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Update</button>
            </form>

            <div class="detail-grid">
                <div class="detail-item">
                    <label>Estimate ID</label>
                    <div class="dval" style="color:#ffc600; font-family:monospace; font-weight:700;"><?php echo pe($viewRequest['estimate_id'] ?? '—'); ?></div>
                </div>
                <div class="detail-item">
                    <label>Submitted</label>
                    <div class="dval"><?php echo pe(date('M j, Y g:i A', strtotime($viewRequest['created_at'] ?? 'now'))); ?></div>
                </div>
                <div class="detail-item">
                    <label>Name</label>
                    <div class="dval"><?php echo pe($viewRequest['name'] ?? '—'); ?></div>
                </div>
                <div class="detail-item">
                    <label>Email</label>
                    <div class="dval"><a href="mailto:<?php echo pe($viewRequest['email'] ?? ''); ?>" style="color:#36f3ff;"><?php echo pe($viewRequest['email'] ?? '—') ?: '—'; ?></a></div>
                </div>
                <div class="detail-item">
                    <label>Phone</label>
                    <div class="dval"><?php echo pe($viewRequest['phone'] ?? '—') ?: '—'; ?></div>
                </div>
                <div class="detail-item">
                    <label>Preferred Contact</label>
                    <div class="dval" style="color:#ffc600;"><?php echo pe($viewRequest['contact_method'] ?? '—'); ?></div>
                </div>
                <div class="detail-item">
                    <label>Project Type</label>
                    <div class="dval"><?php echo pe($viewRequest['project_type'] ?? '—'); ?></div>
                </div>
                <div class="detail-item">
                    <label>What Needed</label>
                    <div class="dval"><?php echo pe($viewRequest['what_needed'] ?? '—'); ?></div>
                </div>
                <div class="detail-item">
                    <label>Project Stage</label>
                    <div class="dval"><?php echo pe($viewRequest['project_stage'] ?? '—'); ?></div>
                </div>
                <div class="detail-item">
                    <label>Approx. Size</label>
                    <div class="dval"><?php echo pe($viewRequest['approx_size'] ?? '—'); ?></div>
                </div>
                <div class="detail-item">
                    <label>Timeline</label>
                    <div class="dval"><?php echo pe($viewRequest['timeline'] ?? '—'); ?></div>
                </div>
                <div class="detail-item">
                    <label>Budget Comfort</label>
                    <div class="dval"><?php echo pe($viewRequest['budget'] ?? '—'); ?></div>
                </div>
                <div class="detail-item">
                    <label>Has Files</label>
                    <div class="dval"><?php echo pe($viewRequest['has_files'] ?? '—'); ?></div>
                </div>
                <div class="detail-item">
                    <label>Auto Category</label>
                    <div class="dval" style="color:#36f3ff;"><?php echo pe($viewRequest['category'] ?? '—'); ?></div>
                </div>
            </div>

            <?php if (!empty($viewRequest['repo_link'])): ?>
            <div class="detail-item" style="margin-bottom:16px;">
                <label>Repository / Project Link</label>
                <div class="dval"><a href="<?php echo pe($viewRequest['repo_link']); ?>" target="_blank" rel="noopener noreferrer" style="color:#36f3ff;"><?php echo pe($viewRequest['repo_link']); ?></a></div>
            </div>
            <?php endif; ?>

            <div style="margin-bottom:20px;">
                <label style="color:#5a7a9e; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.04em; display:block; margin-bottom:8px;">Description</label>
                <div class="desc-block"><?php echo pe($viewRequest['description'] ?? ''); ?></div>
            </div>

            <form method="post">
                <input type="hidden" name="request_id" value="<?php echo pe($viewRequest['id']); ?>">
                <input type="hidden" name="action" value="save_notes">
                <label style="color:#5a7a9e; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.04em; display:block; margin-bottom:6px;">Internal Notes</label>
                <textarea name="notes" class="notes-area"><?php echo pe($viewRequest['notes'] ?? ''); ?></textarea>
                <br><br>
                <button type="submit" class="btn-save">Save Notes</button>
            </form>
        </div>

<?php else: ?>
        <p class="section-title">Estimate Requests</p>

        <?php
        $newCount = count(array_filter($requests, function($r) { return ($r['status'] ?? '') === 'new'; }));
        if ($newCount > 0):
        ?>
        <div style="background: rgba(255,198,0,0.08); border: 1px solid rgba(255,198,0,0.25); border-radius: 8px; padding: 12px 18px; margin-bottom: 20px; color: #ffc600; font-size: 0.9rem;">
            ⚠️ <strong><?php echo $newCount; ?></strong> new estimate request<?php echo $newCount !== 1 ? 's' : ''; ?> waiting for review.
        </div>
        <?php endif; ?>

        <?php if (empty($requests)): ?>
            <div class="empty-msg">No estimate requests yet.</div>
        <?php else: ?>
            <?php foreach ($requests as $r): ?>
                <?php
                $st   = $r['status'] ?? 'new';
                $info = $statusLabels[$st] ?? ['label' => ucfirst($st), 'color' => '#7a9ac0'];
                ?>
                <div class="req-row">
                    <div>
                        <div class="req-name">
                            <?php echo pe($r['name'] ?? '—'); ?>
                            <?php if (!empty($r['estimate_id'])): ?>
                                <span style="color:#ffc600; font-family:monospace; font-size:0.8rem; margin-left:8px;"><?php echo pe($r['estimate_id']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="req-type"><?php echo pe($r['project_type'] ?? ''); ?> · <?php echo pe($r['what_needed'] ?? ''); ?></div>
                        <div class="req-date"><?php echo pe(date('M j, Y', strtotime($r['created_at'] ?? 'now'))); ?> · <?php echo pe($r['contact_method'] ?? ''); ?></div>
                    </div>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <span class="status-badge" style="color: <?php echo pe($info['color']); ?>; border: 1px solid <?php echo pe($info['color']); ?>; background: rgba(100,100,100,0.1);">
                            <?php echo pe($info['label']); ?>
                        </span>
                        <a href="?view=<?php echo urlencode($r['id'] ?? ''); ?>" class="btn-view">View</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
<?php endif; ?>

    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="../assets/js/jquery-1.12.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>
