<?php
define('WDS_SYSTEM', true);
require_once __DIR__ . '/includes/portal-helpers.php';

$current_page = 'estimate';
$header_class = 'inner-header';

// Project types that trigger the Discord community invite block
$gameTypes = ['Game / Interactive project', 'Server / Mod / Script'];

$error   = '';
$warning = '';
$success = false;
$submitted = null;

/**
 * Generate a unique Estimate ID in the format RLS-YYYYMMDD-XXXX.
 * Uses the current date plus a random 4-digit number.
 */
function generateEstimateId() {
    return 'RLS-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_estimate'])) {
    $name          = trim($_POST['name'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $phone         = trim($_POST['phone'] ?? '');
    $contactMethod = trim($_POST['contact_method'] ?? '');
    $projectType   = trim($_POST['project_type'] ?? '');
    $whatNeeded    = trim($_POST['what_needed'] ?? '');
    $projectStage  = trim($_POST['project_stage'] ?? '');
    $approxSize    = trim($_POST['approx_size'] ?? '');
    $hasFiles      = trim($_POST['has_files'] ?? '');
    $repoLink      = trim($_POST['repo_link'] ?? '');
    $description   = trim($_POST['description'] ?? '');
    $timeline      = trim($_POST['timeline'] ?? '');
    $budget        = trim($_POST['budget'] ?? '');
    $agreed        = isset($_POST['agreement']);

    $isGameType = in_array($projectType, $gameTypes, true);

    if ($name === '') {
        $error = 'Please enter your name.';
    } elseif ($contactMethod === '') {
        $error = 'Please select a preferred contact method.';
    } elseif ($projectType === '') {
        $error = 'Please select a project type.';
    } elseif ($whatNeeded === '') {
        $error = 'Please select what you need.';
    } elseif ($description === '') {
        $error = 'Please describe your request.';
    } elseif (!$agreed) {
        $error = 'Please check the acknowledgement checkbox before submitting.';
    } elseif ($repoLink !== '' && !filter_var($repoLink, FILTER_VALIDATE_URL)) {
        $error = 'The repository or project link does not appear to be a valid URL.';
    } elseif ($email === '' && $phone === '') {
        // Soft validation: warn but allow submit if Discord is the preferred contact for a game project
        if ($isGameType && $contactMethod === 'Discord') {
            $warning = 'Please join our Discord and mention your Estimate ID so we can connect your message to this request.';
        } else {
            $error = 'Please provide at least one way for us to contact you, such as email or phone.';
        }
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'The email address provided does not appear to be valid.';
    }

    if ($error === '') {
        // Determine rough category for confirmation display
        $category = 'Software Project';
        if (in_array($projectType, ['Training / Simulation'], true)) {
            $category = 'Simulation / Training Project';
        } elseif (in_array($whatNeeded, ['Fix something broken', 'Review or rescue a project'], true) || $projectStage === 'It worked before but broke') {
            $category = 'Project Rescue';
        } elseif (in_array($approxSize, ['Very small task'], true) || $whatNeeded === 'Add a feature') {
            $category = 'Quick Fix / Small Task';
        } elseif (in_array($approxSize, ['Ongoing work'], true) || $whatNeeded === 'Ongoing help') {
            $category = 'Ongoing Development Support';
        } elseif (in_array($approxSize, ['Small project'], true) && $projectStage === 'Just an idea') {
            $category = 'Starter Project';
        } elseif (in_array($projectType, ['Business application', 'Infrastructure / Backend'], true)) {
            $category = 'Business / Infrastructure Project';
        } elseif ($isGameType) {
            $category = 'Game / Interactive Project';
        }

        $estimateId = generateEstimateId();

        $request = [
            'estimate_id'    => $estimateId,
            'id'             => bin2hex(random_bytes(8)),
            'name'           => $name,
            'email'          => $email,
            'phone'          => $phone,
            'contact_method' => $contactMethod,
            'project_type'   => $projectType,
            'what_needed'    => $whatNeeded,
            'project_stage'  => $projectStage,
            'approx_size'    => $approxSize,
            'has_files'      => $hasFiles,
            'repo_link'      => $repoLink,
            'description'    => $description,
            'timeline'       => $timeline,
            'budget'         => $budget,
            'category'       => $category,
            'status'         => 'new',
            'notes'          => '',
            'created_at'     => date('c'),
        ];

        if (portalAppendEstimateRequest($request)) {
            $success   = true;
            $submitted = $request;
        } else {
            $error = 'There was an error saving your request. Please try again or contact us directly.';
        }
    }
}

// Determine if previously selected project type is game-related (for JS init)
$selectedType = $_POST['project_type'] ?? '';
$isGameSelected = in_array($selectedType, $gameTypes, true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="assets/images/RL-icon.png">
    <title>Project Requirements &amp; Estimate | Runlevel Systems</title>
    <meta name="description" content="Tell us what you need. Runlevel Systems will review your project requirements and provide a realistic estimate and next step.">
    <link href="assets/css/coreloop.css" rel="stylesheet">
    <style>
        .est-wrap { padding: 40px 0 80px; }
        .est-hero { padding: 48px 0 24px; text-align: center; }
        .est-hero h1 { color: #ffc600; font-size: 2rem; margin-bottom: 10px; }
        .est-hero .subtitle { color: #7a9ac0; font-size: 1.05rem; max-width: 580px; margin: 0 auto; }
        .est-card { background: #0c1729; border: 1px solid rgba(54,243,255,0.18); border-radius: 10px; padding: 30px; margin-bottom: 24px; }
        .est-card h2 { color: #36f3ff; margin-top: 0; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 20px; }
        label { color: #a8bedc; font-size: 0.9rem; margin-bottom: 4px; display: block; }
        .form-field { margin-bottom: 18px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        @media (max-width: 600px) { .form-row { grid-template-columns: 1fr; } }
        .est-input, .est-textarea, .est-select { background: #09111d; color: #eaf3ff; border: 1px solid rgba(54,243,255,0.25); border-radius: 6px; padding: 10px 12px; font-size: 0.95rem; width: 100%; }
        .est-textarea { min-height: 130px; resize: vertical; }
        .est-input:focus, .est-textarea:focus, .est-select:focus { outline: 2px solid #0a84ff; border-color: #0a84ff; }
        .optional-badge { color: #5a7a9e; font-size: 0.8rem; margin-left: 4px; }
        .agreement-row { display: flex; gap: 10px; align-items: flex-start; background: rgba(255,198,0,0.06); border: 1px solid rgba(255,198,0,0.18); border-radius: 6px; padding: 14px; margin-bottom: 20px; }
        .agreement-row input[type=checkbox] { width: auto; margin-top: 3px; flex-shrink: 0; }
        .agreement-row label { color: #c7a800; font-size: 0.875rem; margin: 0; }
        .btn-submit { background: #ffc600; color: #08111f; border: none; border-radius: 6px; padding: 13px 32px; font-weight: 700; font-size: 1.05rem; cursor: pointer; }
        .btn-submit:hover { background: #36f3ff; }
        .alert-error { background: rgba(239,68,68,0.18); border: 1px solid #ef4444; color: #fecaca; border-radius: 6px; padding: 12px 14px; margin-bottom: 18px; }
        .alert-warning { background: rgba(255,198,0,0.1); border: 1px solid rgba(255,198,0,0.35); color: #ffc600; border-radius: 6px; padding: 12px 14px; margin-bottom: 18px; }
        .success-card { background: #0c1729; border: 1px solid rgba(34,197,94,0.3); border-radius: 10px; padding: 36px 30px; max-width: 640px; margin: 0 auto; text-align: center; }
        .success-card h2 { color: #22c55e; margin-top: 0; }
        .success-card p { color: #a8bedc; line-height: 1.7; }
        .category-badge { display: inline-block; background: rgba(54,243,255,0.1); border: 1px solid rgba(54,243,255,0.3); color: #36f3ff; border-radius: 6px; padding: 8px 18px; font-weight: 700; font-size: 1rem; margin: 12px 0 20px; }
        .estimate-id-box { background: rgba(255,198,0,0.08); border: 2px solid rgba(255,198,0,0.4); border-radius: 8px; padding: 16px 20px; margin: 16px 0 24px; }
        .estimate-id-box .eid-label { color: #a8bedc; font-size: 0.85rem; margin-bottom: 6px; }
        .estimate-id-box .eid-value { color: #ffc600; font-size: 1.4rem; font-weight: 700; font-family: monospace; letter-spacing: 0.06em; }
        .terms-note { background: rgba(54,243,255,0.05); border: 1px solid rgba(54,243,255,0.12); border-radius: 8px; padding: 14px 18px; margin-bottom: 24px; color: #7a9ac0; font-size: 0.875rem; line-height: 1.6; }
        .discord-invite-block { display: none; background: rgba(88,101,242,0.12); border: 1px solid rgba(88,101,242,0.35); border-radius: 8px; padding: 16px 20px; margin-top: 16px; }
        .discord-invite-block p { color: #a8bedc; font-size: 0.9rem; margin: 0 0 12px; }
        .btn-discord { display: inline-block; background: #5865f2; color: #fff; border: none; border-radius: 6px; padding: 9px 20px; font-weight: 700; font-size: 0.95rem; text-decoration: none; }
        .btn-discord:hover { background: #7289da; color: #fff; text-decoration: none; }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navigation.php'; ?>

<section class="est-hero">
    <div class="container">
        <h1>Project Requirements &amp; Estimate</h1>
        <p class="subtitle">Tell us what you need. We will help figure out the next step.</p>
    </div>
</section>

<section class="est-wrap">
    <div class="container">

<?php if ($success && $submitted): ?>
        <div class="success-card">
            <h2>✅ Request Received</h2>
            <p>Thank you, <strong><?php echo pe($submitted['name']); ?></strong>. We received your project information.</p>

            <div class="estimate-id-box">
                <div class="eid-label">Your Estimate ID:</div>
                <div class="eid-value"><?php echo pe($submitted['estimate_id']); ?></div>
            </div>
            <p style="color:#7a9ac0; font-size:0.875rem; margin-top: -12px; margin-bottom: 20px;">
                Please save this ID. If you contact us later or submit another request, this helps us find your project quickly.
            </p>

            <p>
                <strong>Likely project category:</strong><br>
                <span class="category-badge"><?php echo pe($submitted['category']); ?></span>
            </p>

            <hr style="border-color: rgba(54,243,255,0.12); margin: 20px 0;">
            <p>
                <strong style="color:#eaf3ff;">Next step:</strong><br>
                Runlevel Systems will review your request and contact you using the contact information provided.
                You may also contact us directly and reference your Estimate ID.
            </p>
            <?php if (!empty($submitted['contact_method']) && $submitted['contact_method'] !== 'Not sure'): ?>
            <p style="color:#7a9ac0; font-size:0.875rem;">
                Preferred contact method: <strong style="color:#eaf3ff;"><?php echo pe($submitted['contact_method']); ?></strong>
            </p>
            <?php endif; ?>
            <?php if (in_array($submitted['project_type'] ?? '', $gameTypes, true)): ?>
            <div style="margin-top: 16px; background: rgba(88,101,242,0.12); border: 1px solid rgba(88,101,242,0.3); border-radius: 8px; padding: 14px 18px;">
                <p style="color:#a8bedc; font-size:0.9rem; margin:0 0 10px;">
                    For game, server, or mod-related projects, you can also join our Discord and mention your Estimate ID.
                </p>
                <?php /* TODO: Replace Discord invite link with official Runlevel Systems Discord invite. */ ?>
                <a href="https://discord.gg/REPLACE_ME" target="_blank" rel="noopener noreferrer" class="btn-discord">Join Discord</a>
            </div>
            <?php endif; ?>
            <p style="margin-top: 20px;">
                <a href="pricing.php" style="color:#36f3ff;">← Back to Pricing</a>
            </p>
        </div>
<?php else: ?>

        <div class="terms-note">
            Submitting this form does not create a contract or payment obligation.
            Runlevel Systems will review your request and contact you before any paid work begins.
            Any final price, timeline, or deliverable will be confirmed in writing before work starts.
        </div>

        <?php if ($error !== ''): ?>
            <div class="alert-error"><?php echo pe($error); ?></div>
        <?php endif; ?>
        <?php if ($warning !== ''): ?>
            <div class="alert-warning">⚠️ <?php echo pe($warning); ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="submit_estimate" value="1">

            <!-- Contact Info -->
            <div class="est-card">
                <h2>📋 Your Contact Information</h2>
                <div class="form-field">
                    <label for="est_name">Name *</label>
                    <input id="est_name" type="text" name="name" class="est-input" required
                           value="<?php echo pe($_POST['name'] ?? ''); ?>" placeholder="Your name or company">
                </div>
                <div class="form-row">
                    <div class="form-field">
                        <label for="est_email">Email <span class="optional-badge">optional</span></label>
                        <input id="est_email" type="email" name="email" class="est-input"
                               value="<?php echo pe($_POST['email'] ?? ''); ?>" placeholder="you@example.com">
                    </div>
                    <div class="form-field">
                        <label for="est_phone">Phone <span class="optional-badge">optional</span></label>
                        <input id="est_phone" type="text" name="phone" class="est-input"
                               value="<?php echo pe($_POST['phone'] ?? ''); ?>" placeholder="Phone or text number">
                    </div>
                </div>
                <div class="form-field">
                    <label for="est_contact">Preferred contact method *</label>
                    <select id="est_contact" name="contact_method" class="est-select" required>
                        <option value="">— Select —</option>
                        <?php foreach (['Email', 'Phone', 'Google Meet', 'Discord', 'Not sure'] as $opt): ?>
                            <option value="<?php echo pe($opt); ?>" <?php echo (($_POST['contact_method'] ?? '') === $opt) ? 'selected' : ''; ?>><?php echo pe($opt); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Project Details -->
            <div class="est-card">
                <h2>🗂️ About Your Project</h2>
                <div class="form-field">
                    <label for="est_type">Project type *</label>
                    <select id="est_type" name="project_type" class="est-select" required>
                        <option value="">— Select —</option>
                        <?php foreach (['Website', 'Web application', 'Mobile app', 'Business application', 'Training / Simulation', 'Game / Interactive project', 'Server / Mod / Script', 'Existing project fix', 'Infrastructure / Backend', 'Not sure'] as $opt): ?>
                            <option value="<?php echo pe($opt); ?>" <?php echo (($_POST['project_type'] ?? '') === $opt) ? 'selected' : ''; ?>><?php echo pe($opt); ?></option>
                        <?php endforeach; ?>
                    </select>

                    <!-- Discord community invite: shown only for game/server/mod project types -->
                    <div class="discord-invite-block" id="discord-invite-block">
                        <p>
                            For game, server, or mod-related projects, you can also join our Discord and mention your Estimate ID.
                        </p>
                        <?php /* TODO: Replace Discord invite link with official Runlevel Systems Discord invite. */ ?>
                        <a href="https://discord.gg/REPLACE_ME" target="_blank" rel="noopener noreferrer" class="btn-discord">Join Discord</a>
                    </div>
                </div>
                <div class="form-field">
                    <label for="est_what">What do you need? *</label>
                    <select id="est_what" name="what_needed" class="est-select" required>
                        <option value="">— Select —</option>
                        <?php foreach (['Build something new', 'Fix something broken', 'Add a feature', 'Improve an existing project', 'Publish or deploy something', 'Review or rescue a project', 'Ongoing help', 'Not sure'] as $opt): ?>
                            <option value="<?php echo pe($opt); ?>" <?php echo (($_POST['what_needed'] ?? '') === $opt) ? 'selected' : ''; ?>><?php echo pe($opt); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-field">
                    <label for="est_stage">Project stage</label>
                    <select id="est_stage" name="project_stage" class="est-select">
                        <option value="">— Select —</option>
                        <?php foreach (['Just an idea', 'I have notes or a design', 'I already have code', 'It worked before but broke', 'It is live and needs changes', 'Not sure'] as $opt): ?>
                            <option value="<?php echo pe($opt); ?>" <?php echo (($_POST['project_stage'] ?? '') === $opt) ? 'selected' : ''; ?>><?php echo pe($opt); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-field">
                        <label for="est_size">Approximate size</label>
                        <select id="est_size" name="approx_size" class="est-select">
                            <option value="">— Select —</option>
                            <?php foreach (['Very small task', 'Small project', 'Medium project', 'Larger project', 'Ongoing work', 'Not sure'] as $opt): ?>
                                <option value="<?php echo pe($opt); ?>" <?php echo (($_POST['approx_size'] ?? '') === $opt) ? 'selected' : ''; ?>><?php echo pe($opt); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-field">
                        <label for="est_timeline">Desired timeline</label>
                        <select id="est_timeline" name="timeline" class="est-select">
                            <option value="">— Select —</option>
                            <?php foreach (['Flexible', 'A few days', '1-2 weeks', 'This month', 'Ongoing', 'Urgent'] as $opt): ?>
                                <option value="<?php echo pe($opt); ?>" <?php echo (($_POST['timeline'] ?? '') === $opt) ? 'selected' : ''; ?>><?php echo pe($opt); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Files / Code -->
            <div class="est-card">
                <h2>📁 Files &amp; Code</h2>
                <div class="form-field">
                    <label for="est_files">Do you have files or code?</label>
                    <select id="est_files" name="has_files" class="est-select">
                        <option value="">— Select —</option>
                        <?php foreach (['No', 'Yes, I can upload files', 'Yes, I have a GitHub / Forgejo / Git link', 'Yes, but I need help sharing it', 'Not sure'] as $opt): ?>
                            <option value="<?php echo pe($opt); ?>" <?php echo (($_POST['has_files'] ?? '') === $opt) ? 'selected' : ''; ?>><?php echo pe($opt); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-field">
                    <label for="est_repo">Repository or project link <span class="optional-badge">optional</span></label>
                    <input id="est_repo" type="url" name="repo_link" class="est-input"
                           value="<?php echo pe($_POST['repo_link'] ?? ''); ?>"
                           placeholder="https://github.com/yourrepo or similar">
                </div>
            </div>

            <!-- Description & Budget -->
            <div class="est-card">
                <h2>💬 Details &amp; Budget</h2>
                <div class="form-field">
                    <label for="est_desc">Describe your request *</label>
                    <textarea id="est_desc" name="description" class="est-textarea" required
                              placeholder="Describe what you need in as much detail as you can. What is the goal? What is broken or missing? What should it do when complete?"><?php echo pe($_POST['description'] ?? ''); ?></textarea>
                </div>
                <div class="form-field">
                    <label for="est_budget">Budget comfort</label>
                    <select id="est_budget" name="budget" class="est-select">
                        <option value="">— Select —</option>
                        <?php foreach (['I need the most affordable option', 'I have a small budget', 'I have a moderate budget', 'This is a business project', 'I need help understanding cost'] as $opt): ?>
                            <option value="<?php echo pe($opt); ?>" <?php echo (($_POST['budget'] ?? '') === $opt) ? 'selected' : ''; ?>><?php echo pe($opt); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Agreement & Submit -->
            <div class="agreement-row">
                <input type="checkbox" name="agreement" id="est_agree" value="1" <?php echo isset($_POST['agreement']) ? 'checked' : ''; ?>>
                <label for="est_agree">
                    I understand this is an estimate request and not a final quote.
                    Runlevel Systems will review my request and contact me before any paid work begins.
                </label>
            </div>

            <button type="submit" class="btn-submit">Send Project Request</button>
        </form>

<?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/jquery-1.12.3.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script>
(function () {
    // Discord invite block: show only for game-related project types
    var gameTypes = <?php echo json_encode($gameTypes); ?>;
    var projectSelect  = document.getElementById('est_type');
    var discordBlock   = document.getElementById('discord-invite-block');

    function updateDiscordBlock() {
        if (!projectSelect || !discordBlock) { return; }
        var selected = projectSelect.value;
        if (gameTypes.indexOf(selected) !== -1) {
            discordBlock.style.display = 'block';
        } else {
            discordBlock.style.display = 'none';
        }
    }

    if (projectSelect) {
        projectSelect.addEventListener('change', updateDiscordBlock);
    }

    // On page load: apply state based on any pre-selected value (e.g. form re-display after validation error)
    updateDiscordBlock();
})();
</script>
</body>
</html>
