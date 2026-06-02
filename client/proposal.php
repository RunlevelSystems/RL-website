<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';

// Forward to the new unified proposal view page
$proposalId = isset($_GET['proposal_id']) ? '?proposal_id=' . urlencode((string)$_GET['proposal_id']) : '';
header('Location: /client/proposal-view.php' . $proposalId);
exit;


$user = portalGetUser();
$username = (string)($user['username'] ?? '');
$proposalId = trim((string)($_GET['proposal_id'] ?? ''));

$proposal = null;
foreach (portalLoadProposals() as $row) {
    if ((string)($row['proposal_id'] ?? '') !== $proposalId) {
        continue;
    }
    if ((string)($row['client_username'] ?? '') === $username) {
        $proposal = $row;
        break;
    }
    foreach (portalLoadProjectRequests() as $request) {
        if ((string)($request['client_username'] ?? '') === $username && portalGetRequestDisplayId((array)$request) === (string)($row['request_id'] ?? '')) {
            $proposal = $row;
            break 2;
        }
    }
}

if ($proposal === null) {
    http_response_code(404);
}

$current_page = 'client-portal';
$header_class = 'inner-header';
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Proposal | Client Portal</title><link href="../assets/css/coreloop.css?v=clean-20260602" rel="stylesheet">
<style>.wrap{padding:30px 0 70px;}.card{background:#0c1729;border:1px solid rgba(54,243,255,.18);border-radius:10px;padding:16px;} .lbl{color:#5a7a9e;font-size:.72rem;text-transform:uppercase;} .val{color:#eaf3ff;font-size:.86rem;white-space:pre-wrap;} .mono{font-family:monospace;color:#ffc600;}</style></head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>
<section class="wrap"><div class="container"><a href="/client/proposals.php" style="color:#36f3ff;font-size:.84rem;">← Back to proposals</a><div class="card" style="margin-top:10px;">
<?php if ($proposal === null): ?><p style="color:#fecaca;">Proposal not found.</p><?php else: ?>
<h2 style="margin:0 0 10px;color:#ffc600;font-size:1.1rem;">Project Proposal</h2>
<div class="lbl">Proposal ID</div><div class="val mono"><?php echo pe($proposal['proposal_id'] ?? ''); ?></div>
<div class="lbl" style="margin-top:8px;">Request ID</div><div class="val mono"><?php echo pe($proposal['request_id'] ?? ''); ?></div>
<div class="lbl" style="margin-top:8px;">Project Title</div><div class="val"><?php echo pe($proposal['project_title'] ?? ''); ?></div>
<div class="lbl" style="margin-top:8px;">Status</div><div class="val"><?php echo pe(ucfirst((string)($proposal['status'] ?? 'draft'))); ?></div>
<div class="lbl" style="margin-top:8px;">Request Summary</div><div class="val"><?php echo pe($proposal['request_summary'] ?? ''); ?></div>
<div class="lbl" style="margin-top:8px;">Proposed Work</div><div class="val"><?php echo pe($proposal['proposed_work'] ?? ''); ?></div>
<div class="lbl" style="margin-top:8px;">Deliverables</div><div class="val"><?php echo pe($proposal['deliverables'] ?? ''); ?></div>
<div class="lbl" style="margin-top:8px;">Estimated Cost</div><div class="val"><?php echo pe($proposal['estimated_cost'] ?? ''); ?></div>
<div class="lbl" style="margin-top:8px;">Estimated Time</div><div class="val"><?php echo pe($proposal['estimated_time'] ?? ''); ?></div>
<div class="lbl" style="margin-top:8px;">Payment Required To Begin</div><div class="val"><?php echo pe($proposal['payment_required_to_begin'] ?? ''); ?></div>
<div class="lbl" style="margin-top:8px;">Next Steps</div><div class="val"><?php echo pe($proposal['next_steps'] ?? ''); ?></div>
<?php endif; ?>
</div></div></section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="../assets/js/jquery-1.12.3.min.js"></script><script src="../assets/js/bootstrap.min.js"></script>
</body></html>
