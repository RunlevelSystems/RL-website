    <!-- Footer -->
<?php
$current_url = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
$is_in_wiki = (strpos($current_url, '/staff/wiki/') !== false);
$is_in_staff = (strpos($current_url, '/staff/') !== false);
$is_in_projects = (strpos($current_url, '/projects/') !== false);
$is_in_portal = (bool) preg_match('#/portal(?:/|$)#', $current_url);

if ($is_in_wiki) {
    $footer_base_path = '../../';
} elseif ($is_in_staff || $is_in_projects || $is_in_portal) {
    $footer_base_path = '../';
} else {
    $footer_base_path = '';
}
?>
    <section id="footer-widget" class="footer-widget" style="background: #000000; color: #eaf3ff; padding: 34px 0 24px; border-top: 1px solid rgba(255,255,255,0.08);">
        <style>
            .footer-widget .footer-list { list-style: none; padding-left: 0; margin: 0; }
            .footer-widget .footer-list li { margin: 0 0 8px 0; text-align: center; }
            .footer-widget .footer-list a { color: #eaf3ff; text-decoration: none; }
            .footer-widget .footer-list a:hover,
            .footer-widget .footer-list a:focus { color: #ffc600; text-decoration: underline; }
            .footer-widget .footer-col h4 { color: #eaf3ff; font-size: 19px; margin: 0 0 10px 0; text-align: center; }
            .footer-widget .footer-widget-container { width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 1rem; }
            .footer-widget .footer-row { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; text-align: center; gap: 1.5rem; }
            .footer-widget .footer-col { flex: 1 1 220px; }
            .footer-widget .footer-search-wrap { display: flex; justify-content: center; margin: 0 0 22px; }
            .footer-widget .footer-search-form { width: 100%; max-width: 560px; display: flex; align-items: center; gap: 0.45rem; }
            .footer-widget .footer-search-form input {
                flex: 1 1 auto; margin: 0; min-width: 0; border: 1px solid rgba(54, 243, 255, 0.32);
                background: #09111d; color: #eef6ff; height: 38px; border-radius: 4px; padding: 0 0.7rem;
            }
            .footer-widget .footer-search-form input::placeholder { color: #96adc5; }
            .footer-widget .footer-search-form button {
                height: 38px; padding: 0 0.9rem; border: 1px solid rgba(255, 198, 0, 0.55);
                border-radius: 4px; background: #0a84ff; color: #08111f; white-space: nowrap;
            }
            .footer-widget .footer-search-form button:hover,
            .footer-widget .footer-search-form button:focus { background: #36f3ff; color: #08111f; }
            @media (min-width: 992px) {
                .footer-widget .footer-widget-container { width: 90%; max-width: 90%; }
            }
            @media (max-width: 600px) {
                .footer-widget .footer-search-form { flex-direction: column; align-items: stretch; }
            }
            @media (max-width: 768px) {
                .footer-widget .footer-row { flex-direction: column; align-items: center; justify-content: center; gap: 1.25rem; }
                .footer-widget .footer-col { width: 100%; max-width: 320px; text-align: center; margin: 0 auto; }
            }
        </style>
        <div class="container footer-widget-container">
            <div class="footer-search-wrap">
                <form method="GET" action="<?php echo $footer_base_path; ?>search.php" class="footer-search-form" role="search">
                    <label class="sr-only" for="footer-search-input">Search</label>
                    <input id="footer-search-input" type="text" name="q" placeholder="Search Runlevel Systems">
                    <button type="submit">Search</button>
                </form>
            </div>
            <div class="row footer-row">
                <div class="col-sm-4 footer-col">
                    <h4>What We Build</h4>
                    <ul class="footer-list">
                        <li><a href="<?php echo $footer_base_path; ?>software.php">Software Development</a></li>
                        <li><a href="<?php echo $footer_base_path; ?>simulation.php">Training Simulations</a></li>
                        <li><a href="<?php echo $footer_base_path; ?>software.php">Mobile Apps</a></li>
                        <li><a href="<?php echo $footer_base_path; ?>software.php">Web Applications</a></li>
                        <li><a href="<?php echo $footer_base_path; ?>software.php">Infrastructure Platforms</a></li>
                    </ul>
                </div>
                <div class="col-sm-4 footer-col">
                    <h4>Products</h4>
                    <ul class="footer-list">
                        <li><a href="<?php echo $footer_base_path; ?>simulation.php">PureOPS</a></li>
                        <li><a href="<?php echo $footer_base_path; ?>design-debug-deploy.php">Runlevel Tools</a></li>
                        <li><a href="<?php echo $footer_base_path; ?>game-server-panel.php">GSP Panel</a></li>
                        <li><a href="https://gameservers.world" target="_blank" rel="noopener noreferrer">GameServers.World</a></li>
                    </ul>
                </div>
                <div class="col-sm-4 footer-col">
                    <h4>Work With Us</h4>
                    <ul class="footer-list">
                        <li><a href="<?php echo $footer_base_path; ?>design-debug-deploy.php">Dev Partner</a></li>
                        <li><a href="<?php echo $footer_base_path; ?>contact.php">Tell Us About Your Project</a></li>
                        <li><a href="<?php echo $footer_base_path; ?>contact.php">Contact</a></li>
                        <li><a href="<?php echo $footer_base_path; ?>privacy.php">Privacy Policy</a></li>
                        <li><a href="<?php echo $footer_base_path; ?>runlevel-terms.php">Runlevel Systems Terms</a></li>
                        <li><a href="<?php echo $footer_base_path; ?>gameservers-world-hosting-terms.php">GameServers.World Hosting Terms</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <footer class="footer text-center" style="background: #000000; color: #eaf3ff;">
        <div style="padding: 14px 0 18px; border-top: 1px solid rgba(255,255,255,0.08);">
            <p style="color: #c7d7e8; margin: 0; font-size: 14px;">
                &copy; 2026 Runlevel Systems. DESIGN • DEBUG • DEPLOY software, simulations, mobile apps, and infrastructure.
            </p>
        </div>
    </footer>
