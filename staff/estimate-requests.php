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
$viewRequest = null;
foreach ($requests as $request) {
    if ((string)($request['id'] ?? '') === $viewId) {
        $viewRequest = portalNormalizeProjectRequest((array)$request);
        break;
    }
}

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
    <link href="../assets/css/coreloop.css" rel="stylesheet">
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
    <?php
        $requestId = portalGetRequestDisplayId($viewRequest);
        $st = (string)($viewRequest['status'] ?? 'new');
        $linkedProposals = array_values(array_filter($proposals, function ($p) use ($requestId) {
            return (string)($p['request_id'] ?? '') === $requestId;
        }));
        $linkedAgreements = array_values(array_filter($agreements, function ($a) use ($requestId) {
            return (string)($a['request_id'] ?? '') === $requestId;
        }));
    ?>
    <div class="portal-card">
        <div style="display:flex;justify-content:space-between;gap:8px;flex-wrap:wrap;align-items:center;" class="no-print">
            <h2 style="margin:0;color:#ffc600;">Project Request Detail</h2>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <button type="button" class="btn btn-teal" onclick="window.print()">Print / Save PDF</button>
                <a class="btn btn-gold" href="/staff/create-proposal.php?request_id=<?php echo urlencode($requestId); ?>">Create Proposal</a>
                <a class="btn btn-teal" href="/staff/create-agreement.php?request_id=<?php echo urlencode($requestId); ?>">Create Project Agreement</a>
            </div>
        </div>

        <div class="detail-grid">
            <div class="detail-item"><label>Request ID</label><div class="dval mono"><?php echo pe($requestId); ?></div></div>
            <div class="detail-item"><label>Status</label><div class="dval"><span class="status"><?php echo pe($statuses[$st] ?? ucfirst($st)); ?></span></div></div>
            <div class="detail-item"><label>Submitted date</label><div class="dval"><?php echo pe(date('M j, Y g:i A', strtotime((string)($viewRequest['created_at'] ?? 'now')))); ?></div></div>
            <div class="detail-item"><label>Client name</label><div class="dval"><?php echo pe($viewRequest['name'] ?? '—'); ?></div></div>
            <div class="detail-item"><label>Username</label><div class="dval"><?php echo pe($viewRequest['client_username'] ?? '—'); ?></div></div>
            <div class="detail-item"><label>Email</label><div class="dval"><?php echo pe($viewRequest['email'] ?? '—'); ?></div></div>
            <div class="detail-item"><label>Phone</label><div class="dval"><?php echo pe($viewRequest['phone'] ?? '—'); ?></div></div>
            <div class="detail-item"><label>Discord username</label><div class="dval"><?php echo pe($viewRequest['discord_username'] ?? '—'); ?></div></div>
            <div class="detail-item"><label>Preferred contact</label><div class="dval"><?php echo pe($viewRequest['contact_method'] ?? '—'); ?></div></div>
            <div class="detail-item"><label>Project type</label><div class="dval"><?php echo pe($viewRequest['project_type'] ?? '—'); ?></div></div>
            <div class="detail-item"><label>Project stage</label><div class="dval"><?php echo pe($viewRequest['project_stage'] ?? '—'); ?></div></div>
            <div class="detail-item"><label>Project size</label><div class="dval"><?php echo pe($viewRequest['project_size'] ?? '—'); ?></div></div>
            <div class="detail-item"><label>Timeline</label><div class="dval"><?php echo pe($viewRequest['timeline'] ?? '—'); ?></div></div>
            <div class="detail-item"><label>Budget comfort</label><div class="dval"><?php echo pe($viewRequest['budget_comfort'] ?? '—'); ?></div></div>
            <div class="detail-item"><label>Repository / project link</label><div class="dval"><?php if (!empty($viewRequest['repo_link'])): ?><a href="<?php echo pe($viewRequest['repo_link']); ?>" target="_blank" rel="noopener noreferrer" style="color:#36f3ff;">View Link</a><?php else: ?>—<?php endif; ?></div></div>
        </div>

        <div style="margin:10px 0 14px;">
            <label class="muted" style="text-transform:uppercase;display:block;margin-bottom:5px;">Description</label>
            <div style="background:rgba(0,0,0,.3);border:1px solid rgba(54,243,255,.1);border-radius:6px;padding:10px;color:#a8bedc;white-space:pre-wrap;font-size:.86rem;"><?php echo pe($viewRequest['description'] ?? ''); ?></div>
        </div>

        <form method="post" class="no-print">
            <input type="hidden" name="record_id" value="<?php echo pe($viewRequest['id']); ?>">
            <input type="hidden" name="action" value="save_updates">
            <div class="detail-grid">
                <div><label>Status</label><select name="status" class="select"><?php foreach ($statuses as $k => $label): ?><option value="<?php echo pe($k); ?>" <?php echo $st === $k ? 'selected' : ''; ?>><?php echo pe($label); ?></option><?php endforeach; ?></select></div>
                <div><label>Estimated Cost Range</label><input class="input" type="text" name="estimated_cost_range" value="<?php echo pe($viewRequest['estimated_cost_range'] ?? ''); ?>"></div>
                <div><label>Estimated Time Range</label><input class="input" type="text" name="estimated_time_range" value="<?php echo pe($viewRequest['estimated_time_range'] ?? ''); ?>"></div>
                <div><label>Recommended Next Step</label><input class="input" type="text" name="recommended_next_step" value="<?php echo pe($viewRequest['recommended_next_step'] ?? ''); ?>"></div>
            </div>
            <div style="margin-top:8px;"><label>Staff Summary</label><textarea class="textarea" name="staff_summary"><?php echo pe($viewRequest['staff_summary'] ?? ''); ?></textarea></div>
            <div style="margin-top:8px;"><label>Internal Notes</label><textarea class="textarea" name="internal_notes"><?php echo pe($viewRequest['internal_notes'] ?? ''); ?></textarea></div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;">
                <button type="submit" class="btn btn-blue">Save Updates</button>
            </div>
        </form>

        <form method="post" class="no-print" style="margin-top:8px;">
            <input type="hidden" name="record_id" value="<?php echo pe($viewRequest['id']); ?>">
            <input type="hidden" name="action" value="email_customer">
            <button type="submit" class="btn btn-teal">Email Customer</button>
        </form>

        <div style="margin-top:14px;">
            <label class="muted" style="text-transform:uppercase;display:block;margin-bottom:6px;">Linked Proposals</label>
            <?php if (empty($linkedProposals)): ?>
                <div class="muted">No linked proposals yet.</div>
            <?php else: ?>
                <?php foreach ($linkedProposals as $proposal): ?>
                    <div><a style="color:#36f3ff;font-size:.86rem;" href="/staff/create-proposal.php?proposal_id=<?php echo urlencode((string)($proposal['proposal_id'] ?? '')); ?>"><?php echo pe((string)($proposal['proposal_id'] ?? 'Proposal')); ?></a></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div style="margin-top:10px;">
            <label class="muted" style="text-transform:uppercase;display:block;margin-bottom:6px;">Linked Project Agreements</label>
            <?php if (empty($linkedAgreements)): ?>
                <div class="muted">No linked project agreements yet.</div>
            <?php else: ?>
                <?php foreach ($linkedAgreements as $agreement): ?>
                    <div><a style="color:#36f3ff;font-size:.86rem;" href="/staff/create-agreement.php?agreement_id=<?php echo urlencode((string)($agreement['agreement_id'] ?? '')); ?>"><?php echo pe((string)($agreement['agreement_id'] ?? 'Agreement')); ?></a></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
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
                        <th>Submitted Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($requests)): ?>
                    <tr><td colspan="7" style="text-align:center;color:#5a7a9e;padding:20px;">No project requests yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($requests as $r): ?>
                        <?php $status = (string)($r['status'] ?? 'new'); ?>
                        <tr>
                            <td class="mono"><?php echo pe(portalGetRequestDisplayId((array)$r)); ?></td>
                            <td><?php echo pe($r['name'] ?? '—'); ?></td>
                            <td><?php echo pe($r['email'] ?? '—'); ?></td>
                            <td><?php echo pe($r['project_type'] ?? '—'); ?></td>
                            <td><span class="status"><?php echo pe($statuses[$status] ?? ucfirst($status)); ?></span></td>
                            <td><?php echo pe(date('M j, Y', strtotime((string)($r['created_at'] ?? 'now')))); ?></td>
                            <td><a class="btn btn-teal" href="?view=<?php echo urlencode((string)($r['id'] ?? '')); ?>">View</a></td>
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
