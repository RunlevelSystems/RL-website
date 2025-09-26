<?php
/* ==== EDIT THESE LINES ==== */
$project_name   = 'GameServer Panel (GSP)';
$upstream_name  = 'Open Game Panel (OGP)';
$license_note   = 'Both projects are open-source and released under the GNU GPL.';
$purpose        = 'Provide commercial hosting of game servers across multiple locations.';
$ogp_scope      = 'OGP was originally oriented to a single panel server and a single game server (often the same machine).';
$billing_change = 'We rebuilt the “Simple Billing” approach: billing now lives on a separate website, while the panel focuses on provisioning and operations.';
$thanks         = 'Huge thanks to the OGP community, especially Own3mall and DieFem.';
$links = [
  'Docs'     => '/gsp/docs',
  'Contact'  => '/contact',
  'Discord'  => 'https://discord.gg/bYx6GY7DUY',
];
/* ========================= */
?>
<section style="margin:16px 0;padding:16px;border:1px solid #ddd;border-radius:10px;background:#111;color:#e8e8e8;font:15px/1.6 system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif">
  <h2 style="margin:0 0 8px 0;font-size:22px"><?= htmlspecialchars($project_name) ?> — Overview</h2>

  <p style="margin:.4rem 0 0 0">
    This is a modernized version of <strong><?= htmlspecialchars($upstream_name) ?></strong> tailored for commercial providers.<br>
    <?= htmlspecialchars($license_note) ?>
  </p>

  <ul style="margin:.8rem 0;padding-left:18px">
    <li><?= htmlspecialchars($purpose) ?></li>
    <li><?= htmlspecialchars($ogp_scope) ?></li>
    <li><?= htmlspecialchars($billing_change) ?></li>
  </ul>

  <p style="margin:.6rem 0 0 0;opacity:.85"><?= htmlspecialchars($thanks) ?></p>

  <?php if (!empty($links)): ?>
    <p style="margin:.6rem 0 0 0">
      <?php foreach ($links as $label => $url): ?>
        <a href="<?= htmlspecialchars($url) ?>" style="display:inline-block;margin:.25rem .4rem .25rem 0;padding:.45rem .7rem;border:1px solid #333;border-radius:8px;background:#1a1a1a;color:#e8e8e8;text-decoration:none"><?= htmlspecialchars($label) ?></a>
      <?php endforeach; ?>
    </p>
  <?php endif; ?>
</section>
