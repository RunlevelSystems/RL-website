<?php
define('WDS_SYSTEM', true);
require_once __DIR__ . '/includes/portal-helpers.php';
require_once __DIR__ . '/includes/email.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend_verification'])) {
    $email = trim($_POST['email'] ?? '');

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $user = null;
        $refreshed = portalRefreshVerificationTokenByEmail($email, $user);
        if (!$refreshed || !$user) {
            $error = 'Verification link is invalid or expired. Please request a new verification email.';
        } else {
            $verifyLink = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'runlevel.systems') . '/verify-email.php?token=' . urlencode((string)$user['verification_token']);
            $name = trim((string)($user['display_name'] ?? $user['username'] ?? 'Customer'));
            $body = "Hello {$name},\n\n"
                  . "We received your request to resend account verification.\n\n"
                  . "Please verify your email before logging in:\n\n"
                  . $verifyLink . "\n\n"
                  . "Runlevel Systems\n"
                  . "DESIGN • DEBUG • DEPLOY\n";
            send_email($email, 'Verify your Runlevel Systems account', $body);
            $message = 'Verification email sent. Please check your inbox.';
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
    <title>Resend Verification | Runlevel Systems</title>
    <link href="assets/css/coreloop.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>
<?php include __DIR__ . '/includes/navigation.php'; ?>

<section style="padding:40px 0 80px;">
    <div class="container">
        <div style="background:#0c1729;border:1px solid rgba(54,243,255,0.2);border-radius:10px;padding:28px;max-width:760px;margin:0 auto;">
            <h1 style="margin-top:0;color:#ffc600;">Resend Verification Email</h1>
            <p style="color:#a8bedc;">Enter your account email to receive a new verification link.</p>

            <?php if ($message !== ''): ?>
                <div style="background:rgba(34,197,94,0.15);border:1px solid rgba(34,197,94,0.4);padding:10px 12px;border-radius:6px;color:#86efac;margin-bottom:14px;"><?php echo pe($message); ?></div>
            <?php endif; ?>
            <?php if ($error !== ''): ?>
                <div style="background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.4);padding:10px 12px;border-radius:6px;color:#fca5a5;margin-bottom:14px;"><?php echo pe($error); ?></div>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="resend_verification" value="1">
                <label for="rv-email" style="display:block;color:#a8bedc;margin-bottom:6px;">Email</label>
                <input id="rv-email" type="email" name="email" required value="<?php echo pe($_POST['email'] ?? ''); ?>" style="width:100%;background:#09111d;color:#eaf3ff;border:1px solid rgba(54,243,255,0.25);border-radius:6px;padding:10px 12px;">
                <button type="submit" style="margin-top:12px;background:#ffc600;color:#08111f;border:none;border-radius:6px;padding:10px 18px;font-weight:700;">Send Verification Email</button>
            </form>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
<script src="assets/js/jquery-1.12.3.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
