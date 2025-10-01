<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>WDS</title>

        <!-- CSS -->

        <!-- google fonts -->
        <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,700' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Oswald:400,300,700' rel='stylesheet' type='text/css'>

        <!-- files -->
        <link href="assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="assets/css/magnific-popup.css" rel="stylesheet">
        <link href="assets/css/owl.carousel.css" rel="stylesheet">
        <link href="assets/css/owl.carousel.theme.min.css" rel="stylesheet">
        <link href="assets/css/ionicons.css" rel="stylesheet">
        <link href="assets/css/main.css" rel="stylesheet">
        <link href="assets/css/readability-improvements.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <?php 
        // Page-specific variables - customize these for each page
        $current_page = 'index';
        $page_subtitle = 'Design. Debug. Deploy.';
        $page_description = '';
        $page_title = '';
        $page_title_thin = '';
        ?>    <!-- Include Site Header -->
    <?php include 'includes/header.html'; ?>
        
    <!-- Include Navigation Header -->
    <?php include 'includes/navigation.php'; ?>

    <!-- World Domination Software — Overview Block (High-Contrast, HTML-only) -->
<section class="wds-hero" aria-label="Company Overview">
  <style>
    .wds-hero { 
      /* Higher contrast for black page backgrounds */
      --wds-primary: #B8621B;   /* brighter rust accent for better contrast */
      --wds-bg-top: #0f1419;    /* lighter than pure black for separation */
      --wds-bg-btm: #141b22;
      --wds-text: #E6D3B7;      /* brighter tan for better readability */
      --wds-muted: #C4A676;     /* much brighter muted text for better contrast */
      --wds-card: #0f1620;      /* cards stand out from section bg */
      --wds-border: #334155;    /* stronger border for definition */

      isolation: isolate;
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
      color: var(--wds-text);
      background: linear-gradient(180deg, var(--wds-bg-top), var(--wds-bg-btm));
      border: 1px solid rgba(255,255,255,.08);
      border-radius: 16px; padding: 36px; margin: 24px auto; max-width: 1100px;
      box-shadow: 0 16px 40px rgba(0,0,0,.55);
      overflow: hidden; position: relative;
    }
    .wds-hero:before {
      content: ""; position: absolute; inset: -25% -10% auto -10%; height: 220px;
      background: radial-gradient(60% 100% at 10% 50%, rgba(34,197,94,.35), transparent 60%);
      filter: blur(24px); pointer-events: none;
    }
    .wds-wrap { display: grid; grid-template-columns: 1.2fr 1fr; gap: 28px; align-items: start; }
    @media (max-width: 900px){ .wds-wrap { grid-template-columns: 1fr; } }
    .wds-eyebrow { color: var(--wds-muted); letter-spacing: .08em; text-transform: uppercase; font-size: 12px; margin-bottom: 10px; }
    /* EDIT THIS LINE if you want a different headline */
    .wds-title { font-size: clamp(30px, 4vw, 44px); line-height: 1.05; margin: 0 0 12px; }
    .wds-tagline { font-size: clamp(16px, 2.4vw, 19px); color: #e2e8f0; margin: 0 0 18px; }
    .wds-motto { font-size: 15px; color: var(--wds-muted); margin: 0 0 22px; }
    .wds-cta { display: flex; flex-wrap: wrap; gap: 12px; margin: 20px 0 26px; }
    .wds-btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 16px; border-radius: 999px; text-decoration: none; border: 1px solid var(--wds-border); font-weight: 700; }
    .wds-btn--primary { background: var(--wds-primary); color: #07210f; border-color: transparent; }
    .wds-btn--ghost { background: rgba(255,255,255,.02); color: #e8fff3; }
    .wds-btn:hover { filter: brightness(1.05); transform: translateY(-1px); transition: .15s ease; }
    .wds-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
    @media (max-width: 900px){ .wds-cards { grid-template-columns: 1fr; } }
    .wds-card { background: linear-gradient(180deg, var(--wds-card), #111a26); border: 1px solid var(--wds-border); border-radius: 14px; padding: 16px; box-shadow: inset 0 1px 0 rgba(255,255,255,.03); }
    .wds-card h3 { font-size: 16px; margin: 0 0 8px; }
    .wds-card p { font-size: 14px; color: #e5edf6; margin: 0; }
    .wds-bullets { display: grid; gap: 9px; margin-top: 12px; }
    .wds-bullet { display: grid; grid-template-columns: 22px 1fr; gap: 10px; align-items: start; font-size: 14px; color: #e2e8f0; }
    .wds-check { width: 18px; height: 18px; border-radius: 999px; background: rgba(34,197,94,.15); border: 1px solid rgba(34,197,94,.45); display: inline-grid; place-items: center; font-size: 12px; color: var(--wds-primary); }
    .wds-meta { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 12px; color: var(--wds-muted); font-size: 13px; }
    .wds-meta a { color: #a7f3d0; text-decoration: none; }
    .wds-meta a:hover { text-decoration: underline; }
    .wds-divider { height: 1px; background: var(--wds-border); margin: 20px 0; }
    .wds-small { font-size: 12px; color: var(--wds-muted); }
    .wds-hero:after { content: ""; position: absolute; inset: 0; background-image: linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px); background-size: 28px 28px; opacity: .08; pointer-events: none; }
  </style>

  <div class="wds-wrap">
    <div>
      <div class="wds-eyebrow">Engineering • Delivery • Uptime</div>
      <!-- NEW, neutral headline so your company name doesn't repeat -->
      <h1 class="wds-title">Build • Launch • Scale</h1>
      <p class="wds-tagline">Indie game dev • Game server hosting • Business applications</p>
      <p class="wds-motto">We build resilient software and scalable game infrastructure.</p>

      <div class="wds-cta">
        <a class="wds-btn wds-btn--primary" href="/contact">Request a consultation</a>
        <a class="wds-btn wds-btn--ghost" href="/portal">Client portal</a>
        <a class="wds-btn wds-btn--ghost" href="https://gameservers.world" target="_blank" rel="noopener">Game servers</a>
      </div>

      <div class="wds-divider"></div>

      <div class="wds-bullets" role="list">
        <div class="wds-bullet"><span class="wds-check">✓</span><span>Indie studio shipping performant games and tools.</span></div>
        <div class="wds-bullet"><span class="wds-check">✓</span><span>DevOps-minded hosting: monitoring, crash recovery, workshop updates, CI/CD.</span></div>
        <div class="wds-bullet"><span class="wds-check">✓</span><span>Custom business apps and integrations—scoped clearly, delivered predictably.</span></div>
        <div class="wds-bullet"><span class="wds-check">✓</span><span>Engagements: fixed-scope builds or flexible retainers.</span></div>
      </div>

      <div class="wds-meta">
        <span>📧 <a href="mailto:hello@worlddomination.dev">hello@worlddomination.dev</a></span>
        <span>🔒 Private side available for clients</span>
      </div>
    </div>
  </div>

  <!-- Service Cards Section -->
  <div class="wds-wrap" style="margin-top: 40px; grid-template-columns: 1fr;">
    <div>
      <div class="wds-cards">
        <article class="wds-card" aria-label="Game Server Hosting">
          <h3>Game Server Hosting</h3>
          <p>Global game server hosting across multiple world locations. We're the most affordable and reliable solution available. Experience premium hosting at <a href="http://gameservers.world" target="_blank" rel="noopener">gameservers.world</a>.</p>
        </article>
        <article class="wds-card" aria-label="Game Development">
          <h3>Game Development</h3>
          <p>Multiplayer and open-world game development. Check out our growing catalog on <a href="https://store.steampowered.com/search/?developer=WorldDominationSoftware" target="_blank" rel="noopener">Steam</a> and see what we're building next.</p>
        </article>
        <article class="wds-card" aria-label="Business Applications">
          <h3>Business Applications & Websites</h3>
          <p>Custom business applications and professional websites built with enterprise-grade security, scalability, and maintainability for companies of all sizes.</p>
        </article>
      </div>

      <div class="wds-divider"></div>
      <p class="wds-small">Prefer to talk scope first? We'll propose the smallest thing that works—then earn the right to build more.</p>
    </div>
  </div>

  <!-- Structured data for better SEO -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "World Domination Software",
    "url": "https://worlddomination.dev",
    "sameAs": ["https://gameservers.world"],
    "contactPoint": [{
      "@type": "ContactPoint",
      "contactType": "customer support",
      "email": "hello@worlddomination.dev"
    }]
  }
  </script>
</section>

    <!-- History -->
        <section id="history" class="history">
            <div class="container-fluid section-bg">
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
                        <div class="col-sm-10 col-sm-offset-1">
                            <p style="font-size: 18px; line-height: 1.8; color: #8B7355; margin-bottom: 30px;">
                                We are seasoned IT professionals with an unrelenting passion for development and innovation. Since 1996, we've been at the forefront of digital communities—from running bulletin board systems (BBS) and managing high-performance game servers to developing cutting-edge applications that push technological boundaries.
                            </p>
                            <p style="font-size: 18px; line-height: 1.8; color: #8B7355; margin-bottom: 30px;">
                                In our professional careers, we architect and deliver enterprise-grade solutions for Fortune 500 companies across diverse industries. Our expertise spans cloud infrastructure, scalable web applications, cybersecurity implementations, and mission-critical system integrations that serve millions of users worldwide.
                            </p>
                            <p style="font-size: 18px; line-height: 1.8; color: #8B7355;">
                                <strong style="color: #8B4513;">Nearly three decades of experience</strong> have taught us that the most groundbreaking innovations come from passionate collaboration. That's why we're building World Domination Software—to channel our corporate expertise and entrepreneurial spirit into creating the next generation of gaming experiences.
                            </p>
                        </div>
                        <div class="col-sm-12" style="text-align: center; margin: 40px 0;">
                            <div style="display: inline-flex; align-items: center; gap: 30px; flex-wrap: wrap; justify-content: center;">
                                <div style="text-align: center; color: #8B4513;">
                                    <i class="fas fa-calendar-alt" style="font-size: 48px; margin-bottom: 10px; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);"></i>
                                    <h4 style="color: #8B4513; margin: 5px 0; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">1996</h4>
                                    <p style="color: #D2B48C; font-size: 14px; font-weight: 500;">Started BBS</p>
                                </div>
                                <div style="color: #475569;">
                                    <i class="fas fa-arrow-right" style="font-size: 24px;"></i>
                                </div>
                                <div style="text-align: center; color: #8B4513;">
                                    <i class="fas fa-server" style="font-size: 48px; margin-bottom: 10px; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);"></i>
                                    <h4 style="color: #8B4513; margin: 5px 0; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">2000s</h4>
                                    <p style="color: #D2B48C; font-size: 14px; font-weight: 500;">Game Servers</p>
                                </div>
                                <div style="color: #475569;">
                                    <i class="fas fa-arrow-right" style="font-size: 24px;"></i>
                                </div>
                                <div style="text-align: center; color: #8B4513;">
                                    <i class="fas fa-building" style="font-size: 48px; margin-bottom: 10px; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);"></i>
                                    <h4 style="color: #8B4513; margin: 5px 0; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">2010s</h4>
                                    <p style="color: #D2B48C; font-size: 14px; font-weight: 500;">Enterprise</p>
                                </div>
                                <div style="color: #475569;">
                                    <i class="fas fa-arrow-right" style="font-size: 24px;"></i>
                                </div>
                                <div style="text-align: center; color: #8B4513;">
                                    <i class="fas fa-rocket" style="font-size: 48px; margin-bottom: 10px; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);"></i>
                                    <h4 style="color: #8B4513; margin: 5px 0; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">2025</h4>
                                    <p style="color: #D2B48C; font-size: 14px; font-weight: 500;">WDS Co-op</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    <!-- CTA: Minimal Hero -->
<section class="hero-hook">
  <style>
    .hero-hook {
      padding: 64px 16px; text-align: center; color: #8B7355; background: #1C1C1C;
      border-bottom: 1px solid #334155;
    }
    .hero-hook h1 { font-size: clamp(28px, 5vw, 46px); margin: 0 0 10px; color: #D2B48C; }
    .hero-hook p { margin: 0 auto 18px; max-width: 760px; color: #8B7355; font-size: 18px; }
    .hero-hook .cta { display: inline-flex; gap: 10px; }
    .hero-hook .btn {
      appearance: none; border-radius: 9999px; padding: 12px 20px; font-weight: 700; text-decoration: none;
      border: 1px solid #0000; display: inline-flex; align-items: center; justify-content: center;
    }
    .hero-hook .primary { background: #8B4513; color: #0f1419; }
    .hero-hook .secondary { background: #1C1C1C; color: #D2B48C; border-color: #475569; }
  </style>

  <h1>Build games. Host worlds. Share profits.</h1>
  <p>Real projects in Server Hosting, Game Dev, Modding, and Web Apps — using our tools and servers. <br>Launch your own side projects too (keep 100%).</p>
  <div class="cta">
    <a class="btn primary" href="joinus.php" data-cta="hero-apply">Apply Now</a>
    <a class="btn primary" href="joinus.php" data-cta="hero-details">How It Works</a>
  </div>
</section>


    <!-- Include Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
        <script src="assets/js/jquery-1.12.3.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/jquery.magnific-popup.min.js"></script>
        <script src="assets/js/owl.carousel.min.js"></script>
        <script src="assets/js/script.js"></script>

    </body>
</html>
