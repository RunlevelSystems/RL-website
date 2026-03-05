<?php
/**
 * Fleet Status Display
 * 
 * Shows a public-facing overview of all servers in the fleet.
 * Deployed on core.iaregamer.com and core-dr.iaregamer.com
 * 
 * Reads from the peer_status MySQL database.
 */

require __DIR__ . '/config.php';

try {
    $pdo = db();
    
    // Get latest metrics for all servers
    $sql = "
        SELECT m.hostname, n.ip, m.ts, m.cpu_used_pct, 
               m.mem_used_bytes, m.mem_total_bytes, 
               m.disk_used_bytes, m.disk_total_bytes
        FROM metrics m 
        JOIN (
            SELECT hostname, MAX(ts) AS max_ts 
            FROM metrics 
            GROUP BY hostname
        ) x ON m.hostname = x.hostname AND m.ts = x.max_ts
        JOIN nodes n ON n.hostname = m.hostname 
        ORDER BY m.hostname
    ";
    $servers = $pdo->query($sql)->fetchAll();
    
} catch (Exception $e) {
    $servers = [];
    $errorMessage = 'Unable to connect to status database';
}

function getStatus($server) {
    if (!$server) return ['status' => 'unknown', 'color' => '#6b7280', 'label' => 'Unknown'];
    
    $minsSinceUpdate = round((time() - strtotime($server['ts'])) / 60);
    $memPct = $server['mem_total_bytes'] > 0 
        ? ($server['mem_used_bytes'] * 100.0) / $server['mem_total_bytes'] 
        : 0;
    $diskPct = $server['disk_total_bytes'] > 0 
        ? ($server['disk_used_bytes'] * 100.0) / $server['disk_total_bytes'] 
        : 0;
    
    if ($minsSinceUpdate > 10) {
        return ['status' => 'offline', 'color' => '#ef4444', 'label' => 'Offline'];
    } elseif ($server['cpu_used_pct'] > 90 || $memPct > 95 || $diskPct > 95) {
        return ['status' => 'degraded', 'color' => '#f59e0b', 'label' => 'Degraded'];
    }
    return ['status' => 'healthy', 'color' => '#22c55e', 'label' => 'Operational'];
}

function formatBytes($bytes) {
    if ($bytes >= 1073741824) return round($bytes / 1073741824, 1) . ' GB';
    if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
    return round($bytes / 1024, 1) . ' KB';
}

// Calculate overall fleet status
$overallStatus = 'healthy';
$healthyCount = 0;
$totalCount = count($servers);
foreach ($servers as $srv) {
    $srvStatus = getStatus($srv);
    if ($srvStatus['status'] === 'healthy') $healthyCount++;
    elseif ($srvStatus['status'] === 'offline') $overallStatus = 'degraded';
}
if ($healthyCount === 0 && $totalCount > 0) $overallStatus = 'outage';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="60">
    <title>Fleet Status - iaregamer.com</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            color: #e5e7eb;
            padding: 40px 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .logo {
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 10px;
        }
        .overall-status {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 500;
            font-size: 1.1rem;
        }
        .status-healthy { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
        .status-degraded { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
        .status-outage { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
        
        .servers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .server-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 25px;
            backdrop-filter: blur(10px);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .server-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }
        .server-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .server-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: #fff;
        }
        .server-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .metrics-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 15px;
        }
        .metric-box {
            text-align: center;
        }
        .metric-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
        }
        .metric-label {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 4px;
        }
        .progress-bar {
            height: 4px;
            background: rgba(255,255,255,0.1);
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            border-radius: 2px;
        }
        .last-seen {
            color: #6b7280;
            font-size: 0.8rem;
            margin-top: 15px;
            text-align: right;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            color: #6b7280;
            font-size: 0.875rem;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🎮 iaregamer.com</div>
            <div class="overall-status status-<?php echo $overallStatus; ?>">
                <?php if ($overallStatus === 'healthy'): ?>
                    ✓ All Systems Operational
                <?php elseif ($overallStatus === 'degraded'): ?>
                    ⚠ Some Systems Degraded
                <?php else: ?>
                    ✕ Service Outage
                <?php endif; ?>
            </div>
            <p style="margin-top: 15px; color: #9ca3af;">
                <?php echo $healthyCount; ?> of <?php echo $totalCount; ?> servers operational
            </p>
        </div>

        <?php if (!empty($servers)): ?>
        <div class="servers-grid">
            <?php foreach ($servers as $srv): 
                $srvStatus = getStatus($srv);
                $memPct = $srv['mem_total_bytes'] > 0 
                    ? round(($srv['mem_used_bytes'] * 100.0) / $srv['mem_total_bytes'], 1) 
                    : 0;
                $diskPct = $srv['disk_total_bytes'] > 0 
                    ? round(($srv['disk_used_bytes'] * 100.0) / $srv['disk_total_bytes'], 1) 
                    : 0;
                $minsSinceUpdate = round((time() - strtotime($srv['ts'])) / 60);
                $lastSeen = $minsSinceUpdate < 1 ? 'Just now' : $minsSinceUpdate . ' min ago';
            ?>
            <div class="server-card">
                <div class="server-header">
                    <span class="server-name"><?php echo htmlspecialchars($srv['hostname']); ?></span>
                    <span class="server-status" style="background: <?php echo $srvStatus['color']; ?>20; color: <?php echo $srvStatus['color']; ?>;">
                        <?php echo $srvStatus['label']; ?>
                    </span>
                </div>
                
                <div class="metrics-row">
                    <div class="metric-box">
                        <div class="metric-value"><?php echo number_format($srv['cpu_used_pct'], 0); ?>%</div>
                        <div class="metric-label">CPU</div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo min(100, $srv['cpu_used_pct']); ?>%; background: <?php echo $srv['cpu_used_pct'] > 80 ? '#ef4444' : ($srv['cpu_used_pct'] > 50 ? '#f59e0b' : '#22c55e'); ?>;"></div>
                        </div>
                    </div>
                    <div class="metric-box">
                        <div class="metric-value"><?php echo number_format($memPct, 0); ?>%</div>
                        <div class="metric-label">Memory</div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo min(100, $memPct); ?>%; background: <?php echo $memPct > 80 ? '#ef4444' : ($memPct > 50 ? '#f59e0b' : '#22c55e'); ?>;"></div>
                        </div>
                    </div>
                    <div class="metric-box">
                        <div class="metric-value"><?php echo number_format($diskPct, 0); ?>%</div>
                        <div class="metric-label">Disk</div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo min(100, $diskPct); ?>%; background: <?php echo $diskPct > 80 ? '#ef4444' : ($diskPct > 50 ? '#f59e0b' : '#22c55e'); ?>;"></div>
                        </div>
                    </div>
                </div>
                
                <div class="last-seen">Updated: <?php echo $lastSeen; ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <p>No server data available</p>
            <?php if (isset($errorMessage)): ?>
            <p style="color: #ef4444; margin-top: 10px;"><?php echo htmlspecialchars($errorMessage); ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="footer">
            <p>Auto-refreshes every 60 seconds</p>
            <p style="margin-top: 5px;">Powered by World Domination Software</p>
        </div>
    </div>
</body>
</html>
