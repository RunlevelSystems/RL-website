<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';
require_once __DIR__ . '/../includes/email.php';

portalRequireLogin();
if (portalGetRole() !== 'client') {
    header('Location: /dashboard.php');
    exit;
}

$user     = portalGetUser();
$username = (string)($user['username'] ?? '');

$proposalId = trim((string)($_GET['proposal_id'] ?? ''));
$proposals  = portalLoadProposals();
$requests   = portalLoadProjectRequests();

$proposal = null;
foreach ($proposals as $row) {
    if ((string)($row['proposal_id'] ?? '') !== $proposalId) { continue; }
    // Check ownership
    if ((string)($row['client_username'] ?? '') === $username) {
        $proposal = $row;
        break;
    }
    foreach ($requests as $req) {
        if ((string)($req['client_username'] ?? '') === $username
            && portalGetRequestDisplayId((array)$req) === (string)($row['request_id'] ?? '')) {
            $proposal = $row;
            break 2;
        }
    }
}

if ($proposal === null) {
    http_response_code(404);
}

$status = '';
if ($proposal !== null) {
    $status = (string)($proposal['proposal_status'] ?? $proposal['status'] ?? 'sent');
}

// Block draft proposals from client view
if ($proposal !== null && in_array($status, ['draft', 'cancelled'], true)) {
    $proposal = null;
    http_response_code(404);
}

$notice = '';
$error  = '';

// Handle POST actions
if ($proposal !== null && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = trim((string)($_POST['action'] ?? ''));

    if ($action === 'accept') {
        $agreed = ($_POST['agreed'] ?? '') === '1';
        if (!$agreed) {
            $error = 'You must check the agreement checkbox to accept this proposal.';
        } elseif (!in_array($status, ['sent', 'changes_requested'], true)) {
            $error = 'This proposal cannot be accepted in its current status.';
        } else {
            $now = date('c');
            $proposals = portalLoadProposals();
            foreach ($proposals as &$p) {
                if ((string)($p['proposal_id'] ?? '') !== $proposalId) { continue; }
                $p['proposal_status'] = 'accepted';
                $p['accepted_at']     = $now;
                $p['updated_at']      = $now;
                $proposal = $p;
                break;
            }
            unset($p);
            portalSaveProposals($proposals);
            $status = 'accepted';
            $host = $_SERVER['HTTP_HOST'] ?? 'runlevel.systems';
            $projectUrl = 'https://' . $host . '/client/proposal-view.php?proposal_id=' . urlencode($proposalId);
            send_proposal_accepted_customer_email(
                (string)($proposal['client_email'] ?? ''),
                (string)($proposal['client_name'] ?? ''),
                (string)($proposal['request_id'] ?? ''),
                (string)($proposal['proposal_id'] ?? ''),
                $projectUrl
            );
            send_proposal_accepted_staff_email(
                (string)($proposal['request_id'] ?? ''),
                (string)($proposal['proposal_id'] ?? ''),
                (string)($proposal['client_name'] ?? ''),
                'https://' . $host . '/staff/proposal-view.php?proposal_id=' . urlencode($proposalId)
            );
            $notice = 'Proposal accepted. We will follow up with payment instructions.';
        }
    } elseif ($action === 'request_changes') {
        $message = trim((string)($_POST['change_message'] ?? ''));
        if ($message === '') {
            $error = 'Please describe what you would like changed.';
        } else {
            $cr = [
                'date'                  => date('Y-m-d H:i:s'),
                'message'               => $message,
                'requested_budget'      => trim((string)($_POST['requested_budget']      ?? '')),
                'requested_timeline'    => trim((string)($_POST['requested_timeline']    ?? '')),
                'requested_deliverable' => trim((string)($_POST['requested_deliverable'] ?? '')),
                'status'                => 'open',
            ];
            $now = date('c');
            $proposals = portalLoadProposals();
            foreach ($proposals as &$p) {
                if ((string)($p['proposal_id'] ?? '') !== $proposalId) { continue; }
                $existing = (isset($p['change_requests']) && is_array($p['change_requests'])) ? $p['change_requests'] : [];
                $existing[] = $cr;
                $p['change_requests']      = $existing;
                $p['proposal_status']      = 'changes_requested';
                $p['change_requested_at']  = $now;
                $p['updated_at']           = $now;
                $proposal = $p;
                break;
            }
            unset($p);
            portalSaveProposals($proposals);
            $status = 'changes_requested';
            $host = $_SERVER['HTTP_HOST'] ?? 'runlevel.systems';
            send_proposal_change_requested_email(
                (string)($proposal['proposal_id'] ?? ''),
                (string)($proposal['request_id'] ?? ''),
                (string)($proposal['client_name'] ?? ''),
                $message,
                'https://' . $host . '/staff/proposal-view.php?proposal_id=' . urlencode($proposalId)
            );
            $notice = 'Your change request has been submitted. We will review and update the proposal.';
        }
    }
}

$settings = portalLoadAdminSettings();
$paypalMode = (string)($settings['paypal']['mode'] ?? 'manual');
$paypalLink = '';
if ($paypalMode === 'live') {
    $paypalLink = (string)($settings['paypal']['live']['payment_link'] ?? '');
} elseif ($paypalMode === 'sandbox') {
    $paypalLink = (string)($settings['paypal']['sandbox']['payment_link'] ?? '');
}

$milestoneStatuses = [
    'not_started'       => 'Not Started',
    'in_progress'       => 'In Progress',
    'ready_for_review'  => 'Ready for Review',
    'payment_due'       => 'Payment Due',
    'paid'              => 'Paid',
    'complete'          => 'Complete',
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
    <title>Proposal | Client Portal | Runlevel Systems</title>
    <link href="../assets/css/coreloop.css" rel="stylesheet">
    <style>
        .wrap{padding:30px 0 80px;}
        .card{background:#0c1729;border:1px solid rgba(54,243,255,.18);border-radius:10px;padding:18px;margin-bottom:14px;}
        .lbl{color:#5a7a9e;font-size:.7rem;text-transform:uppercase;letter-spacing:.04em;margin-bottom:3px;}
        .val{color:#d8e7f7;font-size:.88rem;white-space:pre-wrap;}
        .section-label{color:#36f3ff;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin:14px 0 8px;padding-bottom:4px;border-bottom:1px solid rgba(54,243,255,.12);}
        .notice{background:rgba(34,197,94,.16);border:1px solid rgba(34,197,94,.35);color:#86efac;border-radius:6px;padding:12px 14px;margin-bottom:14px;font-size:.86rem;}
        .error{background:rgba(239,68,68,.16);border:1px solid rgba(239,68,68,.35);color:#fecaca;border-radius:6px;padding:12px 14px;margin-bottom:14px;font-size:.86rem;}
        .milestone-card{background:#06101d;border:1px solid rgba(54,243,255,.12);border-radius:8px;padding:12px;margin-bottom:8px;}
        .milestone-title{color:#ffc600;font-size:.82rem;font-weight:700;margin-bottom:8px;}
        .btn{display:inline-block;border-radius:5px;padding:8px 14px;font-size:.84rem;font-weight:700;text-decoration:none;border:none;cursor:pointer;}
        .btn-teal{background:rgba(54,243,255,.12);border:1px solid rgba(54,243,255,.3);color:#36f3ff;}
        .btn-gold{background:#ffc600;color:#08111f;}
        .btn-green{background:rgba(52,211,153,.14);border:1px solid rgba(52,211,153,.4);color:#34d399;}
        .action-card{border-color:rgba(54,243,255,.3);}
        .input,.textarea,.select{background:#09111d;color:#eaf3ff;border:1px solid rgba(54,243,255,.25);border-radius:6px;padding:8px 10px;width:100%;font-size:.86rem;}
        .textarea{min-height:100px;resize:vertical;}
        .status-badge{display:inline-block;padding:3px 12px;border-radius:999px;font-size:.72rem;font-weight:700;}
        .how-list{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:5px;}
        .how-list li{color:#7a9ac0;font-size:.82rem;padding-left:22px;position:relative;}
        .how-list li::before{content:attr(data-n);position:absolute;left:0;top:0;color:#36f3ff;font-weight:700;font-size:.72rem;}
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>
<section class="wrap">
<div class="container">
    <a href="/client/proposals.php" style="color:#36f3ff;font-size:.84rem;">← My Proposals</a>

    <?php if ($notice !== ''): ?><div class="notice" style="margin-top:10px;"><?php echo pe($notice); ?></div><?php endif; ?>
    <?php if ($error !== ''): ?><div class="error" style="margin-top:10px;"><?php echo pe($error); ?></div><?php endif; ?>

    <?php if ($proposal === null): ?>
        <div class="card" style="margin-top:10px;"><p style="color:#fecaca;">Proposal not found or not available.</p></div>
    <?php else: ?>

    <div class="card" style="margin-top:10px;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;flex-wrap:wrap;">
            <div>
                <h1 style="margin:0 0 4px;color:#ffc600;font-size:1.18rem;"><?php echo pe($proposal['project_title'] ?? 'Project Proposal'); ?></h1>
                <p style="margin:0;color:#7a9ac0;font-size:.8rem;">Runlevel Systems &mdash; Proposal ID: <?php echo pe($proposal['proposal_id'] ?? ''); ?></p>
            </div>
            <?php
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
                $statusLabel = $statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status));
                $statusColor = $statusColors[$status] ?? '#7a9ac0';
            ?>
            <span class="status-badge" style="background:<?php echo pe($statusColor); ?>18;border:1px solid <?php echo pe($statusColor); ?>44;color:<?php echo pe($statusColor); ?>;"><?php echo pe($statusLabel); ?></span>
        </div>
    </div>

    <div class="card">
        <div class="section-label">What You Asked For</div>
        <div class="val"><?php echo pe($proposal['request_summary'] ?? ''); ?></div>
    </div>

    <div class="card">
        <div class="section-label">Proposed Work</div>
        <div class="val"><?php echo pe($proposal['proposed_work'] ?? ''); ?></div>
        <?php if (!empty($proposal['deliverables'])): ?>
        <div class="section-label" style="margin-top:14px;">Deliverables</div>
        <div class="val"><?php echo pe($proposal['deliverables']); ?></div>
        <?php endif; ?>
        <?php if (!empty($proposal['out_of_scope'])): ?>
        <div class="section-label" style="margin-top:14px;">Not Included (Out of Scope)</div>
        <div class="val" style="color:#a8bedc;"><?php echo pe($proposal['out_of_scope']); ?></div>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="section-label">Timeline & Pricing</div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px;">
            <?php if (!empty($proposal['timeline_estimate'])): ?>
            <div><div class="lbl">Timeline</div><div class="val"><?php echo pe($proposal['timeline_estimate']); ?></div></div>
            <?php endif; ?>
            <?php if (!empty($proposal['total_price'])): ?>
            <div><div class="lbl">Total Project Amount</div><div class="val" style="color:#86efac;font-size:1.1rem;font-weight:700;">$<?php echo pe($proposal['total_price']); ?></div></div>
            <?php endif; ?>
            <?php if (!empty($proposal['amount_due_to_start'])): ?>
            <div><div class="lbl">Amount Due To Start</div><div class="val" style="color:#ffc600;font-size:1.1rem;font-weight:700;">$<?php echo pe($proposal['amount_due_to_start']); ?></div></div>
            <?php endif; ?>
        </div>
        <?php if (!empty($proposal['payment_terms'])): ?>
        <div style="margin-top:10px;"><div class="lbl">Payment Terms</div><div class="val" style="color:#a8bedc;"><?php echo pe($proposal['payment_terms']); ?></div></div>
        <?php endif; ?>
    </div>

    <?php $milestones = (isset($proposal['milestones']) && is_array($proposal['milestones'])) ? $proposal['milestones'] : []; ?>
    <?php if (!empty($milestones)): ?>
    <div class="card">
        <div class="section-label">Project Milestones</div>
        <p style="color:#7a9ac0;font-size:.82rem;margin:0 0 10px;">Amount due to start: <strong style="color:#ffc600;">$<?php echo pe($proposal['amount_due_to_start'] ?? '0'); ?></strong> — required before work begins.</p>
        <p style="color:#7a9ac0;font-size:.78rem;margin:0 0 10px;">Future milestone payments are only due when the related milestone is completed or ready for review, depending on the proposal terms.</p>
        <?php foreach ($milestones as $idx => $ms): ?>
        <div class="milestone-card">
            <div class="milestone-title">Milestone <?php echo $idx + 1; ?>: <?php echo pe($ms['milestone_name'] ?? ''); ?></div>
            <div style="display:flex;gap:16px;flex-wrap:wrap;font-size:.82rem;margin-bottom:6px;">
                <?php if (!empty($ms['milestone_amount'])): ?><div style="color:#86efac;">Amount Due Upon Completion: <strong>$<?php echo pe($ms['milestone_amount']); ?></strong></div><?php endif; ?>
                <div style="color:#36f3ff;"><?php echo pe($milestoneStatuses[$ms['milestone_status'] ?? 'not_started'] ?? 'Not Started'); ?></div>
            </div>
            <?php if (!empty($ms['milestone_description'])): ?><div class="val" style="font-size:.82rem;"><?php echo pe($ms['milestone_description']); ?></div><?php endif; ?>
            <?php if (!empty($ms['milestone_deliverables'])): ?><div style="margin-top:6px;"><div class="lbl">Deliverables</div><div class="val" style="font-size:.82rem;"><?php echo pe($ms['milestone_deliverables']); ?></div></div><?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($proposal['customer_responsibilities']) || !empty($proposal['third_party_costs']) || !empty($proposal['revision_terms'])): ?>
    <div class="card">
        <div class="section-label">Terms & Expectations</div>
        <?php if (!empty($proposal['customer_responsibilities'])): ?>
        <div style="margin-bottom:10px;"><div class="lbl">Your Responsibilities</div><div class="val" style="color:#a8bedc;"><?php echo pe($proposal['customer_responsibilities']); ?></div></div>
        <?php endif; ?>
        <?php if (!empty($proposal['third_party_costs'])): ?>
        <div style="margin-bottom:10px;"><div class="lbl">Third-Party Costs</div><div class="val" style="color:#a8bedc;"><?php echo pe($proposal['third_party_costs']); ?></div></div>
        <?php endif; ?>
        <?php if (!empty($proposal['revision_terms'])): ?>
        <div><div class="lbl">Revisions</div><div class="val" style="color:#a8bedc;"><?php echo pe($proposal['revision_terms']); ?></div></div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($status === 'accepted' || $status === 'payment_pending' || $status === 'paid_start'): ?>
    <div class="card" id="payment-section">
        <div class="section-label">Payment</div>
        <?php if ($status === 'paid_start'): ?>
        <p style="color:#34d399;font-weight:700;margin:0 0 8px;">✓ Starting payment received. Work will begin shortly.</p>
        <?php elseif ($status === 'accepted' || $status === 'payment_pending'): ?>
        <p style="color:#d8e7f7;font-size:.88rem;margin:0 0 10px;">To begin work, the starting payment listed above must be received.</p>
        <?php if (!empty($proposal['amount_due_to_start'])): ?>
        <div style="font-size:1.1rem;color:#ffc600;font-weight:700;margin-bottom:12px;">Amount Due To Start: $<?php echo pe($proposal['amount_due_to_start']); ?></div>
        <?php endif; ?>
        <?php if ($paypalMode === 'manual'): ?>
        <p style="color:#7a9ac0;font-size:.84rem;">Runlevel Systems will send a PayPal invoice or payment link after proposal acceptance.</p>
        <?php elseif ($paypalLink !== ''): ?>
        <a href="<?php echo pe($paypalLink); ?>" class="btn btn-gold" target="_blank" rel="noopener noreferrer">Pay With PayPal</a>
        <?php else: ?>
        <p style="color:#7a9ac0;font-size:.84rem;">Payment instructions will be sent to you by email.</p>
        <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if (in_array($status, ['sent', 'changes_requested'], true)): ?>

    <div class="card action-card" id="accept">
        <h2 style="margin:0 0 8px;color:#34d399;font-size:1rem;">Accept This Proposal</h2>
        <p style="color:#d8e7f7;font-size:.86rem;margin:0 0 12px;">By accepting this proposal, you are asking Runlevel Systems to begin work after the required starting payment is received.</p>
        <?php if (!empty($proposal['amount_due_to_start'])): ?>
        <div style="margin-bottom:12px;font-size:.92rem;color:#d8e7f7;">Amount Due To Start: <strong style="color:#ffc600;">$<?php echo pe($proposal['amount_due_to_start']); ?></strong></div>
        <?php endif; ?>
        <?php if (!empty($proposal['acceptance_statement'])): ?>
        <div style="background:rgba(52,211,153,.06);border:1px solid rgba(52,211,153,.18);border-radius:6px;padding:10px 12px;margin-bottom:12px;color:#a8bedc;font-size:.82rem;"><?php echo pe($proposal['acceptance_statement']); ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="hidden" name="action" value="accept">
            <label style="display:flex;align-items:flex-start;gap:8px;cursor:pointer;margin-bottom:12px;">
                <input type="checkbox" name="agreed" value="1" style="margin-top:2px;">
                <span style="color:#d8e7f7;font-size:.84rem;">I have reviewed the proposal and agree to the listed work, deliverables, payment terms, and project terms.</span>
            </label>
            <button class="btn btn-green" type="submit">Accept Proposal</button>
        </form>
    </div>

    <div class="card" id="request-changes">
        <h2 style="margin:0 0 8px;color:#ffc600;font-size:1rem;">Request Changes</h2>
        <p style="color:#7a9ac0;font-size:.84rem;margin:0 0 12px;">Tell us what you would like changed and we will review and update the proposal.</p>
        <form method="post">
            <input type="hidden" name="action" value="request_changes">
            <div style="margin-bottom:10px;">
                <label style="color:#a8bedc;font-size:.75rem;text-transform:uppercase;letter-spacing:.04em;display:block;margin-bottom:4px;">What would you like changed? *</label>
                <textarea class="textarea" name="change_message" required placeholder="Describe the changes you are requesting..."></textarea>
            </div>
            <div style="margin-bottom:10px;">
                <label style="color:#a8bedc;font-size:.75rem;text-transform:uppercase;letter-spacing:.04em;display:block;margin-bottom:4px;">Requested Budget Change (optional)</label>
                <input class="input" type="text" name="requested_budget" placeholder="e.g. Looking for something closer to $300">
            </div>
            <div style="margin-bottom:10px;">
                <label style="color:#a8bedc;font-size:.75rem;text-transform:uppercase;letter-spacing:.04em;display:block;margin-bottom:4px;">Requested Timeline Change (optional)</label>
                <input class="input" type="text" name="requested_timeline" placeholder="e.g. Need it done in 1 week">
            </div>
            <div style="margin-bottom:12px;">
                <label style="color:#a8bedc;font-size:.75rem;text-transform:uppercase;letter-spacing:.04em;display:block;margin-bottom:4px;">Deliverable Change (optional)</label>
                <input class="input" type="text" name="requested_deliverable" placeholder="e.g. Remove the mobile app part">
            </div>
            <button class="btn btn-gold" type="submit">Submit Change Request</button>
        </form>
    </div>

    <?php endif; ?>

    <?php endif; ?>
</div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="../assets/js/jquery-1.12.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>
