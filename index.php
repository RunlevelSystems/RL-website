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
            <h1 class="home-title">Commercial Infrastructure Software <span class="home-title-accent">and Hosting Platforms</span></h1>
            <p class="home-lead">Runlevel Systems is a technology and infrastructure software company that builds hosting platforms, backend systems, multiplayer infrastructure, deployment automation, and custom Linux-based software ecosystems.</p>
            <p class="home-summary">We design and operate production-grade systems for organizations that need reliable platform tooling, automated operations, and scalable service architecture.</p>
            <div class="home-actions">
              <a class="home-btn home-btn-primary" href="projects.php">View Platforms</a>
              <a class="home-btn home-btn-ghost" href="contact.php">Request Consultation</a>
            </div>
          </div>
        </div>

        <div class="home-block" aria-label="Core capabilities">
          <div class="home-inner">
            <p class="home-label">Core Capabilities</p>
            <div class="home-grid">
              <article class="home-card">
                <h3>Infrastructure Platforms</h3>
                <p>We build scalable backend management platforms for hosting and online services, with a focus on reliability, service lifecycle control, and long-term maintainability.</p>
              </article>
              <article class="home-card home-highlight">
                <h3>Game Server Technology</h3>
                <p>Our GameServer Panel platform is commercial-grade infrastructure software for game server management, deployment automation, and orchestration across distributed multiplayer hosting environments.</p>
              </article>
              <article class="home-card">
                <h3>Automation and Deployment</h3>
                <p>We implement automated provisioning, remote management, update control, monitoring pipelines, and orchestration tooling that reduces operational overhead at scale.</p>
              </article>
              <article class="home-card">
                <h3>Software Engineering</h3>
                <p>We deliver custom backend engineering, Linux infrastructure services, API systems, and platform integrations tailored to customer environments and business workflows.</p>
              </article>
              <article class="home-card">
                <h3>Network and Hosting Solutions</h3>
                <p>Our architecture work spans virtualization, remote node management, infrastructure monitoring, and scalable operations for regional and multi-location hosting platforms.</p>
              </article>
            </div>
          </div>
        </div>

        <div class="home-block" aria-label="GameServer Panel platform">
          <div class="home-inner">
            <p class="home-label">Platform Spotlight</p>
            <h2 class="home-title" style="font-size: clamp(24px, 3.4vw, 34px);">GameServer Panel by Runlevel Systems</h2>
            <p class="home-summary">GameServer Panel is positioned as a commercial hosting platform and infrastructure management system for multiplayer service operators. It provides centralized control, deployment orchestration, node-level lifecycle operations, and automation tooling for Linux-based hosting environments.</p>
            <hr class="home-divider">
            <ul class="home-list">
              <li>Centralized game server management across nodes and infrastructure locations.</li>
              <li>Automated provisioning and deployment workflows for service consistency.</li>
              <li>Remote orchestration for node operations, updates, and service rollouts.</li>
              <li>Integrated monitoring and operational controls for production hosting.</li>
              <li>Extensible platform architecture for custom integrations and backend tooling.</li>
            </ul>
          </div>
        </div>

        <div class="home-block" aria-label="Who we serve">
          <div class="home-inner">
            <p class="home-label">Who We Serve</p>
            <div class="home-tag-grid">
              <div class="home-tag">Hosting Providers</div>
              <div class="home-tag">Online Gaming Communities</div>
              <div class="home-tag">Infrastructure Operators</div>
              <div class="home-tag">Developers and Product Teams</div>
              <div class="home-tag">Multiplayer Platform Operators</div>
              <div class="home-tag">Businesses Requiring Scalable Backend Systems</div>
            </div>
          </div>
        </div>

        <div class="home-block" aria-label="Core technologies">
          <div class="home-inner">
            <p class="home-label">Core Technologies</p>
            <div class="home-tech">
              <div class="home-tech-item"><strong>Linux Infrastructure</strong><span>Production-first Linux environments for stable and predictable operations.</span></div>
              <div class="home-tech-item"><strong>Virtualization</strong><span>Resource-isolated service layers for efficient multi-tenant hosting.</span></div>
              <div class="home-tech-item"><strong>Distributed Systems</strong><span>Node-aware design for resilient, horizontally scalable services.</span></div>
              <div class="home-tech-item"><strong>Backend Automation</strong><span>Automated workflows for provisioning, updates, and operational recovery.</span></div>
              <div class="home-tech-item"><strong>Deployment Architecture</strong><span>Repeatable orchestration patterns for fast, controlled releases.</span></div>
              <div class="home-tech-item"><strong>Multiplayer Hosting</strong><span>Infrastructure and control tooling for online game server environments.</span></div>
            </div>
          </div>
        </div>

        <div class="home-block" aria-label="Call to action">
          <div class="home-inner" style="text-align: center;">
            <h2 style="margin:0 0 10px; color:#f4f9ff; font-size:clamp(22px,3vw,30px);">Infrastructure Software Built for Real Operations</h2>
            <p class="home-summary" style="margin:0 auto 16px; max-width:760px;">If you need hosting technology, backend automation, or a custom platform tailored to your environment, Runlevel Systems can design and deliver it.</p>
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
