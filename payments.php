<?php
$current_page = 'payments';
$header_class = 'inner-header';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="assets/images/RL-icon.png">
    <title>Payments | Runlevel Systems</title>
    <meta name="description" content="How payments work with Runlevel Systems. We review your request before asking for payment.">
    <link href="assets/css/coreloop.css" rel="stylesheet">
    <style>
        .pay-hero { padding: 56px 0 32px; text-align: center; }
        .pay-hero h1 { color: #ffc600; font-size: 2rem; margin-bottom: 10px; }
        .pay-hero .subtitle { color: #7a9ac0; font-size: 1.05rem; max-width: 560px; margin: 0 auto; }
        .pay-section { padding: 40px 0 24px; }
        .pay-section h2 { color: #36f3ff; font-size: 1.2rem; margin-bottom: 16px; }
        .pay-text { color: #a8bedc; font-size: 0.95rem; line-height: 1.75; max-width: 700px; margin-bottom: 16px; }
        .steps-list { list-style: none; padding: 0; margin: 0 0 32px; max-width: 600px; }
        .steps-list li { display: flex; gap: 14px; align-items: flex-start; margin-bottom: 14px; color: #a8bedc; font-size: 0.95rem; }
        .steps-list .step-num { background: rgba(54,243,255,0.12); color: #36f3ff; font-weight: 700; min-width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; flex-shrink: 0; }
        .pay-note { background: rgba(54,243,255,0.05); border: 1px solid rgba(54,243,255,0.12); border-radius: 8px; padding: 16px 20px; color: #7a9ac0; font-size: 0.875rem; line-height: 1.65; max-width: 680px; margin-bottom: 40px; }
        .btn-estimate { display: inline-block; background: #ffc600; color: #08111f; font-weight: 700; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-size: 1rem; transition: background 0.2s; margin-top: 8px; }
        .btn-estimate:hover { background: #36f3ff; color: #08111f; text-decoration: none; }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navigation.php'; ?>

<section class="pay-hero">
    <div class="container">
        <h1>Payments</h1>
        <p class="subtitle">Simple, transparent payment process — you always know what you are paying for before you pay.</p>
    </div>
</section>

<section class="pay-section">
    <div class="container">
        <p class="pay-text">
            Runlevel Systems normally reviews your request before asking for payment.
        </p>
        <p class="pay-text">
            For small tasks, payment may be requested before work begins.
            For larger projects, we may use deposits, milestones, or written proposals.
        </p>
        <p class="pay-text">
            Payments are usually handled through PayPal invoice or payment link.
            You will always know what the payment is for before you pay.
        </p>

        <h2>Simple Payment Flow</h2>
        <ol class="steps-list">
            <li><span class="step-num">1</span> Submit your request or estimate form.</li>
            <li><span class="step-num">2</span> Review the estimate or proposal we prepare.</li>
            <li><span class="step-num">3</span> Approve the work and confirm the terms.</li>
            <li><span class="step-num">4</span> Pay through PayPal invoice or approved payment link.</li>
            <li><span class="step-num">5</span> Work begins after payment is confirmed.</li>
            <li><span class="step-num">6</span> Review completed work against the agreed deliverables.</li>
        </ol>

        <div class="pay-note">
            Payment allows work to begin. Final acceptance happens after the agreed work is delivered
            and reviewed according to the proposal or terms.
        </div>

        <a href="estimate.php" class="btn-estimate">Start With An Estimate</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/jquery-1.12.3.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
