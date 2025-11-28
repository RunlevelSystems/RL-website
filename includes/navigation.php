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
    <div class="container-fluid">
        <div class="row" style="background-color: #000000; position: relative; z-index: 5;">
            <nav class="navbar navbar-default">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#site-nav-bar" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>

                <div class="collapse navbar-collapse" id="site-nav-bar" style="background-color: #000000;">
                    <ul class="nav navbar-nav">
                        <?php 
                        // Universal path detection for both XAMPP and web hosting
                        // Get the current page's directory level
                        $current_url = $_SERVER['REQUEST_URI'];
                        $is_in_projects = (strpos($current_url, '/projects/') !== false);
                        $is_in_staff = (strpos($current_url, '/staff/') !== false);
                        $is_in_wiki = (strpos($current_url, '/staff/wiki/') !== false);
                        
                        if ($is_in_wiki) {
                            // We're in wiki subdirectory, need to go up two directory levels
                            $base_path = '../../';
                        } elseif ($is_in_staff) {
                            // We're in staff subdirectory, need to go up one directory level
                            $base_path = '../';
                        } elseif ($is_in_projects) {
                            // We're in a project page, need to go up one directory level
                            $base_path = '../';
                        } else {
                            // We're at root level, use relative paths
                            // This works for both XAMPP and most web hosts
                            $base_path = '';
                        }
                        
                        // Check if we're on any staff page
                        $is_staff_page = in_array($current_page, ['staff-info', 'staff-ops', 'staff-tools', 'staff-wiki']);
                        ?>
                        <li <?php echo ($current_page == 'index') ? 'class="active"' : ''; ?>><a href="<?php echo $base_path; ?>index.php">Home</a></li>
                        <li <?php echo ($current_page == 'projects') ? 'class="active"' : ''; ?>><a href="<?php echo $base_path; ?>projects.php">Projects</a></li>
                        <li <?php echo ($current_page == 'joinus') ? 'class="active"' : ''; ?>><a href="<?php echo $base_path; ?>joinus.php">Join Us</a></li>
                        <li <?php echo ($current_page == 'contact') ? 'class="active"' : ''; ?>><a href="<?php echo $base_path; ?>contact.php">Contact</a></li>
                        
                        <?php if ($is_logged_in): ?>
                            <li class="dropdown <?php echo $is_staff_page ? 'active' : ''; ?>">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" style="color: #8B4513;">
                                    <i class="ion-locked"></i> Staff Area <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu" style="background: #1a1a1a; border: 1px solid #8B4513;">
                                    <li><a href="<?php echo $base_path; ?>staff-info.php" style="color: #8B7355;"><i class="ion-home"></i> Staff Home</a></li>
                                    <li><a href="<?php echo $base_path; ?>staff/operations.php" style="color: #8B7355;"><i class="ion-android-desktop"></i> Operations</a></li>
                                    <li><a href="<?php echo $base_path; ?>staff/tools.php" style="color: #8B7355;"><i class="ion-wrench"></i> Toolbox</a></li>
                                    <li><a href="<?php echo $base_path; ?>staff/wiki/index.php" style="color: #8B7355;"><i class="ion-document"></i> Wiki</a></li>
                                </ul>
                            </li>
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
                                    <li><a href="<?php echo $base_path; ?>staff-info.php" style="color: #8B7355;"><i class="ion-information-circled"></i> Staff Home</a></li>
                                    <li><a href="<?php echo $base_path; ?>staff/operations.php" style="color: #8B7355;"><i class="ion-android-desktop"></i> Operations</a></li>
                                    <li><a href="<?php echo $base_path; ?>staff/tools.php" style="color: #8B7355;"><i class="ion-wrench"></i> Toolbox</a></li>
                                    <li><a href="<?php echo $base_path; ?>staff/wiki/index.php" style="color: #8B7355;"><i class="ion-document"></i> Wiki</a></li>
                                    <li role="separator" class="divider" style="background: #333;"></li>
                                    <li><a href="<?php echo $base_path; ?>logout.php" style="color: #f87171;"><i class="ion-log-out"></i> Logout</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li><a href="<?php echo $base_path; ?>login.php" style="color: #8B4513;"><i class="ion-log-in"></i> Staff Login</a></li>
                        <?php endif; ?>
                    </ul>
                </div><!-- /.navbar-collapse -->
            </nav>
        </div>
        
        <div class="intro row intro-fixed-height" style="height: 400px; min-height: 400px; background: #000000; position: relative; z-index: 2;">
            <div class="overlay"></div>
            <div class="col-sm-6" style="display: flex; align-items: center; height: 100%; position: relative; z-index: 3;">
                <a href="<?php echo $base_path; ?>index.php" style="height: 100%; width: 100%; display: flex; align-items: center; justify-content: center;">
                    <img src="<?php echo $base_path; ?>assets/images/wds-logo.png" alt="WDS Logo" class="header-logo" style="max-width: 90%; max-height: 90%; object-fit: contain;">
                </a>
            </div>
            <div class="col-sm-6">
                <div style="padding: 15px 0; text-align: center;">
                    <h2 class="header-quote" style="color: #F5F5F5; margin-bottom: 5px;">
                        <?php echo isset($page_subtitle) ? $page_subtitle : 'Design. Debug. Deploy.'; ?>
                    </h2>
                    <?php if(isset($page_description) && $page_description): ?>
                        <p style="color: #CCCCCC; margin-bottom: 10px;">
                            <?php echo $page_description; ?>
                        </p>
                    <?php endif; ?>
                    <?php if(isset($page_title) && $page_title): ?>
                        <h1 class="header-title" style="color: #8B4513;">
                            <?php echo $page_title; ?>
                            <?php if(isset($page_title_thin) && $page_title_thin): ?>
                                <br><span class="thin"><?php echo $page_title_thin; ?></span>
                            <?php endif; ?>
                        </h1>
                    <?php endif; ?>
                </div>
            </div>
        </div> <!-- /.intro.row -->
    </div> <!-- /.container -->

</section> <!-- /#header -->
