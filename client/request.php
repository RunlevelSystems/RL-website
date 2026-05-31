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
$requestId = trim((string)($_GET['request_id'] ?? ''));

$request = null;
foreach (portalLoadProjectRequests() as $row) {
    if ((string)($row['client_username'] ?? '') !== $username) {
        continue;
    }
    if (portalGetRequestDisplayId((array)$row) === $requestId) {
        $request = portalNormalizeProjectRequest((array)$row);
        break;
    }
}

if ($request === null) {
    http_response_code(404);
}

$current_page = 'client-portal';
$header_class = 'inner-header';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Request Detail | Client Portal</title>
    <link href="../assets/css/runlevel.css" rel="stylesheet">
    <style>
        .wrap{padding:30px 0 70px;}.card{background:#0c1729;border:1px solid rgba(54,243,255,.18);border-radius:10px;padding:16px;}.grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;}@media(max-width:760px){.grid{grid-template-columns:1fr;}}
        .lbl{color:#5a7a9e;font-size:.72rem;text-transform:uppercase;} .val{color:#eaf3ff;font-size:.86rem;} .mono{font-family:monospace;color:#ffc600;}
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>
<section class="wrap"><div class="container"><a href="/client/requests.php" style="color:#36f3ff;font-size:.84rem;">← Back to requests</a><div class="card" style="margin-top:10px;">
<?php if ($request === null): ?>
<p style="color:#fecaca;">Request not found.</p>
<?php else: ?>
<h2 style="margin:0 0 10px;color:#ffc600;font-size:1.1rem;">Project Request Detail</h2>
<div class="grid">
<div><div class="lbl">Request ID</div><div class="val mono"><?php echo pe(portalGetRequestDisplayId($request)); ?></div></div>
<div><div class="lbl">Status</div><div class="val"><?php echo pe(ucfirst((string)($request['status'] ?? 'new'))); ?></div></div>
<div><div class="lbl">Project Type</div><div class="val"><?php echo pe($request['project_type'] ?? '—'); ?></div></div>
<div><div class="lbl">Project Stage</div><div class="val"><?php echo pe($request['project_stage'] ?? '—'); ?></div></div>
<div><div class="lbl">Project Size</div><div class="val"><?php echo pe($request['project_size'] ?? '—'); ?></div></div>
<div><div class="lbl">Timeline</div><div class="val"><?php echo pe($request['timeline'] ?? '—'); ?></div></div>
<div><div class="lbl">Budget Comfort</div><div class="val"><?php echo pe($request['budget_comfort'] ?? '—'); ?></div></div>
<div><div class="lbl">Preferred Contact</div><div class="val"><?php echo pe($request['contact_method'] ?? '—'); ?></div></div>
</div>
<div style="margin-top:10px;"><div class="lbl">Description</div><div class="val" style="white-space:pre-wrap;"><?php echo pe($request['description'] ?? ''); ?></div></div>
<?php endif; ?>
</div></div></section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="../assets/js/jquery-1.12.3.min.js"></script><script src="../assets/js/bootstrap.min.js"></script>
</body></html>
