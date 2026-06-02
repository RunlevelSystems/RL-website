<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';
require_once __DIR__ . '/../includes/email.php';

if (portalIsLoggedIn()) {
    header('Location: /dashboard.php');
    exit;
}

$error   = '';
$successMessage = '';
$smtpConfigured = function_exists('rlsMailerHasSmtpConfiguration') ? rlsMailerHasSmtpConfiguration(rlsMailerSettings()) : false;

$form = [
    'name' => '',
    'email' => '',
    'username' => '',
    'phone' => '',
    'company' => '',
    'preferred_contact_method' => 'Email',
    'marketing_opt_in' => false,
];

foreach ($form as $key => $value) {
    if (isset($_POST[$key])) {
        if ($key === 'marketing_opt_in') {
            $form[$key] = $_POST[$key] === '1';
        } else {
            $form[$key] = trim((string)$_POST[$key]);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $name        = trim($_POST['name'] ?? '');
    $username    = trim($_POST['username'] ?? '');
    $password    = $_POST['password'] ?? '';
    $password2   = $_POST['password2'] ?? '';
    $email       = strtolower(trim($_POST['email'] ?? ''));
    $phone       = trim($_POST['phone'] ?? '');
    $company     = trim($_POST['company'] ?? '');
    $preferred   = trim($_POST['preferred_contact_method'] ?? 'Email');
    $agreement   = isset($_POST['acknowledge_portal']) && $_POST['acknowledge_portal'] === '1';
    $marketingOptIn = isset($_POST['marketing_opt_in']) && $_POST['marketing_opt_in'] === '1';
    $csrfToken   = $_POST['csrf_token'] ?? '';

    if (!portalVerifyCsrfToken($csrfToken)) {
        $error = 'Your session expired. Please refresh and try again.';
    } elseif ($username === '') {
        $error = 'Please enter a username.';
    } elseif ($password !== $password2) {
        $error = 'Passwords do not match.';
    } elseif (!in_array($preferred, ['Email', 'Phone', 'Dashboard Message'], true)) {
        $error = 'Please select a preferred contact method.';
    } elseif (!$agreement) {
        $error = 'You must acknowledge account terms before creating an account.';
    } else {
        $verificationToken = portalGenerateVerificationToken();
        $err = '';
        $registered = portalRegisterClient($username, $password, $email, $name, $err, [
            'name' => $name,
            'phone' => $phone,
            'company' => $company,
            'preferred_contact_method' => $preferred,
            'marketing_opt_in' => $marketingOptIn,
            'email_verification_token' => $verificationToken,
        ]);
        if ($registered) {
            $baseUrl = rlsSiteBaseUrl();
            $verifyUrl = $baseUrl . '/verify-email.php?token=' . urlencode($verificationToken);
            if ($smtpConfigured) {
                send_verification_email($email, $name !== '' ? $name : $username, $verifyUrl);
            } else {
                $successMessage = 'Account created. Email verification is not currently available. Runlevel Systems staff may verify your account manually.';
            }

            send_account_created_email(
                $email,
                $name !== '' ? $name : $username,
                $username,
                $baseUrl . '/login.php'
            );

            $staffEmail = rlsInternalNotificationEmail();
            if ($staffEmail !== '') {
                send_rls_email(
                    $staffEmail,
                    'New Runlevel Systems client account',
                    "A new client account was created.\n\nName: {$name}\nEmail: {$email}\nUsername: {$username}\n",
                    ['context' => 'staff-new-account']
                );
            }

            $message = $smtpConfigured ? 'registered' : 'registered_no_email';
            header('Location: /login.php?msg=' . urlencode($message));
            exit;
        } else {
            $error = $err;
        }
    }
}

$current_page = 'client-portal';
$header_class = 'inner-header';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../assets/images/RL-icon.png">
    <title>Create Account | Client Portal | Runlevel Systems</title>
    <link href="../assets/css/coreloop.css" rel="stylesheet">
    <style>
        .portal-wrap { padding: 60px 0 80px; }
        .portal-card { background: #0c1729; border: 1px solid rgba(54,243,255,0.2); border-radius: 12px; padding: 32px; }
        .portal-card h2 { color: #ffc600; margin-top: 0; }
        label { color: #a8bedc; font-size: 0.95rem; margin-bottom: 4px; display: block; }
        .portal-input { background: #09111d; color: #eaf3ff; border: 1px solid rgba(54,243,255,0.25); border-radius: 6px; padding: 10px 12px; margin-bottom: 16px; font-size: 1rem; width: 100%; }
        .portal-input:focus { outline: 2px solid #0a84ff; border-color: #0a84ff; }
        .portal-btn { background: #ffc600; color: #08111f; border: none; border-radius: 6px; padding: 11px 24px; font-weight: 700; font-size: 1rem; cursor: pointer; width: 100%; }
        .portal-btn:hover { background: #36f3ff; }
        .portal-alert-error { background: rgba(239,68,68,0.18); border: 1px solid #ef4444; color: #fecaca; border-radius: 6px; padding: 12px 14px; margin-bottom: 18px; }
        .portal-link-row { margin-top: 20px; text-align: center; font-size: 0.9rem; }
        .hint { color: #5a7a9e; font-size: 0.8rem; margin-top: -12px; margin-bottom: 14px; }
        .tos-note { background: rgba(54,243,255,0.06); border: 1px solid rgba(54,243,255,0.15); border-radius: 6px; padding: 12px; font-size: 0.875rem; color: #7a9ac0; margin-bottom: 16px; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>

<section class="portal-wrap">
    <div class="container">
        <div class="row" style="justify-content: center;">
            <div class="col-sm-5" style="flex: 0 0 480px; max-width: 480px;">
                <div class="portal-card">
                    <div style="text-align:center; margin-bottom: 24px;">
                        <div style="font-size: 2rem; margin-bottom: 8px;">✅</div>
                        <h2>Create Account</h2>
                        <p style="color:#7a9ac0; margin:0;">Set up your Runlevel Systems client account</p>
                    </div>

                    <?php if ($error !== ''): ?>
                        <div class="portal-alert-error"><?php echo pe($error); ?></div>
                    <?php endif; ?>
                    <?php if ($successMessage !== ''): ?>
                        <div class="tos-note" style="margin-bottom:18px;"><?php echo pe($successMessage); ?></div>
                    <?php endif; ?>

                    <form method="post">
                        <input type="hidden" name="register" value="1">
                        <input type="hidden" name="csrf_token" value="<?php echo pe(portalGetCsrfToken()); ?>">

                        <label for="reg_username">Username *</label>
                        <p class="hint" style="margin-top:0;margin-bottom:8px;">Choose a username for your account.</p>
                        <input id="reg_username" type="text" name="username" class="portal-input"
                               required
                               value="<?php echo pe($form['username']); ?>"
                               autocomplete="username" placeholder="Choose a username">

                        <label for="reg_password">Password *</label>
                        <p class="hint" style="margin-top:0;margin-bottom:8px;">At least 8 characters.</p>
                        <input id="reg_password" type="password" name="password" class="portal-input" required
                               autocomplete="new-password" placeholder="At least 8 characters">

                        <label for="reg_password2">Confirm Password *</label>
                        <p class="hint" style="margin-top:0;margin-bottom:8px;">Repeat your password.</p>
                        <input id="reg_password2" type="password" name="password2" class="portal-input" required
                               autocomplete="new-password" placeholder="Repeat your password">

                        <label for="reg_email">Email Address *</label>
                        <p class="hint" style="margin-top:0;margin-bottom:8px;">Used for project updates and account recovery.</p>
                        <input id="reg_email" type="email" name="email" class="portal-input" required
                               value="<?php echo pe($form['email']); ?>"
                               autocomplete="email" placeholder="you@example.com">

                        <label for="reg_name">Name</label>
                        <input id="reg_name" type="text" name="name" class="portal-input"
                               value="<?php echo pe($form['name']); ?>"
                               autocomplete="name" placeholder="Your full name">

                        <label for="reg_phone">Phone</label>
                        <input id="reg_phone" type="text" name="phone" class="portal-input"
                               value="<?php echo pe($form['phone']); ?>"
                               autocomplete="tel" placeholder="Best callback number">

                        <label for="reg_company">Company / Organization</label>
                        <input id="reg_company" type="text" name="company" class="portal-input"
                               value="<?php echo pe($form['company']); ?>"
                               placeholder="Company or organization">

                        <label for="reg_contact">Preferred Contact Method</label>
                        <select id="reg_contact" name="preferred_contact_method" class="portal-input">
                            <option value="Email" <?php echo $form['preferred_contact_method'] === 'Email' ? 'selected' : ''; ?>>Email</option>
                            <option value="Phone" <?php echo $form['preferred_contact_method'] === 'Phone' ? 'selected' : ''; ?>>Phone</option>
                            <option value="Dashboard Message" <?php echo $form['preferred_contact_method'] === 'Dashboard Message' ? 'selected' : ''; ?>>Dashboard Message</option>
                        </select>

                        <div class="tos-note">
                            <label class="checkbox-row" style="margin-bottom:10px;">
                                <input type="checkbox" name="acknowledge_portal" value="1" <?php echo isset($_POST['acknowledge_portal']) ? 'checked' : ''; ?>>
                                <span>I understand that creating an account allows me to submit requests and view project updates.</span>
                            </label>
                            <label class="checkbox-row" style="margin:0;">
                                <input type="checkbox" name="marketing_opt_in" value="1" <?php echo $form['marketing_opt_in'] ? 'checked' : ''; ?>>
                                <span>I agree to receive project-related email messages from Runlevel Systems.</span>
                            </label>
                        </div>

                        <div class="tos-note">
                            By creating an account you agree to the
                            <a href="/runlevel-terms.php" target="_blank" rel="noopener noreferrer" style="color:#36f3ff;">Runlevel Systems Terms of Service</a>.
                        </div>

                        <button type="submit" class="portal-btn">Create Account</button>
                    </form>

                    <div class="portal-link-row">
                        Already have an account? <a href="/login.php" style="color:#36f3ff;">Sign in</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="../assets/js/jquery-1.12.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>
