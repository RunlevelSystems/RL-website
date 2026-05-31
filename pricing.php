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
    <title>Typical Price Ranges | Runlevel Systems</title>
    <meta name="description" content="Runlevel Systems typical price ranges for web development, bug fixes, game scripts, Unity help, mobile builds, and commercial projects.">
    <link href="assets/css/coreloop.css" rel="stylesheet">
    <style>
        .pub-wrap { padding: 60px 0 80px; }
        .pub-hero { margin-bottom: 40px; }
        .pub-hero h1 { color: #ffc600; font-size: 2rem; margin-bottom: 10px; }
        .pub-hero p { color: #7a9ac0; font-size: 1.05rem; max-width: 680px; }
        .price-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-bottom: 32px; }
        .price-card { background: #0c1729; border: 1px solid rgba(54,243,255,0.18); border-radius: 10px; padding: 24px 22px; display: flex; flex-direction: column; }
        .price-card .card-icon { font-size: 1.8rem; margin-bottom: 10px; }
        .price-card h3 { color: #36f3ff; margin: 0 0 8px; font-size: 1.1rem; }
        .price-card .price-range { color: #ffc600; font-size: 1.4rem; font-weight: 700; margin-bottom: 12px; }
        .price-card .price-examples { color: #7a9ac0; font-size: 0.875rem; line-height: 1.6; }
        .price-card .price-examples strong { color: #a8bedc; display: block; margin-bottom: 4px; }
        .price-card .price-examples ul { padding-left: 1.2rem; margin: 0; }
        .price-card .price-examples li { margin-bottom: 3px; }
        .price-card.highlight { border-color: rgba(255,198,0,0.3); }
        .price-card.custom { border-color: rgba(167,139,250,0.3); }
        .price-card.custom .price-range { color: #a78bfa; }
        .disclaimer { background: rgba(54,243,255,0.06); border: 1px solid rgba(54,243,255,0.15); border-radius: 8px; padding: 18px 22px; margin-bottom: 28px; }
        .disclaimer p { color: #7a9ac0; font-size: 0.9rem; line-height: 1.6; margin: 0; }
        .cta-row { display: flex; gap: 12px; flex-wrap: wrap; }
        .btn-gold { background: #ffc600; color: #08111f; border: none; border-radius: 6px; padding: 11px 22px; font-weight: 700; font-size: 0.95rem; text-decoration: none; display: inline-block; }
        .btn-gold:hover { background: #36f3ff; color: #08111f; text-decoration: none; }
        .btn-outline { border: 2px solid #36f3ff; color: #36f3ff; background: transparent; border-radius: 6px; padding: 10px 20px; font-weight: 600; font-size: 0.95rem; text-decoration: none; display: inline-block; }
        .btn-outline:hover { background: rgba(54,243,255,0.1); text-decoration: none; }
        .audience-note { background: rgba(255,198,0,0.06); border: 1px solid rgba(255,198,0,0.2); border-radius: 8px; padding: 16px 20px; margin-bottom: 28px; color: #c7a800; font-size: 0.9rem; }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navigation.php'; ?>

<section class="pub-wrap">
    <div class="container">

        <div class="pub-hero">
            <h1>💰 Typical Price Ranges</h1>
            <p>
                Real pricing for real people — not enterprise-only quotes.
                Runlevel Systems is here to help solo developers, hobbyists, small teams, game server owners,
                and small businesses get things done at an accessible price.
            </p>
        </div>

        <div class="audience-note">
            🎯 <strong>Who we work with:</strong> Solo developers · Hobby developers · Game server owners · Small teams · Indie creators · Small businesses
        </div>

        <div class="price-grid">

            <div class="price-card highlight">
                <div class="card-icon">🔧</div>
                <h3>Quick Fixes</h3>
                <div class="price-range">$20 – $40</div>
                <div class="price-examples">
                    <strong>Examples:</strong>
                    <ul>
                        <li>Small bug fix</li>
                        <li>Config issue</li>
                        <li>Simple script change</li>
                        <li>Animation problem</li>
                        <li>Minor website edit</li>
                    </ul>
                </div>
            </div>

            <div class="price-card">
                <div class="card-icon">⚙️</div>
                <h3>Small Development Tasks</h3>
                <div class="price-range">$30 – $100</div>
                <div class="price-examples">
                    <strong>Examples:</strong>
                    <ul>
                        <li>Small feature addition</li>
                        <li>Game script</li>
                        <li>Simple Unity issue</li>
                        <li>Mod adjustment</li>
                        <li>Small automation task</li>
                    </ul>
                </div>
            </div>

            <div class="price-card">
                <div class="card-icon">🌐</div>
                <h3>Starter Website</h3>
                <div class="price-range">$40 – $100</div>
                <div class="price-examples">
                    <strong>Examples:</strong>
                    <ul>
                        <li>Single-page site</li>
                        <li>Simple business page</li>
                        <li>Project landing page</li>
                        <li>Basic contact page</li>
                    </ul>
                </div>
            </div>

            <div class="price-card">
                <div class="card-icon">✏️</div>
                <h3>Website Improvements</h3>
                <div class="price-range">$20 – $150</div>
                <div class="price-examples">
                    <strong>Examples:</strong>
                    <ul>
                        <li>Fix layout issues</li>
                        <li>Add page or section</li>
                        <li>Update content</li>
                        <li>Improve mobile view</li>
                        <li>Add basic form</li>
                    </ul>
                </div>
            </div>

            <div class="price-card">
                <div class="card-icon">📱</div>
                <h3>Mobile / Build Help</h3>
                <div class="price-range">$40 – $150</div>
                <div class="price-examples">
                    <strong>Examples:</strong>
                    <ul>
                        <li>Android build help</li>
                        <li>iOS build preparation</li>
                        <li>Store publishing guidance</li>
                        <li>Build troubleshooting</li>
                    </ul>
                </div>
            </div>

            <div class="price-card">
                <div class="card-icon">🚨</div>
                <h3>Project Rescue</h3>
                <div class="price-range" style="color:#36f3ff;">Quoted After Review</div>
                <div class="price-examples">
                    <strong>Examples:</strong>
                    <ul>
                        <li>Broken project</li>
                        <li>Inherited code</li>
                        <li>Build failures</li>
                        <li>Unknown bugs</li>
                        <li>Server issues</li>
                    </ul>
                </div>
            </div>

            <div class="price-card custom" style="grid-column: span 1;">
                <div class="card-icon">🏢</div>
                <h3>Commercial / Long-Term Work</h3>
                <div class="price-range">Custom Quote</div>
                <div class="price-examples">
                    <strong>Examples:</strong>
                    <ul>
                        <li>Business applications</li>
                        <li>Training simulation</li>
                        <li>Mobile application</li>
                        <li>Backend systems</li>
                        <li>Infrastructure platforms</li>
                        <li>Ongoing support</li>
                    </ul>
                </div>
            </div>

        </div>

        <div class="disclaimer">
            <p>
                <strong style="color:#a8bedc;">These are examples only.</strong>
                Final pricing depends on project scope, code condition, testing needs, and requested delivery timeline.
                All prices are in USD. Work begins after a proposal is approved and required payment is received.
                <a href="/proposals.php" style="color:#36f3ff;">Learn how proposals work →</a>
            </p>
        </div>

        <div class="cta-row">
            <a href="/client/new-request.php" class="btn-gold">Request a Quote</a>
            <a href="/commercial.php" class="btn-outline">Commercial Projects</a>
            <a href="/payments.php" class="btn-outline">How Payments Work</a>
        </div>

    </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/jquery-1.12.3.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
