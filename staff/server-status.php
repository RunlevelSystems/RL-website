<?php
/**
 * Staff Server Status - Detailed Fleet Overview
 * 
 * Shows detailed server metrics including top processes for staff members.
 * Reads from the peer_status MySQL database on core.iaregamer.com
 */
session_start();
define('WDS_SYSTEM', true);

require_once '../includes/db-config.php';
requireAdminLogin();

$current_page = 'staff-status';
$header_class = 'projects-header inner-header';
$page_subtitle = 'Server Fleet Status';
$page_description = 'Real-time monitoring of all servers in the fleet with detailed metrics.';

// Database configuration for peer_status
// Uses the same credentials as panel DB but connects to peer_status database
$STATUS_DB_HOST = 'core.iaregamer.com';
$STATUS_DB_PORT = 3306;
$STATUS_DB_NAME = 'peer_status';
$STATUS_DB_USER = DB_USER;     // From db-config.php
$STATUS_DB_PASS = DB_PASS;     // From db-config.php

$servers = [];
$errorMessage = null;

try {
    $dsn = "mysql:host=$STATUS_DB_HOST;port=$STATUS_DB_PORT;dbname=$STATUS_DB_NAME;charset=utf8mb4";
    $pdo = new PDO($dsn, $STATUS_DB_USER, $STATUS_DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 5
    ]);
    
    // Get latest metrics for all servers with top processes
    $sql = "
        SELECT m.hostname, n.ip, n.first_seen, n.last_seen, m.ts, 
               m.cpu_used_pct, m.mem_used_bytes, m.mem_total_bytes, 
               m.disk_used_bytes, m.disk_total_bytes, m.top_procs
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
    $errorMessage = 'Unable to connect to peer_status database: ' . $e->getMessage();
}

function getStatusInfo($server) {
    if (!$server) return ['status' => 'unknown', 'color' => '#6b7280', 'label' => 'Unknown', 'bg' => 'rgba(107,114,128,0.2)'];
    
    $minsSinceUpdate = round((time() - strtotime($server['ts'])) / 60);
    $memPct = $server['mem_total_bytes'] > 0 
        ? ($server['mem_used_bytes'] * 100.0) / $server['mem_total_bytes'] 
        : 0;
    $diskPct = $server['disk_total_bytes'] > 0 
        ? ($server['disk_used_bytes'] * 100.0) / $server['disk_total_bytes'] 
        : 0;
    
    if ($minsSinceUpdate > 10) {
        return ['status' => 'offline', 'color' => '#ef4444', 'label' => 'Offline', 'bg' => 'rgba(239,68,68,0.2)'];
    } elseif ($server['cpu_used_pct'] > 90 || $memPct > 95 || $diskPct > 95) {
        return ['status' => 'degraded', 'color' => '#f59e0b', 'label' => 'Degraded', 'bg' => 'rgba(245,158,11,0.2)'];
    }
    return ['status' => 'healthy', 'color' => '#36f3ff', 'label' => 'Operational', 'bg' => 'rgba(54,243,255,0.2)'];
}

function formatBytes($bytes) {
    if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 1) . ' GB';
    if ($bytes >= 1048576) return number_format($bytes / 1048576, 1) . ' MB';
    if ($bytes >= 1024) return number_format($bytes / 1024, 1) . ' KB';
    return $bytes . ' B';
}

function timeAgo($datetime) {
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    return floor($diff / 86400) . ' days ago';
}

// Calculate fleet summary
$healthyCount = 0;
$degradedCount = 0;
$offlineCount = 0;
foreach ($servers as $srv) {
    $status = getStatusInfo($srv);
    if ($status['status'] === 'healthy') $healthyCount++;
    elseif ($status['status'] === 'degraded') $degradedCount++;
    else $offlineCount++;
}
$totalCount = count($servers);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/assets/images/icon.png">
    <meta http-equiv="refresh" content="60">
    <title>Core Loop | Server Fleet Status</title>
    <link href="../assets/css/coreloop.css" rel="stylesheet">
    <style>
        .staff-login .page-bgc {
            background-color: #071228 !important;
        }

        .status-card {
            background: #0d1a33;
            border: 1px solid rgba(54,243,255,0.25);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.18);
        }

        .status-card h3 {
            margin-top: 0;
            color: #ffd166 !important;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .summary-box {
            background: #0f2142;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            border: 1px solid rgba(54,243,255,0.25);
        }

        .summary-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #eaf3ff;
        }

        .summary-label {
            color: #a8bedc;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .server-row {
            background: #0f2142;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            border: 1px solid rgba(54,243,255,0.25);
        }

        .server-row:hover {
            background: #132b57;
        }

        .server-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .server-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: #eaf3ff;
        }

        .server-ip {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .metric-box {
            text-align: center;
        }

        .metric-value {
            font-size: 1.5rem;
            font-weight: 600;
            color: #eaf3ff;
        }

        .metric-label {
            color: #a8bedc;
            font-size: 0.8rem;
            margin-top: 4px;
        }

        .progress-bar {
            height: 6px;
            background: rgba(0,0,0,0.08);
            border-radius: 3px;
            margin-top: 8px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 3px;
        }

        .top-procs {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(54,243,255,0.2);
        }

        .top-procs h5 {
            color: #6b7280;
            font-size: 0.85rem;
            margin-bottom: 10px;
        }

        .proc-table {
            width: 100%;
            font-size: 0.85rem;
        }

        .proc-table th {
            color: #6b7280;
            font-weight: 500;
            padding: 5px 0;
            text-align: left;
        }

        .proc-table td {
            color: #a8bedc;
            padding: 4px 0;
        }

        .proc-table td:first-child {
            color: #eaf3ff;
        }

        .last-update {
            color: #6b7280;
            font-size: 0.8rem;
            text-align: right;
            margin-top: 10px;
        }

        .error-box {
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.4);
            border-radius: 10px;
            padding: 20px;
            color: #7f1d1d;
            text-align: center;
        }
        @media (max-width: 768px) {
            .summary-grid { grid-template-columns: repeat(2, 1fr); }
            .metrics-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navigation.php'; ?>
<section class="staff-login">
    <div class="container page-bgc">
        <div class="row">
            <div class="col-sm-12">
                <div class="title-box">
                    <p>Live Monitoring</p>
                    <h2 class="title mt0" style="color:#ffd166;">Server Fleet Status</h2>
                    <p style="color:#7894b9;">Data from <code>peer_status</code> database on core.iaregamer.com • Auto-refreshes every 60 seconds</p>
                </div>
            </div>
        </div>

        <?php if ($errorMessage): ?>
        <div class="row">
            <div class="col-sm-12">
                <div class="error-box">
                    <i class="ion-alert-circled" style="font-size: 2rem;"></i>
                    <p style="margin-top: 10px;"><?php echo htmlspecialchars($errorMessage); ?></p>
                    <p style="margin-top: 10px; color: #F87171;">Make sure the peer_status database exists and the report_server_status.sh script has been run on each server.</p>
                </div>
            </div>
        </div>
        <?php else: ?>

        <!-- Fleet Summary -->
        <div class="row">
            <div class="col-sm-12">
                <div class="summary-grid">
                    <div class="summary-box">
                        <div class="summary-value" style="color: var(--lx-text);"><?php echo $totalCount; ?></div>
                        <div class="summary-label">Total Servers</div>
                    </div>
                    <div class="summary-box">
                        <div class="summary-value" style="color: var(--lx-accent);"><?php echo $healthyCount; ?></div>
                        <div class="summary-label">Operational</div>
                    </div>
                    <div class="summary-box">
                        <div class="summary-value" style="color: #f59e0b;"><?php echo $degradedCount; ?></div>
                        <div class="summary-label">Degraded</div>
                    </div>
                    <div class="summary-box">
                        <div class="summary-value" style="color: #ef4444;"><?php echo $offlineCount; ?></div>
                        <div class="summary-label">Offline</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Server Details -->
        <div class="row">
            <div class="col-sm-12">
                <div class="status-card">
                    <h3><i class="ion-ios-pulse"></i> Server Details</h3>
                    
                    <?php if (empty($servers)): ?>
                    <p style="color: #7894b9; text-align: center; padding: 40px;">
                        No servers reporting. Run <code>report_server_status.sh</code> on each server to begin collecting metrics.
                    </p>
                    <?php else: ?>
                    
                    <?php foreach ($servers as $srv): 
                        $status = getStatusInfo($srv);
                        $memPct = $srv['mem_total_bytes'] > 0 
                            ? round(($srv['mem_used_bytes'] * 100.0) / $srv['mem_total_bytes'], 1) 
                            : 0;
                        $diskPct = $srv['disk_total_bytes'] > 0 
                            ? round(($srv['disk_used_bytes'] * 100.0) / $srv['disk_total_bytes'], 1) 
                            : 0;
                        $topProcs = json_decode($srv['top_procs'], true) ?: [];
                    ?>
                    <div class="server-row">
                        <div class="server-header">
                            <div>
                                <span class="server-name"><?php echo htmlspecialchars($srv['hostname']); ?></span>
                                <span class="server-ip">(<?php echo htmlspecialchars($srv['ip']); ?>)</span>
                            </div>
                            <span class="status-badge" style="background: <?php echo $status['bg']; ?>; color: <?php echo $status['color']; ?>;">
                                <?php echo $status['label']; ?>
                            </span>
                        </div>
                        
                        <div class="metrics-grid">
                            <div class="metric-box">
                                <div class="metric-value"><?php echo number_format($srv['cpu_used_pct'], 1); ?>%</div>
                                <div class="metric-label">CPU Usage</div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo min(100, $srv['cpu_used_pct']); ?>%; background: <?php echo $srv['cpu_used_pct'] > 80 ? '#ef4444' : ($srv['cpu_used_pct'] > 50 ? '#f59e0b' : '#36f3ff'); ?>;"></div>
                                </div>
                            </div>
                            <div class="metric-box">
                                <div class="metric-value"><?php echo number_format($memPct, 1); ?>%</div>
                                <div class="metric-label">Memory (<?php echo formatBytes($srv['mem_used_bytes']); ?> / <?php echo formatBytes($srv['mem_total_bytes']); ?>)</div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo min(100, $memPct); ?>%; background: <?php echo $memPct > 80 ? '#ef4444' : ($memPct > 50 ? '#f59e0b' : '#36f3ff'); ?>;"></div>
                                </div>
                            </div>
                            <div class="metric-box">
                                <div class="metric-value"><?php echo number_format($diskPct, 1); ?>%</div>
                                <div class="metric-label">Disk (<?php echo formatBytes($srv['disk_used_bytes']); ?> / <?php echo formatBytes($srv['disk_total_bytes']); ?>)</div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo min(100, $diskPct); ?>%; background: <?php echo $diskPct > 80 ? '#ef4444' : ($diskPct > 50 ? '#f59e0b' : '#36f3ff'); ?>;"></div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (!empty($topProcs)): ?>
                        <div class="top-procs">
                            <h5><i class="ion-ios-list"></i> Top 5 Processes by CPU</h5>
                            <table class="proc-table">
                                <thead>
                                    <tr>
                                        <th style="width:40%">Process</th>
                                        <th style="width:15%">PID</th>
                                        <th style="width:22%">CPU %</th>
                                        <th style="width:23%">Memory %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($topProcs as $proc): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($proc['name'] ?? 'Unknown'); ?></td>
                                        <td><?php echo htmlspecialchars($proc['pid'] ?? '-'); ?></td>
                                        <td><?php echo number_format($proc['cpu'] ?? 0, 1); ?>%</td>
                                        <td><?php echo number_format($proc['mem'] ?? 0, 1); ?>%</td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                        
                        <div class="last-update">
                            First seen: <?php echo date('Y-m-d H:i', strtotime($srv['first_seen'])); ?> | 
                            Last update: <?php echo timeAgo($srv['ts']); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php endif; ?>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-sm-12">
                <div class="status-card">
                    <h3><i class="ion-wrench"></i> Monitoring Setup</h3>
                    <p style="color:#a8bedc;">To add a new server to monitoring:</p>
                    <ol style="color:#a8bedc; padding-left:20px; margin-top:10px;">
                        <li>Copy <code>ops-tools/scripts/report_server_status.sh</code> to <code>/home/gameserver/tools/scripts/</code></li>
                        <li>Ensure <code>/home/gameserver/tools/.password</code> exists with the MySQL password</li>
                        <li>Run: <code>./report_server_status.sh --mysql-host core.iaregamer.com</code></li>
                        <li>Add to cron for regular updates: <code>*/5 * * * * /home/gameserver/tools/scripts/report_server_status.sh</code></li>
                    </ol>
                    <p style="color:#7894b9; margin-top:15px; font-size:0.9rem;">
                        The script auto-creates the database and tables on first run. For DR failover, change <code>--mysql-host</code> to <code>core-dr.iaregamer.com</code>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include '../includes/footer.php'; ?>
<script src="../assets/js/jquery-1.12.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>
