<?php
define('WDS_SYSTEM', true);
require_once __DIR__ . '/includes/portal-helpers.php';

$token = trim($_GET['token'] ?? '');
$verified = false;
$error = '';

if ($token === '') {
    $error = 'Verification link is invalid or expired. Please request a new verification email.';
} else {
    $verified = portalActivateUserByVerificationToken($token);
    if (!$verified) {
        $error = 'Verification link is invalid or expired. Please request a new verification email.';
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
    <title>Email Verification | Runlevel Systems</title>
    <link href="assets/css/runlevel.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>
<?php include __DIR__ . '/includes/navigation.php'; ?>

<section style="padding:40px 0 80px;">
    <div class="container">
        <div style="background:#0c1729;border:1px solid rgba(54,243,255,0.2);border-radius:10px;padding:28px;max-width:760px;margin:0 auto;">
            <?php if ($verified): ?>
                <h1 style="margin-top:0;color:#22c55e;">Email Verified</h1>
                <p style="color:#a8bedc;">Your email has been verified. You can now log in to your dashboard.</p>
                <p><a href="/login.php" style="color:#36f3ff;">Go to Login</a></p>
            <?php else: ?>
                <h1 style="margin-top:0;color:#ffc600;">Verification Needed</h1>
                <p style="color:#a8bedc;"><?php echo pe($error); ?></p>
                <p><a href="/resend-verification.php" style="color:#36f3ff;">Request new verification email</a></p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
<script src="assets/js/jquery-1.12.3.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
