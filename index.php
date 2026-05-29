<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/assets/images/RL-icon.png">

        <title>Runlevel Systems | Design Debug Deploy</title>
        <meta name="description" content="Runlevel Systems helps indie game developers with Unity support, multiplayer infrastructure, Atavism consulting, deployment, publishing, hosting, and technical services.">
        <meta name="keywords" content="indie game development, unity consulting, atavism support, multiplayer hosting, steam publishing, game deployment, linux game servers, online game infrastructure, runlevel systems">

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
        $page_description = 'Technical partner services for indie game developers.';
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
                padding: 28px;
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
                margin-bottom: 10px;
                font-size: 24px;
            }

            .feature-card p,
            .service-card p,
            .audience-card p,
            .product-card p,
            .timeline-step p {
                color: #334966;
                margin-bottom: 0;
                line-height: 1.7;
            }

            .service-card .learn-more {
                margin-top: 18px;
                display: inline-flex;
                align-items: center;
                padding: 9px 14px;
                border-radius: 6px;
                background: #0a84ff;
                color: #ffffff;
                border: 1px solid #0a84ff;
                text-decoration: none;
                font-size: 12px;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                font-weight: 700;
            }

            .service-card .learn-more:hover,
            .service-card .learn-more:focus {
                background: #08111f;
                border-color: #08111f;
                color: #ffc600;
                text-decoration: none;
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
            }
        </style>

        <main class="landing-page" aria-label="Runlevel Systems indie game developer landing page">
            <section class="landing-hero">
                <div class="container">
                    <h1 class="hero-title">Build Your Game. We'll Help You Launch It.</h1>
                    <p class="hero-subtitle">From first prototype to live production servers, Runlevel Systems helps indie developers solve the technical challenges that stand between a great idea and a successful game.</p>
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
                    <p class="section-intro">Modern tools like Unity, Unreal Engine, AI coding assistants, and marketplace assets have made game development more accessible than ever.</p>
                    <p class="section-intro">But many developers eventually hit technical roadblocks involving multiplayer systems, networking, hosting, databases, publishing, deployment, infrastructure, version control, and live operations.</p>
                    <p class="section-intro">Runlevel Systems helps bridge that gap. You focus on creating your game. We help solve the technical challenges.</p>
                </div>
            </section>

            <section class="section-wrap alt">
                <div class="container">
                    <h2 class="section-title">DESIGN • DEBUG • DEPLOY</h2>
                    <div class="row">
                        <div class="col-md-4">
                            <article class="feature-card">
                                <div class="card-icon" aria-hidden="true">🧭</div>
                                <h3>DESIGN</h3>
                                <p>Plan your project with confidence. We help developers design scalable game architectures, multiplayer infrastructure, server layouts, deployment strategies, and technical workflows before expensive mistakes are made.</p>
                            </article>
                        </div>
                        <div class="col-md-4">
                            <article class="feature-card">
                                <div class="card-icon" aria-hidden="true">🛠️</div>
                                <h3>DEBUG</h3>
                                <p>Stuck on a technical problem? From Unity issues and Atavism configuration problems to server networking and deployment challenges, we help identify and solve blockers quickly.</p>
                            </article>
                        </div>
                        <div class="col-md-4">
                            <article class="feature-card">
                                <div class="card-icon" aria-hidden="true">🚀</div>
                                <h3>DEPLOY</h3>
                                <p>Launch with confidence. We help deploy multiplayer servers, websites, databases, cloud infrastructure, updates, and publishing pipelines so your game reaches players successfully.</p>
                            </article>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section-wrap">
                <div class="container">
                    <h2 class="section-title">How We Help</h2>
                    <div class="row">
                        <div class="col-sm-6 col-lg-4"><article class="service-card"><div class="card-icon">🧩</div><h3>Unity Development Support</h3><p>Hands-on troubleshooting, architecture guidance, and workflow support for Unity projects.</p><a class="learn-more" href="contact.php">Learn More</a></article></div>
                        <div class="col-sm-6 col-lg-4"><article class="service-card"><div class="card-icon">⚙️</div><h3>Atavism MMO Consulting</h3><p>Configuration help, backend integration support, and production readiness consulting for Atavism.</p><a class="learn-more" href="contact.php">Learn More</a></article></div>
                        <div class="col-sm-6 col-lg-4"><article class="service-card"><div class="card-icon">🌐</div><h3>Multiplayer Infrastructure</h3><p>Scalable networking and backend system planning for co-op, competitive, and MMO gameplay.</p><a class="learn-more" href="contact.php">Learn More</a></article></div>
                        <div class="col-sm-6 col-lg-4"><article class="service-card"><div class="card-icon">🖥️</div><h3>Dedicated Server Deployment</h3><p>Reliable deployment workflows for game servers across cloud and bare-metal environments.</p><a class="learn-more" href="contact.php">Learn More</a></article></div>
                        <div class="col-sm-6 col-lg-4"><article class="service-card"><div class="card-icon">🐧</div><h3>Linux Administration</h3><p>Server hardening, updates, monitoring, and operational tuning for game infrastructure.</p><a class="learn-more" href="contact.php">Learn More</a></article></div>
                        <div class="col-sm-6 col-lg-4"><article class="service-card"><div class="card-icon">🗄️</div><h3>Database Management</h3><p>Performance tuning, migration planning, and stability support for game and platform data.</p><a class="learn-more" href="contact.php">Learn More</a></article></div>
                        <div class="col-sm-6 col-lg-4"><article class="service-card"><div class="card-icon">🎮</div><h3>Steam Publishing Assistance</h3><p>Release preparation support, deployment alignment, and launch readiness for Steam publishing.</p><a class="learn-more" href="contact.php">Learn More</a></article></div>
                        <div class="col-sm-6 col-lg-4"><article class="service-card"><div class="card-icon">📱</div><h3>Google Play Publishing Assistance</h3><p>Technical prep, update strategy, and production support for Android game releases.</p><a class="learn-more" href="contact.php">Learn More</a></article></div>
                        <div class="col-sm-6 col-lg-4"><article class="service-card"><div class="card-icon">🍎</div><h3>Apple App Store Publishing Assistance</h3><p>Build validation, deployment review, and technical issue resolution for iOS submission.</p><a class="learn-more" href="contact.php">Learn More</a></article></div>
                        <div class="col-sm-6 col-lg-4"><article class="service-card"><div class="card-icon">📊</div><h3>Live Operations &amp; Monitoring</h3><p>Observability and support workflows to keep your game stable after launch.</p><a class="learn-more" href="contact.php">Learn More</a></article></div>
                    </div>
                </div>
            </section>

            <section class="section-wrap alt">
                <div class="container">
                    <h2 class="section-title">Who We Help</h2>
                    <div class="row">
                        <div class="col-md-4"><article class="audience-card"><div class="card-icon">🧠</div><h3>Solo Developers</h3><p>You have the vision but need help navigating the technical side of development.</p></article></div>
                        <div class="col-md-4"><article class="audience-card"><div class="card-icon">🤝</div><h3>Small Indie Studios</h3><p>Extend your team with experienced infrastructure and deployment support.</p></article></div>
                        <div class="col-md-4"><article class="audience-card"><div class="card-icon">🌍</div><h3>Multiplayer &amp; MMO Projects</h3><p>From server architecture to deployment and scaling, we help bring online worlds to life.</p></article></div>
                    </div>
                </div>
            </section>

            <section class="section-wrap">
                <div class="container">
                    <h2 class="section-title">Products Built By Developers</h2>
                    <div class="row">
                        <div class="col-md-4"><article class="product-card"><div class="card-icon">🛰️</div><h3>GameServers.World</h3><p>Managed game hosting, multiplayer infrastructure, deployment assistance, and server management tools for game developers and communities.</p></article></div>
                        <div class="col-md-4"><article class="product-card"><div class="card-icon">🧰</div><h3>GSP Panel</h3><p>Powerful game server management software developed for hosting providers and game developers.</p></article></div>
                        <div class="col-md-4"><article class="product-card"><div class="card-icon">✨</div><h3>Future Runlevel Systems Products</h3><p>Developer tools, automation systems, deployment platforms, AI assistants, and studio management solutions.</p></article></div>
                    </div>
                </div>
            </section>

            <section class="section-wrap alt">
                <div class="container">
                    <h2 class="section-title">From Idea to Launch</h2>
                    <p class="section-intro">DESIGN • DEBUG • DEPLOY guides every stage of bringing your game to players.</p>
                    <div class="timeline">
                        <div class="timeline-step"><strong>Step 1: Build Your Idea</strong><p>Shape your core concept, gameplay loop, and player experience.</p></div>
                        <div class="timeline-step"><strong>Step 2: Design Your Systems</strong><p>Define multiplayer architecture, services, and technical workflows with confidence.</p></div>
                        <div class="timeline-step"><strong>Step 3: Debug Technical Challenges</strong><p>Identify blockers early and resolve engine, networking, and infrastructure issues.</p></div>
                        <div class="timeline-step"><strong>Step 4: Deploy Infrastructure</strong><p>Launch dedicated servers, backend services, databases, and release pipelines.</p></div>
                        <div class="timeline-step"><strong>Step 5: Launch To Players</strong><p>Go live with monitoring, support, and live operations that keep your game running.</p></div>
                    </div>
                </div>
            </section>

            <section class="section-wrap" style="padding-top: 0;">
                <div class="container">
                    <div class="cta-banner">
                        <h2>Don't Let Technical Challenges Stop Your Game</h2>
                        <p>Whether you're building your first indie title, launching a multiplayer game, or creating the next online world, Runlevel Systems can help you move faster and launch with confidence.</p>
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
              "description": "Technical partner services for indie game developers building multiplayer games and online infrastructure.",
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
