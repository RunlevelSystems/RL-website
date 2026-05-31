<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';

// Redirect if already logged in
if (portalIsClientLoggedIn()) {
    $redirect = $_GET['redirect'] ?? '/client/dashboard.php';
    if (empty($redirect) || !preg_match('#^/#', $redirect) || preg_match('#^//|^/\\\\#', $redirect)) {
        $redirect = '/client/dashboard.php';
    }
    header('Location: ' . $redirect);
    exit;
}

$error = '';
$msg   = '';

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'logged_out') {
        $msg = 'You have been signed out.';
    } elseif ($_GET['msg'] === 'registered') {
        $msg = 'Account created! You can now sign in.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['client_login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter your username and password.';
    } else {
        $client = portalVerifyClientLogin($username, $password);
        if ($client) {
            $_SESSION[PORTAL_CLIENT_SESSION] = [
                'id'           => $client['id'],
                'username'     => $client['username'],
                'display_name' => $client['display_name'] ?? $client['username'],
                'email'        => $client['email'] ?? '',
                'login_time'   => time(),
            ];
            $redirect = $_GET['redirect'] ?? '/client/dashboard.php';
            if (empty($redirect) || !preg_match('#^/#', $redirect) || preg_match('#^//|^/\\\\#', $redirect)) {
                $redirect = '/client/dashboard.php';
            }
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = 'Incorrect username or password, or account is inactive.';
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
    <title>Client Portal | Runlevel Systems</title>
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
        .portal-alert-info { background: rgba(54,243,255,0.1); border: 1px solid rgba(54,243,255,0.3); color: #a8f0ff; border-radius: 6px; padding: 12px 14px; margin-bottom: 18px; }
        .portal-link-row { margin-top: 20px; text-align: center; font-size: 0.9rem; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>

<section class="portal-wrap">
    <div class="container">
        <div class="row" style="justify-content: center;">
            <div class="col-sm-5" style="flex: 0 0 420px; max-width: 420px;">
                <div class="portal-card">
                    <div style="text-align:center; margin-bottom: 24px;">
                        <div style="font-size: 2rem; margin-bottom: 8px;">🌐</div>
                        <h2>Client Portal</h2>
                        <p style="color:#7a9ac0; margin:0;">Sign in to manage your requests and proposals</p>
                    </div>

                    <?php if ($msg !== ''): ?>
                        <div class="portal-alert-info"><?php echo pe($msg); ?></div>
                    <?php endif; ?>
                    <?php if ($error !== ''): ?>
                        <div class="portal-alert-error"><?php echo pe($error); ?></div>
                    <?php endif; ?>

                    <form method="post" action="/client/login.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>">
                        <input type="hidden" name="client_login" value="1">
                        <label for="cl_username">Username</label>
                        <input id="cl_username" type="text" name="username" class="portal-input" required
                               value="<?php echo pe($_POST['username'] ?? ''); ?>"
                               autocomplete="username" placeholder="Your username">
                        <label for="cl_password">Password</label>
                        <input id="cl_password" type="password" name="password" class="portal-input" required
                               autocomplete="current-password" placeholder="Password">
                        <button type="submit" class="portal-btn">Sign In</button>
                    </form>

                    <div class="portal-link-row">
                        <a href="/client/register.php" style="color:#36f3ff;">Create a new account</a>
                    </div>
                    <div class="portal-link-row">
                        <a href="/contact.php" style="color:#5a7a9e; font-size:0.85rem;">Need help? Contact us</a>
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
