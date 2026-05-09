    <!-- Footer -->
<?php
// Path detection for footer links (mirrors header/navigation behaviour)
$current_url = $_SERVER['REQUEST_URI'];
$is_in_wiki = (strpos($current_url, '/staff/wiki/') !== false);
$is_in_staff = (strpos($current_url, '/staff/') !== false);
$is_in_projects = (strpos($current_url, '/projects/') !== false);

if ($is_in_wiki) {
    $footer_base_path = '../../';
} elseif ($is_in_staff || $is_in_projects) {
    $footer_base_path = '../';
} else {
    $footer_base_path = '';
}
?>
    <!-- Footer -->
        <section id="footer-widget" class="footer-widget" style="background: #0d1117; color: #94a3b8; padding: 40px 0;">
            <div class="container">
                <div class="row">
                    <div class="col-sm-4">
                        <h3>Dev Links</h3>
                        <ul>
                            <li><a href="https://store.steampowered.com/curator/45805039/" target="_blank">
                                <i class="ion-social-steam" style="margin-right: 8px;"></i>Level X on Steam
                            </a></li>
                            <li><a href="https://github.com/World-Domination-Software" target="_blank">
                                <i class="ion-social-github" style="margin-right: 8px;"></i>GitHub
                            </a></li>
                            <li><a href="https://www.youtube.com/playlist?list=PLPFDCnhUCfoubJuoaGk2i5zzJ4RKqjwkZ" target="_blank">
                                <i class="ion-social-youtube" style="margin-right: 8px;"></i>YouTube Showcase
                            </a></li>
                        </ul>
                    </div>
                    <div class="col-sm-4">
                        <h3>Community</h3>
                        <ul>
                            <li><a href="https://discord.gg/XPFnNdWGyW" target="_blank">
                                <i class="ion-social-discord" style="margin-right: 8px;"></i>Level X Discord
                            </a></li>
                            <li><a href="https://discord.gg/Ktxc9jT2sF" target="_blank">
                                <i class="ion-social-discord" style="margin-right: 8px;"></i>Gameservers.world Discord
                            </a></li>
                            <li><a href="<?php echo $footer_base_path; ?>joinus.php">Join Our Team</a></li>
                            <li><a href="<?php echo $footer_base_path; ?>contact.php">Contact Us</a></li>
                            <li><a href="<?php echo $footer_base_path; ?>privacy.php">Privacy Policy</a></li>
                        </ul>
                    </div>
                    <div class="col-sm-4">
                        <h3>Quick Access</h3>
                        <ul>
                            <li><a href="https://store.steampowered.com/app/1376150/Roadkill/" target="_blank">Roadkill on Steam</a></li>
                            <li><a href="https://store.steampowered.com/app/2096070/Neverwards/" target="_blank">Neverwards on Steam</a></li>
                            <li><a href="https://store.steampowered.com/app/1774030/Mystical_Islands/" target="_blank">Mystical Islands on Steam</a></li>
                        </ul>
                        <p style="color: #64748b; font-size: 14px; line-height: 1.5; margin-top: 15px;">
                            Design • Debug • Deploy — building the future of games and infrastructure.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    <footer class="footer text-center" style="background: #0d1117; color: #94a3b8;">
            <div style="padding: 20px 0; border-top: 1px solid rgba(0,212,255,0.1);">
                <p style="color: #64748b; margin-bottom: 10px; font-size: 16px;">
                    &copy; 2025 Level X Development. All rights reserved.
                </p>
                <p style="color: #64748b; margin: 0; font-size: 14px;">
                    Design • Debug • Deploy | 
                    <a href="https://gameservers.world" target="_blank" style="color: #64748b;">Gameservers.world</a> | 
                    <a href="https://github.com/World-Domination-Software" target="_blank" style="color: #64748b;">GitHub</a> | 
                    <a href="https://discord.gg/XPFnNdWGyW" target="_blank" style="color: #64748b;">Discord</a>
                </p>
            </div>
        </footer>
