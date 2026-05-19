<?php
// Start session and define system constant
session_start();
define('WDS_SYSTEM', true);

// Include database configuration
require_once 'includes/db-config.php';
addLoginDebug('Config file loaded', __FILE__);

// Check if already logged in
if (isLoggedInAdmin()) {
    $redirect = $_GET['redirect'] ?? '';
    if (empty($redirect) || !preg_match('#^/#', $redirect) || preg_match('#^//|^/\\\\#', $redirect)) {
        $redirect = 'staff-info.php';
    }
    header('Location: ' . $redirect);
    exit;
}

$error_message = '';
$login_attempted = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_form'])) {
    $login_attempted = true;
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error_message = 'Please enter both username and password.';
    } else {
        $loginFailureReason = null;
        $user = verifyAdminLogin($username, $password, $loginFailureReason);
        if ($user) {
            // Set session variables
            $_SESSION['wds_admin_user'] = $user['username'];
            $_SESSION['wds_admin_role'] = $user['role'];
            $_SESSION['wds_login_time'] = $user['login_time'];
            addLoginDebug('Session created', (isset($_SESSION['wds_admin_user']) && isset($_SESSION['wds_admin_role'])) ? 'YES' : 'NO');
            
            // Redirect to requested page or staff info.
            // Only allow relative paths (no scheme/host) to prevent open-redirect attacks.
            $redirect = $_GET['redirect'] ?? '';
            if (empty($redirect) || !preg_match('#^/#', $redirect) || preg_match('#^//|^/\\\\#', $redirect)) {
                $redirect = 'staff-info.php';
            }
            header('Location: ' . $redirect);
            exit;
        } else {
            addLoginDebug('Session created', 'NO');
            // User-facing text is intentionally generic for credential failures,
            // while authorization failures receive a separate message.
            if ($loginFailureReason === 'no_authorization') {
                $error_message = 'Your account is not authorized for staff access.';
            } else {
                $error_message = 'Incorrect username or password.';
            }
        }
    }
}

// Page-specific variables
$current_page = 'login';
$header_class = 'login-header inner-header';
$page_subtitle = 'Design • Debug • Deploy';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/assets/images/RL-icon.png">

    <title>Staff Login | Runlevel Systems</title>

        <!-- CSS -->
        <link href="assets/css/coreloop.css" rel="stylesheet">

        <style>
            .staff-login .title-box p {
                color: #94a3b8;
            }

            .login-container.staff-card {
                margin-bottom: 30px;
            }

            .staff-login-form label {
                color: #00d4ff;
            }

            .staff-login-form .form-control {
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
        <section class="staff-login">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="title-box">
                            <p>Admin Access</p>
                            <h2 class="title mt0" style="color: #00d4ff;">Staff Login</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 col-sm-offset-3">
                        <div class="staff-card login-container">
                            
                            <!-- Security Notice -->
                            <div class="info-box" style="margin-bottom: 25px;">
                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                    <i class="ion-locked" style="font-size: 22px; color: #00d4ff; margin-right: 10px;"></i>
                                    <h4 style="color: #00d4ff; margin: 0; font-size: 16px; text-transform: none;">Secure Staff Area</h4>
                                </div>
                                <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #94a3b8;">
                                    This area is restricted to authorized team members only. 
                                    Login credentials are verified against our secure admin database.
                                </p>
                            </div>

                            <!-- Error Message -->
                            <?php if ($error_message): ?>
                                <div style="background: rgba(248,113,113,0.2); color: #fca5a5; padding: 15px; border-radius: 8px; margin-bottom: 25px; border-left: 4px solid #ef4444;">
                                    <i class="ion-alert-circled" style="margin-right: 8px;"></i>
                                    <?php echo htmlspecialchars($error_message); ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Login Form -->
                            <form action="login.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>" method="post" class="staff-login-form">
                                <input type="hidden" name="login_form" value="1">
                                
                                <div style="margin-bottom: 25px;">
                                    <label style="display: block; margin-bottom: 8px; font-weight: bold; font-size: 16px;">
                                        <i class="ion-person" style="margin-right: 8px;"></i>Username
                                    </label>
                                    <input type="text" name="username" required 
                                           class="form-control"
                                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                                           placeholder="Enter your admin username">
                                </div>
                                
                                 <div style="margin-bottom: 30px;">
                                     <label style="display: block; margin-bottom: 8px; font-weight: bold; font-size: 16px;">
                                         <i class="ion-locked" style="margin-right: 8px;"></i>Password
                                     </label>
                                     <div class="password-field-wrapper">
                                         <input type="password" name="password" required
                                                class="form-control"
                                                id="password"
                                                placeholder="Enter your password">
                                         <button type="button" class="password-toggle-btn" id="passwordToggle" aria-label="Show password">
                                             <i class="ion-eye" aria-hidden="true"></i>
                                         </button>
                                     </div>
                                 </div>
                                
                                <div style="text-align: center;">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ion-log-in" style="margin-right: 10px;"></i>Login to Staff Area
                                    </button>
                                </div>
                            </form>

                            <!-- Access Information -->
                            <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid rgba(0,212,255,0.15);">
                                <h5 style="color: #00d4ff; margin-bottom: 15px; font-weight: bold; text-transform: none;">
                                    <i class="ion-information-circled" style="margin-right: 8px;"></i>Staff Access Information
                                </h5>
                                <ul style="color: #94a3b8; font-size: 14px; line-height: 1.6; margin: 0; padding-left: 20px;">
                                    <li>Access to learning platform credentials (Zenva, Mammoth Interactive, Udemy)</li>
                                    <li>Partner hosting resources and cPanel access</li>
                                    <li>Development tools and team resource sharing</li>
                                    <li>Private staff communication channels</li>
                                </ul>
                                <p style="color: #64748b; font-size: 12px; margin-top: 20px; font-style: italic;">
                                    Need access? Contact the team manager to get admin privileges added to your account.
                                </p>
                            </div>
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
                var toggleButton = document.getElementById('passwordToggle');
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
