<?php
/* ==== EDIT THESE LINES ==== */
$regions = ['Region A', 'Region B', 'Region C']; // add/remove regions here
$show_edge = true;                                // set to false if you don’t want an “Edge/Proxy” box
/* ========================= */
?>
<section style="margin:16px 0;padding:16px;border:1px solid #ddd;border-radius:10px;background:#111;color:#e8e8e8;font:15px/1.6 system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif">
  <h2 style="margin:0 0 6px 0;font-size:20px">Reference Architecture (Generic)</h2>
  <p style="margin:0 0 10px 0;opacity:.85">High-level flow: Panel/DB ⇄ DR, optional Edge/Proxy, Monitoring, Backups, and multiple Game Hosts in different regions.</p>

  <!-- Simple legend -->
  <p style="margin:0 0 10px 0">
    <span style="display:inline-block;border:1px solid #2a2a2a;border-radius:8px;background:#161616;padding:.35rem .55rem;margin:0 .3rem .3rem 0">Panel / DB</span>
    <span style="display:inline-block;border:1px solid #2a2a2a;border-radius:8px;background:#161616;padding:.35rem .55rem;margin:0 .3rem .3rem 0">DR</span>
    <span style="display:inline-block;border:1px solid #2a2a2a;border-radius:8px;background:#161616;padding:.35rem .55rem;margin:0 .3rem .3rem 0">Monitoring</span>
    <span style="display:inline-block;border:1px solid #2a2a2a;border-radius:8px;background:#161616;padding:.35rem .55rem;margin:0 .3rem .3rem 0">Backups / Object Storage</span>
    <span style="display:inline-block;border:1px solid #2a2a2a;border-radius:8px;background:#161616;padding:.35rem .55rem;margin:0 .3rem .3rem 0">Game Hosts (Regions)</span>
  </p>

  <!-- Very small, very simple SVG. Edit text labels above; positions are fixed for readability. -->
  <div style="border:1px solid #2a2a2a;border-radius:10px;background:#0f0f0f;padding:8px">
    <svg viewBox="0 0 900 500" width="100%" height="auto" role="img" aria-label="Generic architecture diagram" style="display:block">
      <defs>
        <marker id="arrow" markerWidth="10" markerHeight="7" refX="10" refY="3.5" orient="auto">
          <polygon points="0 0, 10 3.5, 0 7" fill="#6aa9ff"/>
        </marker>
        <style>
          .box{fill:#161616;stroke:#2a2a2a;stroke-width:1}
          .label{fill:#e8e8e8;font:14px sans-serif}
          .sublabel{fill:#bdbdbd;font:12px sans-serif}
          .link{stroke:#6aa9ff;stroke-width:2;marker-end:url(#arrow)}
          .linkdash{stroke:#6aa9ff;stroke-width:2;stroke-dasharray:5 4;marker-end:url(#arrow)}
        </style>
      </defs>

      <?php if ($show_edge): ?>
      <!-- Edge / Proxy -->
      <g transform="translate(20,20)">
        <rect class="box" width="220" height="60" rx="8"></rect>
        <text class="label" x="12" y="24">Edge / Reverse Proxy</text>
        <text class="sublabel" x="12" y="44">TLS, WAF / rate-limit</text>
      </g>
      <?php endif; ?>

      <!-- Panel / DB (Primary) -->
      <g transform="translate(260,20)">
        <rect class="box" width="260" height="80" rx="8"></rect>
        <text class="label" x="12" y="26">Panel / Database (Primary)</text>
        <text class="sublabel" x="12" y="48">UI/API, jobs, MySQL</text>
      </g>

      <!-- DR -->
      <g transform="translate(540,20)">
        <rect class="box" width="340" height="80" rx="8"></rect>
        <text class="label" x="12" y="26">DR (Replica)</text>
        <text class="sublabel" x="12" y="48">DB replication, failover target</text>
      </g>

      <!-- Monitoring -->
      <g transform="translate(20,120)">
        <rect class="box" width="220" height="70" rx="8"></rect>
        <text class="label" x="12" y="26">Monitoring</text>
        <text class="sublabel" x="12" y="48">Host & game process metrics</text>
      </g>

      <!-- Backups / Object Storage -->
      <g transform="translate(20,210)">
        <rect class="box" width="220" height="70" rx="8"></rect>
        <text class="label" x="12" y="26">Backups / Object Storage</text>
        <text class="sublabel" x="12" y="48">Snapshots & server data</text>
      </g>

      <!-- Game Hosts container -->
      <g transform="translate(260,130)">
        <rect class="box" width="620" height="250" rx="8"></rect>
        <text class="label" x="12" y="26">Game Hosts (Regions)</text>
        <text class="sublabel" x="12" y="48">Agents receive jobs & report status</text>

        <?php
        // Draw each region box in a row
        $x = 16; $y = 70; $w = 185; $h = 90; $gap = 20;
        foreach ($regions as $idx => $name):
        ?>
          <g transform="translate(<?= $x + ($idx * ($w + $gap)) ?>, <?= $y ?>)">
            <rect class="box" width="<?= $w ?>" height="<?= $h ?>" rx="8"></rect>
            <text class="label" x="10" y="24"><?= htmlspecialchars($name) ?></text>
            <text class="sublabel" x="10" y="44">GSP Agent</text>
            <text class="sublabel" x="10" y="62">Game services</text>
          </g>
        <?php endforeach; ?>
      </g>

      <!-- Lines (kept few and obvious) -->
      <?php if ($show_edge): ?>
        <line class="link" x1="240" y1="50" x2="260" y2="60"></line>  <!-- Edge -> Panel -->
      <?php endif; ?>
      <line class="link" x1="520" y1="60" x2="540" y2="60"></line>    <!-- Panel -> DR -->
      <line class="linkdash" x1="390" y1="100" x2="390" y2="130"></line> <!-- Panel -> Hosts -->
      <line class="linkdash" x1="120" y1="190" x2="260" y2="60"></line>  <!-- Monitoring -> Panel -->
      <line class="link" x1="120" y1="245" x2="260" y2="60"></line>     <!-- Backups -> Panel -->
    </svg>
  </div>

  <p style="margin:10px 0 0 0;opacity:.75;font-size:14px">
    Edit the region names at the top of this block. No real hostnames or IPs are displayed—this is a generic model for customers.
  </p>
</section>
