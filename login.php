<?php
session_start();
define('WDS_SYSTEM', true);
require_once 'includes/db-config.php';

if (isLoggedInAdmin()) {
    $redirect = $_GET['redirect'] ?? 'staff-info.php';
    header('Location: ' . $redirect);
    exit;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_form'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error_message = 'Please enter both username and password.';
    } else {
        $user = verifyAdminLogin($username, $password);
        if ($user) {
            $_SESSION['wds_admin_user'] = $user['username'];
            $_SESSION['wds_admin_role'] = $user['role'];
            $_SESSION['wds_login_time'] = $user['login_time'];

            $redirect = $_GET['redirect'] ?? 'staff-info.php';
            header('Location: ' . $redirect);
            exit;
        }
        $error_message = 'Invalid username or password, or insufficient privileges.';
    }
}

$current_page = 'login';
$header_class = 'login-header inner-header';
$page_subtitle = 'Engineer • Ship • Scale';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/assets/images/icon.png">

        <title>Staff Login | Core Loop Development</title>

        <link href="assets/css/coreloop.css" rel="stylesheet">
    </head>
    <body>
        <?php include 'includes/header.php'; ?>
        <?php include 'includes/navigation.php'; ?>

        <section class="staff-login">
            <div class="container page-bgc">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="title-box">
                            <p>Admin Access</p>
                            <h2 class="title mt0">Staff Login</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 col-sm-offset-3">
                        <div class="login-container">
                            <div class="info-box">
                                <h4><i class="ion-locked"></i> Secure Staff Area</h4>
                                <p>This area is restricted to authorized team members only. Login credentials are verified against our secure admin database.</p>
                            </div>

                            <?php if ($error_message): ?>
                                <div class="info-box">
                                    <i class="ion-alert-circled"></i>
                                    <?php echo htmlspecialchars($error_message); ?>
                                </div>
                            <?php endif; ?>

                            <form action="login.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>" method="post" class="staff-login-form">
                                <input type="hidden" name="login_form" value="1">

                                <div>
                                    <label for="username"><i class="ion-person"></i> Username</label>
                                    <input id="username" type="text" name="username" required class="form-control" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" placeholder="Enter your admin username">
                                </div>

                                <div>
                                    <label for="password"><i class="ion-locked"></i> Password</label>
                                    <input id="password" type="password" name="password" required class="form-control" placeholder="Enter your password">
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn">
                                        <i class="ion-log-in"></i> Login to Staff Area
                                    </button>
                                </div>
                            </form>

                            <div class="info-box">
                                <h5><i class="ion-information-circled"></i> Staff Access Information</h5>
                                <ul>
                                    <li>Access to learning platform credentials (Zenva, Mammoth Interactive, Udemy)</li>
                                    <li>Partner hosting resources and cPanel access</li>
                                    <li>Development tools and team resource sharing</li>
                                    <li>Private staff communication channels</li>
                                </ul>
                                <p>Need access? Contact the team manager to get admin privileges added to your account.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <?php include 'includes/footer.php'; ?>

        <script src="assets/js/jquery-1.12.3.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/jquery.magnific-popup.min.js"></script>
        <script src="assets/js/owl.carousel.min.js"></script>
        <script src="assets/js/script.js"></script>
    </body>
</html>
