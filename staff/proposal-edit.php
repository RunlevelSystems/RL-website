<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';
require_once __DIR__ . '/../includes/email.php';

portalRequireStaff();

$proposals    = portalLoadProposals();
$requests     = portalLoadProjectRequests();

$proposalStatuses = [
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

$requestIdQuery  = trim((string)($_GET['request_id']  ?? ''));
$proposalIdQuery = trim((string)($_GET['proposal_id'] ?? ''));
$requestRecordId = '';

$request  = null;
$proposal = null;

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
$error  = '';

// Build defaults from existing proposal or request
$defaults = [
    'proposal_id'            => $proposal['proposal_id'] ?? '',
    'request_id'             => $requestIdQuery,
    'client_id'              => $proposal['client_id'] ?? ($request['client_id'] ?? ''),
    'client_name'            => $proposal['client_name'] ?? ($request['name'] ?? ''),
    'client_email'           => $proposal['client_email'] ?? ($request['email'] ?? ''),
    'client_username'        => $proposal['client_username'] ?? ($request['client_username'] ?? ''),
    'project_title'          => $proposal['project_title'] ?? ($request['project_type'] ?? ''),
    'proposal_status'        => $proposal['proposal_status'] ?? ($proposal['status'] ?? 'draft'),
    'request_summary'        => $proposal['request_summary'] ?? ($request['description'] ?? ''),
    'staff_review_notes'     => $proposal['staff_review_notes'] ?? ($proposal['staff_summary'] ?? ''),
    'proposed_work'          => $proposal['proposed_work'] ?? '',
    'deliverables'           => $proposal['deliverables'] ?? '',
    'out_of_scope'           => $proposal['out_of_scope'] ?? '',
    'timeline_estimate'      => $proposal['timeline_estimate'] ?? ($proposal['estimated_time'] ?? ($request['estimated_time_range'] ?? '')),
    'total_price'            => $proposal['total_price'] ?? ($proposal['estimated_cost'] ?? ($request['estimated_cost_range'] ?? '')),
    'amount_due_to_start'    => $proposal['amount_due_to_start'] ?? ($proposal['payment_required_to_begin'] ?? ''),
    'payment_terms'          => $proposal['payment_terms'] ?? "Start payment required before work begins.\nRemaining balance due after milestone completion or final delivery.",
    'customer_responsibilities' => $proposal['customer_responsibilities'] ?? "Customer must provide access, files, logins, repo links, screenshots, or other information needed to complete the work.",
    'third_party_costs'      => $proposal['third_party_costs'] ?? "Assets, hosting, app store fees, licenses, paid APIs, plugins, or marketplace tools are not included unless specifically listed.",
    'revision_terms'         => $proposal['revision_terms'] ?? "Small corrections related to agreed work are included. New requests or scope changes may require a revised proposal.",
    'acceptance_statement'   => $proposal['acceptance_statement'] ?? "By accepting this proposal, the customer agrees that Runlevel Systems may begin work after the required starting payment is received.",
    'requires_contract'      => $proposal['requires_contract'] ?? '0',
    'contract_link'          => $proposal['contract_link'] ?? '',
    'num_milestones'         => (string)($proposal['num_milestones'] ?? '0'),
    'created_at'             => $proposal['created_at'] ?? '',
    'updated_at'             => $proposal['updated_at'] ?? '',
    'sent_at'                => $proposal['sent_at'] ?? '',
    'accepted_at'            => $proposal['accepted_at'] ?? '',
    'change_requested_at'    => $proposal['change_requested_at'] ?? '',
];

// Milestones array
$existingMilestones = (isset($proposal['milestones']) && is_array($proposal['milestones'])) ? $proposal['milestones'] : [];

$form = $defaults;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = array_keys($form);
    foreach ($fields as $key) {
        $form[$key] = trim((string)($_POST[$key] ?? ''));
    }

    if ($form['proposal_id'] === '') {
        $form['proposal_id'] = portalGenerateUniqueProposalId();
    }

    if ($form['request_id'] === '' || $form['client_name'] === '' || $form['client_email'] === '') {
        $error = 'Request ID, client name, and client email are required.';
    } else {
        $action = trim((string)($_POST['action'] ?? 'save_draft'));

        // Status logic
        if ($action === 'send_to_customer') {
            $form['proposal_status'] = 'sent';
            $form['sent_at'] = date('c');
        } else {
            if (!isset($proposalStatuses[$form['proposal_status']])) {
                $form['proposal_status'] = 'draft';
            }
        }

        // Parse milestones
        $numMs = max(0, min(5, (int)$form['num_milestones']));
        $form['num_milestones'] = (string)$numMs;
        $milestones = [];
        for ($i = 0; $i < $numMs; $i++) {
            $milestones[] = [
                'milestone_name'        => trim((string)($_POST['ms_name_'       . $i] ?? '')),
                'milestone_description' => trim((string)($_POST['ms_desc_'       . $i] ?? '')),
                'milestone_deliverables'=> trim((string)($_POST['ms_deliv_'      . $i] ?? '')),
                'milestone_amount'      => trim((string)($_POST['ms_amount_'     . $i] ?? '')),
                'milestone_trigger'     => trim((string)($_POST['ms_trigger_'    . $i] ?? '')),
                'milestone_status'      => trim((string)($_POST['ms_status_'     . $i] ?? 'not_started')),
            ];
        }

        $record = $form;
        $record['milestones']  = $milestones;
        $record['updated_at']  = date('c');
        if (empty($record['created_at'])) {
            $record['created_at'] = date('c');
        }
        // Remove legacy field aliases
        unset($record['status']);

        $proposals = portalLoadProposals();
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

        // Update linked request
        $requests = portalLoadProjectRequests();
        foreach ($requests as &$req) {
            if (portalGetRequestDisplayId((array)$req) !== $record['request_id']) { continue; }
            $ids = isset($req['proposal_ids']) && is_array($req['proposal_ids']) ? $req['proposal_ids'] : [];
            if (!in_array($record['proposal_id'], $ids, true)) {
                $ids[] = $record['proposal_id'];
            }
            $req['proposal_ids'] = array_values($ids);
            if ($action === 'send_to_customer') {
                $req['status'] = 'proposal_sent';
            } elseif (in_array($req['status'] ?? '', ['new', 'reviewing'], true)) {
                $req['status'] = 'proposal_drafted';
            }
            break;
        }
        unset($req);
        portalSaveProjectRequests($requests);

        if ($action === 'send_to_customer') {
            $host = $_SERVER['HTTP_HOST'] ?? 'runlevel.systems';
            $clientLink = 'https://' . $host . '/client/proposal-view.php?proposal_id=' . urlencode($record['proposal_id']);
            send_project_proposal_email($record['client_email'], $record['client_name'], $record['request_id'], $clientLink, $record['proposal_id']);
            $notice = 'Proposal sent to customer.';
        } else {
            $notice = 'Proposal saved.';
        }

        $proposal = $record;
        $form = $record;
        $existingMilestones = $milestones;
    }
}

$numMsForm = max(0, min(5, (int)$form['num_milestones']));

$current_page = 'staff-portal';
$header_class = 'inner-header';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../assets/images/RL-icon.png">
    <title><?php echo $form['proposal_id'] !== '' ? 'Edit Proposal' : 'New Proposal'; ?> | Staff | Runlevel Systems</title>
    <link href="../assets/css/coreloop.css?v=clean-20260602" rel="stylesheet">
    <style>
        .wrap{padding:28px 0 80px;}
        .card{background:#0c1729;border:1px solid rgba(54,243,255,.18);border-radius:10px;padding:18px;margin-bottom:14px;}
        .grid2{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;}
        @media(max-width:760px){.grid2{grid-template-columns:1fr;}}
        label{color:#a8bedc;font-size:.75rem;display:block;margin-bottom:4px;text-transform:uppercase;letter-spacing:.04em;}
        .input,.textarea,.select{background:#09111d;color:#eaf3ff;border:1px solid rgba(54,243,255,.25);border-radius:6px;padding:8px 10px;width:100%;font-size:.86rem;}
        .textarea{min-height:100px;resize:vertical;}
        .textarea-sm{min-height:70px;}
        .btn{display:inline-block;border-radius:5px;padding:8px 12px;font-size:.82rem;border:none;font-weight:700;text-decoration:none;cursor:pointer;}
        .btn-blue{background:#0a84ff;color:#fff;}
        .btn-gold{background:#ffc600;color:#08111f;}
        .btn-teal{background:rgba(54,243,255,.12);border:1px solid rgba(54,243,255,.3);color:#36f3ff;}
        .notice{background:rgba(34,197,94,.16);border:1px solid rgba(34,197,94,.35);color:#86efac;border-radius:6px;padding:10px 12px;margin-bottom:10px;font-size:.84rem;}
        .error{background:rgba(239,68,68,.16);border:1px solid rgba(239,68,68,.35);color:#fecaca;border-radius:6px;padding:10px 12px;margin-bottom:10px;font-size:.84rem;}
        .section-label{color:#36f3ff;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin:18px 0 10px;padding-bottom:4px;border-bottom:1px solid rgba(54,243,255,.14);}
        .milestone-card{background:#06101d;border:1px solid rgba(54,243,255,.15);border-radius:8px;padding:14px;margin-bottom:10px;}
        .milestone-title{color:#ffc600;font-size:.8rem;font-weight:700;margin-bottom:10px;}
        #milestone-container .grid2{margin-top:8px;}
        .private-note{border-color:rgba(255,198,0,.25) !important;}
        @media print{.no-print{display:none!important;} body{background:#fff;color:#000;} .card{border:1px solid #ccc;background:#fff;}}
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>
<section class="wrap">
<div class="container">
    <div style="display:flex;justify-content:space-between;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:6px;" class="no-print">
        <a href="/staff/proposals.php" style="color:#36f3ff;font-size:.84rem;">← All Proposals</a>
        <button type="button" class="btn btn-teal" onclick="window.print()">Print / Save PDF</button>
    </div>

    <?php if ($notice !== ''): ?><div class="notice"><?php echo pe($notice); ?></div><?php endif; ?>
    <?php if ($error !== ''): ?><div class="error"><?php echo pe($error); ?></div><?php endif; ?>

    <form method="post">
        <div class="card">
            <h1 style="margin:0 0 4px;color:#ffc600;font-size:1.2rem;"><?php echo $form['proposal_id'] !== '' ? 'Edit Proposal' : 'New Proposal'; ?></h1>
            <p style="margin:0 0 14px;color:#7a9ac0;font-size:.82rem;">Runlevel Systems &mdash; Proposal Editor</p>

            <div class="section-label">Proposal Info</div>
            <div class="grid2">
                <div>
                    <label>Proposal ID</label>
                    <input class="input" type="text" name="proposal_id" value="<?php echo pe($form['proposal_id']); ?>" readonly placeholder="Auto-generated">
                </div>
                <div>
                    <label>Related Request ID</label>
                    <input class="input" type="text" name="request_id" value="<?php echo pe($form['request_id']); ?>" placeholder="RLS-YYYYMMDD-XXXX">
                </div>
                <div>
                    <label>Client Name</label>
                    <input class="input" type="text" name="client_name" value="<?php echo pe($form['client_name']); ?>" required>
                </div>
                <div>
                    <label>Client Email</label>
                    <input class="input" type="email" name="client_email" value="<?php echo pe($form['client_email']); ?>" required>
                </div>
                <div>
                    <label>Proposal Title</label>
                    <input class="input" type="text" name="project_title" value="<?php echo pe($form['project_title']); ?>" placeholder="e.g. Website Redesign Project">
                </div>
                <div>
                    <label>Status</label>
                    <select class="select" name="proposal_status">
                        <?php foreach ($proposalStatuses as $k => $v): ?>
                        <option value="<?php echo pe($k); ?>" <?php echo $form['proposal_status'] === $k ? 'selected' : ''; ?>><?php echo pe($v); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="section-label">Request & Work Details</div>

            <div style="margin-bottom:10px;">
                <label>Request Summary <span style="color:#5a7a9e;font-size:.7rem;">(what the customer asked for)</span></label>
                <textarea class="textarea" name="request_summary"><?php echo pe($form['request_summary']); ?></textarea>
            </div>

            <div style="margin-bottom:10px;">
                <label>Staff Review Notes <span style="color:#ffc600;font-size:.7rem;">PRIVATE — not visible to customer</span></label>
                <textarea class="textarea private-note" name="staff_review_notes"><?php echo pe($form['staff_review_notes']); ?></textarea>
            </div>

            <div style="margin-bottom:10px;">
                <label>Proposed Work <span style="color:#5a7a9e;font-size:.7rem;">(plain English explanation of what we will do)</span></label>
                <textarea class="textarea" name="proposed_work"><?php echo pe($form['proposed_work']); ?></textarea>
            </div>

            <div style="margin-bottom:10px;">
                <label>Deliverables <span style="color:#5a7a9e;font-size:.7rem;">(what the customer will receive)</span></label>
                <textarea class="textarea" name="deliverables"><?php echo pe($form['deliverables']); ?></textarea>
            </div>

            <div style="margin-bottom:10px;">
                <label>Out of Scope <span style="color:#5a7a9e;font-size:.7rem;">(what is not included unless separately approved)</span></label>
                <textarea class="textarea textarea-sm" name="out_of_scope"><?php echo pe($form['out_of_scope']); ?></textarea>
            </div>
        </div>

        <div class="card">
            <div class="section-label">Pricing & Timeline</div>
            <div class="grid2">
                <div>
                    <label>Timeline Estimate</label>
                    <input class="input" type="text" name="timeline_estimate" value="<?php echo pe($form['timeline_estimate']); ?>" placeholder="e.g. 1-2 weeks">
                </div>
                <div>
                    <label>Total Estimated Price ($)</label>
                    <input class="input" type="text" name="total_price" value="<?php echo pe($form['total_price']); ?>" placeholder="e.g. 500">
                </div>
                <div>
                    <label>Amount Due To Start ($)</label>
                    <input class="input" type="text" name="amount_due_to_start" value="<?php echo pe($form['amount_due_to_start']); ?>" placeholder="e.g. 250">
                </div>
            </div>
        </div>

        <div class="card">
            <div class="section-label">Milestones</div>
            <div style="margin-bottom:12px;">
                <label>Number of Milestones</label>
                <select class="select" name="num_milestones" id="num-milestones-select" style="max-width:160px;" onchange="updateMilestones(this.value)">
                    <?php for ($n = 0; $n <= 5; $n++): ?>
                    <option value="<?php echo $n; ?>" <?php echo $numMsForm === $n ? 'selected' : ''; ?>><?php echo $n === 0 ? '0 — Simple Job' : $n; ?></option>
                    <?php endfor; ?>
                </select>
                <p style="color:#7a9ac0;font-size:.78rem;margin:6px 0 0;">If 0: single start payment + final delivery. If 1+: milestone payments apply after start payment.</p>
            </div>

            <div id="milestone-container">
            <?php for ($i = 0; $i < max($numMsForm, 5); $i++):
                $ms = $existingMilestones[$i] ?? [];
                $display = $i < $numMsForm ? '' : 'none';
            ?>
                <div class="milestone-card" id="ms-block-<?php echo $i; ?>" style="display:<?php echo $display; ?>;">
                    <div class="milestone-title">Milestone <?php echo $i + 1; ?></div>
                    <div class="grid2">
                        <div>
                            <label>Milestone Name</label>
                            <input class="input" type="text" name="ms_name_<?php echo $i; ?>" value="<?php echo pe($ms['milestone_name'] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Amount Due ($)</label>
                            <input class="input" type="text" name="ms_amount_<?php echo $i; ?>" value="<?php echo pe($ms['milestone_amount'] ?? ''); ?>" placeholder="e.g. 150">
                        </div>
                        <div>
                            <label>Due / Trigger</label>
                            <input class="input" type="text" name="ms_trigger_<?php echo $i; ?>" value="<?php echo pe($ms['milestone_trigger'] ?? ''); ?>" placeholder="e.g. Upon completion of phase 1">
                        </div>
                        <div>
                            <label>Milestone Status</label>
                            <select class="select" name="ms_status_<?php echo $i; ?>">
                                <?php foreach ($milestoneStatuses as $mk => $mv): ?>
                                <option value="<?php echo pe($mk); ?>" <?php echo ($ms['milestone_status'] ?? 'not_started') === $mk ? 'selected' : ''; ?>><?php echo pe($mv); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div style="margin-top:8px;">
                        <label>Milestone Description</label>
                        <textarea class="textarea textarea-sm" name="ms_desc_<?php echo $i; ?>"><?php echo pe($ms['milestone_description'] ?? ''); ?></textarea>
                    </div>
                    <div style="margin-top:8px;">
                        <label>Milestone Deliverables</label>
                        <textarea class="textarea textarea-sm" name="ms_deliv_<?php echo $i; ?>"><?php echo pe($ms['milestone_deliverables'] ?? ''); ?></textarea>
                    </div>
                </div>
            <?php endfor; ?>
            </div>
        </div>

        <div class="card">
            <div class="section-label">Terms & Conditions</div>

            <div style="margin-bottom:10px;">
                <label>Payment Terms</label>
                <textarea class="textarea textarea-sm" name="payment_terms"><?php echo pe($form['payment_terms']); ?></textarea>
            </div>
            <div style="margin-bottom:10px;">
                <label>Customer Responsibilities</label>
                <textarea class="textarea textarea-sm" name="customer_responsibilities"><?php echo pe($form['customer_responsibilities']); ?></textarea>
            </div>
            <div style="margin-bottom:10px;">
                <label>Third-Party Costs</label>
                <textarea class="textarea textarea-sm" name="third_party_costs"><?php echo pe($form['third_party_costs']); ?></textarea>
            </div>
            <div style="margin-bottom:10px;">
                <label>Revision / Change Terms</label>
                <textarea class="textarea textarea-sm" name="revision_terms"><?php echo pe($form['revision_terms']); ?></textarea>
            </div>
            <div style="margin-bottom:10px;">
                <label>Acceptance Statement</label>
                <textarea class="textarea textarea-sm" name="acceptance_statement"><?php echo pe($form['acceptance_statement']); ?></textarea>
            </div>

            <div style="display:flex;align-items:center;gap:8px;margin-top:10px;">
                <input type="checkbox" id="requires_contract" name="requires_contract" value="1" <?php echo $form['requires_contract'] === '1' ? 'checked' : ''; ?> onchange="toggleContractField(this)">
                <label for="requires_contract" style="text-transform:none;letter-spacing:0;font-size:.84rem;cursor:pointer;">Requires a separate contract / project agreement</label>
            </div>
            <div id="contract-link-field" style="margin-top:8px;<?php echo $form['requires_contract'] !== '1' ? 'display:none;' : ''; ?>">
                <label>Contract Link or Reference</label>
                <input class="input" type="text" name="contract_link" value="<?php echo pe($form['contract_link']); ?>" placeholder="URL or reference number">
            </div>
        </div>

        <div class="card no-print">
            <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                <button class="btn btn-blue" type="submit" name="action" value="save_draft">Save Draft</button>
                <button class="btn btn-gold" type="submit" name="action" value="send_to_customer">Send To Customer</button>
                <?php if ($form['proposal_id'] !== ''): ?>
                <a class="btn btn-teal" href="/staff/proposal-view.php?proposal_id=<?php echo urlencode($form['proposal_id']); ?>">View Proposal</a>
                <?php endif; ?>
                <a class="btn btn-teal" href="/staff/proposals.php">All Proposals</a>
            </div>
        </div>
    </form>
</div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="../assets/js/jquery-1.12.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script>
function updateMilestones(num) {
    num = parseInt(num, 10);
    for (var i = 0; i < 5; i++) {
        var block = document.getElementById('ms-block-' + i);
        if (block) {
            block.style.display = (i < num) ? '' : 'none';
        }
    }
}
function toggleContractField(cb) {
    var field = document.getElementById('contract-link-field');
    if (field) field.style.display = cb.checked ? '' : 'none';
}
</script>
</body>
</html>
