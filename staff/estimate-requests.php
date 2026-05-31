<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';

portalRequireStaff();

$requests = portalLoadEstimateRequests();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = trim($_POST['request_id'] ?? '');
    $action = trim($_POST['action'] ?? '');

    if ($id !== '') {
        foreach ($requests as &$r) {
            if (($r['id'] ?? '') !== $id) {
                continue;
            }
            if ($action === 'set_status') {
                $newStatus = trim($_POST['new_status'] ?? '');
                $allowed = ['new', 'reviewed', 'contacted', 'proposal_needed', 'proposal_sent', 'closed'];
                if (in_array($newStatus, $allowed, true)) {
                    $r['status'] = $newStatus;
                }
            } elseif ($action === 'save_notes') {
                $r['notes'] = trim($_POST['notes'] ?? '');
            }
            break;
        }
        unset($r);
        portalSaveEstimateRequests($requests);
        header('Location: /staff/estimate-requests.php' . ($action === 'save_notes' ? '?view=' . urlencode($id) : ''));
        exit;
    }
}

$viewId = trim($_GET['view'] ?? '');
$viewRequest = null;
if ($viewId !== '') {
    foreach ($requests as $r) {
        if (($r['id'] ?? '') === $viewId) {
            $viewRequest = $r;
            break;
        }
    }
}

usort($requests, function ($a, $b) {
    return strcmp((string)($b['created_at'] ?? ''), (string)($a['created_at'] ?? ''));
});

$statusLabels = [
    'new' => ['label' => 'New', 'color' => '#ffc600'],
    'reviewed' => ['label' => 'Reviewed', 'color' => '#0a84ff'],
    'contacted' => ['label' => 'Contacted', 'color' => '#36f3ff'],
    'proposal_needed' => ['label' => 'Proposal Needed', 'color' => '#a78bfa'],
    'proposal_sent' => ['label' => 'Proposal Sent', 'color' => '#22c55e'],
    'closed' => ['label' => 'Closed', 'color' => '#5a7a9e'],
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
        .portal-wrap{padding:40px 0 80px;}
        .portal-card{background:#0c1729;border:1px solid rgba(54,243,255,.18);border-radius:10px;padding:26px;margin-bottom:20px;}
        .portal-back{color:#36f3ff;font-size:.9rem;margin-bottom:20px;display:inline-block;}
        .table-wrap{overflow:auto;}
        table{width:100%;min-width:1080px;border-collapse:collapse;}
        th,td{padding:10px 8px;border-bottom:1px solid rgba(54,243,255,.12);font-size:.86rem;vertical-align:top;color:#a8bedc;}
        th{color:#36f3ff;font-size:.78rem;text-transform:uppercase;letter-spacing:.04em;}
        .mono{font-family:monospace;color:#ffc600;font-weight:700;white-space:nowrap;}
        .status{display:inline-block;border-radius:4px;padding:2px 8px;font-size:.75rem;font-weight:700;background:rgba(100,100,100,.15);}
        .btn-view{color:#36f3ff;text-decoration:none;border:1px solid rgba(54,243,255,.3);border-radius:5px;padding:4px 10px;display:inline-block;}
        .detail-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;margin-bottom:20px;}
        @media(max-width:720px){.detail-grid{grid-template-columns:1fr;}}
        .detail-item label{color:#5a7a9e;font-size:.78rem;text-transform:uppercase;display:block;margin-bottom:3px;}
        .detail-item .dval{color:#eaf3ff;}
        .notes-area{background:#09111d;color:#eaf3ff;border:1px solid rgba(54,243,255,.2);border-radius:6px;padding:10px 12px;width:100%;min-height:80px;}
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>

<section class="portal-wrap">
<div class="container">
    <a href="/dashboard.php" class="portal-back">← Dashboard</a>

<?php if ($viewRequest !== null): ?>
    <?php $st = $viewRequest['status'] ?? 'new'; $info = $statusLabels[$st] ?? ['label' => ucfirst((string)$st), 'color' => '#7a9ac0']; ?>
    <div class="portal-card">
        <div style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;align-items:flex-start;">
            <h2 style="margin:0;color:#ffc600;">Estimate Request Detail</h2>
            <span class="status" style="border:1px solid <?php echo pe($info['color']); ?>;color:<?php echo pe($info['color']); ?>;"><?php echo pe($info['label']); ?></span>
        </div>

        <form method="post" style="margin:14px 0 18px;">
            <input type="hidden" name="request_id" value="<?php echo pe($viewRequest['id']); ?>">
            <input type="hidden" name="action" value="set_status">
            <label style="color:#5a7a9e;font-size:.8rem;display:block;margin-bottom:4px;">Update Status</label>
            <select name="new_status" style="background:#09111d;color:#eaf3ff;border:1px solid rgba(54,243,255,.2);border-radius:5px;padding:6px 10px;">
                <?php foreach ($statusLabels as $key => $lbl): ?>
                    <option value="<?php echo pe($key); ?>" <?php echo $st === $key ? 'selected' : ''; ?>><?php echo pe($lbl['label']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" style="margin-left:8px;background:#0a84ff;color:#fff;border:none;border-radius:5px;padding:6px 12px;">Update</button>
        </form>

        <div class="detail-grid">
            <div class="detail-item"><label>Estimate ID</label><div class="dval mono"><?php echo pe(portalGetEstimateDisplayId($viewRequest)); ?></div></div>
            <div class="detail-item"><label>Submitted</label><div class="dval"><?php echo pe(date('M j, Y g:i A', strtotime($viewRequest['created_at'] ?? 'now'))); ?></div></div>
            <div class="detail-item"><label>Name</label><div class="dval"><?php echo pe($viewRequest['name'] ?? '—'); ?></div></div>
            <div class="detail-item"><label>Username</label><div class="dval"><?php echo pe($viewRequest['client_username'] ?? '—'); ?></div></div>
            <div class="detail-item"><label>Email</label><div class="dval"><a href="mailto:<?php echo pe($viewRequest['email'] ?? ''); ?>" style="color:#36f3ff;"><?php echo pe($viewRequest['email'] ?? '—'); ?></a></div></div>
            <div class="detail-item"><label>Phone</label><div class="dval"><?php echo pe($viewRequest['phone'] ?? '—'); ?></div></div>
            <div class="detail-item"><label>Discord Username</label><div class="dval"><?php echo pe($viewRequest['discord_username'] ?? '—'); ?></div></div>
            <div class="detail-item"><label>Preferred Contact</label><div class="dval"><?php echo pe($viewRequest['contact_method'] ?? '—'); ?></div></div>
            <div class="detail-item"><label>Project Type</label><div class="dval"><?php echo pe($viewRequest['project_type'] ?? '—'); ?></div></div>
            <div class="detail-item"><label>Repository / Project Link</label><div class="dval"><?php if (!empty($viewRequest['repo_link'])): ?><a href="<?php echo pe($viewRequest['repo_link']); ?>" target="_blank" rel="noopener noreferrer" style="color:#36f3ff;">View Link</a><?php else: ?>—<?php endif; ?></div></div>
        </div>

        <div style="margin-bottom:16px;">
            <label style="color:#5a7a9e;font-size:.8rem;text-transform:uppercase;display:block;margin-bottom:6px;">Description</label>
            <div style="background:rgba(0,0,0,.3);border:1px solid rgba(54,243,255,.1);border-radius:6px;padding:12px;color:#a8bedc;white-space:pre-wrap;"><?php echo pe($viewRequest['description'] ?? ''); ?></div>
        </div>

        <form method="post">
            <input type="hidden" name="request_id" value="<?php echo pe($viewRequest['id']); ?>">
            <input type="hidden" name="action" value="save_notes">
            <label style="color:#5a7a9e;font-size:.8rem;text-transform:uppercase;display:block;margin-bottom:6px;">Internal Notes</label>
            <textarea name="notes" class="notes-area"><?php echo pe($viewRequest['notes'] ?? ''); ?></textarea>
            <button type="submit" style="margin-top:10px;background:#0a84ff;color:#fff;border:none;border-radius:5px;padding:8px 14px;">Save Notes</button>
        </form>
    </div>
<?php else: ?>
    <div class="portal-card">
        <h2 style="margin-top:0;color:#ffc600;">Estimate Requests</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Estimate ID</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Discord username</th>
                        <th>Preferred Contact</th>
                        <th>Project Type</th>
                        <th>Status</th>
                        <th>Submitted Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($requests)): ?>
                    <tr><td colspan="11" style="text-align:center;color:#5a7a9e;padding:20px;">No estimate requests yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($requests as $r): ?>
                        <?php $st = $r['status'] ?? 'new'; $info = $statusLabels[$st] ?? ['label' => ucfirst((string)$st), 'color' => '#7a9ac0']; ?>
                        <tr>
                            <td class="mono"><?php echo pe(portalGetEstimateDisplayId($r)); ?></td>
                            <td><?php echo pe($r['name'] ?? '—'); ?></td>
                            <td><?php echo pe($r['client_username'] ?? '—'); ?></td>
                            <td><?php echo pe($r['email'] ?? '—'); ?></td>
                            <td><?php echo pe($r['phone'] ?? '—'); ?></td>
                            <td><?php echo pe($r['discord_username'] ?? '—'); ?></td>
                            <td><?php echo pe($r['contact_method'] ?? '—'); ?></td>
                            <td><?php echo pe($r['project_type'] ?? '—'); ?></td>
                            <td><span class="status" style="color:<?php echo pe($info['color']); ?>;border:1px solid <?php echo pe($info['color']); ?>;"><?php echo pe($info['label']); ?></span></td>
                            <td><?php echo pe(date('M j, Y', strtotime($r['created_at'] ?? 'now'))); ?></td>
                            <td><a class="btn-view" href="?view=<?php echo urlencode((string)($r['id'] ?? '')); ?>">View</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

</div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="../assets/js/jquery-1.12.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>
