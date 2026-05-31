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
    'Existing project fix',
    'Infrastructure / Backend',
    'Not sure',
];
$contactOptions = ['Email', 'Phone', 'Google Meet', 'Discord', 'Not sure'];
$gameTypes = ['Game / Interactive project', 'Server / Mod / Script'];

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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_estimate'])) {
    $name = $form['name'];
    $email = $form['email'];
    $phone = $form['phone'];
    $discordUsername = $form['discord_username'];
    $contactMethod = $form['contact_method'];
    $projectType = $form['project_type'];
    $repoLink = $form['repo_link'];
    $description = $form['description'];
    $desiredUsername = strtolower($form['desired_username']);

    if ($name === '') {
        $error = 'Please enter your name.';
    } elseif ($email === '') {
        $error = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (!in_array($projectType, $projectTypes, true)) {
        $error = 'Please select a project type.';
    } elseif ($description === '') {
        $error = 'Please describe your request.';
    } elseif (!in_array($contactMethod, $contactOptions, true)) {
        $error = 'Please select a preferred contact method.';
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
        $estimateId = portalGenerateUniqueEstimateId();
        $request = [
            'id' => bin2hex(random_bytes(8)),
            'estimate_id' => $estimateId,
            'client_username' => $clientUsername,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'discord_username' => $discordUsername,
            'contact_method' => $contactMethod,
            'project_type' => $projectType,
            'repo_link' => $repoLink,
            'description' => $description,
            'status' => 'new',
            'notes' => '',
            'created_at' => date('c'),
        ];

        if (!portalAppendEstimateRequest($request)) {
            $error = 'There was an error saving your request. Please try again or contact us directly.';
        } else {
            $success = true;
            $submitted = [
                'request' => $request,
                'created_account' => $createdAccount,
                'needs_verification' => !$isLoggedIn,
                'show_discord' => in_array($projectType, $gameTypes, true),
            ];

            if ($createdAccount !== null) {
                $verifyLink = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'runlevel.systems')
                    . '/verify-email.php?token=' . urlencode((string)$createdAccount['verification_token']);
                $subject = 'Verify your Runlevel Systems account';
                $body = "Hello {$name},\n\n"
                    . "We received your project estimate request.\n\n"
                    . "Estimate ID:\n{$estimateId}\n\n"
                    . "A client dashboard account was created for you:\n\n"
                    . "Username:\n{$clientUsername}\n\n"
                    . "Temporary password:\n{$createdAccount['temporary_password']}\n\n"
                    . "Please verify your email before logging in:\n\n"
                    . "{$verifyLink}\n\n"
                    . "After verification, you can log in to view your dashboard and track project information.\n\n"
                    . "Runlevel Systems\n"
                    . "DESIGN • DEBUG • DEPLOY\n";
                send_email($email, $subject, $body);
            }
        }
    }
}

$showDiscordHint = in_array($form['project_type'], $gameTypes, true) || $form['contact_method'] === 'Discord';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="assets/images/RL-icon.png">
    <title>Project Requirements &amp; Estimate | Runlevel Systems</title>
    <link href="assets/css/coreloop.css" rel="stylesheet">
    <style>
        .est-wrap { padding: 40px 0 80px; }
        .est-hero { padding: 48px 0 24px; text-align: center; }
        .est-hero h1 { color: #ffc600; font-size: 2rem; margin-bottom: 10px; }
        .est-hero .subtitle { color: #7a9ac0; font-size: 1.05rem; max-width: 700px; margin: 0 auto; }
        .est-card { background: #0c1729; border: 1px solid rgba(54,243,255,0.18); border-radius: 10px; padding: 30px; margin-bottom: 24px; }
        .est-card h2 { color: #36f3ff; margin-top: 0; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 20px; }
        .row2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        @media (max-width: 720px) { .row2 { grid-template-columns:1fr; } }
        label { color:#a8bedc; font-size:.9rem; margin-bottom:4px; display:block; }
        .field { margin-bottom:16px; }
        .est-input, .est-textarea, .est-select { background:#09111d; color:#eaf3ff; border:1px solid rgba(54,243,255,.25); border-radius:6px; padding:10px 12px; width:100%; }
        .est-textarea { min-height:140px; resize:vertical; }
        .help-note { color:#7a9ac0; font-size:.88rem; line-height:1.55; margin-bottom:18px; }
        .muted { color:#5a7a9e; font-size:.82rem; }
        .alert-error { background: rgba(239,68,68,0.18); border: 1px solid #ef4444; color: #fecaca; border-radius: 6px; padding: 12px 14px; margin-bottom: 18px; }
        .success-card { background: #0c1729; border: 1px solid rgba(34,197,94,0.35); border-radius: 10px; padding: 30px; max-width: 760px; margin: 0 auto; }
        .success-card h2 { color: #22c55e; margin-top:0; }
        .success-card p { color:#a8bedc; }
        .eid { background: rgba(255,198,0,0.08); border: 2px solid rgba(255,198,0,0.4); border-radius: 8px; padding: 14px 16px; margin: 14px 0 18px; }
        .eid .v { color:#ffc600; font-family:monospace; font-size:1.35rem; font-weight:700; }
        .compact-discord { display:flex; align-items:center; gap:10px; flex-wrap:wrap; background: rgba(88,101,242,0.12); border: 1px solid rgba(88,101,242,0.35); border-radius: 8px; padding: 10px 12px; margin-top: 10px; }
        .compact-discord span { color:#a8bedc; font-size:.88rem; }
        .discord-link { display:inline-block; padding:6px 12px; border-radius:6px; background:#5865f2; color:#fff; text-decoration:none; font-weight:700; font-size:.85rem; }
        .discord-link:hover { color:#fff; text-decoration:none; background:#7289da; }
        .btn-submit { background: #ffc600; color: #08111f; border: none; border-radius: 6px; padding: 13px 28px; font-weight: 700; font-size: 1rem; cursor: pointer; }
        .action-links { display:flex; gap:10px; flex-wrap:wrap; margin-top:16px; }
        .action-links a { display:inline-block; padding:10px 14px; border-radius:6px; text-decoration:none; font-weight:700; }
        .btn-a { background:#0a84ff; color:#fff; }
        .btn-b { background:rgba(54,243,255,0.12); border:1px solid rgba(54,243,255,.25); color:#36f3ff; }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navigation.php'; ?>

<section class="est-hero">
    <div class="container">
        <h1>Project Requirements &amp; Estimate</h1>
        <p class="subtitle">Please complete and submit this estimate request so we have the information needed to review your project. After submission, we will give you an Estimate ID that you can reference when contacting us.</p>
    </div>
</section>

<section class="est-wrap">
<div class="container">
<?php if ($success && $submitted): ?>
    <?php $req = $submitted['request']; ?>
    <div class="success-card">
        <h2>✅ Request Received</h2>
        <p>We received your estimate request.</p>
        <div class="eid">
            <div>Your Estimate ID:</div>
            <div class="v"><?php echo pe($req['estimate_id']); ?></div>
        </div>

        <?php if (!empty($req['client_username'])): ?>
            <p><strong style="color:#eaf3ff;">Your client account:</strong> <?php echo pe($req['client_username']); ?></p>
        <?php endif; ?>

        <?php if (!empty($submitted['created_account'])): ?>
            <p>Please check your email to verify your account before logging in.</p>
            <p><strong style="color:#eaf3ff;">Temporary password:</strong> <?php echo pe($submitted['created_account']['temporary_password']); ?></p>
            <p class="muted">Please save this. You can change it later once account settings are added.</p>
            <p class="muted">TODO: Add password reset flow and remove temporary-password delivery.</p>
        <?php endif; ?>

        <p>We will review your request and contact you using the information provided.</p>

        <?php if (!empty($submitted['show_discord'])): ?>
            <div class="compact-discord">
                <span>Game or server project? You can also join our Discord and mention your Estimate ID.</span>
                <?php /* TODO: Replace with official Runlevel Systems Discord invite. */ ?>
                <a class="discord-link" href="https://discord.gg/REPLACE_ME" target="_blank" rel="noopener noreferrer">Join Discord</a>
            </div>
        <?php endif; ?>

        <div class="action-links">
            <a class="btn-a" href="/dashboard.php">Go To Dashboard</a>
            <a class="btn-b" href="/estimate.php">Submit Another Request</a>
            <a class="btn-b" href="/contact.php">Contact Runlevel Systems</a>
        </div>
    </div>
<?php else: ?>

    <?php if ($error !== ''): ?>
        <div class="alert-error"><?php echo pe($error); ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="submit_estimate" value="1">

        <?php if (!$isLoggedIn): ?>
            <div class="est-card">
                <h2>Account Information</h2>
                <p class="help-note">We will create a free client dashboard account so you can track this request, view estimates, and communicate about the project.</p>
                <p class="help-note" style="margin-top:-6px;">Already have an account? <a href="/login.php?return=<?php echo urlencode('/estimate.php'); ?>" style="color:#36f3ff;">Log in and continue estimate</a></p>
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
                    <label for="phone">Phone <span class="muted">optional</span></label>
                    <input id="phone" type="text" name="phone" class="est-input" value="<?php echo pe($form['phone']); ?>">
                </div>
                <div class="field">
                    <label for="contact_method">Preferred contact method *</label>
                    <select id="contact_method" name="contact_method" class="est-select" required>
                        <?php foreach ($contactOptions as $opt): ?>
                            <option value="<?php echo pe($opt); ?>" <?php echo $form['contact_method'] === $opt ? 'selected' : ''; ?>><?php echo pe($opt); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="field" id="discord-username-wrap" style="display:<?php echo $showDiscordHint ? 'block' : 'none'; ?>;">
                <label for="discord_username">Discord username <span class="muted">optional</span></label>
                <input id="discord_username" type="text" name="discord_username" class="est-input" value="<?php echo pe($form['discord_username']); ?>" placeholder="username#0000 or display name">
            </div>

            <div id="discord-line" class="compact-discord" style="display:<?php echo $showDiscordHint ? 'flex' : 'none'; ?>;">
                <span>Game or server project? You can also join our Discord and mention your Estimate ID.</span>
                <?php /* TODO: Replace with official Runlevel Systems Discord invite. */ ?>
                <a class="discord-link" href="https://discord.gg/REPLACE_ME" target="_blank" rel="noopener noreferrer">Join Discord</a>
            </div>
        </div>

        <div class="est-card">
            <h2>Project Details</h2>
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
                <label for="repo_link">Repository / project link <span class="muted">optional</span></label>
                <input id="repo_link" type="url" name="repo_link" class="est-input" value="<?php echo pe($form['repo_link']); ?>" placeholder="https://github.com/example/project">
            </div>
            <div class="field">
                <label for="description">Description *</label>
                <textarea id="description" name="description" class="est-textarea" required><?php echo pe($form['description']); ?></textarea>
            </div>
        </div>

        <button type="submit" class="btn-submit">Submit Estimate Request</button>
    </form>

<?php endif; ?>
</div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/jquery-1.12.3.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script>
(function () {
    var gameTypes = <?php echo json_encode($gameTypes); ?>;
    var projectType = document.getElementById('project_type');
    var contactMethod = document.getElementById('contact_method');
    var discordLine = document.getElementById('discord-line');
    var discordWrap = document.getElementById('discord-username-wrap');

    function shouldShowDiscord() {
        var byType = projectType && gameTypes.indexOf(projectType.value) !== -1;
        var byContact = contactMethod && contactMethod.value === 'Discord';
        return byType || byContact;
    }

    function syncDiscordUI() {
        if (!discordLine || !discordWrap) { return; }
        var show = shouldShowDiscord();
        discordLine.style.display = show ? 'flex' : 'none';
        discordWrap.style.display = show ? 'block' : 'none';
    }

    if (projectType) { projectType.addEventListener('change', syncDiscordUI); }
    if (contactMethod) { contactMethod.addEventListener('change', syncDiscordUI); }
    syncDiscordUI();
})();
</script>
</body>
</html>
