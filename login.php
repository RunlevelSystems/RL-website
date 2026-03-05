// Developed by World Domination Software LLC
<?php
// Start session and define system constant
session_start();
define('WDS_SYSTEM', true);

// Include database configuration
require_once 'includes/db-config.php';

// Check if already logged in
if (isLoggedInAdmin()) {
    $redirect = $_GET['redirect'] ?? 'staff-info.php';
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
        $user = verifyAdminLogin($username, $password);
        if ($user) {
            // Set session variables
            $_SESSION['wds_admin_user'] = $user['username'];
            $_SESSION['wds_admin_role'] = $user['role'];
            $_SESSION['wds_login_time'] = $user['login_time'];
            
            // Redirect to requested page or staff info
            $redirect = $_GET['redirect'] ?? 'staff-info.php';
            header('Location: ' . $redirect);
            exit;
        } else {
            $error_message = 'Invalid username or password, or insufficient privileges.';
        }
    }
}

// Page-specific variables
$current_page = 'login';
$header_class = 'login-header inner-header';
$page_subtitle = 'Design. Debug. Deploy.';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Staff Login | WDS</title>

        <!-- CSS -->
        <!-- google fonts -->
        <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,700' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Oswald:400,300,700' rel='stylesheet' type='text/css'>

        <!-- files -->
        <link href="assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="assets/css/magnific-popup.css" rel="stylesheet">
        <link href="assets/css/owl.carousel.css" rel="stylesheet">
        <link href="assets/css/owl.carousel.theme.min.css" rel="stylesheet">
        <link href="assets/css/ionicons.css" rel="stylesheet">
        <link href="assets/css/main.css" rel="stylesheet">
        <link href="assets/css/wds-unified.css" rel="stylesheet">

        <style>
            /* Keep login page aligned with the Orwellian tan theme */
            .staff-login .title-box p {
                color: #4a4a4a;
            }

            .login-container.staff-card {
                margin-bottom: 30px;
            }

            .staff-login-form label {
                color: #8B4513;
            }

            .staff-login-form .form-control {
                font-size: 16px;
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
                            <h2 class="title mt0" style="color: #8B4513;">Staff Login</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 col-sm-offset-3">
                        <div class="staff-card login-container">
                            
                            <!-- Security Notice -->
                            <div class="info-box" style="margin-bottom: 25px;">
                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                    <i class="ion-locked" style="font-size: 22px; color: #8B4513; margin-right: 10px;"></i>
                                    <h4 style="color: #8B4513; margin: 0; font-size: 16px; text-transform: none;">Secure Staff Area</h4>
                                </div>
                                <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #4a4a4a;">
                                    This area is restricted to authorized co-op staff members only. 
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
                            
                            <!-- DEBUG OUTPUT -->
                            <!-- ⚠️ WARNING: TEMPORARY DEBUG OUTPUT - REMOVE BEFORE PRODUCTION! -->
                            <!-- This exposes sensitive login information for troubleshooting purposes only -->
                            <?php if ($login_attempted && function_exists('getLoginDebug')): ?>
                                <div style="background: #1a1a2e; color: #00ff00; padding: 20px; border-radius: 8px; margin-bottom: 25px; border-left: 4px solid #00ff00; font-family: 'Courier New', monospace; font-size: 12px; max-height: 500px; overflow-y: auto;">
                                    <h4 style="color: #00ff00; margin-top: 0; margin-bottom: 15px; border-bottom: 1px solid #00ff00; padding-bottom: 10px;">🔍 LOGIN DEBUG OUTPUT</h4>
                                    <?php 
                                    $debugInfo = getLoginDebug();
                                    if (empty($debugInfo)): 
                                    ?>
                                        <p style="color: #ffaa00;">No debug information captured. This might indicate a very early failure.</p>
                                    <?php else: ?>
                                        <?php foreach ($debugInfo as $index => $entry): ?>
                                            <div style="margin-bottom: 8px; padding: 5px; background: rgba(0,255,0,0.1); border-radius: 4px;">
                                                <span style="color: #888;">[<?php echo $index + 1; ?>]</span>
                                                <span style="color: #00ff00; font-weight: bold;"><?php echo htmlspecialchars($entry['message']); ?></span>
                                                <?php if (isset($entry['data'])): ?>
                                                    <span style="color: #ffff00;"> → </span>
                                                    <span style="color: #00ffff;"><?php 
                                                        if (is_array($entry['data'])) {
                                                            echo htmlspecialchars(json_encode($entry['data'], JSON_PRETTY_PRINT));
                                                        } else {
                                                            echo htmlspecialchars((string)$entry['data']);
                                                        }
                                                    ?></span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
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
                                    <input type="password" name="password" required 
                                           class="form-control"
                                           placeholder="Enter your password">
                                </div>
                                
                                <div style="text-align: center;">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ion-log-in" style="margin-right: 10px;"></i>Login to Staff Area
                                    </button>
                                </div>
                            </form>

                            <!-- Access Information -->
                            <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #C6B8A0;">
                                <h5 style="color: #8B4513; margin-bottom: 15px; font-weight: bold; text-transform: none;">
                                    <i class="ion-information-circled" style="margin-right: 8px;"></i>Staff Access Information
                                </h5>
                                <ul style="color: #1a1a1a; font-size: 14px; line-height: 1.6; margin: 0; padding-left: 20px;">
                                    <li>Access to learning platform credentials (Zenva, Mammoth Interactive, Udemy)</li>
                                    <li>Partner hosting resources and cPanel access</li>
                                    <li>Development tools and co-op resource sharing</li>
                                    <li>Private staff communication channels</li>
                                </ul>
                                <p style="color: #666666; font-size: 12px; margin-top: 20px; font-style: italic;">
                                    Need access? Contact the co-op manager to get admin privileges added to your account.
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

    </body>
</html>
