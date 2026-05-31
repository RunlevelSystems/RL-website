<?php
if (!isset($_SESSION)) {
    session_start();
}

$current_url = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
$is_in_projects = (strpos($current_url, '/projects/') !== false);
$is_in_staff = (strpos($current_url, '/staff/') !== false);
$is_in_wiki = (strpos($current_url, '/staff/wiki/') !== false);
$is_in_portal = (bool) preg_match('#/portal(?:/|$)#', $current_url);
$is_in_client = (strpos($current_url, '/client/') !== false);

if ($is_in_wiki) {
    $base_path = '../../';
} elseif ($is_in_staff || $is_in_projects || $is_in_portal || $is_in_client) {
    $base_path = '../';
} else {
    $base_path = '';
}
?>

<section id="header" class="main-header <?php echo isset($header_class) ? $header_class : ''; ?>">
    <div class="container-fluid">
        <nav class="navbar core-navbar" role="navigation" aria-label="Primary navigation">
            <div class="core-nav-inner">
                <a href="<?php echo $base_path; ?>index.php" class="core-brand" aria-label="Runlevel Systems home">
                    <img src="<?php echo $base_path; ?>assets/images/RL-logo-name-only.png" alt="Runlevel Systems" class="core-brand-logo">
                </a>

                <button type="button" class="core-nav-toggle" aria-controls="site-nav-bar" aria-expanded="false" aria-label="Open navigation menu">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <div class="core-nav-collapse" id="site-nav-bar">
                    <ul class="core-nav-links">
                        <li><a href="<?php echo $base_path; ?>index.php" <?php echo ($current_page == 'index') ? 'aria-current="page"' : ''; ?>>Home</a></li>
                        <li><a href="<?php echo $base_path; ?>software.php" <?php echo ($current_page == 'software') ? 'aria-current="page"' : ''; ?>>Software</a></li>
                        <li><a href="<?php echo $base_path; ?>simulation.php" <?php echo ($current_page == 'simulation') ? 'aria-current="page"' : ''; ?>>Simulation</a></li>
                        <li><a href="<?php echo $base_path; ?>design-debug-deploy.php" <?php echo (in_array($current_page, ['design-debug-deploy', 'dev-team', 'technical-partner', 'dev-partner'], true)) ? 'aria-current="page"' : ''; ?>>Dev Partner</a></li>
                        <li><a href="<?php echo $base_path; ?>products.php" <?php echo ($current_page == 'products') ? 'aria-current="page"' : ''; ?>>Products</a></li>
                        <li><a href="<?php echo $base_path; ?>pricing.php" <?php echo ($current_page == 'pricing') ? 'aria-current="page"' : ''; ?>>Pricing</a></li>
                        <?php
                        // Dashboard link: staff → staff dashboard, client → client dashboard, guest → login
                        $dashUrl = $base_path . 'login.php';
                        if (!isset($_SESSION)) { @session_start(); }
                        if (!empty($_SESSION['rls_portal_staff']['username'])) {
                            $dashUrl = $base_path . 'staff/dashboard.php';
                        } elseif (!empty($_SESSION['rls_portal_client']['username'])) {
                            $dashUrl = $base_path . 'client/dashboard.php';
                        }
                        $dashActive = in_array($current_page, ['staff-portal', 'client-portal'], true) ? 'aria-current="page"' : '';
                        ?>
                        <li><a href="<?php echo $dashUrl; ?>" <?php echo $dashActive; ?>>Dashboard</a></li>
                        <li><a href="<?php echo $base_path; ?>contact.php" <?php echo ($current_page == 'contact') ? 'aria-current="page"' : ''; ?>>Contact</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var navToggle = document.querySelector('.core-nav-toggle');
    var navMenu = document.getElementById('site-nav-bar');
    if (!navToggle || !navMenu) {
        return;
    }

    var mobileMedia = window.matchMedia('(max-width: 768px)');
    var syncToggleState = function (isOpen) {
        navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        navToggle.setAttribute('aria-label', isOpen ? 'Close navigation menu' : 'Open navigation menu');
    };

    navToggle.addEventListener('click', function () {
        var isOpen = navMenu.classList.toggle('is-open');
        syncToggleState(isOpen);
    });

    var closeMobileMenu = function () {
        navMenu.classList.remove('is-open');
        syncToggleState(false);
    };

    navMenu.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
            if (mobileMedia.matches) {
                closeMobileMenu();
            }
        });
    });

    var handleViewportChange = function (event) {
        if (!event.matches) {
            closeMobileMenu();
        }
    };

    if (typeof mobileMedia.addEventListener === 'function') {
        mobileMedia.addEventListener('change', handleViewportChange);
    }

    closeMobileMenu();
});
</script>
