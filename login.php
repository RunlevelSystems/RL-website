<?php
// Unified Dashboard Login
session_start();
define('WDS_SYSTEM', true);
require_once 'includes/portal-helpers.php';

// If already logged in via unified session, redirect to dashboard
if (portalIsLoggedIn()) {
    $redirect = portalSanitizeReturnPath($_GET['return'] ?? ($_GET['redirect'] ?? ''), '/dashboard.php');
    header('Location: ' . $redirect);
    exit;
}

$error_message = '';
$info_message = '';

if (isset($_GET['msg'])) {
    $msg = (string)$_GET['msg'];
    if ($msg === 'registered') {
        $info_message = 'Account created successfully. You can sign in now and verify your email from the dashboard.';
    } elseif ($msg === 'registered_no_email') {
        $info_message = 'Account created. Email verification is not currently available. Staff may verify your account manually.';
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_form'])) {
    $username = trim($_POST['username_or_email'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (!portalVerifyCsrfToken($csrfToken)) {
        $error_message = 'Your session expired. Please refresh and try again.';
    } elseif (empty($username) || empty($password)) {
        $error_message = 'Please enter both username/email and password.';
    } else {
        // Authenticate against /data/users.json (all roles: admin, staff, client)
        $failureReason = '';
        $user = portalVerifyLogin($username, $password, $failureReason);
        if ($user) {
            $role        = $user['role'] ?? 'staff';
            $displayName = $user['display_name'] ?? $user['username'];

            // Set unified dashboard session
            $_SESSION[PORTAL_UNIFIED_SESSION] = [
                'username'     => $user['username'],
                'role'         => $role,
                'display_name' => $displayName,
                'logged_in'    => true,
                'login_time'   => time(),
            ];

            // Also populate legacy portal staff session for backward compatibility
            // with existing staff pages (estimate-requests.php, users.php, etc.)
            if (in_array($role, ['admin', 'staff'], true)) {
                $_SESSION[PORTAL_STAFF_SESSION] = [
                    'username'     => $user['username'],
                    'role'         => $role,
                    'display_name' => $displayName,
                    'login_time'   => time(),
                ];
            }

            $redirect = portalSanitizeReturnPath($_GET['return'] ?? ($_GET['redirect'] ?? ''), '/dashboard.php');
            header('Location: ' . $redirect);
            exit;
        } else {
            if ($failureReason === 'disabled' || $failureReason === 'inactive') {
                $error_message = 'Your account is currently disabled. Please contact Runlevel Systems.';
            } else {
                $error_message = 'Incorrect username/email or password.';
            }
        }
    }
}

$current_page = 'dashboard';
$header_class = 'login-header inner-header';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/assets/images/RL-icon.png">

        <title>Dashboard Login | Runlevel Systems</title>

        <!-- CSS -->
        <link href="assets/css/coreloop.css" rel="stylesheet">

        <style>
            .dashboard-login .title-box p {
                color: #94a3b8;
            }

            .login-container.dashboard-card {
                margin-bottom: 30px;
            }

            .dashboard-login-form label {
                color: #00d4ff;
            }

            .dashboard-login-form .form-control {
                font-size: 16px;
            }

            .password-field-wrapper {
                position: relative;
            }

            .password-field-wrapper input[type="password"],
            .password-field-wrapper input[type="text"] {
                padding-right: 48px;
            }

            .password-toggle-btn {
                position: absolute;
                top: 50%;
                right: 10px;
                transform: translateY(-50%);
                border: 1px solid rgba(255, 209, 102, 0.45);
                background: rgba(13, 26, 51, 0.85);
                color: #ffd166;
                border-radius: 6px;
                width: 34px;
                height: 34px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
            }

            .password-toggle-btn:hover,
            .password-toggle-btn:focus {
                background: #ffd166;
                color: #0a1730;
                border-color: #ffd166;
                outline: none;
            }
        </style>

        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <!-- Include Site Header -->
        <?php include 'includes/header.php'; ?>

        <!-- Include Navigation Header -->
        <?php include 'includes/navigation.php'; ?>

        <!-- Login Section -->
        <section class="dashboard-login">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="title-box">
                            <p>Runlevel Systems</p>
                            <h2 class="title mt0" style="color: #00d4ff;">Dashboard Login</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 col-sm-offset-3">
                        <div class="dashboard-card login-container">

                            <!-- Error Message -->
                            <?php if ($error_message): ?>
                                <div style="background: rgba(248,113,113,0.2); color: #fca5a5; padding: 15px; border-radius: 8px; margin-bottom: 25px; border-left: 4px solid #ef4444;">
                                    <i class="ion-alert-circled" style="margin-right: 8px;"></i>
                                    <?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($info_message): ?>
                                <div style="background: rgba(16,185,129,0.15); color: #86efac; padding: 15px; border-radius: 8px; margin-bottom: 25px; border-left: 4px solid #10b981;">
                                    <i class="ion-checkmark-circled" style="margin-right: 8px;"></i>
                                    <?php echo htmlspecialchars($info_message, ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            <?php endif; ?>

                            <!-- Login Form -->
                            <form action="login.php<?php echo isset($_GET['return']) ? '?return=' . urlencode($_GET['return']) : (isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''); ?>" method="post" class="dashboard-login-form">
                                <input type="hidden" name="login_form" value="1">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(portalGetCsrfToken(), ENT_QUOTES, 'UTF-8'); ?>">

                                <p style="color:#94a3b8;margin:0 0 20px 0;">New here? Create an account to submit a project request and track updates.</p>

                                <div style="margin-bottom: 25px;">
                                    <label style="display: block; margin-bottom: 8px; font-weight: bold; font-size: 16px;">
                                        <i class="ion-person" style="margin-right: 8px;"></i>Username or Email
                                    </label>
                                    <input type="text" name="username_or_email" required
                                           class="form-control"
                                           value="<?php echo isset($_POST['username_or_email']) ? htmlspecialchars($_POST['username_or_email'], ENT_QUOTES, 'UTF-8') : ''; ?>"
                                           placeholder="Enter your username or email"
                                           autocomplete="username">
                                </div>

                                <div style="margin-bottom: 30px;">
                                    <label style="display: block; margin-bottom: 8px; font-weight: bold; font-size: 16px;">
                                        <i class="ion-locked" style="margin-right: 8px;"></i>Password
                                    </label>
                                    <div class="password-field-wrapper">
                                        <input type="password" name="password" required
                                               class="form-control"
                                               id="password"
                                               placeholder="Enter your password"
                                               autocomplete="current-password">
                                        <button type="button" class="password-toggle-btn" id="passwordToggle" aria-label="Show password">
                                            <i class="ion-eye" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>

                                <div style="text-align: center;">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ion-log-in" style="margin-right: 10px;"></i>Sign In
                                    </button>
                                </div>
                                <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-top:18px;">
                                    <a href="/client/register.php" style="color:#36f3ff;">Create Client Account</a>
                                    <a href="#" style="color:#ffd166;">Forgot Password</a>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Include Footer -->
        <?php include 'includes/footer.php'; ?>

        <!-- Scripts -->
        <script src="assets/js/jquery-1.12.3.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/jquery.magnific-popup.min.js"></script>
        <script src="assets/js/owl.carousel.min.js"></script>
        <script src="assets/js/script.js"></script>
        <script>
            (function () {
                var passwordInput = document.getElementById('password');
                var toggleButton  = document.getElementById('passwordToggle');
                if (!passwordInput || !toggleButton) {
                    return;
                }

                toggleButton.addEventListener('click', function () {
                    var isHidden = passwordInput.type === 'password';
                    passwordInput.type = isHidden ? 'text' : 'password';
                    toggleButton.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
                    toggleButton.innerHTML = isHidden
                        ? '<i class="ion-eye-disabled" aria-hidden="true"></i>'
                        : '<i class="ion-eye" aria-hidden="true"></i>';
                });
            })();
        </script>

    </body>
</html>
