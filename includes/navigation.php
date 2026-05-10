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
        <nav class="navbar core-navbar" role="navigation" aria-label="Primary navigation">
            <div class="core-nav-inner">
                <a href="<?php echo $base_path; ?>index.php" class="core-brand" aria-label="Core Loop home">
                    <img src="<?php echo $base_path; ?>assets/images/icon.png" alt="Core Loop" class="core-brand-icon">
                    <span class="core-brand-text">Core Loop</span>
                </a>

                <button type="button" class="navbar-toggle collapsed core-nav-toggle" data-toggle="collapse" data-target="#site-nav-bar" aria-expanded="false" aria-label="Toggle navigation menu">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <div class="collapse navbar-collapse core-nav-collapse" id="site-nav-bar">
                    <ul class="core-nav-links">
                        <li><a href="<?php echo $base_path; ?>index.php" <?php echo ($current_page == 'index') ? 'aria-current="page"' : ''; ?>>Home</a></li>
                        <li><a href="<?php echo $base_path; ?>projects.php" <?php echo ($current_page == 'projects') ? 'aria-current="page"' : ''; ?>>Projects</a></li>
                        <li><a href="<?php echo $base_path; ?>joinus.php" <?php echo ($current_page == 'joinus') ? 'aria-current="page"' : ''; ?>>Join Us</a></li>
                        <li><a href="<?php echo $base_path; ?>contact.php" <?php echo ($current_page == 'contact') ? 'aria-current="page"' : ''; ?>>Contact</a></li>
                        <?php if ($is_logged_in): ?>
                            <li><a href="<?php echo $base_path; ?>staff-info.php">Staff Home</a></li>
                            <li><a href="<?php echo $base_path; ?>logout.php">Logout</a></li>
                        <?php else: ?>
                            <li><a href="<?php echo $base_path; ?>login.php">Staff Login</a></li>
                        <?php endif; ?>
                    </ul>

                    <form method="GET" action="<?php echo $base_path; ?>search.php" class="core-nav-search" role="search">
                        <label class="sr-only" for="site-search-input">Search</label>
                        <input id="site-search-input" type="text" name="q" placeholder="Search">
                        <button type="submit">Search</button>
                    </form>
                </div>
            </div>
        </nav>
    </div>
</section>
