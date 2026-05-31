<!-- Footer -->
<?php
$current_url = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
$is_in_wiki = (strpos($current_url, '/staff/wiki/') !== false);
$is_in_staff = (strpos($current_url, '/staff/') !== false);
$is_in_projects = (strpos($current_url, '/projects/') !== false);
$is_in_portal = (bool) preg_match('#/portal(?:/|$)#', $current_url);
$is_in_client = (strpos($current_url, '/client/') !== false);

if ($is_in_wiki) {
    $footer_base_path = '../../';
} elseif ($is_in_staff || $is_in_projects || $is_in_portal || $is_in_client) {
    $footer_base_path = '../';
} else {
    $footer_base_path = '';
}
?>
<section id="footer-widget" class="footer-widget sitemap-footer">
    <div class="container footer-widget-container">
        <div class="footer-search-wrap">
            <form method="GET" action="<?php echo $footer_base_path; ?>search.php" class="footer-search-form" role="search">
                <label class="sr-only" for="footer-search-input">Search</label>
                <input id="footer-search-input" type="text" name="q" placeholder="Search Runlevel Systems">
                <button type="submit">Search</button>
            </form>
        </div>
        <div class="footer-sitemap-grid">
            <div class="footer-col">
                <h4>What We Build</h4>
                <ul class="footer-list">
                    <li><a href="<?php echo $footer_base_path; ?>software.php">Software Development</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>software.php#business-applications">Business Applications</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>software.php#web-applications">Web Applications</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>software.php#mobile-applications">Mobile Apps</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>software.php#backend-systems">Backend Systems</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>software.php#training-simulation">Training &amp; Simulation</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>software.php#servers-infrastructure">Infrastructure Platforms</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>software.php#automation">Automation</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>software.php#virtualization-containers">Virtualization &amp; Containers</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Products</h4>
                <ul class="footer-list">
                    <li><a href="<?php echo $footer_base_path; ?>design-debug-deploy.php#runlevel-tools">Runlevel Tools</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>game-server-panel.php">GSP Panel</a></li>
                    <li><a href="https://gameservers.world" target="_blank" rel="noopener noreferrer">GameServers.World</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>products.php#pureops">PureOPS</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Projects</h4>
                <ul class="footer-list">
                    <li><a href="<?php echo $footer_base_path; ?>projects.php">Projects &amp; Portfolio</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>projects/pure-storage-training-simulator.php">Pure Storage Training Simulator</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>projects/roadkill.php">Roadkill</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>projects/neverwards.php">Neverwards</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>projects/mystical-islands.php">Mystical Islands</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Work With Us</h4>
                <ul class="footer-list">
                    <li><a href="<?php echo $footer_base_path; ?>design-debug-deploy.php">Dev+1</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>start-project.php">Start Project</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>payments.php">Payment Details</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>contracts.php">Contracts</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>contact.php">Contact</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Dashboard</h4>
                <ul class="footer-list">
                    <li><a href="<?php echo $footer_base_path; ?>client/login.php">Client Login</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>staff/login.php">Staff Login</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>dashboard.php">Dashboard</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Legal</h4>
                <ul class="footer-list">
                    <li><a href="<?php echo $footer_base_path; ?>runlevel-terms.php">Terms of Service</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>gameservers-world-hosting-terms.php">GameServers.World Terms</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>privacy.php">Privacy Policy</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>
<footer class="footer text-center footer-signoff">
    <div>
        <p>&copy; 2026 Runlevel Systems. DESIGN • DEBUG • DEPLOY software, systems, simulations, apps, and infrastructure.</p>
    </div>
</footer>
