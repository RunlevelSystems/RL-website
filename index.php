<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/assets/images/RL-icon.png">

        <title>Runlevel Systems | Design Debug Deploy</title>
        <meta name="description" content="Runlevel Systems helps solo developers and small teams with practical support for websites, code, servers, publishing, and launch updates.">
        <meta name="keywords" content="game development support, solo developer help, small team technical support, game websites, online game servers, publishing support, website hosting, runlevel systems">

        <link href="assets/css/coreloop.css" rel="stylesheet">

        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <?php
        $current_page = 'index';
        $page_subtitle = 'Design • Debug • Deploy';
        $page_description = 'Practical technical support for indie game developers and small teams.';
        $page_title = 'Runlevel Systems';
        $page_title_thin = 'Design • Debug • Deploy';
        ?>

        <?php include 'includes/header.php'; ?>
        <?php include 'includes/navigation.php'; ?>

        <style>
            .landing-page {
                background: #f4f8fc;
                color: #0d1a2b;
            }

            .landing-page .section-wrap {
                padding: 72px 0;
                background: #ffffff;
            }

            .landing-page .section-wrap.alt {
                background: #f4f8fc;
            }

            .landing-page .section-title {
                color: #08111f;
                font-size: clamp(28px, 3.6vw, 42px);
                margin-bottom: 16px;
            }

            .landing-page .section-intro {
                color: #2c3c54;
                font-size: 18px;
                line-height: 1.7;
                max-width: 920px;
            }

            .landing-hero {
                padding: 90px 0;
                color: #ecf5ff;
                background: linear-gradient(120deg, rgba(7, 17, 31, 0.92), rgba(10, 132, 255, 0.7)), url('assets/images/RL-splash.png') center / cover no-repeat;
                border-bottom: 1px solid rgba(255, 255, 255, 0.18);
            }

            .hero-title {
                color: #ffffff;
                font-size: clamp(36px, 5vw, 58px);
                margin-bottom: 18px;
                line-height: 1.08;
            }

            .hero-subtitle {
                font-size: 20px;
                line-height: 1.7;
                color: #dcecff;
                max-width: 860px;
            }

            .tagline {
                display: inline-block;
                margin: 18px 0 0;
                letter-spacing: 0.22em;
                text-transform: uppercase;
                font-weight: 700;
                color: var(--core-gold);
                border: 1px solid rgba(255, 198, 0, 0.45);
                padding: 10px 14px;
                border-radius: 8px;
                background: rgba(8, 17, 31, 0.35);
            }

            .hero-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                margin-top: 28px;
            }

            .hero-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 12px 20px;
                border-radius: 6px;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                font-weight: 700;
                font-size: 12px;
                text-decoration: none;
                transition: all 0.2s ease;
            }

            .hero-btn.primary {
                background: var(--core-gold);
                color: #08111f;
                border: 1px solid var(--core-gold);
            }

            .hero-btn.primary:hover,
            .hero-btn.primary:focus {
                background: var(--core-cyan);
                border-color: var(--core-cyan);
                color: #08111f;
                text-decoration: none;
            }

            .hero-btn.secondary {
                background: rgba(255, 255, 255, 0.08);
                color: #f0f6ff;
                border: 1px solid rgba(255, 255, 255, 0.35);
            }

            .hero-btn.secondary:hover,
            .hero-btn.secondary:focus {
                background: rgba(54, 243, 255, 0.2);
                border-color: rgba(54, 243, 255, 0.65);
                color: #ffffff;
                text-decoration: none;
            }

            .feature-card,
            .service-card,
            .audience-card,
            .product-card {
                background: #ffffff;
                border: 1px solid rgba(10, 132, 255, 0.14);
                border-radius: 14px;
                padding: 22px;
                height: 100%;
                box-shadow: 0 14px 32px rgba(5, 18, 35, 0.08);
            }

            .feature-card .card-icon,
            .service-card .card-icon,
            .audience-card .card-icon,
            .product-card .card-icon {
                width: 54px;
                height: 54px;
                border-radius: 12px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: rgba(10, 132, 255, 0.1);
                color: var(--core-cobalt);
                font-size: 24px;
                margin-bottom: 14px;
            }

            .feature-card h3,
            .service-card h3,
            .audience-card h3,
            .product-card h3 {
                color: #08111f;
                margin-bottom: 8px;
                font-size: 22px;
            }

            .feature-card p,
            .service-card p,
            .audience-card p,
            .product-card p,
            .timeline-step p {
                color: #334966;
                margin-bottom: 0;
                line-height: 1.55;
            }

            .result-word {
                margin-top: 14px;
                margin-bottom: 0;
                color: #0a84ff !important;
                font-weight: 700;
                letter-spacing: 0.12em;
                text-transform: uppercase;
                font-size: 12px;
            }

            .service-grid .service-card h3 {
                font-size: 20px;
            }

            .timeline {
                position: relative;
                margin-top: 20px;
                padding-left: 28px;
                border-left: 2px solid rgba(10, 132, 255, 0.25);
            }

            .timeline-step {
                position: relative;
                margin-bottom: 26px;
                padding: 14px 18px 14px 16px;
                background: #ffffff;
                border: 1px solid rgba(10, 132, 255, 0.14);
                border-radius: 12px;
            }

            .timeline-step::before {
                content: '';
                position: absolute;
                left: -38px;
                top: 22px;
                width: 16px;
                height: 16px;
                border-radius: 50%;
                background: #0a84ff;
                box-shadow: 0 0 0 4px rgba(10, 132, 255, 0.16);
            }

            .timeline-step strong {
                display: block;
                color: #08111f;
                font-size: 20px;
                margin-bottom: 5px;
            }

            .cta-banner {
                background: linear-gradient(125deg, #08111f, #0a84ff);
                color: #eef7ff;
                text-align: center;
                border-radius: 18px;
                padding: 54px 28px;
            }

            .cta-banner h2 {
                color: #ffffff;
                font-size: clamp(30px, 4.2vw, 48px);
                margin-bottom: 15px;
            }

            .cta-banner p {
                max-width: 860px;
                margin: 0 auto;
                color: #d9ecff;
                font-size: 19px;
            }

            .cta-banner .tagline {
                margin-top: 22px;
            }

            .cta-actions {
                margin-top: 28px;
                display: flex;
                justify-content: center;
                flex-wrap: wrap;
                gap: 12px;
            }

            @media (max-width: 768px) {
                .landing-page .section-wrap {
                    padding: 54px 0;
                }

                .landing-hero {
                    padding: 64px 0;
                }

                .hero-subtitle {
                    font-size: 18px;
                }

                .tagline {
                    letter-spacing: 0.14em;
                }

                .feature-card,
                .service-card,
                .audience-card,
                .product-card {
                    padding: 18px;
                }

                .service-grid > div {
                    margin-bottom: 14px;
                }
            }
        </style>

        <main class="landing-page" aria-label="Runlevel Systems indie game developer landing page">
            <section class="landing-hero">
                <div class="container">
                    <h1 class="hero-title">Build Your Game. We'll Help You Launch It.</h1>
                    <p class="hero-subtitle">You have the idea. We help with the technical parts that can slow you down — websites, code, servers, publishing, updates, and getting your project in front of players.</p>
                    <p class="tagline">DESIGN • DEBUG • DEPLOY</p>
                    <div class="hero-actions">
                        <a class="hero-btn primary" href="contact.php">Schedule a Consultation</a>
                        <a class="hero-btn secondary" href="https://discord.gg/XPFnNdWGyW" target="_blank" rel="noopener noreferrer">Join Our Community</a>
                    </div>
                </div>
            </section>

            <section class="section-wrap">
                <div class="container">
                    <h2 class="section-title">More Than Hosting. A Technical Partner.</h2>
                    <p class="section-intro">Building a game is like building a house. The artwork and gameplay are what people see, but the wiring, plumbing, foundation, and delivery systems still have to work.</p>
                    <p class="section-intro">Runlevel Systems helps solo developers and small teams handle the technical pieces behind the scenes so they can keep building instead of getting stuck.</p>
                </div>
            </section>

            <section class="section-wrap alt">
                <div class="container">
                    <h2 class="section-title">How We Help You Move Forward</h2>
                    <div class="row">
                        <div class="col-md-4">
                            <article class="feature-card">
                                <div class="card-icon" aria-hidden="true">🧭</div>
                                <h3>Plan The Project</h3>
                                <p>Before you build too much, we help you choose a clear path. We can help organize your idea, plan your website, decide what servers you need, set up your workflow, and avoid expensive mistakes early.</p>
                                <p class="result-word">DESIGNED</p>
                            </article>
                        </div>
                        <div class="col-md-4">
                            <article class="feature-card">
                                <div class="card-icon" aria-hidden="true">🛠️</div>
                                <h3>Fix What Is Broken</h3>
                                <p>When your project stops working, we help find the problem. Whether it is code errors, broken builds, server issues, database problems, login trouble, or confusing setup steps, we help get things working again.</p>
                                <p class="result-word">DEBUGGED</p>
                            </article>
                        </div>
                        <div class="col-md-4">
                            <article class="feature-card">
                                <div class="card-icon" aria-hidden="true">🚀</div>
                                <h3>Get It Launched</h3>
                                <p>When it is time to go live, we help you publish and deploy. That can mean launching a website, preparing for Steam, Google Play, or Apple App Store, setting up a dedicated server, and getting your updates out smoothly.</p>
                                <p class="result-word">DEPLOYED</p>
                            </article>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section-wrap">
                <div class="container">
                    <h2 class="section-title">What We Can Help With</h2>
                    <div class="row service-grid">
                        <div class="col-sm-6 col-lg-4"><article class="service-card"><div class="card-icon">🌐</div><h3>Game Websites</h3><p>We can create landing pages, project websites, update pages, documentation, and community portals.</p></article></div>
                        <div class="col-sm-6 col-lg-4"><article class="service-card"><div class="card-icon">🧩</div><h3>Game Development Help</h3><p>We can help with broken code, confusing setup steps, project organization, and feature planning.</p></article></div>
                        <div class="col-sm-6 col-lg-4"><article class="service-card"><div class="card-icon">🖥️</div><h3>Online Game Servers</h3><p>We can help set up dedicated servers, multiplayer hosting, databases, updates, and backups.</p></article></div>
                        <div class="col-sm-6 col-lg-4"><article class="service-card"><div class="card-icon">🚀</div><h3>Publishing Help</h3><p>We can help prepare your project for Steam, Google Play, Apple App Store, or direct download.</p></article></div>
                        <div class="col-sm-6 col-lg-4"><article class="service-card"><div class="card-icon">🏠</div><h3>Website Hosting</h3><p>We can host and maintain websites, support portals, documentation, and small web apps.</p></article></div>
                        <div class="col-sm-6 col-lg-4"><article class="service-card"><div class="card-icon">🧯</div><h3>Project Rescue</h3><p>If your project is stuck, broken, messy, or abandoned, we can help sort it out and create a recovery plan.</p></article></div>
                        <div class="col-sm-6 col-lg-4"><article class="service-card"><div class="card-icon">💬</div><h3>Community Tools</h3><p>We can help connect your website, Discord, support system, updates, and player communication.</p></article></div>
                        <div class="col-sm-6 col-lg-4"><article class="service-card"><div class="card-icon">🔧</div><h3>Ongoing Support</h3><p>We can stay involved after launch to help with updates, fixes, server issues, and improvements.</p></article></div>
                    </div>
                </div>
            </section>

            <section class="section-wrap alt">
                <div class="container">
                    <h2 class="section-title">Who We Help</h2>
                    <div class="row">
                        <div class="col-md-4"><article class="audience-card"><div class="card-icon">🧠</div><h3>Solo Developers</h3><p>You have the idea and the motivation. We help with the technical work that is hard to do alone.</p></article></div>
                        <div class="col-md-4"><article class="audience-card"><div class="card-icon">🤝</div><h3>Small Teams</h3><p>You are building together but need someone who understands websites, servers, code, and deployment.</p></article></div>
                        <div class="col-md-4"><article class="audience-card"><div class="card-icon">🧯</div><h3>Creators With A Stuck Project</h3><p>Your project was moving forward, then something broke or became too complicated. We help get it moving again.</p></article></div>
                    </div>
                </div>
            </section>

            <section class="section-wrap">
                <div class="container">
                    <h2 class="section-title">Our Products</h2>
                    <div class="row">
                        <div class="col-md-4"><article class="product-card"><div class="card-icon">🛰️</div><h3>GameServers.World</h3><p>Hosting, deployment, and server management for online games and communities.</p></article></div>
                        <div class="col-md-4"><article class="product-card"><div class="card-icon">🧰</div><h3>GSP Panel</h3><p>Server management and automation tools for game hosting and online infrastructure.</p></article></div>
                        <div class="col-md-4"><article class="product-card"><div class="card-icon">✨</div><h3>Runlevel Systems Tools</h3><p>Future tools, templates, automation, and support systems for creators and small teams.</p></article></div>
                    </div>
                </div>
            </section>

            <section class="section-wrap" style="padding-top: 0;">
                <div class="container">
                    <div class="cta-banner">
                        <h2>Ready To Get Your Project Moving?</h2>
                        <p>Whether you need a website, help fixing your game, a server for players, or guidance getting published, Runlevel Systems can help you take the next step.</p>
                        <p class="tagline">DESIGN • DEBUG • DEPLOY</p>
                        <div class="cta-actions">
                            <a class="hero-btn primary" href="contact.php">Schedule a Consultation</a>
                            <a class="hero-btn secondary" href="contact.php">Contact Runlevel Systems</a>
                        </div>
                    </div>
                </div>
            </section>

            <script type="application/ld+json">
            {
              "@context": "https://schema.org",
              "@type": "Organization",
              "name": "Runlevel Systems",
              "url": "https://runlevel.systems",
              "slogan": "Design • Debug • Deploy",
              "description": "Technical support services for solo developers and small teams building and launching game projects.",
              "sameAs": [
                "https://gameservers.world",
                "https://github.com/GameServerPanel/GSP"
              ]
            }
            </script>
        </main>

        <?php include 'includes/footer.php'; ?>

        <script src="assets/js/jquery-1.12.3.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/jquery.magnific-popup.min.js"></script>
        <script src="assets/js/owl.carousel.min.js"></script>
        <script src="assets/js/script.js"></script>
    </body>
</html>
