<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';
require_once __DIR__ . '/../includes/email.php';

portalRequireStaff();

$statuses = [
    'new' => 'New',
    'reviewing' => 'Reviewing',
    'contacted' => 'Contacted',
    'needs_info' => 'Needs Info',
    'proposal_drafted' => 'Proposal Drafted',
    'proposal_sent' => 'Proposal Sent',
    'accepted' => 'Accepted',
    'declined' => 'Declined',
    'closed' => 'Closed',
];

$requests = portalLoadProjectRequests();
$notice = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recordId = trim((string)($_POST['record_id'] ?? ''));
    $action = trim((string)($_POST['action'] ?? 'save_updates'));
    foreach ($requests as &$req) {
        if ((string)($req['id'] ?? '') !== $recordId) {
            continue;
        }

        if ($action === 'email_customer') {
            $name = (string)($req['name'] ?? 'there');
            $email = (string)($req['email'] ?? '');
            $requestId = portalGetRequestDisplayId($req);
            $subject = 'Runlevel Systems Project Request - ' . $requestId;
            $body = "Hello {$name},\n\n"
                . "We reviewed your project request and have an update.\n\n"
                . "Request ID:\n{$requestId}\n\n"
                . "Current status:\n" . ($statuses[$req['status']] ?? ucfirst((string)$req['status'])) . "\n\n"
                . "If you have any updates, reply to this email and include your Request ID.\n\n"
                . "Runlevel Systems\n"
                . "DESIGN • DEBUG • DEPLOY\n";
            send_email($email, $subject, $body);
            $notice = 'Customer email sent.';
        } else {
            $newStatus = trim((string)($_POST['status'] ?? 'new'));
            $req['status'] = isset($statuses[$newStatus]) ? $newStatus : 'new';
            $req['estimated_cost_range'] = trim((string)($_POST['estimated_cost_range'] ?? ''));
            $req['estimated_time_range'] = trim((string)($_POST['estimated_time_range'] ?? ''));
            $req['staff_summary'] = trim((string)($_POST['staff_summary'] ?? ''));
            $req['recommended_next_step'] = trim((string)($_POST['recommended_next_step'] ?? ''));
            $req['internal_notes'] = trim((string)($_POST['internal_notes'] ?? ''));
            $notice = 'Request updates saved.';
        }

        $req['updated_at'] = date('c');
        break;
    }
    unset($req);
    portalSaveProjectRequests($requests);
}

$proposals = portalLoadProposals();
$agreements = portalLoadProjectAgreements();

$viewId = trim((string)($_GET['view'] ?? ($_POST['record_id'] ?? '')));
// If a ?view= param is set, redirect to the unified project workspace
if ($viewId !== '') {
    foreach ($requests as $r) {
        if ((string)($r['id'] ?? '') === $viewId) {
            $rid = portalGetRequestDisplayId((array)$r);
            header('Location: /project.php?id=' . urlencode($rid));
            exit;
        }
    }
}
$viewRequest = null;

usort($requests, function ($a, $b) {
    return strcmp((string)($b['created_at'] ?? ''), (string)($a['created_at'] ?? ''));
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
    <title>Project Requests | Staff | Runlevel Systems</title>
    <link href="../assets/css/runlevel.css" rel="stylesheet">
    <style>
        .portal-wrap{padding:30px 0 70px;}
        .portal-card{background:#0c1729;border:1px solid rgba(54,243,255,.18);border-radius:10px;padding:20px;margin-bottom:16px;}
        .portal-back{color:#36f3ff;font-size:.85rem;margin-bottom:14px;display:inline-block;}
        .table-wrap{overflow:auto;}
        table{width:100%;min-width:940px;border-collapse:collapse;}
        th,td{padding:8px 6px;border-bottom:1px solid rgba(54,243,255,.12);font-size:.82rem;vertical-align:top;color:#a8bedc;}
        th{color:#36f3ff;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;}
        .mono{font-family:monospace;color:#ffc600;font-weight:700;white-space:nowrap;}
        .status{display:inline-block;border-radius:4px;padding:2px 8px;font-size:.72rem;font-weight:700;background:rgba(100,100,100,.15);}
        .btn{display:inline-block;border-radius:5px;padding:7px 10px;font-size:.8rem;border:none;font-weight:700;text-decoration:none;cursor:pointer;}
        .btn-blue{background:#0a84ff;color:#fff;}
        .btn-teal{background:rgba(54,243,255,.12);border:1px solid rgba(54,243,255,.3);color:#36f3ff;}
        .btn-gold{background:#ffc600;color:#08111f;}
        .detail-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin:12px 0;}
        @media(max-width:760px){.detail-grid{grid-template-columns:1fr;}}
        .detail-item label{color:#5a7a9e;font-size:.72rem;text-transform:uppercase;display:block;margin-bottom:2px;}
        .detail-item .dval{color:#eaf3ff;font-size:.86rem;}
        .input,.textarea,.select{background:#09111d;color:#eaf3ff;border:1px solid rgba(54,243,255,.2);border-radius:6px;padding:8px 10px;width:100%;font-size:.85rem;}
        .textarea{min-height:90px;resize:vertical;}
        .muted{color:#5a7a9e;font-size:.8rem;}
        .notice{background:rgba(34,197,94,.16);border:1px solid rgba(34,197,94,.35);color:#86efac;border-radius:6px;padding:10px 12px;margin-bottom:12px;font-size:.84rem;}
        @media print {
            #header, #footer-widget, footer, .portal-back, .no-print, .btn { display:none !important; }
            body { background:#fff; color:#000; }
            .portal-card { border:1px solid #ccc; background:#fff; }
            .detail-item .dval, .mono { color:#111 !important; }
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>

<section class="portal-wrap">
<div class="container">
    <a href="/dashboard.php" class="portal-back">← Dashboard</a>

<?php if ($notice !== ''): ?><div class="notice"><?php echo pe($notice); ?></div><?php endif; ?>

<?php if ($viewRequest !== null): ?>
    <?php /* Redirect handled above; this block kept as safety fallback */ ?>
    <div class="portal-card"><p style="color:#ffc600;">Redirecting to project workspace&hellip;</p><script>window.location = '/project.php?id=' + encodeURIComponent('<?php echo pe($viewId); ?>');</script></div>
<?php else: ?>
    <div class="portal-card">
        <h2 style="margin-top:0;color:#ffc600;font-size:1.2rem;">Project Requests</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Client</th>
                        <th>Email</th>
                        <th>Project Type</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Last Updated</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($requests)): ?>
                    <tr><td colspan="8" style="text-align:center;color:#5a7a9e;padding:20px;">No project requests yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($requests as $r): ?>
                        <?php
                            $status = (string)($r['status'] ?? 'new');
                            $rid    = portalGetRequestDisplayId((array)$r);
                            $workspaceUrl = '/project.php?id=' . urlencode($rid);
                        ?>
                        <tr style="cursor:pointer;" onclick="window.location='<?php echo pe($workspaceUrl); ?>'">
                            <td class="mono"><?php echo pe($rid); ?></td>
                            <td><?php echo pe($r['name'] ?? '—'); ?></td>
                            <td><?php echo pe($r['email'] ?? '—'); ?></td>
                            <td><?php echo pe($r['project_type'] ?? '—'); ?></td>
                            <td><span class="status"><?php echo pe($statuses[$status] ?? ucfirst($status)); ?></span></td>
                            <td><?php echo pe(date('M j, Y', strtotime((string)($r['created_at'] ?? 'now')))); ?></td>
                            <td><?php echo pe(!empty($r['updated_at']) ? date('M j, Y', strtotime((string)$r['updated_at'])) : '—'); ?></td>
                            <td onclick="event.stopPropagation()"><a class="btn btn-teal" href="<?php echo pe($workspaceUrl); ?>">View</a></td>
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
