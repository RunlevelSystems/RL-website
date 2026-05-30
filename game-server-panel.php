<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/assets/images/RL-icon.png">

        <title>GSP | Enterprise Infrastructure Platform | Runlevel Systems</title>
        <meta name="description" content="GSP is an open source enterprise infrastructure platform built by Runlevel Systems for managing services, customers, billing, deployments, automation, and infrastructure. Game server hosting is one example use case.">
        <meta name="keywords" content="GSP, enterprise infrastructure platform, open source, service management, deployment automation, Runlevel Systems">
        <link href="assets/css/coreloop.css" rel="stylesheet">
    </head>
    <body>
        <?php
        $current_page = 'game-server-panel';
        $header_class = 'inner-header';
        $page_subtitle = 'Open Source Enterprise Infrastructure Platform';
        ?>
        <?php include 'includes/header.php'; ?>
        <?php include 'includes/navigation.php'; ?>

        <main class="service-site" aria-label="GSP product page">
            <section class="service-hero compact">
                <div class="container">
                    <p class="service-kicker">Product</p>
                    <h1>GSP</h1>
                    <p class="service-lead">An open source enterprise infrastructure platform for managing services, customers, billing, deployments, automation, and infrastructure.</p>
                    <p class="service-sublead">Game server hosting is one example of what GSP runs. The platform is designed to manage any hosted service operation.</p>
                    <div class="service-actions">
                        <a class="core-action primary" href="https://github.com/RunlevelSystems/GSP" target="_blank" rel="noopener noreferrer">View On GitHub</a>
                        <a class="core-action secondary" href="https://gameservers.world" target="_blank" rel="noopener noreferrer">See Live Example</a>
                    </div>
                </div>
            </section>

            <section class="service-section">
                <div class="container">
                    <h2>What GSP Does</h2>
                    <p class="section-intro">GSP is built to manage the full lifecycle of hosted services — from customer accounts and provisioning through billing, automation, and ongoing operations.</p>
                    <div class="service-card-grid two-columns">
                        <article class="service-card-item"><span class="service-icon" aria-hidden="true">👥</span><h3>Customer Management</h3><p>Manage customer accounts, subscriptions, and service access in one place.</p></article>
                        <article class="service-card-item"><span class="service-icon" aria-hidden="true">💳</span><h3>Billing &amp; Service Management</h3><p>Track services, billing cycles, and customer accounts with built-in management tools.</p></article>
                        <article class="service-card-item"><span class="service-icon" aria-hidden="true">🚀</span><h3>Deployment Automation</h3><p>Provision and deploy services automatically without manual configuration for every customer.</p></article>
                        <article class="service-card-item"><span class="service-icon" aria-hidden="true">🌐</span><h3>Multi-Location Support</h3><p>Manage services across multiple physical or cloud locations from a single control panel.</p></article>
                        <article class="service-card-item"><span class="service-icon" aria-hidden="true">🖥️</span><h3>Linux &amp; Windows Agents</h3><p>Deploy service agents on Linux and Windows hosts for cross-platform infrastructure management.</p></article>
                        <article class="service-card-item"><span class="service-icon" aria-hidden="true">🔧</span><h3>Automation Tools</h3><p>Schedule maintenance, automate updates, and manage infrastructure operations without manual intervention.</p></article>
                    </div>
                </div>
            </section>

            <section class="service-section alt">
                <div class="container">
                    <h2>Current &amp; Future Applications</h2>
                    <p class="section-intro">GSP was initially built for game server hosting. The platform architecture supports much broader infrastructure use cases.</p>
                    <div class="service-card-grid two-columns">
                        <article class="service-card-item">
                            <div class="service-icon" aria-hidden="true">🎮</div>
                            <h3>Game Server Hosting</h3>
                            <p>The original use case. GSP manages game server provisioning, configuration, customer accounts, and operations. <a href="https://gameservers.world" target="_blank" rel="noopener noreferrer">GameServers.World</a> is a live production deployment.</p>
                        </article>
                        <article class="service-card-item">
                            <div class="service-icon" aria-hidden="true">🐳</div>
                            <h3>Docker &amp; Container Hosting</h3>
                            <p>GSP's deployment model extends naturally to Docker container management and containerized application hosting.</p>
                        </article>
                        <article class="service-card-item">
                            <div class="service-icon" aria-hidden="true">📦</div>
                            <h3>Application Hosting</h3>
                            <p>Host and manage customer-facing applications with the same provisioning and management infrastructure.</p>
                        </article>
                        <article class="service-card-item">
                            <div class="service-icon" aria-hidden="true">🏢</div>
                            <h3>Managed Infrastructure</h3>
                            <p>Service platforms, managed infrastructure providers, and businesses running hosted services can use GSP as their operations backbone.</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="service-section">
                <div class="container cta-block">
                    <h2>Open Source &amp; Available Now</h2>
                    <p>GSP is free and open source. You can use it to build your own hosted service business. Commercial support, installation, customization, and migration assistance is available from Runlevel Systems.</p>
                    <div class="service-actions">
                        <a class="core-action primary" href="https://github.com/RunlevelSystems/GSP" target="_blank" rel="noopener noreferrer">View On GitHub</a>
                        <a class="core-action secondary" href="/contact.php?subject=GSP%20Commercial%20Support">Request Commercial Support</a>
                    </div>
                </div>
            </section>
        </main>

        <?php include 'includes/footer.php'; ?>

        <script src="assets/js/jquery-1.12.3.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/jquery.magnific-popup.min.js"></script>
        <script src="assets/js/owl.carousel.min.js"></script>
        <script src="assets/js/script.js"></script>
    </body>
</html>
