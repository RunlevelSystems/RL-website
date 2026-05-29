<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/assets/images/RL-icon.png">

        <title>Products | Runlevel Systems</title>
        <meta name="description" content="Products and platforms from Runlevel Systems, plus released and in-development projects.">

        <link href="assets/css/coreloop.css" rel="stylesheet">
    </head>
    <body>
        <?php
        $current_page = 'products';
        $page_subtitle = 'Products';
        $page_description = 'Products and platforms built by Runlevel Systems.';
        $page_title = 'Products';
        ?>

        <?php include 'includes/header.php'; ?>
        <?php include 'includes/navigation.php'; ?>

        <main class="service-site" aria-label="Runlevel Systems products and projects">
            <section class="service-hero compact">
                <div class="container">
                    <p class="service-kicker">Runlevel Systems</p>
                    <h1>Products</h1>
                    <p class="service-lead">Products we sell and projects we build.</p>
                </div>
            </section>

            <section class="service-section">
                <div class="container">
                    <h2>Products &amp; Platforms</h2>
                    <div class="three-column-stack">
                        <article class="service-card-item">
                            <div class="service-icon" aria-hidden="true">🌐</div>
                            <h3>GameServers.World</h3>
                            <p>Hosting and infrastructure services for online communities and multiplayer projects.</p>
                            <a class="core-action tertiary" href="/gameserver-hosting.php">Learn More</a>
                        </article>

                        <article class="service-card-item">
                            <div class="service-icon" aria-hidden="true">🛠️</div>
                            <h3>GSP Panel</h3>
                            <p>Game server management and automation platform.</p>
                            <a class="core-action tertiary" href="/game-server-panel.php">Learn More</a>
                        </article>

                        <article class="service-card-item">
                            <div class="service-icon" aria-hidden="true">☁️</div>
                            <h3>Developer Workspaces</h3>
                            <p>Future collaboration and project management tools.</p>
                            <a class="core-action tertiary" href="/developer-workspaces.php">Learn More</a>
                        </article>
                    </div>
                    <p class="section-intro" style="margin-top: 1.5rem;">Additional future Runlevel Systems products can be added here later.</p>
                </div>
            </section>

            <section class="service-section alt">
                <div class="container">
                    <h2>Released Projects</h2>
                    <p class="section-intro">These are projects built, released, or actively developed by Runlevel Systems.</p>
                    <div class="three-column-stack">
                        <article class="service-card-item project-card-item">
                            <img src="assets/images/RL-splash.png" alt="Roadkill project image" class="project-image">
                            <h3>Roadkill</h3>
                            <p>A completed and released project.</p>
                            <p class="project-status"><strong>Status:</strong> Released</p>
                            <a class="core-action tertiary" href="https://store.steampowered.com/app/1376150/Roadkill/" target="_blank" rel="noopener noreferrer">Learn More</a>
                        </article>

                        <article class="service-card-item project-card-item">
                            <img src="assets/images/code.png" alt="Mystical Islands project image" class="project-image">
                            <h3>Mystical Islands</h3>
                            <p>Multiplayer fantasy RPG currently in development.</p>
                            <p class="project-status"><strong>Status:</strong> In Development</p>
                            <a class="core-action tertiary" href="/projects.php">Learn More</a>
                        </article>

                        <article class="service-card-item project-card-item">
                            <img src="assets/images/RL-logo.png" alt="Neverwards project image" class="project-image">
                            <h3>Neverwards</h3>
                            <p>Project description placeholder until final details are available.</p>
                            <p class="project-status"><strong>Status:</strong> Active Development</p>
                            <a class="core-action tertiary" href="https://store.steampowered.com/app/2096070/Neverwards/" target="_blank" rel="noopener noreferrer">Learn More</a>
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
