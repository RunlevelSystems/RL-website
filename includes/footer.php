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
<section id="footer-widget" class="footer-widget sitemap-footer site-footer">
    <div class="footer-top-divider" aria-hidden="true"></div>
    <div class="container footer-widget-container">
        <div class="footer-sitemap-grid">
            <div class="footer-col footer-brand">
                <h4>Runlevel Systems</h4>
                <p class="footer-eyebrow">Design • Debug • Deploy</p>
                <p class="footer-description">Runlevel Systems delivers software, infrastructure, and project execution from concept through launch.</p>
            </div>
            <div class="footer-col">
                <h4>Services</h4>
                <ul class="footer-list">
                    <li><a href="<?php echo $footer_base_path; ?>design-debug-deploy.php">Design • Debug • Deploy</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>gameserver-hosting.php">Game Server Hosting</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>game-server-panel.php">Game Server Panel</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>developer-workspaces.php">Developer Workspaces</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Products</h4>
                <ul class="footer-list">
                    <li><a href="https://gameservers.world" target="_blank" rel="noopener noreferrer">GameServers.World</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>game-server-panel.php">Game Server Panel</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>products.php#pureops">PureOPS</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Projects</h4>
                <ul class="footer-list">
                    <li><a href="<?php echo $footer_base_path; ?>projects.php">Projects &amp; Portfolio</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>projects/roadkill.php">Roadkill</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>projects/neverwards.php">Neverwards</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>projects/mystical-islands.php">Mystical Islands</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Company</h4>
                <ul class="footer-list">
                    <li><a href="<?php echo $footer_base_path; ?>contact.php">Contact</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>start-project.php">Start Project</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>runlevel-terms.php">Legal</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Account</h4>
                <ul class="footer-list">
                    <li><a href="<?php echo $footer_base_path; ?>dashboard.php">Dashboard</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>
<footer class="footer text-center footer-signoff site-footer">
    <div>
        <p>&copy; 2026 Runlevel Systems. DESIGN • DEBUG • DEPLOY software, systems, simulations, apps, and infrastructure.</p>
    </div>
</footer>
