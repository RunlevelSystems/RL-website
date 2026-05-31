<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';

portalRequireClient();

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
    <title>My Contracts | Client Portal | Runlevel Systems</title>
    <link href="../assets/css/coreloop.css" rel="stylesheet">
    <style>
        .portal-wrap { padding: 40px 0 80px; }
        .portal-back { color: #36f3ff; font-size: 0.9rem; margin-bottom: 18px; display: inline-block; }
        .portal-back:hover { color: #ffc600; }
        .portal-card { background: #0c1729; border: 1px solid rgba(54,243,255,0.18); border-radius: 10px; padding: 30px; }
        .portal-card h2 { color: #ffc600; margin-top: 0; }
        .info-section { background: rgba(54,243,255,0.05); border: 1px solid rgba(54,243,255,0.15); border-radius: 8px; padding: 20px 22px; margin-bottom: 22px; }
        .coming-soon { text-align: center; padding: 40px 20px; color: #5a7a9e; }
        .coming-soon .icon { font-size: 2.5rem; margin-bottom: 12px; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>

<section class="portal-wrap">
    <div class="container">
        <a href="/client/dashboard.php" class="portal-back">← Back to Dashboard</a>

        <div class="portal-card">
            <h2>📑 My Contracts</h2>

            <div class="info-section">
                <h4 style="color:#36f3ff; margin-top:0;">About Contracts</h4>
                <p style="color:#a8bedc; margin-bottom:0;">
                    For smaller jobs, the accepted proposal and Runlevel Systems Terms of Service typically serve as
                    the project agreement. For larger, commercial, or ongoing work, a written project agreement may
                    be used. Contracts spell out project scope, payment terms, deliverables, and both parties'
                    responsibilities in more detail.
                </p>
            </div>

            <div class="coming-soon">
                <div class="icon">🔧</div>
                <p style="color:#5a7a9e;">Contracts for your projects will appear here when applicable.</p>
                <p>
                    <a href="/contracts.php" style="color:#36f3ff; font-size:0.875rem;">Learn more about how contracts work →</a>
                </p>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="../assets/js/jquery-1.12.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>
