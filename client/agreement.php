<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';

portalRequireLogin();
if (portalGetRole() !== 'client') {
    header('Location: /dashboard.php');
    exit;
}

$user = portalGetUser();
$username = (string)($user['username'] ?? '');
$agreementId = trim((string)($_GET['agreement_id'] ?? ''));

$agreement = null;
$clientRequestIds = [];
foreach (portalLoadProjectRequests() as $request) {
    if ((string)($request['client_username'] ?? '') === $username) {
        $clientRequestIds[] = portalGetRequestDisplayId((array)$request);
    }
}

foreach (portalLoadProjectAgreements() as $row) {
    if ((string)($row['agreement_id'] ?? '') !== $agreementId) {
        continue;
    }
    if ((string)($row['client_username'] ?? '') === $username || in_array((string)($row['request_id'] ?? ''), $clientRequestIds, true)) {
        $agreement = $row;
        break;
    }
}

if ($agreement === null) {
    http_response_code(404);
}

$current_page = 'client-portal';
$header_class = 'inner-header';
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Project Agreement | Client Portal</title><link href="../assets/css/coreloop.css?v=clean-20260602" rel="stylesheet">
<style>.wrap{padding:30px 0 70px;}.card{background:#0c1729;border:1px solid rgba(54,243,255,.18);border-radius:10px;padding:16px;} .lbl{color:#5a7a9e;font-size:.72rem;text-transform:uppercase;} .val{color:#eaf3ff;font-size:.86rem;white-space:pre-wrap;} .mono{font-family:monospace;color:#ffc600;}</style></head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>
<section class="wrap"><div class="container"><a href="/client/contracts.php" style="color:#36f3ff;font-size:.84rem;">← Back to project agreements</a><div class="card" style="margin-top:10px;">
<?php if ($agreement === null): ?><p style="color:#fecaca;">Project agreement not found.</p><?php else: ?>
<h2 style="margin:0 0 10px;color:#ffc600;font-size:1.1rem;">Project Agreement</h2>
<div class="lbl">Agreement ID</div><div class="val mono"><?php echo pe($agreement['agreement_id'] ?? ''); ?></div>
<div class="lbl" style="margin-top:8px;">Request ID</div><div class="val mono"><?php echo pe($agreement['request_id'] ?? ''); ?></div>
<div class="lbl" style="margin-top:8px;">Proposal ID</div><div class="val mono"><?php echo pe($agreement['proposal_id'] ?? ''); ?></div>
<div class="lbl" style="margin-top:8px;">Project Title</div><div class="val"><?php echo pe($agreement['project_title'] ?? ''); ?></div>
<div class="lbl" style="margin-top:8px;">Scope Of Work</div><div class="val"><?php echo pe($agreement['scope_of_work'] ?? ''); ?></div>
<div class="lbl" style="margin-top:8px;">Deliverables</div><div class="val"><?php echo pe($agreement['deliverables'] ?? ''); ?></div>
<div class="lbl" style="margin-top:8px;">Timeline</div><div class="val"><?php echo pe($agreement['timeline'] ?? ''); ?></div>
<div class="lbl" style="margin-top:8px;">Payment Terms</div><div class="val"><?php echo pe($agreement['payment_terms'] ?? ''); ?></div>
<div class="lbl" style="margin-top:8px;">Revision Terms</div><div class="val"><?php echo pe($agreement['revision_terms'] ?? ''); ?></div>
<div class="lbl" style="margin-top:8px;">Customer Responsibilities</div><div class="val"><?php echo pe($agreement['customer_responsibilities'] ?? ''); ?></div>
<?php endif; ?>
</div></div></section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="../assets/js/jquery-1.12.3.min.js"></script><script src="../assets/js/bootstrap.min.js"></script>
</body></html>
