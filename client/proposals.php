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
    <title>My Proposals | Client Portal | Runlevel Systems</title>
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
            <h2>📄 My Proposals</h2>

            <div class="info-section">
                <h4 style="color:#36f3ff; margin-top:0;">What is a Proposal?</h4>
                <p style="color:#a8bedc; margin-bottom:0;">
                    After reviewing your request, Runlevel Systems will prepare a written proposal. A proposal explains
                    what we plan to do, estimated cost, timeline, deliverables, and payment required to begin.
                    You will need to approve the proposal before any work starts.
                </p>
            </div>

            <div class="coming-soon">
                <div class="icon">🔧</div>
                <p style="color:#5a7a9e;">Proposals will appear here once they have been prepared for your requests.</p>
                <p style="color:#3a5a7e; font-size:0.875rem;">
                    If you have submitted a request and haven't heard back yet,
                    please allow time for review. You can also
                    <a href="/contact.php" style="color:#36f3ff;">contact us</a> if you have questions.
                </p>
                <p>
                    <a href="/proposals.php" style="color:#36f3ff; font-size:0.875rem;">Learn more about how proposals work →</a>
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
