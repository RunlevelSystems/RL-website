<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';

if (portalIsClientLoggedIn()) {
    header('Location: /client/dashboard.php');
    exit;
}

$error   = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username    = trim($_POST['username'] ?? '');
    $password    = $_POST['password'] ?? '';
    $password2   = $_POST['password2'] ?? '';
    $email       = trim($_POST['email'] ?? '');
    $displayName = trim($_POST['display_name'] ?? '');

    if ($password !== $password2) {
        $error = 'Passwords do not match.';
    } else {
        $err = '';
        if (portalRegisterClient($username, $password, $email, $displayName, $err)) {
            header('Location: /client/login.php?msg=registered');
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
    <link href="../assets/css/runlevel.css" rel="stylesheet">
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

                    <form method="post">
                        <input type="hidden" name="register" value="1">

                        <label for="reg_username">Username</label>
                        <input id="reg_username" type="text" name="username" class="portal-input" required
                               value="<?php echo pe($_POST['username'] ?? ''); ?>"
                               autocomplete="username" placeholder="e.g. your-name">
                        <p class="hint">Letters, numbers, dot, underscore, dash. At least 3 characters.</p>

                        <label for="reg_display">Display Name (optional)</label>
                        <input id="reg_display" type="text" name="display_name" class="portal-input"
                               value="<?php echo pe($_POST['display_name'] ?? ''); ?>"
                               placeholder="Your full name or handle">

                        <label for="reg_email">Email Address</label>
                        <input id="reg_email" type="email" name="email" class="portal-input" required
                               value="<?php echo pe($_POST['email'] ?? ''); ?>"
                               autocomplete="email" placeholder="you@example.com">

                        <label for="reg_password">Password</label>
                        <input id="reg_password" type="password" name="password" class="portal-input" required
                               autocomplete="new-password" placeholder="At least 8 characters">

                        <label for="reg_password2">Confirm Password</label>
                        <input id="reg_password2" type="password" name="password2" class="portal-input" required
                               autocomplete="new-password" placeholder="Repeat your password">

                        <div class="tos-note">
                            By creating an account you agree to the
                            <a href="/runlevel-terms.php" target="_blank" rel="noopener noreferrer" style="color:#36f3ff;">Runlevel Systems Terms of Service</a>
                            and acknowledge that project requests are reviewed before a final quote is provided.
                        </div>

                        <button type="submit" class="portal-btn">Create Account</button>
                    </form>

                    <div class="portal-link-row">
                        Already have an account? <a href="/client/login.php" style="color:#36f3ff;">Sign in</a>
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
