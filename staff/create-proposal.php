<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';
require_once __DIR__ . '/../includes/email.php';

portalRequireStaff();

function generateProposalId(array $proposals) {
    $existing = [];
    foreach ($proposals as $proposal) {
        if (!empty($proposal['proposal_id'])) {
            $existing[(string)$proposal['proposal_id']] = true;
        }
    }
    $datePart = date('Ymd');
    do {
        $id = 'PROP-' . $datePart . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
    } while (isset($existing[$id]));
    return $id;
}

$proposalStatuses = ['draft' => 'Draft', 'sent' => 'Sent', 'accepted' => 'Accepted', 'rejected' => 'Rejected', 'expired' => 'Expired'];
$requests = portalLoadProjectRequests();
$proposals = portalLoadProposals();

$requestIdQuery = trim((string)($_GET['request_id'] ?? ''));
$proposalIdQuery = trim((string)($_GET['proposal_id'] ?? ''));
$request = null;
$proposal = null;
$requestRecordId = '';

if ($proposalIdQuery !== '') {
    foreach ($proposals as $row) {
        if ((string)($row['proposal_id'] ?? '') === $proposalIdQuery) {
            $proposal = $row;
            $requestIdQuery = (string)($row['request_id'] ?? $requestIdQuery);
            break;
        }
    }
}

if ($requestIdQuery !== '') {
    foreach ($requests as $row) {
        if (portalGetRequestDisplayId((array)$row) === $requestIdQuery) {
            $request = portalNormalizeProjectRequest((array)$row);
            $requestRecordId = (string)($row['id'] ?? '');
            break;
        }
    }
}

$notice = '';
$error = '';

$defaults = [
    'proposal_id' => $proposal['proposal_id'] ?? '',
    'request_id' => $requestIdQuery,
    'client_name' => $proposal['client_name'] ?? ($request['name'] ?? ''),
    'client_email' => $proposal['client_email'] ?? ($request['email'] ?? ''),
    'client_username' => $proposal['client_username'] ?? ($request['client_username'] ?? ''),
    'project_title' => $proposal['project_title'] ?? ($request['project_type'] ?? ''),
    'request_summary' => $proposal['request_summary'] ?? ($request['description'] ?? ''),
    'proposed_work' => $proposal['proposed_work'] ?? '',
    'deliverables' => $proposal['deliverables'] ?? '',
    'estimated_cost' => $proposal['estimated_cost'] ?? ($request['estimated_cost_range'] ?? ''),
    'estimated_time' => $proposal['estimated_time'] ?? ($request['estimated_time_range'] ?? ''),
    'payment_required_to_begin' => $proposal['payment_required_to_begin'] ?? '',
    'revision_terms' => $proposal['revision_terms'] ?? '',
    'assumptions' => $proposal['assumptions'] ?? '',
    'customer_responsibilities' => $proposal['customer_responsibilities'] ?? '',
    'next_steps' => $proposal['next_steps'] ?? ($request['recommended_next_step'] ?? ''),
    'status' => $proposal['status'] ?? 'draft',
    'repo_link' => $proposal['repo_link'] ?? ($request['repo_link'] ?? ''),
    'timeline' => $proposal['timeline'] ?? ($request['timeline'] ?? ''),
    'budget_comfort' => $proposal['budget_comfort'] ?? ($request['budget_comfort'] ?? ''),
    'staff_summary' => $proposal['staff_summary'] ?? ($request['staff_summary'] ?? ''),
];

$form = $defaults;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($form as $key => $value) {
        $form[$key] = trim((string)($_POST[$key] ?? ''));
    }
    if ($form['proposal_id'] === '') {
        $form['proposal_id'] = generateProposalId($proposals);
    }

    if ($form['request_id'] === '' || $form['client_name'] === '' || $form['client_email'] === '') {
        $error = 'Request ID, client name, and client email are required.';
    } else {
        $action = trim((string)($_POST['action'] ?? 'save_draft'));
        $form['status'] = ($action === 'send_email') ? 'sent' : ($proposalStatuses[$form['status']] ?? false ? $form['status'] : 'draft');

        $record = $form;
        $record['updated_at'] = date('c');
        if (empty($record['created_at'])) {
            $record['created_at'] = date('c');
        }

        $found = false;
        foreach ($proposals as &$item) {
            if ((string)($item['proposal_id'] ?? '') === $record['proposal_id']) {
                $record['created_at'] = (string)($item['created_at'] ?? $record['created_at']);
                $item = $record;
                $found = true;
                break;
            }
        }
        unset($item);
        if (!$found) {
            $proposals[] = $record;
        }
        portalSaveProposals($proposals);

        foreach ($requests as &$req) {
            if (portalGetRequestDisplayId((array)$req) !== $record['request_id']) {
                continue;
            }
            $ids = isset($req['proposal_ids']) && is_array($req['proposal_ids']) ? $req['proposal_ids'] : [];
            if (!in_array($record['proposal_id'], $ids, true)) {
                $ids[] = $record['proposal_id'];
            }
            $req['proposal_ids'] = array_values($ids);
            if ($action === 'send_email') {
                $req['status'] = 'proposal_sent';
            } elseif (($req['status'] ?? '') === 'new' || ($req['status'] ?? '') === 'reviewing') {
                $req['status'] = 'proposal_drafted';
            }
            break;
        }
        unset($req);
        portalSaveProjectRequests($requests);

        $host = $_SERVER['HTTP_HOST'] ?? 'runlevel.systems';
        $clientLink = 'https://' . $host . '/client/proposals.php?proposal_id=' . urlencode($record['proposal_id']);
        if ($action === 'send_email') {
            send_project_proposal_email($record['client_email'], $record['client_name'], $record['request_id'], $clientLink);
            $notice = 'Proposal saved and sent by email.';
        } else {
            $notice = 'Proposal draft saved.';
        }

        $proposal = $record;
        $form = $record;
    }
}

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
    <title>Create Proposal | Staff | Runlevel Systems</title>
    <link href="../assets/css/coreloop.css" rel="stylesheet">
    <style>
        .wrap{padding:28px 0 70px;}
        .card{background:#0c1729;border:1px solid rgba(54,243,255,.18);border-radius:10px;padding:18px;margin-bottom:14px;}
        .grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;}
        @media(max-width:760px){.grid{grid-template-columns:1fr;}}
        label{color:#a8bedc;font-size:.78rem;display:block;margin-bottom:4px;text-transform:uppercase;letter-spacing:.04em;}
        .input,.textarea,.select{background:#09111d;color:#eaf3ff;border:1px solid rgba(54,243,255,.25);border-radius:6px;padding:8px 10px;width:100%;font-size:.86rem;}
        .textarea{min-height:110px;resize:vertical;}
        .btn{display:inline-block;border-radius:5px;padding:8px 12px;font-size:.82rem;border:none;font-weight:700;text-decoration:none;cursor:pointer;}
        .btn-blue{background:#0a84ff;color:#fff;}
        .btn-gold{background:#ffc600;color:#08111f;}
        .btn-teal{background:rgba(54,243,255,.12);border:1px solid rgba(54,243,255,.3);color:#36f3ff;}
        .notice{background:rgba(34,197,94,.16);border:1px solid rgba(34,197,94,.35);color:#86efac;border-radius:6px;padding:10px 12px;margin-bottom:10px;font-size:.84rem;}
        .error{background:rgba(239,68,68,.16);border:1px solid rgba(239,68,68,.35);color:#fecaca;border-radius:6px;padding:10px 12px;margin-bottom:10px;font-size:.84rem;}
        @media print {
            #header, #footer-widget, footer, .no-print, button, a.btn { display:none !important; }
            body { background:#fff; color:#000; }
            .card { border:1px solid #ccc; background:#fff; }
            .input,.textarea,.select { border:1px solid #ccc; color:#000; background:#fff; }
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>
<section class="wrap">
<div class="container">
    <a class="no-print" href="/staff/estimate-requests.php<?php echo $requestRecordId !== '' ? '?view=' . urlencode($requestRecordId) : ''; ?>" style="color:#36f3ff;font-size:.84rem;">← Back To Request</a>

    <div class="card">
        <div style="display:flex;justify-content:space-between;gap:10px;align-items:center;flex-wrap:wrap;" class="no-print">
            <h1 style="margin:0;color:#ffc600;font-size:1.25rem;">Project Proposal</h1>
            <button type="button" class="btn btn-teal" onclick="window.print()">Print / Save PDF</button>
        </div>
        <p style="color:#7a9ac0;font-size:.85rem;margin:8px 0 0;">Runlevel Systems · Request ID: <strong style="color:#ffc600;"><?php echo pe($form['request_id']); ?></strong></p>
    </div>

    <?php if ($notice !== ''): ?><div class="notice"><?php echo pe($notice); ?></div><?php endif; ?>
    <?php if ($error !== ''): ?><div class="error"><?php echo pe($error); ?></div><?php endif; ?>

    <form method="post" class="card">
        <div class="grid">
            <div><label>Proposal ID</label><input class="input" type="text" name="proposal_id" value="<?php echo pe($form['proposal_id']); ?>" readonly></div>
            <div><label>Request ID</label><input class="input" type="text" name="request_id" value="<?php echo pe($form['request_id']); ?>" readonly></div>
            <div><label>Client Name</label><input class="input" type="text" name="client_name" value="<?php echo pe($form['client_name']); ?>"></div>
            <div><label>Client Email</label><input class="input" type="email" name="client_email" value="<?php echo pe($form['client_email']); ?>"></div>
            <div><label>Project Name / Title</label><input class="input" type="text" name="project_title" value="<?php echo pe($form['project_title']); ?>"></div>
            <div><label>Proposal Status</label><select class="select" name="status"><?php foreach ($proposalStatuses as $k=>$v): ?><option value="<?php echo pe($k); ?>" <?php echo $form['status'] === $k ? 'selected' : ''; ?>><?php echo pe($v); ?></option><?php endforeach; ?></select></div>
            <div><label>Timeline</label><input class="input" type="text" name="timeline" value="<?php echo pe($form['timeline']); ?>"></div>
            <div><label>Budget Comfort</label><input class="input" type="text" name="budget_comfort" value="<?php echo pe($form['budget_comfort']); ?>"></div>
            <div><label>Repository Link</label><input class="input" type="url" name="repo_link" value="<?php echo pe($form['repo_link']); ?>"></div>
            <div><label>Payment Required To Begin</label><input class="input" type="text" name="payment_required_to_begin" value="<?php echo pe($form['payment_required_to_begin']); ?>"></div>
            <div><label>Estimated Cost</label><input class="input" type="text" name="estimated_cost" value="<?php echo pe($form['estimated_cost']); ?>"></div>
            <div><label>Estimated Time</label><input class="input" type="text" name="estimated_time" value="<?php echo pe($form['estimated_time']); ?>"></div>
        </div>

        <div style="margin-top:10px;"><label>Staff Summary</label><textarea class="textarea" name="staff_summary"><?php echo pe($form['staff_summary']); ?></textarea></div>
        <div style="margin-top:10px;"><label>Request Summary</label><textarea class="textarea" name="request_summary"><?php echo pe($form['request_summary']); ?></textarea></div>
        <div style="margin-top:10px;"><label>Proposed Work</label><textarea class="textarea" name="proposed_work"><?php echo pe($form['proposed_work']); ?></textarea></div>
        <div style="margin-top:10px;"><label>Deliverables</label><textarea class="textarea" name="deliverables"><?php echo pe($form['deliverables']); ?></textarea></div>
        <div style="margin-top:10px;"><label>Revision Terms</label><textarea class="textarea" name="revision_terms"><?php echo pe($form['revision_terms']); ?></textarea></div>
        <div style="margin-top:10px;"><label>Assumptions</label><textarea class="textarea" name="assumptions"><?php echo pe($form['assumptions']); ?></textarea></div>
        <div style="margin-top:10px;"><label>Customer Responsibilities</label><textarea class="textarea" name="customer_responsibilities"><?php echo pe($form['customer_responsibilities']); ?></textarea></div>
        <div style="margin-top:10px;"><label>Next Steps</label><textarea class="textarea" name="next_steps"><?php echo pe($form['next_steps']); ?></textarea></div>

        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;" class="no-print">
            <button class="btn btn-blue" type="submit" name="action" value="save_draft">Save Draft</button>
            <button class="btn btn-gold" type="submit" name="action" value="send_email">Send By Email</button>
            <?php if ($form['proposal_id'] !== ''): ?>
                <a class="btn btn-teal" href="/staff/create-agreement.php?proposal_id=<?php echo urlencode($form['proposal_id']); ?>">Create Project Agreement</a>
            <?php endif; ?>
            <a class="btn btn-teal" href="/staff/estimate-requests.php">Back To Request</a>
        </div>
    </form>
</div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="../assets/js/jquery-1.12.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>
