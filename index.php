<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/assets/images/icon.png">

        <title>Core Loop Development</title>

        <link href="assets/css/coreloop.css" rel="stylesheet">
    </head>
    <body>
        <?php
        $current_page = 'index';
        $page_subtitle = 'Engineer • Ship • Scale';
        $page_description = '';
        $page_title = '';
        $page_title_thin = '';
        ?>

        <?php include 'includes/header.php'; ?>
        <?php include 'includes/navigation.php'; ?>

        <section class="wds-hero" aria-label="Core Loop overview">
            <div class="container hero-content">
                <div>
                    <img src="assets/images/logo.png" alt="Core Loop Development" class="hero-logo">
                    <div class="hero-eyebrow">Software Company</div>
                    <h1 class="hero-title">Core Loop Development</h1>
                    <p class="hero-tagline">Engineer • Ship • Scale</p>
                    <p class="hero-support">We design and deliver software development, game development, multiplayer infrastructure, and business applications for teams that need reliable delivery and long-term maintainability.</p>
                    <div class="hero-cta">
                        <a class="btn" href="contact.php">Request a Consultation</a>
                        <a class="btn btn-ghost" href="https://gameservers.world" target="_blank" rel="noopener">Game Servers</a>
                        <a class="btn btn-ghost" href="/portal">Client Portal</a>
                    </div>
                </div>
                <div class="service-grid">
                    <article class="service-card">
                        <h3>Software Engineering</h3>
                        <p>Robust product engineering for web platforms, APIs, and operational tooling.</p>
                    </article>
                    <article class="service-card">
                        <h3>Game Development</h3>
                        <p>Gameplay systems, multiplayer features, and production-ready game pipelines.</p>
                    </article>
                    <article class="service-card">
                        <h3>Infrastructure &amp; Hosting</h3>
                        <p>Multiplayer infrastructure, server operations, and scalable deployment support.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="history">
            <div class="container page-bgc">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="title-box">
                            <p>Since 1996</p>
                            <h2 class="title mt0">Our Professional Legacy</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="boxed">
                        <div class="col-sm-12">
                            <p class="inner-p">We are seasoned IT professionals with a long-standing commitment to development and operations. From early online communities to modern cloud and game hosting stacks, we have continuously shipped reliable systems.</p>
                            <p class="inner-p">Our day-to-day work spans enterprise-grade software architecture, security-conscious operations, and scalable platform engineering. We apply this same discipline to every product and partnership.</p>
                            <p class="inner-p"><strong>Nearly three decades of experience</strong> guide how we deliver practical, high-impact software solutions for games and business.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="hero-hook">
            <div class="container text-center">
                <div class="boxed">
                    <h2>Build games. Host worlds. Ship faster.</h2>
                    <p class="inner-p">Real projects in server hosting, game development, modding, and web applications with room to grow your own side projects.</p>
                    <a class="btn" href="joinus.php">Join the Team</a>
                </div>
            </div>
        </section>

        <?php include 'includes/footer.php'; ?>

        <script src="assets/js/jquery-1.12.3.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/jquery.magnific-popup.min.js"></script>
        <script src="assets/js/owl.carousel.min.js"></script>
        <script src="assets/js/script.js"></script>
    </body>
</html>
