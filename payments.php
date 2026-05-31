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
    <title>How Payments Work | Runlevel Systems</title>
    <meta name="description" content="Learn how Runlevel Systems handles payments — using PayPal, how approval works, and what paying actually means.">
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
        .step-list { counter-reset: steps; list-style: none; padding: 0; margin: 0; }
        .step-list li { counter-increment: steps; display: flex; gap: 14px; margin-bottom: 16px; align-items: flex-start; }
        .step-list li::before { content: counter(steps); background: #0a84ff; color: #fff; font-weight: 700; min-width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; flex-shrink: 0; margin-top: 1px; }
        .step-list li span { color: #a8bedc; line-height: 1.6; }
        .reassure-card { background: rgba(34,197,94,0.08); border: 1px solid rgba(34,197,94,0.25); border-radius: 10px; padding: 24px 28px; margin-bottom: 24px; }
        .reassure-card h3 { color: #86efac; margin-top: 0; }
        .reassure-card p { color: #a8bedc; line-height: 1.7; margin-bottom: 0; }
        .cta-row { margin-top: 32px; display: flex; gap: 12px; flex-wrap: wrap; }
        .btn-gold { background: #ffc600; color: #08111f; border: none; border-radius: 6px; padding: 11px 22px; font-weight: 700; font-size: 0.95rem; text-decoration: none; display: inline-block; }
        .btn-gold:hover { background: #36f3ff; color: #08111f; text-decoration: none; }
        .btn-outline { border: 2px solid #36f3ff; color: #36f3ff; background: transparent; border-radius: 6px; padding: 10px 20px; font-weight: 600; font-size: 0.95rem; text-decoration: none; display: inline-block; }
        .btn-outline:hover { background: rgba(54,243,255,0.1); text-decoration: none; }
        .tos-note { background: rgba(54,243,255,0.06); border: 1px solid rgba(54,243,255,0.15); border-radius: 6px; padding: 12px 16px; font-size: 0.875rem; color: #7a9ac0; }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navigation.php'; ?>

<section class="pub-wrap">
    <div class="container">

        <div class="pub-hero">
            <h1>💳 How Payments Work</h1>
            <p>
                We keep payments simple and fair. You'll never be asked to pay for something you haven't approved,
                and you'll never be surprised by unexpected charges.
            </p>
        </div>

        <div class="reassure-card">
            <h3>✅ You're in control</h3>
            <p>
                You can request work without paying anything upfront. We review your request, prepare an estimate or proposal,
                and you decide whether to move forward. Payment only comes <em>after</em> you've agreed to the plan.
                Nothing happens without your approval.
            </p>
        </div>

        <div class="pub-card">
            <h3>The Payment Process — Step by Step</h3>
            <ol class="step-list">
                <li><span><strong>Send us a request.</strong> Tell us what you need. There's no charge to submit a request.</span></li>
                <li><span><strong>We review the request.</strong> We may ask a few questions to better understand the project.</span></li>
                <li><span><strong>We provide a quote or proposal.</strong> A clear, written plan with estimated price and timeline.</span></li>
                <li><span><strong>You approve the work.</strong> No work begins until you've read and accepted the proposal.</span></li>
                <li><span><strong>We send a PayPal invoice or payment link.</strong> You pay securely through PayPal.</span></li>
                <li><span><strong>Work begins after payment is confirmed.</strong> For small jobs, full payment upfront. For larger jobs, a deposit or milestone payment may be used.</span></li>
                <li><span><strong>You review the completed work.</strong> We deliver the agreed work and you review it.</span></li>
                <li><span><strong>Revisions or issues are handled.</strong> According to the proposal and our Terms of Service.</span></li>
            </ol>
            <div class="tos-note" style="margin-top:16px;">
                Please review the <a href="/runlevel-terms.php" style="color:#36f3ff;">Terms of Service</a> and the proposal before making any payment.
            </div>
        </div>

        <div class="pub-card">
            <h3>Why PayPal?</h3>
            <p>
                PayPal allows customers to pay securely using a familiar, trusted payment provider.
                Most people already have a PayPal account or can pay through PayPal using a debit or credit card
                without needing one.
            </p>
            <p>
                PayPal also gives you access to transaction records and PayPal's applicable buyer protection
                policies, depending on the transaction type and PayPal's current terms.
            </p>
            <p>
                <strong style="color:#eaf3ff;">We never collect your card details directly.</strong>
                Payment is always handled through PayPal's secure system — not on this website.
            </p>
        </div>

        <div class="pub-card" style="border-color: rgba(255,198,0,0.2);">
            <h3 style="color:#ffc600;">Payment Is Not Final Acceptance</h3>
            <p>
                Paying a deposit or invoice is what allows work to begin — but it doesn't mean you've automatically
                accepted the final result.
            </p>
            <p>
                Final acceptance happens <strong>after</strong> the agreed deliverables have been provided and reviewed
                by you, in accordance with the proposal or project agreement. You get to review the work before
                it's considered complete.
            </p>
        </div>

        <div class="pub-card">
            <h3>Small Jobs vs. Larger Projects</h3>
            <ul>
                <li>
                    <strong style="color:#eaf3ff;">Quick fixes and small tasks:</strong>
                    Full payment is typically required before work begins. The amount is small and the scope is clear.
                </li>
                <li>
                    <strong style="color:#eaf3ff;">Larger projects:</strong>
                    A deposit may be required to begin, with milestone payments or final payment upon delivery.
                    The proposal will explain the exact terms.
                </li>
                <li>
                    <strong style="color:#eaf3ff;">Commercial / long-term work:</strong>
                    Custom payment schedule. Milestones, deposits, and ongoing payments will be defined in the project agreement.
                </li>
            </ul>
        </div>

        <div class="pub-card">
            <h3>Refunds &amp; Revisions</h3>
            <p>
                Refunds, revisions, and disputes depend on the project type, how much work has been completed,
                the terms in the proposal, and the Runlevel Systems Terms of Service.
            </p>
            <p>
                We're a small team and we care about fair outcomes. If something isn't right, we'd rather fix it
                than argue about it. Reach out and let's talk.
            </p>
            <p style="font-size:0.875rem; color:#5a7a9e; margin-bottom:0;">
                PayPal may also provide buyer protections depending on the transaction and PayPal's current policies.
                We encourage customers to understand those policies before paying.
            </p>
        </div>

        <div class="cta-row">
            <a href="/client/new-request.php" class="btn-gold">Submit a Request</a>
            <a href="/proposals.php" class="btn-outline">How Proposals Work</a>
            <a href="/pricing.php" class="btn-outline">Typical Prices</a>
        </div>

    </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/jquery-1.12.3.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
