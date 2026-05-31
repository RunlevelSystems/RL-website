<?php
$current_page = 'contracts';
$header_class = 'inner-header';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="assets/images/RL-icon.png">
    <title>Project Agreements | Runlevel Systems</title>
    <meta name="description" content="Learn how Runlevel Systems handles project agreements and contracts — from small job proposals to formal written agreements for larger work.">
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
        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        @media(max-width: 600px) { .two-col { grid-template-columns: 1fr; } }
        .tier-card { background: #09111d; border: 1px solid rgba(54,243,255,0.12); border-radius: 8px; padding: 20px; }
        .tier-card h4 { color: #ffc600; margin-top: 0; }
        .tier-card p { color: #7a9ac0; font-size: 0.9rem; margin-bottom: 0; }
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
            <h1>📑 Project Agreements</h1>
            <p>
                We believe agreements should be clear and fair. Here's how Runlevel Systems handles project agreements
                — from simple jobs to larger commercial projects.
            </p>
        </div>

        <div class="pub-card">
            <h3>Small Jobs Keep It Simple</h3>
            <p>
                For most small jobs, quick fixes, and starter website work, you don't need a lengthy contract.
                Your accepted <a href="/proposals.php" style="color:#36f3ff;">project proposal</a> and the
                <a href="/runlevel-terms.php" style="color:#36f3ff;">Runlevel Systems Terms of Service</a>
                together form the project agreement.
            </p>
            <p>
                This covers how the work is done, what's delivered, what happens if changes are needed,
                and how disputes are handled — without needing a separate document.
            </p>
        </div>

        <div class="pub-card">
            <h3>When Is a Written Agreement Used?</h3>
            <p>For larger, commercial, or ongoing projects, Runlevel Systems may use a written project agreement. This is more common when:</p>
            <div class="two-col" style="margin-top: 16px;">
                <div class="tier-card">
                    <h4>🏢 Commercial Projects</h4>
                    <p>Business applications, training simulation, infrastructure platforms, backend systems, or branded products.</p>
                </div>
                <div class="tier-card">
                    <h4>🔁 Ongoing Work</h4>
                    <p>Long-term partnerships, managed development, recurring support, or multi-phase projects.</p>
                </div>
                <div class="tier-card">
                    <h4>📱 Full Applications</h4>
                    <p>Mobile apps, full-featured web applications, or complex multi-platform projects.</p>
                </div>
                <div class="tier-card">
                    <h4>💼 Larger Budgets</h4>
                    <p>Projects involving significant custom development, milestone payments, or extended timelines.</p>
                </div>
            </div>
        </div>

        <div class="pub-card">
            <h3>What a Written Agreement Covers</h3>
            <ul>
                <li>Parties involved</li>
                <li>Project description and scope of work</li>
                <li>Deliverables — what you'll actually receive</li>
                <li>Payment terms, deposits, and milestones</li>
                <li>Your responsibilities as the client</li>
                <li>Third-party tools, licenses, or assets used</li>
                <li>Who owns the source code and final work</li>
                <li>How change requests are handled</li>
                <li>Testing, acceptance, and revisions</li>
                <li>Refunds, disputes, and how they're resolved</li>
                <li>Confidentiality</li>
                <li>Termination conditions</li>
            </ul>
            <p style="font-size:0.875rem; color:#5a7a9e; margin-bottom:0;">
                Agreements are provided in plain, readable language. We're not trying to trick anyone — we just want
                both sides to be clear about what's expected.
            </p>
        </div>

        <div class="pub-card">
            <h3>Source Code &amp; Ownership</h3>
            <p>
                How source code is handled depends on the project type. For most custom development work,
                you'll receive the deliverables as agreed in the proposal. For managed or ongoing development,
                Runlevel Systems retains management rights while delivering the agreed product.
            </p>
            <p>
                Full source code transfer options are available and will be stated clearly in the project proposal
                or agreement. If this matters to you — and it often should — ask about it early.
            </p>
        </div>

        <div class="pub-card" style="border-color: rgba(255,198,0,0.2);">
            <h3 style="color:#ffc600;">Plain English, Always</h3>
            <p>
                We don't want agreements to be scary. Contracts are tools for clarity — not weapons.
                If you have questions about any part of an agreement, just ask.
                We'll explain it without the legalese.
            </p>
            <p style="font-size:0.875rem; color:#5a7a9e; margin-bottom:0;">
                Note: For legally binding commercial agreements, we recommend having an attorney review any
                project contract before signing.
            </p>
        </div>

        <div class="cta-row">
            <a href="/client/new-request.php" class="btn-gold">Submit a Request</a>
            <a href="/proposals.php" class="btn-outline">How Proposals Work</a>
            <a href="/payments.php" class="btn-outline">How Payments Work</a>
        </div>

    </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/jquery-1.12.3.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
