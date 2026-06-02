<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';
require_once __DIR__ . '/../includes/email.php';

portalRequireLogin();

$sessionUser = portalGetUser();
$user = $sessionUser && !empty($sessionUser['username']) ? portalFindUserByUsername((string)$sessionUser['username']) : null;

if (!$user) {
    portalLogout();
    header('Location: /login.php');
    exit;
}

$message = '';
$error = '';
$smtpConfigured = function_exists('rlsMailerHasSmtpConfiguration') ? rlsMailerHasSmtpConfiguration(rlsMailerSettings()) : false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_account'])) {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!portalVerifyCsrfToken($csrf)) {
        $error = 'Your session expired. Please refresh and try again.';
    } else {
        $name = trim((string)($_POST['name'] ?? ''));
        $phone = trim((string)($_POST['phone'] ?? ''));
        $company = trim((string)($_POST['company'] ?? ''));
        $preferred = trim((string)($_POST['preferred_contact_method'] ?? 'Email'));
        $email = strtolower(trim((string)($_POST['email'] ?? '')));

        if ($name === '') {
            $error = 'Name is required.';
        } elseif ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'A valid email address is required.';
        } elseif (!in_array($preferred, ['Email', 'Phone', 'Dashboard Message'], true)) {
            $error = 'Select a valid preferred contact method.';
        } else {
            $existing = portalFindUserByEmail($email);
            if ($existing && strcasecmp((string)($existing['username'] ?? ''), (string)$user['username']) !== 0) {
                $error = 'That email is already in use by another account.';
            } else {
                $emailChanged = strcasecmp((string)($user['email'] ?? ''), $email) !== 0;
                $updated = $user;
                $updated['name'] = $name;
                $updated['display_name'] = $name;
                $updated['phone'] = $phone;
                $updated['company'] = $company;
                $updated['preferred_contact_method'] = $preferred;
                $updated['email'] = $email;
                if ($emailChanged) {
                    $updated['email_verified'] = false;
                    if (($updated['account_status'] ?? '') !== 'staff_approved') {
                        $updated['account_status'] = 'unverified';
                    }
                    $updated['email_verification_token'] = portalGenerateVerificationToken();
                    $updated['email_verification_sent_at'] = date('c');
                    $updated['verification_token'] = $updated['email_verification_token'];
                    $updated['verification_expires_at'] = date('c', time() + (48 * 3600));
                }
                $updated['updated_at'] = date('c');

                if (!portalUpdateUser($updated)) {
                    $error = 'Unable to save account changes right now.';
                } else {
                    $user = portalNormalizeUserRecord($updated);
                    $_SESSION[PORTAL_UNIFIED_SESSION]['display_name'] = $user['display_name'];
                    $message = 'Account details updated.';
                    if ($emailChanged) {
                        if ($smtpConfigured) {
                            $verifyUrl = rlsSiteBaseUrl() . '/verify-email.php?token=' . urlencode((string)$user['email_verification_token']);
                            send_verification_email($user['email'], $user['display_name'] ?: $user['username'], $verifyUrl);
                            $message = 'Account details updated. Please verify your new email address.';
                        } else {
                            $message = 'Account details updated. Email verification is not currently available; staff may verify manually.';
                        }
                    }
                }
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!portalVerifyCsrfToken($csrf)) {
        $error = 'Your session expired. Please refresh and try again.';
    } else {
        $currentPassword = (string)($_POST['current_password'] ?? '');
        $newPassword = (string)($_POST['new_password'] ?? '');
        $confirmPassword = (string)($_POST['confirm_password'] ?? '');

        if (!portalPasswordMatches($user, $currentPassword)) {
            $error = 'Current password is incorrect.';
        } elseif (strlen($newPassword) < 8) {
            $error = 'New password must be at least 8 characters.';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'New passwords do not match.';
        } else {
            $user['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
            $user['password'] = '';
            $user['updated_at'] = date('c');
            if (!portalUpdateUser($user)) {
                $error = 'Unable to change password right now.';
            } else {
                $message = 'Password updated successfully.';
            }
        }
    }
}

$current_page = 'dashboard';
$header_class = 'inner-header';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="/assets/images/RL-icon.png">
    <title>Account Settings | Runlevel Systems</title>
    <link href="/assets/css/coreloop.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>

<section style="padding:40px 0 80px;">
    <div class="container" style="max-width:900px;">
        <div class="dashboard-card" style="margin-bottom:20px;">
            <h2 style="margin-top:0;color:#00d4ff;">Account Settings</h2>
            <p style="color:#94a3b8;">Account Status: <strong style="color:#ffd166;"><?php echo pe(ucwords(str_replace('_', ' ', (string)($user['account_status'] ?? 'unverified')))); ?></strong></p>
            <?php if ($message !== ''): ?>
                <div style="background:rgba(34,197,94,0.15);border:1px solid rgba(34,197,94,0.4);padding:10px 12px;border-radius:6px;color:#86efac;margin-bottom:14px;"><?php echo pe($message); ?></div>
            <?php endif; ?>
            <?php if ($error !== ''): ?>
                <div style="background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.4);padding:10px 12px;border-radius:6px;color:#fca5a5;margin-bottom:14px;"><?php echo pe($error); ?></div>
            <?php endif; ?>

            <form method="post" style="margin-bottom:24px;">
                <input type="hidden" name="save_account" value="1">
                <input type="hidden" name="csrf_token" value="<?php echo pe(portalGetCsrfToken()); ?>">
                <div class="row">
                    <div class="col-sm-6">
                        <label for="name" style="display:block;color:#a8bedc;margin-bottom:6px;">Name</label>
                        <input id="name" class="form-control" type="text" name="name" required value="<?php echo pe((string)($user['name'] ?? $user['display_name'] ?? '')); ?>">
                    </div>
                    <div class="col-sm-6">
                        <label for="email" style="display:block;color:#a8bedc;margin-bottom:6px;">Email</label>
                        <input id="email" class="form-control" type="email" name="email" required value="<?php echo pe((string)($user['email'] ?? '')); ?>">
                    </div>
                </div>
                <div class="row" style="margin-top:12px;">
                    <div class="col-sm-6">
                        <label for="phone" style="display:block;color:#a8bedc;margin-bottom:6px;">Phone</label>
                        <input id="phone" class="form-control" type="text" name="phone" value="<?php echo pe((string)($user['phone'] ?? '')); ?>">
                    </div>
                    <div class="col-sm-6">
                        <label for="company" style="display:block;color:#a8bedc;margin-bottom:6px;">Company</label>
                        <input id="company" class="form-control" type="text" name="company" value="<?php echo pe((string)($user['company'] ?? '')); ?>">
                    </div>
                </div>
                <div style="margin-top:12px;">
                    <label for="preferred_contact_method" style="display:block;color:#a8bedc;margin-bottom:6px;">Preferred Contact Method</label>
                    <select id="preferred_contact_method" class="form-control" name="preferred_contact_method">
                        <?php foreach (['Email', 'Phone', 'Dashboard Message'] as $method): ?>
                            <option value="<?php echo pe($method); ?>" <?php echo (($user['preferred_contact_method'] ?? 'Email') === $method) ? 'selected' : ''; ?>><?php echo pe($method); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top:14px;">Save Account Info</button>
            </form>

            <form method="post">
                <input type="hidden" name="change_password" value="1">
                <input type="hidden" name="csrf_token" value="<?php echo pe(portalGetCsrfToken()); ?>">
                <h3 style="color:#ffd166;">Change Password</h3>
                <div class="row">
                    <div class="col-sm-4">
                        <label for="current_password" style="display:block;color:#a8bedc;margin-bottom:6px;">Current Password</label>
                        <input id="current_password" class="form-control" type="password" name="current_password" required autocomplete="current-password">
                    </div>
                    <div class="col-sm-4">
                        <label for="new_password" style="display:block;color:#a8bedc;margin-bottom:6px;">New Password</label>
                        <input id="new_password" class="form-control" type="password" name="new_password" required autocomplete="new-password">
                    </div>
                    <div class="col-sm-4">
                        <label for="confirm_password" style="display:block;color:#a8bedc;margin-bottom:6px;">Confirm New Password</label>
                        <input id="confirm_password" class="form-control" type="password" name="confirm_password" required autocomplete="new-password">
                    </div>
                </div>
                <button type="submit" class="btn btn-warning" style="margin-top:14px;">Update Password</button>
            </form>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="/assets/js/jquery-1.12.3.min.js"></script>
<script src="/assets/js/bootstrap.min.js"></script>
</body>
</html>
