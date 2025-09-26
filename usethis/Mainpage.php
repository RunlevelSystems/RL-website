<!-- World Domination Software — Overview Block (High-Contrast, HTML-only) -->
<section class="wds-hero" aria-label="Company Overview">
  <style>
    .wds-hero { 
      /* Higher contrast for black page backgrounds */
      --wds-primary: #22c55e;   /* brighter green accent */
      --wds-bg-top: #0f1419;    /* lighter than pure black for separation */
      --wds-bg-btm: #141b22;
      --wds-text: #f1f5f9;      /* near-white */
      --wds-muted: #cbd5e1;     /* brighter muted text */
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

    <div>
      <div class="wds-cards">
        <article class="wds-card" aria-label="Indie Game Development">
          <h3>Indie Game Development</h3>
          <p>Prototype to launch: gameplay systems, tools, pipelines, and platform builds.</p>
        </article>
        <article class="wds-card" aria-label="Game Server Hosting">
          <h3>Game Server Hosting</h3>
          <p>Classic &amp; modern titles, automated updates, and resilient uptime via <a href="https://gameservers.world" target="_blank" rel="noopener">Gameservers.World</a>.</p>
        </article>
        <article class="wds-card" aria-label="Business Applications">
          <h3>Business Applications</h3>
          <p>APIs, dashboards, integrations, and internal tools—built with security and maintainability.</p>
        </article>
      </div>

      <div class="wds-divider"></div>
      <p class="wds-small">Prefer to talk scope first? We’ll propose the smallest thing that works—then earn the right to build more.</p>
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
