<?php
$current_page = 'proposals';
$header_class = 'inner-header';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="assets/images/RL-icon.png">
    <title>Proposals | Runlevel Systems</title>
    <meta name="description" content="How Runlevel Systems project proposals work. We prepare a short proposal before work begins on larger projects.">
    <link href="assets/css/coreloop.css" rel="stylesheet">
    <style>
        .prop-hero { padding: 56px 0 32px; text-align: center; }
        .prop-hero h1 { color: #ffc600; font-size: 2rem; margin-bottom: 10px; }
        .prop-hero .subtitle { color: #7a9ac0; font-size: 1.05rem; max-width: 560px; margin: 0 auto; }
        .prop-section { padding: 40px 0 24px; }
        .prop-section h2 { color: #36f3ff; font-size: 1.2rem; margin-bottom: 16px; }
        .prop-text { color: #a8bedc; font-size: 0.95rem; line-height: 1.75; max-width: 700px; margin-bottom: 24px; }
        .prop-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px; margin-bottom: 40px; }
        .prop-card { background: #0c1729; border: 1px solid rgba(54,243,255,0.15); border-radius: 10px; padding: 20px 18px; }
        .prop-card .pc-title { color: #ffc600; font-weight: 700; font-size: 0.95rem; margin-bottom: 8px; }
        .prop-card .pc-body { color: #7a9ac0; font-size: 0.875rem; line-height: 1.6; margin: 0; }
        .btn-estimate { display: inline-block; background: #ffc600; color: #08111f; font-weight: 700; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-size: 1rem; transition: background 0.2s; margin-top: 8px; }
        .btn-estimate:hover { background: #36f3ff; color: #08111f; text-decoration: none; }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navigation.php'; ?>

<section class="prop-hero">
    <div class="container">
        <h1>Proposals</h1>
        <p class="subtitle">Clear, written agreements before work begins.</p>
    </div>
</section>

<section class="prop-section">
    <div class="container">
        <p class="prop-text">
            For many projects, Runlevel Systems will provide a short proposal before work begins.
            A proposal explains what we understand, what we plan to do, the expected deliverables,
            estimated cost, and next steps.
        </p>
        <p class="prop-text">
            Small tasks may be approved informally. Larger projects, business applications, and
            multi-phase work will typically include a written proposal for clarity on both sides.
        </p>

        <h2>What A Proposal Includes</h2>
        <div class="prop-cards">
            <div class="prop-card">
                <div class="pc-title">📋 What We Will Do</div>
                <p class="pc-body">A clear description of the work we plan to complete.</p>
            </div>
            <div class="prop-card">
                <div class="pc-title">📦 What You Will Receive</div>
                <p class="pc-body">The specific deliverables included at the end of the project.</p>
            </div>
            <div class="prop-card">
                <div class="pc-title">💰 Estimated Cost</div>
                <p class="pc-body">The agreed price based on project scope and requirements.</p>
            </div>
            <div class="prop-card">
                <div class="pc-title">📅 Timeline</div>
                <p class="pc-body">Estimated timeframe and any milestones or phases.</p>
            </div>
            <div class="prop-card">
                <div class="pc-title">💳 Payment Needed To Begin</div>
                <p class="pc-body">Deposit or initial payment required before work starts.</p>
            </div>
            <div class="prop-card">
                <div class="pc-title">❓ Questions Or Assumptions</div>
                <p class="pc-body">Any open items, assumptions, or clarifications noted up front.</p>
            </div>
        </div>

        <a href="estimate.php" class="btn-estimate">Start With An Estimate</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/jquery-1.12.3.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
