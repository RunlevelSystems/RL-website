<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/assets/images/icon.png">

        <title>Core Loop Development</title>

        <!-- CSS -->
        <link href="assets/css/coreloop.css" rel="stylesheet">

        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <?php 
        // Page-specific variables - customize these for each page
        $current_page = 'index';
        $page_subtitle = 'Design • Debug • Deploy';
        $page_description = '';
        $page_title = '';
        $page_title_thin = '';
        ?>
    <!-- Include Site Header -->
    <?php include 'includes/header.php'; ?>
        
    <!-- Include Navigation Header -->
    <?php include 'includes/navigation.php'; ?>

    <!-- Core Loop Development — Overview Block -->
<section class="wds-hero" aria-label="Company Overview">
  <style>
    .wds-hero {
      isolation: isolate;
      background: linear-gradient(180deg, var(--wds-bg-2), var(--wds-bg));
      border: 1px solid rgba(76, 201, 255,0.15);
      border-radius: 16px;
      padding: 48px 36px;
      margin: 24px auto;
      max-width: 1100px;
      overflow: hidden;
      position: relative;
    }
    .wds-hero::before {
      content: "";
      position: absolute;
      inset: 0;
      background: radial-gradient(ellipse at 20% 0%, rgba(76, 201, 255,0.07), transparent 60%);
      pointer-events: none;
    }
    .wds-hero::after {
      content: "";
      position: absolute;
      inset: 0;
      background-image:
        linear-gradient(rgba(76, 201, 255,0.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(76, 201, 255,0.04) 1px, transparent 1px);
      background-size: 32px 32px;
      pointer-events: none;
      opacity: 0.6;
    }
    .lx-wrap { display: grid; grid-template-columns: 1.2fr 1fr; gap: 28px; align-items: start; position: relative; z-index: 1; }
    @media (max-width: 900px){ .lx-wrap { grid-template-columns: 1fr; } }
    .lx-eyebrow { color: #00a8ff; letter-spacing: .15em; text-transform: uppercase; font-size: 11px; margin-bottom: 10px; font-family: 'Exo 2', sans-serif; font-weight: 600; }
    .lx-title { font-size: clamp(28px, 4vw, 44px); line-height: 1.05; margin: 0 0 12px; color: #e2e8f0; font-family: 'Exo 2', sans-serif; font-weight: 800; text-shadow: none !important; }
    .lx-title span { color: #00a8ff; }
    .lx-tagline { font-size: clamp(15px, 2.2vw, 18px); color: #94a3b8; margin: 0 0 18px; }
    .lx-motto { font-size: 14px; color: #64748b; margin: 0 0 22px; }
    .lx-cta { display: flex; flex-wrap: wrap; gap: 12px; margin: 20px 0 26px; }
    .lx-btn { display: inline-flex; align-items: center; gap: 8px; padding: 11px 20px; border-radius: 6px; text-decoration: none; font-weight: 700; font-family: 'Exo 2', sans-serif; font-size: 13px; letter-spacing: 0.08em; text-transform: uppercase; transition: all 0.2s ease; text-shadow: none !important; }
    .lx-btn--primary { background: #00a8ff; color: #08111f; border: 1px solid #00a8ff; }
    .lx-btn--primary:hover { background: #4cc9ff; color: #08111f; box-shadow: 0 0 16px rgba(76, 201, 255,0.3); }
    .lx-btn--ghost { background: transparent; color: #94a3b8; border: 1px solid rgba(76, 201, 255,0.25); }
    .lx-btn--ghost:hover { background: rgba(76, 201, 255,0.08); color: #00a8ff; border-color: rgba(76, 201, 255,0.5); }
    .lx-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
    @media (max-width: 900px){ .lx-cards { grid-template-columns: 1fr; } }
    .lx-card { background: rgba(76, 201, 255,0.04); border: 1px solid rgba(76, 201, 255,0.12); border-radius: 10px; padding: 18px; transition: border-color 0.2s ease; }
    .lx-card:hover { border-color: rgba(76, 201, 255,0.35); }
    .lx-card h3 { font-size: 15px; margin: 0 0 8px; color: #e2e8f0; font-family: 'Exo 2', sans-serif; text-shadow: none !important; }
    .lx-card p { font-size: 13px; color: #64748b; margin: 0; text-shadow: none !important; }
    .lx-card a { color: #00a8ff; text-decoration: none; }
    .lx-bullets { display: grid; gap: 9px; margin-top: 12px; }
    .lx-bullet { display: grid; grid-template-columns: 22px 1fr; gap: 10px; align-items: start; font-size: 14px; color: #94a3b8; text-shadow: none !important; }
    .lx-check { width: 18px; height: 18px; border-radius: 4px; background: rgba(76, 201, 255,0.1); border: 1px solid rgba(76, 201, 255,0.35); display: inline-grid; place-items: center; font-size: 11px; color: #00a8ff; }
    .lx-meta { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 12px; color: #64748b; font-size: 13px; }
    .lx-meta a { color: #00a8ff; text-decoration: none; }
    .lx-meta a:hover { text-decoration: underline; }
    .lx-divider { height: 1px; background: linear-gradient(90deg, transparent, rgba(76, 201, 255,0.3), transparent); margin: 20px 0; border: none; }
    .lx-small { font-size: 12px; color: #64748b; text-shadow: none !important; }
  </style>

  <div class="lx-wrap">
    <div>
      <div class="lx-eyebrow">Advanced Engineering Studio</div>
      <h1 class="lx-title">Design <span>•</span> Debug <span>•</span> Deploy</h1>
      <p class="lx-tagline">Game development • Multiplayer infrastructure • Business applications</p>
      <p class="lx-motto">We engineer resilient software and scalable game infrastructure.</p>

      <div class="lx-cta">
        <a class="lx-btn lx-btn--primary" href="contact.php">Request a consultation</a>
        <a class="lx-btn lx-btn--ghost" href="/portal">Client portal</a>
        <a class="lx-btn lx-btn--ghost" href="https://gameservers.world" target="_blank" rel="noopener">Game servers</a>
      </div>

      <hr class="lx-divider">

      <div class="lx-bullets" role="list">
        <div class="lx-bullet"><span class="lx-check">✓</span><span>Indie studio shipping performant games and tools.</span></div>
        <div class="lx-bullet"><span class="lx-check">✓</span><span>DevOps-grade hosting: monitoring, crash recovery, CI/CD pipelines.</span></div>
        <div class="lx-bullet"><span class="lx-check">✓</span><span>Custom business apps and integrations — scoped clearly, delivered predictably.</span></div>
        <div class="lx-bullet"><span class="lx-check">✓</span><span>Flexible engagements: fixed-scope builds or ongoing retainers.</span></div>
      </div>

      <div class="lx-meta">
        <span>⚡ <a href="mailto:hello@coreloop.dev">hello@coreloop.dev</a></span>
        <span>🔒 Private portal available for clients</span>
      </div>
    </div>
  </div>

  <!-- Service Cards Section -->
  <div style="margin-top: 40px; position: relative; z-index: 1;">
    <div class="lx-cards">
      <article class="lx-card" aria-label="Game Server Hosting">
        <h3>Game Server Hosting</h3>
        <p>Global game server hosting across multiple regions. Affordable, reliable, and optimized for performance. Experience it at <a href="http://gameservers.world" target="_blank" rel="noopener">gameservers.world</a>.</p>
      </article>
      <article class="lx-card" aria-label="Game Development">
        <h3>Game Development</h3>
        <p>Multiplayer and open-world game development. See our growing catalog on <a href="https://store.steampowered.com/curator/45805039/" target="_blank" rel="noopener">Steam Curator</a> and explore what we're building next.</p>
      </article>
      <article class="lx-card" aria-label="Business Applications">
        <h3>Business Applications &amp; Websites</h3>
        <p>Custom business applications and professional websites built with enterprise-grade security, scalability, and maintainability.</p>
      </article>
    </div>

    <hr class="lx-divider" style="margin-top: 28px;">
    <p class="lx-small">Prefer to talk scope first? We'll propose the smallest thing that works — then earn the right to build more.</p>
  </div>

  <!-- Structured data for better SEO -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "Core Loop Development",
    "url": "https://coreloop.dev",
    "sameAs": ["https://gameservers.world"],
    "contactPoint": [{
      "@type": "ContactPoint",
      "contactType": "customer support",
      "email": "hello@coreloop.dev"
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
                            <p style="font-size: 17px; line-height: 1.8; color: #94a3b8; margin-bottom: 30px;">
                                We are seasoned IT professionals with an unrelenting passion for development and innovation. Since 1996, we've been at the forefront of digital communities — from running bulletin board systems (BBS) and managing high-performance game servers to developing cutting-edge applications that push technological boundaries.
                            </p>
                            <p style="font-size: 17px; line-height: 1.8; color: #94a3b8; margin-bottom: 30px;">
                                In our professional careers, we architect and deliver enterprise-grade solutions for Fortune 500 companies across diverse industries. Our expertise spans cloud infrastructure, scalable web applications, cybersecurity implementations, and mission-critical system integrations that serve millions of users worldwide.
                            </p>
                            <p style="font-size: 17px; line-height: 1.8; color: #94a3b8;">
                                <strong style="color: #00a8ff;">Nearly three decades of experience</strong> have taught us that the most groundbreaking innovations come from passionate collaboration. That's why we founded Core Loop Development — to channel our corporate expertise and entrepreneurial drive into creating the next generation of gaming experiences and infrastructure.
                            </p>
                        </div>
                        <div class="col-sm-12" style="text-align: center; margin: 40px 0;">
                            <div style="display: inline-flex; align-items: center; gap: 30px; flex-wrap: wrap; justify-content: center;">
                                <div style="text-align: center; color: #00a8ff;">
                                    <i class="fas fa-calendar-alt" style="font-size: 40px; margin-bottom: 10px;"></i>
                                    <h4 style="color: #00a8ff; margin: 5px 0;">1996</h4>
                                    <p style="color: #64748b; font-size: 13px; font-weight: 500;">Started BBS</p>
                                </div>
                                <div style="color: #64748b;">
                                    <i class="fas fa-arrow-right" style="font-size: 20px;"></i>
                                </div>
                                <div style="text-align: center; color: #00a8ff;">
                                    <i class="fas fa-server" style="font-size: 40px; margin-bottom: 10px;"></i>
                                    <h4 style="color: #00a8ff; margin: 5px 0;">2000s</h4>
                                    <p style="color: #64748b; font-size: 13px; font-weight: 500;">Game Servers</p>
                                </div>
                                <div style="color: #64748b;">
                                    <i class="fas fa-arrow-right" style="font-size: 20px;"></i>
                                </div>
                                <div style="text-align: center; color: #00a8ff;">
                                    <i class="fas fa-building" style="font-size: 40px; margin-bottom: 10px;"></i>
                                    <h4 style="color: #00a8ff; margin: 5px 0;">2010s</h4>
                                    <p style="color: #64748b; font-size: 13px; font-weight: 500;">Enterprise</p>
                                </div>
                                <div style="color: #64748b;">
                                    <i class="fas fa-arrow-right" style="font-size: 20px;"></i>
                                </div>
                                <div style="text-align: center; color: #00a8ff;">
                                    <i class="fas fa-rocket" style="font-size: 40px; margin-bottom: 10px;"></i>
                                    <h4 style="color: #00a8ff; margin: 5px 0;">2025</h4>
                                    <p style="color: #64748b; font-size: 13px; font-weight: 500;">Core Loop</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    <!-- CTA: Join Us -->
<section class="hero-hook">
  <style>
    .hero-hook {
      padding: 64px 16px;
      text-align: center;
      background: linear-gradient(135deg, #0f1e2e 0%, #0a0f1a 100%);
      border-top: 1px solid rgba(76, 201, 255,0.15);
      border-bottom: 1px solid rgba(76, 201, 255,0.15);
      position: relative;
      overflow: hidden;
    }
    .hero-hook::before {
      content: "";
      position: absolute;
      inset: 0;
      background: radial-gradient(ellipse at 50% 0%, rgba(76, 201, 255,0.08), transparent 65%);
      pointer-events: none;
    }
    .hero-hook h1 { font-size: clamp(26px, 5vw, 44px); margin: 0 0 12px; color: #e2e8f0; font-family: 'Exo 2', sans-serif; font-weight: 800; text-shadow: none !important; position: relative; z-index: 1; }
    .hero-hook p { margin: 0 auto 20px; max-width: 760px; color: #94a3b8; font-size: 17px; position: relative; z-index: 1; text-shadow: none !important; }
    .hero-hook .lx-btn {
      display: inline-flex; align-items: center; padding: 12px 28px;
      border-radius: 6px; font-weight: 700; text-decoration: none;
      font-family: 'Exo 2', sans-serif; font-size: 13px; letter-spacing: 0.08em; text-transform: uppercase;
      background: #00a8ff; color: #08111f; border: 1px solid #00a8ff;
      transition: all 0.2s ease; position: relative; z-index: 1; text-shadow: none !important;
    }
    .hero-hook .lx-btn:hover { background: #4cc9ff; box-shadow: 0 0 20px rgba(76, 201, 255,0.3); }
  </style>

  <h1>Build games. Host worlds. Ship faster.</h1>
  <p>Real projects in Server Hosting, Game Dev, Modding, and Web Apps — using our tools and servers.<br>Launch your own side projects too (keep 100%).</p>
  <div>
    <a class="lx-btn" href="joinus.php" data-cta="hero-readmore">Join the Team</a>
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
