<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';
require_once __DIR__ . '/../includes/email.php';

portalRequireStaff();

function generateAgreementId(array $agreements) {
    $existing = [];
    foreach ($agreements as $agreement) {
        if (!empty($agreement['agreement_id'])) {
            $existing[(string)$agreement['agreement_id']] = true;
        }
    }
    $datePart = date('Ymd');
    do {
        $id = 'AGR-' . $datePart . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
    } while (isset($existing[$id]));
    return $id;
}

$requests = portalLoadProjectRequests();
$proposals = portalLoadProposals();
$agreements = portalLoadProjectAgreements();

$agreementIdQuery = trim((string)($_GET['agreement_id'] ?? ''));
$proposalIdQuery = trim((string)($_GET['proposal_id'] ?? ''));
$requestIdQuery = trim((string)($_GET['request_id'] ?? ''));

$proposal = null;
$agreement = null;
$request = null;

if ($agreementIdQuery !== '') {
    foreach ($agreements as $row) {
        if ((string)($row['agreement_id'] ?? '') === $agreementIdQuery) {
            $agreement = $row;
            $proposalIdQuery = (string)($row['proposal_id'] ?? $proposalIdQuery);
            $requestIdQuery = (string)($row['request_id'] ?? $requestIdQuery);
            break;
        }
    }
}

if ($proposalIdQuery !== '') {
    foreach ($proposals as $row) {
        if ((string)($row['proposal_id'] ?? '') === $proposalIdQuery) {
            $proposal = $row;
            if ($requestIdQuery === '') {
                $requestIdQuery = (string)($row['request_id'] ?? '');
            }
            break;
        }
    }
}

if ($requestIdQuery !== '') {
    foreach ($requests as $row) {
        if (portalGetRequestDisplayId((array)$row) === $requestIdQuery) {
            $request = portalNormalizeProjectRequest((array)$row);
            break;
        }
    }
}

$notice = '';
$error = '';

$defaults = [
    'agreement_id' => $agreement['agreement_id'] ?? '',
    'proposal_id' => $agreement['proposal_id'] ?? ($proposal['proposal_id'] ?? ''),
    'request_id' => $agreement['request_id'] ?? $requestIdQuery,
    'client_name' => $agreement['client_name'] ?? ($proposal['client_name'] ?? ($request['name'] ?? '')),
    'client_email' => $agreement['client_email'] ?? ($proposal['client_email'] ?? ($request['email'] ?? '')),
    'client_username' => $agreement['client_username'] ?? ($proposal['client_username'] ?? ($request['client_username'] ?? '')),
    'project_title' => $agreement['project_title'] ?? ($proposal['project_title'] ?? ($request['project_type'] ?? '')),
    'effective_date' => $agreement['effective_date'] ?? date('Y-m-d'),
    'scope_of_work' => $agreement['scope_of_work'] ?? ($proposal['proposed_work'] ?? ''),
    'deliverables' => $agreement['deliverables'] ?? ($proposal['deliverables'] ?? ''),
    'timeline' => $agreement['timeline'] ?? ($proposal['estimated_time'] ?? ($request['timeline'] ?? '')),
    'payment_terms' => $agreement['payment_terms'] ?? ($proposal['payment_required_to_begin'] ?? ''),
    'revision_terms' => $agreement['revision_terms'] ?? ($proposal['revision_terms'] ?? ''),
    'change_request_policy' => $agreement['change_request_policy'] ?? 'Changes outside scope require approval and may affect timeline and cost.',
    'customer_responsibilities' => $agreement['customer_responsibilities'] ?? ($proposal['customer_responsibilities'] ?? ''),
    'third_party_licenses' => $agreement['third_party_licenses'] ?? 'Client supplies or approves all required third-party licenses and assets.',
    'source_code_ownership_terms' => $agreement['source_code_ownership_terms'] ?? 'Ownership terms follow approved proposal and Runlevel Systems Terms of Service.',
    'testing_and_acceptance' => $agreement['testing_and_acceptance'] ?? 'Client reviews deliverables within agreed review window and confirms acceptance in writing.',
    'termination' => $agreement['termination'] ?? 'Either party may terminate with written notice; completed work remains billable.',
    'legal_notice' => $agreement['legal_notice'] ?? 'This agreement should be reviewed by legal counsel before final signature when required.',
    'signature_placeholder' => $agreement['signature_placeholder'] ?? 'Client Signature: ____________________   Date: __________',
    'status' => $agreement['status'] ?? 'draft',
];

$form = $defaults;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($form as $key => $value) {
        $form[$key] = trim((string)($_POST[$key] ?? ''));
    }

    if ($form['agreement_id'] === '') {
        $form['agreement_id'] = generateAgreementId($agreements);
    }

    if ($form['request_id'] === '' || $form['client_name'] === '' || $form['client_email'] === '') {
        $error = 'Request ID, client name, and client email are required.';
    } else {
        $action = trim((string)($_POST['action'] ?? 'save_draft'));
        if ($action === 'send_email') {
            $form['status'] = 'sent';
        } elseif ($form['status'] === '') {
            $form['status'] = 'draft';
        }

        $record = $form;
        $record['updated_at'] = date('c');
        if (empty($record['created_at'])) {
            $record['created_at'] = date('c');
        }

        $found = false;
        foreach ($agreements as &$item) {
            if ((string)($item['agreement_id'] ?? '') === $record['agreement_id']) {
                $record['created_at'] = (string)($item['created_at'] ?? $record['created_at']);
                $item = $record;
                $found = true;
                break;
            }
        }
        unset($item);
        if (!$found) {
            $agreements[] = $record;
        }
        portalSaveProjectAgreements($agreements);

        foreach ($requests as &$req) {
            if (portalGetRequestDisplayId((array)$req) !== $record['request_id']) {
                continue;
            }
            $ids = isset($req['agreement_ids']) && is_array($req['agreement_ids']) ? $req['agreement_ids'] : [];
            if (!in_array($record['agreement_id'], $ids, true)) {
                $ids[] = $record['agreement_id'];
            }
            $req['agreement_ids'] = array_values($ids);
            if ($action === 'send_email') {
                $req['status'] = 'accepted';
            }
            break;
        }
        unset($req);
        portalSaveProjectRequests($requests);

        if ($action === 'send_email') {
            $host = $_SERVER['HTTP_HOST'] ?? 'runlevelsystems.com';
            $clientLink = 'https://' . $host . '/client/contracts.php?agreement_id=' . urlencode($record['agreement_id']);
            send_project_agreement_email($record['client_email'], $record['client_name'], $record['request_id'], $clientLink);
            $notice = 'Project agreement saved and sent by email.';
        } else {
            $notice = 'Project agreement draft saved.';
        }

        $agreement = $record;
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
    <title>Create Project Agreement | Staff | Runlevel Systems</title>
    <link href="../assets/css/runlevel.css" rel="stylesheet">
    <style>
        .wrap{padding:28px 0 70px;}
        .card{background:#0c1729;border:1px solid rgba(54,243,255,.18);border-radius:10px;padding:18px;margin-bottom:14px;}
        .grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;}
        @media(max-width:760px){.grid{grid-template-columns:1fr;}}
        label{color:#a8bedc;font-size:.78rem;display:block;margin-bottom:4px;text-transform:uppercase;letter-spacing:.04em;}
        .input,.textarea{background:#09111d;color:#eaf3ff;border:1px solid rgba(54,243,255,.25);border-radius:6px;padding:8px 10px;width:100%;font-size:.86rem;}
        .textarea{min-height:92px;resize:vertical;}
        details{background:#09111d;border:1px solid rgba(54,243,255,.18);border-radius:8px;padding:8px 10px;margin-top:10px;}
        summary{cursor:pointer;color:#36f3ff;font-size:.84rem;font-weight:700;}
        .btn{display:inline-block;border-radius:5px;padding:8px 12px;font-size:.82rem;border:none;font-weight:700;text-decoration:none;cursor:pointer;}
        .btn-blue{background:#0a84ff;color:#fff;}
        .btn-gold{background:#ffc600;color:#08111f;}
        .btn-teal{background:rgba(54,243,255,.12);border:1px solid rgba(54,243,255,.3);color:#36f3ff;}
        .notice{background:rgba(34,197,94,.16);border:1px solid rgba(34,197,94,.35);color:#86efac;border-radius:6px;padding:10px 12px;margin-bottom:10px;font-size:.84rem;}
        .error{background:rgba(239,68,68,.16);border:1px solid rgba(239,68,68,.35);color:#fecaca;border-radius:6px;padding:10px 12px;margin-bottom:10px;font-size:.84rem;}
        @media print {
            #header, #footer-widget, footer, .no-print, button, a.btn, summary { display:none !important; }
            body { background:#fff; color:#000; }
            .card, details { border:1px solid #ccc; background:#fff; }
            .input,.textarea { border:1px solid #ccc; color:#000; background:#fff; }
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>
<section class="wrap">
<div class="container">
    <a class="no-print" href="/staff/estimate-requests.php" style="color:#36f3ff;font-size:.84rem;">← Back To Request</a>

    <div class="card">
        <div style="display:flex;justify-content:space-between;gap:10px;align-items:center;flex-wrap:wrap;" class="no-print">
            <h1 style="margin:0;color:#ffc600;font-size:1.25rem;">Project Agreement</h1>
            <button type="button" class="btn btn-teal" onclick="window.print()">Print / Save PDF</button>
        </div>
        <p style="color:#7a9ac0;font-size:.85rem;margin:8px 0 0;">Runlevel Systems · Request ID: <strong style="color:#ffc600;"><?php echo pe($form['request_id']); ?></strong></p>
    </div>

    <?php if ($notice !== ''): ?><div class="notice"><?php echo pe($notice); ?></div><?php endif; ?>
    <?php if ($error !== ''): ?><div class="error"><?php echo pe($error); ?></div><?php endif; ?>

    <form method="post" class="card">
        <div class="grid">
            <div><label>Agreement ID</label><input class="input" type="text" name="agreement_id" value="<?php echo pe($form['agreement_id']); ?>" readonly></div>
            <div><label>Proposal ID</label><input class="input" type="text" name="proposal_id" value="<?php echo pe($form['proposal_id']); ?>"></div>
            <div><label>Request ID</label><input class="input" type="text" name="request_id" value="<?php echo pe($form['request_id']); ?>" readonly></div>
            <div><label>Project Title</label><input class="input" type="text" name="project_title" value="<?php echo pe($form['project_title']); ?>"></div>
            <div><label>Client Name</label><input class="input" type="text" name="client_name" value="<?php echo pe($form['client_name']); ?>"></div>
            <div><label>Client Email</label><input class="input" type="email" name="client_email" value="<?php echo pe($form['client_email']); ?>"></div>
            <div><label>Effective Date</label><input class="input" type="date" name="effective_date" value="<?php echo pe($form['effective_date']); ?>"></div>
            <div><label>Status</label><input class="input" type="text" name="status" value="<?php echo pe($form['status']); ?>"></div>
            <div><label>Timeline</label><input class="input" type="text" name="timeline" value="<?php echo pe($form['timeline']); ?>"></div>
            <div><label>Payment Terms</label><input class="input" type="text" name="payment_terms" value="<?php echo pe($form['payment_terms']); ?>"></div>
        </div>

        <details open><summary>Scope & Deliverables</summary>
            <div style="margin-top:8px;"><label>Scope Of Work</label><textarea class="textarea" name="scope_of_work"><?php echo pe($form['scope_of_work']); ?></textarea></div>
            <div style="margin-top:8px;"><label>Deliverables</label><textarea class="textarea" name="deliverables"><?php echo pe($form['deliverables']); ?></textarea></div>
        </details>

        <details><summary>Terms</summary>
            <div style="margin-top:8px;"><label>Revision Terms</label><textarea class="textarea" name="revision_terms"><?php echo pe($form['revision_terms']); ?></textarea></div>
            <div style="margin-top:8px;"><label>Change Request Policy</label><textarea class="textarea" name="change_request_policy"><?php echo pe($form['change_request_policy']); ?></textarea></div>
            <div style="margin-top:8px;"><label>Customer Responsibilities</label><textarea class="textarea" name="customer_responsibilities"><?php echo pe($form['customer_responsibilities']); ?></textarea></div>
            <div style="margin-top:8px;"><label>Third-Party Licenses</label><textarea class="textarea" name="third_party_licenses"><?php echo pe($form['third_party_licenses']); ?></textarea></div>
            <div style="margin-top:8px;"><label>Source Code / Ownership Terms</label><textarea class="textarea" name="source_code_ownership_terms"><?php echo pe($form['source_code_ownership_terms']); ?></textarea></div>
            <div style="margin-top:8px;"><label>Testing And Acceptance</label><textarea class="textarea" name="testing_and_acceptance"><?php echo pe($form['testing_and_acceptance']); ?></textarea></div>
            <div style="margin-top:8px;"><label>Termination</label><textarea class="textarea" name="termination"><?php echo pe($form['termination']); ?></textarea></div>
            <div style="margin-top:8px;"><label>Legal Notice</label><textarea class="textarea" name="legal_notice"><?php echo pe($form['legal_notice']); ?></textarea></div>
            <div style="margin-top:8px;"><label>Signature Placeholder</label><textarea class="textarea" name="signature_placeholder"><?php echo pe($form['signature_placeholder']); ?></textarea></div>
        </details>

        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;" class="no-print">
            <button class="btn btn-blue" type="submit" name="action" value="save_draft">Save Draft</button>
            <button class="btn btn-gold" type="submit" name="action" value="send_email">Send By Email</button>
            <?php if ($form['proposal_id'] !== ''): ?>
                <a class="btn btn-teal" href="/staff/create-proposal.php?proposal_id=<?php echo urlencode($form['proposal_id']); ?>">Back To Proposal</a>
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
