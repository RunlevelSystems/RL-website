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
    <title>Payment Details | Runlevel Systems</title>
    <meta name="description" content="How Runlevel Systems reviews requests, sends estimates, and handles project payments.">
    <link href="assets/css/coreloop.css?v=clean-20260602" rel="stylesheet">
</head>
<body>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navigation.php'; ?>

<main class="service-site" aria-label="Runlevel Systems payment details page">
    <section class="service-hero compact">
        <div class="container">
            <p class="service-kicker">Payments</p>
            <h1>Payment Details</h1>
            <p class="service-lead">Clear payment expectations before work begins.</p>
            <p class="service-sublead">Runlevel Systems reviews the request first, then provides an estimate, proposal, or next step before work starts.</p>
            <p class="hero-tagline">DESIGN • DEBUG • DEPLOY</p>
        </div>
    </section>

    <section class="service-section">
        <div class="container">
            <h2>How Payment Works</h2>
            <div class="service-card-grid two-columns">
                <article class="service-card-item"><h3>1. Submit A Request</h3><p>The process starts when you send a request through Start Project and tell us what you need built, fixed, improved, or launched.</p></article>
                <article class="service-card-item"><h3>2. Review &amp; Estimate</h3><p>Runlevel Systems reviews the request and provides an estimate, proposal, questions, or a recommended next step.</p></article>
                <article class="service-card-item"><h3>3. Payment Approval</h3><p>PayPal invoice or payment link may be sent when the work is approved to begin.</p></article>
                <article class="service-card-item"><h3>4. Work Begins</h3><p>Small jobs may require upfront payment. Larger projects may use deposits, milestone payments, or a written project agreement.</p></article>
            </div>
        </div>
    </section>

    <section class="service-section alt">
        <div class="container">
            <h2>Important Notes</h2>
            <ul class="service-icon-list single-column">
                <li>Submitting a request does not commit you to anything.</li>
                <li>Payment starts work, but it does not automatically mean final acceptance.</li>
                <li>Final acceptance happens after the agreed work is delivered and reviewed according to the proposal or project agreement.</li>
                <li>Refunds and revisions follow the Terms of Service and the specific proposal or agreement used for the job.</li>
            </ul>
            <div class="service-actions" style="margin-top: 1rem;">
                <a class="core-action primary" href="/start-project.php">Start Project</a>
                <a class="core-action secondary" href="/runlevel-terms.php">Read Terms of Service</a>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/jquery-1.12.3.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
