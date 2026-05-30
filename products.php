<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/assets/images/RL-icon.png">

        <title>Products | Runlevel Systems</title>
        <meta name="description" content="Runlevel Systems products and portfolio examples including PureOPS, Runlevel Tools, GSP Panel, and GameServers.World.">

        <link href="assets/css/coreloop.css" rel="stylesheet">
    </head>
    <body>
        <?php
        $current_page = 'products';
        $page_subtitle = 'Platforms and products built by Runlevel Systems';
        $page_description = 'Products and portfolio examples from Runlevel Systems.';
        $page_title = 'Products';
        ?>

        <?php include 'includes/header.php'; ?>
        <?php include 'includes/navigation.php'; ?>

        <main class="service-site" aria-label="Runlevel Systems products and portfolio examples">
            <section class="service-hero compact">
                <div class="container">
                    <p class="service-kicker">Products</p>
                    <h1>Platforms &amp; Products</h1>
                    <p class="service-lead">Runlevel Systems builds products that support software delivery, simulation, operations, and hosted infrastructure.</p>
                    <p class="hero-tagline">DESIGN • DEBUG • DEPLOY</p>
                </div>
            </section>

            <section class="service-section">
                <div class="container">
                    <h2>Platforms &amp; Products</h2>
                    <div class="service-card-grid two-columns">
                        <article class="service-card-item">
                            <div class="service-icon" aria-hidden="true">🧪</div>
                            <h3>PureOPS</h3>
                            <p>Virtual training and operations simulation platform based on our interactive hardware training work.</p>
                            <div class="service-actions">
                                <a class="core-action tertiary" href="/simulation.php">Learn More</a>
                            </div>
                        </article>
                        <article class="service-card-item">
                            <div class="service-icon" aria-hidden="true">☁️</div>
                            <h3>Runlevel Tools</h3>
                            <p>Shared project hub for planning, code access, issue tracking, builds, testing, documentation, and Dev Partner workflow.</p>
                            <div class="service-actions">
                                <a class="core-action tertiary" href="/design-debug-deploy.php">Learn More</a>
                            </div>
                        </article>
                        <article class="service-card-item">
                            <div class="service-icon" aria-hidden="true">🛠️</div>
                            <h3>GSP Panel</h3>
                            <p>Open source hosting and service management platform for commercial infrastructure.</p>
                            <div class="service-actions">
                                <a class="core-action tertiary" href="/game-server-panel.php">Learn More</a>
                            </div>
                        </article>
                        <article class="service-card-item">
                            <div class="service-icon" aria-hidden="true">🌐</div>
                            <h3>GameServers.World</h3>
                            <p>Live example of GSP running as a customer-facing hosting service.</p>
                            <div class="service-actions">
                                <a class="core-action tertiary" href="https://gameservers.world" target="_blank" rel="noopener noreferrer">Visit Site</a>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section class="service-section alt">
                <div class="container">
                    <h2>Portfolio Examples</h2>
                    <p class="section-intro">These are examples of our work, not the main company focus.</p>
                    <div class="three-column-stack">
                        <article class="service-card-item project-card-item">
                            <img src="assets/images/RL-splash.png" alt="Pure Storage Training Simulator image" class="project-image">
                            <h3>Pure Storage Training Simulator</h3>
                            <p>Interactive training simulator with virtual hardware and command workflow practice.</p>
                        </article>

                        <article class="service-card-item project-card-item">
                            <img src="assets/images/RL-splash.png" alt="Roadkill project image" class="project-image">
                            <h3>Roadkill</h3>
                            <p>Released multiplayer game and example of interactive product development experience.</p>
                            <a class="core-action tertiary" href="https://store.steampowered.com/app/1376150/Roadkill/" target="_blank" rel="noopener noreferrer">View on Steam</a>
                        </article>

                        <article class="service-card-item project-card-item">
                            <img src="assets/images/RL-logo.png" alt="Neverwards project image" class="project-image">
                            <h3>Neverwards</h3>
                            <p>Active game project and example of long-cycle interactive systems development.</p>
                            <a class="core-action tertiary" href="https://store.steampowered.com/app/2096070/Neverwards/" target="_blank" rel="noopener noreferrer">View on Steam</a>
                        </article>

                        <article class="service-card-item project-card-item">
                            <img src="assets/images/code.png" alt="Mystical Islands project image" class="project-image">
                            <h3>Mystical Islands</h3>
                            <p>In-development multiplayer title and example of real-time systems capability.</p>
                        </article>
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
