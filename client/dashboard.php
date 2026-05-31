<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';

portalRequireClient();
$client = portalGetClientUser();

// Load this client's requests
$allRequests    = portalLoadRequests();
$myRequests     = array_filter($allRequests, function($r) use ($client) {
    return ($r['client_username'] ?? '') === ($client['username'] ?? '');
});
$myRequestCount = count($myRequests);

$current_page = 'client-portal';
$header_class = 'inner-header';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../assets/images/RL-icon.png">
    <title>My Dashboard | Client Portal | Runlevel Systems</title>
    <link href="../assets/css/coreloop.css" rel="stylesheet">
    <style>
        .portal-wrap { padding: 40px 0 80px; }
        .portal-header-bar { background: #0c1729; border: 1px solid rgba(54,243,255,0.15); border-radius: 10px; padding: 18px 24px; margin-bottom: 32px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
        .portal-header-bar h1 { color: #ffc600; margin: 0; font-size: 1.5rem; }
        .portal-header-bar .portal-user { color: #7a9ac0; font-size: 0.9rem; }
        .portal-card-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 16px; margin-bottom: 32px; }
        .portal-dash-card { background: #0c1729; border: 1px solid rgba(54,243,255,0.18); border-radius: 10px; padding: 24px 18px; text-align: center; text-decoration: none; color: #eaf3ff; display: block; transition: border-color 0.2s, transform 0.15s; }
        .portal-dash-card:hover { border-color: #ffc600; color: #ffc600; text-decoration: none; transform: translateY(-2px); }
        .portal-dash-card .card-icon { font-size: 2rem; margin-bottom: 8px; }
        .portal-dash-card .card-label { font-weight: 700; font-size: 1rem; }
        .portal-dash-card .card-sub { font-size: 0.8rem; color: #5a7a9e; margin-top: 6px; }
        .portal-dash-card .card-count { font-size: 1.4rem; color: #36f3ff; font-weight: 700; margin-top: 6px; }
        .portal-section-title { color: #36f3ff; font-size: 1.1rem; font-weight: 700; margin: 0 0 14px; text-transform: uppercase; letter-spacing: 0.05em; }
        .portal-logout { color: #5a7a9e; font-size: 0.875rem; }
        .portal-logout:hover { color: #ef4444; }
        .portal-info-card { background: rgba(54,243,255,0.05); border: 1px solid rgba(54,243,255,0.15); border-radius: 8px; padding: 18px 20px; }
        .portal-info-card h4 { color: #36f3ff; margin-top: 0; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>

<section class="portal-wrap">
    <div class="container">

        <div class="portal-header-bar">
            <div>
                <h1>🌐 Client Portal</h1>
                <div class="portal-user">
                    Welcome, <strong><?php echo pe($client['display_name'] ?? $client['username']); ?></strong>
                </div>
            </div>
            <a href="/client/logout.php" class="portal-logout">Sign Out</a>
        </div>

        <p class="portal-section-title">My Portal</p>
        <div class="portal-card-grid">
            <a href="/client/new-request.php" class="portal-dash-card" style="border-color: rgba(255,198,0,0.3);">
                <div class="card-icon">➕</div>
                <div class="card-label">Submit Request</div>
                <div class="card-sub">Start a new project request</div>
            </a>
            <a href="/client/requests.php" class="portal-dash-card">
                <div class="card-icon">📥</div>
                <div class="card-label">My Requests</div>
                <div class="card-count"><?php echo $myRequestCount; ?></div>
            </a>
            <a href="/client/proposals.php" class="portal-dash-card">
                <div class="card-icon">📄</div>
                <div class="card-label">My Proposals</div>
                <div class="card-sub">Coming soon</div>
            </a>
            <a href="/client/contracts.php" class="portal-dash-card">
                <div class="card-icon">📑</div>
                <div class="card-label">My Contracts</div>
                <div class="card-sub">Coming soon</div>
            </a>
            <a href="/payments.php" class="portal-dash-card">
                <div class="card-icon">💳</div>
                <div class="card-label">Payments</div>
                <div class="card-sub">How payments work</div>
            </a>
            <a href="/client/tools.php" class="portal-dash-card">
                <div class="card-icon">🛠️</div>
                <div class="card-label">Runlevel Tools</div>
                <div class="card-sub">Coming soon</div>
            </a>
            <a href="/contact.php" class="portal-dash-card">
                <div class="card-icon">🙋</div>
                <div class="card-label">Support / Issues</div>
                <div class="card-sub">Contact us</div>
            </a>
        </div>

        <div class="portal-info-card">
            <h4>🔧 Portal Expanding</h4>
            <p style="color:#a8bedc; font-size:0.9rem; margin-bottom:0;">
                The client portal is actively being developed. Proposals, contracts, and Runlevel Tools will be available
                as we expand the portal. Submitted requests are reviewed by our team and we will follow up with
                you using the contact method you provided.
            </p>
        </div>

    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="../assets/js/jquery-1.12.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>
