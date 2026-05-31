<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';

// Redirect if already logged in as portal staff
if (portalIsStaffLoggedIn()) {
    $redirect = $_GET['redirect'] ?? '/staff/dashboard.php';
    if (empty($redirect) || !preg_match('#^/#', $redirect) || preg_match('#^//|^/\\\\#', $redirect)) {
        $redirect = '/staff/dashboard.php';
    }
    header('Location: ' . $redirect);
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['portal_login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        $user = portalVerifyStaffLogin($username, $password);
        if ($user) {
            $_SESSION[PORTAL_STAFF_SESSION] = [
                'username'     => $user['username'],
                'role'         => $user['role'],
                'display_name' => $user['display_name'] ?? $user['username'],
                'login_time'   => time(),
            ];
            $redirect = $_GET['redirect'] ?? '/staff/dashboard.php';
            if (empty($redirect) || !preg_match('#^/#', $redirect) || preg_match('#^//|^/\\\\#', $redirect)) {
                $redirect = '/staff/dashboard.php';
            }
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = 'Incorrect username or password.';
        }
    }
}

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
    <title>Staff Portal Login | Runlevel Systems</title>
    <link href="../assets/css/coreloop.css" rel="stylesheet">
    <style>
        .portal-login-wrap { padding: 60px 0 80px; }
        .portal-card { background: #0c1729; border: 1px solid rgba(54,243,255,0.2); border-radius: 12px; padding: 32px; }
        .portal-card h2 { color: #ffc600; margin-top: 0; }
        .portal-card label { color: #a8bedc; font-size: 0.95rem; margin-bottom: 4px; display: block; }
        .portal-input { background: #09111d; color: #eaf3ff; border: 1px solid rgba(54,243,255,0.25); border-radius: 6px; padding: 10px 12px; margin-bottom: 18px; font-size: 1rem; width: 100%; }
        .portal-input:focus { outline: 2px solid #0a84ff; border-color: #0a84ff; }
        .portal-btn { background: #ffc600; color: #08111f; border: none; border-radius: 6px; padding: 11px 24px; font-weight: 700; font-size: 1rem; cursor: pointer; width: 100%; }
        .portal-btn:hover { background: #36f3ff; }
        .portal-alert-error { background: rgba(239,68,68,0.18); border: 1px solid #ef4444; color: #fecaca; border-radius: 6px; padding: 12px 14px; margin-bottom: 18px; }
        .portal-muted { color: #5a7a9e; font-size: 0.88rem; margin-top: 18px; text-align: center; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>

<section class="portal-login-wrap">
    <div class="container">
        <div class="row" style="justify-content: center;">
            <div class="col-sm-5" style="flex: 0 0 420px; max-width: 420px;">
                <div class="portal-card">
                    <div style="text-align:center; margin-bottom: 24px;">
                        <div style="font-size: 2rem; margin-bottom: 8px;">🔐</div>
                        <h2>Staff Portal</h2>
                        <p style="color:#7a9ac0; margin:0;">Runlevel Systems internal access</p>
                    </div>

                    <?php if ($error !== ''): ?>
                        <div class="portal-alert-error"><?php echo pe($error); ?></div>
                    <?php endif; ?>

                    <form method="post" action="/staff/login.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>">
                        <input type="hidden" name="portal_login" value="1">
                        <label for="staff_username">Username</label>
                        <input id="staff_username" type="text" name="username" class="portal-input" required
                               value="<?php echo pe($_POST['username'] ?? ''); ?>"
                               autocomplete="username" placeholder="Staff username">
                        <label for="staff_password">Password</label>
                        <input id="staff_password" type="password" name="password" class="portal-input" required
                               autocomplete="current-password" placeholder="Password">
                        <button type="submit" class="portal-btn">Sign In to Staff Portal</button>
                    </form>

                    <p class="portal-muted">
                        Staff-only area. If you need access, contact the site administrator.
                    </p>
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
