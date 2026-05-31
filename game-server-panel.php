<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/assets/images/RL-icon.png">

        <title>GSP Panel | Runlevel Systems</title>
        <meta name="description" content="GSP Panel is an open source hosting and service management platform by Runlevel Systems.">
        <meta name="keywords" content="GSP Panel, open source hosting platform, service management platform, Runlevel Systems">
        <link href="assets/css/coreloop.css" rel="stylesheet">
    </head>
    <body>
        <?php
        $current_page = 'game-server-panel';
        $header_class = 'inner-header';
        $page_subtitle = 'Open Source Hosting & Service Management Platform';
        ?>
        <?php include 'includes/header.php'; ?>
        <?php include 'includes/navigation.php'; ?>

        <main class="service-site" aria-label="GSP product page">
            <section class="service-hero compact">
                <div class="container">
                    <p class="service-kicker">Product</p>
                    <h1>GSP Panel</h1>
                    <p class="service-lead">Open Source Hosting &amp; Service Management Platform</p>
                    <p class="service-sublead">GSP is an open source platform created by Runlevel Systems to manage hosted services, users, customer-facing pages, automation, remote systems, and infrastructure.</p>
                    <p class="service-sublead">It currently powers GameServers.World as a live example of a production hosting business.</p>
                    <div class="service-actions">
                        <a class="core-action primary" href="https://github.com/RunlevelSystems/GSP" target="_blank" rel="noopener noreferrer">View On GitHub</a>
                        <a class="core-action secondary" href="https://gameservers.world" target="_blank" rel="noopener noreferrer">View GameServers.World</a>
                        <a class="core-action secondary" href="/start-project.php?type=Commercial%20%2F%20Long-Term%20Work&amp;title=Commercial%20Support%20For%20GSP">Request Commercial Support</a>
                    </div>
                </div>
            </section>

            <section class="service-section">
                <div class="container">
                    <h2>What GSP Can Support</h2>
                    <div class="service-card-grid two-columns">
                        <article class="service-card-item"><h3>Hosted Services</h3><p>Manage services, users, automation, and customer-facing operations in one platform.</p></article>
                        <article class="service-card-item"><h3>Remote Systems</h3><p>Support infrastructure workflows, service management, and hosted environments.</p></article>
                        <article class="service-card-item"><h3>Application Hosting</h3><p>Extend the platform toward hosted applications, managed workspaces, and deployment workflows.</p></article>
                        <article class="service-card-item"><h3>Service Automation</h3><p>Build repeatable operations around provisioning, management, and commercial delivery.</p></article>
                        <article class="service-card-item"><h3>Containers &amp; Workspaces</h3><p>Future uses may include Docker containers, hosted applications, development workspaces, and managed infrastructure.</p></article>
                    </div>
                    <div class="ddd-callout secondary" style="margin-top: 1rem;">
                        <div class="ddd-callout-title">Free and Open Source</div>
                        <p>GSP is free and open source. It can be used to run your own commercial hosting company.</p>
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
