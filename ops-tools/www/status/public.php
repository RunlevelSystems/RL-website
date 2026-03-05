<?php
/**
 * Public Server Status Display
 * 
 * Shows a customer-facing view of server health without exposing
 * specific hardware details. Reads from the peer_status MySQL database.
 * 
 * Deploy to: /var/www/html/status/ on each gameserver
 * Configure: Update config.php with database credentials
 */

require __DIR__ . '/config.php';

try {
    $pdo = db();
    
    // Get this server's hostname (can be overridden via config)
    $thisHost = defined('SERVER_HOSTNAME') ? SERVER_HOSTNAME : gethostname();
    if (strpos($thisHost, '.') !== false) {
        $thisHost = explode('.', $thisHost)[0]; // Get short hostname
    }
    
    // Get latest metrics for this server
    $stmt = $pdo->prepare("
        SELECT m.hostname, n.ip, m.ts, m.cpu_used_pct, 
               m.mem_used_bytes, m.mem_total_bytes, 
               m.disk_used_bytes, m.disk_total_bytes
        FROM metrics m 
        JOIN nodes n ON n.hostname = m.hostname 
        WHERE m.hostname = :host
        ORDER BY m.ts DESC 
        LIMIT 1
    ");
    $stmt->execute([':host' => $thisHost]);
    $server = $stmt->fetch();
    
    // Calculate percentages
    $memPct = 0;
    $diskPct = 0;
    $lastSeen = 'Unknown';
    $status = 'unknown';
    
    if ($server) {
        $memPct = $server['mem_total_bytes'] > 0 
            ? round(($server['mem_used_bytes'] * 100.0) / $server['mem_total_bytes'], 1) 
            : 0;
        $diskPct = $server['disk_total_bytes'] > 0 
            ? round(($server['disk_used_bytes'] * 100.0) / $server['disk_total_bytes'], 1) 
            : 0;
        
        $lastSeenTime = strtotime($server['ts']);
        $minsSinceUpdate = round((time() - $lastSeenTime) / 60);
        $lastSeen = $minsSinceUpdate < 1 ? 'Just now' : $minsSinceUpdate . ' min ago';
        
        // Determine status based on last update and resource usage
        if ($minsSinceUpdate > 10) {
            $status = 'offline';
        } elseif ($server['cpu_used_pct'] > 90 || $memPct > 95 || $diskPct > 95) {
            $status = 'degraded';
        } else {
            $status = 'healthy';
        }
    }
    
} catch (Exception $e) {
    $status = 'error';
    $errorMessage = 'Unable to retrieve server status';
}

// Status display configuration
$statusConfig = [
    'healthy' => ['color' => '#22c55e', 'label' => 'Operational', 'icon' => '✓'],
    'degraded' => ['color' => '#f59e0b', 'label' => 'Degraded', 'icon' => '⚠'],
    'offline' => ['color' => '#ef4444', 'label' => 'Offline', 'icon' => '✕'],
    'unknown' => ['color' => '#6b7280', 'label' => 'Unknown', 'icon' => '?'],
    'error' => ['color' => '#ef4444', 'label' => 'Error', 'icon' => '!']
];

$currentStatus = $statusConfig[$status] ?? $statusConfig['unknown'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="60">
    <title>Server Status - <?php echo htmlspecialchars($thisHost); ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e5e7eb;
        }
        .container {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            width: 90%;
            backdrop-filter: blur(10px);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .server-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: #fff;
            margin-bottom: 10px;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 500;
            font-size: 1rem;
        }
        .status-icon {
            font-size: 1.2rem;
        }
        .metrics {
            display: grid;
            gap: 20px;
            margin-top: 30px;
        }
        .metric {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            padding: 20px;
        }
        .metric-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .metric-label {
            color: #9ca3af;
            font-size: 0.875rem;
        }
        .metric-value {
            color: #fff;
            font-weight: 600;
        }
        .progress-bar {
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        .progress-low { background: #22c55e; }
        .progress-medium { background: #f59e0b; }
        .progress-high { background: #ef4444; }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6b7280;
            font-size: 0.875rem;
        }
        .last-update {
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="server-name"><?php echo htmlspecialchars($thisHost); ?></div>
            <div class="status-badge" style="background: <?php echo $currentStatus['color']; ?>20; color: <?php echo $currentStatus['color']; ?>;">
                <span class="status-icon"><?php echo $currentStatus['icon']; ?></span>
                <span><?php echo $currentStatus['label']; ?></span>
            </div>
        </div>
        
        <?php if ($status !== 'error' && $server): ?>
        <div class="metrics">
            <div class="metric">
                <div class="metric-header">
                    <span class="metric-label">CPU Usage</span>
                    <span class="metric-value"><?php echo number_format($server['cpu_used_pct'], 1); ?>%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill <?php echo $server['cpu_used_pct'] > 80 ? 'progress-high' : ($server['cpu_used_pct'] > 50 ? 'progress-medium' : 'progress-low'); ?>" 
                         style="width: <?php echo min(100, $server['cpu_used_pct']); ?>%"></div>
                </div>
            </div>
            
            <div class="metric">
                <div class="metric-header">
                    <span class="metric-label">Memory Usage</span>
                    <span class="metric-value"><?php echo number_format($memPct, 1); ?>%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill <?php echo $memPct > 80 ? 'progress-high' : ($memPct > 50 ? 'progress-medium' : 'progress-low'); ?>" 
                         style="width: <?php echo min(100, $memPct); ?>%"></div>
                </div>
            </div>
            
            <div class="metric">
                <div class="metric-header">
                    <span class="metric-label">Disk Usage</span>
                    <span class="metric-value"><?php echo number_format($diskPct, 1); ?>%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill <?php echo $diskPct > 80 ? 'progress-high' : ($diskPct > 50 ? 'progress-medium' : 'progress-low'); ?>" 
                         style="width: <?php echo min(100, $diskPct); ?>%"></div>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <span class="last-update">Last updated: <?php echo $lastSeen; ?></span>
        </div>
        <?php else: ?>
        <div class="footer">
            <span style="color: #ef4444;"><?php echo $errorMessage ?? 'No data available'; ?></span>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
