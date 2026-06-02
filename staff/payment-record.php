<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';
require_once __DIR__ . '/../includes/email.php';

portalRequireStaff();

$proposals = portalLoadProposals();

$preloadProposalId = trim((string)($_GET['proposal_id'] ?? ''));

$paymentTypes = [
    'start_payment'   => 'Start Payment',
    'milestone_payment'=> 'Milestone Payment',
    'final_payment'   => 'Final Payment',
    'manual_payment'  => 'Manual Payment',
    'refund'          => 'Refund',
];

$paymentStatuses = [
    'pending'   => 'Pending',
    'received'  => 'Received',
    'failed'    => 'Failed',
    'refunded'  => 'Refunded',
    'disputed'  => 'Disputed',
];

$milestoneStatuses = [
    'not_started'       => 'Not Started',
    'in_progress'       => 'In Progress',
    'ready_for_review'  => 'Ready for Review',
    'payment_due'       => 'Payment Due',
    'paid'              => 'Paid',
    'complete'          => 'Complete',
];

$notice = '';
$error  = '';

$form = [
    'proposal_id'          => $preloadProposalId,
    'payment_type'         => 'start_payment',
    'milestone_index'      => '',
    'amount'               => '',
    'currency'             => 'USD',
    'payment_status'       => 'received',
    'paypal_transaction_id'=> '',
    'paypal_invoice_id'    => '',
    'paypal_payer_email'   => '',
    'environment'          => 'live',
    'notes'                => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (array_keys($form) as $key) {
        $form[$key] = trim((string)($_POST[$key] ?? ''));
    }

    if ($form['proposal_id'] === '' || $form['amount'] === '' || !is_numeric($form['amount'])) {
        $error = 'Proposal and a valid amount are required.';
    } else {
        // Find proposal
        $linkedProposal = null;
        foreach ($proposals as $p) {
            if ((string)($p['proposal_id'] ?? '') === $form['proposal_id']) {
                $linkedProposal = $p;
                break;
            }
        }
        if ($linkedProposal === null) {
            $error = 'Proposal not found.';
        }
    }

    if ($error === '') {
        $paymentId = portalGenerateUniquePaymentId();
        $now = date('c');

        $payment = [
            'payment_id'            => $paymentId,
            'proposal_id'           => $form['proposal_id'],
            'request_id'            => (string)($linkedProposal['request_id'] ?? ''),
            'client_id'             => (string)($linkedProposal['client_id'] ?? ''),
            'payment_type'          => $form['payment_type'],
            'milestone_index'       => $form['milestone_index'],
            'amount'                => $form['amount'],
            'currency'              => $form['currency'] !== '' ? $form['currency'] : 'USD',
            'payment_status'        => $form['payment_status'],
            'paypal_transaction_id' => $form['paypal_transaction_id'],
            'paypal_invoice_id'     => $form['paypal_invoice_id'],
            'paypal_payer_email'    => $form['paypal_payer_email'],
            'environment'           => $form['environment'],
            'notes'                 => $form['notes'],
            'created_at'            => $now,
            'recorded_at'           => $now,
            'recorded_by'           => (string)((portalGetUser())['username'] ?? 'staff'),
        ];

        $payments = portalLoadPayments();
        $payments[] = $payment;
        portalSavePayments($payments);

        // Update proposal status
        $proposalStatus = (string)($linkedProposal['proposal_status'] ?? $linkedProposal['status'] ?? 'accepted');
        $newStatus = $proposalStatus;

        if ($form['payment_status'] === 'received') {
            if ($form['payment_type'] === 'start_payment') {
                $newStatus = 'paid_start';
            } elseif ($form['payment_type'] === 'final_payment') {
                $newStatus = 'completed';
            }
        }

        $proposals = portalLoadProposals();
        foreach ($proposals as &$p) {
            if ((string)($p['proposal_id'] ?? '') !== $form['proposal_id']) { continue; }
            $p['proposal_status'] = $newStatus;
            $p['updated_at'] = $now;

            // Update milestone status if applicable
            if ($form['payment_type'] === 'milestone_payment' && $form['milestone_index'] !== '' && $form['payment_status'] === 'received') {
                $msIdx = (int)$form['milestone_index'];
                if (isset($p['milestones'][$msIdx])) {
                    $p['milestones'][$msIdx]['milestone_status'] = 'paid';
                }
            }
            break;
        }
        unset($p);
        portalSaveProposals($proposals);

        if ($form['payment_status'] === 'received') {
            $host = $_SERVER['HTTP_HOST'] ?? 'runlevel.systems';
            $projectUrl = 'https://' . $host . '/project.php?id=' . urlencode((string)($linkedProposal['request_id'] ?? ''));
            $amountLabel = '$' . number_format((float)$form['amount'], 2) . ' ' . strtoupper((string)($form['currency'] !== '' ? $form['currency'] : 'USD'));
            send_payment_recorded_customer_email(
                (string)($linkedProposal['client_email'] ?? ''),
                (string)($linkedProposal['client_name'] ?? ''),
                (string)($linkedProposal['request_id'] ?? ''),
                (string)($linkedProposal['proposal_id'] ?? ''),
                $paymentId,
                $amountLabel,
                ucfirst((string)$form['payment_status']),
                $projectUrl
            );
            send_payment_recorded_staff_email(
                (string)($linkedProposal['request_id'] ?? ''),
                (string)($linkedProposal['proposal_id'] ?? ''),
                $paymentId,
                $amountLabel,
                ucfirst((string)$form['payment_status']),
                $projectUrl
            );
        }

        $notice = 'Payment recorded. Payment ID: ' . $paymentId;
        $form = array_fill_keys(array_keys($form), '');
        $form['proposal_id']    = $preloadProposalId;
        $form['payment_type']   = 'start_payment';
        $form['payment_status'] = 'received';
        $form['environment']    = 'live';
        $form['currency']       = 'USD';
    }
}

// Build proposal options
$proposalOptions = [];
foreach ($proposals as $p) {
    $pid = (string)($p['proposal_id'] ?? '');
    if ($pid === '') { continue; }
    $proposalOptions[$pid] = $pid . ' — ' . ($p['project_title'] ?? '') . ' (' . ($p['client_name'] ?? '') . ')';
}

// Selected proposal for milestone list
$selectedProposal = null;
foreach ($proposals as $p) {
    if ((string)($p['proposal_id'] ?? '') === $form['proposal_id']) {
        $selectedProposal = $p;
        break;
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
    <title>Record Payment | Staff | Runlevel Systems</title>
    <link href="../assets/css/coreloop.css?v=clean-20260602" rel="stylesheet">
    <style>
        .wrap{padding:28px 0 70px;}
        .card{background:#0c1729;border:1px solid rgba(54,243,255,.18);border-radius:10px;padding:18px;margin-bottom:14px;}
        .grid2{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;}
        @media(max-width:640px){.grid2{grid-template-columns:1fr;}}
        label{color:#a8bedc;font-size:.75rem;display:block;margin-bottom:4px;text-transform:uppercase;letter-spacing:.04em;}
        .input,.textarea,.select{background:#09111d;color:#eaf3ff;border:1px solid rgba(54,243,255,.25);border-radius:6px;padding:8px 10px;width:100%;font-size:.86rem;}
        .textarea{min-height:80px;resize:vertical;}
        .btn{display:inline-block;border-radius:5px;padding:8px 14px;font-size:.84rem;font-weight:700;text-decoration:none;border:none;cursor:pointer;}
        .btn-gold{background:#ffc600;color:#08111f;}
        .btn-teal{background:rgba(54,243,255,.12);border:1px solid rgba(54,243,255,.3);color:#36f3ff;}
        .notice{background:rgba(34,197,94,.16);border:1px solid rgba(34,197,94,.35);color:#86efac;border-radius:6px;padding:10px 12px;margin-bottom:10px;font-size:.84rem;}
        .error{background:rgba(239,68,68,.16);border:1px solid rgba(239,68,68,.35);color:#fecaca;border-radius:6px;padding:10px 12px;margin-bottom:10px;font-size:.84rem;}
        .section-label{color:#36f3ff;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin:14px 0 8px;padding-bottom:4px;border-bottom:1px solid rgba(54,243,255,.12);}
        .milestone-field{display:none;}
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>
<section class="wrap">
<div class="container">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:10px;">
        <h1 style="margin:0;color:#ffc600;font-size:1.18rem;">Record Payment</h1>
        <a href="/staff/proposals.php" class="btn btn-teal">← Proposals</a>
    </div>

    <?php if ($notice !== ''): ?><div class="notice"><?php echo pe($notice); ?></div><?php endif; ?>
    <?php if ($error !== ''): ?><div class="error"><?php echo pe($error); ?></div><?php endif; ?>

    <div class="card">
        <p style="color:#7a9ac0;font-size:.82rem;margin:0 0 14px;">Manually record a PayPal or other payment for a proposal.</p>
        <form method="post" onsubmit="return confirm('Record this payment?');">
            <div class="section-label">Payment Details</div>
            <div class="grid2">
                <div>
                    <label>Proposal *</label>
                    <select class="select" name="proposal_id" required onchange="updateMilestoneField(this)">
                        <option value="">— Select Proposal —</option>
                        <?php foreach ($proposalOptions as $pid => $plabel): ?>
                        <option value="<?php echo pe($pid); ?>" <?php echo $form['proposal_id'] === $pid ? 'selected' : ''; ?>><?php echo pe($plabel); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Payment Type *</label>
                    <select class="select" name="payment_type" onchange="toggleMilestoneField(this.value)">
                        <?php foreach ($paymentTypes as $k => $v): ?>
                        <option value="<?php echo pe($k); ?>" <?php echo $form['payment_type'] === $k ? 'selected' : ''; ?>><?php echo pe($v); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div id="milestone-index-wrap" class="milestone-field">
                    <label>Milestone Number (0-based)</label>
                    <input class="input" type="number" name="milestone_index" value="<?php echo pe($form['milestone_index']); ?>" min="0" max="4" placeholder="0">
                </div>
                <div>
                    <label>Amount *</label>
                    <input class="input" type="number" name="amount" step="0.01" min="0.01" value="<?php echo pe($form['amount']); ?>" required placeholder="e.g. 250.00">
                </div>
                <div>
                    <label>Currency</label>
                    <input class="input" type="text" name="currency" value="<?php echo pe($form['currency'] !== '' ? $form['currency'] : 'USD'); ?>" maxlength="3">
                </div>
                <div>
                    <label>Payment Status</label>
                    <select class="select" name="payment_status">
                        <?php foreach ($paymentStatuses as $k => $v): ?>
                        <option value="<?php echo pe($k); ?>" <?php echo $form['payment_status'] === $k ? 'selected' : ''; ?>><?php echo pe($v); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="section-label" style="margin-top:14px;">PayPal Details</div>
            <div class="grid2">
                <div>
                    <label>PayPal Transaction ID</label>
                    <input class="input" type="text" name="paypal_transaction_id" value="<?php echo pe($form['paypal_transaction_id']); ?>" placeholder="Optional">
                </div>
                <div>
                    <label>PayPal Invoice ID</label>
                    <input class="input" type="text" name="paypal_invoice_id" value="<?php echo pe($form['paypal_invoice_id']); ?>" placeholder="Optional">
                </div>
                <div>
                    <label>Payer Email</label>
                    <input class="input" type="email" name="paypal_payer_email" value="<?php echo pe($form['paypal_payer_email']); ?>" placeholder="Optional">
                </div>
                <div>
                    <label>Environment</label>
                    <select class="select" name="environment">
                        <option value="live" <?php echo $form['environment'] === 'live' ? 'selected' : ''; ?>>Live</option>
                        <option value="sandbox" <?php echo $form['environment'] === 'sandbox' ? 'selected' : ''; ?>>Sandbox</option>
                        <option value="manual" <?php echo $form['environment'] === 'manual' ? 'selected' : ''; ?>>Manual / Other</option>
                    </select>
                </div>
            </div>

            <div style="margin-top:10px;">
                <label>Notes</label>
                <textarea class="textarea" name="notes"><?php echo pe($form['notes']); ?></textarea>
            </div>

            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:14px;">
                <button class="btn btn-gold" type="submit">Record Payment</button>
                <a class="btn btn-teal" href="/staff/proposals.php">Cancel</a>
            </div>
        </form>
    </div>
</div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="../assets/js/jquery-1.12.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script>
function toggleMilestoneField(type) {
    var wrap = document.getElementById('milestone-index-wrap');
    if (wrap) wrap.style.display = (type === 'milestone_payment') ? '' : 'none';
}
function updateMilestoneField(sel) {
    // Could be extended to dynamically load milestone list
}
// Init
toggleMilestoneField(document.querySelector('[name="payment_type"]').value);
</script>
</body>
</html>
