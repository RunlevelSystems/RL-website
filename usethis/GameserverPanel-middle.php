<?php
/**
 * GSP (GameServer Panel) — embed for PHP Everywhere (no shortcode)
 * Drop this entire snippet into your PHP Everywhere block on a Page.
 * Edit the config section below as needed.
 */

/* ── CONFIG ───────────────────────────────────────────────────────────────── */
$title        = 'GameServer Panel (GSP)';
$tagline      = 'Open-source control panel for commercial game-server hosting — a modern fork of Open Game Panel with multi-location support.';
$docs_url     = 'https://worlddomination.dev/docs/gsp';
$download_url = 'https://github.com/GameServerPanel/GSP';
$discord_url  = 'https://discord.gg/bYx6GY7DUY';
$email        = 'support@worlddomination.dev';

$features = [
  ['title'=>'Multi-location deployment','desc'=>'Assign a single game service to multiple locations at once (e.g., ATL, LA, KC) for true commercial hosting workflows.','status'=>'available'],
  ['title'=>'OGP lineage & compatibility','desc'=>'Based on Open Game Panel—keeping the spirit of OGP while modernizing for host providers.','status'=>'available'],
  ['title'=>'Per-game templates & sane defaults','desc'=>'Startup params, common config paths, and known-good defaults for classics like CS 1.6, Urban Terror, Arma.','status'=>'available'],
  ['title'=>'Post-install hooks (Git-backed)','desc'=>'Pull standardized post-install scripts and configs from private Git—run only when needed.','status'=>'in-progress'],
  ['title'=>'Workshop & mod mirroring','desc'=>'Mirror popular Steam Workshop items and common mod packs (e.g., AMX Mod X) to save bandwidth.','status'=>'in-progress'],
  ['title'=>'Backups & DR patterns','desc'=>'Manifest/diff-based backups; sync to DR nodes so only changed files move.','status'=>'in-progress'],
  ['title'=>'Commercial-ready pricing hooks','desc'=>'Support both slot-based and flat-rate pricing models; map templates to SKUs.','status'=>'planned'],
  ['title'=>'Role-based access & tenant safety','desc'=>'Separate customer and operator roles; restrict risky actions by default.','status'=>'planned'],
  ['title'=>'Health checks & auto-heal','desc'=>'Lightweight checks for process/port state with restart policies.','status'=>'planned'],
  ['title'=>'Docs-first UX','desc'=>'Inline help, tooltips, and deep links to your Knowledge Base.','status'=>'planned'],
];

/* ── HELPERS ──────────────────────────────────────────────────────────────── */
if (!function_exists('wds_e')) {
  function wds_e($s){ return esc_html($s); }
}
if (!function_exists('wds_u')) {
  function wds_u($s){ return esc_url($s); }
}

/* ── RENDER ───────────────────────────────────────────────────────────────── */
?>
<section class="wds-gsp">
  <style>
    .wds-gsp { --bg:#0b0b0c; --card:#141417; --text:#e7e7ea; --muted:#a7acb4; --green:#2dd774; --ring:#2a2f36; --blue:#7bd3ff; --yellow:#ffd566;
      margin: 2rem 0; padding: 1.25rem; background: var(--bg); color: var(--text); border:1px solid var(--ring); border-radius:14px }
    .wds-gsp h2 { margin:0 0 .25rem; font-size:1.6rem; line-height:1.2 }
    .wds-gsp p.lead { margin:.25rem 0 1rem; color: var(--muted) }
    .wds-gsp .cta { display:flex; gap:.5rem; flex-wrap:wrap; margin:.5rem 0 1rem }
    .wds-gsp .btn { display:inline-block; padding:.55rem .85rem; border-radius:10px; border:1px solid var(--ring); text-decoration:none; color:var(--text); background:linear-gradient(180deg,#191a1d,#121215) }
    .wds-gsp .btn.primary { border-color:#1f3a2a; box-shadow: inset 0 0 0 1px rgba(45,215,116,.25) }
    .wds-gsp .btn:hover { border-color:#3b4048 }
    .wds-gsp .grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap:.8rem }
    .wds-gsp .card { background:var(--card); border:1px solid var(--ring); border-radius:12px; padding:.9rem }
    .wds-gsp .card h3 { margin:.1rem 0 .4rem; font-size:1rem; display:flex; align-items:center; gap:.5rem }
    .wds-gsp .badge { padding:.18rem .5rem; border-radius:999px; font-size:.72rem; border:1px solid var(--ring); color:var(--muted) }
    .wds-gsp .badge.available { color:#c1f3d7; background:rgba(45,215,116,.10); border-color:rgba(45,215,116,.35) }
    .wds-gsp .badge.in-progress { color:#fff1cd; background:rgba(255,213,102,.10); border-color:rgba(255,213,102,.35) }
    .wds-gsp .badge.planned { color:#d8efff; background:rgba(123,211,255,.08); border-color:rgba(123,211,255,.35) }
    .wds-gsp .muted { color: var(--muted) }
    .wds-gsp .ask { margin-top:1rem; padding:.85rem; border-radius:10px; border:1px dashed var(--ring); background:#101013 }
    .wds-gsp .ask a { color:var(--blue); text-decoration:none }
    .wds-gsp .filters { display:flex; gap:.5rem; align-items:center; margin:.25rem 0 1rem; color:var(--muted); font-size:.9rem }
    .wds-gsp .filters input { vertical-align: middle }
  </style>

  <h2><?php echo wds_e($title); ?></h2>
  <p class="lead"><?php echo wds_e($tagline); ?></p>

  <div class="cta">
    <?php if ($docs_url): ?><a class="btn" href="<?php echo wds_u($docs_url); ?>">View Docs</a><?php endif; ?>
    <?php if ($download_url): ?><a class="btn primary" href="<?php echo wds_u($download_url); ?>">Download / Git</a><?php endif; ?>
    <?php if ($discord_url): ?><a class="btn" href="<?php echo wds_u($discord_url); ?>">Discord</a><?php endif; ?>
  </div>

  <div class="filters">
    <label><input type="checkbox" id="wds-hide-planned"> Hide planned</label>
    <label><input type="checkbox" id="wds-hide-inprogress"> Hide in-progress</label>
  </div>

  <div class="grid" id="wds-grid">
    <?php foreach ($features as $f): ?>
      <?php $status = preg_replace('/[^a-z\-]/','', $f['status']); ?>
      <div class="card" data-status="<?php echo wds_e($status); ?>">
        <h3>
          <?php echo wds_e($f['title']); ?>
          <span class="badge <?php echo wds_e($status); ?>">
            <?php echo wds_e(ucfirst(str_replace('-', ' ', $status))); ?>
          </span>
        </h3>
        <p class="muted"><?php echo wds_e($f['desc']); ?></p>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="ask">
    <strong>Questions or feature requests?</strong>
    <?php if ($email): ?>
      Email us at <a href="mailto:<?php echo wds_e($email); ?>"><?php echo wds_e($email); ?></a>.
    <?php endif; ?>
    <?php if ($discord_url): ?>
      Or hop into <a href="<?php echo wds_u($discord_url); ?>">Discord</a>.
    <?php endif; ?>
    You can also comment on our Q&amp;A posts (see <em>Support → GSP Q&amp;A</em>).
  </div>

  <script>
    (function(){
      const grid = document.getElementById('wds-grid');
      if(!grid) return;
      const hidePlanned = document.getElementById('wds-hide-planned');
      const hideInProg  = document.getElementById('wds-hide-inprogress');
      function apply(){
        const cards = grid.querySelectorAll('.card');
        cards.forEach(c=>{
          const s = c.getAttribute('data-status');
          let show = true;
          if(hidePlanned && hidePlanned.checked && s === 'planned') show = false;
          if(hideInProg  && hideInProg.checked  && s === 'in-progress') show = false;
          c.style.display = show ? '' : 'none';
        });
      }
      hidePlanned && hidePlanned.addEventListener('change', apply);
      hideInProg  && hideInProg.addEventListener('change', apply);
    })();
  </script>
</section>
