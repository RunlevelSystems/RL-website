<?php
require __DIR__ . '/config.php'; $host = $_GET['host'] ?? ''; $range = $_GET['range'] ?? '60min';
$valid=['60min','24h','30d','6mo']; if(!in_array($range,$valid)) $range='60min'; ?>
<!doctype html><html><head><meta charset="utf-8"><title>Stats for <?= htmlspecialchars($host) ?></title>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script><script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
<style>body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;margin:20px}h1{margin-top:0}.nav a{margin-right:10px}canvas{max-width:100%}</style></head>
<body><h1>Stats: <?= htmlspecialchars($host) ?></h1><div class="nav">Range:
<?php foreach (['60min','24h','30d','6mo'] as $r): ?><a href="server.php?host=<?= urlencode($host) ?>&range=<?= $r ?>"><?= $r ?></a><?php endforeach; ?> | <a href="index.php">Back</a></div>
<p>Showing: <?= htmlspecialchars($range) ?></p><h3>CPU %</h3><canvas id="cpu"></canvas><h3>RAM %</h3><canvas id="ram"></canvas><h3>Disk %</h3><canvas id="disk"></canvas>
<script>
async function fetchData(){ const resp=await fetch('api.php?host=<?= urlencode($host) ?>&range=<?= urlencode($range) ?>'); return resp.json(); }
function makeLabels(d){ return d.map(r=>new Date(r.ts)); }
function pct(u,t){ return (!t||t<=0)?0:Math.round((u*100.0/t)*100)/100; }
function seriesPct(d,u,t){ return d.map(r=>pct(Number(r[u]),Number(r[t]))); }
function chart(id,labels,values){ const ctx=document.getElementById(id).getContext('2d'); return new Chart(ctx,{type:'line',data:{labels,datasets:[{label:id.toUpperCase(),data:values,fill:false}]},options:{responsive:true,scales:{x:{type:'time',time:{unit:'minute'}},y:{beginAtZero:true,max:100}}}}); }
(async()=>{ const d=await fetchData(); const labels=makeLabels(d); chart('cpu',labels,d.map(r=>Number(r.cpu_used_pct))); chart('ram',labels,seriesPct(d,'mem_used_bytes','mem_total_bytes')); chart('disk',labels,seriesPct(d,'disk_used_bytes','disk_total_bytes')); })();
</script></body></html>
