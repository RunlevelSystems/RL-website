<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/assets/images/RL-icon.png">

        <title>Runlevel Systems</title>

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

    <style>
      .home {
        padding: 28px 0 34px;
      }
      .home-block {
        background: linear-gradient(180deg, #07111f, #0b1630);
        border: 1px solid rgba(54, 243, 255, 0.16);
        border-radius: 14px;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.22);
        margin-bottom: 22px;
        position: relative;
        overflow: hidden;
      }
      .home-block::before {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 12% -5%, rgba(54, 243, 255, 0.1), transparent 45%);
        pointer-events: none;
      }
      .home-inner {
        position: relative;
        z-index: 1;
        padding: 36px;
      }
      .home-label {
        color: #36f3ff;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        font-size: 11px;
        margin: 0 0 10px;
        font-weight: 700;
      }
      .home-title {
        margin: 0 0 12px;
        color: #f4f9ff;
        font-size: clamp(30px, 4.3vw, 46px);
        line-height: 1.08;
      }
      .home-title-line {
        display: block;
      }
      .home-title-accent {
        color: #ffc600;
      }
      .home-lead {
        margin: 0;
        color: #d7e6f7;
        font-size: 19px;
        line-height: 1.6;
        max-width: 840px;
      }
      .home-summary {
        margin: 14px 0 0;
        color: #a8bedc;
        font-size: 16px;
        line-height: 1.75;
        max-width: 900px;
      }
      .home-actions {
        margin-top: 22px;
        display: flex;
        flex-wrap: wrap;
        gap: 11px;
      }
      .home-btn {
        display: inline-flex;
        align-items: center;
        padding: 11px 18px;
        border-radius: 6px;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-size: 12px;
        font-weight: 700;
        transition: all 0.2s ease;
      }
      .home-btn-primary {
        background: #ffc600;
        border: 1px solid #ffc600;
        color: #08111f;
      }
      .home-btn-primary:hover,
      .home-btn-primary:focus {
        background: #36f3ff;
        color: #08111f;
        box-shadow: 0 0 18px rgba(54, 243, 255, 0.26);
      }
      .home-btn-ghost {
        background: transparent;
        border: 1px solid rgba(54, 243, 255, 0.35);
        color: #d7e6f7;
      }
      .home-btn-ghost:hover,
      .home-btn-ghost:focus {
        color: #ffc600;
        border-color: rgba(54, 243, 255, 0.58);
        background: rgba(54, 243, 255, 0.1);
      }
      .home-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
      }
      .home-card {
        background: rgba(5, 15, 29, 0.65);
        border: 1px solid rgba(54, 243, 255, 0.2);
        border-radius: 10px;
        padding: 18px;
        animation: homeFadeUp 0.45s ease both;
      }
      .home-card h3 {
        color: #f4f9ff;
        margin: 0 0 8px;
        font-size: 19px;
      }
      .home-card p {
        margin: 0;
        color: #a8bedc;
        font-size: 15px;
        line-height: 1.72;
      }
      .home-highlight {
        border: 1px solid rgba(255, 198, 0, 0.35);
        box-shadow: inset 0 0 0 1px rgba(255, 198, 0, 0.09), 0 0 24px rgba(54, 243, 255, 0.14);
      }
      .home-list {
        margin: 0;
        padding: 0;
        list-style: none;
        display: grid;
        gap: 8px;
      }
      .home-list li {
        color: #c7d7e8;
        padding-left: 18px;
        position: relative;
        line-height: 1.68;
      }
      .home-list li::before {
        content: "";
        position: absolute;
        left: 0;
        top: 10px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #36f3ff;
        box-shadow: 0 0 8px rgba(54, 243, 255, 0.55);
      }
      .home-tag-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
      }
      .home-tag {
        border: 1px solid rgba(54, 243, 255, 0.26);
        background: rgba(4, 14, 26, 0.72);
        color: #dce9f8;
        border-radius: 8px;
        padding: 11px 10px;
        text-align: center;
        font-size: 14px;
        line-height: 1.4;
      }
      .home-tech {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
      }
      .home-tech-item {
        border: 1px solid rgba(54, 243, 255, 0.2);
        background: rgba(4, 14, 26, 0.72);
        border-radius: 9px;
        padding: 14px;
      }
      .home-tech-item strong {
        display: block;
        color: #f4f9ff;
        margin-bottom: 4px;
      }
      .home-tech-item span {
        color: #a8bedc;
        font-size: 14px;
        line-height: 1.55;
      }
      .home-divider {
        border: 0;
        height: 1px;
        margin: 18px 0;
        background: linear-gradient(90deg, transparent, rgba(54, 243, 255, 0.46), transparent);
      }
      @keyframes homeFadeUp {
        from {
          opacity: 0;
          transform: translateY(8px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }
      @media (max-width: 980px) {
        .home-grid,
        .home-tag-grid,
        .home-tech {
          grid-template-columns: 1fr;
        }
        .home-inner {
          padding: 24px;
        }
        .home-lead {
          font-size: 17px;
        }
      }
    </style>

    <main class="home" aria-label="Runlevel Systems homepage">
      <section class="container">
        <div class="home-block" aria-label="Hero">
          <div class="home-inner">
            <p class="home-label">Runlevel Systems</p>
            <h1 class="home-title"><span class="home-title-line">Software Development</span><span class="home-title-accent home-title-line">Platforms, Games, and Operations</span></h1>
            <p class="home-lead">RunLevel Systems develops software for multiplayer games, hosting platforms, backend systems, and Linux-based infrastructure.</p>
            <p class="home-summary">We build both customer-facing experiences and the systems that keep online platforms running reliably.</p>
            <div class="home-actions">
              <a class="home-btn home-btn-primary" href="projects.php">See What We've Built</a>
              <a class="home-btn home-btn-ghost" href="contact.php">Get in Touch</a>
            </div>
          </div>
        </div>

        <div class="home-block" aria-label="Core capabilities">
          <div class="home-inner">
            <p class="home-label">What We Build</p>
            <div class="home-grid">
              <article class="home-card home-highlight">
                <h3>Hosting Platforms</h3>
                <p>We develop control software for game hosting providers that need reliable provisioning, customer operations tooling, and day-to-day service management in one place.</p>
              </article>
              <article class="home-card">
                <h3>Multiplayer Game Development</h3>
                <p>We build online and cooperative game experiences with production-ready networking, live progression systems, and practical support tooling for ongoing releases.</p>
              </article>
              <article class="home-card">
                <h3>Backend Systems</h3>
                <p>We design APIs and service layers that connect game clients, admin interfaces, billing, and operations workflows without unnecessary complexity.</p>
              </article>
              <article class="home-card">
                <h3>Linux Infrastructure</h3>
                <p>We run Linux-first environments for hosting workloads, with practical standards around configuration, security hardening, and long-term maintainability.</p>
              </article>
              <article class="home-card">
                <h3>Deployment Automation</h3>
                <p>We automate rollout, server setup, service restarts, and update pipelines so teams can ship reliably while reducing manual operational overhead.</p>
              </article>
              <article class="home-card">
                <h3>Custom Software</h3>
                <p>We deliver targeted software for organizations that need tools built around their real workflow instead of forcing operations into generic products.</p>
              </article>
            </div>
          </div>
        </div>

        <div class="home-block" aria-label="GameServer Panel platform">
          <div class="home-inner">
            <p class="home-label">Platform Spotlight</p>
            <h2 class="home-title" style="font-size: clamp(24px, 3.4vw, 34px);">GameServer Panel</h2>
            <p class="home-summary">GameServer Panel is used by hosting operators who need to deploy, manage, and support customer game servers at scale. We built it because we operate hosting infrastructure ourselves and needed practical tooling for real operational demands. The platform powers Gameservers World and helps automate server deployment, updates, and daily management work.</p>
            <hr class="home-divider">
            <ul class="home-list">
              <li>Centralizes multi-node operations so teams can manage distributed game services from one control point.</li>
              <li>Automates server installs, updates, and routine maintenance to reduce repetitive manual work.</li>
              <li>Provides operational visibility through status monitoring and alerts for actionable incident response.</li>
              <li>Supports tenant-based environments for shared infrastructure with clear customer separation.</li>
              <li>Improves consistency across deployments, lowering operational drift across regions and nodes.</li>
            </ul>
          </div>
        </div>

        <div class="home-block" aria-label="Technical capabilities">
          <div class="home-inner">
            <p class="home-label">Technical Capabilities</p>
            <div class="home-grid">
              <article class="home-card">
                <h3>Linux Infrastructure</h3>
                <p>We design and manage Linux-based hosting environments for multiplayer platforms, backend services, and online applications. Our experience includes system configuration, automation, security hardening, monitoring, and long-term operational reliability.</p>
              </article>
              <article class="home-card">
                <h3>Deployment Automation</h3>
                <p>We build automated deployment systems that simplify server provisioning, software rollouts, updates, and service management across development and production environments.</p>
              </article>
              <article class="home-card">
                <h3>Remote Infrastructure Management</h3>
                <p>We develop centralized management systems for handling remote servers, distributed nodes, and multi-location infrastructure from a unified control platform.</p>
              </article>
              <article class="home-card">
                <h3>Monitoring &amp; Reliability</h3>
                <p>We implement monitoring, alerting, and operational tooling that help identify problems quickly and keep online systems stable and maintainable.</p>
              </article>
              <article class="home-card">
                <h3>Backend Development</h3>
                <p>We create APIs, backend services, account systems, and operational tools that support multiplayer games, hosting platforms, and custom business applications.</p>
              </article>
              <article class="home-card">
                <h3>Multiplayer Systems</h3>
                <p>We build networking and backend systems for multiplayer games including matchmaking, session management, persistent world services, and real-time synchronization.</p>
              </article>
            </div>
          </div>
        </div>

        <div class="home-block" aria-label="Games and interactive projects">
          <div class="home-inner">
            <p class="home-label">Games &amp; Interactive Projects</p>
            <div class="home-grid">
              <article class="home-card">
                <h3>Neverwards</h3>
                <p>Online cooperative adventure with persistent progression and long-term shared-world systems.</p>
              </article>
              <article class="home-card">
                <h3>Mystical Islands</h3>
                <p>Multiplayer fantasy exploration focused on cooperative play and persistent online sessions.</p>
              </article>
              <article class="home-card">
                <h3>Roadkill v2</h3>
                <p>Competitive vehicular combat built for responsive multiplayer gameplay and repeat sessions.</p>
              </article>
              <article class="home-card">
                <h3>Retro Space Blaster</h3>
                <p>Fast-paced mobile arcade action designed for short play loops and cross-platform delivery.</p>
              </article>
              <article class="home-card">
                <h3>Be Very Very Quiet</h3>
                <p>Mobile zombie survival experience centered on stealth, pressure, and extraction choices.</p>
              </article>
              <article class="home-card">
                <h3>Interactive Focus</h3>
                <p>Our game work spans online multiplayer, cooperative systems, persistent worlds, and mobile-first gameplay.</p>
              </article>
            </div>
          </div>
        </div>

        <div class="home-block" aria-label="Technical capabilities">
          <div class="home-inner">
            <p class="home-label">Technical Capabilities</p>
            <div class="home-tech">
              <div class="home-tech-item"><strong>Linux</strong><span>Production environments built and maintained on Linux infrastructure.</span></div>
              <div class="home-tech-item"><strong>Virtualization</strong><span>Isolated service workloads for stable and efficient infrastructure usage.</span></div>
              <div class="home-tech-item"><strong>Backend APIs</strong><span>Service interfaces that connect products, operations, and customer tooling.</span></div>
              <div class="home-tech-item"><strong>Deployment Automation</strong><span>Automated rollout and maintenance workflows for reliable release cycles.</span></div>
              <div class="home-tech-item"><strong>Multiplayer Networking</strong><span>Online session architecture designed for responsiveness and persistence.</span></div>
              <div class="home-tech-item"><strong>Monitoring Systems</strong><span>Operational visibility with alerting, diagnostics, and service health tracking.</span></div>
              <div class="home-tech-item"><strong>Scalable Hosting Operations</strong><span>Multi-node service operations designed for growth and long-term support.</span></div>
            </div>
          </div>
        </div>

        <div class="home-block" aria-label="Call to action">
          <div class="home-inner" style="text-align: center;">
            <h2 style="margin:0 0 10px; color:#f4f9ff; font-size:clamp(22px,3vw,30px);">Let's Build Something That Runs in the Real World</h2>
            <p class="home-summary" style="margin:0 auto 16px; max-width:760px;">If you need practical software for games, hosting operations, or backend systems, we can help you plan it, build it, and run it.</p>
            <a class="home-btn home-btn-primary" href="contact.php">Talk to Our Team</a>
          </div>
        </div>
      </section>

      <script type="application/ld+json">
      {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "Runlevel Systems",
        "url": "https://worlddomination.dev",
        "contactPoint": [{
          "@type": "ContactPoint",
          "contactType": "customer support",
          "url": "https://worlddomination.dev/contact.php"
        }]
      }
      </script>
    </main>


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
