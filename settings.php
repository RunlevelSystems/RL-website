<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/includes/portal-helpers.php';
portalRequireStaff(['admin']);

$current_page = 'dashboard';
$header_class = 'inner-header';

$user     = portalGetUser();
$settings = portalLoadAdminSettings();
$notice   = '';
$error    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paypal   = $settings['paypal'];
    $email    = $settings['email'];
    $site     = $settings['site'];
    $business = $settings['business'];

    // PayPal general
    $paypal['mode']     = in_array(trim((string)($_POST['paypal_mode'] ?? 'manual')), ['manual','sandbox','live'], true)
                          ? trim((string)$_POST['paypal_mode']) : 'manual';
    $paypal['currency'] = strtoupper(substr(trim((string)($_POST['paypal_currency'] ?? 'USD')), 0, 3));
    $paypal['invoice_message']             = trim((string)($_POST['paypal_invoice_message']             ?? ''));
    $paypal['payment_instructions']        = trim((string)($_POST['paypal_payment_instructions']        ?? ''));
    $paypal['require_payment_before_work'] = (bool)($_POST['paypal_require_payment_before_work'] ?? false);
    $paypal['enable_manual_recording']     = (bool)($_POST['paypal_enable_manual_recording']     ?? false);
    $paypal['enable_webhook_logging']      = (bool)($_POST['paypal_enable_webhook_logging']      ?? false);

    // Sandbox credentials — only overwrite secret if a non-blank value is submitted
    $paypal['sandbox']['client_id']     = trim((string)($_POST['sandbox_client_id']     ?? ''));
    $paypal['sandbox']['business_email']= trim((string)($_POST['sandbox_business_email']?? ''));
    $paypal['sandbox']['webhook_id']    = trim((string)($_POST['sandbox_webhook_id']    ?? ''));
    $paypal['sandbox']['payment_link']  = trim((string)($_POST['sandbox_payment_link']  ?? ''));
    $sbSecret = trim((string)($_POST['sandbox_secret'] ?? ''));
    if ($sbSecret !== '') {
        $paypal['sandbox']['secret'] = $sbSecret;
    }

    // Live credentials
    $paypal['live']['client_id']     = trim((string)($_POST['live_client_id']     ?? ''));
    $paypal['live']['business_email']= trim((string)($_POST['live_business_email']?? ''));
    $paypal['live']['webhook_id']    = trim((string)($_POST['live_webhook_id']    ?? ''));
    $paypal['live']['payment_link']  = trim((string)($_POST['live_payment_link']  ?? ''));
    $liveSecret = trim((string)($_POST['live_secret'] ?? ''));
    if ($liveSecret !== '') {
        $paypal['live']['secret'] = $liveSecret;
    }

    $email['from_name']  = trim((string)($_POST['email_from_name']  ?? ''));
    $email['from_email'] = trim((string)($_POST['email_from_email'] ?? ''));
    $email['reply_to']   = trim((string)($_POST['email_reply_to']   ?? ''));

    $site['company_name']   = trim((string)($_POST['site_company_name']   ?? 'Runlevel Systems'));
    $site['support_email']  = trim((string)($_POST['site_support_email']  ?? ''));
    $site['support_phone']  = trim((string)($_POST['site_support_phone']  ?? ''));

    $business['legal_name'] = trim((string)($_POST['business_legal_name'] ?? ''));
    $business['address']    = trim((string)($_POST['business_address']    ?? ''));

    // Email validation
    $sbEmailVal   = $paypal['sandbox']['business_email'];
    $liveEmailVal = $paypal['live']['business_email'];
    if ($sbEmailVal !== '' && !filter_var($sbEmailVal, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid sandbox business email.';
    } elseif ($liveEmailVal !== '' && !filter_var($liveEmailVal, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid live business email.';
    } elseif ($email['from_email'] !== '' && !filter_var($email['from_email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid sender email.';
    } elseif ($email['reply_to'] !== '' && !filter_var($email['reply_to'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid reply-to email.';
    } elseif ($site['support_email'] !== '' && !filter_var($site['support_email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid support email.';
    }

    if ($error === '') {
        $settings = [
            'paypal'     => $paypal,
            'email'      => $email,
            'site'       => $site,
            'business'   => $business,
            'updated_at' => date('c'),
            'updated_by' => (string)($user['username'] ?? 'admin'),
        ];
        if (portalSaveAdminSettings($settings)) {
            $notice = 'Settings saved.';
            $settings = portalLoadAdminSettings();
        } else {
            $error = 'Unable to save settings.';
        }
    }
}

// Helper: mask secret for display
function maskSecret($val) {
    $val = (string)$val;
    if ($val === '') { return ''; }
    if (strlen($val) <= 8) { return str_repeat('•', strlen($val)); }
    return substr($val, 0, 4) . str_repeat('•', min(24, strlen($val) - 4));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="/assets/images/RL-icon.png">
    <title>Settings | Admin | Runlevel Systems</title>
    <link href="assets/css/coreloop.css" rel="stylesheet">
    <style>
        .settings-wrap{padding:28px 0 70px;}
        .settings-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:12px;}
        .settings-card{background:#0c1729;border:1px solid rgba(54,243,255,.16);border-radius:10px;padding:14px;}
        .settings-card h2{margin:0 0 10px;color:#ffc600;font-size:.95rem;text-transform:uppercase;letter-spacing:.05em;}
        .settings-card p{margin:0 0 8px;color:#7a9ac0;font-size:.82rem;}
        .settings-input,.settings-select,.settings-textarea{width:100%;background:#09111d;border:1px solid rgba(54,243,255,.22);border-radius:6px;color:#eaf3ff;padding:8px 10px;font-size:.84rem;}
        .settings-textarea{min-height:82px;resize:vertical;}
        .settings-label{display:block;color:#a8bedc;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;margin:9px 0 4px;}
        .settings-btn{display:inline-block;border:none;border-radius:6px;padding:8px 12px;background:#ffc600;color:#08111f;font-size:.8rem;font-weight:700;}
        .settings-link{display:inline-block;margin-right:8px;border:1px solid rgba(54,243,255,.3);background:rgba(54,243,255,.08);color:#36f3ff;border-radius:6px;padding:6px 10px;font-size:.78rem;text-decoration:none;}
        .settings-link:hover{color:#ffc600;border-color:#ffc600;text-decoration:none;}
        .notice{background:rgba(34,197,94,.14);border:1px solid rgba(34,197,94,.35);color:#86efac;border-radius:6px;padding:10px 14px;margin-bottom:14px;font-size:.84rem;}
        .error{background:rgba(239,68,68,.14);border:1px solid rgba(239,68,68,.35);color:#fecaca;border-radius:6px;padding:10px 14px;margin-bottom:14px;font-size:.84rem;}
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navigation.php'; ?>

<section class="settings-wrap">
    <div class="container">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:12px;">
            <div>
                <h1 style="margin:0;color:#ffc600;font-size:1.22rem;">Settings</h1>
                <p style="margin:4px 0 0;color:#7a9ac0;font-size:.82rem;">Admin only configuration dashboard.</p>
            </div>
            <a href="/dashboard.php" class="settings-link">← Dashboard</a>
        </div>

        <?php if ($notice !== ''): ?><div class="notice"><?php echo pe($notice); ?></div><?php endif; ?>
        <?php if ($error !== ''): ?><div class="error"><?php echo pe($error); ?></div><?php endif; ?>

        <form method="post" class="settings-grid">
            <article class="settings-card" style="grid-column:1/-1;">
                <h2>PayPal Settings</h2>
                <p>Configure PayPal mode and credentials. Secrets are masked after saving — enter a new value to update.</p>

                <label class="settings-label" for="paypal_mode">PayPal Mode</label>
                <select class="settings-select" id="paypal_mode" name="paypal_mode">
                    <option value="manual"  <?php echo ($settings['paypal']['mode'] ?? 'manual') === 'manual'  ? 'selected' : ''; ?>>Manual Invoice (send invoice/link manually)</option>
                    <option value="sandbox" <?php echo ($settings['paypal']['mode'] ?? 'manual') === 'sandbox' ? 'selected' : ''; ?>>Sandbox API (testing)</option>
                    <option value="live"    <?php echo ($settings['paypal']['mode'] ?? 'manual') === 'live'    ? 'selected' : ''; ?>>Live API (production)</option>
                </select>

                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px;margin-top:14px;">
                    <div>
                        <div style="color:#36f3ff;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px;padding-bottom:4px;border-bottom:1px solid rgba(54,243,255,.14);">Sandbox Credentials</div>
                        <label class="settings-label">Sandbox Client ID</label>
                        <input class="settings-input" name="sandbox_client_id" type="text" value="<?php echo pe($settings['paypal']['sandbox']['client_id'] ?? ''); ?>">
                        <label class="settings-label">Sandbox Secret <span style="color:#5a7a9e;font-size:.65rem;">(leave blank to keep existing)</span></label>
                        <input class="settings-input" name="sandbox_secret" type="password" placeholder="<?php echo ($settings['paypal']['sandbox']['secret'] ?? '') !== '' ? maskSecret($settings['paypal']['sandbox']['secret']) : ''; ?>" autocomplete="new-password">
                        <label class="settings-label">Sandbox Business Email</label>
                        <input class="settings-input" name="sandbox_business_email" type="email" value="<?php echo pe($settings['paypal']['sandbox']['business_email'] ?? ''); ?>">
                        <label class="settings-label">Sandbox Webhook ID <span style="color:#5a7a9e;font-size:.65rem;">(optional)</span></label>
                        <input class="settings-input" name="sandbox_webhook_id" type="text" value="<?php echo pe($settings['paypal']['sandbox']['webhook_id'] ?? ''); ?>">
                        <label class="settings-label">Sandbox PayPal.Me / Payment Link <span style="color:#5a7a9e;font-size:.65rem;">(optional)</span></label>
                        <input class="settings-input" name="sandbox_payment_link" type="url" value="<?php echo pe($settings['paypal']['sandbox']['payment_link'] ?? ''); ?>">
                    </div>
                    <div>
                        <div style="color:#ffc600;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px;padding-bottom:4px;border-bottom:1px solid rgba(255,198,0,.18);">Live Credentials</div>
                        <label class="settings-label">Live Client ID</label>
                        <input class="settings-input" name="live_client_id" type="text" value="<?php echo pe($settings['paypal']['live']['client_id'] ?? ''); ?>">
                        <label class="settings-label">Live Secret <span style="color:#5a7a9e;font-size:.65rem;">(leave blank to keep existing)</span></label>
                        <input class="settings-input" name="live_secret" type="password" placeholder="<?php echo ($settings['paypal']['live']['secret'] ?? '') !== '' ? maskSecret($settings['paypal']['live']['secret']) : ''; ?>" autocomplete="new-password">
                        <label class="settings-label">Live Business Email</label>
                        <input class="settings-input" name="live_business_email" type="email" value="<?php echo pe($settings['paypal']['live']['business_email'] ?? ''); ?>">
                        <label class="settings-label">Live Webhook ID <span style="color:#5a7a9e;font-size:.65rem;">(optional)</span></label>
                        <input class="settings-input" name="live_webhook_id" type="text" value="<?php echo pe($settings['paypal']['live']['webhook_id'] ?? ''); ?>">
                        <label class="settings-label">Live PayPal.Me / Payment Link <span style="color:#5a7a9e;font-size:.65rem;">(optional)</span></label>
                        <input class="settings-input" name="live_payment_link" type="url" value="<?php echo pe($settings['paypal']['live']['payment_link'] ?? ''); ?>">
                    </div>
                </div>

                <div style="margin-top:14px;padding-top:10px;border-top:1px solid rgba(54,243,255,.1);">
                    <div style="color:#a8bedc;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px;">General PayPal Settings</div>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:10px;">
                        <div>
                            <label class="settings-label" for="paypal_currency">Default Currency</label>
                            <input class="settings-input" id="paypal_currency" name="paypal_currency" type="text" maxlength="3" value="<?php echo pe($settings['paypal']['currency'] ?? 'USD'); ?>">
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;padding-top:18px;">
                            <input type="checkbox" id="paypal_require_payment" name="paypal_require_payment_before_work" value="1" <?php echo !empty($settings['paypal']['require_payment_before_work']) ? 'checked' : ''; ?>>
                            <label for="paypal_require_payment" class="settings-label" style="margin:0;text-transform:none;cursor:pointer;">Require payment before work begins</label>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;padding-top:18px;">
                            <input type="checkbox" id="paypal_manual_recording" name="paypal_enable_manual_recording" value="1" <?php echo !empty($settings['paypal']['enable_manual_recording']) ? 'checked' : ''; ?>>
                            <label for="paypal_manual_recording" class="settings-label" style="margin:0;text-transform:none;cursor:pointer;">Enable manual payment recording</label>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;padding-top:18px;">
                            <input type="checkbox" id="paypal_webhook_logging" name="paypal_enable_webhook_logging" value="1" <?php echo !empty($settings['paypal']['enable_webhook_logging']) ? 'checked' : ''; ?>>
                            <label for="paypal_webhook_logging" class="settings-label" style="margin:0;text-transform:none;cursor:pointer;">Enable webhook payload logging</label>
                        </div>
                    </div>
                    <label class="settings-label" for="paypal_invoice_message" style="margin-top:10px;">Invoice Default Message</label>
                    <textarea class="settings-textarea" id="paypal_invoice_message" name="paypal_invoice_message"><?php echo pe($settings['paypal']['invoice_message'] ?? ''); ?></textarea>
                    <label class="settings-label" for="paypal_payment_instructions">Payment Instructions (shown to customer)</label>
                    <textarea class="settings-textarea" id="paypal_payment_instructions" name="paypal_payment_instructions"><?php echo pe($settings['paypal']['payment_instructions'] ?? ''); ?></textarea>
                </div>

                <div style="margin-top:12px;"><button class="settings-btn" type="submit">Save Settings</button></div>
            </article>

            <article class="settings-card">
                <h2>Email Settings</h2>
                <label class="settings-label" for="email_from_name">From Name</label>
                <input class="settings-input" id="email_from_name" name="email_from_name" type="text" value="<?php echo pe($settings['email']['from_name'] ?? ''); ?>">
                <label class="settings-label" for="email_from_email">From Email</label>
                <input class="settings-input" id="email_from_email" name="email_from_email" type="email" value="<?php echo pe($settings['email']['from_email'] ?? ''); ?>">
                <label class="settings-label" for="email_reply_to">Reply-To</label>
                <input class="settings-input" id="email_reply_to" name="email_reply_to" type="email" value="<?php echo pe($settings['email']['reply_to'] ?? ''); ?>">
            </article>

            <article class="settings-card">
                <h2>Site Settings</h2>
                <label class="settings-label" for="site_company_name">Company Name</label>
                <input class="settings-input" id="site_company_name" name="site_company_name" type="text" value="<?php echo pe($settings['site']['company_name'] ?? ''); ?>">
                <label class="settings-label" for="site_support_email">Support Email</label>
                <input class="settings-input" id="site_support_email" name="site_support_email" type="email" value="<?php echo pe($settings['site']['support_email'] ?? ''); ?>">
                <label class="settings-label" for="site_support_phone">Support Phone</label>
                <input class="settings-input" id="site_support_phone" name="site_support_phone" type="text" value="<?php echo pe($settings['site']['support_phone'] ?? ''); ?>">
            </article>

            <article class="settings-card">
                <h2>Business Information</h2>
                <label class="settings-label" for="business_legal_name">Legal Name</label>
                <input class="settings-input" id="business_legal_name" name="business_legal_name" type="text" value="<?php echo pe($settings['business']['legal_name'] ?? ''); ?>">
                <label class="settings-label" for="business_address">Business Address</label>
                <textarea class="settings-textarea" id="business_address" name="business_address"><?php echo pe($settings['business']['address'] ?? ''); ?></textarea>
            </article>

            <article class="settings-card">
                <h2>Staff Management</h2>
                <p>Manage staff and admin user accounts.</p>
                <a class="settings-link" href="/staff/users.php">Open Staff Management</a>
            </article>

            <article class="settings-card">
                <h2>User Management</h2>
                <p>Manage client portal users and file access.</p>
                <a class="settings-link" href="/staff/client-portal.php">Open User Management</a>
            </article>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/jquery-1.12.3.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
