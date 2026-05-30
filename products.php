<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/assets/images/RL-icon.png">

        <title>Products | Runlevel Systems</title>
        <meta name="description" content="Featured products and platforms from Runlevel Systems: GSP infrastructure platform, PureOPS Training Platform, and Runlevel Tools. Plus completed projects including Roadkill, Neverwards, and Mystical Islands.">

        <link href="assets/css/coreloop.css" rel="stylesheet">
    </head>
    <body>
        <?php
        $current_page = 'products';
        $page_subtitle = 'Products that support software delivery and operations';
        $page_description = 'Products and platforms built by Runlevel Systems.';
        $page_title = 'Products';
        ?>

        <?php include 'includes/header.php'; ?>
        <?php include 'includes/navigation.php'; ?>

        <main class="service-site" aria-label="Runlevel Systems products and projects">
            <section class="service-hero compact">
                <div class="container">
                    <p class="service-kicker">Runlevel Systems</p>
                    <h1>Featured Products &amp; Platforms</h1>
                    <p class="service-lead">Products and platforms built by Runlevel Systems — from enterprise infrastructure to team collaboration to virtual training.</p>
                    <p class="hero-tagline">DESIGN • DEBUG • DEPLOY</p>
                </div>
            </section>

            <section class="service-section">
                <div class="container">
                    <h2>Products &amp; Platforms</h2>
                    <div class="service-card-grid two-columns">
                        <article class="service-card-item">
                            <div class="service-icon" aria-hidden="true">🛠️</div>
                            <h3>Game Server Panel (GSP)</h3>
                            <p class="service-card-subtitle">Enterprise infrastructure platform</p>
                            <p>GSP is an enterprise infrastructure platform for managing services, customers, billing, deployments, automation, and infrastructure. Game server hosting is one example of what it runs.</p>
                            <p>GSP is open source and free to use. Future applications include Docker hosting, application hosting, service platforms, and managed infrastructure.</p>
                            <div class="service-actions">
                                <a class="core-action tertiary" href="/game-server-panel.php">Learn More</a>
                                <a class="core-action tertiary" href="https://github.com/RunlevelSystems/GSP" target="_blank" rel="noopener noreferrer">View on GitHub</a>
                            </div>
                        </article>

                        <article class="service-card-item">
                            <div class="service-icon" aria-hidden="true">🧪</div>
                            <h3>PureOPS Training Platform</h3>
                            <p class="service-card-subtitle">Interactive hardware training simulation</p>
                            <p>PureOPS is an interactive hardware training environment developed by Runlevel Systems. It allows technicians to learn installation, maintenance, and upgrade procedures inside a virtual environment before working on production equipment.</p>
                            <p>Designed for enterprise equipment training, safety training, onboarding, IT operations, and customer education.</p>
                            <div class="service-actions">
                                <a class="core-action tertiary" href="/simulation.php">Learn More</a>
                            </div>
                        </article>

                        <article class="service-card-item">
                            <div class="service-icon" aria-hidden="true">☁️</div>
                            <h3>Runlevel Tools</h3>
                            <p class="service-card-subtitle">Shared development workspace</p>
                            <p>Runlevel Tools is a shared development workspace connecting customers and Runlevel Systems. It is the platform behind Dev Partner — keeping project planning, source code, builds, deployments, documentation, and team collaboration connected in one place.</p>
                            <ul class="service-icon-list single-column" style="margin-top: 0.6rem;">
                                <li>Project planning and Kanban boards</li>
                                <li>Milestones and progress tracking</li>
                                <li>Source code management</li>
                                <li>Build and deployment tracking</li>
                                <li>Documentation and team collaboration</li>
                            </ul>
                            <div class="service-actions">
                                <a class="core-action tertiary" href="/design-debug-deploy.php">Learn More</a>
                            </div>
                        </article>

                        <article class="service-card-item">
                            <div class="service-icon" aria-hidden="true">🌐</div>
                            <h3>GameServers.World</h3>
                            <p class="service-card-subtitle">Live deployment of GSP infrastructure</p>
                            <p>GameServers.World is a real-world deployment of the GSP platform, providing hosted infrastructure for gaming communities and multiplayer projects. It is a working example of GSP in commercial production use.</p>
                            <div class="service-actions">
                                <a class="core-action tertiary" href="/gameserver-hosting.php">Learn More</a>
                                <a class="core-action tertiary" href="https://gameservers.world" target="_blank" rel="noopener noreferrer">Visit Site</a>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section class="service-section alt">
                <div class="container">
                    <h2>Projects &amp; Portfolio</h2>
                    <p class="section-intro">These are examples of completed and active development work from Runlevel Systems. Games and interactive experiences represent one area of our development portfolio alongside business software, infrastructure platforms, and training systems.</p>
                    <div class="three-column-stack">
                        <article class="service-card-item project-card-item">
                            <img src="assets/images/RL-splash.png" alt="Roadkill project image" class="project-image">
                            <h3>Roadkill</h3>
                            <p>Released action game developed and published by Runlevel Systems.</p>
                            <p class="project-status"><strong>Status:</strong> Released</p>
                            <a class="core-action tertiary" href="https://store.steampowered.com/app/1376150/Roadkill/" target="_blank" rel="noopener noreferrer">View on Steam</a>
                        </article>

                        <article class="service-card-item project-card-item">
                            <img src="assets/images/RL-logo.png" alt="Neverwards project image" class="project-image">
                            <h3>Neverwards</h3>
                            <p>Active game development project within the Runlevel Systems portfolio.</p>
                            <p class="project-status"><strong>Status:</strong> Active Development</p>
                            <a class="core-action tertiary" href="https://store.steampowered.com/app/2096070/Neverwards/" target="_blank" rel="noopener noreferrer">View on Steam</a>
                        </article>

                        <article class="service-card-item project-card-item">
                            <img src="assets/images/code.png" alt="Mystical Islands project image" class="project-image">
                            <h3>Mystical Islands</h3>
                            <p>Multiplayer fantasy RPG and example of ongoing game development within the broader Runlevel Systems portfolio.</p>
                            <p class="project-status"><strong>Status:</strong> In Development</p>
                            <a class="core-action tertiary" href="/projects.php">Learn More</a>
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
