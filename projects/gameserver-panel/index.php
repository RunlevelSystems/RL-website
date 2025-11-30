<?php
// Simple fragment for the GameServer Panel project.
// Only PHP used here is for project icon detection; the rest is static HTML.

$projectDir = __DIR__;
$iconHtml = '';
if (file_exists($projectDir . '/projecticon.png')) {
    $iconHtml = '<img src="projects/gameserver-panel/projecticon.png" alt="GameServer Panel" style="width:100px;height:100px;border-radius:8px;object-fit:cover;box-shadow:0 4px 12px rgba(139,69,19,0.3);">';
} elseif (file_exists($projectDir . '/projecticon.jpg')) {
    $iconHtml = '<img src="projects/gameserver-panel/projecticon.jpg" alt="GameServer Panel" style="width:100px;height:100px;border-radius:8px;object-fit:cover;box-shadow:0 4px 12px rgba(139,69,19,0.3);">';
} else {
    $iconHtml = '<div style="width:100px;height:100px;background:#8B4513;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(139,69,19,0.3);"><i class="fas fa-server" style="color:#E8E4D8;font-size:48px"></i></div>';
}
?>

<!-- Project Overview -->
<div style="text-align:center;margin-bottom:30px;">
    <div style="background:#D4CFC0;border:1px solid #333;border-radius:8px;padding:40px;margin-bottom:40px;">
        <div style="margin-bottom:20px;">
            <?= $iconHtml ?>
        </div>
        <h3 style="color:#8B4513;margin-bottom:20px;">Professional Game Server Management Software</h3>
        <p style="color:#4a4a4a;font-size:18px;line-height:1.8;margin-bottom:20px;">
            Our enhanced fork of OpenGamePanel (OGP) featuring complete commercial billing integration with PayPal, coupon system, automated provisioning, and multi-location server management.
            Open source software for hosting providers who want to run their own commercial game server business.
        </p>
    </div>
</div>

<!-- Core Features -->
<div style="background:#D4CFC0;border:1px solid #B8B3A8;border-radius:8px;padding:40px;margin-bottom:40px;">
    <h3 style="color:#8B4513;margin-bottom:30px;">Key Features</h3>
    <div class="row">
        <div class="col-sm-6">
            <ul style="color:#4a4a4a;line-height:2;">
                <li><strong style="color:#8B4513;">Complete Billing System:</strong> PayPal integration, coupon codes, automated provisioning</li>
                <li><strong style="color:#8B4513;">100+ Supported Games:</strong> Minecraft, ARK, Rust, CS2, Valheim, and many more</li>
                <li><strong style="color:#8B4513;">Multi-Server Management:</strong> Manage servers across multiple physical machines</li>
                <li><strong style="color:#8B4513;">Customer Portal:</strong> Professional interface for customers to manage their servers</li>
            </ul>
        </div>
        <div class="col-sm-6">
            <ul style="color:#4a4a4a;line-height:2;">
                <li><strong style="color:#8B4513;">Windows &amp; Linux Agents:</strong> Flexible deployment on any platform</li>
                <li><strong style="color:#8B4513;">Optional Modules:</strong> FastDownload, addons manager, statistics, and more</li>
                <li><strong style="color:#8B4513;">Open Source:</strong> Full source code available on GitHub</li>
                <li><strong style="color:#8B4513;">Active Development:</strong> Regular updates and improvements</li>
            </ul>
        </div>
    </div>
</div>

<!-- GitHub Repositories / What's Included -->
<div style="background:#D4CFC0;border:1px solid #C4C0B4;border-radius:8px;padding:40px;margin-bottom:40px;">
    <h3 style="color:#8B4513;margin-bottom:30px;"><i class="fab fa-github" style="margin-right:10px;"></i>Source Code Repositories</h3>
    <p style="color:#4a4a4a;margin-bottom:30px;">All components are open source and available on GitHub. Fork, modify, and deploy your own game server hosting business.</p>

    <div class="row">
        <div class="col-sm-4">
            <div style="background:#E8E4D8;border:1px solid #C4C0B4;border-radius:6px;padding:25px;text-align:center;margin-bottom:20px;min-height:240px;">
                <i class="fas fa-server" style="color:#8B4513;font-size:48px;margin-bottom:15px;"></i>
                <h4 style="color:#8B4513;margin-bottom:15px;">Main Panel</h4>
                <p style="color:#4a4a4a;font-size:14px;margin-bottom:20px;">Web panel with billing system and customer management</p>
                <a href="https://github.com/GameServerPanel/GSP" target="_blank" class="btn btn-wds"><i class="fab fa-github" style="margin-right:8px;"></i>View Repository</a>
            </div>
        </div>

        <div class="col-sm-4">
            <div style="background:#E8E4D8;border:1px solid #C4C0B4;border-radius:6px;padding:25px;text-align:center;margin-bottom:20px;min-height:240px;">
                <i class="fab fa-linux" style="color:#8B4513;font-size:48px;margin-bottom:15px;"></i>
                <h4 style="color:#8B4513;margin-bottom:15px;">Linux Agent</h4>
                <p style="color:#4a4a4a;font-size:14px;margin-bottom:20px;">Agent software for Linux game server hosts</p>
                <a href="https://github.com/GameServerPanel/GSP_Agent_Linux" target="_blank" class="btn btn-wds"><i class="fab fa-github" style="margin-right:8px;"></i>View Repository</a>
            </div>
        </div>

        <div class="col-sm-4">
            <div style="background:#E8E4D8;border:1px solid #C4C0B4;border-radius:6px;padding:25px;text-align:center;margin-bottom:20px;min-height:240px;">
                <i class="fab fa-windows" style="color:#8B4513;font-size:48px;margin-bottom:15px;"></i>
                <h4 style="color:#8B4513;margin-bottom:15px;">Windows Agent</h4>
                <p style="color:#4a4a4a;font-size:14px;margin-bottom:20px;">Agent software for Windows game server hosts</p>
                <a href="https://github.com/GameServerPanel/GSP-Agent-Windows" target="_blank" class="btn btn-wds"><i class="fab fa-github" style="margin-right:8px;"></i>View Repository</a>
            </div>
        </div>
    </div>
</div>

<!-- What's Included -->
<div style="background:#D4CFC0;border:1px solid #B8B3A8;border-radius:8px;padding:40px;margin-bottom:40px;">
    <h3 style="color:#8B4513;margin-bottom:30px;">What's Included</h3>
    <div class="row">
        <div class="col-sm-6">
            <h4 style="color:#8B4513;margin-bottom:15px;"><i class="fas fa-check-circle" style="color:#8B4513;margin-right:10px;"></i>Billing &amp; Payment</h4>
            <p style="color:#4a4a4a;margin-bottom:25px;">
                Complete billing module with PayPal integration, invoice generation, coupon system, and automated server provisioning after payment. Everything you need to accept payments and automatically deliver game servers to customers.
            </p>
            
            <h4 style="color:#8B4513;margin-bottom:15px;"><i class="fas fa-check-circle" style="color:#8B4513;margin-right:10px;"></i>Customer Management</h4>
            <p style="color:#4a4a4a;margin-bottom:25px;">
                Professional customer portal where users can view their servers, manage settings, access file managers, view invoices, and control their game servers. Clean, modern interface.
            </p>
            
            <h4 style="color:#8B4513;margin-bottom:15px;"><i class="fas fa-check-circle" style="color:#8B4513;margin-right:10px;"></i>Multi-Location Support</h4>
            <p style="color:#4a4a4a;margin-bottom:25px;">
                Manage game servers across multiple physical machines and locations. Central web panel communicates with agent software running on each game server host.
            </p>
        </div>
        <div class="col-sm-6">
            <h4 style="color:#8B4513;margin-bottom:15px;"><i class="fas fa-check-circle" style="color:#8B4513;margin-right:10px;"></i>Extensive Game Support</h4>
            <p style="color:#4a4a4a;margin-bottom:25px;">
                Pre-configured templates for 100+ game server types including Minecraft (Java/Bedrock), ARK: Survival Evolved, Rust, Counter-Strike 2, Valheim, 7 Days to Die, Terraria, and many more. Easy to add new games.
            </p>
            
            <h4 style="color:#8B4513;margin-bottom:15px;"><i class="fas fa-check-circle" style="color:#8B4513;margin-right:10px;"></i>Optional Modules</h4>
            <p style="color:#4a4a4a;margin-bottom:25px;">
                Many optional modules included by default: FastDownload for game content, addons manager, statistics tracking, MySQL database management, FTP server integration, and more. Extend functionality as needed.
            </p>
            
            <h4 style="color:#8B4513;margin-bottom:15px;"><i class="fas fa-check-circle" style="color:#8B4513;margin-right:10px;"></i>Full Source Code</h4>
            <p style="color:#4a4a4a;margin-bottom:25px;">
                Complete source code available on GitHub. Fork and modify to fit your specific needs. Active community development with regular updates and improvements.
            </p>
        </div>
    </div>
</div>

<!-- Getting Started -->
<div style="background:#D4CFC0;border:1px solid #333;border-radius:8px;padding:40px;margin-bottom:40px;">
    <h3 style="color:#8B4513;margin-bottom:20px;">Getting Started</h3>
    <ol style="color:#4a4a4a;font-size:16px;line-height:2;padding-left:30px;">
        <li><strong style="color:#8B4513;">Clone the repositories:</strong> Download the panel and agent software from GitHub</li>
        <li><strong style="color:#8B4513;">Install the panel:</strong> Follow the installation guide to set up the web panel on your server</li>
        <li><strong style="color:#8B4513;">Deploy agents:</strong> Install agent software on machines that will host game servers</li>
        <li><strong style="color:#8B4513;">Configure billing:</strong> Set up your PayPal account and payment settings</li>
        <li><strong style="color:#8B4513;">Add game templates:</strong> Configure which games you want to offer to customers</li>
        <li><strong style="color:#8B4513;">Launch your business:</strong> Start accepting orders and provisioning game servers!</li>
    </ol>

    <div style="margin-top:30px;padding:20px;background:#E8E4D8;border-left:4px solid #8B4513;border-radius:4px;">
        <p style="color:#4a4a4a;margin:0;"><i class="fas fa-info-circle" style="color:#8B4513;margin-right:10px;"></i><strong>Documentation:</strong> Every repository now ships a <code>documentation/</code> folder with Markdown guides you can read offline or mirror into a wiki. Use the quick links below.</p>
        <div style="margin-top:15px;display:flex;flex-wrap:wrap;gap:10px;">
            <a href="https://github.com/GameServerPanel/GSP/tree/main/documentation" target="_blank" class="btn btn-wds"><i class="fas fa-server" style="margin-right:8px;"></i>Panel Docs</a>
            <a href="https://github.com/GameServerPanel/GSP_Agent_Linux/tree/main/documentation" target="_blank" class="btn btn-wds"><i class="fab fa-linux" style="margin-right:8px;"></i>Linux Agent Docs</a>
            <a href="https://github.com/GameServerPanel/GSP-Agent-Windows/tree/main/documentation" target="_blank" class="btn btn-wds"><i class="fab fa-windows" style="margin-right:8px;"></i>Windows Agent Docs</a>
            <a href="#" onclick="loadProjectFile('industry-stats.php'); return false;" class="btn btn-wds"><i class="fas fa-chart-line" style="margin-right:8px;"></i>Industry Stats</a>
            <a href="#" onclick="loadProjectFile('wiki.php?doc=overview'); return false;" class="btn btn-wds"><i class="fas fa-book-open" style="margin-right:8px;"></i>Project Wiki</a>
        </div>
    </div>
</div>

<!-- Additional Resources -->
<div style="background:#D4CFC0;border:1px solid #B8B3A8;border-radius:8px;padding:40px;margin-bottom:40px;">
    <h3 style="color:#8B4513;margin-bottom:30px;">Learn More</h3>
    <div class="row">
        <div class="col-sm-6" style="margin-bottom:20px;">
            <div style="background:#E8E4D8;border:1px solid #C4C0B4;border-radius:6px;padding:25px;height:100%;">
                <h4 style="color:#8B4513;margin-bottom:15px;"><i class="fas fa-code-branch" style="margin-right:10px;"></i>Repositories &amp; Docs</h4>
                <p style="color:#4a4a4a;margin-bottom:20px;">Browse the source code plus the new <code>documentation/</code> folders for offline Markdown guides covering deployment, XML authoring, and runbooks.</p>
                <a href="https://github.com/GameServerPanel" target="_blank" class="btn btn-secondary"><i class="fab fa-github" style="margin-right:8px;"></i>GitHub Org</a>
            </div>
        </div>
        <div class="col-sm-6" style="margin-bottom:20px;">
            <div style="background:#E8E4D8;border:1px solid #C4C0B4;border-radius:6px;padding:25px;height:100%;">
                <h4 style="color:#8B4513;margin-bottom:15px;"><i class="fas fa-chart-line" style="margin-right:10px;"></i>Industry Statistics</h4>
                <p style="color:#4a4a4a;margin-bottom:20px;">Market data and statistics about the game server hosting industry and growth opportunities.</p>
                <a href="#" onclick="loadProjectFile('industry-stats.php'); return false;" class="btn btn-secondary">View Industry Stats</a>
            </div>
        </div>
    </div>
</div>

<!-- Call to Action -->
<div style="text-align:center;padding:40px;">
    <h3 style="color:#8B4513;margin-bottom:20px;">Ready to Build Your Hosting Business?</h3>
    <p style="color:#4a4a4a;font-size:16px;margin-bottom:30px;">Download the source code and start your commercial game server hosting company today.</p>
    <a href="https://github.com/GameServerPanel/GSP" target="_blank" class="btn btn-wds" style="margin-right:15px;"><i class="fab fa-github" style="margin-right:8px;"></i>View on GitHub</a>
    <a href="https://discord.gg/XPFnNdWGyW" target="_blank" class="btn btn-secondary" style="margin-right:15px;"><i class="fab fa-discord" style="margin-right:8px;"></i>Join Discord</a>
    <a href="/contact.php" class="btn btn-secondary"><i class="fas fa-envelope" style="margin-right:8px;"></i>Contact Us</a>
</div>
