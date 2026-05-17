    <!-- Footer -->
<?php
// Path detection for footer links (mirrors header/navigation behaviour)
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
    <!-- Footer -->
        <section id="footer-widget" class="footer-widget" style="background: #000000; color: #eaf3ff; padding: 34px 0 24px; border-top: 1px solid rgba(255,255,255,0.08);">
            <style>
                .footer-widget .footer-list {
                    list-style: none;
                    padding-left: 0;
                    margin: 0;
                }
                .footer-widget .footer-list li {
                    margin: 0 0 8px 0;
                    text-align: center;
                }
                .footer-widget .footer-list a {
                    color: #eaf3ff;
                    text-decoration: none;
                }
                .footer-widget .footer-list a:hover,
                .footer-widget .footer-list a:focus {
                    color: #ffc600;
                    text-decoration: underline;
                }
                .footer-widget .footer-col h3 {
                    color: #eaf3ff;
                    font-size: 24px;
                    margin: 0 0 10px 0;
                }
                .footer-widget .footer-col h4 {
                    color: #eaf3ff;
                    font-size: 19px;
                    margin: 0 0 10px 0;
                    text-align: center;
                }
                .footer-widget .footer-brand-line {
                    color: #c7d7e8;
                    margin: 0;
                    font-size: 14px;
                    line-height: 1.5;
                }
                .footer-widget .footer-widget-container {
                    width: 100%;
                    max-width: 1200px;
                    margin: 0 auto;
                    padding: 0 1rem;
                }
                .footer-widget .footer-row {
                    display: flex;
                    flex-wrap: wrap;
                    justify-content: space-between;
                    align-items: flex-start;
                    text-align: center;
                    gap: 1.5rem;
                }
                .footer-widget .footer-col {
                    flex: 1 1 220px;
                }
                .footer-widget .footer-search-wrap {
                    display: flex;
                    justify-content: center;
                    margin: 0 0 22px;
                }
                .footer-widget .footer-search-form {
                    width: 100%;
                    max-width: 560px;
                    display: flex;
                    align-items: center;
                    gap: 0.45rem;
                }
                .footer-widget .footer-search-form input {
                    flex: 1 1 auto;
                    margin: 0;
                    min-width: 0;
                    border: 1px solid rgba(54, 243, 255, 0.32);
                    background: #09111d;
                    color: #eef6ff;
                    height: 38px;
                    border-radius: 4px;
                    padding: 0 0.7rem;
                }
                .footer-widget .footer-search-form input::placeholder {
                    color: #96adc5;
                }
                .footer-widget .footer-search-form button {
                    height: 38px;
                    padding: 0 0.9rem;
                    border: 1px solid rgba(255, 198, 0, 0.55);
                    border-radius: 4px;
                    background: #0a84ff;
                    color: #08111f;
                    white-space: nowrap;
                }
                .footer-widget .footer-search-form button:hover,
                .footer-widget .footer-search-form button:focus {
                    background: #36f3ff;
                    color: #08111f;
                }
                @media (min-width: 992px) {
                    .footer-widget .footer-widget-container {
                        width: 90%;
                        max-width: 90%;
                    }
                }
                @media (max-width: 600px) {
                    .footer-widget .footer-search-form {
                        flex-direction: column;
                        align-items: stretch;
                    }
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
                        <h4>Site</h4>
                        <ul class="footer-list">
                            <li><a href="<?php echo $footer_base_path; ?>index.php">Home</a></li>
                            <li><a href="<?php echo $footer_base_path; ?>projects.php">Projects</a></li>
                            <li><a href="<?php echo $footer_base_path; ?>joinus.php">Join Our Team</a></li>
                            <li><a href="<?php echo $footer_base_path; ?>contact.php">Contact</a></li>
                            <li><a href="<?php echo $footer_base_path; ?>privacy.php">Privacy Policy</a></li>
                        </ul>
                    </div>
                    <div class="col-sm-4 footer-col">
                        <h4>Community</h4>
                        <ul class="footer-list">
                            <li><a href="https://discord.gg/XPFnNdWGyW" target="_blank">Runlevel Discord</a></li>
                            <li><a href="https://github.com/World-Domination-Software" target="_blank">GitHub</a></li>
                            <li><a href="https://www.youtube.com/playlist?list=PLPFDCnhUCfoubJuoaGk2i5zzJ4RKqjwkZ" target="_blank">YouTube</a></li>
                            <li><a href="https://store.steampowered.com/curator/45805039/" target="_blank">Steam Curator</a></li>
                        </ul>
                    </div>
                    <div class="col-sm-4 footer-col">
                        <h4>Products</h4>
                        <ul class="footer-list">
                            <li><a href="https://github.com/GameServerPanel/GSP" target="_blank" rel="noopener noreferrer">GameServer Panel (Platform)</a></li>
                            <li><a href="https://gameservers.world" target="_blank" rel="noopener noreferrer">Gameservers.world (Hosting Website)</a></li>
                            <li><a href="https://store.steampowered.com/app/1376150/Roadkill/" target="_blank">Roadkill</a></li>
                            <li><a href="https://store.steampowered.com/app/2096070/Neverwards/" target="_blank">Neverwards</a></li>
                            <li><a href="https://store.steampowered.com/app/1774030/Mystical_Islands/" target="_blank">Mystical Islands</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    <footer class="footer text-center" style="background: #000000; color: #eaf3ff;">
            <div style="padding: 14px 0 18px; border-top: 1px solid rgba(255,255,255,0.08);">
                <p style="color: #c7d7e8; margin: 0; font-size: 14px;">
                    &copy; 2025 Runlevel Systems. All rights reserved.
                </p>
            </div>
        </footer>
