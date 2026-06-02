<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/includes/portal-helpers.php';
require_once __DIR__ . '/includes/email.php';

portalRequireLogin();

$user       = portalGetUser();
$role       = portalGetRole();
$isStaff    = in_array($role, ['admin', 'staff'], true);
$isClient   = $role === 'client';
$myUsername = (string)($user['username'] ?? '');

$projectId = trim((string)($_GET['id'] ?? ''));
if ($projectId === '') {
    header('Location: /dashboard.php');
    exit;
}

$allowedUploadExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'txt', 'md', 'zip', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'json', 'log'];
$milestoneStatuses = ['Not Started', 'In Progress', 'Ready For Review', 'Payment Due', 'Paid', 'Complete'];

function projectNow() {
    return date('c');
}

function projectGenerateId($prefix, $len = 8) {
    return strtoupper($prefix) . '-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(8)), 0, $len));
}

function projectNormalizeMilestone(array $milestone, $index = 1) {
    $name = trim((string)($milestone['name'] ?? 'Milestone ' . $index));
    if ($name === '') {
        $name = 'Milestone ' . $index;
    }
    $status = trim((string)($milestone['status'] ?? 'Not Started'));
    $allowed = ['Not Started', 'In Progress', 'Ready For Review', 'Payment Due', 'Paid', 'Complete'];
    if (!in_array($status, $allowed, true)) {
        $status = 'Not Started';
    }
    return [
        'milestone_id' => trim((string)($milestone['milestone_id'] ?? '')) !== '' ? (string)$milestone['milestone_id'] : projectGenerateId('MS', 6),
        'name' => $name,
        'description' => trim((string)($milestone['description'] ?? '')),
        'deliverables' => trim((string)($milestone['deliverables'] ?? '')),
        'amount' => trim((string)($milestone['amount'] ?? '')),
        'payment_trigger' => trim((string)($milestone['payment_trigger'] ?? 'Upon completion and approval')),
        'estimate' => trim((string)($milestone['estimate'] ?? '')),
        'status' => $status,
    ];
}

function projectNormalizeProposal(array $proposal, array $request, $projectId) {
    $defaults = [
        'proposal_id' => '',
        'request_id' => $projectId,
        'client_username' => (string)($request['client_username'] ?? ''),
        'client_name' => (string)($request['name'] ?? ''),
        'client_email' => (string)($request['email'] ?? ''),
        'project_title' => (string)($request['project_type'] ?? 'Project'),
        'proposal_summary' => '',
        'proposed_work' => '',
        'deliverables' => '',
        'out_of_scope' => '',
        'timeline_estimate' => (string)($request['timeline'] ?? ''),
        'amount_due_to_start' => '',
        'total_project_amount' => '',
        'customer_responsibilities' => '',
        'revision_terms' => '',
        'terms_summary' => '',
        'full_terms_link' => '/runlevel-terms.php',
        'status' => 'draft',
        'milestone_count' => 0,
        'milestones' => [],
        'change_requests' => [],
        'customer_agreed' => false,
        'customer_typed_name' => '',
        'customer_acceptance_date' => '',
        'customer_notes' => '',
        'staff_agreed' => false,
        'staff_typed_name' => '',
        'staff_acceptance_date' => '',
        'created_at' => projectNow(),
        'updated_at' => projectNow(),
    ];
    $normalized = array_merge($defaults, $proposal);
    if (trim((string)$normalized['proposal_id']) === '') {
        $normalized['proposal_id'] = projectGenerateId('PROP', 6);
    }

    $status = trim((string)$normalized['status']);
    $allowedStatuses = ['draft', 'sent', 'changes_requested', 'accepted', 'awaiting_payment', 'payment_received', 'project_active', 'milestones', 'completed'];
    if (!in_array($status, $allowedStatuses, true)) {
        $normalized['status'] = 'draft';
    }

    $milestones = [];
    if (isset($normalized['milestones']) && is_array($normalized['milestones'])) {
        $idx = 1;
        foreach ($normalized['milestones'] as $milestone) {
            if (!is_array($milestone)) {
                continue;
            }
            $milestones[] = projectNormalizeMilestone($milestone, $idx++);
        }
    }
    $normalized['milestones'] = $milestones;
    $normalized['milestone_count'] = min(5, max(0, (int)($normalized['milestone_count'] ?? count($milestones))));

    $changes = [];
    if (isset($normalized['change_requests']) && is_array($normalized['change_requests'])) {
        foreach ($normalized['change_requests'] as $row) {
            if (!is_array($row)) {
                continue;
            }
            $changes[] = [
                'change_id' => trim((string)($row['change_id'] ?? '')) !== '' ? (string)$row['change_id'] : projectGenerateId('CHG', 6),
                'request_text' => trim((string)($row['request_text'] ?? '')),
                'budget_concern' => trim((string)($row['budget_concern'] ?? '')),
                'timeline_concern' => trim((string)($row['timeline_concern'] ?? '')),
                'deliverable_change' => trim((string)($row['deliverable_change'] ?? '')),
                'other_note' => trim((string)($row['other_note'] ?? '')),
                'requested_by' => trim((string)($row['requested_by'] ?? '')),
                'requested_by_role' => trim((string)($row['requested_by_role'] ?? 'client')),
                'requested_at' => trim((string)($row['requested_at'] ?? projectNow())),
            ];
        }
    }
    $normalized['change_requests'] = $changes;

    $normalized['customer_agreed'] = !empty($normalized['customer_agreed']);
    $normalized['staff_agreed'] = !empty($normalized['staff_agreed']);

    return $normalized;
}

function projectNormalizeRequest(array $request) {
    $request = portalNormalizeProjectRequest($request);

    $request['project_files'] = isset($request['project_files']) && is_array($request['project_files']) ? array_values($request['project_files']) : [];
    $request['messages'] = isset($request['messages']) && is_array($request['messages']) ? array_values($request['messages']) : [];
    $request['timeline_events'] = isset($request['timeline_events']) && is_array($request['timeline_events']) ? array_values($request['timeline_events']) : [];
    $request['payment_records'] = isset($request['payment_records']) && is_array($request['payment_records']) ? array_values($request['payment_records']) : [];
    $request['project_active_at'] = (string)($request['project_active_at'] ?? '');
    $request['project_completed_at'] = (string)($request['project_completed_at'] ?? '');
    $request['request_summary'] = (string)($request['request_summary'] ?? ($request['description'] ?? ''));
    $request['problem_to_solve'] = (string)($request['problem_to_solve'] ?? '');
    $request['existing_work'] = (string)($request['existing_work'] ?? ($request['existing_assets'] ?? ''));
    $request['next_action_override'] = (string)($request['next_action_override'] ?? '');

    return $request;
}

function projectAppendTimeline(array &$request, $event, $label, $visibility = 'customer_visible') {
    $request['timeline_events'][] = [
        'event_id' => projectGenerateId('EVT', 7),
        'event' => (string)$event,
        'label' => (string)$label,
        'timestamp' => projectNow(),
        'visibility' => $visibility,
    ];
}

function projectProposalStatusLabel($status) {
    $map = [
        'draft' => 'Proposal Draft',
        'sent' => 'Proposal Sent',
        'changes_requested' => 'Changes Requested',
        'accepted' => 'Proposal Accepted',
        'awaiting_payment' => 'Awaiting Payment',
        'payment_received' => 'Payment Received',
        'project_active' => 'Active',
        'milestones' => 'Milestone Review',
        'completed' => 'Completed',
    ];
    return $map[$status] ?? ucfirst(str_replace('_', ' ', (string)$status));
}

function projectStatusBadge(array $request, ?array $proposal) {
    $status = (string)($proposal['status'] ?? 'draft');
    if ((string)($request['status'] ?? '') === 'completed') {
        $status = 'completed';
    }
    $map = [
        'draft' => ['Proposal Draft', '#60a5fa', 'rgba(96,165,250,.14)'],
        'sent' => ['Proposal Sent', '#fbbf24', 'rgba(251,191,36,.14)'],
        'changes_requested' => ['Changes Requested', '#fb923c', 'rgba(251,146,60,.14)'],
        'accepted' => ['Proposal Accepted', '#34d399', 'rgba(52,211,153,.14)'],
        'awaiting_payment' => ['Awaiting Payment', '#f97316', 'rgba(249,115,22,.14)'],
        'payment_received' => ['Payment Received', '#10b981', 'rgba(16,185,129,.14)'],
        'project_active' => ['Active', '#22c55e', 'rgba(34,197,94,.14)'],
        'milestones' => ['Milestone Review', '#a78bfa', 'rgba(167,139,250,.14)'],
        'completed' => ['Completed', '#4ade80', 'rgba(74,222,128,.14)'],
    ];
    $row = $map[$status] ?? ['Request Submitted', '#36f3ff', 'rgba(54,243,255,.14)'];
    return '<span class="status-pill" style="border-color:' . $row[1] . ';color:' . $row[1] . ';background:' . $row[2] . ';">' . htmlspecialchars($row[0], ENT_QUOTES, 'UTF-8') . '</span>';
}

function projectCurrentWorkflowStep(array $request, ?array $proposal) {
    $requestSubmitted = true;
    $proposalSent = $proposal !== null && in_array((string)($proposal['status'] ?? 'draft'), ['sent', 'changes_requested', 'accepted', 'awaiting_payment', 'payment_received', 'project_active', 'milestones', 'completed'], true);
    $proposalAccepted = $proposal !== null && !empty($proposal['customer_agreed']) && !empty($proposal['staff_agreed']);
    $payments = isset($request['payment_records']) && is_array($request['payment_records']) ? $request['payment_records'] : [];
    $hasStartPayment = false;
    foreach ($payments as $payment) {
        if (!is_array($payment)) {
            continue;
        }
        if ((string)($payment['payment_for'] ?? '') === 'start_payment' && in_array((string)($payment['status'] ?? ''), ['recorded', 'paid'], true)) {
            $hasStartPayment = true;
            break;
        }
    }

    $projectActive = (string)($request['status'] ?? '') === 'active' || trim((string)($request['project_active_at'] ?? '')) !== '';
    $projectCompleted = (string)($request['status'] ?? '') === 'completed' || trim((string)($request['project_completed_at'] ?? '')) !== '';

    $milestonesExist = $proposal !== null && !empty($proposal['milestones']);
    $allMilestonesComplete = false;
    if ($milestonesExist) {
        $allMilestonesComplete = true;
        foreach ($proposal['milestones'] as $milestone) {
            if ((string)($milestone['status'] ?? '') !== 'Complete') {
                $allMilestonesComplete = false;
                break;
            }
        }
    }

    $steps = [
        ['label' => 'Project Request', 'done' => $requestSubmitted],
        ['label' => 'Proposal Sent', 'done' => $proposalSent],
        ['label' => 'Proposal Accepted', 'done' => $proposalAccepted],
        ['label' => 'Payment Received', 'done' => $hasStartPayment],
        ['label' => 'Project Active', 'done' => $projectActive],
        ['label' => 'Milestones', 'done' => $milestonesExist ? $allMilestonesComplete : false, 'partial' => $milestonesExist && !$allMilestonesComplete],
        ['label' => 'Project Completed', 'done' => $projectCompleted],
    ];

    $currentIndex = 0;
    foreach ($steps as $idx => $step) {
        if (!$step['done']) {
            $currentIndex = $idx;
            break;
        }
        $currentIndex = $idx;
    }

    if ($projectCompleted) {
        $currentIndex = count($steps) - 1;
    }

    foreach ($steps as $idx => &$step) {
        if ($step['done']) {
            $step['state'] = 'completed';
        } elseif ($idx === $currentIndex) {
            $step['state'] = 'current';
        } else {
            $step['state'] = 'future';
        }
        if (!isset($step['partial'])) {
            $step['partial'] = false;
        }
    }
    unset($step);

    return $steps;
}

function projectNextAction(array $request, ?array $proposal, $isStaff) {
    if ($proposal === null) {
        return $isStaff ? 'Staff needs to create proposal.' : 'Runlevel Systems is preparing your proposal.';
    }

    $status = (string)($proposal['status'] ?? 'draft');
    if ($status === 'draft') {
        return $isStaff ? 'Staff needs to send proposal to customer.' : 'Proposal draft is being finalized.';
    }
    if (in_array($status, ['sent', 'changes_requested'], true)) {
        return $isStaff ? 'Customer needs to review proposal.' : 'Please review the proposal and accept or request changes.';
    }

    $bothAccepted = !empty($proposal['customer_agreed']) && !empty($proposal['staff_agreed']);
    if (!$bothAccepted) {
        if ($isStaff) {
            return empty($proposal['staff_agreed']) ? 'Staff needs to approve proposal signoff.' : 'Waiting for customer signoff.';
        }
        return empty($proposal['customer_agreed']) ? 'Please complete proposal signoff.' : 'Waiting for staff approval.';
    }

    $hasStartPayment = false;
    foreach (($request['payment_records'] ?? []) as $payment) {
        if (!is_array($payment)) {
            continue;
        }
        if ((string)($payment['payment_for'] ?? '') === 'start_payment' && in_array((string)($payment['status'] ?? ''), ['recorded', 'paid'], true)) {
            $hasStartPayment = true;
            break;
        }
    }
    if (!$hasStartPayment) {
        return $isStaff ? 'Waiting for starting payment.' : 'Please complete starting payment.';
    }

    if ((string)($request['status'] ?? '') !== 'active') {
        return $isStaff ? 'Set project active when work starts.' : 'Payment received. Project starts when staff marks active.';
    }

    if ((string)($request['status'] ?? '') === 'completed') {
        return 'Project is completed.';
    }

    return 'Project is active.';
}

function projectOpenSections(array $request, ?array $proposal) {
    $status = (string)($proposal['status'] ?? 'draft');
    $requestStatus = (string)($request['status'] ?? 'new');

    return [
        'request' => true,
        'proposal' => in_array($status, ['draft', 'sent', 'changes_requested', 'accepted'], true),
        'payments' => in_array($status, ['accepted', 'awaiting_payment', 'payment_received'], true) || in_array($requestStatus, ['proposal_sent', 'accepted'], true),
        'messages' => true,
        'timeline' => in_array($requestStatus, ['active', 'completed'], true),
        'files' => false,
    ];
}

function projectCanViewFile(array $file, $isStaff) {
    $visibility = (string)($file['visibility'] ?? 'customer_visible');
    if ($visibility === 'staff_only' && !$isStaff) {
        return false;
    }
    return true;
}

function projectUploadBaseDir() {
    return __DIR__ . '/data/uploads';
}

function projectEnsureUploadProtection() {
    $base = projectUploadBaseDir();
    if (!is_dir($base)) {
        @mkdir($base, 0755, true);
    }
    $htaccess = $base . '/.htaccess';
    if (!is_file($htaccess)) {
        $rules = "Options -Indexes\nRemoveHandler .php .phtml .php3 .php4 .php5 .php7 .phar\nphp_flag engine off\n";
        @file_put_contents($htaccess, $rules);
    }
}

function projectStoreUpload($projectId, $fileBag, array $allowedExtensions, &$error = '') {
    if (!is_array($fileBag) || !isset($fileBag['name'], $fileBag['tmp_name'], $fileBag['error'])) {
        $error = 'No file upload found.';
        return null;
    }

    if ((int)$fileBag['error'] !== UPLOAD_ERR_OK) {
        $error = 'Upload failed with error code ' . (int)$fileBag['error'] . '.';
        return null;
    }

    if (!is_uploaded_file((string)$fileBag['tmp_name'])) {
        $error = 'Invalid uploaded file.';
        return null;
    }

    $original = trim((string)$fileBag['name']);
    $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    if ($ext === '' || !in_array($ext, $allowedExtensions, true)) {
        $error = 'Unsupported file type.';
        return null;
    }

    $size = (int)($fileBag['size'] ?? 0);
    if ($size <= 0 || $size > (15 * 1024 * 1024)) {
        $error = 'File size must be between 1 byte and 15 MB.';
        return null;
    }

    projectEnsureUploadProtection();
    $safeProject = preg_replace('/[^A-Za-z0-9_-]/', '', (string)$projectId);
    if ($safeProject === '') {
        $safeProject = 'project';
    }
    $projectDir = projectUploadBaseDir() . '/' . $safeProject;
    if (!is_dir($projectDir) && !@mkdir($projectDir, 0755, true)) {
        $error = 'Unable to create upload directory.';
        return null;
    }

    $storedName = date('His') . '-' . bin2hex(random_bytes(6)) . '.' . $ext;
    $targetPath = $projectDir . '/' . $storedName;

    if (!move_uploaded_file((string)$fileBag['tmp_name'], $targetPath)) {
        $error = 'Unable to move uploaded file.';
        return null;
    }
    @chmod($targetPath, 0644);

    $cleanOriginal = preg_replace('/[^A-Za-z0-9._ -]/', '_', basename($original));
    if ($cleanOriginal === '') {
        $cleanOriginal = 'file.' . $ext;
    }

    return [
        'file_id' => projectGenerateId('FILE', 8),
        'project_id' => $projectId,
        'original_filename' => $cleanOriginal,
        'stored_filename' => $storedName,
        'relative_path' => 'data/uploads/' . $safeProject . '/' . $storedName,
        'file_size' => $size,
        'mime_type' => (string)($fileBag['type'] ?? ''),
        'upload_time' => projectNow(),
    ];
}

function projectPersistProposal(array &$allProposals, array $proposal) {
    $proposalId = (string)($proposal['proposal_id'] ?? '');
    $found = false;
    foreach ($allProposals as $idx => $existing) {
        if ((string)($existing['proposal_id'] ?? '') !== $proposalId) {
            continue;
        }
        $allProposals[$idx] = $proposal;
        $found = true;
        break;
    }
    if (!$found) {
        $allProposals[] = $proposal;
    }
}

$allRequests = portalLoadProjectRequests();
$allProposals = portalLoadProposals();
$allAgreements = portalLoadProjectAgreements();
$adminSettings = portalLoadAdminSettings();

$request = null;
$requestIndex = null;
foreach ($allRequests as $idx => $row) {
    if (portalGetRequestDisplayId((array)$row) === $projectId) {
        $request = projectNormalizeRequest((array)$row);
        $requestIndex = $idx;
        break;
    }
}

$notFound = ($request === null);
$forbidden = false;
if (!$notFound && $isClient && (string)($request['client_username'] ?? '') !== $myUsername) {
    $forbidden = true;
}

$currentProposal = null;
if (!$notFound && !$forbidden) {
    foreach ($allProposals as $proposalRow) {
        if ((string)($proposalRow['request_id'] ?? '') === $projectId) {
            $proposalCandidate = projectNormalizeProposal((array)$proposalRow, $request, $projectId);
            if ($currentProposal === null) {
                $currentProposal = $proposalCandidate;
                continue;
            }
            $currTs = strtotime((string)($currentProposal['updated_at'] ?? '')) ?: 0;
            $candTs = strtotime((string)($proposalCandidate['updated_at'] ?? '')) ?: 0;
            if ($candTs >= $currTs) {
                $currentProposal = $proposalCandidate;
            }
        }
    }
}

$notice = '';
$error = '';

if (!$notFound && !$forbidden && isset($_GET['download']) && trim((string)$_GET['download']) !== '') {
    $downloadId = trim((string)$_GET['download']);
    $targetFile = null;
    foreach (($request['project_files'] ?? []) as $file) {
        if ((string)($file['file_id'] ?? '') === $downloadId) {
            $targetFile = $file;
            break;
        }
    }

    if ($targetFile === null || !projectCanViewFile((array)$targetFile, $isStaff)) {
        http_response_code(403);
        echo 'File access denied.';
        exit;
    }

    $path = __DIR__ . '/' . ltrim((string)($targetFile['relative_path'] ?? ''), '/');
    if (!is_file($path)) {
        http_response_code(404);
        echo 'File not found.';
        exit;
    }

    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename((string)($targetFile['original_filename'] ?? 'download.bin')) . '"');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$notFound && !$forbidden) {
    $action = trim((string)($_POST['action'] ?? ''));

    if ($action === 'save_request_next_action' && $isStaff) {
        $request['next_action_override'] = trim((string)($_POST['next_action_override'] ?? ''));
        $request['updated_at'] = projectNow();
        projectAppendTimeline($request, 'staff-updated-request', 'Staff updated request details', 'staff_only');
        $notice = 'Request details updated.';
    }

    if ($isStaff && in_array($action, ['save_proposal', 'send_proposal'], true)) {
        $proposal = $currentProposal !== null ? $currentProposal : [];
        $proposal = projectNormalizeProposal($proposal, $request, $projectId);

        $proposal['client_name'] = trim((string)($_POST['client_name'] ?? ($request['name'] ?? '')));
        $proposal['client_email'] = trim((string)($_POST['client_email'] ?? ($request['email'] ?? '')));
        $proposal['project_title'] = trim((string)($_POST['project_title'] ?? ($request['project_type'] ?? 'Project')));
        $proposal['proposal_summary'] = trim((string)($_POST['proposal_summary'] ?? ''));
        $proposal['proposed_work'] = trim((string)($_POST['proposed_work'] ?? ''));
        $proposal['deliverables'] = trim((string)($_POST['deliverables'] ?? ''));
        $proposal['out_of_scope'] = trim((string)($_POST['out_of_scope'] ?? ''));
        $proposal['timeline_estimate'] = trim((string)($_POST['timeline_estimate'] ?? ($request['timeline'] ?? '')));
        $proposal['amount_due_to_start'] = trim((string)($_POST['amount_due_to_start'] ?? ''));
        $proposal['total_project_amount'] = trim((string)($_POST['total_project_amount'] ?? ''));
        $proposal['customer_responsibilities'] = trim((string)($_POST['customer_responsibilities'] ?? ''));
        $proposal['revision_terms'] = trim((string)($_POST['revision_terms'] ?? ''));
        $proposal['terms_summary'] = trim((string)($_POST['terms_summary'] ?? ''));
        $proposal['full_terms_link'] = trim((string)($_POST['full_terms_link'] ?? '/runlevel-terms.php'));
        $proposal['updated_at'] = projectNow();

        $milestoneCount = max(0, min(5, (int)($_POST['milestone_count'] ?? 0)));
        $proposal['milestone_count'] = $milestoneCount;
        $proposalMilestones = [];
        for ($i = 0; $i < $milestoneCount; $i++) {
            $proposalMilestones[] = projectNormalizeMilestone([
                'milestone_id' => trim((string)($_POST['milestone_id'][$i] ?? '')),
                'name' => trim((string)($_POST['milestone_name'][$i] ?? '')),
                'description' => trim((string)($_POST['milestone_description'][$i] ?? '')),
                'deliverables' => trim((string)($_POST['milestone_deliverables'][$i] ?? '')),
                'amount' => trim((string)($_POST['milestone_amount'][$i] ?? '')),
                'payment_trigger' => trim((string)($_POST['milestone_trigger'][$i] ?? 'Upon completion and approval')),
                'estimate' => trim((string)($_POST['milestone_estimate'][$i] ?? '')),
                'status' => trim((string)($_POST['milestone_status'][$i] ?? 'Not Started')),
            ], $i + 1);
        }
        $proposal['milestones'] = $proposalMilestones;

        if ($action === 'send_proposal') {
            $proposal['status'] = 'sent';
            $request['status'] = 'proposal_sent';
            projectAppendTimeline($request, 'proposal-sent', 'Proposal sent to customer');
            $host = $_SERVER['HTTP_HOST'] ?? 'runlevel.systems';
            $projectUrl = 'https://' . $host . '/project.php?id=' . urlencode($projectId);
            send_project_proposal_email($proposal['client_email'], $proposal['client_name'], $projectId, $projectUrl, $proposal['proposal_id']);
            $notice = 'Proposal sent to customer.';
        } else {
            $proposal['status'] = 'draft';
            $request['status'] = 'proposal_drafted';
            projectAppendTimeline($request, 'proposal-updated', 'Proposal updated by staff');
            $notice = 'Proposal saved.';
        }

        projectPersistProposal($allProposals, $proposal);
        $currentProposal = $proposal;

        $proposalIds = isset($request['proposal_ids']) && is_array($request['proposal_ids']) ? $request['proposal_ids'] : [];
        if (!in_array($proposal['proposal_id'], $proposalIds, true)) {
            $proposalIds[] = $proposal['proposal_id'];
        }
        $request['proposal_ids'] = array_values($proposalIds);
        $request['updated_at'] = projectNow();
    }

    if ($isClient && $action === 'request_changes') {
        if ($currentProposal === null) {
            $error = 'No proposal available yet.';
        } else {
            $changeText = trim((string)($_POST['change_request_text'] ?? ''));
            if ($changeText === '') {
                $error = 'Please tell us what changes you would like.';
            } else {
                $proposal = projectNormalizeProposal($currentProposal, $request, $projectId);
                $proposal['status'] = 'changes_requested';
                $proposal['updated_at'] = projectNow();
                $proposal['change_requests'][] = [
                    'change_id' => projectGenerateId('CHG', 6),
                    'request_text' => $changeText,
                    'budget_concern' => trim((string)($_POST['change_budget_concern'] ?? '')),
                    'timeline_concern' => trim((string)($_POST['change_timeline_concern'] ?? '')),
                    'deliverable_change' => trim((string)($_POST['change_deliverable_change'] ?? '')),
                    'other_note' => trim((string)($_POST['change_other_note'] ?? '')),
                    'requested_by' => $myUsername,
                    'requested_by_role' => 'client',
                    'requested_at' => projectNow(),
                ];
                projectPersistProposal($allProposals, $proposal);
                $currentProposal = $proposal;
                $request['status'] = 'needs_info';
                $request['updated_at'] = projectNow();
                projectAppendTimeline($request, 'customer-requested-changes', 'Customer requested proposal changes');

                $host = $_SERVER['HTTP_HOST'] ?? 'runlevel.systems';
                $projectUrl = 'https://' . $host . '/project.php?id=' . urlencode($projectId);
                send_proposal_change_requested_email($proposal['proposal_id'], $projectId, (string)($request['name'] ?? $myUsername), $changeText, $projectUrl);
                $notice = 'Change request sent to staff.';
            }
        }
    }

    if ($isClient && $action === 'accept_proposal') {
        if ($currentProposal === null) {
            $error = 'No proposal available yet.';
        } else {
            $agreeChecked = !empty($_POST['customer_agreement']);
            $typedName = trim((string)($_POST['customer_typed_name'] ?? ''));
            if (!$agreeChecked || $typedName === '') {
                $error = 'Please check the agreement box and type your name.';
            } else {
                $proposal = projectNormalizeProposal($currentProposal, $request, $projectId);
                $proposal['customer_agreed'] = true;
                $proposal['customer_typed_name'] = $typedName;
                $proposal['customer_acceptance_date'] = projectNow();
                $proposal['customer_notes'] = trim((string)($_POST['customer_notes'] ?? ''));
                $proposal['updated_at'] = projectNow();

                if (!empty($proposal['staff_agreed'])) {
                    $proposal['status'] = 'accepted';
                    $request['status'] = 'accepted';
                } else {
                    $proposal['status'] = 'sent';
                    $request['status'] = 'proposal_sent';
                }

                projectPersistProposal($allProposals, $proposal);
                $currentProposal = $proposal;
                $request['updated_at'] = projectNow();
                projectAppendTimeline($request, 'customer-accepted-proposal', 'Customer accepted proposal');

                $host = $_SERVER['HTTP_HOST'] ?? 'runlevel.systems';
                $projectUrl = 'https://' . $host . '/project.php?id=' . urlencode($projectId);
                send_proposal_accepted_customer_email($proposal['client_email'], $proposal['client_name'], $projectId, $proposal['proposal_id'], $projectUrl);
                send_proposal_accepted_staff_email($projectId, $proposal['proposal_id'], (string)($request['name'] ?? ''), $projectUrl);

                $notice = !empty($proposal['staff_agreed']) ? 'Proposal accepted and signoff complete. Awaiting payment.' : 'Proposal accepted. Waiting for staff approval.';
            }
        }
    }

    if ($isStaff && $action === 'staff_accept_proposal') {
        if ($currentProposal === null) {
            $error = 'No proposal available yet.';
        } else {
            $agreeChecked = !empty($_POST['staff_agreement']);
            $typedName = trim((string)($_POST['staff_typed_name'] ?? ''));
            if (!$agreeChecked || $typedName === '') {
                $error = 'Please check the agreement box and type your name.';
            } else {
                $proposal = projectNormalizeProposal($currentProposal, $request, $projectId);
                $proposal['staff_agreed'] = true;
                $proposal['staff_typed_name'] = $typedName;
                $proposal['staff_acceptance_date'] = projectNow();
                $proposal['updated_at'] = projectNow();

                if (!empty($proposal['customer_agreed'])) {
                    $proposal['status'] = 'accepted';
                    $request['status'] = 'accepted';
                    projectAppendTimeline($request, 'proposal-accepted', 'Proposal accepted by both parties');
                    $notice = 'Staff acceptance saved. Proposal is now accepted and awaiting payment.';
                } else {
                    $proposal['status'] = 'sent';
                    $request['status'] = 'proposal_sent';
                    projectAppendTimeline($request, 'staff-accepted-proposal', 'Staff approved proposal signoff');
                    $notice = 'Staff acceptance saved.';
                }

                projectPersistProposal($allProposals, $proposal);
                $currentProposal = $proposal;
                $request['updated_at'] = projectNow();
            }
        }
    }

    if ($isStaff && in_array($action, ['create_invoice', 'send_payment_link', 'record_payment', 'mark_project_active', 'mark_project_completed', 'set_waiting_customer'], true)) {
        if ($action === 'create_invoice') {
            $request['invoice_reference'] = trim((string)($_POST['invoice_reference'] ?? ''));
            if ($request['invoice_reference'] === '') {
                $request['invoice_reference'] = projectGenerateId('INV', 6);
            }
            $request['invoice_status'] = 'draft';
            $request['amount_due'] = trim((string)($_POST['amount_due'] ?? ($currentProposal['amount_due_to_start'] ?? '')));
            $request['payment_notes'] = trim((string)($_POST['payment_notes'] ?? ''));
            $request['updated_at'] = projectNow();
            projectAppendTimeline($request, 'invoice-created', 'Invoice created');
            $notice = 'Invoice created.';
        }

        if ($action === 'send_payment_link') {
            $paymentLink = trim((string)($_POST['payment_link'] ?? ''));
            if ($paymentLink === '' || !filter_var($paymentLink, FILTER_VALIDATE_URL)) {
                $error = 'Enter a valid payment link.';
            } else {
                $request['payment_link'] = $paymentLink;
                $request['invoice_status'] = 'payment_link_sent';
                $request['invoice_sent_at'] = projectNow();
                $request['updated_at'] = projectNow();
                projectAppendTimeline($request, 'payment-link-sent', 'Payment link sent');
                $notice = 'Payment link saved.';
            }
        }

        if ($action === 'record_payment' && $error === '') {
            $paymentAmount = trim((string)($_POST['payment_amount'] ?? ''));
            $paymentFor = trim((string)($_POST['payment_for'] ?? 'start_payment'));
            $validPaymentFor = ['start_payment', 'milestone_payment', 'final_payment'];
            if (!in_array($paymentFor, $validPaymentFor, true)) {
                $paymentFor = 'start_payment';
            }

            $paymentRecord = [
                'payment_id' => projectGenerateId('PAY', 7),
                'payment_for' => $paymentFor,
                'milestone_index' => max(0, (int)($_POST['payment_milestone_index'] ?? 0)),
                'amount' => $paymentAmount,
                'status' => 'recorded',
                'paypal_transaction_id' => trim((string)($_POST['paypal_transaction_id'] ?? '')),
                'paypal_invoice_id' => trim((string)($_POST['paypal_invoice_id'] ?? '')),
                'recorded_at' => projectNow(),
                'recorded_by' => $myUsername,
                'note' => trim((string)($_POST['payment_note'] ?? '')),
            ];

            $request['payment_records'][] = $paymentRecord;
            $request['amount_paid'] = $paymentAmount;
            $request['payment_received_at'] = projectNow();
            $request['invoice_status'] = 'paid';
            $request['updated_at'] = projectNow();

            if ($paymentFor === 'start_payment') {
                if ($currentProposal !== null) {
                    $currentProposal['status'] = 'payment_received';
                    $currentProposal['updated_at'] = projectNow();
                    projectPersistProposal($allProposals, $currentProposal);
                }
                projectAppendTimeline($request, 'payment-received', 'Starting payment received');
            } elseif ($paymentFor === 'milestone_payment') {
                projectAppendTimeline($request, 'milestone-paid', 'Milestone payment recorded');
            } else {
                projectAppendTimeline($request, 'final-payment', 'Final payment recorded');
            }

            $host = $_SERVER['HTTP_HOST'] ?? 'runlevel.systems';
            $projectUrl = 'https://' . $host . '/project.php?id=' . urlencode($projectId);
            $proposalId = (string)($currentProposal['proposal_id'] ?? '');
            send_payment_recorded_customer_email((string)($request['email'] ?? ''), (string)($request['name'] ?? ''), $projectId, $proposalId, $paymentRecord['payment_id'], $paymentAmount, 'recorded', $projectUrl);
            send_payment_recorded_staff_email($projectId, $proposalId, $paymentRecord['payment_id'], $paymentAmount, 'recorded', $projectUrl);

            $notice = 'Payment recorded.';
        }

        if ($action === 'mark_project_active' && $error === '') {
            $hasStartPayment = false;
            foreach (($request['payment_records'] ?? []) as $payment) {
                if (!is_array($payment)) {
                    continue;
                }
                if ((string)($payment['payment_for'] ?? '') === 'start_payment') {
                    $hasStartPayment = true;
                    break;
                }
            }
            if (!$hasStartPayment) {
                $error = 'Record the starting payment before marking project active.';
            } else {
                $request['status'] = 'active';
                $request['project_active_at'] = projectNow();
                $request['updated_at'] = projectNow();
                if ($currentProposal !== null) {
                    $currentProposal['status'] = !empty($currentProposal['milestones']) ? 'milestones' : 'project_active';
                    $currentProposal['updated_at'] = projectNow();
                    projectPersistProposal($allProposals, $currentProposal);
                }
                projectAppendTimeline($request, 'project-active', 'Project marked active');
                $notice = 'Project marked active.';
            }
        }

        if ($action === 'mark_project_completed' && $error === '') {
            $request['status'] = 'completed';
            $request['project_completed_at'] = projectNow();
            $request['updated_at'] = projectNow();
            if ($currentProposal !== null) {
                $currentProposal['status'] = 'completed';
                $currentProposal['updated_at'] = projectNow();
                projectPersistProposal($allProposals, $currentProposal);
            }
            projectAppendTimeline($request, 'project-completed', 'Project marked complete');
            $notice = 'Project marked completed.';
        }

        if ($action === 'set_waiting_customer' && $error === '') {
            $request['status'] = 'needs_info';
            $request['updated_at'] = projectNow();
            projectAppendTimeline($request, 'waiting-customer', 'Waiting on customer response');
            $notice = 'Project status set to waiting on customer.';
        }
    }

    if ($isStaff && $action === 'update_milestone_status' && $currentProposal !== null) {
        $idx = max(0, (int)($_POST['milestone_index'] ?? 0));
        $newStatus = trim((string)($_POST['milestone_status'] ?? 'Not Started'));
        if (!in_array($newStatus, $milestoneStatuses, true)) {
            $newStatus = 'Not Started';
        }

        $proposal = projectNormalizeProposal($currentProposal, $request, $projectId);
        if (isset($proposal['milestones'][$idx])) {
            $proposal['milestones'][$idx]['status'] = $newStatus;
            $proposal['updated_at'] = projectNow();
            if ($newStatus === 'Ready For Review') {
                $request['status'] = 'needs_info';
                projectAppendTimeline($request, 'milestone-ready-for-review', ($proposal['milestones'][$idx]['name'] ?? 'Milestone') . ' ready for review');
            }
            if ($newStatus === 'Paid') {
                projectAppendTimeline($request, 'milestone-paid', ($proposal['milestones'][$idx]['name'] ?? 'Milestone') . ' marked paid');
            }
            if ($newStatus === 'Complete') {
                projectAppendTimeline($request, 'milestone-complete', ($proposal['milestones'][$idx]['name'] ?? 'Milestone') . ' completed');
            }
            projectPersistProposal($allProposals, $proposal);
            $currentProposal = $proposal;
            $request['updated_at'] = projectNow();
            $notice = 'Milestone updated.';
        }
    }

    if ($action === 'upload_file') {
        if (isset($_FILES['project_file'])) {
            $uploadError = '';
            $stored = projectStoreUpload($projectId, $_FILES['project_file'], $allowedUploadExt, $uploadError);
            if ($stored === null) {
                $error = $uploadError;
            } else {
                $visibility = $isStaff ? trim((string)($_POST['file_visibility'] ?? 'customer_visible')) : 'customer_visible';
                if (!in_array($visibility, ['staff_only', 'customer_visible'], true)) {
                    $visibility = 'customer_visible';
                }
                $stored['uploaded_by'] = $myUsername;
                $stored['uploaded_by_role'] = $role;
                $stored['description'] = trim((string)($_POST['file_description'] ?? ''));
                $stored['visibility'] = $visibility;
                $request['project_files'][] = $stored;
                $request['updated_at'] = projectNow();
                projectAppendTimeline($request, 'files-uploaded', 'File uploaded: ' . $stored['original_filename'], $visibility === 'staff_only' ? 'staff_only' : 'customer_visible');
                $notice = 'File uploaded.';
            }
        }
    }

    if (in_array($action, ['send_message', 'send_message_email'], true)) {
        $messageText = trim((string)($_POST['message_text'] ?? ''));
        if ($messageText === '') {
            $error = 'Enter a message before sending.';
        } else {
            $visibility = $isStaff ? trim((string)($_POST['message_visibility'] ?? 'customer_visible')) : 'customer_visible';
            if (!in_array($visibility, ['staff_only', 'customer_visible'], true)) {
                $visibility = 'customer_visible';
            }

            $attachmentIds = [];
            if (isset($_FILES['message_attachment']) && (int)($_FILES['message_attachment']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                $uploadError = '';
                $stored = projectStoreUpload($projectId, $_FILES['message_attachment'], $allowedUploadExt, $uploadError);
                if ($stored === null) {
                    $error = $uploadError;
                } else {
                    $stored['uploaded_by'] = $myUsername;
                    $stored['uploaded_by_role'] = $role;
                    $stored['description'] = 'Message attachment';
                    $stored['visibility'] = $visibility;
                    $request['project_files'][] = $stored;
                    $attachmentIds[] = $stored['file_id'];
                }
            }

            if ($error === '') {
                $sendEmailToo = $isStaff && $action === 'send_message_email';
                $request['messages'][] = [
                    'message_id' => projectGenerateId('MSG', 8),
                    'sender' => $myUsername,
                    'role' => $role,
                    'sent_at' => projectNow(),
                    'text' => $messageText,
                    'visibility' => $visibility,
                    'attachments' => $attachmentIds,
                    'email_sent' => $sendEmailToo,
                ];
                $request['updated_at'] = projectNow();
                projectAppendTimeline($request, 'message-sent', ($isStaff ? 'Staff' : 'Customer') . ' message sent', $visibility === 'staff_only' ? 'staff_only' : 'customer_visible');

                if ($sendEmailToo) {
                    $recipient = trim((string)($request['email'] ?? ''));
                    if ($recipient !== '' && filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                        $subject = 'Project Message - ' . $projectId;
                        $body = "Project: {$projectId}\nSender: {$myUsername} ({$role})\n\n{$messageText}";
                        send_email($recipient, $subject, nl2br(htmlspecialchars($body, ENT_QUOTES, 'UTF-8')), ['context' => 'project-message']);
                    }
                }
                $notice = 'Message sent.';
            }
        }
    }

    if ($error === '') {
        $allRequests[$requestIndex] = $request;
        portalSaveProjectRequests($allRequests);
        portalSaveProposals($allProposals);

        $allRequests = portalLoadProjectRequests();
        foreach ($allRequests as $idx => $row) {
            if (portalGetRequestDisplayId((array)$row) === $projectId) {
                $request = projectNormalizeRequest((array)$row);
                $requestIndex = $idx;
                break;
            }
        }
        $allProposals = portalLoadProposals();
        $currentProposal = null;
        foreach ($allProposals as $row) {
            if ((string)($row['request_id'] ?? '') !== $projectId) {
                continue;
            }
            $candidate = projectNormalizeProposal((array)$row, $request, $projectId);
            if ($currentProposal === null) {
                $currentProposal = $candidate;
                continue;
            }
            $currentTs = strtotime((string)($currentProposal['updated_at'] ?? '')) ?: 0;
            $candidateTs = strtotime((string)($candidate['updated_at'] ?? '')) ?: 0;
            if ($candidateTs >= $currentTs) {
                $currentProposal = $candidate;
            }
        }
    }
}

$current_page = 'dashboard';
$header_class = 'inner-header';

$trackerSteps = (!$notFound && !$forbidden) ? projectCurrentWorkflowStep($request, $currentProposal) : [];
$nextAction = (!$notFound && !$forbidden) ? ($request['next_action_override'] !== '' ? $request['next_action_override'] : projectNextAction($request, $currentProposal, $isStaff)) : '';
$openSections = (!$notFound && !$forbidden) ? projectOpenSections($request, $currentProposal) : [];

$timeline = [];
if (!$notFound && !$forbidden) {
    if (!empty($request['created_at'])) {
        $timeline[] = ['timestamp' => (string)$request['created_at'], 'label' => 'Project request submitted', 'visibility' => 'customer_visible'];
    }
    foreach (($request['timeline_events'] ?? []) as $event) {
        if (!is_array($event)) {
            continue;
        }
        $timeline[] = [
            'timestamp' => (string)($event['timestamp'] ?? projectNow()),
            'label' => (string)($event['label'] ?? (string)($event['event'] ?? 'Project event')),
            'visibility' => (string)($event['visibility'] ?? 'customer_visible'),
        ];
    }
    usort($timeline, function ($a, $b) {
        return (strtotime((string)$b['timestamp']) ?: 0) <=> (strtotime((string)$a['timestamp']) ?: 0);
    });
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="/assets/images/RL-icon.png">
    <title>Project <?php echo pe($projectId); ?> | Runlevel Systems</title>
    <link href="assets/css/coreloop.css?v=clean-20260602" rel="stylesheet">
    <style>
        .pw-wrap{padding:24px 0 70px;}
        .pw-card{background:#0c1729;border:1px solid rgba(54,243,255,.18);border-radius:10px;padding:16px;margin-bottom:12px;}
        .pw-header{display:flex;justify-content:space-between;flex-wrap:wrap;gap:10px;align-items:flex-start;}
        .pw-id{font-family:monospace;color:#ffc600;font-size:1rem;font-weight:700;}
        .pw-name{color:#eaf3ff;font-size:1.15rem;font-weight:700;margin:4px 0;}
        .status-pill{display:inline-block;border:1px solid rgba(54,243,255,.4);padding:4px 10px;border-radius:999px;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;}
        .pw-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;}
        @media(max-width:760px){.pw-grid{grid-template-columns:1fr;}}
        .pw-lbl{color:#7a9ac0;font-size:.7rem;text-transform:uppercase;letter-spacing:.04em;display:block;margin-bottom:3px;}
        .pw-help{color:#5a7a9e;font-size:.75rem;line-height:1.4;margin-top:2px;}
        .pw-val{color:#eaf3ff;font-size:.86rem;white-space:pre-wrap;word-break:break-word;}
        .pw-input,.pw-textarea,.pw-select{width:100%;background:#09111d;border:1px solid rgba(54,243,255,.22);border-radius:6px;color:#eaf3ff;padding:8px 10px;font-size:.84rem;}
        .pw-textarea{min-height:90px;resize:vertical;}
        .btn{display:inline-block;border:none;border-radius:6px;padding:8px 12px;font-size:.8rem;font-weight:700;cursor:pointer;text-decoration:none;}
        .btn-blue{background:#0a84ff;color:#fff;}
        .btn-gold{background:#ffc600;color:#08111f;}
        .btn-teal{background:rgba(54,243,255,.11);border:1px solid rgba(54,243,255,.3);color:#36f3ff;}
        .btn-green{background:rgba(34,197,94,.14);border:1px solid rgba(34,197,94,.35);color:#6ee7b7;}
        .btn-red{background:rgba(239,68,68,.14);border:1px solid rgba(239,68,68,.35);color:#fca5a5;}
        .notice{background:rgba(34,197,94,.14);border:1px solid rgba(34,197,94,.35);color:#86efac;border-radius:8px;padding:10px 12px;margin-bottom:12px;font-size:.83rem;}
        .error{background:rgba(239,68,68,.14);border:1px solid rgba(239,68,68,.35);color:#fecaca;border-radius:8px;padding:10px 12px;margin-bottom:12px;font-size:.83rem;}
        .tracker{display:grid;grid-template-columns:repeat(7,minmax(0,1fr));gap:6px;}
        .tracker-step{border:1px solid rgba(54,243,255,.18);border-radius:8px;padding:8px 6px;text-align:center;font-size:.72rem;color:#7a9ac0;background:#09111d;line-height:1.3;}
        .tracker-step.completed{border-color:rgba(34,197,94,.45);background:rgba(34,197,94,.12);color:#86efac;}
        .tracker-step.current{border-color:rgba(255,198,0,.45);background:rgba(255,198,0,.14);color:#ffc600;}
        .tracker-step.future{opacity:.85;}
        .tracker-step.partial{border-style:dashed;}
        @media(max-width:980px){.tracker{grid-template-columns:repeat(2,minmax(0,1fr));}}
        .next-action{background:#09111d;border:1px solid rgba(54,243,255,.18);border-radius:8px;padding:10px;}
        details.pw-section{margin-bottom:12px;}
        details.pw-section summary{cursor:pointer;list-style:none;background:#0c1729;border:1px solid rgba(54,243,255,.18);border-radius:10px;padding:13px 16px;color:#36f3ff;font-size:.82rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;display:flex;justify-content:space-between;align-items:center;}
        details.pw-section summary::-webkit-details-marker{display:none;}
        details.pw-section summary::after{content:'▼';font-size:.65rem;color:#5a7a9e;}
        details.pw-section[open] summary{border-bottom-color:transparent;border-radius:10px 10px 0 0;}
        details.pw-section[open] summary::after{content:'▲';}
        .pw-body{background:#0c1729;border:1px solid rgba(54,243,255,.18);border-top:none;border-radius:0 0 10px 10px;padding:14px 16px;}
        .mini-card{background:#09111d;border:1px solid rgba(54,243,255,.14);border-radius:8px;padding:10px;}
        .thread-item{background:#09111d;border:1px solid rgba(54,243,255,.14);border-radius:8px;padding:10px;margin-bottom:8px;}
        .thread-meta{display:flex;gap:10px;flex-wrap:wrap;color:#7a9ac0;font-size:.73rem;margin-bottom:6px;}
        .timeline{list-style:none;margin:0;padding:0;}
        .timeline li{padding:8px 0;border-bottom:1px solid rgba(54,243,255,.1);}
        .timeline li:last-child{border-bottom:none;}
        @media print{
            .no-print,#header,#footer-widget,footer,nav{display:none !important;}
            body{background:#fff;color:#111;}
            .pw-card,.pw-body,.mini-card,.thread-item{background:#fff;border:1px solid #ccc;color:#111;}
            .pw-val,.pw-name,.pw-id{color:#111 !important;}
            .staff-only{display:none !important;}
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>
<?php include __DIR__ . '/includes/navigation.php'; ?>
<section class="pw-wrap">
<div class="container">
    <div class="no-print" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;margin-bottom:10px;">
        <a href="/dashboard.php" style="color:#36f3ff;font-size:.84rem;">← Dashboard</a>
        <?php if ($isStaff): ?><a href="/staff/estimate-requests.php" style="color:#7a9ac0;font-size:.78rem;">All Requests</a><?php endif; ?>
    </div>

<?php if ($notFound): ?>
    <div class="pw-card"><p class="pw-val">Project not found.</p></div>
<?php elseif ($forbidden): ?>
    <div class="pw-card"><p class="pw-val">You do not have permission to view this project.</p></div>
<?php else: ?>

    <?php if ($notice !== ''): ?><div class="notice no-print"><?php echo pe($notice); ?></div><?php endif; ?>
    <?php if ($error !== ''): ?><div class="error no-print"><?php echo pe($error); ?></div><?php endif; ?>

    <div class="pw-card pw-header">
        <div>
            <div class="pw-id"><?php echo pe($projectId); ?></div>
            <div class="pw-name"><?php echo pe((string)($currentProposal['project_title'] ?? $request['project_type'] ?? 'Project')); ?></div>
            <div class="pw-val" style="white-space:normal;"><?php echo pe((string)($request['name'] ?? '')); ?><?php if (!empty($request['email'])): ?> · <?php echo pe((string)$request['email']); ?><?php endif; ?></div>
        </div>
        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;">
            <?php echo projectStatusBadge($request, $currentProposal); ?>
            <button class="btn btn-teal no-print" type="button" onclick="window.print();">Print / PDF</button>
        </div>
    </div>

    <div class="pw-card">
        <div class="pw-lbl" style="margin-bottom:8px;">Project Progress</div>
        <div class="tracker">
            <?php foreach ($trackerSteps as $step): ?>
                <div class="tracker-step <?php echo pe($step['state']); ?><?php echo !empty($step['partial']) ? ' partial' : ''; ?>">
                    <?php echo pe($step['label']); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="pw-card">
        <div class="pw-lbl">Next Action</div>
        <div class="next-action pw-val"><?php echo pe($nextAction); ?></div>
        <?php if ($isStaff): ?>
            <form method="post" class="no-print" style="margin-top:8px;display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap;">
                <input type="hidden" name="action" value="save_request_next_action">
                <div style="flex:1;min-width:220px;">
                    <label class="pw-lbl">Override Next Action</label>
                    <input class="pw-input" type="text" name="next_action_override" value="<?php echo pe((string)($request['next_action_override'] ?? '')); ?>" placeholder="Leave blank to use automatic next action">
                </div>
                <button class="btn btn-blue" type="submit">Save</button>
            </form>
        <?php endif; ?>
    </div>

    <details class="pw-section" <?php echo !empty($openSections['request']) ? 'open' : ''; ?>>
        <summary>Project Request</summary>
        <div class="pw-body">
            <div class="pw-grid">
                <div class="mini-card"><span class="pw-lbl">Project Type</span><div class="pw-val"><?php echo pe((string)($request['project_type'] ?? '—')); ?></div><div class="pw-help">What kind of work is this? Example: website, mobile app, business software, training simulator, game/server script, quick fix, project rescue.</div></div>
                <div class="mini-card"><span class="pw-lbl">Budget Comfort</span><div class="pw-val"><?php echo pe((string)($request['budget_comfort'] ?? '—')); ?></div><div class="pw-help">This helps us recommend a realistic solution. It is not a final price.</div></div>
                <div class="mini-card"><span class="pw-lbl">Timeline</span><div class="pw-val"><?php echo pe((string)($request['timeline'] ?? '—')); ?></div><div class="pw-help">When would you ideally like this completed?</div></div>
                <div class="mini-card"><span class="pw-lbl">Repository / File Link</span><div class="pw-val"><?php echo !empty($request['repo_link']) ? '<a href="' . pe((string)$request['repo_link']) . '" target="_blank" rel="noopener noreferrer" style="color:#36f3ff;">' . pe((string)$request['repo_link']) . '</a>' : '—'; ?></div><div class="pw-help">Optional link to code, website, repo, screenshots, files, or other information.</div></div>
            </div>
            <div class="pw-grid" style="margin-top:10px;">
                <div class="mini-card">
                    <span class="pw-lbl">What Is Needed</span>
                    <div class="pw-val"><?php echo pe((string)($request['what_is_needed'] ?? $request['request_summary'] ?? $request['problem_to_solve'] ?? $request['description'] ?? '—')); ?></div>
                    <div class="pw-help">Describe what you need built, fixed, improved, or finished.</div>
                </div>
            </div>
            <div class="mini-card" style="margin-top:10px;"><span class="pw-lbl">Existing Work</span><div class="pw-val"><?php echo pe((string)($request['existing_work'] ?? '—')); ?></div><div class="pw-help">Does this project already exist, or are we starting from scratch?</div></div>
        </div>
    </details>

    <details class="pw-section" <?php echo !empty($openSections['proposal']) ? 'open' : ''; ?>>
        <summary>Proposal <?php if ($currentProposal !== null): ?><span style="font-weight:400;text-transform:none;color:#7a9ac0;">· <?php echo pe(projectProposalStatusLabel((string)$currentProposal['status'])); ?></span><?php endif; ?></summary>
        <div class="pw-body">
            <?php if ($currentProposal === null): ?>
                <p class="pw-val">No proposal yet.</p>
            <?php else: ?>
                <div class="pw-grid">
                    <div class="mini-card"><span class="pw-lbl">Proposal Summary</span><div class="pw-val"><?php echo pe((string)$currentProposal['proposal_summary']); ?></div><div class="pw-help">Short overview of what Runlevel Systems is offering to do.</div></div>
                    <div class="mini-card"><span class="pw-lbl">Timeline Estimate</span><div class="pw-val"><?php echo pe((string)$currentProposal['timeline_estimate']); ?></div><div class="pw-help">Expected completion time or schedule.</div></div>
                    <div class="mini-card"><span class="pw-lbl">Amount Due To Start</span><div class="pw-val"><?php echo pe((string)$currentProposal['amount_due_to_start']); ?></div><div class="pw-help">Payment required before work begins.</div></div>
                    <div class="mini-card"><span class="pw-lbl">Total Project Amount</span><div class="pw-val"><?php echo pe((string)$currentProposal['total_project_amount']); ?></div><div class="pw-help">Estimated total price for the agreed work.</div></div>
                </div>
                <div class="pw-grid" style="margin-top:10px;">
                    <div class="mini-card"><span class="pw-lbl">Proposed Work</span><div class="pw-val"><?php echo pe((string)$currentProposal['proposed_work']); ?></div><div class="pw-help">Plain-English explanation of the work we will perform.</div></div>
                    <div class="mini-card"><span class="pw-lbl">Deliverables</span><div class="pw-val"><?php echo pe((string)$currentProposal['deliverables']); ?></div><div class="pw-help">What the customer will receive when the work is completed.</div></div>
                </div>
                <div class="pw-grid" style="margin-top:10px;">
                    <div class="mini-card"><span class="pw-lbl">Out Of Scope</span><div class="pw-val"><?php echo pe((string)$currentProposal['out_of_scope']); ?></div><div class="pw-help">What is not included unless separately approved.</div></div>
                    <div class="mini-card"><span class="pw-lbl">Customer Responsibilities</span><div class="pw-val"><?php echo pe((string)$currentProposal['customer_responsibilities']); ?></div><div class="pw-help">What the customer must provide, such as files, logins, screenshots, code access, app store accounts, or feedback.</div></div>
                </div>
                <div class="pw-grid" style="margin-top:10px;">
                    <div class="mini-card"><span class="pw-lbl">Revision Terms</span><div class="pw-val"><?php echo pe((string)$currentProposal['revision_terms']); ?></div><div class="pw-help">What small corrections are included and what counts as new work.</div></div>
                    <div class="mini-card"><span class="pw-lbl">Terms Summary</span><div class="pw-val"><?php echo pe((string)$currentProposal['terms_summary']); ?></div><div class="pw-help">Short summary of payment, acceptance, revisions, and project limits.</div></div>
                </div>
                <div class="mini-card" style="margin-top:10px;"><span class="pw-lbl">Full Terms Link</span><div class="pw-val"><?php if ((string)$currentProposal['full_terms_link'] !== ''): ?><a href="<?php echo pe((string)$currentProposal['full_terms_link']); ?>" target="_blank" rel="noopener noreferrer" style="color:#36f3ff;">View Terms of Service</a><?php else: ?>—<?php endif; ?></div></div>

                <div class="mini-card" style="margin-top:10px;">
                    <span class="pw-lbl">Milestones</span>
                    <?php if (empty($currentProposal['milestones'])): ?>
                        <div class="pw-val">No milestones for this project.</div>
                    <?php else: ?>
                        <?php foreach ($currentProposal['milestones'] as $index => $milestone): ?>
                            <div style="margin-top:8px;border-top:1px solid rgba(54,243,255,.12);padding-top:8px;">
                                <div class="pw-val" style="font-weight:700;">Milestone <?php echo (int)$index + 1; ?>: <?php echo pe((string)$milestone['name']); ?></div>
                                <div class="pw-help"><?php echo pe((string)$milestone['description']); ?></div>
                                <div class="pw-help">Deliverables: <?php echo pe((string)$milestone['deliverables']); ?></div>
                                <div class="pw-help">Amount Due Upon Completion: <?php echo pe((string)$milestone['amount']); ?></div>
                                <div class="pw-help">Payment Trigger: <?php echo pe((string)$milestone['payment_trigger']); ?></div>
                                <div class="pw-help">Estimated Date / Timeframe: <?php echo pe((string)$milestone['estimate']); ?></div>
                                <div class="pw-help">Status: <?php echo pe((string)$milestone['status']); ?></div>
                                <?php if ($isStaff): ?>
                                    <form method="post" class="no-print" style="margin-top:6px;display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap;">
                                        <input type="hidden" name="action" value="update_milestone_status">
                                        <input type="hidden" name="milestone_index" value="<?php echo (int)$index; ?>">
                                        <div>
                                            <label class="pw-lbl">Milestone Status</label>
                                            <select name="milestone_status" class="pw-select">
                                                <?php foreach ($milestoneStatuses as $statusOption): ?>
                                                    <option value="<?php echo pe($statusOption); ?>" <?php echo $statusOption === (string)$milestone['status'] ? 'selected' : ''; ?>><?php echo pe($statusOption); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <button class="btn btn-teal" type="submit">Update</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="mini-card" style="margin-top:10px;">
                    <span class="pw-lbl">Agreement / Signoff</span>
                    <div class="pw-grid">
                        <div>
                            <div class="pw-help">Customer Agreement: <?php echo !empty($currentProposal['customer_agreed']) ? 'Accepted' : 'Pending'; ?></div>
                            <div class="pw-help">Customer Typed Name: <?php echo pe((string)$currentProposal['customer_typed_name']); ?></div>
                            <div class="pw-help">Customer Acceptance Date: <?php echo pe((string)$currentProposal['customer_acceptance_date']); ?></div>
                            <div class="pw-help">Customer Notes: <?php echo pe((string)$currentProposal['customer_notes']); ?></div>
                        </div>
                        <div>
                            <div class="pw-help">Staff Agreement: <?php echo !empty($currentProposal['staff_agreed']) ? 'Accepted' : 'Pending'; ?></div>
                            <div class="pw-help">Staff Typed Name: <?php echo pe((string)$currentProposal['staff_typed_name']); ?></div>
                            <div class="pw-help">Staff Acceptance Date: <?php echo pe((string)$currentProposal['staff_acceptance_date']); ?></div>
                        </div>
                    </div>
                </div>

                <?php if (!empty($currentProposal['change_requests'])): ?>
                    <div class="mini-card" style="margin-top:10px;">
                        <span class="pw-lbl">Change Request History</span>
                        <?php foreach (array_reverse($currentProposal['change_requests']) as $change): ?>
                            <div style="margin-top:8px;border-top:1px solid rgba(54,243,255,.12);padding-top:8px;">
                                <div class="pw-help"><?php echo pe((string)$change['requested_at']); ?> · <?php echo pe((string)$change['requested_by_role']); ?> · <?php echo pe((string)$change['requested_by']); ?></div>
                                <div class="pw-val"><?php echo pe((string)$change['request_text']); ?></div>
                                <?php if ((string)$change['budget_concern'] !== ''): ?><div class="pw-help">Budget concern: <?php echo pe((string)$change['budget_concern']); ?></div><?php endif; ?>
                                <?php if ((string)$change['timeline_concern'] !== ''): ?><div class="pw-help">Timeline concern: <?php echo pe((string)$change['timeline_concern']); ?></div><?php endif; ?>
                                <?php if ((string)$change['deliverable_change'] !== ''): ?><div class="pw-help">Deliverable change: <?php echo pe((string)$change['deliverable_change']); ?></div><?php endif; ?>
                                <?php if ((string)$change['other_note'] !== ''): ?><div class="pw-help">Other note: <?php echo pe((string)$change['other_note']); ?></div><?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($isClient && $currentProposal !== null): ?>
                <div class="pw-grid no-print" style="margin-top:12px;">
                    <div class="mini-card">
                        <form method="post">
                            <input type="hidden" name="action" value="accept_proposal">
                            <div class="check-block"><input type="checkbox" name="customer_agreement" value="1" required><label>I have reviewed this proposal and agree to the listed work, deliverables, price, payment terms, revision terms, and applicable Runlevel Systems Terms of Service.</label></div>
                            <label class="pw-lbl" style="margin-top:8px;">Customer Typed Name</label>
                            <input class="pw-input" type="text" name="customer_typed_name" required>
                            <label class="pw-lbl" style="margin-top:8px;">Customer Notes (Optional)</label>
                            <textarea class="pw-textarea" name="customer_notes"></textarea>
                            <div style="margin-top:8px;"><button class="btn btn-green" type="submit">Accept Proposal</button></div>
                        </form>
                    </div>
                    <div class="mini-card">
                        <form method="post">
                            <input type="hidden" name="action" value="request_changes">
                            <label class="pw-lbl">What would you like changed?</label>
                            <textarea class="pw-textarea" name="change_request_text" required></textarea>
                            <label class="pw-lbl" style="margin-top:8px;">Budget concern (optional)</label>
                            <input class="pw-input" type="text" name="change_budget_concern">
                            <label class="pw-lbl" style="margin-top:8px;">Timeline concern (optional)</label>
                            <input class="pw-input" type="text" name="change_timeline_concern">
                            <label class="pw-lbl" style="margin-top:8px;">Deliverable change (optional)</label>
                            <input class="pw-input" type="text" name="change_deliverable_change">
                            <label class="pw-lbl" style="margin-top:8px;">Other note (optional)</label>
                            <textarea class="pw-textarea" name="change_other_note"></textarea>
                            <div style="margin-top:8px;"><button class="btn btn-gold" type="submit">Request Changes</button></div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($isStaff): ?>
                <details class="no-print" style="margin-top:12px;">
                    <summary style="cursor:pointer;color:#36f3ff;font-size:.8rem;">Edit Proposal</summary>
                    <div style="padding-top:10px;">
                        <form method="post">
                            <input type="hidden" name="action" value="save_proposal">
                            <div class="pw-grid">
                                <div><label class="pw-lbl">Client Name</label><input class="pw-input" type="text" name="client_name" value="<?php echo pe((string)($currentProposal['client_name'] ?? $request['name'] ?? '')); ?>"></div>
                                <div><label class="pw-lbl">Client Email</label><input class="pw-input" type="email" name="client_email" value="<?php echo pe((string)($currentProposal['client_email'] ?? $request['email'] ?? '')); ?>"></div>
                                <div><label class="pw-lbl">Project Title</label><input class="pw-input" type="text" name="project_title" value="<?php echo pe((string)($currentProposal['project_title'] ?? $request['project_type'] ?? 'Project')); ?>"></div>
                                <div><label class="pw-lbl">Milestones</label><select class="pw-select" name="milestone_count" id="milestone_count" onchange="toggleMilestones();"><?php for ($i = 0; $i <= 5; $i++): ?><option value="<?php echo $i; ?>" <?php echo ((int)($currentProposal['milestone_count'] ?? 0) === $i) ? 'selected' : ''; ?>><?php echo $i; ?></option><?php endfor; ?></select></div>
                            </div>

                            <div style="margin-top:8px;"><label class="pw-lbl">Proposal Summary</label><textarea class="pw-textarea" name="proposal_summary"><?php echo pe((string)($currentProposal['proposal_summary'] ?? '')); ?></textarea></div>
                            <div style="margin-top:8px;"><label class="pw-lbl">Proposed Work</label><textarea class="pw-textarea" name="proposed_work"><?php echo pe((string)($currentProposal['proposed_work'] ?? '')); ?></textarea></div>
                            <div style="margin-top:8px;"><label class="pw-lbl">Deliverables</label><textarea class="pw-textarea" name="deliverables"><?php echo pe((string)($currentProposal['deliverables'] ?? '')); ?></textarea></div>
                            <div style="margin-top:8px;"><label class="pw-lbl">Out Of Scope</label><textarea class="pw-textarea" name="out_of_scope"><?php echo pe((string)($currentProposal['out_of_scope'] ?? '')); ?></textarea></div>

                            <div class="pw-grid" style="margin-top:8px;">
                                <div><label class="pw-lbl">Timeline Estimate</label><input class="pw-input" type="text" name="timeline_estimate" value="<?php echo pe((string)($currentProposal['timeline_estimate'] ?? $request['timeline'] ?? '')); ?>"></div>
                                <div><label class="pw-lbl">Amount Due To Start</label><input class="pw-input" type="text" name="amount_due_to_start" value="<?php echo pe((string)($currentProposal['amount_due_to_start'] ?? '')); ?>"></div>
                                <div><label class="pw-lbl">Total Project Amount</label><input class="pw-input" type="text" name="total_project_amount" value="<?php echo pe((string)($currentProposal['total_project_amount'] ?? '')); ?>"></div>
                                <div><label class="pw-lbl">Full Terms Link</label><input class="pw-input" type="url" name="full_terms_link" value="<?php echo pe((string)($currentProposal['full_terms_link'] ?? '/runlevel-terms.php')); ?>"></div>
                            </div>

                            <div style="margin-top:8px;"><label class="pw-lbl">Customer Responsibilities</label><textarea class="pw-textarea" name="customer_responsibilities"><?php echo pe((string)($currentProposal['customer_responsibilities'] ?? '')); ?></textarea></div>
                            <div style="margin-top:8px;"><label class="pw-lbl">Revision Terms</label><textarea class="pw-textarea" name="revision_terms"><?php echo pe((string)($currentProposal['revision_terms'] ?? '')); ?></textarea></div>
                            <div style="margin-top:8px;"><label class="pw-lbl">Terms Summary</label><textarea class="pw-textarea" name="terms_summary"><?php echo pe((string)($currentProposal['terms_summary'] ?? '')); ?></textarea></div>

                            <div id="milestone_fields" style="margin-top:8px;">
                                <?php for ($i = 0; $i < 5; $i++):
                                    $milestone = isset($currentProposal['milestones'][$i]) ? $currentProposal['milestones'][$i] : ['milestone_id' => '', 'name' => '', 'description' => '', 'deliverables' => '', 'amount' => '', 'payment_trigger' => '', 'estimate' => '', 'status' => 'Not Started'];
                                ?>
                                    <div class="mini-card milestone-row" data-index="<?php echo $i; ?>" style="margin-top:8px;">
                                        <input type="hidden" name="milestone_id[]" value="<?php echo pe((string)$milestone['milestone_id']); ?>">
                                        <div class="pw-grid">
                                            <div><label class="pw-lbl">Milestone Name</label><input class="pw-input" type="text" name="milestone_name[]" value="<?php echo pe((string)$milestone['name']); ?>"></div>
                                            <div><label class="pw-lbl">Milestone Amount</label><input class="pw-input" type="text" name="milestone_amount[]" value="<?php echo pe((string)$milestone['amount']); ?>"></div>
                                            <div><label class="pw-lbl">Payment Trigger</label><input class="pw-input" type="text" name="milestone_trigger[]" value="<?php echo pe((string)$milestone['payment_trigger']); ?>"></div>
                                            <div><label class="pw-lbl">Estimated Date / Timeframe</label><input class="pw-input" type="text" name="milestone_estimate[]" value="<?php echo pe((string)$milestone['estimate']); ?>"></div>
                                            <div><label class="pw-lbl">Milestone Status</label><select class="pw-select" name="milestone_status[]"><?php foreach ($milestoneStatuses as $statusOption): ?><option value="<?php echo pe($statusOption); ?>" <?php echo $statusOption === (string)$milestone['status'] ? 'selected' : ''; ?>><?php echo pe($statusOption); ?></option><?php endforeach; ?></select></div>
                                        </div>
                                        <div style="margin-top:8px;"><label class="pw-lbl">Milestone Description</label><textarea class="pw-textarea" name="milestone_description[]"><?php echo pe((string)$milestone['description']); ?></textarea></div>
                                        <div style="margin-top:8px;"><label class="pw-lbl">Deliverables For This Milestone</label><textarea class="pw-textarea" name="milestone_deliverables[]"><?php echo pe((string)$milestone['deliverables']); ?></textarea></div>
                                    </div>
                                <?php endfor; ?>
                            </div>

                            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;">
                                <button class="btn btn-blue" type="submit" name="action" value="save_proposal">Save Proposal</button>
                                <button class="btn btn-gold" type="submit" name="action" value="send_proposal">Send Proposal</button>
                            </div>
                        </form>

                        <form method="post" style="margin-top:10px;">
                            <input type="hidden" name="action" value="staff_accept_proposal">
                            <div class="check-block"><input type="checkbox" name="staff_agreement" value="1" required><label>Runlevel Systems approves this proposal and may begin work once required payment is received.</label></div>
                            <label class="pw-lbl" style="margin-top:8px;">Staff Typed Name</label>
                            <input class="pw-input" type="text" name="staff_typed_name" required>
                            <div style="margin-top:8px;"><button class="btn btn-green" type="submit">Save Staff Signoff</button></div>
                        </form>
                    </div>
                </details>
            <?php endif; ?>
        </div>
    </details>

    <details class="pw-section" <?php echo !empty($openSections['payments']) ? 'open' : ''; ?>>
        <summary>Payments</summary>
        <div class="pw-body">
            <div class="pw-grid">
                <div class="mini-card"><span class="pw-lbl">Amount Due To Start</span><div class="pw-val"><?php echo pe((string)($currentProposal['amount_due_to_start'] ?? '—')); ?></div></div>
                <div class="mini-card"><span class="pw-lbl">Payment Status</span><div class="pw-val"><?php echo pe((string)($request['invoice_status'] ?? 'Not started')); ?></div></div>
                <div class="mini-card"><span class="pw-lbl">PayPal Invoice Link / Payment Link</span><div class="pw-val"><?php if (!empty($request['payment_link'])): ?><a href="<?php echo pe((string)$request['payment_link']); ?>" target="_blank" rel="noopener noreferrer" style="color:#36f3ff;">Open Payment Link</a><?php else: ?>—<?php endif; ?></div></div>
                <div class="mini-card"><span class="pw-lbl">Invoice Reference</span><div class="pw-val"><?php echo pe((string)($request['invoice_reference'] ?? '—')); ?></div></div>
            </div>

            <div class="mini-card" style="margin-top:10px;">
                <span class="pw-lbl">Payment Records</span>
                <?php if (empty($request['payment_records'])): ?>
                    <div class="pw-val">No payments recorded yet.</div>
                <?php else: ?>
                    <?php foreach (array_reverse($request['payment_records']) as $payment): ?>
                        <div style="margin-top:8px;border-top:1px solid rgba(54,243,255,.12);padding-top:8px;">
                            <div class="pw-help">Payment ID: <?php echo pe((string)$payment['payment_id']); ?> · Type: <?php echo pe((string)$payment['payment_for']); ?> · Amount: <?php echo pe((string)$payment['amount']); ?></div>
                            <div class="pw-help">Recorded: <?php echo pe((string)$payment['recorded_at']); ?> · By: <?php echo pe((string)$payment['recorded_by']); ?></div>
                            <?php if ((string)$payment['paypal_transaction_id'] !== ''): ?><div class="pw-help">PayPal Transaction ID: <?php echo pe((string)$payment['paypal_transaction_id']); ?></div><?php endif; ?>
                            <?php if ((string)$payment['paypal_invoice_id'] !== ''): ?><div class="pw-help">PayPal Invoice ID: <?php echo pe((string)$payment['paypal_invoice_id']); ?></div><?php endif; ?>
                            <?php if ((string)$payment['note'] !== ''): ?><div class="pw-help">Note: <?php echo pe((string)$payment['note']); ?></div><?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if ($isStaff): ?>
                <div class="pw-grid no-print" style="margin-top:10px;">
                    <form method="post" class="mini-card">
                        <input type="hidden" name="action" value="create_invoice">
                        <label class="pw-lbl">Invoice Reference</label>
                        <input class="pw-input" type="text" name="invoice_reference" value="<?php echo pe((string)($request['invoice_reference'] ?? '')); ?>" placeholder="Auto if blank">
                        <label class="pw-lbl" style="margin-top:8px;">Amount Due</label>
                        <input class="pw-input" type="text" name="amount_due" value="<?php echo pe((string)($request['amount_due'] ?? ($currentProposal['amount_due_to_start'] ?? ''))); ?>">
                        <label class="pw-lbl" style="margin-top:8px;">Payment Notes</label>
                        <textarea class="pw-textarea" name="payment_notes"><?php echo pe((string)($request['payment_notes'] ?? '')); ?></textarea>
                        <div style="margin-top:8px;"><button class="btn btn-blue" type="submit">Create Invoice</button></div>
                    </form>

                    <form method="post" class="mini-card">
                        <input type="hidden" name="action" value="send_payment_link">
                        <label class="pw-lbl">Payment Link</label>
                        <input class="pw-input" type="url" name="payment_link" value="<?php echo pe((string)($request['payment_link'] ?? '')); ?>" placeholder="https://paypal.com/...">
                        <div style="margin-top:8px;"><button class="btn btn-teal" type="submit">Send Payment Link</button></div>
                    </form>
                </div>

                <form method="post" class="mini-card no-print" style="margin-top:10px;">
                    <input type="hidden" name="action" value="record_payment">
                    <div class="pw-grid">
                        <div><label class="pw-lbl">Payment Amount</label><input class="pw-input" type="text" name="payment_amount" required></div>
                        <div><label class="pw-lbl">Associate Payment With</label><select class="pw-select" name="payment_for"><option value="start_payment">Start Payment</option><option value="milestone_payment">Milestone Payment</option><option value="final_payment">Final Payment</option></select></div>
                        <div><label class="pw-lbl">Milestone Number (optional)</label><input class="pw-input" type="number" min="0" name="payment_milestone_index" value="0"></div>
                        <div><label class="pw-lbl">PayPal Transaction ID</label><input class="pw-input" type="text" name="paypal_transaction_id"></div>
                        <div><label class="pw-lbl">PayPal Invoice ID</label><input class="pw-input" type="text" name="paypal_invoice_id"></div>
                    </div>
                    <div style="margin-top:8px;"><label class="pw-lbl">Payment Note</label><textarea class="pw-textarea" name="payment_note"></textarea></div>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px;">
                        <button class="btn btn-green" type="submit">Record Payment</button>
                        <button class="btn btn-teal" type="submit" name="action" value="mark_project_active">Mark Project Active</button>
                        <button class="btn btn-teal" type="submit" name="action" value="set_waiting_customer">Set Waiting On Customer</button>
                        <button class="btn btn-red" type="submit" name="action" value="mark_project_completed">Mark Completed</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </details>

    <details class="pw-section" <?php echo !empty($openSections['files']) ? 'open' : ''; ?>>
        <summary>Files &amp; Attachments</summary>
        <div class="pw-body">
            <p class="pw-help" style="margin-top:0;">Uploads support jpg, jpeg, png, gif, webp, pdf, txt, md, zip, doc, docx, xls, xlsx, csv, json, and log files. Files are stored in protected project upload storage.</p>
            <div class="mini-card">
                <span class="pw-lbl">Uploaded Files</span>
                <?php
                    $visibleFiles = [];
                    foreach (($request['project_files'] ?? []) as $file) {
                        if (projectCanViewFile((array)$file, $isStaff)) {
                            $visibleFiles[] = $file;
                        }
                    }
                ?>
                <?php if (empty($visibleFiles)): ?>
                    <div class="pw-val">No files uploaded yet.</div>
                <?php else: ?>
                    <ul style="padding-left:18px;margin:8px 0 0;">
                        <?php foreach (array_reverse($visibleFiles) as $file): ?>
                            <li style="margin-bottom:6px;">
                                <a href="/project.php?id=<?php echo urlencode($projectId); ?>&download=<?php echo urlencode((string)$file['file_id']); ?>" style="color:#36f3ff;"><?php echo pe((string)$file['original_filename']); ?></a>
                                <span class="pw-help">(<?php echo pe((string)$file['uploaded_by_role']); ?> · <?php echo pe((string)$file['uploaded_by']); ?> · <?php echo pe((string)$file['upload_time']); ?> · <?php echo pe((string)$file['visibility']); ?>)</span>
                                <?php if ((string)($file['description'] ?? '') !== ''): ?><div class="pw-help"><?php echo pe((string)$file['description']); ?></div><?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <form method="post" enctype="multipart/form-data" class="mini-card no-print" style="margin-top:10px;">
                <input type="hidden" name="action" value="upload_file">
                <label class="pw-lbl">Upload File</label>
                <input class="pw-input" type="file" name="project_file" required>
                <label class="pw-lbl" style="margin-top:8px;">Description</label>
                <input class="pw-input" type="text" name="file_description" placeholder="Optional file note">
                <?php if ($isStaff): ?>
                    <label class="pw-lbl" style="margin-top:8px;">Visibility</label>
                    <select class="pw-select" name="file_visibility">
                        <option value="customer_visible">Customer Visible</option>
                        <option value="staff_only">Staff Only</option>
                    </select>
                <?php endif; ?>
                <div style="margin-top:8px;"><button class="btn btn-blue" type="submit">Upload File</button></div>
            </form>
        </div>
    </details>

    <details class="pw-section" <?php echo !empty($openSections['messages']) ? 'open' : ''; ?>>
        <summary>Messages</summary>
        <div class="pw-body">
            <?php
                $visibleMessages = [];
                foreach (($request['messages'] ?? []) as $msg) {
                    if (!is_array($msg)) {
                        continue;
                    }
                    if ((string)($msg['visibility'] ?? 'customer_visible') === 'staff_only' && !$isStaff) {
                        continue;
                    }
                    $visibleMessages[] = $msg;
                }
                usort($visibleMessages, function ($a, $b) {
                    return (strtotime((string)($b['sent_at'] ?? '')) ?: 0) <=> (strtotime((string)($a['sent_at'] ?? '')) ?: 0);
                });
            ?>

            <div class="mini-card">
                <span class="pw-lbl">Conversation</span>
                <?php if (empty($visibleMessages)): ?>
                    <div class="pw-val">No messages yet.</div>
                <?php else: ?>
                    <?php foreach ($visibleMessages as $msg): ?>
                        <div class="thread-item <?php echo ((string)($msg['visibility'] ?? '') === 'staff_only') ? 'staff-only' : ''; ?>">
                            <div class="thread-meta">
                                <span>Sender: <?php echo pe((string)($msg['sender'] ?? '')); ?></span>
                                <span>Role: <?php echo pe((string)($msg['role'] ?? '')); ?></span>
                                <span>Date: <?php echo pe((string)($msg['sent_at'] ?? '')); ?></span>
                                <span>Visibility: <?php echo pe((string)($msg['visibility'] ?? 'customer_visible')); ?></span>
                                <?php if (!empty($msg['email_sent'])): ?><span>Email sent</span><?php endif; ?>
                            </div>
                            <div class="pw-val"><?php echo pe((string)($msg['text'] ?? '')); ?></div>
                            <?php
                                $msgAttachments = [];
                                if (!empty($msg['attachments']) && is_array($msg['attachments'])) {
                                    foreach ($msg['attachments'] as $fileId) {
                                        foreach (($request['project_files'] ?? []) as $file) {
                                            if ((string)($file['file_id'] ?? '') === (string)$fileId && projectCanViewFile((array)$file, $isStaff)) {
                                                $msgAttachments[] = $file;
                                            }
                                        }
                                    }
                                }
                            ?>
                            <?php if (!empty($msgAttachments)): ?>
                                <div class="pw-help" style="margin-top:6px;">Attachments:</div>
                                <ul style="margin:4px 0 0;padding-left:18px;">
                                    <?php foreach ($msgAttachments as $attachment): ?>
                                        <li><a href="/project.php?id=<?php echo urlencode($projectId); ?>&download=<?php echo urlencode((string)$attachment['file_id']); ?>" style="color:#36f3ff;"><?php echo pe((string)$attachment['original_filename']); ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <form method="post" enctype="multipart/form-data" class="mini-card no-print" style="margin-top:10px;">
                <input type="hidden" name="action" value="send_message">
                <label class="pw-lbl">Message</label>
                <textarea class="pw-textarea" name="message_text" placeholder="Send a project message..." required></textarea>
                <label class="pw-lbl" style="margin-top:8px;">Attachment (optional)</label>
                <input class="pw-input" type="file" name="message_attachment">
                <?php if ($isStaff): ?>
                    <label class="pw-lbl" style="margin-top:8px;">Visibility</label>
                    <select class="pw-select" name="message_visibility">
                        <option value="customer_visible">Customer Visible</option>
                        <option value="staff_only">Staff Only</option>
                    </select>
                <?php endif; ?>
                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px;">
                    <button class="btn btn-blue" type="submit" name="action" value="send_message">Send Message</button>
                    <?php if ($isStaff): ?><button class="btn btn-gold" type="submit" name="action" value="send_message_email">Send Message + Email</button><?php endif; ?>
                    <a class="btn btn-teal" href="mailto:<?php echo pe((string)($request['email'] ?? '')); ?>?subject=Project%20<?php echo urlencode($projectId); ?>">Email Client</a>
                </div>
            </form>
        </div>
    </details>

    <details class="pw-section" <?php echo !empty($openSections['timeline']) ? 'open' : ''; ?>>
        <summary>Project Timeline</summary>
        <div class="pw-body">
            <?php if (empty($timeline)): ?>
                <div class="pw-val">No timeline events yet.</div>
            <?php else: ?>
                <ul class="timeline">
                    <?php foreach ($timeline as $item): ?>
                        <?php if ((string)($item['visibility'] ?? 'customer_visible') === 'staff_only' && !$isStaff) { continue; } ?>
                        <li>
                            <div class="pw-val"><?php echo pe((string)$item['label']); ?></div>
                            <div class="pw-help"><?php echo pe((string)$item['timestamp']); ?></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </details>

<?php endif; ?>
</div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
<script src="assets/js/jquery-1.12.3.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script>
function toggleMilestones() {
    var select = document.getElementById('milestone_count');
    var count = select ? parseInt(select.value || '0', 10) : 0;
    var rows = document.querySelectorAll('.milestone-row');
    rows.forEach(function (row, index) {
        row.style.display = index < count ? '' : 'none';
    });
}
document.addEventListener('DOMContentLoaded', toggleMilestones);
</script>
</body>
</html>
