<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';
portalRequireStaff();
$current_page = 'staff-portal';
$header_class = 'inner-header';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../assets/images/RL-icon.png">
    <title>PayPal Setup | Staff Portal | Runlevel Systems</title>
    <link href="../assets/css/coreloop.css" rel="stylesheet">
    <style>
        .portal-wrap { padding: 40px 0 80px; }
        .portal-back { color: #36f3ff; font-size: 0.9rem; margin-bottom: 18px; display: inline-block; }
        .portal-back:hover { color: #ffc600; }
        .setup-card { background: #0c1729; border: 1px solid rgba(54,243,255,0.18); border-radius: 10px; padding: 28px; margin-bottom: 24px; }
        .setup-card h3 { color: #ffc600; margin-top: 0; }
        .setup-step { display: flex; gap: 16px; margin-bottom: 18px; align-items: flex-start; }
        .step-num { background: #ffc600; color: #08111f; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.95rem; flex-shrink: 0; margin-top: 2px; }
        .step-body { flex: 1; }
        .step-body h4 { color: #36f3ff; margin: 0 0 4px; font-size: 1rem; }
        .step-body p { color: #a8bedc; font-size: 0.9rem; margin: 0; }
        .setup-note { background: rgba(255,198,0,0.07); border: 1px solid rgba(255,198,0,0.2); border-radius: 8px; padding: 12px 16px; margin-top: 16px; color: #c7a800; font-size: 0.875rem; }
        .security-box { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); border-radius: 8px; padding: 14px 18px; margin-bottom: 24px; color: #fca5a5; font-size: 0.875rem; }
        .config-block { background: #09111d; border: 1px solid rgba(54,243,255,0.2); border-radius: 6px; padding: 16px; font-family: monospace; font-size: 0.88rem; color: #a8d4ff; margin-top: 12px; white-space: pre-wrap; }
        .invoice-type { background: #09111d; border: 1px solid rgba(54,243,255,0.15); border-radius: 6px; padding: 10px 14px; margin-bottom: 8px; }
        .invoice-type strong { color: #36f3ff; }
        .invoice-type span { color: #7a9ac0; font-size: 0.88rem; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>

<section class="portal-wrap">
    <div class="container">
        <a href="/staff/dashboard.php" class="portal-back">← Back to Dashboard</a>
        <h1 style="color:#ffc600; margin-bottom: 8px;">🅿️ PayPal Setup Instructions</h1>
        <p style="color:#7a9ac0; margin-bottom: 24px;">Internal setup guide for configuring Runlevel Systems payments through PayPal. Staff access only.</p>

        <div class="security-box">
            🔒 <strong>Security reminder:</strong> Never store PayPal API keys, client secrets, or credentials in public files or version control.
            Use environment variables or a private server-side config file. Never collect card data directly on the Runlevel Systems website.
        </div>

        <!-- Setup Steps -->
        <div class="setup-card">
            <h3>Setup Steps</h3>
            <div class="setup-step">
                <div class="step-num">1</div>
                <div class="step-body">
                    <h4>Create or Use a PayPal Business Account</h4>
                    <p>Go to <strong>paypal.com/business</strong> and create a Business account, or upgrade an existing Personal account. Use the business email you want on invoices and payment links.</p>
                </div>
            </div>
            <div class="setup-step">
                <div class="step-num">2</div>
                <div class="step-body">
                    <h4>Confirm Business Email</h4>
                    <p>Verify your business email address through the PayPal confirmation link. Unverified accounts have sending limits.</p>
                </div>
            </div>
            <div class="setup-step">
                <div class="step-num">3</div>
                <div class="step-body">
                    <h4>Complete Identity and Tax Verification</h4>
                    <p>PayPal may require identity documents and tax information (e.g., EIN or SSN for US accounts). Complete this to lift withdrawal limits and enable full invoicing features.</p>
                </div>
            </div>
            <div class="setup-step">
                <div class="step-num">4</div>
                <div class="step-body">
                    <h4>Configure PayPal.Me or Payment Links</h4>
                    <p>Set up a PayPal.Me link (e.g., paypal.me/runlevelsystems) for quick informal payments. Add this link to <code>/config/payments.php</code> once created.</p>
                </div>
            </div>
            <div class="setup-step">
                <div class="step-num">5</div>
                <div class="step-body">
                    <h4>Enable Invoicing Inside PayPal Business</h4>
                    <p>In your PayPal Business dashboard, go to <strong>Invoicing</strong> and enable it. PayPal invoicing lets you send professional invoices with due dates and tracking.</p>
                </div>
            </div>
            <div class="setup-step">
                <div class="step-num">6</div>
                <div class="step-body">
                    <h4>Create Standard Invoice Templates</h4>
                    <p>Create saved invoice templates in PayPal for faster billing. Suggested templates:</p>
                    <div style="margin-top:10px;">
                        <div class="invoice-type"><strong>Quick Fix</strong> <span>— Small bug fix, config, script change</span></div>
                        <div class="invoice-type"><strong>Website Starter</strong> <span>— Single-page or basic business site</span></div>
                        <div class="invoice-type"><strong>Development Task</strong> <span>— Feature, game script, automation task</span></div>
                        <div class="invoice-type"><strong>Commercial Deposit</strong> <span>— Milestone deposit for larger projects</span></div>
                    </div>
                </div>
            </div>
            <div class="setup-step">
                <div class="step-num">7</div>
                <div class="step-body">
                    <h4>Add PayPal Business Email to Site Configuration</h4>
                    <p>Update <code>/config/payments.php</code> with your confirmed PayPal business email and PayPal.Me link.</p>
                </div>
            </div>
            <div class="setup-step">
                <div class="step-num">8</div>
                <div class="step-body">
                    <h4>Later: Upgrade to PayPal Checkout API</h4>
                    <p>Once the site has proper secure infrastructure and HTTPS, the payment page can be upgraded to use the PayPal Checkout API for automated invoices and checkout buttons.
                    Do NOT build full API integration until the site is properly secured.</p>
                </div>
            </div>
            <div class="setup-step">
                <div class="step-num">9</div>
                <div class="step-body">
                    <h4>Store PayPal Transaction IDs in Project Records</h4>
                    <p>When a payment is received, record the PayPal Transaction ID in the project or client record. This helps track payments and resolve any disputes.</p>
                </div>
            </div>
            <div class="setup-step">
                <div class="step-num">10</div>
                <div class="step-body">
                    <h4>Never Collect Card Data Directly</h4>
                    <p>Never build or use a form that collects credit card numbers, CVV codes, or card expiry dates directly on the Runlevel Systems website.
                    All card processing must go through PayPal or a properly PCI-compliant payment provider.</p>
                </div>
            </div>
        </div>

        <!-- Configuration Placeholder -->
        <div class="setup-card">
            <h3>Configuration Placeholder</h3>
            <p style="color:#a8bedc; font-size:0.9rem;">
                Edit <code>/config/payments.php</code> to set your PayPal details once your account is configured.
                This file is not publicly accessible.
            </p>
            <div class="config-block">&lt;?php
// /config/payments.php — Payment configuration placeholder
// TODO: Move to environment variables before production.

$paypal_business_email = "payments@runlevelsystems.com"; // Replace with real email
$paypal_mode           = "manual"; // manual | invoice | checkout_api
$paypal_me_link        = "";       // e.g. "https://paypal.me/runlevelsystems"
$paypal_invoice_note   = "Thank you for choosing Runlevel Systems.";</div>
            <div class="setup-note">
                💡 The payment workflow is currently <strong>manual</strong>: send a PayPal invoice or PayPal.Me link to the client after the proposal is approved.
                A checkout API integration can be added later when secure infrastructure is in place.
            </div>
        </div>

    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="../assets/js/jquery-1.12.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>
