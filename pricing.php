<?php
$current_page = 'pricing';
$header_class = 'inner-header';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="assets/images/RL-icon.png">
    <title>Pricing &amp; Estimates | Runlevel Systems</title>
    <meta name="description" content="Fair pricing based on what your project actually needs. Runlevel Systems provides estimates after reviewing your requirements.">
    <link href="assets/css/coreloop.css?v=clean-20260602" rel="stylesheet">
    <style>
        .pricing-hero { padding: 64px 0 40px; text-align: center; }
        .pricing-hero h1 { color: #ffc600; font-size: 2.2rem; margin-bottom: 12px; }
        .pricing-hero .subtitle { color: #7a9ac0; font-size: 1.15rem; max-width: 600px; margin: 0 auto 28px; }
        .pricing-section { padding: 48px 0; }
        .pricing-section h2 { color: #36f3ff; font-size: 1.4rem; margin-bottom: 8px; }
        .pricing-section .section-intro { color: #a8bedc; font-size: 1rem; max-width: 700px; margin: 0 0 32px; line-height: 1.7; }
        .factor-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px; margin-bottom: 40px; }
        .factor-card { background: #0c1729; border: 1px solid rgba(54,243,255,0.15); border-radius: 10px; padding: 22px 20px; }
        .factor-card .fc-title { color: #ffc600; font-weight: 700; font-size: 1rem; margin-bottom: 8px; }
        .factor-card .fc-body { color: #7a9ac0; font-size: 0.9rem; line-height: 1.6; margin: 0; }
        .cta-section { background: #0c1729; border: 1px solid rgba(255,198,0,0.2); border-radius: 12px; padding: 40px 32px; text-align: center; margin: 40px 0 60px; }
        .cta-section h2 { color: #ffc600; margin-top: 0; margin-bottom: 12px; }
        .cta-section p { color: #a8bedc; max-width: 560px; margin: 0 auto 24px; font-size: 1rem; line-height: 1.7; }
        .btn-estimate { display: inline-block; background: #ffc600; color: #08111f; font-weight: 700; font-size: 1.05rem; padding: 14px 36px; border-radius: 6px; text-decoration: none; transition: background 0.2s; }
        .btn-estimate:hover { background: #36f3ff; color: #08111f; text-decoration: none; }
        .process-steps { list-style: none; padding: 0; margin: 0 0 32px; }
        .process-steps li { display: flex; gap: 14px; align-items: flex-start; margin-bottom: 14px; color: #a8bedc; font-size: 0.95rem; }
        .process-steps li .step-num { background: rgba(54,243,255,0.12); color: #36f3ff; font-weight: 700; min-width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; flex-shrink: 0; }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navigation.php'; ?>

<section class="pricing-hero">
    <div class="container">
        <h1>Pricing &amp; Estimates</h1>
        <p class="subtitle">Fair pricing based on what your project actually needs.</p>
    </div>
</section>

<section class="pricing-section">
    <div class="container">
        <p class="section-intro">
            Every project is different. A small fix, a starter website, a mobile app, a business application,
            or a training simulator all require different levels of planning, development, testing, and support.
        </p>
        <p class="section-intro">
            Runlevel Systems provides fair estimates after reviewing what you need.
            Instead of guessing, we use a short project requirements form to understand the work
            and provide a realistic next step.
        </p>

        <h2>What Affects Cost</h2>
        <div class="factor-grid">
            <div class="factor-card">
                <div class="fc-title">📐 Project Size</div>
                <p class="fc-body">A small fix is different from a complete application. Scope drives the estimate.</p>
            </div>
            <div class="factor-card">
                <div class="fc-title">💾 Existing Code</div>
                <p class="fc-body">Clean code is faster to update than broken or undocumented code.</p>
            </div>
            <div class="factor-card">
                <div class="fc-title">🧪 Testing Needs</div>
                <p class="fc-body">Some work needs only a quick check. Other work needs device, server, or user testing.</p>
            </div>
            <div class="factor-card">
                <div class="fc-title">🖥️ Platform</div>
                <p class="fc-body">Websites, mobile apps, desktop apps, servers, and simulations all have different requirements.</p>
            </div>
            <div class="factor-card">
                <div class="fc-title">⏱️ Timeline</div>
                <p class="fc-body">Urgent work may require more focused scheduling.</p>
            </div>
            <div class="factor-card">
                <div class="fc-title">🔧 Ongoing Support</div>
                <p class="fc-body">Some projects need launch help, updates, hosting, or maintenance after delivery.</p>
            </div>
        </div>

        <h2>How It Works</h2>
        <ul class="process-steps">
            <li><span class="step-num">1</span> Submit your project requirements using our estimate form.</li>
            <li><span class="step-num">2</span> We review your request and reach out using your preferred contact method.</li>
            <li><span class="step-num">3</span> If needed, we schedule a brief call or Google Meet to clarify scope.</li>
            <li><span class="step-num">4</span> We provide a written estimate or proposal with expected deliverables and cost.</li>
            <li><span class="step-num">5</span> You approve the work before any payment is requested.</li>
        </ul>
    </div>
</section>

<section>
    <div class="container">
        <div class="cta-section">
            <h2>Get A Project Estimate</h2>
            <p>
                Tell us what you need. No account required. We work with hobby developers, small businesses,
                and commercial customers using the same professional process.
            </p>
            <a href="estimate.php" class="btn-estimate">Get A Project Estimate</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/jquery-1.12.3.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
