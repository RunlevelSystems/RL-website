<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';

portalRequireStaff();

$staff = portalGetStaffUser();
$allRequests     = portalLoadRequests();
$commercialReqs  = portalLoadCommercialRequests();
$estimateReqs    = portalLoadEstimateRequests();
$newRequests     = array_filter($allRequests, function($r) { return ($r['status'] ?? '') === 'new'; });
$newCommercial   = array_filter($commercialReqs, function($r) { return ($r['status'] ?? '') === 'new'; });
$newEstimates    = array_filter($estimateReqs, function($r) { return ($r['status'] ?? '') === 'new'; });

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
    <title>Staff Dashboard | Runlevel Systems</title>
    <link href="../assets/css/coreloop.css" rel="stylesheet">
    <style>
        .portal-wrap { padding: 40px 0 80px; }
        .portal-header-bar { background: #0c1729; border: 1px solid rgba(54,243,255,0.15); border-radius: 10px; padding: 18px 24px; margin-bottom: 32px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
        .portal-header-bar h1 { color: #ffc600; margin: 0; font-size: 1.5rem; }
        .portal-header-bar .portal-user { color: #7a9ac0; font-size: 0.9rem; }
        .portal-card-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; margin-bottom: 32px; }
        .portal-dash-card { background: #0c1729; border: 1px solid rgba(54,243,255,0.18); border-radius: 10px; padding: 24px 18px; text-align: center; text-decoration: none; color: #eaf3ff; display: block; transition: border-color 0.2s, transform 0.15s; }
        .portal-dash-card:hover { border-color: #ffc600; color: #ffc600; text-decoration: none; transform: translateY(-2px); }
        .portal-dash-card .card-icon { font-size: 2rem; margin-bottom: 8px; }
        .portal-dash-card .card-label { font-weight: 700; font-size: 1rem; }
        .portal-dash-card .card-count { font-size: 1.5rem; color: #36f3ff; font-weight: 700; margin-top: 6px; }
        .portal-dash-card .card-count.alert { color: #ffc600; }
        .portal-section-title { color: #36f3ff; font-size: 1.1rem; font-weight: 700; margin: 0 0 14px; text-transform: uppercase; letter-spacing: 0.05em; }
        .portal-logout { color: #5a7a9e; font-size: 0.875rem; }
        .portal-logout:hover { color: #ef4444; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>

<section class="portal-wrap">
    <div class="container">

        <div class="portal-header-bar">
            <div>
                <h1>🖥️ Staff Dashboard</h1>
                <div class="portal-user">
                    Signed in as <strong><?php echo pe($staff['display_name'] ?? $staff['username']); ?></strong>
                    &nbsp;·&nbsp; Role: <strong><?php echo pe(ucfirst($staff['role'])); ?></strong>
                </div>
            </div>
            <a href="/staff/logout.php" class="portal-logout">Sign Out</a>
        </div>

        <p class="portal-section-title">Portal Overview</p>
        <div class="portal-card-grid">
            <a href="/staff/estimate-requests.php" class="portal-dash-card" style="border-color: rgba(255,198,0,0.35);">
                <div class="card-icon">📝</div>
                <div class="card-label">Estimate Requests</div>
                <div class="card-count <?php echo count($newEstimates) > 0 ? 'alert' : ''; ?>"><?php echo count($newEstimates); ?></div>
            </a>
            <a href="/staff/requests.php" class="portal-dash-card">
                <div class="card-icon">📥</div>
                <div class="card-label">Project Requests</div>
                <div class="card-count <?php echo count($newRequests) > 0 ? 'alert' : ''; ?>"><?php echo count($newRequests); ?></div>
            </a>
            <?php if (portalGetStaffRole() === 'admin'): ?>
            <a href="/staff/users.php" class="portal-dash-card">
                <div class="card-icon">👥</div>
                <div class="card-label">Users</div>
                <div class="card-count"><?php echo count(portalLoadUsers()); ?></div>
            </a>
            <?php endif; ?>
            <a href="/proposals.php" class="portal-dash-card">
                <div class="card-icon">📄</div>
                <div class="card-label">Proposals</div>
                <div class="card-count" style="color:#5a7a9e;">—</div>
            </a>
            <a href="/contracts.php" class="portal-dash-card">
                <div class="card-icon">📑</div>
                <div class="card-label">Contracts</div>
                <div class="card-count" style="color:#5a7a9e;">—</div>
            </a>
            <a href="/payments.php" class="portal-dash-card">
                <div class="card-icon">💳</div>
                <div class="card-label">Payments</div>
                <div class="card-count" style="color:#5a7a9e;">—</div>
            </a>
            <a href="/staff/paypal-setup.php" class="portal-dash-card">
                <div class="card-icon">🅿️</div>
                <div class="card-label">PayPal Setup</div>
                <div class="card-count" style="font-size:0.8rem; color:#5a7a9e;">Setup</div>
            </a>
            <a href="/staff/tools.php" class="portal-dash-card">
                <div class="card-icon">🛠️</div>
                <div class="card-label">Runlevel Tools</div>
                <div class="card-count" style="font-size:0.8rem; color:#5a7a9e;">Tools</div>
            </a>
        </div>

        <p class="portal-section-title">Quick Templates</p>
        <div class="portal-card-grid">
            <a href="/staff/proposal-template.php" class="portal-dash-card">
                <div class="card-icon">✍️</div>
                <div class="card-label">Proposal Template</div>
            </a>
            <a href="/staff/contract-template.php" class="portal-dash-card">
                <div class="card-icon">📋</div>
                <div class="card-label">Contract Template</div>
            </a>
        </div>

        <div style="background: rgba(54,243,255,0.06); border: 1px solid rgba(54,243,255,0.15); border-radius: 8px; padding: 16px 20px; margin-top: 24px;">
            <p style="margin:0; color:#7a9ac0; font-size:0.9rem;">
                <strong style="color:#36f3ff;">Portal Status:</strong>
                This is the first version of the Runlevel Systems business portal. Proposals and contracts
                will be expanded in future updates. Requests submitted through the client portal appear in
                the New Requests queue above.
            </p>
        </div>

    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="../assets/js/jquery-1.12.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>
