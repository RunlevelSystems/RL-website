<?php 
// Initialize session and check login status if not already done
if (!isset($_SESSION)) {
    session_start();
}

// Check if user is logged in (only include db-config if not already included)
if (!function_exists('isLoggedInAdmin')) {
    if (defined('WDS_SYSTEM')) {
        // If WDS_SYSTEM is already defined, we can assume db-config is included
        // Otherwise we need to include it for navigation
    } else {
        define('WDS_SYSTEM', true);
        require_once __DIR__ . '/db-config.php';
    }
}

$is_logged_in = function_exists('isLoggedInAdmin') ? isLoggedInAdmin() : false;
?>

<!-- Navigation Header -->
<section id="header" class="main-header <?php echo isset($header_class) ? $header_class : ''; ?>">
    <div class="container">
        <div class="row" style="background-color: #1C1C1C;">
            <nav class="navbar navbar-default">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#site-nav-bar" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>

                <div class="collapse navbar-collapse" id="site-nav-bar" style="background-color: #080808;">
                    <ul class="nav navbar-nav">
                        <li <?php echo ($current_page == 'index') ? 'class="active"' : ''; ?>><a href="index.php">Home</a></li>
                        <li <?php echo ($current_page == 'projects') ? 'class="active"' : ''; ?>><a href="projects.php">Projects</a></li>
                        <li <?php echo ($current_page == 'joinus') ? 'class="active"' : ''; ?>><a href="joinus.php">Join Us</a></li>
                        <li <?php echo ($current_page == 'contact') ? 'class="active"' : ''; ?>><a href="contact.php">Contact</a></li>
                        
                        <?php if ($is_logged_in): ?>
                            <li <?php echo ($current_page == 'staff-info') ? 'class="active"' : ''; ?>><a href="staff-info.php" style="color: #8B4513;"><i class="ion-locked"></i> Staff Info</a></li>
                        <?php endif; ?>
                    </ul>
                    
                    <!-- Login/Logout Section -->
                    <ul class="nav navbar-nav navbar-right">
                        <?php if ($is_logged_in): ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" style="color: #8B4513;">
                                    <i class="ion-person"></i> <?php echo isset($_SESSION['wds_admin_user']) ? htmlspecialchars($_SESSION['wds_admin_user']) : 'Staff'; ?> <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu" style="background: #1a1a1a; border: 1px solid #8B4513;">
                                    <li><a href="staff-info.php" style="color: #8B7355;"><i class="ion-information-circled"></i> Staff Resources</a></li>
                                    <li role="separator" class="divider" style="background: #333;"></li>
                                    <li><a href="logout.php" style="color: #f87171;"><i class="ion-log-out"></i> Logout</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li><a href="login.php" style="color: #8B4513;"><i class="ion-log-in"></i> Staff Login</a></li>
                        <?php endif; ?>
                    </ul>
                </div><!-- /.navbar-collapse -->
            </nav>
        </div>
        
        <div class="intro row intro-fixed-height" style="height: 300px; min-height: 300px; background-color: #1C1C1C;">
            <div class="overlay"></div>
            <div class="col-sm-3" style="display: flex; align-items: center; height: 100%;">
                <a href="index.php" style="height: 100%; width: 100%; display: flex; align-items: center; justify-content: center;">
                    <img src="assets/images/wds-logo.png" alt="WDS Logo" class="header-logo" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                </a>
            </div>
            <?php if(isset($show_breadcrumb) && $show_breadcrumb): ?>
                <div class="col-sm-9">
                    <ol class="breadcrumb">
                        <li><a href="index.php">Home</a></li>
                        <li class="active"><?php echo $page_breadcrumb; ?></li>
                    </ol>
                </div>
            <?php else: ?>
                <div class="col-sm-6 col-sm-offset-3">
                    <h2 class="header-quote"><?php echo $page_subtitle; ?></h2>
                    <p>
                        <?php echo $page_description; ?>
                    </p>
                    <h1 class="header-title"><?php echo $page_title; ?><br><span class="thin"><?php echo $page_title_thin; ?></span></h1>
                </div>
            <?php endif; ?>
        </div> <!-- /.intro.row -->
    </div> <!-- /.container -->
    <div class="nutral"></div>
</section> <!-- /#header -->
