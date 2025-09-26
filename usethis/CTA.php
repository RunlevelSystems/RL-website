<!-- CTA: Sticky Footer Banner -->
<div class="cta-sticky">
  <style>
    .cta-sticky { position: fixed; inset: auto 0 0 0; z-index: 9999; font-family: inherit; }
    .cta-sticky .bar {
      display: grid; gap: 10px; grid-template-columns: 1fr auto;
      align-items: center; padding: 12px 16px; background: #0f172a; color: #fff;
      border-top: 1px solid #1f2937; box-shadow: 0 -6px 20px rgba(0,0,0,.3);
    }
    .cta-sticky .msg { font-size: 15px; line-height: 1.3; }
    .cta-sticky .msg b { font-weight: 800; }
    .cta-sticky .actions { display: flex; gap: 8px; }
    .cta-sticky .btn {
      appearance: none; border: 1px solid transparent; border-radius: 9999px;
      padding: 10px 16px; font-weight: 700; cursor: pointer; text-decoration: none;
      display: inline-flex; align-items: center; justify-content: center;
    }
    .cta-sticky .primary { background: #22c55e; color: #0b141a; }
    .cta-sticky .ghost { background: transparent; color: #e5e7eb; border-color: #334155; }
    .cta-sticky .close { background: transparent; color: #94a3b8; border: 0; font-size: 18px; cursor: pointer; }
    @media (max-width: 640px) {
      .cta-sticky .bar { grid-template-columns: 1fr; }
      .cta-sticky .actions { justify-content: flex-start; }
    }
  </style>
  <div class="bar" role="region" aria-label="Join our co-op">
    <div class="msg">
      <b>Join the co-op:</b> build games, host servers, and share <b>50% of profits</b>. Side projects welcome — keep 100% of your own.
    </div>
    <div class="actions">
      <a class="btn primary" href="/apply" data-cta="sticky-apply">Apply Now</a>
      <a class="btn ghost" href="/coop-details" data-cta="sticky-learn">Learn More</a>
      <button class="close" aria-label="Close banner" onclick="this.closest('.cta-sticky').remove()">✕</button>
    </div>
  </div>
</div>
