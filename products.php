<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/assets/images/RL-icon.png">

        <title>Products | Runlevel Systems</title>
        <meta name="description" content="Products built by Runlevel Systems — GameServers.World, GSP Panel, and Developer Workspaces.">

        <link href="assets/css/coreloop.css" rel="stylesheet">
    </head>
    <body>
        <?php
        $current_page = 'products';
        $page_subtitle = 'Products';
        $page_description = 'Software and services built by Runlevel Systems.';
        $page_title = 'Products';
        ?>

        <?php include 'includes/header.php'; ?>
        <?php include 'includes/navigation.php'; ?>

        <main class="service-site" aria-label="Runlevel Systems products">
            <section class="service-hero compact">
                <div class="container">
                    <p class="service-kicker">Runlevel Systems</p>
                    <h1>Products Built By Runlevel Systems</h1>
                    <p class="service-lead">Software and infrastructure platforms developed to support online communities, game servers, and development teams.</p>
                </div>
            </section>

            <section class="service-section">
                <div class="container">
                    <div class="three-column-stack">

                        <article class="service-card-item">
                            <div class="service-icon" aria-hidden="true">🌐</div>
                            <h3>GameServers.World</h3>
                            <p class="service-card-subtitle">Hosting &amp; Infrastructure</p>
                            <p>Hosting and infrastructure services for online communities and multiplayer projects. From a single game server to a full online platform.</p>
                            <a class="core-action tertiary" href="/gameserver-hosting.php">Learn More</a>
                        </article>

                        <article class="service-card-item">
                            <div class="service-icon" aria-hidden="true">🛠️</div>
                            <h3>GSP Panel</h3>
                            <p class="service-card-subtitle">Game Server Management</p>
                            <p>Game server management and automation platform. Deploy, monitor, and manage game servers with ease.</p>
                            <a class="core-action tertiary" href="/game-server-panel.php">Learn More</a>
                        </article>

                        <article class="service-card-item">
                            <div class="service-icon" aria-hidden="true">☁️</div>
                            <h3>Developer Workspaces</h3>
                            <p class="service-card-subtitle">Tools for Creators</p>
                            <p>Tools and services designed to help creators organize and manage projects, collaborate with teams, and stay productive.</p>
                            <a class="core-action tertiary" href="/developer-workspaces.php">Learn More</a>
                        </article>

                    </div>
                </div>
            </section>

            <section class="service-section">
                <div class="container cta-block">
                    <h2>Need A Technical Partner?</h2>
                    <p>Whether you need development, hosting, customization, or long-term support, Runlevel Systems can help.</p>
                    <div class="service-actions">
                        <a class="core-action primary" href="/contact.php">Contact Us</a>
                        <a class="core-action secondary" href="/projects.php">View Projects</a>
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
