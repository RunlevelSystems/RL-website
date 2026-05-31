<?php
define('WDS_SYSTEM', true);
require_once __DIR__ . '/includes/portal-helpers.php';
require_once __DIR__ . '/includes/email.php';

$current_page = 'estimate';
$header_class = 'inner-header';

$projectTypes = [
    'Website',
    'Web application',
    'Mobile app',
    'Business application',
    'Training / Simulation',
    'Game / Interactive project',
    'Server / Mod / Script',
    'Infrastructure / Backend',
    'Existing project fix',
    'Other',
];
$projectStages = ['Idea', 'Planning', 'In progress', 'Needs rescue', 'Maintenance', 'Not sure'];
$projectSizes = ['Small task', 'Small project', 'Medium project', 'Large project', 'Not sure'];
$timelineOptions = ['ASAP', '1-2 weeks', '2-4 weeks', '1-3 months', 'Flexible'];
$budgetComfortOptions = ['Under $250', '$250-$1,000', '$1,000-$5,000', '$5,000+', 'Not sure yet'];
$contactOptions = ['Email', 'Phone', 'Google Meet', 'Discord', 'Not sure'];

$error = '';
$success = false;
$submitted = null;

$isLoggedIn = portalIsLoggedIn();
$sessionUser = portalGetUser();
$fullUser = null;
if ($isLoggedIn && !empty($sessionUser['username'])) {
    $fullUser = portalFindUserByUsername((string)$sessionUser['username']);
}

$defaults = [
    'name' => (string)($fullUser['display_name'] ?? ''),
    'email' => (string)($fullUser['email'] ?? ''),
    'phone' => (string)($fullUser['phone'] ?? ''),
    'discord_username' => (string)($fullUser['discord_username'] ?? ''),
    'contact_method' => 'Email',
    'project_type' => '',
    'project_stage' => '',
    'project_size' => '',
    'timeline' => '',
    'budget_comfort' => '',
    'repo_link' => '',
    'description' => '',
    'desired_username' => '',
];

$form = $defaults;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($form as $key => $value) {
        $form[$key] = trim((string)($_POST[$key] ?? ''));
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_request'])) {
    $name = $form['name'];
    $email = $form['email'];
    $phone = $form['phone'];
    $discordUsername = $form['discord_username'];
    $contactMethod = $form['contact_method'];
    $projectType = $form['project_type'];
    $projectStage = $form['project_stage'];
    $projectSize = $form['project_size'];
    $timeline = $form['timeline'];
    $budgetComfort = $form['budget_comfort'];
    $repoLink = $form['repo_link'];
    $description = $form['description'];
    $desiredUsername = strtolower($form['desired_username']);

    if ($name === '') {
        $error = 'Please enter your name.';
    } elseif ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (!in_array($projectType, $projectTypes, true)) {
        $error = 'Please select a project type.';
    } elseif (!in_array($projectStage, $projectStages, true)) {
        $error = 'Please select a project stage.';
    } elseif (!in_array($projectSize, $projectSizes, true)) {
        $error = 'Please select a project size.';
    } elseif (!in_array($timeline, $timelineOptions, true)) {
        $error = 'Please select a timeline.';
    } elseif (!in_array($budgetComfort, $budgetComfortOptions, true)) {
        $error = 'Please select your budget comfort.';
    } elseif (!in_array($contactMethod, $contactOptions, true)) {
        $error = 'Please select a preferred contact method.';
    } elseif ($description === '') {
        $error = 'Please describe your request.';
    } elseif ($repoLink !== '' && !filter_var($repoLink, FILTER_VALIDATE_URL)) {
        $error = 'The repository or project link does not appear to be a valid URL.';
    } elseif (!$isLoggedIn && $desiredUsername === '') {
        $error = 'Please enter a desired username.';
    } elseif (!$isLoggedIn && !portalIsValidEstimateUsername($desiredUsername)) {
        $error = 'Username must be at least 3 characters and use only letters, numbers, dash, or underscore.';
    }

    $createdAccount = null;
    $clientUsername = '';
    if ($error === '') {
        if ($isLoggedIn && !empty($sessionUser['username'])) {
            $clientUsername = (string)$sessionUser['username'];
        } else {
            $createError = '';
            $createdAccount = portalCreateClientUserFromEstimate(
                $desiredUsername,
                $name,
                $email,
                $phone,
                $discordUsername,
                $createError
            );
            if ($createdAccount === false) {
                $error = $createError !== '' ? $createError : 'Unable to create your client account right now.';
            } else {
                $clientUsername = (string)$createdAccount['username'];
            }
        }
    }

    if ($error === '') {
        $requestId = portalGenerateUniqueRequestId();
        $request = [
            'id' => bin2hex(random_bytes(8)),
            'request_id' => $requestId,
            'estimate_id' => $requestId,
            'client_username' => $clientUsername,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'discord_username' => $discordUsername,
            'contact_method' => $contactMethod,
            'project_type' => $projectType,
            'project_stage' => $projectStage,
            'project_size' => $projectSize,
            'timeline' => $timeline,
            'budget_comfort' => $budgetComfort,
            'repo_link' => $repoLink,
            'description' => $description,
            'status' => 'new',
            'estimated_cost_range' => '',
            'estimated_time_range' => '',
            'staff_summary' => '',
            'recommended_next_step' => '',
            'internal_notes' => '',
            'proposal_ids' => [],
            'agreement_ids' => [],
            'created_at' => date('c'),
        ];

        if (!portalAppendProjectRequest($request)) {
            $error = 'There was an error saving your request. Please try again or contact us directly.';
        } else {
            $success = true;
            $submitted = [
                'request' => $request,
                'created_account' => $createdAccount,
            ];

            $ackSubject = 'Project Request Received - ' . $requestId;
            $ackBody = "Hello {$name},\n\n"
                . "Project Request Received\n\n"
                . "Request ID:\n{$requestId}\n\n"
                . "Please save this ID. You can reference it if you contact us by email, phone, Google Meet, or Discord.\n\n"
                . "Runlevel Systems\n"
                . "DESIGN • DEBUG • DEPLOY\n";
            send_email($email, $ackSubject, $ackBody);

            if ($createdAccount !== null) {
                $verifyLink = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'runlevel.systems')
                    . '/verify-email.php?token=' . urlencode((string)$createdAccount['verification_token']);
                $subject = 'Verify your Runlevel Systems account - ' . $requestId;
                $body = "Hello {$name},\n\n"
                    . "We received your project request.\n\n"
                    . "Request ID:\n{$requestId}\n\n"
                    . "A client dashboard account was created for you:\n\n"
                    . "Username:\n{$clientUsername}\n\n"
                    . "Temporary password:\n{$createdAccount['temporary_password']}\n\n"
                    . "Please verify your email before logging in:\n\n"
                    . "{$verifyLink}\n\n"
                    . "Runlevel Systems\n"
                    . "DESIGN • DEBUG • DEPLOY\n";
                send_email($email, $subject, $body);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="assets/images/RL-icon.png">
    <title>Project Request | Runlevel Systems</title>
    <link href="assets/css/coreloop.css" rel="stylesheet">
    <style>
        .est-wrap { padding: 32px 0 64px; }
        .est-hero { padding: 36px 0 18px; text-align: center; }
        .est-hero h1 { color: #ffc600; font-size: 1.8rem; margin-bottom: 8px; }
        .est-hero .subtitle { color: #7a9ac0; font-size: 1rem; max-width: 760px; margin: 0 auto; }
        .est-card { background: #0c1729; border: 1px solid rgba(54,243,255,0.18); border-radius: 10px; padding: 24px; margin-bottom: 18px; }
        .est-card h2 { color: #36f3ff; margin-top: 0; font-size: 1rem; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 14px; }
        .row2 { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
        @media (max-width: 740px) { .row2 { grid-template-columns:1fr; } }
        label { color:#a8bedc; font-size:.85rem; margin-bottom:4px; display:block; }
        .field { margin-bottom:12px; }
        .est-input, .est-textarea, .est-select { background:#09111d; color:#eaf3ff; border:1px solid rgba(54,243,255,.25); border-radius:6px; padding:9px 11px; width:100%; }
        .est-textarea { min-height:130px; resize:vertical; }
        .help-note { color:#7a9ac0; font-size:.84rem; line-height:1.55; margin-bottom:10px; }
        .muted { color:#5a7a9e; font-size:.8rem; }
        .alert-error { background: rgba(239,68,68,0.18); border: 1px solid #ef4444; color: #fecaca; border-radius: 6px; padding: 12px 14px; margin-bottom: 18px; }
        .success-card { background: #0c1729; border: 1px solid rgba(34,197,94,0.35); border-radius: 10px; padding: 24px; max-width: 760px; margin: 0 auto; }
        .success-card h2 { color: #22c55e; margin-top:0; }
        .eid { background: rgba(255,198,0,0.08); border: 2px solid rgba(255,198,0,0.4); border-radius: 8px; padding: 14px 16px; margin: 14px 0 18px; }
        .eid .v { color:#ffc600; font-family:monospace; font-size:1.3rem; font-weight:700; }
        .btn-submit { background: #ffc600; color: #08111f; border: none; border-radius: 6px; padding: 12px 22px; font-weight: 700; font-size: .95rem; cursor: pointer; }
        .action-links { display:flex; gap:10px; flex-wrap:wrap; margin-top:14px; }
        .action-links a { display:inline-block; padding:9px 12px; border-radius:6px; text-decoration:none; font-weight:700; font-size:.85rem; }
        .btn-a { background:#0a84ff; color:#fff; }
        .btn-b { background:rgba(54,243,255,0.12); border:1px solid rgba(54,243,255,.25); color:#36f3ff; }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navigation.php'; ?>

<section class="est-hero">
    <div class="container">
        <h1>Project Request</h1>
        <p class="subtitle">Tell us what you need. We will review it and respond with the next step.</p>
    </div>
</section>

<section class="est-wrap">
<div class="container">
<?php if ($success && $submitted): ?>
    <?php $req = $submitted['request']; ?>
    <div class="success-card">
        <h2>Project Request Received</h2>
        <p>Your Request ID:</p>
        <div class="eid"><div class="v"><?php echo pe($req['request_id']); ?></div></div>
        <p>Please save this ID. You can reference it if you contact us by email, phone, Google Meet, or Discord.</p>

        <?php if (!empty($req['client_username'])): ?>
            <p><strong style="color:#eaf3ff;">Your client account:</strong> <?php echo pe($req['client_username']); ?></p>
        <?php endif; ?>

        <?php if (!empty($submitted['created_account'])): ?>
            <p>Please check your email to verify your account before logging in.</p>
            <p><strong style="color:#eaf3ff;">Temporary password:</strong> <?php echo pe($submitted['created_account']['temporary_password']); ?></p>
        <?php endif; ?>

        <div class="action-links">
            <a class="btn-a" href="/dashboard.php">Go To Dashboard</a>
            <a class="btn-b" href="/estimate.php">Submit Another Project Request</a>
            <a class="btn-b" href="/contact.php">Contact Runlevel Systems</a>
        </div>
    </div>
<?php else: ?>

    <?php if ($error !== ''): ?>
        <div class="alert-error"><?php echo pe($error); ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="submit_request" value="1">

        <?php if (!$isLoggedIn): ?>
            <div class="est-card">
                <h2>Account Information</h2>
                <p class="help-note">We create a client dashboard account so you can track your project request, proposal, and agreement.</p>
                <div class="field">
                    <label for="desired_username">Desired username *</label>
                    <input id="desired_username" type="text" name="desired_username" required class="est-input" value="<?php echo pe($form['desired_username']); ?>" placeholder="letters, numbers, dash, underscore">
                </div>
            </div>
        <?php endif; ?>

        <div class="est-card">
            <h2>Contact Information</h2>
            <div class="row2">
                <div class="field">
                    <label for="name">Name *</label>
                    <input id="name" type="text" name="name" required class="est-input" value="<?php echo pe($form['name']); ?>">
                </div>
                <div class="field">
                    <label for="email">Email *</label>
                    <input id="email" type="email" name="email" required class="est-input" value="<?php echo pe($form['email']); ?>">
                </div>
            </div>
            <div class="row2">
                <div class="field">
                    <label for="phone">Phone</label>
                    <input id="phone" type="text" name="phone" class="est-input" value="<?php echo pe($form['phone']); ?>">
                </div>
                <div class="field">
                    <label for="discord_username">Discord username</label>
                    <input id="discord_username" type="text" name="discord_username" class="est-input" value="<?php echo pe($form['discord_username']); ?>">
                </div>
            </div>
            <div class="field">
                <label for="contact_method">Preferred contact method *</label>
                <select id="contact_method" name="contact_method" class="est-select" required>
                    <option value="">— Select —</option>
                    <?php foreach ($contactOptions as $opt): ?>
                        <option value="<?php echo pe($opt); ?>" <?php echo $form['contact_method'] === $opt ? 'selected' : ''; ?>><?php echo pe($opt); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="est-card">
            <h2>Project Details</h2>
            <div class="row2">
                <div class="field">
                    <label for="project_type">Project type *</label>
                    <select id="project_type" name="project_type" class="est-select" required>
                        <option value="">— Select —</option>
                        <?php foreach ($projectTypes as $opt): ?>
                            <option value="<?php echo pe($opt); ?>" <?php echo $form['project_type'] === $opt ? 'selected' : ''; ?>><?php echo pe($opt); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="project_stage">Project stage *</label>
                    <select id="project_stage" name="project_stage" class="est-select" required>
                        <option value="">— Select —</option>
                        <?php foreach ($projectStages as $opt): ?>
                            <option value="<?php echo pe($opt); ?>" <?php echo $form['project_stage'] === $opt ? 'selected' : ''; ?>><?php echo pe($opt); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row2">
                <div class="field">
                    <label for="project_size">Project size *</label>
                    <select id="project_size" name="project_size" class="est-select" required>
                        <option value="">— Select —</option>
                        <?php foreach ($projectSizes as $opt): ?>
                            <option value="<?php echo pe($opt); ?>" <?php echo $form['project_size'] === $opt ? 'selected' : ''; ?>><?php echo pe($opt); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="timeline">Timeline *</label>
                    <select id="timeline" name="timeline" class="est-select" required>
                        <option value="">— Select —</option>
                        <?php foreach ($timelineOptions as $opt): ?>
                            <option value="<?php echo pe($opt); ?>" <?php echo $form['timeline'] === $opt ? 'selected' : ''; ?>><?php echo pe($opt); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row2">
                <div class="field">
                    <label for="budget_comfort">Budget comfort *</label>
                    <select id="budget_comfort" name="budget_comfort" class="est-select" required>
                        <option value="">— Select —</option>
                        <?php foreach ($budgetComfortOptions as $opt): ?>
                            <option value="<?php echo pe($opt); ?>" <?php echo $form['budget_comfort'] === $opt ? 'selected' : ''; ?>><?php echo pe($opt); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="repo_link">Repository / project link</label>
                    <input id="repo_link" type="url" name="repo_link" class="est-input" value="<?php echo pe($form['repo_link']); ?>" placeholder="https://github.com/example/project">
                </div>
            </div>
            <div class="field">
                <label for="description">Project description *</label>
                <textarea id="description" name="description" class="est-textarea" required><?php echo pe($form['description']); ?></textarea>
            </div>
            <p class="help-note">This form starts the process. We review your request and may respond with questions, a rough estimate, a proposal, or a project agreement depending on the size and type of work.</p>
        </div>

        <button type="submit" class="btn-submit">Submit Project Request</button>
    </form>

<?php endif; ?>
</div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/jquery-1.12.3.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
