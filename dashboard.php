<?php
session_start();
define('WDS_SYSTEM', true);
require_once 'includes/portal-helpers.php';

portalRequireLogin();

$user = portalGetUser();
$role = portalGetRole();
$displayName = $user['display_name'] ?? $user['username'] ?? 'User';
$isStaffRole = in_array($role, ['admin', 'staff'], true);
$isAdminRole = $role === 'admin';

$requests = portalLoadProjectRequests();
$proposals = portalLoadProposals();
$agreements = portalLoadProjectAgreements();

$myUsername = (string)($user['username'] ?? '');

$dashboardTs = static function (array $row, array $fields): int {
    foreach ($fields as $field) {
        $value = trim((string)($row[$field] ?? ''));
        if ($value !== '') {
            $ts = strtotime($value);
            if ($ts !== false) {
                return $ts;
            }
        }
    }
    return 0;
};

$statusLabels = [
    'new' => 'New',
    'reviewing' => 'Reviewing',
    'contacted' => 'Contacted',
    'needs_info' => 'Needs Info',
    'proposal_drafted' => 'Proposal Drafted',
    'proposal_sent' => 'Awaiting Approval',
    'accepted' => 'Active',
    'active' => 'Active',
    'completed' => 'Completed',
    'closed' => 'Completed',
    'declined' => 'Declined',
    'cancelled' => 'Cancelled',
];

$proposalStatusLabels = [
    'draft' => 'Draft',
    'sent' => 'Sent',
    'accepted' => 'Approved',
    'rejected' => 'Rejected',
];

$agreementStatusLabels = [
    'draft' => 'Draft',
    'sent' => 'Sent',
    'signed' => 'Signed',
];

$pendingProjectStatuses = ['new', 'reviewing', 'contacted', 'needs_info', 'proposal_drafted'];
$activeProjectStatuses = ['accepted', 'active', 'proposal_sent'];
$completedProjectStatuses = ['completed', 'closed'];

$visibleRequests = array_values(array_filter($requests, function ($request) use ($role, $myUsername) {
    if ($role === 'client') {
        return (string)($request['client_username'] ?? '') === $myUsername;
    }
    return true;
}));

$visibleRequestIds = [];
foreach ($visibleRequests as $request) {
    $visibleRequestIds[] = portalGetRequestDisplayId((array)$request);
}
$visibleRequestIds = array_values(array_unique($visibleRequestIds));

$proposalsByRequest = [];
foreach ($proposals as $proposal) {
    $requestId = trim((string)($proposal['request_id'] ?? ''));
    if ($requestId === '' || !in_array($requestId, $visibleRequestIds, true)) {
        continue;
    }
    $proposalsByRequest[$requestId][] = $proposal;
}

$agreementsByRequest = [];
foreach ($agreements as $agreement) {
    $requestId = trim((string)($agreement['request_id'] ?? ''));
    if ($requestId === '' || !in_array($requestId, $visibleRequestIds, true)) {
        continue;
    }
    $agreementsByRequest[$requestId][] = $agreement;
}

foreach ($proposalsByRequest as &$proposalRows) {
    usort($proposalRows, function ($a, $b) use ($dashboardTs) {
        return $dashboardTs((array)$b, ['updated_at', 'created_at']) <=> $dashboardTs((array)$a, ['updated_at', 'created_at']);
    });
}
unset($proposalRows);

foreach ($agreementsByRequest as &$agreementRows) {
    usort($agreementRows, function ($a, $b) use ($dashboardTs) {
        return $dashboardTs((array)$b, ['updated_at', 'created_at', 'signed_at']) <=> $dashboardTs((array)$a, ['updated_at', 'created_at', 'signed_at']);
    });
}
unset($agreementRows);

$projectRows = [];
$activeProjects = 0;
$pendingProjects = 0;
$completedProjects = 0;
$awaitingApproval = 0;
$awaitingSignature = 0;

foreach ($visibleRequests as $request) {
    $projectId = portalGetRequestDisplayId((array)$request);
    $requestStatus = (string)($request['status'] ?? 'new');
    $proposal = $proposalsByRequest[$projectId][0] ?? null;
    $agreement = $agreementsByRequest[$projectId][0] ?? null;

    if (in_array($requestStatus, $activeProjectStatuses, true)) {
        $activeProjects++;
    } elseif (in_array($requestStatus, $completedProjectStatuses, true)) {
        $completedProjects++;
    } else {
        $pendingProjects++;
    }

    if ((string)($proposal['status'] ?? '') === 'sent') {
        $awaitingApproval++;
    }
    if ((string)($agreement['status'] ?? '') === 'sent') {
        $awaitingSignature++;
    }

    $createdTs = $dashboardTs((array)$request, ['created_at']);
    $updatedTs = $dashboardTs((array)$request, ['updated_at', 'created_at']);
    $proposalUpdatedTs = $proposal ? $dashboardTs((array)$proposal, ['updated_at', 'created_at']) : 0;
    $agreementUpdatedTs = $agreement ? $dashboardTs((array)$agreement, ['signed_at', 'updated_at', 'created_at']) : 0;
    $lastUpdatedTs = max($updatedTs, $proposalUpdatedTs, $agreementUpdatedTs, $createdTs);

    $projectRows[] = [
        'id' => $projectId,
        'name' => (string)($proposal['project_title'] ?? ($request['project_name'] ?? $request['project_type'] ?? 'Project')),
        'client' => (string)($request['name'] ?? '—'),
        'status' => $requestStatus,
        'status_label' => $statusLabels[$requestStatus] ?? ucfirst($requestStatus),
        'created_ts' => $createdTs,
        'updated_ts' => $updatedTs,
        'last_updated_ts' => $lastUpdatedTs,
        'proposal_status' => (string)($proposal['status'] ?? ''),
        'proposal_status_label' => $proposalStatusLabels[(string)($proposal['status'] ?? '')] ?? '—',
        'proposal_updated_ts' => $proposalUpdatedTs,
        'agreement_status' => (string)($agreement['status'] ?? ''),
        'agreement_status_label' => $agreementStatusLabels[(string)($agreement['status'] ?? '')] ?? '—',
        'agreement_updated_ts' => $agreementUpdatedTs,
        'client_notes' => trim((string)($request['client_notes'] ?? '')),
        'client_notes_ts' => $dashboardTs((array)$request, ['client_notes_updated_at', 'updated_at', 'created_at']),
        'admin_notes' => trim((string)($request['admin_notes'] ?? ($request['internal_notes'] ?? ''))),
        'admin_notes_ts' => $dashboardTs((array)$request, ['admin_notes_updated_at', 'updated_at', 'created_at']),
    ];
}

usort($projectRows, function ($a, $b) {
    return ((int)$b['last_updated_ts']) <=> ((int)$a['last_updated_ts']);
});

$recentProjects = array_slice($projectRows, 0, 6);
$activeProjectRows = array_values(array_filter($projectRows, function ($project) use ($activeProjectStatuses) {
    return in_array((string)$project['status'], $activeProjectStatuses, true);
}));
$pendingProjectRows = array_values(array_filter($projectRows, function ($project) use ($pendingProjectStatuses, $activeProjectStatuses, $completedProjectStatuses) {
    $status = (string)$project['status'];
    if (in_array($status, $pendingProjectStatuses, true)) {
        return true;
    }
    return !in_array($status, $activeProjectStatuses, true) && !in_array($status, $completedProjectStatuses, true);
}));
$completedProjectRows = array_values(array_filter($projectRows, function ($project) use ($completedProjectStatuses) {
    return in_array((string)$project['status'], $completedProjectStatuses, true);
}));

$recentActivity = [];
foreach ($projectRows as $project) {
    if ((int)$project['created_ts'] > 0) {
        $recentActivity[] = [
            'timestamp' => (int)$project['created_ts'],
            'project_id' => (string)$project['id'],
            'label' => 'Request Submitted',
            'detail' => (string)$project['name'],
        ];
    }
    if ((int)$project['proposal_updated_ts'] > 0) {
        $proposalLabel = (string)$project['proposal_status_label'];
        $recentActivity[] = [
            'timestamp' => (int)$project['proposal_updated_ts'],
            'project_id' => (string)$project['id'],
            'label' => 'Proposal ' . ($proposalLabel !== '—' ? $proposalLabel : 'Updated'),
            'detail' => (string)$project['name'],
        ];
    }
    if ((int)$project['agreement_updated_ts'] > 0) {
        $agreementLabel = (string)$project['agreement_status_label'];
        $recentActivity[] = [
            'timestamp' => (int)$project['agreement_updated_ts'],
            'project_id' => (string)$project['id'],
            'label' => 'Agreement ' . ($agreementLabel !== '—' ? $agreementLabel : 'Updated'),
            'detail' => (string)$project['name'],
        ];
    }
    if ((int)$project['updated_ts'] > 0 && (int)$project['updated_ts'] !== (int)$project['created_ts']) {
        $recentActivity[] = [
            'timestamp' => (int)$project['updated_ts'],
            'project_id' => (string)$project['id'],
            'label' => 'Project Updated',
            'detail' => (string)$project['status_label'],
        ];
    }
    if (in_array((string)$project['status'], ['completed', 'closed'], true) && (int)$project['last_updated_ts'] > 0) {
        $recentActivity[] = [
            'timestamp' => (int)$project['last_updated_ts'],
            'project_id' => (string)$project['id'],
            'label' => 'Project Completed',
            'detail' => (string)$project['name'],
        ];
    }
}
usort($recentActivity, function ($a, $b) {
    return ((int)$b['timestamp']) <=> ((int)$a['timestamp']);
});
$recentActivity = array_slice($recentActivity, 0, 8);

$recentMessages = [];
foreach ($projectRows as $project) {
    if ($project['client_notes'] !== '' && (int)$project['client_notes_ts'] > 0) {
        $recentMessages[] = [
            'timestamp' => (int)$project['client_notes_ts'],
            'project_id' => (string)$project['id'],
            'label' => 'Client Note',
            'message' => (string)$project['client_notes'],
        ];
    }
    if ($isStaffRole && $project['admin_notes'] !== '' && (int)$project['admin_notes_ts'] > 0) {
        $recentMessages[] = [
            'timestamp' => (int)$project['admin_notes_ts'],
            'project_id' => (string)$project['id'],
            'label' => 'Admin Note',
            'message' => (string)$project['admin_notes'],
        ];
    }
}
usort($recentMessages, function ($a, $b) {
    return ((int)$b['timestamp']) <=> ((int)$a['timestamp']);
});
$recentMessages = array_slice($recentMessages, 0, 8);

$current_page = 'dashboard';
$header_class = 'inner-header';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="/assets/images/RL-icon.png">
    <title>Dashboard | Runlevel Systems</title>
    <link href="assets/css/coreloop.css" rel="stylesheet">
    <style>
        .portal-wrap { padding: 28px 0 70px; }
        .portal-header-bar { background:#0c1729;border:1px solid rgba(54,243,255,.16);border-radius:10px;padding:14px 18px;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px; }
        .portal-header-bar h1 { color:#ffc600;margin:0;font-size:1.28rem; }
        .portal-header-bar .portal-user { color:#7a9ac0;font-size:.84rem; }
        .portal-actions { display:flex;gap:8px;flex-wrap:wrap;align-items:center; }
        .portal-link-btn { border:1px solid rgba(54,243,255,.3);color:#36f3ff;background:rgba(54,243,255,.06);border-radius:6px;padding:6px 10px;font-size:.78rem;text-decoration:none; }
        .portal-link-btn:hover { color:#ffc600;border-color:#ffc600;text-decoration:none; }
        .portal-logout { color:#5a7a9e;font-size:.8rem; }
        .portal-logout:hover { color:#ef4444; }
        .portal-section-title { color:#36f3ff;font-size:.86rem;font-weight:700;margin:0 0 10px;text-transform:uppercase;letter-spacing:.05em; }
        .portal-card-grid { display:grid;grid-template-columns:repeat(auto-fit,minmax(145px,1fr));gap:10px;margin-bottom:14px; }
        .portal-dash-card { background:#0c1729;border:1px solid rgba(54,243,255,.18);border-radius:9px;padding:12px;text-align:center;text-decoration:none;color:#eaf3ff;display:block; }
        .portal-dash-card:hover { border-color:#ffc600;color:#ffc600;text-decoration:none; }
        .portal-dash-card .card-label { font-weight:700;font-size:.82rem;line-height:1.25; }
        .portal-dash-card .card-count { font-size:1.1rem;color:#36f3ff;font-weight:700;margin-top:4px; }
        .portal-layout { display:grid;grid-template-columns:1fr;gap:12px; }
        .portal-panel { background:#0c1729;border:1px solid rgba(54,243,255,.16);border-radius:10px;padding:14px;overflow:auto; }
        .portal-panel h3 { margin:0 0 8px;color:#36f3ff;font-size:.86rem;text-transform:uppercase;letter-spacing:.05em; }
        .portal-panel table { width:100%;min-width:680px;border-collapse:collapse; }
        .portal-panel th,.portal-panel td { padding:8px 6px;border-top:1px solid rgba(54,243,255,.1);font-size:.78rem;vertical-align:top; }
        .portal-panel th { color:#5a7a9e;font-size:.68rem;text-transform:uppercase;border-top:none;letter-spacing:.04em; }
        .portal-muted { color:#7a9ac0;font-size:.8rem; }
        .portal-mono { font-family:monospace;color:#ffc600; }
        .portal-primary-action { display:inline-block;border:1px solid rgba(54,243,255,.35);background:rgba(54,243,255,.08);color:#36f3ff;border-radius:4px;padding:2px 8px;font-size:.68rem;font-weight:700;margin-right:7px;text-transform:uppercase;letter-spacing:.04em; }
        .portal-primary-action:hover { color:#ffc600;border-color:#ffc600;text-decoration:none; }
        .status-chip { display:inline-block;border:1px solid rgba(54,243,255,.28);background:rgba(54,243,255,.08);padding:2px 7px;border-radius:999px;color:#a8bedc;font-size:.68rem;font-weight:700; }
        .timeline-list,.message-list { list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:7px; }
        .timeline-list li,.message-list li { border:1px solid rgba(54,243,255,.12);border-radius:8px;padding:9px 10px;background:rgba(5,11,20,.44); }
        .timeline-label,.message-label { color:#36f3ff;font-size:.74rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em; }
        .timeline-meta,.message-meta { color:#7a9ac0;font-size:.74rem;margin-top:2px; }
        .message-body { color:#d8e7f7;font-size:.82rem;margin-top:5px;white-space:pre-wrap; }
        .role-badge { display:inline-block;background:rgba(54,243,255,.1);border:1px solid rgba(54,243,255,.25);color:#36f3ff;border-radius:4px;padding:2px 6px;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-left:4px;vertical-align:middle; }
        @media (max-width: 768px) {
            .portal-panel table { min-width:600px; }
        }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navigation.php'; ?>

<section class="portal-wrap">
    <div class="container">
        <div class="portal-header-bar">
            <div>
                <h1>Dashboard</h1>
                <div class="portal-user">Signed in as <strong><?php echo pe($displayName); ?></strong> <span class="role-badge"><?php echo pe(ucfirst($role)); ?></span></div>
            </div>
            <div class="portal-actions">
                <a href="/estimate.php" class="portal-link-btn">Start Project</a>
                <?php if ($isStaffRole): ?><a href="/staff/estimate-requests.php" class="portal-link-btn">All Projects</a><?php endif; ?>
                <?php if ($isAdminRole): ?><a href="/settings.php" class="portal-link-btn">Settings</a><?php endif; ?>
                <a href="/logout.php" class="portal-logout">Sign Out</a>
            </div>
        </div>

        <p class="portal-section-title">Project Overview</p>
        <div class="portal-card-grid">
            <a href="#recent-projects" class="portal-dash-card"><div class="card-label">Recent Projects</div><div class="card-count"><?php echo count($recentProjects); ?></div></a>
            <a href="#active-projects" class="portal-dash-card"><div class="card-label">Active Projects</div><div class="card-count"><?php echo $activeProjects; ?></div></a>
            <a href="#pending-projects" class="portal-dash-card"><div class="card-label">Pending Projects</div><div class="card-count"><?php echo $pendingProjects; ?></div></a>
            <a href="#completed-projects" class="portal-dash-card"><div class="card-label">Completed Projects</div><div class="card-count"><?php echo $completedProjects; ?></div></a>
            <a href="#recent-activity" class="portal-dash-card"><div class="card-label">Awaiting Approval</div><div class="card-count"><?php echo $awaitingApproval; ?></div></a>
            <a href="#recent-activity" class="portal-dash-card"><div class="card-label">Awaiting Signature</div><div class="card-count"><?php echo $awaitingSignature; ?></div></a>
        </div>

        <div class="portal-layout">
            <div class="portal-panel" id="recent-projects">
                <h3>Recent Projects</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Project ID</th>
                            <th>Project ID</th>
                            <th>Client</th>
                            <th>Status</th>
                            <th>Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($recentProjects)): ?>
                        <tr><td colspan="5" class="portal-muted">No projects yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentProjects as $project): ?>
                            <tr>
                                <td class="portal-mono"><a class="portal-primary-action" href="/project.php?id=<?php echo urlencode((string)$project['id']); ?>">Open</a><?php echo pe($project['id']); ?></td>
                                <td><?php echo pe($project['name']); ?></td>
                                <td><?php echo pe($project['client']); ?></td>
                                <td><span class="status-chip"><?php echo pe($project['status_label']); ?></span></td>
                                <td class="portal-muted"><?php echo $project['last_updated_ts'] > 0 ? pe(date('M j, Y', (int)$project['last_updated_ts'])) : '—'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="portal-panel" id="active-projects">
                <h3>Active Projects</h3>
                <table>
                    <thead>
                    <tr>
                        <th>Project ID</th>
                        <th>Project ID</th>
                        <th>Proposal</th>
                        <th>Agreement</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($activeProjectRows)): ?>
                        <tr><td colspan="4" class="portal-muted">No active projects right now.</td></tr>
                    <?php else: ?>
                        <?php foreach (array_slice($activeProjectRows, 0, 8) as $project): ?>
                            <tr>
                                <td class="portal-mono"><a class="portal-primary-action" href="/project.php?id=<?php echo urlencode((string)$project['id']); ?>">Open</a><?php echo pe($project['id']); ?></td>
                                <td><?php echo pe($project['name']); ?></td>
                                <td><?php echo pe($project['proposal_status_label']); ?></td>
                                <td><?php echo pe($project['agreement_status_label']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="portal-panel" id="pending-projects">
                <h3>Pending Projects</h3>
                <table>
                    <thead>
                    <tr>
                        <th>Project</th>
                        <th>Project</th>
                        <th>Status</th>
                        <th>Created</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($pendingProjectRows)): ?>
                        <tr><td colspan="4" class="portal-muted">No pending projects.</td></tr>
                    <?php else: ?>
                        <?php foreach (array_slice($pendingProjectRows, 0, 8) as $project): ?>
                            <tr>
                                <td class="portal-mono"><a class="portal-primary-action" href="/project.php?id=<?php echo urlencode((string)$project['id']); ?>">Open</a><?php echo pe($project['id']); ?></td>
                                <td><?php echo pe($project['name']); ?></td>
                                <td><span class="status-chip"><?php echo pe($project['status_label']); ?></span></td>
                                <td class="portal-muted"><?php echo $project['created_ts'] > 0 ? pe(date('M j, Y', (int)$project['created_ts'])) : '—'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="portal-panel" id="completed-projects">
                <h3>Completed Projects</h3>
                <table>
                    <thead>
                    <tr>
                        <th>Project</th>
                        <th>Project</th>
                        <th>Completed</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($completedProjectRows)): ?>
                        <tr><td colspan="3" class="portal-muted">No completed projects yet.</td></tr>
                    <?php else: ?>
                        <?php foreach (array_slice($completedProjectRows, 0, 8) as $project): ?>
                            <tr>
                                <td class="portal-mono"><a class="portal-primary-action" href="/project.php?id=<?php echo urlencode((string)$project['id']); ?>">Open</a><?php echo pe($project['id']); ?></td>
                                <td><?php echo pe($project['name']); ?></td>
                                <td class="portal-muted"><?php echo $project['last_updated_ts'] > 0 ? pe(date('M j, Y', (int)$project['last_updated_ts'])) : '—'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="portal-panel" id="recent-activity">
                <h3>Recent Activity</h3>
                <?php if (empty($recentActivity)): ?>
                    <p class="portal-muted">No activity yet.</p>
                <?php else: ?>
                    <ul class="timeline-list">
                        <?php foreach ($recentActivity as $activity): ?>
                            <li>
                                <div class="timeline-label"><?php echo pe($activity['label']); ?> · <a href="/project.php?id=<?php echo urlencode((string)$activity['project_id']); ?>" style="color:#36f3ff;"><?php echo pe($activity['project_id']); ?></a></div>
                                <div class="timeline-meta"><?php echo pe($activity['detail']); ?> · <?php echo pe(date('M j, Y g:i A', (int)$activity['timestamp'])); ?></div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="portal-panel" id="recent-messages">
                <h3>Recent Messages</h3>
                <?php if (empty($recentMessages)): ?>
                    <p class="portal-muted">No recent messages.</p>
                <?php else: ?>
                    <ul class="message-list">
                        <?php foreach ($recentMessages as $message): ?>
                            <li>
                                <div class="message-label"><?php echo pe($message['label']); ?> · <a href="/project.php?id=<?php echo urlencode((string)$message['project_id']); ?>" style="color:#36f3ff;"><?php echo pe($message['project_id']); ?></a></div>
                                <div class="message-meta"><?php echo pe(date('M j, Y g:i A', (int)$message['timestamp'])); ?></div>
                                <div class="message-body"><?php echo pe($message['message']); ?></div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/jquery-1.12.3.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
