<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';

portalRequireStaff();

$proposals = portalLoadProposals();

usort($proposals, function ($a, $b) {
    $ta = strtotime((string)($b['updated_at'] ?? $b['created_at'] ?? '')) ?: 0;
    $tb = strtotime((string)($a['updated_at'] ?? $a['created_at'] ?? '')) ?: 0;
    return $ta <=> $tb;
});

$statusLabels = [
    'draft'            => 'Draft',
    'sent'             => 'Sent',
    'changes_requested'=> 'Changes Requested',
    'accepted'         => 'Accepted',
    'payment_pending'  => 'Payment Pending',
    'paid_start'       => 'Paid — Ready',
    'in_progress'      => 'In Progress',
    'completed'        => 'Completed',
    'cancelled'        => 'Cancelled',
];

$statusColors = [
    'draft'            => '#7a9ac0',
    'sent'             => '#36f3ff',
    'changes_requested'=> '#ffc600',
    'accepted'         => '#86efac',
    'payment_pending'  => '#fbbf24',
    'paid_start'       => '#34d399',
    'in_progress'      => '#60a5fa',
    'completed'        => '#4ade80',
    'cancelled'        => '#ef4444',
];

$notice = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pid = trim((string)($_POST['proposal_id'] ?? ''));
    $action = trim((string)($_POST['action'] ?? ''));
    $proposals = portalLoadProposals();
    foreach ($proposals as &$p) {
        if ((string)($p['proposal_id'] ?? '') !== $pid) { continue; }
        if ($action === 'cancel') {
            $p['proposal_status'] = 'cancelled';
            $p['updated_at'] = date('c');
            $notice = 'Proposal cancelled.';
        } elseif ($action === 'mark_accepted') {
            $p['proposal_status'] = 'accepted';
            $p['accepted_at'] = date('c');
            $p['updated_at'] = date('c');
            $notice = 'Proposal marked accepted.';
        }
        break;
    }
    unset($p);
    portalSaveProposals($proposals);
    $proposals = portalLoadProposals();
}

$current_page = 'staff-portal';
$header_class = 'inner-header';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../assets/images/RL-icon.png">
    <title>Proposals | Staff | Runlevel Systems</title>
    <link href="../assets/css/coreloop.css" rel="stylesheet">
    <style>
        .wrap{padding:28px 0 70px;}
        .card{background:#0c1729;border:1px solid rgba(54,243,255,.18);border-radius:10px;padding:16px;margin-bottom:14px;}
        .tbl{width:100%;border-collapse:collapse;font-size:.8rem;}
        .tbl th{color:#5a7a9e;font-size:.68rem;text-transform:uppercase;padding:6px 8px;border-bottom:1px solid rgba(54,243,255,.12);white-space:nowrap;}
        .tbl td{padding:8px 8px;border-bottom:1px solid rgba(54,243,255,.08);color:#d0e4f7;vertical-align:middle;}
        .tbl tr:last-child td{border-bottom:none;}
        .btn{display:inline-block;border-radius:4px;padding:3px 8px;font-size:.7rem;font-weight:700;text-decoration:none;border:none;cursor:pointer;}
        .btn-blue{background:#0a84ff;color:#fff;}
        .btn-teal{background:rgba(54,243,255,.12);border:1px solid rgba(54,243,255,.3);color:#36f3ff;}
        .btn-gold{background:#ffc600;color:#08111f;}
        .btn-red{background:rgba(239,68,68,.15);border:1px solid rgba(239,68,68,.35);color:#fca5a5;}
        .mono{font-family:monospace;color:#ffc600;}
        .notice{background:rgba(34,197,94,.14);border:1px solid rgba(34,197,94,.35);color:#86efac;border-radius:6px;padding:10px 12px;margin-bottom:10px;font-size:.84rem;}
        .overflow-x{overflow-x:auto;}
        @media(max-width:700px){.tbl{min-width:700px;}}
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>
<section class="wrap">
<div class="container">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:12px;">
        <div>
            <h1 style="margin:0;color:#ffc600;font-size:1.22rem;">Proposals</h1>
            <p style="margin:4px 0 0;color:#7a9ac0;font-size:.8rem;">All client proposals.</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="/staff/proposal-edit.php" class="btn btn-gold">+ New Proposal</a>
            <a href="/staff/payment-record.php" class="btn btn-teal">Record Payment</a>
            <a href="/dashboard.php" class="btn btn-teal">← Dashboard</a>
        </div>
    </div>

    <?php if ($notice !== ''): ?><div class="notice"><?php echo pe($notice); ?></div><?php endif; ?>

    <div class="card overflow-x">
        <?php if (empty($proposals)): ?>
            <p style="color:#7a9ac0;">No proposals yet. <a href="/staff/proposal-edit.php" style="color:#36f3ff;">Create one →</a></p>
        <?php else: ?>
        <table class="tbl">
            <thead>
                <tr>
                    <th>Proposal ID</th>
                    <th>Project Title</th>
                    <th>Client</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Due To Start</th>
                    <th>Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($proposals as $p):
                $pid = (string)($p['proposal_id'] ?? '');
                $status = (string)($p['proposal_status'] ?? $p['status'] ?? 'draft');
                $label = $statusLabels[$status] ?? ucfirst($status);
                $color = $statusColors[$status] ?? '#7a9ac0';
                $updatedTs = strtotime((string)($p['updated_at'] ?? $p['created_at'] ?? '')) ?: 0;
            ?>
                <tr>
                    <td class="mono"><?php echo pe($pid); ?></td>
                    <td><?php echo pe($p['project_title'] ?? ''); ?></td>
                    <td style="color:#a8bedc;"><?php echo pe($p['client_name'] ?? ''); ?></td>
                    <td><span style="display:inline-block;padding:2px 8px;border-radius:999px;font-size:.68rem;font-weight:700;background:<?php echo pe($color); ?>22;border:1px solid <?php echo pe($color); ?>55;color:<?php echo pe($color); ?>;"><?php echo pe($label); ?></span></td>
                    <td style="color:#86efac;"><?php echo $p['total_price'] !== '' ? '$' . pe($p['total_price'] ?? '') : '—'; ?></td>
                    <td style="color:#ffc600;"><?php echo $p['amount_due_to_start'] !== '' ? '$' . pe($p['amount_due_to_start'] ?? '') : '—'; ?></td>
                    <td style="color:#5a7a9e;white-space:nowrap;"><?php echo $updatedTs > 0 ? pe(date('M j, Y', $updatedTs)) : '—'; ?></td>
                    <td style="white-space:nowrap;">
                        <a class="btn btn-teal" href="/staff/proposal-view.php?proposal_id=<?php echo urlencode($pid); ?>">View</a>
                        <a class="btn btn-blue" href="/staff/proposal-edit.php?proposal_id=<?php echo urlencode($pid); ?>">Edit</a>
                        <?php if (in_array($status, ['draft', 'changes_requested'], true)): ?>
                        <a class="btn btn-gold" href="/staff/proposal-send.php?proposal_id=<?php echo urlencode($pid); ?>">Send</a>
                        <?php endif; ?>
                        <?php if ($status === 'sent'): ?>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="proposal_id" value="<?php echo pe($pid); ?>">
                            <button class="btn btn-teal" type="submit" name="action" value="mark_accepted">Mark Accepted</button>
                        </form>
                        <?php endif; ?>
                        <?php if (in_array($status, ['accepted', 'payment_pending'], true)): ?>
                        <a class="btn btn-gold" href="/staff/payment-record.php?proposal_id=<?php echo urlencode($pid); ?>">Record Payment</a>
                        <?php endif; ?>
                        <?php if (!in_array($status, ['cancelled', 'completed'], true)): ?>
                        <form method="post" style="display:inline;" onsubmit="return confirm('Cancel this proposal?');">
                            <input type="hidden" name="proposal_id" value="<?php echo pe($pid); ?>">
                            <button class="btn btn-red" type="submit" name="action" value="cancel">Cancel</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="../assets/js/jquery-1.12.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>
