<?php
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
<section id="footer-widget" class="footer-widget">
    <div class="container">
        <div class="row">
            <div class="col-sm-4">
                <a href="<?php echo $footer_base_path; ?>index.php">
                    <img src="<?php echo $footer_base_path; ?>assets/images/logo.png" alt="Core Loop Development" class="footer-logo">
                </a>
                <h3>Core Loop Development</h3>
                <p>Developer-first software engineering, multiplayer infrastructure, and platform delivery.</p>
                <ul>
                    <li><a href="https://store.steampowered.com/curator/45805039/" target="_blank" rel="noopener"><i class="ion-social-steam"></i> Core Loop Steam Curator</a></li>
                    <li><a href="https://github.com/World-Domination-Software" target="_blank" rel="noopener"><i class="ion-social-github"></i> GitHub</a></li>
                    <li><a href="https://www.youtube.com/playlist?list=PLPFDCnhUCfoubJuoaGk2i5zzJ4RKqjwkZ" target="_blank" rel="noopener"><i class="ion-social-youtube"></i> YouTube Showcase</a></li>
                </ul>
            </div>
            <div class="col-sm-4">
                <h3>Community</h3>
                <ul>
                    <li><a href="https://discord.gg/XPFnNdWGyW" target="_blank" rel="noopener"><i class="ion-social-discord"></i> Core Loop Discord</a></li>
                    <li><a href="https://discord.gg/Ktxc9jT2sF" target="_blank" rel="noopener"><i class="ion-social-discord"></i> Gameservers.world Discord</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>joinus.php">Join Our Team</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>contact.php">Contact Us</a></li>
                    <li><a href="<?php echo $footer_base_path; ?>privacy.php">Privacy Policy</a></li>
                </ul>
            </div>
            <div class="col-sm-4">
                <h3>Quick Access</h3>
                <ul>
                    <li><a href="https://store.steampowered.com/app/1376150/Roadkill/" target="_blank" rel="noopener">Roadkill on Steam</a></li>
                    <li><a href="https://store.steampowered.com/app/2096070/Neverwards/" target="_blank" rel="noopener">Neverwards on Steam</a></li>
                    <li><a href="https://store.steampowered.com/app/1774030/Mystical_Islands/" target="_blank" rel="noopener">Mystical Islands on Steam</a></li>
                </ul>
                <p>Engineer • Ship • Scale — building the future of games and infrastructure.</p>
            </div>
        </div>
    </div>
</section>
<footer class="footer text-center">
    <div class="container">
        <p>&copy; 2025 Core Loop Development. All rights reserved.</p>
        <p>
            Engineer • Ship • Scale |
            <a href="https://gameservers.world" target="_blank" rel="noopener">Gameservers.world</a> |
            <a href="https://github.com/World-Domination-Software" target="_blank" rel="noopener">GitHub</a> |
            <a href="https://discord.gg/XPFnNdWGyW" target="_blank" rel="noopener">Discord</a>
        </p>
    </div>
</footer>
