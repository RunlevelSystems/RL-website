<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';
require_once __DIR__ . '/../includes/email.php';

portalRequireStaff();

$proposalId = trim((string)($_GET['proposal_id'] ?? ''));
$proposals  = portalLoadProposals();

$proposal = null;
foreach ($proposals as $row) {
    if ((string)($row['proposal_id'] ?? '') === $proposalId) {
        $proposal = $row;
        break;
    }
}

$notice = '';
$error  = '';

if ($proposal === null) {
    $error = 'Proposal not found.';
}

if ($proposal !== null && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $confirmed = ($_POST['confirm'] ?? '') === '1';
    if (!$confirmed) {
        $error = 'Please check the confirmation box before sending.';
    } else {
        foreach ($proposals as &$p) {
            if ((string)($p['proposal_id'] ?? '') === $proposalId) {
                $p['proposal_status'] = 'sent';
                $p['sent_at']         = date('c');
                $p['updated_at']      = date('c');
                $proposal = $p;
                break;
            }
        }
        unset($p);
        portalSaveProposals($proposals);

        $requests = portalLoadProjectRequests();
        foreach ($requests as &$req) {
            if (portalGetRequestDisplayId((array)$req) !== (string)($proposal['request_id'] ?? '')) { continue; }
            $req['status'] = 'proposal_sent';
            $req['updated_at'] = date('c');
            break;
        }
        unset($req);
        portalSaveProjectRequests($requests);

        $host = $_SERVER['HTTP_HOST'] ?? 'runlevel.systems';
        $clientLink = 'https://' . $host . '/client/proposals.php';
        $sent = send_project_proposal_email(
            (string)($proposal['client_email'] ?? ''),
            (string)($proposal['client_name'] ?? ''),
            (string)($proposal['request_id'] ?? ''),
            $clientLink,
            (string)($proposal['proposal_id'] ?? '')
        );
        if ($sent) {
            $notice = 'Proposal sent to ' . htmlspecialchars((string)($proposal['client_email'] ?? ''), ENT_QUOTES, 'UTF-8') . '.';
        } else {
            $notice = 'Proposal status set to Sent. Email notification could not be delivered — check email settings.';
        }
    }
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
    <title>Send Proposal | Staff | Runlevel Systems</title>
    <link href="../assets/css/coreloop.css" rel="stylesheet">
    <style>
        .wrap{padding:28px 0 70px;}
        .card{background:#0c1729;border:1px solid rgba(54,243,255,.18);border-radius:10px;padding:18px;margin-bottom:14px;}
        .btn{display:inline-block;border-radius:5px;padding:8px 14px;font-size:.84rem;font-weight:700;text-decoration:none;border:none;cursor:pointer;}
        .btn-gold{background:#ffc600;color:#08111f;}
        .btn-teal{background:rgba(54,243,255,.12);border:1px solid rgba(54,243,255,.3);color:#36f3ff;}
        .notice{background:rgba(34,197,94,.16);border:1px solid rgba(34,197,94,.35);color:#86efac;border-radius:6px;padding:10px 12px;margin-bottom:10px;font-size:.84rem;}
        .error{background:rgba(239,68,68,.16);border:1px solid rgba(239,68,68,.35);color:#fecaca;border-radius:6px;padding:10px 12px;margin-bottom:10px;font-size:.84rem;}
        .lbl{color:#5a7a9e;font-size:.72rem;text-transform:uppercase;}
        .val{color:#d8e7f7;font-size:.88rem;}
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>
<section class="wrap">
<div class="container">
    <a href="/staff/proposals.php" style="color:#36f3ff;font-size:.84rem;">← All Proposals</a>

    <?php if ($notice !== ''): ?><div class="notice" style="margin-top:10px;"><?php echo pe($notice); ?></div><?php endif; ?>
    <?php if ($error !== ''): ?><div class="error" style="margin-top:10px;"><?php echo pe($error); ?></div><?php endif; ?>

    <?php if ($proposal !== null && $notice === ''): ?>
    <div class="card" style="margin-top:10px;">
        <h1 style="margin:0 0 12px;color:#ffc600;font-size:1.1rem;">Send Proposal to Customer</h1>
        <div style="margin-bottom:8px;"><span class="lbl">Proposal</span><br><span class="val"><?php echo pe($proposal['proposal_id'] ?? ''); ?> — <?php echo pe($proposal['project_title'] ?? ''); ?></span></div>
        <div style="margin-bottom:8px;"><span class="lbl">Customer</span><br><span class="val"><?php echo pe($proposal['client_name'] ?? ''); ?></span></div>
        <div style="margin-bottom:8px;"><span class="lbl">Customer Email</span><br><span class="val"><?php echo pe($proposal['client_email'] ?? ''); ?></span></div>
        <div style="margin-bottom:8px;"><span class="lbl">Total Price</span><br><span class="val" style="color:#86efac;"><?php echo $proposal['total_price'] !== '' ? '$' . pe($proposal['total_price'] ?? '') : '—'; ?></span></div>
        <div style="margin-bottom:14px;"><span class="lbl">Amount Due To Start</span><br><span class="val" style="color:#ffc600;"><?php echo $proposal['amount_due_to_start'] !== '' ? '$' . pe($proposal['amount_due_to_start'] ?? '') : '—'; ?></span></div>

        <form method="post">
            <label style="display:flex;align-items:flex-start;gap:8px;color:#d8e7f7;font-size:.86rem;cursor:pointer;">
                <input type="checkbox" name="confirm" value="1" style="margin-top:2px;">
                <span>I have reviewed this proposal and it is ready to send to the customer. Sending will set the status to <strong>Sent</strong> and make it visible in the client portal.</span>
            </label>
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;">
                <button class="btn btn-gold" type="submit">Send Proposal</button>
                <a class="btn btn-teal" href="/staff/proposal-view.php?proposal_id=<?php echo urlencode((string)($proposal['proposal_id'] ?? '')); ?>">Review First</a>
                <a class="btn btn-teal" href="/staff/proposal-edit.php?proposal_id=<?php echo urlencode((string)($proposal['proposal_id'] ?? '')); ?>">Edit Proposal</a>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <?php if ($notice !== ''): ?>
    <div class="card">
        <p style="margin:0;color:#d8e7f7;">The proposal has been sent. The customer will see it in their client dashboard.</p>
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;">
            <a class="btn btn-teal" href="/staff/proposals.php">All Proposals</a>
            <a class="btn btn-teal" href="/staff/proposal-view.php?proposal_id=<?php echo urlencode((string)($proposal['proposal_id'] ?? '')); ?>">View Proposal</a>
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
