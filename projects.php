<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/assets/images/RL-icon.png">

        <title>Projects &amp; Portfolio | Runlevel Systems</title>
        <meta name="description" content="Examples of software, simulation, games, and interactive systems built or developed by Runlevel Systems.">
        <link href="assets/css/coreloop.css?v=clean-20260602" rel="stylesheet">
    </head>
    <body>
        <?php
        $current_page = 'projects';
        $header_class = 'inner-header';
        $page_subtitle = 'Examples of software, simulation, games, and interactive systems built or developed by Runlevel Systems.';
        ?>
        <?php include 'includes/header.php'; ?>
        <?php include 'includes/navigation.php'; ?>

        <main class="service-site" aria-label="Projects and portfolio page">
            <section class="service-hero compact">
                <div class="container">
                    <p class="service-kicker">Projects</p>
                    <h1>Projects &amp; Portfolio</h1>
                    <p class="service-lead">Examples of software, simulation, games, and interactive systems built or developed by Runlevel Systems.</p>
                    <p class="service-sublead">These examples show range and capability. They are portfolio examples, not the entire company focus.</p>
                    <p class="hero-tagline">DESIGN • DEBUG • DEPLOY</p>
                </div>
            </section>

            <section class="service-section">
                <div class="container">
                    <div class="three-column-stack">
                        <article class="service-card-item project-card-item">
                            <img src="assets/images/RL-splash.png" alt="Pure Storage Training Simulator placeholder image" class="project-image">
                            <h3>Pure Storage Training Simulator</h3>
                            <p>Interactive Unity-based training simulator for technical hardware workflows and technician practice.</p>
                            <div class="service-actions"><a class="core-action tertiary" href="/projects/pure-storage-training-simulator.php">View Project</a></div>
                        </article>
                        <article class="service-card-item project-card-item">
                            <img src="assets/images/RL-splash.png" alt="Roadkill placeholder image" class="project-image">
                            <h3>Roadkill</h3>
                            <p>Released multiplayer project showing commercial game development and live interactive system experience.</p>
                            <div class="service-actions"><a class="core-action tertiary" href="/projects/roadkill.php">View Project</a></div>
                        </article>
                        <article class="service-card-item project-card-item">
                            <img src="assets/images/RL-logo.png" alt="Neverwards placeholder image" class="project-image">
                            <h3>Neverwards</h3>
                            <p>Fantasy action project solely created by our partner Crimsofall Technologies.</p>
                            <div class="service-actions"><a class="core-action tertiary" href="/projects/neverwards.php">View Project</a></div>
                        </article>
                        <article class="service-card-item project-card-item">
                            <img src="assets/images/code.png" alt="Mystical Islands placeholder image" class="project-image">
                            <h3>Mystical Islands</h3>
                            <p>Multiplayer game example co-developed by Runlevel Systems and Crimsofall Technologies.</p>
                            <div class="service-actions"><a class="core-action tertiary" href="/projects/mystical-islands.php">View Project</a></div>
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
