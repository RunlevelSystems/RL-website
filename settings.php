<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/includes/portal-helpers.php';
portalRequireStaff(['admin']);

$current_page = 'dashboard';
$header_class = 'inner-header';

$user = portalGetUser();
$settings = portalLoadAdminSettings();
$notice = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paypal = $settings['paypal'];
    $email = $settings['email'];
    $site = $settings['site'];
    $business = $settings['business'];

    $paypal['client_id'] = trim((string)($_POST['paypal_client_id'] ?? ''));
    $paypal['secret'] = trim((string)($_POST['paypal_secret'] ?? ''));
    $paypal['environment'] = trim((string)($_POST['paypal_environment'] ?? 'sandbox')) === 'production' ? 'production' : 'sandbox';
    $paypal['business_email'] = trim((string)($_POST['paypal_business_email'] ?? ''));
    $paypal['invoice_defaults'] = trim((string)($_POST['paypal_invoice_defaults'] ?? ''));

    $email['from_name'] = trim((string)($_POST['email_from_name'] ?? ''));
    $email['from_email'] = trim((string)($_POST['email_from_email'] ?? ''));
    $email['reply_to'] = trim((string)($_POST['email_reply_to'] ?? ''));

    $site['company_name'] = trim((string)($_POST['site_company_name'] ?? 'Runlevel Systems'));
    $site['support_email'] = trim((string)($_POST['site_support_email'] ?? ''));
    $site['support_phone'] = trim((string)($_POST['site_support_phone'] ?? ''));

    $business['legal_name'] = trim((string)($_POST['business_legal_name'] ?? ''));
    $business['address'] = trim((string)($_POST['business_address'] ?? ''));

    if ($paypal['business_email'] !== '' && !filter_var($paypal['business_email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid PayPal business email.';
    } elseif ($email['from_email'] !== '' && !filter_var($email['from_email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid sender email.';
    } elseif ($email['reply_to'] !== '' && !filter_var($email['reply_to'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid reply-to email.';
    } elseif ($site['support_email'] !== '' && !filter_var($site['support_email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid support email.';
    }

    if ($error === '') {
        $settings = [
            'paypal' => $paypal,
            'email' => $email,
            'site' => $site,
            'business' => $business,
            'updated_at' => date('c'),
            'updated_by' => (string)($user['username'] ?? 'admin'),
        ];
        if (portalSaveAdminSettings($settings)) {
            $notice = 'Settings saved.';
        } else {
            $error = 'Unable to save settings.';
        }
    }
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
            <article class="settings-card">
                <h2>PayPal Settings</h2>
                <p>PayPal Configuration (Admin Only).</p>
                <label class="settings-label" for="paypal_client_id">Client ID</label>
                <input class="settings-input" id="paypal_client_id" name="paypal_client_id" type="text" value="<?php echo pe($settings['paypal']['client_id'] ?? ''); ?>">

                <label class="settings-label" for="paypal_secret">Secret</label>
                <input class="settings-input" id="paypal_secret" name="paypal_secret" type="password" value="<?php echo pe($settings['paypal']['secret'] ?? ''); ?>">

                <label class="settings-label" for="paypal_environment">Environment</label>
                <select class="settings-select" id="paypal_environment" name="paypal_environment">
                    <option value="sandbox" <?php echo ($settings['paypal']['environment'] ?? 'sandbox') === 'sandbox' ? 'selected' : ''; ?>>Sandbox</option>
                    <option value="production" <?php echo ($settings['paypal']['environment'] ?? '') === 'production' ? 'selected' : ''; ?>>Production</option>
                </select>

                <label class="settings-label" for="paypal_business_email">Business Email</label>
                <input class="settings-input" id="paypal_business_email" name="paypal_business_email" type="email" value="<?php echo pe($settings['paypal']['business_email'] ?? ''); ?>">

                <label class="settings-label" for="paypal_invoice_defaults">Invoice Defaults</label>
                <textarea class="settings-textarea" id="paypal_invoice_defaults" name="paypal_invoice_defaults"><?php echo pe($settings['paypal']['invoice_defaults'] ?? ''); ?></textarea>

                <div style="margin-top:10px;"><button class="settings-btn" type="submit">Save Settings</button></div>
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
