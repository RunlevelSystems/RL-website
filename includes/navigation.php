<?php
if (!isset($_SESSION)) {
    session_start();
}

if (!function_exists('isLoggedInAdmin')) {
    if (!defined('WDS_SYSTEM')) {
        define('WDS_SYSTEM', true);
        require_once __DIR__ . '/db-config.php';
    }
}

$is_logged_in = function_exists('isLoggedInAdmin') ? isLoggedInAdmin() : false;
$current_url = $_SERVER['REQUEST_URI'];
$is_in_projects = (strpos($current_url, '/projects/') !== false);
$is_in_staff = (strpos($current_url, '/staff/') !== false);
$is_in_wiki = (strpos($current_url, '/staff/wiki/') !== false);

if ($is_in_wiki) {
    $base_path = '../../';
} elseif ($is_in_staff || $is_in_projects) {
    $base_path = '../';
} else {
    $base_path = '';
}
?>
<section id="header" class="main-header <?php echo isset($header_class) ? $header_class : ''; ?>">
    <div class="container-fluid">
        <div class="row">
            <nav class="navbar navbar-default" aria-label="Main navigation">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#site-nav-bar" aria-expanded="false" aria-label="Toggle menu">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>

                <div class="collapse navbar-collapse" id="site-nav-bar">
                    <ul class="nav navbar-nav">
                        <li <?php echo ($current_page == 'index') ? 'class="active"' : ''; ?>><a href="<?php echo $base_path; ?>index.php">Home</a></li>
                        <li <?php echo ($current_page == 'projects') ? 'class="active"' : ''; ?>><a href="<?php echo $base_path; ?>projects.php">Projects</a></li>
                        <li <?php echo ($current_page == 'joinus') ? 'class="active"' : ''; ?>><a href="<?php echo $base_path; ?>joinus.php">Join Us</a></li>
                        <li <?php echo ($current_page == 'contact') ? 'class="active"' : ''; ?>><a href="<?php echo $base_path; ?>contact.php">Contact</a></li>
                    </ul>

                    <ul class="nav navbar-nav navbar-right">
                        <?php if ($is_logged_in): ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    <i class="ion-person"></i> <?php echo isset($_SESSION['wds_admin_user']) ? htmlspecialchars($_SESSION['wds_admin_user']) : 'Staff'; ?> <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo $base_path; ?>staff-info.php"><i class="ion-information-circled"></i> Staff Home</a></li>
                                    <li><a href="<?php echo $base_path; ?>staff/operations.php"><i class="ion-android-desktop"></i> Operations</a></li>
                                    <li><a href="<?php echo $base_path; ?>staff/tools.php"><i class="ion-wrench"></i> Toolbox</a></li>
                                    <li><a href="<?php echo $base_path; ?>staff/server-status.php"><i class="ion-ios-pulse"></i> Server Status</a></li>
                                    <li><a href="<?php echo $base_path; ?>staff/wiki/index.php"><i class="ion-document"></i> Wiki</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="<?php echo $base_path; ?>logout.php"><i class="ion-log-out"></i> Logout</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li><a href="<?php echo $base_path; ?>login.php"><i class="ion-log-in"></i> Staff Login</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>
        </div>

        <div class="intro row intro-fixed-height">
            <div class="col-sm-7">
                <a href="<?php echo $base_path; ?>index.php" class="lx-logo" aria-label="Core Loop Development home">
                    <img src="<?php echo $base_path; ?>assets/images/logo.png" alt="Core Loop Development" class="lx-logo-banner">
                </a>
            </div>
            <div class="col-sm-5 header-right-column">
                <h2 class="header-quote"><?php echo isset($page_subtitle) ? htmlspecialchars($page_subtitle) : 'Engineer • Ship • Scale'; ?></h2>
                <?php if (isset($page_description) && $page_description): ?>
                    <p><?php echo htmlspecialchars($page_description); ?></p>
                <?php endif; ?>
                <?php if (isset($page_title) && $page_title): ?>
                    <h1 class="header-title">
                        <?php echo htmlspecialchars($page_title); ?>
                        <?php if (isset($page_title_thin) && $page_title_thin): ?>
                            <span class="thin"><?php echo htmlspecialchars($page_title_thin); ?></span>
                        <?php endif; ?>
                    </h1>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
