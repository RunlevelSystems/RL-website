<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/includes/portal-helpers.php';
require_once __DIR__ . '/includes/email.php';

portalRequireStaff(['admin']);

$current_page = 'dashboard';
$header_class = 'inner-header';

$user = portalGetUser();
$settings = portalLoadAdminSettings();
$notice = '';
$error = '';
$warning = '';
$testRecipient = trim((string)($_POST['email_test_recipient'] ?? ''));

function maskSecret($val) {
    $val = (string)$val;
    if ($val === '') {
        return '';
    }
    if (strlen($val) <= 8) {
        return str_repeat('•', strlen($val));
    }
    return substr($val, 0, 4) . str_repeat('•', min(24, strlen($val) - 4));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settingsAction = trim((string)($_POST['settings_action'] ?? 'save_settings'));
    $paypal = $settings['paypal'];
    $email = $settings['email'];
    $site = $settings['site'];
    $business = $settings['business'];

    $paypal['mode'] = in_array(trim((string)($_POST['paypal_mode'] ?? 'manual')), ['manual', 'sandbox', 'live'], true)
        ? trim((string)$_POST['paypal_mode'])
        : 'manual';
    $paypal['currency'] = strtoupper(substr(trim((string)($_POST['paypal_currency'] ?? 'USD')), 0, 3));
    $paypal['invoice_message'] = trim((string)($_POST['paypal_invoice_message'] ?? ''));
    $paypal['payment_instructions'] = trim((string)($_POST['paypal_payment_instructions'] ?? ''));
    $paypal['require_payment_before_work'] = (bool)($_POST['paypal_require_payment_before_work'] ?? false);
    $paypal['enable_manual_recording'] = (bool)($_POST['paypal_enable_manual_recording'] ?? false);
    $paypal['enable_webhook_logging'] = (bool)($_POST['paypal_enable_webhook_logging'] ?? false);

    $paypal['sandbox']['client_id'] = trim((string)($_POST['sandbox_client_id'] ?? ''));
    $paypal['sandbox']['business_email'] = trim((string)($_POST['sandbox_business_email'] ?? ''));
    $paypal['sandbox']['webhook_id'] = trim((string)($_POST['sandbox_webhook_id'] ?? ''));
    $paypal['sandbox']['payment_link'] = trim((string)($_POST['sandbox_payment_link'] ?? ''));
    $sandboxSecret = trim((string)($_POST['sandbox_secret'] ?? ''));
    if ($sandboxSecret !== '') {
        $paypal['sandbox']['secret'] = $sandboxSecret;
    }

    $paypal['live']['client_id'] = trim((string)($_POST['live_client_id'] ?? ''));
    $paypal['live']['business_email'] = trim((string)($_POST['live_business_email'] ?? ''));
    $paypal['live']['webhook_id'] = trim((string)($_POST['live_webhook_id'] ?? ''));
    $paypal['live']['payment_link'] = trim((string)($_POST['live_payment_link'] ?? ''));
    $liveSecret = trim((string)($_POST['live_secret'] ?? ''));
    if ($liveSecret !== '') {
        $paypal['live']['secret'] = $liveSecret;
    }

    $email['from_name'] = trim((string)($_POST['email_from_name'] ?? 'Runlevel Systems'));
    $email['from_email'] = trim((string)($_POST['email_from_email'] ?? 'billing@runlevelsystems.com'));
    $email['reply_to'] = trim((string)($_POST['email_reply_to'] ?? 'billing@runlevelsystems.com'));
    $email['smtp_host'] = trim((string)($_POST['email_smtp_host'] ?? 'mail.runlevelsystems.com'));
    $email['smtp_port'] = (int)($_POST['email_smtp_port'] ?? 465);
    $email['smtp_security'] = in_array(trim((string)($_POST['email_smtp_security'] ?? 'ssl')), ['ssl', 'tls', 'none'], true)
        ? trim((string)$_POST['email_smtp_security'])
        : 'ssl';
    $email['smtp_auth'] = (trim((string)($_POST['email_smtp_auth'] ?? 'enabled')) !== 'disabled');
    $email['smtp_username'] = trim((string)($_POST['email_smtp_username'] ?? 'billing@runlevelsystems.com'));
    $smtpPassword = trim((string)($_POST['email_smtp_password'] ?? ''));
    if ($smtpPassword !== '') {
        $email['smtp_password'] = $smtpPassword;
    }
    $email['smtp_debug'] = in_array(trim((string)($_POST['email_smtp_debug'] ?? 'off')), ['off', 'basic', 'verbose'], true)
        ? trim((string)$_POST['email_smtp_debug'])
        : 'off';

    $site['company_name'] = trim((string)($_POST['site_company_name'] ?? 'Runlevel Systems'));
    $site['support_email'] = trim((string)($_POST['site_support_email'] ?? ''));
    $site['support_phone'] = trim((string)($_POST['site_support_phone'] ?? ''));

    $business['legal_name'] = trim((string)($_POST['business_legal_name'] ?? ''));
    $business['address'] = trim((string)($_POST['business_address'] ?? ''));

    if (($paypal['sandbox']['business_email'] ?? '') !== '' && !filter_var((string)$paypal['sandbox']['business_email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid sandbox business email.';
    } elseif (($paypal['live']['business_email'] ?? '') !== '' && !filter_var((string)$paypal['live']['business_email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid live business email.';
    } elseif ($email['from_email'] === '' || !filter_var($email['from_email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid sender email.';
    } elseif ($email['reply_to'] !== '' && !filter_var($email['reply_to'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid reply-to email.';
    } elseif ($email['smtp_auth'] && ($email['smtp_username'] === '' || !filter_var($email['smtp_username'], FILTER_VALIDATE_EMAIL))) {
        $error = 'Please enter a valid SMTP username email address.';
    } elseif ($email['smtp_host'] === '') {
        $error = 'Please enter the SMTP host.';
    } elseif ($email['smtp_port'] < 1 || $email['smtp_port'] > 65535) {
        $error = 'Please enter a valid SMTP port.';
    } elseif ($site['support_email'] !== '' && !filter_var($site['support_email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid support email.';
    } elseif ($settingsAction === 'send_test_email' && ($testRecipient === '' || !filter_var($testRecipient, FILTER_VALIDATE_EMAIL))) {
        $error = 'Please enter a valid test recipient email address.';
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

        if (!portalSaveAdminSettings($settings)) {
            $error = 'Unable to save settings.';
        } else {
            $settings = portalLoadAdminSettings();
            if ($settingsAction === 'send_test_email') {
                if (send_smtp_test_email($testRecipient)) {
                    $notice = 'Settings saved. Test email sent to ' . $testRecipient . '.';
                    $warning = rlsGetLastEmailWarning();
                } else {
                    $error = 'Settings saved, but the test email failed. ' . (rlsGetLastEmailError() !== '' ? rlsGetLastEmailError() : 'Check your SMTP settings and try again.');
                    $warning = rlsGetLastEmailWarning();
                }
            } else {
                $notice = 'Settings saved.';
            }
        }
    }
}

$recentEmailActivity = array_slice(array_reverse(portalLoadEmailLog()), 0, 12);
$smtpPasswordPlaceholder = ($settings['email']['smtp_password'] ?? '') !== ''
    ? 'Password saved — leave blank to keep current password.'
    : 'Enter SMTP password';
$smtpConfigured = rlsMailerHasSmtpConfiguration($settings['email']);
$transportSummary = rlsMailerTransportSummary();
$mailerInfo = rlsMailerHasPhpMailer()
    ? 'PHPMailer is available and will be used for SMTP delivery.'
    : 'PHPMailer is not installed. The built-in SMTP mailer will be used instead.';
if (!$smtpConfigured && $warning === '') {
    $warning = 'SMTP settings are incomplete. The site will fall back to PHP mail() until SMTP is configured.';
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
        .settings-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:14px;}
        .settings-card{background:#0c1729;border:1px solid rgba(54,243,255,.16);border-radius:12px;padding:16px;}
        .settings-card.full{grid-column:1/-1;}
        .settings-card h2{margin:0 0 10px;color:#ffc600;font-size:.96rem;text-transform:uppercase;letter-spacing:.05em;}
        .settings-card p{margin:0 0 10px;color:#7a9ac0;font-size:.84rem;line-height:1.55;}
        .settings-label{display:block;color:#a8bedc;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;margin:10px 0 5px;}
        .settings-input,.settings-select,.settings-textarea{width:100%;background:#09111d;border:1px solid rgba(54,243,255,.22);border-radius:8px;color:#eaf3ff;padding:10px 12px;font-size:.86rem;}
        .settings-textarea{min-height:92px;resize:vertical;}
        .settings-row{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;}
        .settings-actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:14px;}
        .settings-btn{display:inline-block;border:none;border-radius:8px;padding:10px 16px;background:#ffc600;color:#08111f;font-size:.84rem;font-weight:700;}
        .settings-btn.secondary{background:rgba(54,243,255,.12);border:1px solid rgba(54,243,255,.3);color:#36f3ff;}
        .settings-link{display:inline-block;margin-right:8px;border:1px solid rgba(54,243,255,.3);background:rgba(54,243,255,.08);color:#36f3ff;border-radius:8px;padding:8px 12px;font-size:.8rem;text-decoration:none;}
        .settings-link:hover{color:#ffc600;border-color:#ffc600;text-decoration:none;}
        .notice,.error,.warning{border-radius:8px;padding:12px 14px;margin-bottom:14px;font-size:.84rem;}
        .notice{background:rgba(34,197,94,.14);border:1px solid rgba(34,197,94,.35);color:#86efac;}
        .error{background:rgba(239,68,68,.14);border:1px solid rgba(239,68,68,.35);color:#fecaca;}
        .warning{background:rgba(245,158,11,.14);border:1px solid rgba(245,158,11,.35);color:#fde68a;}
        .settings-help{background:rgba(255,198,0,.06);border:1px solid rgba(255,198,0,.18);border-radius:10px;padding:14px;}
        .settings-help strong{display:block;color:#ffc600;font-size:.82rem;margin-bottom:8px;}
        .settings-help dl{margin:0;display:grid;grid-template-columns:minmax(110px,150px) 1fr;gap:6px 10px;}
        .settings-help dt{color:#a8bedc;font-size:.74rem;text-transform:uppercase;letter-spacing:.04em;}
        .settings-help dd{margin:0;color:#eaf3ff;font-size:.84rem;word-break:break-word;}
        .settings-meta{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:10px;margin-top:10px;}
        .settings-meta div{background:#09111d;border:1px solid rgba(54,243,255,.12);border-radius:8px;padding:10px;}
        .settings-meta span{display:block;color:#5a7a9e;font-size:.7rem;text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px;}
        .activity-list{display:flex;flex-direction:column;gap:10px;}
        .activity-item{display:grid;grid-template-columns:minmax(0,160px) minmax(0,1fr) minmax(0,1.4fr) 100px;gap:10px;align-items:start;background:#09111d;border:1px solid rgba(54,243,255,.12);border-radius:10px;padding:12px;}
        .activity-label{display:block;color:#5a7a9e;font-size:.68rem;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;}
        .activity-value{color:#eaf3ff;font-size:.84rem;word-break:break-word;}
        .status-pill{display:inline-block;padding:5px 10px;border-radius:999px;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;}
        .status-pill.sent{background:rgba(34,197,94,.14);color:#86efac;border:1px solid rgba(34,197,94,.35);}
        .status-pill.failed{background:rgba(239,68,68,.14);color:#fecaca;border:1px solid rgba(239,68,68,.35);}
        .settings-note{color:#5a7a9e;font-size:.76rem;margin-top:6px;}
        @media(max-width:760px){
            .settings-row{grid-template-columns:1fr;}
            .activity-item{grid-template-columns:1fr;}
        }
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
        <?php if ($warning !== ''): ?><div class="warning"><?php echo pe($warning); ?></div><?php endif; ?>

        <form method="post">
            <div class="settings-grid">
                <article class="settings-card full">
                    <h2>PayPal Settings</h2>
                    <p>Configure PayPal mode and credentials. Secrets stay masked after saving — enter a new value only when you want to replace one.</p>

                    <label class="settings-label" for="paypal_mode">PayPal Mode</label>
                    <select class="settings-select" id="paypal_mode" name="paypal_mode">
                        <option value="manual" <?php echo ($settings['paypal']['mode'] ?? 'manual') === 'manual' ? 'selected' : ''; ?>>Manual Invoice</option>
                        <option value="sandbox" <?php echo ($settings['paypal']['mode'] ?? 'manual') === 'sandbox' ? 'selected' : ''; ?>>Sandbox API</option>
                        <option value="live" <?php echo ($settings['paypal']['mode'] ?? 'manual') === 'live' ? 'selected' : ''; ?>>Live API</option>
                    </select>

                    <div class="settings-row" style="margin-top:14px;">
                        <div>
                            <div style="color:#36f3ff;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px;padding-bottom:4px;border-bottom:1px solid rgba(54,243,255,.14);">Sandbox Credentials</div>
                            <label class="settings-label">Sandbox Client ID</label>
                            <input class="settings-input" name="sandbox_client_id" type="text" value="<?php echo pe($settings['paypal']['sandbox']['client_id'] ?? ''); ?>">
                            <label class="settings-label">Sandbox Secret</label>
                            <input class="settings-input" name="sandbox_secret" type="password" placeholder="<?php echo ($settings['paypal']['sandbox']['secret'] ?? '') !== '' ? maskSecret($settings['paypal']['sandbox']['secret']) : ''; ?>" autocomplete="new-password">
                            <label class="settings-label">Sandbox Business Email</label>
                            <input class="settings-input" name="sandbox_business_email" type="email" value="<?php echo pe($settings['paypal']['sandbox']['business_email'] ?? ''); ?>">
                            <label class="settings-label">Sandbox Webhook ID</label>
                            <input class="settings-input" name="sandbox_webhook_id" type="text" value="<?php echo pe($settings['paypal']['sandbox']['webhook_id'] ?? ''); ?>">
                            <label class="settings-label">Sandbox PayPal.Me / Payment Link</label>
                            <input class="settings-input" name="sandbox_payment_link" type="url" value="<?php echo pe($settings['paypal']['sandbox']['payment_link'] ?? ''); ?>">
                        </div>
                        <div>
                            <div style="color:#ffc600;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px;padding-bottom:4px;border-bottom:1px solid rgba(255,198,0,.18);">Live Credentials</div>
                            <label class="settings-label">Live Client ID</label>
                            <input class="settings-input" name="live_client_id" type="text" value="<?php echo pe($settings['paypal']['live']['client_id'] ?? ''); ?>">
                            <label class="settings-label">Live Secret</label>
                            <input class="settings-input" name="live_secret" type="password" placeholder="<?php echo ($settings['paypal']['live']['secret'] ?? '') !== '' ? maskSecret($settings['paypal']['live']['secret']) : ''; ?>" autocomplete="new-password">
                            <label class="settings-label">Live Business Email</label>
                            <input class="settings-input" name="live_business_email" type="email" value="<?php echo pe($settings['paypal']['live']['business_email'] ?? ''); ?>">
                            <label class="settings-label">Live Webhook ID</label>
                            <input class="settings-input" name="live_webhook_id" type="text" value="<?php echo pe($settings['paypal']['live']['webhook_id'] ?? ''); ?>">
                            <label class="settings-label">Live PayPal.Me / Payment Link</label>
                            <input class="settings-input" name="live_payment_link" type="url" value="<?php echo pe($settings['paypal']['live']['payment_link'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="settings-row" style="margin-top:14px;">
                        <div>
                            <label class="settings-label" for="paypal_currency">Default Currency</label>
                            <input class="settings-input" id="paypal_currency" name="paypal_currency" type="text" maxlength="3" value="<?php echo pe($settings['paypal']['currency'] ?? 'USD'); ?>">
                        </div>
                        <div>
                            <label class="settings-label" for="paypal_invoice_message">Invoice Default Message</label>
                            <textarea class="settings-textarea" id="paypal_invoice_message" name="paypal_invoice_message"><?php echo pe($settings['paypal']['invoice_message'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <label class="settings-label" for="paypal_payment_instructions">Payment Instructions</label>
                    <textarea class="settings-textarea" id="paypal_payment_instructions" name="paypal_payment_instructions"><?php echo pe($settings['paypal']['payment_instructions'] ?? ''); ?></textarea>

                    <div class="settings-actions">
                        <label style="display:flex;align-items:center;gap:8px;color:#d8e7f7;font-size:.82rem;"><input type="checkbox" name="paypal_require_payment_before_work" value="1" <?php echo !empty($settings['paypal']['require_payment_before_work']) ? 'checked' : ''; ?>> Require payment before work begins</label>
                        <label style="display:flex;align-items:center;gap:8px;color:#d8e7f7;font-size:.82rem;"><input type="checkbox" name="paypal_enable_manual_recording" value="1" <?php echo !empty($settings['paypal']['enable_manual_recording']) ? 'checked' : ''; ?>> Enable manual payment recording</label>
                        <label style="display:flex;align-items:center;gap:8px;color:#d8e7f7;font-size:.82rem;"><input type="checkbox" name="paypal_enable_webhook_logging" value="1" <?php echo !empty($settings['paypal']['enable_webhook_logging']) ? 'checked' : ''; ?>> Enable webhook payload logging</label>
                    </div>
                </article>

                <article class="settings-card">
                    <h2>Email Settings</h2>
                    <p>These sender details are used for portal notices, request confirmations, payment emails, and test messages.</p>

                    <label class="settings-label" for="email_from_name">From Name</label>
                    <input class="settings-input" id="email_from_name" name="email_from_name" type="text" value="<?php echo pe($settings['email']['from_name'] ?? 'Runlevel Systems'); ?>">

                    <label class="settings-label" for="email_from_email">From Email</label>
                    <input class="settings-input" id="email_from_email" name="email_from_email" type="email" value="<?php echo pe($settings['email']['from_email'] ?? 'billing@runlevelsystems.com'); ?>">

                    <label class="settings-label" for="email_reply_to">Reply-To Email</label>
                    <input class="settings-input" id="email_reply_to" name="email_reply_to" type="email" value="<?php echo pe($settings['email']['reply_to'] ?? 'billing@runlevelsystems.com'); ?>">

                    <div class="settings-meta">
                        <div>
                            <span>Mailer Transport</span>
                            <?php echo pe($transportSummary); ?>
                        </div>
                        <div>
                            <span>SMTP Status</span>
                            <?php echo $smtpConfigured ? 'Configured' : 'Needs setup'; ?>
                        </div>
                    </div>
                    <p class="settings-note"><?php echo pe($mailerInfo); ?></p>
                </article>

                <article class="settings-card full">
                    <h2>SMTP Mail Settings</h2>
                    <p>Configure secure SMTP delivery for proposal notices, request confirmations, payment notices, staff alerts, and portal emails.</p>

                    <div class="settings-row">
                        <div>
                            <label class="settings-label" for="email_smtp_host">SMTP Host</label>
                            <input class="settings-input" id="email_smtp_host" name="email_smtp_host" type="text" value="<?php echo pe($settings['email']['smtp_host'] ?? 'mail.runlevelsystems.com'); ?>">
                        </div>
                        <div>
                            <label class="settings-label" for="email_smtp_port">SMTP Port</label>
                            <input class="settings-input" id="email_smtp_port" name="email_smtp_port" type="number" min="1" max="65535" value="<?php echo pe((string)($settings['email']['smtp_port'] ?? 465)); ?>">
                        </div>
                    </div>

                    <div class="settings-row">
                        <div>
                            <label class="settings-label" for="email_smtp_security">SMTP Security</label>
                            <select class="settings-select" id="email_smtp_security" name="email_smtp_security">
                                <option value="ssl" <?php echo ($settings['email']['smtp_security'] ?? 'ssl') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                <option value="tls" <?php echo ($settings['email']['smtp_security'] ?? 'ssl') === 'tls' ? 'selected' : ''; ?>>TLS</option>
                                <option value="none" <?php echo ($settings['email']['smtp_security'] ?? 'ssl') === 'none' ? 'selected' : ''; ?>>None</option>
                            </select>
                        </div>
                        <div>
                            <label class="settings-label" for="email_smtp_auth">SMTP Authentication</label>
                            <select class="settings-select" id="email_smtp_auth" name="email_smtp_auth">
                                <option value="enabled" <?php echo !empty($settings['email']['smtp_auth']) ? 'selected' : ''; ?>>Enabled</option>
                                <option value="disabled" <?php echo empty($settings['email']['smtp_auth']) ? 'selected' : ''; ?>>Disabled</option>
                            </select>
                        </div>
                    </div>

                    <div class="settings-row">
                        <div>
                            <label class="settings-label" for="email_smtp_username">SMTP Username</label>
                            <input class="settings-input" id="email_smtp_username" name="email_smtp_username" type="email" value="<?php echo pe($settings['email']['smtp_username'] ?? 'billing@runlevelsystems.com'); ?>">
                        </div>
                        <div>
                            <label class="settings-label" for="email_smtp_debug">SMTP Debug Mode</label>
                            <select class="settings-select" id="email_smtp_debug" name="email_smtp_debug">
                                <option value="off" <?php echo ($settings['email']['smtp_debug'] ?? 'off') === 'off' ? 'selected' : ''; ?>>Off</option>
                                <option value="basic" <?php echo ($settings['email']['smtp_debug'] ?? 'off') === 'basic' ? 'selected' : ''; ?>>Basic</option>
                                <option value="verbose" <?php echo ($settings['email']['smtp_debug'] ?? 'off') === 'verbose' ? 'selected' : ''; ?>>Verbose</option>
                            </select>
                        </div>
                    </div>

                    <label class="settings-label" for="email_smtp_password">SMTP Password</label>
                    <input class="settings-input" id="email_smtp_password" name="email_smtp_password" type="password" placeholder="<?php echo pe($smtpPasswordPlaceholder); ?>" autocomplete="new-password">
                    <p class="settings-note">Saved passwords are never displayed. TODO: Move secrets to environment variables or protected server config before production.</p>

                    <div class="settings-row">
                        <div>
                            <label class="settings-label" for="email_test_recipient">Test Recipient Email</label>
                            <input class="settings-input" id="email_test_recipient" name="email_test_recipient" type="email" value="<?php echo pe($testRecipient); ?>" placeholder="name@example.com">
                        </div>
                        <div>
                            <div class="settings-help" style="height:100%;">
                                <strong>Secure SSL/TLS Mail Settings</strong>
                                <dl>
                                    <dt>Username</dt><dd>billing@runlevelsystems.com</dd>
                                    <dt>Incoming Server</dt><dd>mail.runlevelsystems.com</dd>
                                    <dt>IMAP Port</dt><dd>993</dd>
                                    <dt>POP3 Port</dt><dd>995</dd>
                                    <dt>Outgoing SMTP Server</dt><dd>mail.runlevelsystems.com</dd>
                                    <dt>SMTP Port</dt><dd>465</dd>
                                    <dt>SMTP Auth</dt><dd>SMTP requires authentication.</dd>
                                </dl>
                                <p class="settings-note">Only SMTP settings are required for sending website emails. IMAP and POP3 are listed for reference only.</p>
                            </div>
                        </div>
                    </div>

                    <div class="settings-actions">
                        <button class="settings-btn" type="submit" name="settings_action" value="save_settings">Save Settings</button>
                        <button class="settings-btn secondary" type="submit" name="settings_action" value="send_test_email">Send Test Email</button>
                    </div>
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

                <article class="settings-card full">
                    <h2>Recent Email Activity</h2>
                    <p>Recent delivery attempts from the website email system.</p>

                    <?php if (empty($recentEmailActivity)): ?>
                        <div class="settings-help"><p style="margin:0;">No email activity has been logged yet.</p></div>
                    <?php else: ?>
                        <div class="activity-list">
                            <?php foreach ($recentEmailActivity as $logItem): ?>
                                <div class="activity-item">
                                    <div>
                                        <span class="activity-label">Date</span>
                                        <div class="activity-value">
                                            <?php $ts = strtotime((string)($logItem['timestamp'] ?? '')); ?>
                                            <?php echo $ts ? pe(date('M j, Y g:i A', $ts)) : '—'; ?>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="activity-label">Recipient</span>
                                        <div class="activity-value"><?php echo pe($logItem['to'] ?? ''); ?></div>
                                    </div>
                                    <div>
                                        <span class="activity-label">Subject</span>
                                        <div class="activity-value"><?php echo pe($logItem['subject'] ?? ''); ?></div>
                                        <?php if (($logItem['context'] ?? '') !== ''): ?>
                                            <div class="settings-note"><?php echo pe($logItem['context']); ?></div>
                                        <?php endif; ?>
                                        <?php if (($logItem['error_message'] ?? '') !== ''): ?>
                                            <div class="settings-note" style="color:#fca5a5;"><?php echo pe($logItem['error_message']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <span class="activity-label">Status</span>
                                        <span class="status-pill <?php echo ($logItem['status'] ?? '') === 'sent' ? 'sent' : 'failed'; ?>">
                                            <?php echo pe($logItem['status'] ?? 'unknown'); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </article>
            </div>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/jquery-1.12.3.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
