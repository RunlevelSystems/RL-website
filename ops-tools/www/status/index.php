// Developed by World Domination Software LLC
<?php
require __DIR__ . '/config.php'; $pdo = db();
$sql = "SELECT m.hostname, n.ip, m.ts, m.cpu_used_pct, m.mem_used_bytes, m.mem_total_bytes, m.disk_used_bytes, m.disk_total_bytes
        FROM metrics m JOIN ( SELECT hostname, MAX(ts) AS max_ts FROM metrics GROUP BY hostname ) x
        ON m.hostname = x.hostname AND m.ts = x.max_ts JOIN nodes n ON n.hostname = m.hostname ORDER BY m.hostname";
$rows = $pdo->query($sql)->fetchAll();
function pct($used, $total) { if ($total <= 0) return 0; return round(($used*100.0)/$total, 2); }
?><!doctype html><html><head><meta charset="utf-8"><title>Server Status - core.iaregamer.com</title>
<style>body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;margin:20px}h1{margin-top:0}table{border-collapse:collapse;width:100%}th,td{padding:8px 10px;border-bottom:1px solid #ddd;text-align:left}.badge{padding:2px 6px;border-radius:6px;background:#f0f0f0}.progress{height:10px;background:#eee;border-radius:6px;overflow:hidden}.progress>span{display:block;height:100%;background:#4a90e2}small{color:#666}</style></head>
<body><h1>Server Status</h1><p>Last heartbeat from each machine. Click a hostname to see charts (60min / 24h / 30d / 6mo).</p>
<table><thead><tr><th>Hostname</th><th>IP</th><th>Last Seen</th><th>CPU%</th><th>RAM%</th><th>Disk%</th><th>History</th></tr></thead><tbody>
<?php foreach ($rows as $r): $mins = max(0, round((time() - strtotime($r['ts']))/60)); $ram_pct = pct($r['mem_used_bytes'],$r['mem_total_bytes']); $disk_pct=pct($r['disk_used_bytes'],$r['disk_total_bytes']); ?>
<tr><td><?= htmlspecialchars($r['hostname']) ?></td><td><?= htmlspecialchars($r['ip']) ?></td><td><span class="badge"><?= $mins ?> min ago</span></td>
<td><div class="progress"><span style="width: <?= min(100,$r['cpu_used_pct']) ?>%"></span></div><small><?= number_format($r['cpu_used_pct'],2) ?>%</small></td>
<td><div class="progress"><span style="width: <?= min(100,$ram_pct) ?>%"></span></div><small><?= number_format($ram_pct,2) ?>%</small></td>
<td><div class="progress"><span style="width: <?= min(100,$disk_pct) ?>%"></span></div><small><?= number_format($disk_pct,2) ?>%</small></td>
<td><a href="server.php?host=<?= urlencode($r['hostname']) ?>&range=60min">View</a></td></tr>
<?php endforeach; ?>
</tbody></table></body></html>
