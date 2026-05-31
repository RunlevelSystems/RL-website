<?php
session_start();
define('WDS_SYSTEM', true);
require_once 'includes/portal-helpers.php';

portalRequireLogin();

$user = portalGetUser();
$role = portalGetRole();
$displayName = $user['display_name'] ?? $user['username'] ?? 'User';

$requests  = portalLoadProjectRequests();
$proposals = portalLoadProposals();
$agreements = portalLoadProjectAgreements();

$myUsername = (string)($user['username'] ?? '');

// ---- Admin/Staff project counts
$pendingRequests  = 0;
$activeProjects   = 0;
$completedProjects = 0;
$awaitingApproval  = 0;  // proposals sent, not yet accepted
$awaitingSignature = 0;  // agreements sent, not yet signed

if (in_array($role, ['admin', 'staff'], true)) {
    foreach ($requests as $r) {
        $st = (string)($r['status'] ?? 'new');
        if (in_array($st, ['new', 'reviewing', 'contacted', 'needs_info'], true)) {
            $pendingRequests++;
        } elseif (in_array($st, ['active', 'accepted'], true)) {
            $activeProjects++;
        } elseif (in_array($st, ['completed', 'closed'], true)) {
            $completedProjects++;
        }
    }
    foreach ($proposals as $p) {
        if ((string)($p['status'] ?? '') === 'sent') {
            $awaitingApproval++;
        }
    }
    foreach ($agreements as $a) {
        if ((string)($a['status'] ?? '') === 'sent') {
            $awaitingSignature++;
        }
    }
}

// ---- Client data
$myRequests   = [];
$myProposals  = [];
$myAgreements = [];
if ($role === 'client') {
    $myRequests = array_values(array_filter($requests, function ($r) use ($myUsername) {
        return (string)($r['client_username'] ?? '') === $myUsername;
    }));
    $myIds = array_map(function ($r) { return portalGetRequestDisplayId((array)$r); }, $myRequests);

    $myProposals = array_values(array_filter($proposals, function ($p) use ($myIds, $myUsername) {
        return in_array((string)($p['request_id'] ?? ''), $myIds, true)
            || (string)($p['client_username'] ?? '') === $myUsername;
    }));
    $myAgreements = array_values(array_filter($agreements, function ($a) use ($myIds, $myUsername) {
        return in_array((string)($a['request_id'] ?? ''), $myIds, true)
            || (string)($a['client_username'] ?? '') === $myUsername;
    }));

    usort($myRequests, function ($a, $b) {
        return strcmp((string)($b['created_at'] ?? ''), (string)($a['created_at'] ?? ''));
    });
}

$current_page = 'dashboard';
$header_class = 'inner-header';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="/assets/images/RL-icon.png">
    <title>Dashboard | Runlevel Systems</title>
    <link href="assets/css/coreloop.css" rel="stylesheet">
    <style>
        .portal-wrap { padding: 28px 0 70px; }
        .portal-header-bar { background:#0c1729;border:1px solid rgba(54,243,255,0.15);border-radius:10px;padding:14px 18px;margin-bottom:18px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px; }
        .portal-header-bar h1 { color:#ffc600;margin:0;font-size:1.28rem; }
        .portal-header-bar .portal-user { color:#7a9ac0;font-size:.84rem; }
        .portal-card-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px;margin-bottom:16px; }
        .portal-dash-card { background:#0c1729;border:1px solid rgba(54,243,255,0.18);border-radius:9px;padding:14px 12px;text-align:center;text-decoration:none;color:#eaf3ff;display:block; }
        .portal-dash-card:hover { border-color:#ffc600;color:#ffc600;text-decoration:none; }
        .portal-dash-card .card-icon { font-size:1.35rem;margin-bottom:6px; }
        .portal-dash-card .card-label { font-weight:700;font-size:.86rem;line-height:1.25; }
        .portal-dash-card .card-count { font-size:1.05rem;color:#36f3ff;font-weight:700;margin-top:4px; }
        .portal-dash-card .card-sub { font-size:.74rem;color:#5a7a9e;margin-top:4px; }
        .portal-section-title { color:#36f3ff;font-size:.9rem;font-weight:700;margin:0 0 9px;text-transform:uppercase;letter-spacing:.05em; }
        .portal-logout { color:#5a7a9e;font-size:.8rem; }
        .portal-logout:hover { color:#ef4444; }
        .role-badge { display:inline-block;background:rgba(54,243,255,0.1);border:1px solid rgba(54,243,255,0.25);color:#36f3ff;border-radius:4px;padding:2px 6px;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-left:4px;vertical-align:middle; }
        .panel { background:#0c1729;border:1px solid rgba(54,243,255,0.18);border-radius:10px;padding:14px;overflow:auto; }
        table { width:100%;min-width:720px;border-collapse:collapse; }
        th,td { padding:8px 6px;border-top:1px solid rgba(54,243,255,0.12);font-size:.8rem; }
        th { color:#5a7a9e;font-size:.7rem;text-transform:uppercase;border-top:none; }
        .mono { font-family:monospace;color:#ffc600; }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navigation.php'; ?>

<section class="portal-wrap">
    <div class="container">
        <div class="portal-header-bar">
            <div>
                <h1>Dashboard</h1>
                <div class="portal-user">Signed in as <strong><?php echo pe($displayName); ?></strong> <span class="role-badge"><?php echo pe(ucfirst($role)); ?></span></div>
            </div>
            <a href="/logout.php" class="portal-logout">Sign Out</a>
        </div>

<?php if ($role === 'admin'): ?>
        <p class="portal-section-title">Admin Overview</p>
        <div class="portal-card-grid">
            <a href="/staff/estimate-requests.php" class="portal-dash-card"><div class="card-icon">📋</div><div class="card-label">Active Projects</div><div class="card-count"><?php echo $activeProjects; ?></div></a>
            <a href="/staff/estimate-requests.php" class="portal-dash-card"><div class="card-icon">📥</div><div class="card-label">Pending Requests</div><div class="card-count"><?php echo $pendingRequests; ?></div></a>
            <a href="/staff/estimate-requests.php" class="portal-dash-card"><div class="card-icon">📄</div><div class="card-label">Awaiting Approval</div><div class="card-count"><?php echo $awaitingApproval; ?></div><div class="card-sub">Proposals sent</div></a>
            <a href="/staff/estimate-requests.php" class="portal-dash-card"><div class="card-icon">📑</div><div class="card-label">Awaiting Signature</div><div class="card-count"><?php echo $awaitingSignature; ?></div><div class="card-sub">Agreements sent</div></a>
            <a href="/staff/estimate-requests.php" class="portal-dash-card"><div class="card-icon">✅</div><div class="card-label">Completed Projects</div><div class="card-count"><?php echo $completedProjects; ?></div></a>
            <a href="/staff/users.php" class="portal-dash-card"><div class="card-icon">👥</div><div class="card-label">Users</div><div class="card-count"><?php echo count(portalLoadUsers()); ?></div></a>
            <a href="/payments.php" class="portal-dash-card"><div class="card-icon">💳</div><div class="card-label">Payments</div><div class="card-sub">Payment docs</div></a>
            <a href="/staff/tools.php" class="portal-dash-card"><div class="card-icon">🛠️</div><div class="card-label">Runlevel Tools</div><div class="card-sub">Internal tools</div></a>
            <a href="/staff/operations.php" class="portal-dash-card"><div class="card-icon">⚙️</div><div class="card-label">Site Tools</div><div class="card-sub">Operations</div></a>
        </div>

<?php elseif ($role === 'staff'): ?>
        <p class="portal-section-title">Staff Overview</p>
        <div class="portal-card-grid">
            <a href="/staff/estimate-requests.php" class="portal-dash-card"><div class="card-icon">📋</div><div class="card-label">Active Projects</div><div class="card-count"><?php echo $activeProjects; ?></div></a>
            <a href="/staff/estimate-requests.php" class="portal-dash-card"><div class="card-icon">📥</div><div class="card-label">Pending Requests</div><div class="card-count"><?php echo $pendingRequests; ?></div></a>
            <a href="/staff/estimate-requests.php" class="portal-dash-card"><div class="card-icon">📄</div><div class="card-label">Awaiting Approval</div><div class="card-count"><?php echo $awaitingApproval; ?></div><div class="card-sub">Proposals sent</div></a>
            <a href="/staff/estimate-requests.php" class="portal-dash-card"><div class="card-icon">📑</div><div class="card-label">Awaiting Signature</div><div class="card-count"><?php echo $awaitingSignature; ?></div><div class="card-sub">Agreements sent</div></a>
            <a href="/staff/estimate-requests.php" class="portal-dash-card"><div class="card-icon">✅</div><div class="card-label">Completed Projects</div><div class="card-count"><?php echo $completedProjects; ?></div></a>
            <a href="/payments.php" class="portal-dash-card"><div class="card-icon">💳</div><div class="card-label">Payments</div><div class="card-sub">Payment docs</div></a>
            <a href="/staff/tools.php" class="portal-dash-card"><div class="card-icon">🛠️</div><div class="card-label">Runlevel Tools</div><div class="card-sub">Internal tools</div></a>
        </div>

<?php else: ?>
        <p class="portal-section-title">My Dashboard</p>
        <div class="portal-card-grid">
            <a href="/client/requests.php" class="portal-dash-card"><div class="card-icon">📋</div><div class="card-label">My Projects</div><div class="card-count"><?php echo count($myRequests); ?></div></a>
            <a href="/payments.php" class="portal-dash-card"><div class="card-icon">💳</div><div class="card-label">Payments</div><div class="card-sub">Payment info</div></a>
            <a href="/design-debug-deploy.php" class="portal-dash-card"><div class="card-icon">🛠️</div><div class="card-label">Runlevel Tools</div><div class="card-sub">Resources</div></a>
            <a href="/estimate.php" class="portal-dash-card"><div class="card-icon">➕</div><div class="card-label">Submit New Project Request</div><div class="card-sub">Start workflow</div></a>
        </div>

        <div class="panel">
            <h3 style="margin:0 0 8px;color:#36f3ff;font-size:.9rem;text-transform:uppercase;">My Projects</h3>
            <table>
                <thead>
                    <tr>
                        <th>Project ID</th>
                        <th>Project Type</th>
                        <th>Status</th>
                        <th>Submitted Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($myRequests)): ?>
                    <tr><td colspan="5" style="color:#7a9ac0;">No projects yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($myRequests as $req): ?>
                        <?php $rid = portalGetRequestDisplayId((array)$req); ?>
                        <tr>
                            <td class="mono"><?php echo pe($rid); ?></td>
                            <td style="color:#a8bedc;"><?php echo pe($req['project_type'] ?? '—'); ?></td>
                            <td style="color:#a8bedc;"><?php echo pe(ucfirst((string)($req['status'] ?? 'new'))); ?></td>
                            <td style="color:#7a9ac0;"><?php echo pe(date('M j, Y', strtotime((string)($req['created_at'] ?? 'now')))); ?></td>
                            <td><a style="color:#36f3ff;" href="/project.php?id=<?php echo urlencode($rid); ?>">Open Project</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
<?php endif; ?>

    </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/jquery-1.12.3.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
