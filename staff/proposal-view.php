<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';

portalRequireStaff();

$proposalId = trim((string)($_GET['proposal_id'] ?? ''));
$proposal   = null;
foreach (portalLoadProposals() as $row) {
    if ((string)($row['proposal_id'] ?? '') === $proposalId) {
        $proposal = $row;
        break;
    }
}

if ($proposal === null) {
    http_response_code(404);
}

$statusLabels = [
    'draft'            => 'Draft',
    'sent'             => 'Sent',
    'changes_requested'=> 'Changes Requested',
    'accepted'         => 'Accepted',
    'payment_pending'  => 'Payment Pending',
    'paid_start'       => 'Paid — Ready to Start',
    'in_progress'      => 'In Progress',
    'completed'        => 'Completed',
    'cancelled'        => 'Cancelled',
];

$milestoneStatuses = [
    'not_started'       => 'Not Started',
    'in_progress'       => 'In Progress',
    'ready_for_review'  => 'Ready for Review',
    'payment_due'       => 'Payment Due',
    'paid'              => 'Paid',
    'complete'          => 'Complete',
];

$changeRequests = (isset($proposal['change_requests']) && is_array($proposal['change_requests'])) ? $proposal['change_requests'] : [];

$current_page = 'staff-portal';
$header_class = 'inner-header';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../assets/images/RL-icon.png">
    <title>View Proposal | Staff | Runlevel Systems</title>
    <link href="../assets/css/coreloop.css" rel="stylesheet">
    <style>
        .wrap{padding:28px 0 70px;}
        .card{background:#0c1729;border:1px solid rgba(54,243,255,.18);border-radius:10px;padding:18px;margin-bottom:14px;}
        .lbl{color:#5a7a9e;font-size:.7rem;text-transform:uppercase;letter-spacing:.04em;margin-bottom:3px;}
        .val{color:#d8e7f7;font-size:.88rem;white-space:pre-wrap;}
        .mono{font-family:monospace;color:#ffc600;}
        .btn{display:inline-block;border-radius:5px;padding:6px 12px;font-size:.8rem;font-weight:700;text-decoration:none;border:none;cursor:pointer;}
        .btn-teal{background:rgba(54,243,255,.12);border:1px solid rgba(54,243,255,.3);color:#36f3ff;}
        .btn-gold{background:#ffc600;color:#08111f;}
        .btn-blue{background:#0a84ff;color:#fff;}
        .section-label{color:#36f3ff;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin:14px 0 8px;padding-bottom:4px;border-bottom:1px solid rgba(54,243,255,.12);}
        .field-row{margin-bottom:12px;}
        .milestone-card{background:#06101d;border:1px solid rgba(54,243,255,.12);border-radius:8px;padding:12px;margin-bottom:8px;}
        .milestone-title{color:#ffc600;font-size:.8rem;font-weight:700;margin-bottom:8px;}
        .cr-card{background:#0a1628;border:1px solid rgba(255,198,0,.2);border-radius:8px;padding:12px;margin-bottom:8px;}
        .private-badge{display:inline-block;background:rgba(255,198,0,.1);border:1px solid rgba(255,198,0,.3);color:#ffc600;border-radius:4px;padding:2px 6px;font-size:.65rem;font-weight:700;margin-left:6px;}
        @media print{.no-print{display:none!important;} body{background:#fff;color:#000;} .card{border:1px solid #ccc;background:#fff;}}
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>
<section class="wrap">
<div class="container">
    <div style="display:flex;justify-content:space-between;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:8px;" class="no-print">
        <a href="/staff/proposals.php" style="color:#36f3ff;font-size:.84rem;">← All Proposals</a>
        <button type="button" class="btn btn-teal" onclick="window.print()">Print / PDF</button>
    </div>

    <?php if ($proposal === null): ?>
        <div class="card"><p style="color:#fecaca;">Proposal not found.</p></div>
    <?php else: ?>

    <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;flex-wrap:wrap;">
            <div>
                <h1 style="margin:0;color:#ffc600;font-size:1.2rem;"><?php echo pe($proposal['project_title'] ?? 'Project Proposal'); ?></h1>
                <div style="color:#7a9ac0;font-size:.82rem;margin-top:3px;">Proposal ID: <span class="mono"><?php echo pe($proposal['proposal_id'] ?? ''); ?></span></div>
            </div>
            <?php
                $status = (string)($proposal['proposal_status'] ?? $proposal['status'] ?? 'draft');
                $label  = $statusLabels[$status] ?? ucfirst($status);
            ?>
            <span style="display:inline-block;padding:4px 12px;border-radius:999px;font-size:.75rem;font-weight:700;background:rgba(54,243,255,.1);border:1px solid rgba(54,243,255,.3);color:#36f3ff;"><?php echo pe($label); ?></span>
        </div>

        <div class="no-print" style="display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;">
            <a class="btn btn-blue" href="/staff/proposal-edit.php?proposal_id=<?php echo urlencode((string)($proposal['proposal_id'] ?? '')); ?>">Edit</a>
            <?php if (in_array($status, ['draft', 'changes_requested'], true)): ?>
            <a class="btn btn-gold" href="/staff/proposal-send.php?proposal_id=<?php echo urlencode((string)($proposal['proposal_id'] ?? '')); ?>">Send To Customer</a>
            <?php endif; ?>
            <?php if (in_array($status, ['accepted', 'payment_pending'], true)): ?>
            <a class="btn btn-gold" href="/staff/payment-record.php?proposal_id=<?php echo urlencode((string)($proposal['proposal_id'] ?? '')); ?>">Record Payment</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="section-label">Proposal Details</div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:10px;">
            <div class="field-row"><div class="lbl">Request ID</div><div class="val mono"><?php echo pe($proposal['request_id'] ?? ''); ?></div></div>
            <div class="field-row"><div class="lbl">Client Name</div><div class="val"><?php echo pe($proposal['client_name'] ?? ''); ?></div></div>
            <div class="field-row"><div class="lbl">Client Email</div><div class="val"><?php echo pe($proposal['client_email'] ?? ''); ?></div></div>
            <div class="field-row"><div class="lbl">Total Price</div><div class="val" style="color:#86efac;font-weight:700;"><?php echo $proposal['total_price'] !== '' ? '$' . pe($proposal['total_price'] ?? '') : '—'; ?></div></div>
            <div class="field-row"><div class="lbl">Amount Due To Start</div><div class="val" style="color:#ffc600;font-weight:700;"><?php echo $proposal['amount_due_to_start'] !== '' ? '$' . pe($proposal['amount_due_to_start'] ?? '') : '—'; ?></div></div>
            <div class="field-row"><div class="lbl">Timeline</div><div class="val"><?php echo pe($proposal['timeline_estimate'] ?? ''); ?></div></div>
            <div class="field-row"><div class="lbl">Created</div><div class="val"><?php $ts = strtotime((string)($proposal['created_at'] ?? '')); echo $ts ? pe(date('M j, Y g:i A', $ts)) : '—'; ?></div></div>
            <div class="field-row"><div class="lbl">Last Updated</div><div class="val"><?php $ts = strtotime((string)($proposal['updated_at'] ?? '')); echo $ts ? pe(date('M j, Y g:i A', $ts)) : '—'; ?></div></div>
            <div class="field-row"><div class="lbl">Sent At</div><div class="val"><?php $ts = strtotime((string)($proposal['sent_at'] ?? '')); echo $ts ? pe(date('M j, Y g:i A', $ts)) : '—'; ?></div></div>
            <div class="field-row"><div class="lbl">Accepted At</div><div class="val"><?php $ts = strtotime((string)($proposal['accepted_at'] ?? '')); echo $ts ? pe(date('M j, Y g:i A', $ts)) : '—'; ?></div></div>
        </div>
    </div>

    <div class="card">
        <div class="section-label">Content</div>
        <div class="field-row"><div class="lbl">Request Summary</div><div class="val"><?php echo pe($proposal['request_summary'] ?? ''); ?></div></div>
        <div class="field-row" style="margin-top:10px;"><div class="lbl">Staff Review Notes <span class="private-badge">PRIVATE</span></div><div class="val"><?php echo pe($proposal['staff_review_notes'] ?? ''); ?></div></div>
        <div class="field-row" style="margin-top:10px;"><div class="lbl">Proposed Work</div><div class="val"><?php echo pe($proposal['proposed_work'] ?? ''); ?></div></div>
        <div class="field-row" style="margin-top:10px;"><div class="lbl">Deliverables</div><div class="val"><?php echo pe($proposal['deliverables'] ?? ''); ?></div></div>
        <div class="field-row" style="margin-top:10px;"><div class="lbl">Out of Scope</div><div class="val"><?php echo pe($proposal['out_of_scope'] ?? ''); ?></div></div>
    </div>

    <?php $milestones = (isset($proposal['milestones']) && is_array($proposal['milestones'])) ? $proposal['milestones'] : []; ?>
    <?php if (!empty($milestones)): ?>
    <div class="card">
        <div class="section-label">Milestones</div>
        <p style="color:#7a9ac0;font-size:.8rem;margin:0 0 10px;">Amount Due To Start: <strong style="color:#ffc600;"><?php echo $proposal['amount_due_to_start'] !== '' ? '$' . pe($proposal['amount_due_to_start'] ?? '') : '—'; ?></strong> (due before work begins)</p>
        <?php foreach ($milestones as $idx => $ms): ?>
        <div class="milestone-card">
            <div class="milestone-title">Milestone <?php echo $idx + 1; ?>: <?php echo pe($ms['milestone_name'] ?? ''); ?></div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:8px;font-size:.82rem;">
                <div><div class="lbl">Amount</div><div style="color:#86efac;font-weight:700;"><?php echo $ms['milestone_amount'] !== '' ? '$' . pe($ms['milestone_amount'] ?? '') : '—'; ?></div></div>
                <div><div class="lbl">Trigger</div><div style="color:#d8e7f7;"><?php echo pe($ms['milestone_trigger'] ?? ''); ?></div></div>
                <div><div class="lbl">Status</div><div style="color:#36f3ff;"><?php echo pe($milestoneStatuses[$ms['milestone_status'] ?? 'not_started'] ?? 'Not Started'); ?></div></div>
            </div>
            <?php if (!empty($ms['milestone_description'])): ?><div style="margin-top:8px;"><div class="lbl">Description</div><div class="val"><?php echo pe($ms['milestone_description']); ?></div></div><?php endif; ?>
            <?php if (!empty($ms['milestone_deliverables'])): ?><div style="margin-top:8px;"><div class="lbl">Deliverables</div><div class="val"><?php echo pe($ms['milestone_deliverables']); ?></div></div><?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="section-label">Terms</div>
        <div class="field-row"><div class="lbl">Payment Terms</div><div class="val"><?php echo pe($proposal['payment_terms'] ?? ''); ?></div></div>
        <div class="field-row" style="margin-top:10px;"><div class="lbl">Customer Responsibilities</div><div class="val"><?php echo pe($proposal['customer_responsibilities'] ?? ''); ?></div></div>
        <div class="field-row" style="margin-top:10px;"><div class="lbl">Third-Party Costs</div><div class="val"><?php echo pe($proposal['third_party_costs'] ?? ''); ?></div></div>
        <div class="field-row" style="margin-top:10px;"><div class="lbl">Revision / Change Terms</div><div class="val"><?php echo pe($proposal['revision_terms'] ?? ''); ?></div></div>
        <div class="field-row" style="margin-top:10px;"><div class="lbl">Acceptance Statement</div><div class="val"><?php echo pe($proposal['acceptance_statement'] ?? ''); ?></div></div>
        <?php if ($proposal['requires_contract'] === '1'): ?>
        <div class="field-row" style="margin-top:10px;"><div class="lbl">Contract</div><div class="val"><?php echo pe($proposal['contract_link'] ?? ''); ?></div></div>
        <?php else: ?>
        <div class="field-row" style="margin-top:10px;"><div class="lbl">Governing Terms</div><div class="val" style="color:#7a9ac0;">This proposal is governed by the Runlevel Systems Terms of Service.</div></div>
        <?php endif; ?>
    </div>

    <?php if (!empty($changeRequests)): ?>
    <div class="card">
        <div class="section-label">Change Requests <span style="color:#ffc600;">(<?php echo count($changeRequests); ?>)</span></div>
        <?php foreach ($changeRequests as $cr): ?>
        <div class="cr-card">
            <div style="display:flex;justify-content:space-between;gap:8px;margin-bottom:6px;flex-wrap:wrap;">
                <span style="color:#ffc600;font-size:.75rem;font-weight:700;">Change Request</span>
                <span style="color:#7a9ac0;font-size:.72rem;"><?php echo pe($cr['date'] ?? ''); ?></span>
                <span style="display:inline-block;padding:1px 7px;border-radius:999px;font-size:.68rem;font-weight:700;background:rgba(251,191,36,.12);border:1px solid rgba(251,191,36,.3);color:#fbbf24;"><?php echo pe(ucfirst((string)($cr['status'] ?? 'open'))); ?></span>
            </div>
            <div style="color:#d8e7f7;font-size:.84rem;white-space:pre-wrap;"><?php echo pe($cr['message'] ?? ''); ?></div>
            <?php if (!empty($cr['requested_budget'])): ?><div style="margin-top:4px;color:#7a9ac0;font-size:.78rem;">Requested budget: <?php echo pe($cr['requested_budget']); ?></div><?php endif; ?>
            <?php if (!empty($cr['requested_timeline'])): ?><div style="color:#7a9ac0;font-size:.78rem;">Requested timeline: <?php echo pe($cr['requested_timeline']); ?></div><?php endif; ?>
            <?php if (!empty($cr['requested_deliverable'])): ?><div style="color:#7a9ac0;font-size:.78rem;">Deliverable change: <?php echo pe($cr['requested_deliverable']); ?></div><?php endif; ?>
        </div>
        <?php endforeach; ?>
        <a class="btn btn-blue no-print" href="/staff/proposal-edit.php?proposal_id=<?php echo urlencode((string)($proposal['proposal_id'] ?? '')); ?>">Review & Update Proposal →</a>
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
