<?php
session_start();
define('WDS_SYSTEM', true);
require_once 'includes/portal-helpers.php';
require_once 'includes/email.php';

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

// -- Load data
$allRequests  = portalLoadProjectRequests();
$allProposals = portalLoadProposals();
$allAgreements = portalLoadProjectAgreements();

// -- Find the project request
$request      = null;
$requestIndex = null;
foreach ($allRequests as $i => $r) {
    if (portalGetRequestDisplayId((array)$r) === $projectId) {
        $request      = portalNormalizeProjectRequest((array)$r);
        $requestIndex = $i;
        break;
    }
}

$notFound  = ($request === null);
$forbidden = false;
if (!$notFound && $isClient) {
    if ((string)($request['client_username'] ?? '') !== $myUsername) {
        $forbidden = true;
    }
}

// -- Find linked proposals and agreements
$linkedProposals  = [];
$linkedAgreements = [];
if (!$notFound && !$forbidden) {
    $linkedProposals = array_values(array_filter($allProposals, function ($p) use ($projectId) {
        return (string)($p['request_id'] ?? '') === $projectId;
    }));
    $linkedAgreements = array_values(array_filter($allAgreements, function ($a) use ($projectId) {
        return (string)($a['request_id'] ?? '') === $projectId;
    }));
}

$currentProposal  = !empty($linkedProposals)  ? $linkedProposals[0]  : null;
$currentAgreement = !empty($linkedAgreements) ? $linkedAgreements[0] : null;

// -- Option lists
$statusOptions = [
    'new'              => 'Request Submitted',
    'reviewing'        => 'Reviewing',
    'contacted'        => 'Contacted',
    'needs_info'       => 'Needs Info',
    'proposal_drafted' => 'Proposal Drafted',
    'proposal_sent'    => 'Proposal Sent',
    'accepted'         => 'Active',
    'active'           => 'Active',
    'completed'        => 'Completed',
    'declined'         => 'Declined',
    'closed'           => 'Closed',
    'cancelled'        => 'Cancelled',
];

$proposalStatuses  = ['draft' => 'Draft', 'sent' => 'Sent', 'accepted' => 'Approved', 'rejected' => 'Rejected'];
$agreementStatuses = ['draft' => 'Draft', 'sent' => 'Sent', 'signed' => 'Signed'];

// -- Local ID generators
function _genProposalId(array $proposals) {
    $existing = [];
    foreach ($proposals as $p) {
        if (!empty($p['proposal_id'])) {
            $existing[(string)$p['proposal_id']] = true;
        }
    }
    $d = date('Ymd');
    do {
        $id = 'PROP-' . $d . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
    } while (isset($existing[$id]));
    return $id;
}

function _genAgreementId(array $agreements) {
    $existing = [];
    foreach ($agreements as $a) {
        if (!empty($a['agreement_id'])) {
            $existing[(string)$a['agreement_id']] = true;
        }
    }
    $d = date('Ymd');
    do {
        $id = 'AGR-' . $d . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
    } while (isset($existing[$id]));
    return $id;
}

// -- Reload helpers
function _reloadData(&$allRequests, &$allProposals, &$allAgreements,
                     &$request, &$linkedProposals, &$linkedAgreements,
                     &$currentProposal, &$currentAgreement, $projectId) {
    $allRequests  = portalLoadProjectRequests();
    $allProposals = portalLoadProposals();
    $allAgreements = portalLoadProjectAgreements();
    foreach ($allRequests as $r) {
        if (portalGetRequestDisplayId((array)$r) === $projectId) {
            $request = portalNormalizeProjectRequest((array)$r);
            break;
        }
    }
    $linkedProposals = array_values(array_filter($allProposals, function ($p) use ($projectId) {
        return (string)($p['request_id'] ?? '') === $projectId;
    }));
    $linkedAgreements = array_values(array_filter($allAgreements, function ($a) use ($projectId) {
        return (string)($a['request_id'] ?? '') === $projectId;
    }));
    $currentProposal  = !empty($linkedProposals)  ? $linkedProposals[0]  : null;
    $currentAgreement = !empty($linkedAgreements) ? $linkedAgreements[0] : null;
}

$notice = '';
$error  = '';

// ================================================================
// POST HANDLER
// ================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$notFound && !$forbidden) {
    $action = trim((string)($_POST['action'] ?? ''));

    // ---- Staff: save project meta / status / notes
    if ($isStaff && $action === 'save_project') {
        $newStatus = trim((string)($_POST['status'] ?? ''));
        if (!array_key_exists($newStatus, $statusOptions)) {
            $newStatus = (string)($request['status'] ?? 'new');
        }
        foreach ($allRequests as &$req) {
            if (portalGetRequestDisplayId((array)$req) !== $projectId) continue;
            $req['status']                = $newStatus;
            $req['internal_notes']        = trim((string)($_POST['internal_notes']        ?? ''));
            $req['staff_summary']         = trim((string)($_POST['staff_summary']         ?? ''));
            $req['recommended_next_step'] = trim((string)($_POST['recommended_next_step'] ?? ''));
            $req['estimated_cost_range']  = trim((string)($_POST['estimated_cost_range']  ?? ''));
            $req['estimated_time_range']  = trim((string)($_POST['estimated_time_range']  ?? ''));
            $req['updated_at']            = date('c');
            break;
        }
        unset($req);
        portalSaveProjectRequests($allRequests);
        _reloadData($allRequests, $allProposals, $allAgreements,
                    $request, $linkedProposals, $linkedAgreements,
                    $currentProposal, $currentAgreement, $projectId);
        $notice = 'Project details saved.';
    }

    // ---- Staff: save/send proposal
    if ($isStaff && in_array($action, ['save_proposal', 'send_proposal'], true)) {
        $proposalId = trim((string)($_POST['proposal_id'] ?? ($currentProposal['proposal_id'] ?? '')));
        if ($proposalId === '') {
            $proposalId = _genProposalId($allProposals);
        }
        $proposalStatus = ($action === 'send_proposal') ? 'sent'
            : trim((string)($_POST['proposal_status'] ?? 'draft'));
        if (!array_key_exists($proposalStatus, $proposalStatuses)) {
            $proposalStatus = 'draft';
        }

        $record = [
            'proposal_id'               => $proposalId,
            'request_id'                => $projectId,
            'client_username'           => (string)($request['client_username'] ?? ''),
            'client_name'               => trim((string)($_POST['client_name']               ?? $request['name']  ?? '')),
            'client_email'              => trim((string)($_POST['client_email']              ?? $request['email'] ?? '')),
            'project_title'             => trim((string)($_POST['project_title']             ?? $request['project_type'] ?? '')),
            'request_summary'           => trim((string)($_POST['request_summary']           ?? '')),
            'proposed_work'             => trim((string)($_POST['proposed_work']             ?? '')),
            'deliverables'              => trim((string)($_POST['deliverables']              ?? '')),
            'estimated_cost'            => trim((string)($_POST['estimated_cost']            ?? '')),
            'estimated_time'            => trim((string)($_POST['estimated_time']            ?? '')),
            'payment_required_to_begin' => trim((string)($_POST['payment_required_to_begin'] ?? '')),
            'revision_terms'            => trim((string)($_POST['revision_terms']            ?? '')),
            'assumptions'               => trim((string)($_POST['assumptions']               ?? '')),
            'customer_responsibilities' => trim((string)($_POST['customer_responsibilities'] ?? '')),
            'next_steps'                => trim((string)($_POST['next_steps']                ?? '')),
            'timeline'                  => trim((string)($_POST['timeline']                  ?? $request['timeline'] ?? '')),
            'budget_comfort'            => trim((string)($_POST['budget_comfort']            ?? $request['budget_comfort'] ?? '')),
            'repo_link'                 => trim((string)($_POST['repo_link']                 ?? $request['repo_link'] ?? '')),
            'status'                    => $proposalStatus,
            'updated_at'               => date('c'),
        ];

        $found = false;
        foreach ($allProposals as &$p) {
            if ((string)($p['proposal_id'] ?? '') === $proposalId) {
                $record['created_at'] = (string)($p['created_at'] ?? date('c'));
                $p = $record;
                $found = true;
                break;
            }
        }
        unset($p);
        if (!$found) {
            $record['created_at'] = date('c');
            $allProposals[] = $record;
        }
        portalSaveProposals($allProposals);

        foreach ($allRequests as &$req) {
            if (portalGetRequestDisplayId((array)$req) !== $projectId) continue;
            $ids = isset($req['proposal_ids']) && is_array($req['proposal_ids']) ? $req['proposal_ids'] : [];
            if (!in_array($proposalId, $ids, true)) {
                $ids[] = $proposalId;
            }
            $req['proposal_ids'] = array_values($ids);
            if ($action === 'send_proposal') {
                $req['status'] = 'proposal_sent';
            } elseif (in_array((string)($req['status'] ?? ''), ['new', 'reviewing'], true)) {
                $req['status'] = 'proposal_drafted';
            }
            $req['updated_at'] = date('c');
            break;
        }
        unset($req);
        portalSaveProjectRequests($allRequests);

        if ($action === 'send_proposal') {
            $host = $_SERVER['HTTP_HOST'] ?? 'runlevel.systems';
            $link = 'https://' . $host . '/project.php?id=' . urlencode($projectId);
            send_project_proposal_email($record['client_email'], $record['client_name'], $projectId, $link);
            $notice = 'Proposal saved and sent by email.';
        } else {
            $notice = 'Proposal draft saved.';
        }

        _reloadData($allRequests, $allProposals, $allAgreements,
                    $request, $linkedProposals, $linkedAgreements,
                    $currentProposal, $currentAgreement, $projectId);
    }

    // ---- Staff: save/send agreement
    if ($isStaff && in_array($action, ['save_agreement', 'send_agreement'], true)) {
        $agreementId = trim((string)($_POST['agreement_id'] ?? ($currentAgreement['agreement_id'] ?? '')));
        if ($agreementId === '') {
            $agreementId = _genAgreementId($allAgreements);
        }
        $agreementStatus = ($action === 'send_agreement') ? 'sent'
            : trim((string)($_POST['agreement_status'] ?? 'draft'));
        if (!in_array($agreementStatus, ['draft', 'sent', 'signed'], true)) {
            $agreementStatus = 'draft';
        }

        $record = [
            'agreement_id'               => $agreementId,
            'proposal_id'                => (string)($currentProposal['proposal_id'] ?? ''),
            'request_id'                 => $projectId,
            'client_username'            => (string)($request['client_username'] ?? ''),
            'client_name'                => trim((string)($_POST['a_client_name']               ?? $request['name']  ?? '')),
            'client_email'               => trim((string)($_POST['a_client_email']              ?? $request['email'] ?? '')),
            'project_title'              => trim((string)($_POST['a_project_title']             ?? $currentProposal['project_title'] ?? $request['project_type'] ?? '')),
            'effective_date'             => trim((string)($_POST['effective_date']              ?? date('Y-m-d'))),
            'scope_of_work'              => trim((string)($_POST['scope_of_work']               ?? '')),
            'deliverables'               => trim((string)($_POST['a_deliverables']              ?? '')),
            'timeline'                   => trim((string)($_POST['a_timeline']                  ?? $currentProposal['estimated_time'] ?? $request['timeline'] ?? '')),
            'payment_terms'              => trim((string)($_POST['payment_terms']               ?? $currentProposal['payment_required_to_begin'] ?? '')),
            'revision_terms'             => trim((string)($_POST['a_revision_terms']            ?? $currentProposal['revision_terms'] ?? '')),
            'change_request_policy'      => trim((string)($_POST['change_request_policy']       ?? 'Changes outside scope require approval and may affect timeline and cost.')),
            'customer_responsibilities'  => trim((string)($_POST['a_customer_responsibilities'] ?? $currentProposal['customer_responsibilities'] ?? '')),
            'third_party_licenses'       => trim((string)($_POST['third_party_licenses']        ?? 'Client supplies or approves all required third-party licenses and assets.')),
            'source_code_ownership_terms'=> trim((string)($_POST['source_code_ownership_terms'] ?? 'Ownership terms follow approved proposal and Runlevel Systems Terms of Service.')),
            'testing_and_acceptance'     => trim((string)($_POST['testing_and_acceptance']      ?? 'Client reviews deliverables within agreed review window and confirms acceptance in writing.')),
            'termination'                => trim((string)($_POST['termination']                 ?? 'Either party may terminate with written notice; completed work remains billable.')),
            'legal_notice'               => trim((string)($_POST['legal_notice']               ?? '')),
            'signature_placeholder'      => trim((string)($_POST['signature_placeholder']       ?? 'Client Signature: ____________________   Date: __________')),
            'status'                     => $agreementStatus,
            'updated_at'                => date('c'),
        ];

        $found = false;
        foreach ($allAgreements as &$a) {
            if ((string)($a['agreement_id'] ?? '') === $agreementId) {
                $record['created_at'] = (string)($a['created_at'] ?? date('c'));
                $a = $record;
                $found = true;
                break;
            }
        }
        unset($a);
        if (!$found) {
            $record['created_at'] = date('c');
            $allAgreements[] = $record;
        }
        portalSaveProjectAgreements($allAgreements);

        foreach ($allRequests as &$req) {
            if (portalGetRequestDisplayId((array)$req) !== $projectId) continue;
            $ids = isset($req['agreement_ids']) && is_array($req['agreement_ids']) ? $req['agreement_ids'] : [];
            if (!in_array($agreementId, $ids, true)) {
                $ids[] = $agreementId;
            }
            $req['agreement_ids'] = array_values($ids);
            if ($action === 'send_agreement') {
                $req['status'] = 'accepted';
            }
            $req['updated_at'] = date('c');
            break;
        }
        unset($req);
        portalSaveProjectRequests($allRequests);

        if ($action === 'send_agreement') {
            $host = $_SERVER['HTTP_HOST'] ?? 'runlevel.systems';
            $link = 'https://' . $host . '/project.php?id=' . urlencode($projectId);
            send_project_agreement_email($record['client_email'], $record['client_name'], $projectId, $link);
            $notice = 'Project agreement saved and sent by email.';
        } else {
            $notice = 'Agreement draft saved.';
        }

        _reloadData($allRequests, $allProposals, $allAgreements,
                    $request, $linkedProposals, $linkedAgreements,
                    $currentProposal, $currentAgreement, $projectId);
    }

    // ---- Client: approve proposal
    if ($isClient && $action === 'approve_proposal' && $currentProposal !== null) {
        $pid = (string)($currentProposal['proposal_id'] ?? '');
        foreach ($allProposals as &$p) {
            if ((string)($p['proposal_id'] ?? '') === $pid) {
                $p['status']     = 'accepted';
                $p['updated_at'] = date('c');
                break;
            }
        }
        unset($p);
        portalSaveProposals($allProposals);
        _reloadData($allRequests, $allProposals, $allAgreements,
                    $request, $linkedProposals, $linkedAgreements,
                    $currentProposal, $currentAgreement, $projectId);
        $notice = 'Proposal approved.';
    }

    // ---- Client: sign agreement
    if ($isClient && $action === 'sign_agreement' && $currentAgreement !== null) {
        $aid        = (string)($currentAgreement['agreement_id'] ?? '');
        $signedName = trim((string)($_POST['signed_name'] ?? ''));
        foreach ($allAgreements as &$a) {
            if ((string)($a['agreement_id'] ?? '') === $aid) {
                $a['status']     = 'signed';
                $a['signed_at']  = date('c');
                $a['signed_by']  = $signedName !== '' ? $signedName : $myUsername;
                $a['updated_at'] = date('c');
                break;
            }
        }
        unset($a);
        portalSaveProjectAgreements($allAgreements);

        foreach ($allRequests as &$req) {
            if (portalGetRequestDisplayId((array)$req) !== $projectId) continue;
            $req['status']     = 'active';
            $req['updated_at'] = date('c');
            break;
        }
        unset($req);
        portalSaveProjectRequests($allRequests);

        _reloadData($allRequests, $allProposals, $allAgreements,
                    $request, $linkedProposals, $linkedAgreements,
                    $currentProposal, $currentAgreement, $projectId);
        $notice = 'Agreement signed. Your project is now active.';
    }
}

// ================================================================
// HELPERS
// ================================================================

function statusBadge($status) {
    static $map = [
        'new'              => ['Request Submitted', '#36f3ff',  'rgba(54,243,255,0.10)'],
        'reviewing'        => ['Reviewing',          '#a78bfa',  'rgba(167,139,250,0.10)'],
        'contacted'        => ['Contacted',           '#60a5fa',  'rgba(96,165,250,0.10)'],
        'needs_info'       => ['Needs Info',          '#fb923c',  'rgba(251,146,60,0.10)'],
        'proposal_drafted' => ['Proposal Pending',    '#fbbf24',  'rgba(251,191,36,0.10)'],
        'proposal_sent'    => ['Awaiting Approval',   '#ffc600',  'rgba(255,198,0,0.14)'],
        'accepted'         => ['Active',              '#22c55e',  'rgba(34,197,94,0.10)'],
        'active'           => ['Active',              '#22c55e',  'rgba(34,197,94,0.10)'],
        'completed'        => ['Completed',           '#4ade80',  'rgba(74,222,128,0.10)'],
        'declined'         => ['Declined',            '#f87171',  'rgba(248,113,113,0.10)'],
        'closed'           => ['Closed',              '#6b7280',  'rgba(107,114,128,0.10)'],
        'cancelled'        => ['Cancelled',           '#ef4444',  'rgba(239,68,68,0.10)'],
    ];
    $s = $map[$status] ?? [ucfirst(str_replace('_', ' ', $status)), '#a8bedc', 'rgba(168,190,220,0.10)'];
    return '<span style="display:inline-block;border:1px solid ' . $s[1] . ';background:' . $s[2]
        . ';color:' . $s[1] . ';border-radius:5px;padding:3px 10px;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;">'
        . pe($s[0]) . '</span>';
}

function proposalStatusBadge($status) {
    static $map = [
        'draft'    => ['Draft',    '#7a9ac0', 'rgba(122,154,192,0.10)'],
        'sent'     => ['Sent',     '#fbbf24', 'rgba(251,191,36,0.10)'],
        'accepted' => ['Approved', '#22c55e', 'rgba(34,197,94,0.10)'],
        'rejected' => ['Rejected', '#f87171', 'rgba(248,113,113,0.10)'],
    ];
    $s = $map[$status] ?? [ucfirst($status), '#a8bedc', 'rgba(168,190,220,0.10)'];
    return '<span style="display:inline-block;border:1px solid ' . $s[1] . ';background:' . $s[2]
        . ';color:' . $s[1] . ';border-radius:5px;padding:2px 9px;font-size:.7rem;font-weight:700;text-transform:uppercase;">'
        . pe($s[0]) . '</span>';
}

function agreementStatusBadge($status) {
    static $map = [
        'draft'  => ['Draft',  '#7a9ac0', 'rgba(122,154,192,0.10)'],
        'sent'   => ['Sent',   '#fbbf24', 'rgba(251,191,36,0.10)'],
        'signed' => ['Signed', '#22c55e', 'rgba(34,197,94,0.10)'],
    ];
    $s = $map[$status] ?? [ucfirst($status), '#a8bedc', 'rgba(168,190,220,0.10)'];
    return '<span style="display:inline-block;border:1px solid ' . $s[1] . ';background:' . $s[2]
        . ';color:' . $s[1] . ';border-radius:5px;padding:2px 9px;font-size:.7rem;font-weight:700;text-transform:uppercase;">'
        . pe($s[0]) . '</span>';
}

// -- Build communication timeline from existing data
function buildTimeline(array $request, ?array $proposal, ?array $agreement) {
    $events = [];

    if (!empty($request['created_at'])) {
        $events[] = ['ts' => strtotime((string)$request['created_at']), 'icon' => '📥', 'text' => 'Project request submitted', 'color' => '#36f3ff'];
    }

    if ($proposal !== null) {
        $pCreated = strtotime((string)($proposal['created_at'] ?? ''));
        if ($pCreated) {
            $events[] = ['ts' => $pCreated, 'icon' => '📄', 'text' => 'Proposal created', 'color' => '#a78bfa'];
        }
        $pStatus = (string)($proposal['status'] ?? '');
        $pUpdated = strtotime((string)($proposal['updated_at'] ?? ''));
        if ($pStatus === 'sent' && $pUpdated) {
            $events[] = ['ts' => $pUpdated, 'icon' => '📤', 'text' => 'Proposal sent to client', 'color' => '#fbbf24'];
        }
        if ($pStatus === 'accepted' && $pUpdated) {
            $events[] = ['ts' => $pUpdated, 'icon' => '✅', 'text' => 'Proposal approved by client', 'color' => '#22c55e'];
        }
        if ($pStatus === 'rejected' && $pUpdated) {
            $events[] = ['ts' => $pUpdated, 'icon' => '❌', 'text' => 'Proposal rejected', 'color' => '#f87171'];
        }
    }

    if ($agreement !== null) {
        $aCreated = strtotime((string)($agreement['created_at'] ?? ''));
        if ($aCreated) {
            $events[] = ['ts' => $aCreated, 'icon' => '📑', 'text' => 'Project agreement created', 'color' => '#60a5fa'];
        }
        $aStatus  = (string)($agreement['status'] ?? '');
        $aUpdated = strtotime((string)($agreement['updated_at'] ?? ''));
        if ($aStatus === 'sent' && $aUpdated) {
            $events[] = ['ts' => $aUpdated, 'icon' => '📤', 'text' => 'Agreement sent to client', 'color' => '#fbbf24'];
        }
        if ($aStatus === 'signed') {
            $signedAt = strtotime((string)($agreement['signed_at'] ?? ($agreement['updated_at'] ?? '')));
            if ($signedAt) {
                $signedBy = (string)($agreement['signed_by'] ?? '');
                $events[] = ['ts' => $signedAt, 'icon' => '✍️', 'text' => 'Agreement signed' . ($signedBy ? ' by ' . $signedBy : ''), 'color' => '#22c55e'];
            }
        }
    }

    $reqStatus = (string)($request['status'] ?? '');
    if (in_array($reqStatus, ['active', 'accepted'], true)) {
        $ts = strtotime((string)($request['updated_at'] ?? $request['created_at'] ?? ''));
        if ($ts) {
            $events[] = ['ts' => $ts, 'icon' => '🚀', 'text' => 'Project marked active', 'color' => '#22c55e'];
        }
    }
    if (in_array($reqStatus, ['completed', 'closed'], true)) {
        $ts = strtotime((string)($request['updated_at'] ?? $request['created_at'] ?? ''));
        if ($ts) {
            $events[] = ['ts' => $ts, 'icon' => '🏁', 'text' => 'Project completed', 'color' => '#4ade80'];
        }
    }

    usort($events, function ($a, $b) { return $a['ts'] - $b['ts']; });
    return $events;
}

// -- Build status tracker steps
function buildTrackerSteps(array $request, ?array $proposal, ?array $agreement) {
    $reqStatus  = (string)($request['status'] ?? 'new');
    $propStatus = $proposal  ? (string)($proposal['status']  ?? '') : '';
    $agrStatus  = $agreement ? (string)($agreement['status'] ?? '') : '';

    $steps = [];

    $steps[] = ['label' => 'Request Submitted',  'done' => true];
    $steps[] = ['label' => 'Proposal Created',   'done' => $proposal !== null];
    $steps[] = ['label' => 'Proposal Sent',      'done' => in_array($propStatus, ['sent', 'accepted', 'rejected'], true)];
    $steps[] = ['label' => 'Proposal Approved',  'done' => $propStatus === 'accepted'];
    $steps[] = ['label' => 'Agreement Created',  'done' => $agreement !== null];
    $steps[] = ['label' => 'Agreement Sent',     'done' => in_array($agrStatus, ['sent', 'signed'], true)];
    $steps[] = ['label' => 'Agreement Signed',   'done' => $agrStatus === 'signed'];
    $steps[] = ['label' => 'Project Active',     'done' => in_array($reqStatus, ['active', 'accepted', 'completed', 'closed'], true)];
    $steps[] = ['label' => 'Project Completed',  'done' => in_array($reqStatus, ['completed', 'closed'], true)];

    return $steps;
}

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
    <title>Project <?php echo pe($projectId); ?> | Runlevel Systems</title>
    <link href="assets/css/coreloop.css" rel="stylesheet">
    <style>
        .pw-wrap{padding:26px 0 70px;}
        .pw-card{background:#0c1729;border:1px solid rgba(54,243,255,.18);border-radius:10px;padding:18px;margin-bottom:14px;}
        .pw-section-head{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;margin-bottom:14px;}
        .pw-section-title{color:#36f3ff;font-size:.82rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin:0;}
        .pw-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;}
        @media(max-width:720px){.pw-grid{grid-template-columns:1fr;}}
        .pw-lbl{color:#5a7a9e;font-size:.7rem;text-transform:uppercase;letter-spacing:.04em;display:block;margin-bottom:3px;}
        .pw-val{color:#eaf3ff;font-size:.86rem;word-break:break-word;}
        .pw-mono{font-family:monospace;color:#ffc600;font-weight:700;}
        .pw-input,.pw-textarea,.pw-select{background:#09111d;color:#eaf3ff;border:1px solid rgba(54,243,255,.22);border-radius:6px;padding:8px 10px;width:100%;font-size:.85rem;box-sizing:border-box;}
        .pw-textarea{min-height:90px;resize:vertical;}
        label{color:#a8bedc;font-size:.77rem;display:block;margin-bottom:4px;text-transform:uppercase;letter-spacing:.04em;}
        .btn{display:inline-block;border-radius:5px;padding:7px 11px;font-size:.8rem;border:none;font-weight:700;text-decoration:none;cursor:pointer;line-height:1.3;}
        .btn-blue{background:#0a84ff;color:#fff;}
        .btn-gold{background:#ffc600;color:#08111f;}
        .btn-teal{background:rgba(54,243,255,.1);border:1px solid rgba(54,243,255,.3);color:#36f3ff;}
        .btn-green{background:rgba(34,197,94,.15);border:1px solid rgba(34,197,94,.4);color:#4ade80;}
        .btn-red{background:rgba(239,68,68,.14);border:1px solid rgba(239,68,68,.35);color:#fca5a5;}
        .notice{background:rgba(34,197,94,.14);border:1px solid rgba(34,197,94,.35);color:#86efac;border-radius:6px;padding:10px 14px;margin-bottom:14px;font-size:.84rem;}
        .error-box{background:rgba(239,68,68,.14);border:1px solid rgba(239,68,68,.35);color:#fecaca;border-radius:6px;padding:10px 14px;margin-bottom:14px;font-size:.84rem;}
        .pw-header-bar{background:#07111f;border:1px solid rgba(54,243,255,.15);border-radius:10px;padding:16px 18px;margin-bottom:14px;display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:10px;}
        .pw-project-id{font-family:monospace;color:#ffc600;font-size:1rem;font-weight:700;}
        .pw-project-name{color:#eaf3ff;font-size:1.15rem;font-weight:700;margin:4px 0;}
        .pw-client-name{color:#7a9ac0;font-size:.84rem;}
        .tracker{display:flex;flex-wrap:wrap;gap:6px;margin-top:4px;}
        .tracker-step{display:flex;align-items:center;gap:5px;font-size:.74rem;color:#5a7a9e;white-space:nowrap;}
        .tracker-step.done{color:#eaf3ff;}
        .tracker-step .dot{width:10px;height:10px;border-radius:50%;background:#1a2a3a;border:1px solid #2a3a4a;flex-shrink:0;}
        .tracker-step.done .dot{background:#22c55e;border-color:#22c55e;}
        .pw-timeline{list-style:none;padding:0;margin:0;}
        .pw-timeline li{display:flex;gap:10px;padding:7px 0;border-bottom:1px solid rgba(54,243,255,.08);}
        .pw-timeline li:last-child{border-bottom:none;}
        .pw-timeline .tl-icon{font-size:.9rem;flex-shrink:0;padding-top:1px;}
        .pw-timeline .tl-text{color:#a8bedc;font-size:.82rem;}
        .pw-timeline .tl-date{color:#5a7a9e;font-size:.72rem;margin-top:2px;}
        .pw-files-info{color:#5a7a9e;font-size:.82rem;font-style:italic;}
        .int-notes-box{background:rgba(255,198,0,.04);border:1px solid rgba(255,198,0,.18);border-radius:8px;padding:12px;}
        details.pw-section{margin-bottom:14px;}
        details.pw-section summary{cursor:pointer;list-style:none;background:#0c1729;border:1px solid rgba(54,243,255,.18);border-radius:10px;padding:14px 18px;color:#36f3ff;font-size:.82rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;display:flex;align-items:center;justify-content:space-between;}
        details.pw-section summary::-webkit-details-marker{display:none;}
        details.pw-section summary::after{content:"▼";font-size:.65rem;color:#5a7a9e;}
        details.pw-section[open] summary{border-radius:10px 10px 0 0;border-bottom-color:transparent;}
        details.pw-section[open] summary::after{content:"▲";}
        .pw-section-body{background:#0c1729;border:1px solid rgba(54,243,255,.18);border-top:none;border-radius:0 0 10px 10px;padding:16px 18px;}
        @media print{
            .no-print,#header,#footer-widget,footer,nav{display:none !important;}
            body{background:#fff;color:#000;}
            .pw-card,.pw-header-bar,.pw-section-body{border:1px solid #ccc;background:#fff;}
            .pw-val,.pw-mono,.pw-project-name{color:#111 !important;}
        }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navigation.php'; ?>

<section class="pw-wrap">
<div class="container">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;flex-wrap:wrap;gap:6px;" class="no-print">
        <a href="/dashboard.php" style="color:#36f3ff;font-size:.84rem;">← Dashboard</a>
        <?php if ($isStaff): ?>
            <a href="/staff/estimate-requests.php" style="color:#5a7a9e;font-size:.8rem;">All Project Requests</a>
        <?php endif; ?>
    </div>

<?php if ($notFound): ?>
    <div class="pw-card"><p style="color:#fecaca;">Project not found.</p></div>
<?php elseif ($forbidden): ?>
    <div class="pw-card"><p style="color:#fecaca;">You do not have permission to view this project.</p></div>
<?php else:
    $st           = (string)($request['status'] ?? 'new');
    $projectName  = (string)($currentProposal['project_title'] ?? $request['project_type'] ?? 'Project');
    $clientName   = (string)($request['name']   ?? $currentProposal['client_name']  ?? '');
    $clientEmail  = (string)($request['email']  ?? $currentProposal['client_email'] ?? '');
    $trackerSteps = buildTrackerSteps($request, $currentProposal, $currentAgreement);
    $timeline     = buildTimeline($request, $currentProposal, $currentAgreement);
?>

    <?php if ($notice !== ''): ?><div class="notice no-print"><?php echo pe($notice); ?></div><?php endif; ?>
    <?php if ($error !== ''): ?><div class="error-box no-print"><?php echo pe($error); ?></div><?php endif; ?>

    <!-- PROJECT HEADER -->
    <div class="pw-header-bar">
        <div>
            <div class="pw-project-id"><?php echo pe($projectId); ?></div>
            <div class="pw-project-name"><?php echo pe($projectName); ?></div>
            <div class="pw-client-name"><?php echo pe($clientName); ?><?php if ($clientEmail): ?> &middot; <?php echo pe($clientEmail); ?><?php endif; ?></div>
        </div>
        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;">
            <?php echo statusBadge($st); ?>
            <button type="button" class="btn btn-teal no-print" onclick="window.print()" style="font-size:.75rem;padding:5px 9px;">Print / PDF</button>
        </div>
    </div>

    <!-- SECTION 9 — STATUS TRACKER (shown near top for at-a-glance) -->
    <div class="pw-card" style="padding:14px 18px;">
        <div class="pw-section-title" style="margin-bottom:10px;">Project Progress</div>
        <div class="tracker">
            <?php foreach ($trackerSteps as $step): ?>
                <div class="tracker-step <?php echo $step['done'] ? 'done' : ''; ?>">
                    <div class="dot"></div>
                    <?php echo pe($step['label']); ?>
                </div>
                <?php if ($step !== end($trackerSteps)): ?><span style="color:#2a3a4a;font-size:.7rem;">›</span><?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- SECTION 1 — CLIENT INFORMATION -->
    <details class="pw-section" open>
        <summary>Client Information</summary>
        <div class="pw-section-body">
            <div class="pw-grid">
                <div><span class="pw-lbl">Client Name</span><div class="pw-val"><?php echo pe($clientName ?: '—'); ?></div></div>
                <div><span class="pw-lbl">Email</span><div class="pw-val"><?php echo pe($clientEmail ?: '—'); ?></div></div>
                <div><span class="pw-lbl">Phone</span><div class="pw-val"><?php echo pe($request['phone'] ?? '—'); ?></div></div>
                <div><span class="pw-lbl">Discord</span><div class="pw-val"><?php echo pe($request['discord_username'] ?? '—'); ?></div></div>
                <div><span class="pw-lbl">Company / Website</span><div class="pw-val"><?php echo pe($request['company'] ?? $request['website'] ?? '—'); ?></div></div>
                <div><span class="pw-lbl">Preferred Contact</span><div class="pw-val"><?php echo pe($request['contact_method'] ?? '—'); ?></div></div>
                <?php if ($request['repo_link'] ?? ''): ?>
                <div><span class="pw-lbl">Repository / Project Link</span><div class="pw-val"><a href="<?php echo pe($request['repo_link']); ?>" target="_blank" rel="noopener noreferrer" style="color:#36f3ff;"><?php echo pe($request['repo_link']); ?></a></div></div>
                <?php endif; ?>
                <div><span class="pw-lbl">Username</span><div class="pw-val pw-mono"><?php echo pe($request['client_username'] ?? '—'); ?></div></div>
            </div>
        </div>
    </details>

    <!-- SECTION 2 — PROJECT DETAILS -->
    <details class="pw-section" open>
        <summary>Project Details</summary>
        <div class="pw-section-body">
            <div class="pw-grid">
                <div><span class="pw-lbl">Request ID</span><div class="pw-val pw-mono"><?php echo pe($projectId); ?></div></div>
                <div><span class="pw-lbl">Project Type</span><div class="pw-val"><?php echo pe($request['project_type'] ?? '—'); ?></div></div>
                <div><span class="pw-lbl">Project Stage</span><div class="pw-val"><?php echo pe($request['project_stage'] ?? '—'); ?></div></div>
                <div><span class="pw-lbl">Project Size</span><div class="pw-val"><?php echo pe($request['project_size'] ?? '—'); ?></div></div>
                <div><span class="pw-lbl">Timeline</span><div class="pw-val"><?php echo pe($request['timeline'] ?? '—'); ?></div></div>
                <div><span class="pw-lbl">Budget</span><div class="pw-val"><?php echo pe($request['budget_comfort'] ?? '—'); ?></div></div>
                <div><span class="pw-lbl">Submitted</span><div class="pw-val"><?php echo pe(date('M j, Y g:i A', strtotime((string)($request['created_at'] ?? 'now')))); ?></div></div>
                <div><span class="pw-lbl">Last Updated</span><div class="pw-val"><?php echo pe($request['updated_at'] ? date('M j, Y g:i A', strtotime((string)$request['updated_at'])) : '—'); ?></div></div>
            </div>
            <div style="margin-top:10px;">
                <span class="pw-lbl">Description / Requirements</span>
                <div style="background:rgba(0,0,0,.3);border:1px solid rgba(54,243,255,.08);border-radius:6px;padding:10px;white-space:pre-wrap;" class="pw-val"><?php echo pe($request['description'] ?? ''); ?></div>
            </div>

            <?php if ($isStaff): ?>
            <!-- Staff edit form for project status / admin fields -->
            <form method="post" style="margin-top:14px;" class="no-print">
                <input type="hidden" name="action" value="save_project">
                <div class="pw-grid">
                    <div>
                        <label>Project Status</label>
                        <select name="status" class="pw-select">
                            <?php foreach ($statusOptions as $k => $v): ?>
                                <option value="<?php echo pe($k); ?>" <?php echo $st === $k ? 'selected' : ''; ?>><?php echo pe($v); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label>Estimated Cost Range</label>
                        <input class="pw-input" type="text" name="estimated_cost_range" value="<?php echo pe($request['estimated_cost_range'] ?? ''); ?>">
                    </div>
                    <div>
                        <label>Estimated Time Range</label>
                        <input class="pw-input" type="text" name="estimated_time_range" value="<?php echo pe($request['estimated_time_range'] ?? ''); ?>">
                    </div>
                    <div>
                        <label>Recommended Next Step</label>
                        <input class="pw-input" type="text" name="recommended_next_step" value="<?php echo pe($request['recommended_next_step'] ?? ''); ?>">
                    </div>
                </div>
                <div style="margin-top:8px;">
                    <label>Staff Summary</label>
                    <textarea class="pw-textarea" name="staff_summary"><?php echo pe($request['staff_summary'] ?? ''); ?></textarea>
                </div>
                <div style="margin-top:8px;display:flex;gap:8px;flex-wrap:wrap;">
                    <button class="btn btn-blue" type="submit">Save Project Details</button>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </details>

    <!-- SECTION 3 — INTERNAL NOTES (staff only) -->
    <?php if ($isStaff): ?>
    <details class="pw-section">
        <summary>Internal Notes <span style="font-size:.68rem;color:#ffc600;font-weight:400;text-transform:none;letter-spacing:0;margin-left:6px;">(not visible to client)</span></summary>
        <div class="pw-section-body">
            <div class="int-notes-box">
                <form method="post" class="no-print">
                    <input type="hidden" name="action" value="save_project">
                    <!-- Carry forward other fields at their current values so this partial POST doesn't wipe them -->
                    <input type="hidden" name="status" value="<?php echo pe($st); ?>">
                    <input type="hidden" name="staff_summary" value="<?php echo pe($request['staff_summary'] ?? ''); ?>">
                    <input type="hidden" name="recommended_next_step" value="<?php echo pe($request['recommended_next_step'] ?? ''); ?>">
                    <input type="hidden" name="estimated_cost_range" value="<?php echo pe($request['estimated_cost_range'] ?? ''); ?>">
                    <input type="hidden" name="estimated_time_range" value="<?php echo pe($request['estimated_time_range'] ?? ''); ?>">
                    <label>Internal Notes</label>
                    <textarea class="pw-textarea" name="internal_notes" style="min-height:120px;"><?php echo pe($request['internal_notes'] ?? ''); ?></textarea>
                    <div style="margin-top:8px;"><button class="btn btn-gold" type="submit">Save Notes</button></div>
                </form>
            </div>
            <?php if (!empty($request['internal_notes'])): ?>
            <div style="margin-top:12px;">
                <span class="pw-lbl">Current Notes</span>
                <div style="white-space:pre-wrap;color:#a8bedc;font-size:.85rem;background:rgba(0,0,0,.2);border-radius:6px;padding:10px;border:1px solid rgba(54,243,255,.08);"><?php echo pe($request['internal_notes']); ?></div>
            </div>
            <?php endif; ?>
        </div>
    </details>
    <?php endif; ?>

    <!-- SECTION 4 — PROPOSAL -->
    <details class="pw-section" <?php echo ($currentProposal !== null || $isStaff) ? 'open' : ''; ?>>
        <summary>
            Proposal
            <?php if ($currentProposal): ?>
                &nbsp;&nbsp;<?php echo proposalStatusBadge((string)($currentProposal['status'] ?? 'draft')); ?>
            <?php else: ?>
                <span style="font-size:.68rem;color:#5a7a9e;font-weight:400;text-transform:none;letter-spacing:0;margin-left:6px;">Not started</span>
            <?php endif; ?>
        </summary>
        <div class="pw-section-body">

        <?php if ($currentProposal !== null): ?>
            <!-- Proposal exists: show summary -->
            <div class="pw-grid" style="margin-bottom:12px;">
                <div><span class="pw-lbl">Proposal ID</span><div class="pw-val pw-mono"><?php echo pe($currentProposal['proposal_id'] ?? ''); ?></div></div>
                <div><span class="pw-lbl">Status</span><div><?php echo proposalStatusBadge((string)($currentProposal['status'] ?? 'draft')); ?></div></div>
                <div><span class="pw-lbl">Project Title</span><div class="pw-val"><?php echo pe($currentProposal['project_title'] ?? '—'); ?></div></div>
                <div><span class="pw-lbl">Estimated Cost</span><div class="pw-val"><?php echo pe($currentProposal['estimated_cost'] ?? '—'); ?></div></div>
                <div><span class="pw-lbl">Estimated Time</span><div class="pw-val"><?php echo pe($currentProposal['estimated_time'] ?? '—'); ?></div></div>
                <div><span class="pw-lbl">Payment to Begin</span><div class="pw-val"><?php echo pe($currentProposal['payment_required_to_begin'] ?? '—'); ?></div></div>
            </div>
            <?php if ($currentProposal['proposed_work'] ?? ''): ?>
            <div style="margin-bottom:10px;"><span class="pw-lbl">Proposed Work</span><div class="pw-val" style="white-space:pre-wrap;"><?php echo pe($currentProposal['proposed_work']); ?></div></div>
            <?php endif; ?>
            <?php if ($currentProposal['deliverables'] ?? ''): ?>
            <div style="margin-bottom:10px;"><span class="pw-lbl">Deliverables</span><div class="pw-val" style="white-space:pre-wrap;"><?php echo pe($currentProposal['deliverables']); ?></div></div>
            <?php endif; ?>
            <?php if ($currentProposal['next_steps'] ?? ''): ?>
            <div style="margin-bottom:10px;"><span class="pw-lbl">Next Steps</span><div class="pw-val" style="white-space:pre-wrap;"><?php echo pe($currentProposal['next_steps']); ?></div></div>
            <?php endif; ?>

            <?php if ($isClient && (string)($currentProposal['status'] ?? '') === 'sent'): ?>
            <form method="post" style="margin-top:10px;" class="no-print">
                <input type="hidden" name="action" value="approve_proposal">
                <p style="color:#a8bedc;font-size:.84rem;margin:0 0 8px;">Please review the proposal above. Click below to approve.</p>
                <button class="btn btn-green" type="submit">✓ Approve Proposal</button>
            </form>
            <?php endif; ?>

            <?php if ($isClient && (string)($currentProposal['status'] ?? '') === 'accepted'): ?>
            <div style="margin-top:8px;color:#4ade80;font-size:.84rem;">✓ You approved this proposal.</div>
            <?php endif; ?>
        <?php else: ?>
            <p style="color:#5a7a9e;font-size:.84rem;margin:0 0 10px;">No proposal created yet.</p>
        <?php endif; ?>

        <?php if ($isStaff): ?>
            <!-- Staff proposal form -->
            <details style="margin-top:12px;" class="no-print">
                <summary style="cursor:pointer;color:#36f3ff;font-size:.82rem;font-weight:700;list-style:none;background:rgba(54,243,255,.06);border:1px solid rgba(54,243,255,.15);border-radius:6px;padding:8px 12px;">
                    <?php echo $currentProposal ? '✏️ Edit Proposal' : '＋ Create Proposal'; ?>
                </summary>
                <div style="padding:12px 0 0;">
                <form method="post">
                    <input type="hidden" name="action" value="save_proposal">
                    <input type="hidden" name="proposal_id" value="<?php echo pe($currentProposal['proposal_id'] ?? ''); ?>">
                    <div class="pw-grid">
                        <div><label>Client Name</label><input class="pw-input" type="text" name="client_name" value="<?php echo pe($currentProposal['client_name'] ?? $clientName); ?>"></div>
                        <div><label>Client Email</label><input class="pw-input" type="email" name="client_email" value="<?php echo pe($currentProposal['client_email'] ?? $clientEmail); ?>"></div>
                        <div><label>Project Title</label><input class="pw-input" type="text" name="project_title" value="<?php echo pe($currentProposal['project_title'] ?? ($request['project_type'] ?? '')); ?>"></div>
                        <div>
                            <label>Proposal Status</label>
                            <select name="proposal_status" class="pw-select">
                                <?php foreach ($proposalStatuses as $k => $v): ?>
                                    <option value="<?php echo pe($k); ?>" <?php echo ($currentProposal['status'] ?? 'draft') === $k ? 'selected' : ''; ?>><?php echo pe($v); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div><label>Estimated Cost</label><input class="pw-input" type="text" name="estimated_cost" value="<?php echo pe($currentProposal['estimated_cost'] ?? ($request['estimated_cost_range'] ?? '')); ?>"></div>
                        <div><label>Estimated Time</label><input class="pw-input" type="text" name="estimated_time" value="<?php echo pe($currentProposal['estimated_time'] ?? ($request['estimated_time_range'] ?? '')); ?>"></div>
                        <div><label>Payment Required to Begin</label><input class="pw-input" type="text" name="payment_required_to_begin" value="<?php echo pe($currentProposal['payment_required_to_begin'] ?? ''); ?>"></div>
                        <div><label>Timeline</label><input class="pw-input" type="text" name="timeline" value="<?php echo pe($currentProposal['timeline'] ?? ($request['timeline'] ?? '')); ?>"></div>
                        <div><label>Budget Comfort</label><input class="pw-input" type="text" name="budget_comfort" value="<?php echo pe($currentProposal['budget_comfort'] ?? ($request['budget_comfort'] ?? '')); ?>"></div>
                        <div><label>Repository Link</label><input class="pw-input" type="url" name="repo_link" value="<?php echo pe($currentProposal['repo_link'] ?? ($request['repo_link'] ?? '')); ?>"></div>
                    </div>
                    <div style="margin-top:8px;"><label>Request Summary</label><textarea class="pw-textarea" name="request_summary"><?php echo pe($currentProposal['request_summary'] ?? ($request['description'] ?? '')); ?></textarea></div>
                    <div style="margin-top:8px;"><label>Proposed Work</label><textarea class="pw-textarea" name="proposed_work"><?php echo pe($currentProposal['proposed_work'] ?? ''); ?></textarea></div>
                    <div style="margin-top:8px;"><label>Deliverables</label><textarea class="pw-textarea" name="deliverables"><?php echo pe($currentProposal['deliverables'] ?? ''); ?></textarea></div>
                    <div style="margin-top:8px;"><label>Revision Terms</label><textarea class="pw-textarea" name="revision_terms"><?php echo pe($currentProposal['revision_terms'] ?? ''); ?></textarea></div>
                    <div style="margin-top:8px;"><label>Assumptions</label><textarea class="pw-textarea" name="assumptions"><?php echo pe($currentProposal['assumptions'] ?? ''); ?></textarea></div>
                    <div style="margin-top:8px;"><label>Customer Responsibilities</label><textarea class="pw-textarea" name="customer_responsibilities"><?php echo pe($currentProposal['customer_responsibilities'] ?? ''); ?></textarea></div>
                    <div style="margin-top:8px;"><label>Next Steps</label><textarea class="pw-textarea" name="next_steps"><?php echo pe($currentProposal['next_steps'] ?? ($request['recommended_next_step'] ?? '')); ?></textarea></div>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;">
                        <button class="btn btn-blue" type="submit" name="action" value="save_proposal">Save Draft</button>
                        <button class="btn btn-gold" type="submit" name="action" value="send_proposal">Send to Client</button>
                    </div>
                </form>
                </div>
            </details>
        <?php endif; ?>
        </div>
    </details>

    <!-- SECTION 5 — PROJECT AGREEMENT -->
    <details class="pw-section" <?php echo ($currentAgreement !== null && $isClient) ? 'open' : ''; ?>>
        <summary>
            Project Agreement
            <?php if ($currentAgreement): ?>
                &nbsp;&nbsp;<?php echo agreementStatusBadge((string)($currentAgreement['status'] ?? 'draft')); ?>
            <?php else: ?>
                <span style="font-size:.68rem;color:#5a7a9e;font-weight:400;text-transform:none;letter-spacing:0;margin-left:6px;">Not started</span>
            <?php endif; ?>
        </summary>
        <div class="pw-section-body">

        <?php if ($currentAgreement !== null): ?>
            <div class="pw-grid" style="margin-bottom:12px;">
                <div><span class="pw-lbl">Agreement ID</span><div class="pw-val pw-mono"><?php echo pe($currentAgreement['agreement_id'] ?? ''); ?></div></div>
                <div><span class="pw-lbl">Status</span><div><?php echo agreementStatusBadge((string)($currentAgreement['status'] ?? 'draft')); ?></div></div>
                <div><span class="pw-lbl">Project Title</span><div class="pw-val"><?php echo pe($currentAgreement['project_title'] ?? '—'); ?></div></div>
                <div><span class="pw-lbl">Effective Date</span><div class="pw-val"><?php echo pe($currentAgreement['effective_date'] ?? '—'); ?></div></div>
                <div><span class="pw-lbl">Timeline</span><div class="pw-val"><?php echo pe($currentAgreement['timeline'] ?? '—'); ?></div></div>
                <div><span class="pw-lbl">Payment Terms</span><div class="pw-val"><?php echo pe($currentAgreement['payment_terms'] ?? '—'); ?></div></div>
                <?php if (!empty($currentAgreement['signed_by'])): ?>
                <div><span class="pw-lbl">Signed By</span><div class="pw-val"><?php echo pe($currentAgreement['signed_by']); ?></div></div>
                <div><span class="pw-lbl">Signed At</span><div class="pw-val"><?php echo pe(date('M j, Y g:i A', strtotime((string)($currentAgreement['signed_at'] ?? 'now')))); ?></div></div>
                <?php endif; ?>
            </div>
            <?php if ($currentAgreement['scope_of_work'] ?? ''): ?>
            <div style="margin-bottom:10px;"><span class="pw-lbl">Scope of Work</span><div class="pw-val" style="white-space:pre-wrap;"><?php echo pe($currentAgreement['scope_of_work']); ?></div></div>
            <?php endif; ?>
            <?php if ($currentAgreement['deliverables'] ?? ''): ?>
            <div style="margin-bottom:10px;"><span class="pw-lbl">Deliverables</span><div class="pw-val" style="white-space:pre-wrap;"><?php echo pe($currentAgreement['deliverables']); ?></div></div>
            <?php endif; ?>
            <?php if ($currentAgreement['revision_terms'] ?? ''): ?>
            <div style="margin-bottom:10px;"><span class="pw-lbl">Revision Terms</span><div class="pw-val" style="white-space:pre-wrap;"><?php echo pe($currentAgreement['revision_terms']); ?></div></div>
            <?php endif; ?>
            <?php if ($currentAgreement['customer_responsibilities'] ?? ''): ?>
            <div style="margin-bottom:10px;"><span class="pw-lbl">Customer Responsibilities</span><div class="pw-val" style="white-space:pre-wrap;"><?php echo pe($currentAgreement['customer_responsibilities']); ?></div></div>
            <?php endif; ?>

            <?php if ($isClient && (string)($currentAgreement['status'] ?? '') === 'sent'): ?>
            <form method="post" style="margin-top:12px;" class="no-print">
                <input type="hidden" name="action" value="sign_agreement">
                <p style="color:#a8bedc;font-size:.84rem;margin:0 0 8px;">Please review the agreement above. Enter your full name to sign.</p>
                <div style="display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap;">
                    <div style="flex:1;min-width:180px;">
                        <label>Full Name</label>
                        <input class="pw-input" type="text" name="signed_name" placeholder="Your full name" required>
                    </div>
                    <button class="btn btn-green" type="submit">✍️ Sign Agreement</button>
                </div>
            </form>
            <?php endif; ?>

            <?php if ($isClient && (string)($currentAgreement['status'] ?? '') === 'signed'): ?>
            <div style="margin-top:8px;color:#4ade80;font-size:.84rem;">✓ Agreement signed.</div>
            <?php endif; ?>
        <?php else: ?>
            <p style="color:#5a7a9e;font-size:.84rem;margin:0 0 10px;">No agreement created yet.</p>
        <?php endif; ?>

        <?php if ($isStaff): ?>
            <details style="margin-top:12px;" class="no-print">
                <summary style="cursor:pointer;color:#36f3ff;font-size:.82rem;font-weight:700;list-style:none;background:rgba(54,243,255,.06);border:1px solid rgba(54,243,255,.15);border-radius:6px;padding:8px 12px;">
                    <?php echo $currentAgreement ? '✏️ Edit Agreement' : '＋ Create Agreement'; ?>
                </summary>
                <div style="padding:12px 0 0;">
                <form method="post">
                    <input type="hidden" name="action" value="save_agreement">
                    <input type="hidden" name="agreement_id" value="<?php echo pe($currentAgreement['agreement_id'] ?? ''); ?>">
                    <div class="pw-grid">
                        <div><label>Client Name</label><input class="pw-input" type="text" name="a_client_name" value="<?php echo pe($currentAgreement['client_name'] ?? $clientName); ?>"></div>
                        <div><label>Client Email</label><input class="pw-input" type="email" name="a_client_email" value="<?php echo pe($currentAgreement['client_email'] ?? $clientEmail); ?>"></div>
                        <div><label>Project Title</label><input class="pw-input" type="text" name="a_project_title" value="<?php echo pe($currentAgreement['project_title'] ?? ($currentProposal['project_title'] ?? ($request['project_type'] ?? ''))); ?>"></div>
                        <div><label>Effective Date</label><input class="pw-input" type="date" name="effective_date" value="<?php echo pe($currentAgreement['effective_date'] ?? date('Y-m-d')); ?>"></div>
                        <div>
                            <label>Agreement Status</label>
                            <select name="agreement_status" class="pw-select">
                                <?php foreach ($agreementStatuses as $k => $v): ?>
                                    <option value="<?php echo pe($k); ?>" <?php echo ($currentAgreement['status'] ?? 'draft') === $k ? 'selected' : ''; ?>><?php echo pe($v); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div><label>Timeline</label><input class="pw-input" type="text" name="a_timeline" value="<?php echo pe($currentAgreement['timeline'] ?? ($currentProposal['estimated_time'] ?? ($request['timeline'] ?? ''))); ?>"></div>
                        <div><label>Payment Terms</label><input class="pw-input" type="text" name="payment_terms" value="<?php echo pe($currentAgreement['payment_terms'] ?? ($currentProposal['payment_required_to_begin'] ?? '')); ?>"></div>
                    </div>
                    <div style="margin-top:8px;"><label>Scope of Work</label><textarea class="pw-textarea" name="scope_of_work"><?php echo pe($currentAgreement['scope_of_work'] ?? ($currentProposal['proposed_work'] ?? '')); ?></textarea></div>
                    <div style="margin-top:8px;"><label>Deliverables</label><textarea class="pw-textarea" name="a_deliverables"><?php echo pe($currentAgreement['deliverables'] ?? ($currentProposal['deliverables'] ?? '')); ?></textarea></div>
                    <div style="margin-top:8px;"><label>Revision Terms</label><textarea class="pw-textarea" name="a_revision_terms"><?php echo pe($currentAgreement['revision_terms'] ?? ($currentProposal['revision_terms'] ?? '')); ?></textarea></div>
                    <div style="margin-top:8px;"><label>Change Request Policy</label><textarea class="pw-textarea" name="change_request_policy"><?php echo pe($currentAgreement['change_request_policy'] ?? 'Changes outside scope require approval and may affect timeline and cost.'); ?></textarea></div>
                    <div style="margin-top:8px;"><label>Customer Responsibilities</label><textarea class="pw-textarea" name="a_customer_responsibilities"><?php echo pe($currentAgreement['customer_responsibilities'] ?? ($currentProposal['customer_responsibilities'] ?? '')); ?></textarea></div>
                    <details style="margin-top:8px;">
                        <summary style="cursor:pointer;color:#5a7a9e;font-size:.78rem;">Standard Legal Terms (click to expand)</summary>
                        <div style="padding-top:8px;">
                            <div><label>Third-Party Licenses</label><textarea class="pw-textarea" name="third_party_licenses"><?php echo pe($currentAgreement['third_party_licenses'] ?? 'Client supplies or approves all required third-party licenses and assets.'); ?></textarea></div>
                            <div style="margin-top:8px;"><label>Source Code / Ownership Terms</label><textarea class="pw-textarea" name="source_code_ownership_terms"><?php echo pe($currentAgreement['source_code_ownership_terms'] ?? 'Ownership terms follow approved proposal and Runlevel Systems Terms of Service.'); ?></textarea></div>
                            <div style="margin-top:8px;"><label>Testing and Acceptance</label><textarea class="pw-textarea" name="testing_and_acceptance"><?php echo pe($currentAgreement['testing_and_acceptance'] ?? 'Client reviews deliverables within agreed review window and confirms acceptance in writing.'); ?></textarea></div>
                            <div style="margin-top:8px;"><label>Termination</label><textarea class="pw-textarea" name="termination"><?php echo pe($currentAgreement['termination'] ?? 'Either party may terminate with written notice; completed work remains billable.'); ?></textarea></div>
                            <div style="margin-top:8px;"><label>Legal Notice</label><textarea class="pw-textarea" name="legal_notice"><?php echo pe($currentAgreement['legal_notice'] ?? ''); ?></textarea></div>
                            <div style="margin-top:8px;"><label>Signature Placeholder</label><textarea class="pw-textarea" name="signature_placeholder"><?php echo pe($currentAgreement['signature_placeholder'] ?? 'Client Signature: ____________________   Date: __________'); ?></textarea></div>
                        </div>
                    </details>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;">
                        <button class="btn btn-blue" type="submit" name="action" value="save_agreement">Save Draft</button>
                        <button class="btn btn-gold" type="submit" name="action" value="send_agreement">Send to Client</button>
                    </div>
                </form>
                </div>
            </details>
        <?php endif; ?>
        </div>
    </details>

    <!-- SECTION 6 — PAYMENTS -->
    <details class="pw-section">
        <summary>Payments</summary>
        <div class="pw-section-body">
            <p style="color:#5a7a9e;font-size:.84rem;margin:0 0 8px;">Payments are managed via PayPal invoices or approved payment method.</p>
            <?php if ($isStaff): ?>
            <div style="display:flex;gap:8px;flex-wrap:wrap;" class="no-print">
                <a href="/staff/paypal-setup.php" class="btn btn-teal">PayPal Setup</a>
                <a href="/payments.php" class="btn btn-teal">Payment Info</a>
            </div>
            <?php else: ?>
            <a href="/payments.php" class="btn btn-teal">Payment Information</a>
            <?php endif; ?>
        </div>
    </details>

    <!-- SECTION 7 — COMMUNICATION TIMELINE -->
    <details class="pw-section">
        <summary>Project Timeline</summary>
        <div class="pw-section-body">
            <?php if (empty($timeline)): ?>
                <p style="color:#5a7a9e;font-size:.84rem;margin:0;">No activity recorded yet.</p>
            <?php else: ?>
            <ul class="pw-timeline">
                <?php foreach (array_reverse($timeline) as $ev): ?>
                <li>
                    <div class="tl-icon" style="color:<?php echo pe($ev['color']); ?>"><?php echo $ev['icon']; ?></div>
                    <div>
                        <div class="tl-text" style="color:<?php echo pe($ev['color']); ?>"><?php echo pe($ev['text']); ?></div>
                        <div class="tl-date"><?php echo pe(date('M j, Y g:i A', $ev['ts'])); ?></div>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>
    </details>

    <!-- SECTION 8 — FILES -->
    <details class="pw-section">
        <summary>Files &amp; Attachments</summary>
        <div class="pw-section-body">
            <p class="pw-files-info">File attachments and document uploads are coming soon. Contact us directly to share files for this project.</p>
            <?php if ($request['repo_link'] ?? ''): ?>
            <div style="margin-top:8px;"><span class="pw-lbl">Repository / Project Link</span><div><a href="<?php echo pe($request['repo_link']); ?>" target="_blank" rel="noopener noreferrer" style="color:#36f3ff;font-size:.86rem;"><?php echo pe($request['repo_link']); ?></a></div></div>
            <?php endif; ?>
        </div>
    </details>

<?php endif; ?>

</div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/jquery-1.12.3.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
