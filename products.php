<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/assets/images/RL-icon.png">

        <title>Products &amp; Platforms | Runlevel Systems</title>
        <meta name="description" content="Products, platforms, and portfolio examples built by Runlevel Systems.">
        <link href="assets/css/coreloop.css?v=clean-20260602" rel="stylesheet">
    </head>
    <body>
        <?php
        $current_page = 'products';
        $header_class = 'inner-header';
        $page_subtitle = 'Tools, platforms, and systems built by Runlevel Systems.';
        ?>

        <?php include 'includes/header.php'; ?>
        <?php include 'includes/navigation.php'; ?>

        <main class="service-site" aria-label="Runlevel Systems products and platforms">
            <section class="service-hero compact">
                <div class="container">
                    <p class="service-kicker">Products</p>
                    <h1>Products &amp; Platforms</h1>
                    <p class="service-lead">Tools, platforms, and systems built by Runlevel Systems.</p>
                    <p class="service-sublead">This page shows core platform work, live examples, and selected portfolio examples without turning the company into a games-only brand.</p>
                    <p class="hero-tagline">DESIGN • DEBUG • DEPLOY</p>
                </div>
            </section>

            <section class="service-section" id="platforms">
                <div class="container">
                    <h2>Platforms</h2>
                    <div class="service-card-grid two-columns">
                        <article class="service-card-item" id="runlevel-tools-product">
                            <div class="service-icon" aria-hidden="true">☁️</div>
                            <h3>Runlevel Tools</h3>
                            <p>Shared project hub for planning, code access, issue tracking, builds, testing, documentation, and Dev+1 workflow.</p>
                            <div class="service-actions">
                                <a class="core-action tertiary" href="/design-debug-deploy.php#runlevel-tools">Learn More</a>
                            </div>
                        </article>
                        <article class="service-card-item" id="gsp-panel">
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
                            <p>A live example of GSP operating as a customer-facing hosting service.</p>
                            <div class="service-actions">
                                <a class="core-action tertiary" href="https://gameservers.world" target="_blank" rel="noopener noreferrer">Visit Site</a>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section class="service-section alt" id="pureops">
                <div class="container">
                    <h2>Training &amp; Simulation</h2>
                    <div class="service-card-grid two-columns">
                        <article class="service-card-item">
                            <div class="service-icon" aria-hidden="true">🧪</div>
                            <h3>Virtual Training Without Real-World Risk</h3>
                            <p>PureOPS is an interactive hardware and procedure training simulator. It can model equipment, buttons, lights, removable parts, terminal sessions, maintenance steps, and guided procedures.</p>
                            <p>This helps technicians and employees gain experience before working on real equipment.</p>
                            <p style="margin: 0.85rem 0 0.45rem;"><strong>Use cases:</strong></p>
                            <ul class="service-icon-list">
                                <li>Employee training</li>
                                <li>Hardware walkthroughs</li>
                                <li>Installation practice</li>
                                <li>Maintenance procedures</li>
                                <li>Safety training</li>
                                <li>Product demonstrations</li>
                                <li>Customer education</li>
                                <li>Virtual labs</li>
                            </ul>
                            <div class="service-actions">
                                <a class="core-action tertiary" href="/software.php#training-simulation">See Training &amp; Simulation</a>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section class="service-section">
                <div class="container">
                    <h2>Portfolio Examples</h2>
                    <p class="section-intro">These examples show the range of software, simulation, interactive, and game-related work Runlevel Systems has built or developed.</p>
                    <div class="three-column-stack">
                        <article class="service-card-item project-card-item">
                            <img src="assets/images/RL-splash.png" alt="Pure Storage Training Simulator placeholder image" class="project-image">
                            <h3>Pure Storage Training Simulator</h3>
                            <p>Unity-based interactive hardware training simulator with virtual rack equipment and a guided technician workflow.</p>
                            <div class="service-actions">
                                <a class="core-action tertiary" href="/projects/pure-storage-training-simulator.php">View Project</a>
                            </div>
                        </article>
                        <article class="service-card-item project-card-item">
                            <img src="assets/images/RL-splash.png" alt="Roadkill placeholder image" class="project-image">
                            <h3>Roadkill</h3>
                            <p>Released multiplayer game project that demonstrates commercial interactive systems and live gameplay support experience.</p>
                            <div class="service-actions">
                                <a class="core-action tertiary" href="/projects/roadkill.php">View Project</a>
                            </div>
                        </article>
                        <article class="service-card-item project-card-item">
                            <img src="assets/images/RL-logo.png" alt="Neverwards placeholder image" class="project-image">
                            <h3>Neverwards</h3>
                            <p>Ongoing fantasy game project solely created by our partner Crimsofall Technologies.</p>
                            <div class="service-actions">
                                <a class="core-action tertiary" href="/projects/neverwards.php">View Project</a>
                            </div>
                        </article>
                        <article class="service-card-item project-card-item">
                            <img src="assets/images/code.png" alt="Mystical Islands placeholder image" class="project-image">
                            <h3>Mystical Islands</h3>
                            <p>Interactive multiplayer project co-developed by Runlevel Systems and Crimsofall Technologies.</p>
                            <div class="service-actions">
                                <a class="core-action tertiary" href="/projects/mystical-islands.php">View Project</a>
                            </div>
                        </article>
                    </div>
                    <div class="service-actions" style="margin-top: 1rem;">
                        <a class="core-action secondary" href="/projects.php">View Projects &amp; Portfolio</a>
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
