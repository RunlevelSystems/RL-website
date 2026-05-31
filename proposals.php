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
    <title>Project Proposals | Runlevel Systems</title>
    <meta name="description" content="Understand how Runlevel Systems project proposals work — what's included, how pricing works, and how to approve before work begins.">
    <link href="assets/css/coreloop.css" rel="stylesheet">
    <style>
        .pub-wrap { padding: 60px 0 80px; }
        .pub-hero { margin-bottom: 40px; }
        .pub-hero h1 { color: #ffc600; font-size: 2rem; margin-bottom: 10px; }
        .pub-hero p { color: #7a9ac0; font-size: 1.05rem; max-width: 680px; }
        .pub-card { background: #0c1729; border: 1px solid rgba(54,243,255,0.18); border-radius: 10px; padding: 28px 30px; margin-bottom: 24px; }
        .pub-card h3 { color: #36f3ff; margin-top: 0; font-size: 1.15rem; }
        .pub-card p, .pub-card li { color: #a8bedc; line-height: 1.7; }
        .pub-card ul { padding-left: 1.25rem; }
        .pub-card ul li { margin-bottom: 6px; }
        .step-list { counter-reset: steps; list-style: none; padding: 0; }
        .step-list li { counter-increment: steps; display: flex; gap: 14px; margin-bottom: 14px; align-items: flex-start; }
        .step-list li::before { content: counter(steps); background: #0a84ff; color: #fff; font-weight: 700; min-width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; flex-shrink: 0; margin-top: 1px; }
        .step-list li span { color: #a8bedc; line-height: 1.6; }
        .cta-row { margin-top: 32px; display: flex; gap: 12px; flex-wrap: wrap; }
        .btn-gold { background: #ffc600; color: #08111f; border: none; border-radius: 6px; padding: 11px 22px; font-weight: 700; font-size: 0.95rem; text-decoration: none; display: inline-block; }
        .btn-gold:hover { background: #36f3ff; color: #08111f; text-decoration: none; }
        .btn-outline { border: 2px solid #36f3ff; color: #36f3ff; background: transparent; border-radius: 6px; padding: 10px 20px; font-weight: 600; font-size: 0.95rem; text-decoration: none; display: inline-block; }
        .btn-outline:hover { background: rgba(54,243,255,0.1); text-decoration: none; }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navigation.php'; ?>

<section class="pub-wrap">
    <div class="container">

        <div class="pub-hero">
            <h1>📄 Project Proposals</h1>
            <p>
                Before any work begins, Runlevel Systems prepares a written proposal based on your request.
                It's a clear, plain-English plan that both sides agree to before the project starts.
            </p>
        </div>

        <div class="pub-card">
            <h3>What Is a Proposal?</h3>
            <p>
                A proposal is a simple written plan for the work you've requested.
                It's our way of making sure we both understand the project before we start.
                You're never committed to anything until you've read and approved it.
            </p>
            <p>
                A proposal is not a binding contract by itself — but it sets the foundation for the work.
                For most small jobs, the proposal plus our
                <a href="/runlevel-terms.php" style="color:#36f3ff;">Terms of Service</a> is all you need.
                For larger projects, a separate agreement may be used.
            </p>
        </div>

        <div class="pub-card">
            <h3>What's Included in a Proposal</h3>
            <ul>
                <li><strong style="color:#eaf3ff;">What you asked for</strong> — a summary of your request</li>
                <li><strong style="color:#eaf3ff;">What we will do</strong> — a clear description of the planned work</li>
                <li><strong style="color:#eaf3ff;">Deliverables</strong> — what you'll actually receive</li>
                <li><strong style="color:#eaf3ff;">Estimated price</strong> — based on the scope of the project</li>
                <li><strong style="color:#eaf3ff;">Deposit or payment required to begin</strong> — when applicable</li>
                <li><strong style="color:#eaf3ff;">Estimated timeline</strong> — how long the work is expected to take</li>
                <li><strong style="color:#eaf3ff;">Revision terms</strong> — what's included after delivery</li>
                <li><strong style="color:#eaf3ff;">Assumptions or limitations</strong> — anything we're relying on from you</li>
            </ul>
        </div>

        <div class="pub-card">
            <h3>How the Proposal Process Works</h3>
            <ol class="step-list">
                <li><span>You submit a request describing your project and what you need.</span></li>
                <li><span>We review the request and ask any clarifying questions if needed.</span></li>
                <li><span>We prepare a written proposal with scope, price, and timeline.</span></li>
                <li><span>You review the proposal. No payment or work happens yet.</span></li>
                <li><span>If you're happy with the proposal, you approve it.</span></li>
                <li><span>Payment (deposit or full payment for small jobs) is made via PayPal or approved method.</span></li>
                <li><span>Work begins after required payment is confirmed.</span></li>
                <li><span>You review the completed work and provide feedback.</span></li>
            </ol>
        </div>

        <div class="pub-card">
            <h3>Good to Know</h3>
            <ul>
                <li>Submitting a request is always free. There's no cost just to ask.</li>
                <li>You're not committed to anything until you approve a proposal.</li>
                <li>We'll explain everything in plain English — no confusing tech jargon.</li>
                <li>Proposals are fair and transparent. If something changes, we'll update you.</li>
                <li>
                    For smaller jobs, we may skip the formal proposal and jump straight to a quick quote.
                    Either way, you approve it before work starts.
                </li>
            </ul>
            <p style="font-size:0.875rem; color:#5a7a9e; margin-bottom:0;">
                Please review the <a href="/runlevel-terms.php" style="color:#36f3ff;">Terms of Service</a> before submitting a request.
                The proposal and Terms of Service together describe how work is handled, delivered, and revised.
            </p>
        </div>

        <div class="cta-row">
            <a href="/client/new-request.php" class="btn-gold">Submit a Request</a>
            <a href="/payments.php" class="btn-outline">How Payments Work</a>
            <a href="/pricing.php" class="btn-outline">Typical Prices</a>
        </div>

    </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/jquery-1.12.3.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
