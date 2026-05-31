<?php
define('WDS_SYSTEM', true);
require_once __DIR__ . '/includes/portal-helpers.php';

$error   = '';
$success = false;

$projectTypes = [
    'Business Application',
    'Training / Simulation Software',
    'Mobile Application',
    'Infrastructure / Platform',
    'Backend System',
    'Long-Term Development Partnership',
    'Internal Business Tools',
    'Ongoing Support',
    'Other',
];

$budgetRanges = [
    'Under $500',
    '$500 – $1,000',
    '$1,000 – $5,000',
    '$5,000 – $10,000',
    '$10,000+',
    'Not sure yet — want a quote',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_commercial'])) {
    $bizName       = trim($_POST['biz_name'] ?? '');
    $contactName   = trim($_POST['contact_name'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $phone         = trim($_POST['phone'] ?? '');
    $projectType   = trim($_POST['project_type'] ?? '');
    $description   = trim($_POST['description'] ?? '');
    $outcome       = trim($_POST['desired_outcome'] ?? '');
    $timeline      = trim($_POST['timeline'] ?? '');
    $budget        = trim($_POST['budget_range'] ?? '');
    $projectLink   = trim($_POST['project_link'] ?? '');
    $contactMethod = trim($_POST['contact_method'] ?? '');

    if ($contactName === '') {
        $error = 'Please enter a contact name.';
    } elseif ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif ($projectType === '') {
        $error = 'Please select a project type.';
    } elseif ($description === '') {
        $error = 'Please describe your project.';
    } elseif ($budget === '') {
        $error = 'Please select a budget range.';
    } elseif ($projectLink !== '' && !filter_var($projectLink, FILTER_VALIDATE_URL)) {
        $error = 'The website or project link does not appear to be a valid URL.';
    } else {
        $record = [
            'id'             => bin2hex(random_bytes(8)),
            'biz_name'       => $bizName,
            'contact_name'   => $contactName,
            'email'          => $email,
            'phone'          => $phone,
            'project_type'   => $projectType,
            'description'    => $description,
            'desired_outcome'=> $outcome,
            'timeline'       => $timeline,
            'budget_range'   => $budget,
            'project_link'   => $projectLink,
            'contact_method' => $contactMethod,
            'status'         => 'new',
            'created_at'     => date('c'),
        ];
        if (portalAppendCommercialRequest($record)) {
            $success = true;
        } else {
            $error = 'There was an error submitting your request. Please try again or email us directly.';
        }
    }
}

$current_page = 'commercial';
$header_class = 'inner-header';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="assets/images/RL-icon.png">
    <title>Commercial Project Requests | Runlevel Systems</title>
    <meta name="description" content="Submit a commercial project request to Runlevel Systems — for business applications, mobile apps, training simulation, infrastructure, and long-term development.">
    <link href="assets/css/coreloop.css" rel="stylesheet">
    <style>
        .pub-wrap { padding: 60px 0 80px; }
        .pub-hero { margin-bottom: 36px; }
        .pub-hero h1 { color: #ffc600; font-size: 2rem; margin-bottom: 10px; }
        .pub-hero p { color: #7a9ac0; font-size: 1.05rem; max-width: 680px; }
        .pub-card { background: #0c1729; border: 1px solid rgba(54,243,255,0.18); border-radius: 10px; padding: 28px 30px; margin-bottom: 24px; }
        .pub-card h3 { color: #36f3ff; margin-top: 0; font-size: 1.15rem; }
        .pub-card p, .pub-card li { color: #a8bedc; line-height: 1.7; }
        .pub-card ul { padding-left: 1.25rem; }
        .pub-card ul li { margin-bottom: 6px; }
        .step-list { counter-reset: steps; list-style: none; padding: 0; }
        .step-list li { counter-increment: steps; display: flex; gap: 14px; margin-bottom: 14px; align-items: flex-start; }
        .step-list li::before { content: counter(steps); background: #0a84ff; color: #fff; font-weight: 700; min-width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; flex-shrink: 0; margin-top: 2px; }
        .step-list li span { color: #a8bedc; line-height: 1.6; }
        label { color: #a8bedc; font-size: 0.9rem; margin-bottom: 4px; display: block; }
        .form-field { margin-bottom: 20px; }
        .portal-input, .portal-textarea, .portal-select { background: #09111d; color: #eaf3ff; border: 1px solid rgba(54,243,255,0.25); border-radius: 6px; padding: 10px 12px; font-size: 0.95rem; width: 100%; }
        .portal-textarea { min-height: 120px; resize: vertical; }
        .portal-input:focus, .portal-textarea:focus, .portal-select:focus { outline: 2px solid #0a84ff; border-color: #0a84ff; }
        .portal-btn { background: #ffc600; color: #08111f; border: none; border-radius: 6px; padding: 12px 28px; font-weight: 700; font-size: 1rem; cursor: pointer; }
        .portal-btn:hover { background: #36f3ff; }
        .portal-alert-error { background: rgba(239,68,68,0.18); border: 1px solid #ef4444; color: #fecaca; border-radius: 6px; padding: 12px 14px; margin-bottom: 18px; }
        .portal-alert-success { background: rgba(34,197,94,0.15); border: 1px solid #22c55e; color: #86efac; border-radius: 6px; padding: 20px; }
        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        @media(max-width: 600px) { .two-col { grid-template-columns: 1fr; } }
        .optional-label { color: #5a7a9e; font-size: 0.78rem; margin-left: 4px; }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navigation.php'; ?>

<section class="pub-wrap">
    <div class="container">

        <div class="pub-hero">
            <h1>🏢 Commercial Project Requests</h1>
            <p>
                Some projects are bigger than a quick fix or small task. If you're planning a business application,
                training simulation, mobile app, infrastructure platform, or long-term work — this is the right place to start.
            </p>
        </div>

        <div class="pub-card">
            <h3>What Counts as Commercial Work?</h3>
            <p>Commercial requests may include:</p>
            <ul>
                <li>Business applications and internal tools</li>
                <li>Training simulation software</li>
                <li>Mobile applications</li>
                <li>Infrastructure platforms and backend systems</li>
                <li>Ongoing support or managed development</li>
                <li>Long-term development partnerships</li>
            </ul>
        </div>

        <div class="pub-card">
            <h3>How Commercial Projects Work</h3>
            <ol class="step-list">
                <li><span>Submit a commercial request describing your project and goals.</span></li>
                <li><span>We review your goals and any existing materials.</span></li>
                <li><span>We may schedule a planning discussion to understand the scope better.</span></li>
                <li><span>We create a written proposal with scope, timeline, and payment terms.</span></li>
                <li><span>A deposit or milestone payment may be required before work begins.</span></li>
                <li><span>Work begins after the proposal is approved and required payment is confirmed.</span></li>
            </ol>
        </div>

        <?php if ($success): ?>
            <div class="portal-alert-success">
                <strong>✅ Commercial request received!</strong><br><br>
                Thank you for reaching out. We've received your commercial project request and will review it carefully.
                We'll follow up with you using the contact information you provided.
                For larger projects, we may schedule a planning discussion before preparing a proposal.
                <br><br>
                <a href="/" style="color:#22c55e;">← Return to Homepage</a>
            </div>
        <?php else: ?>

        <div class="pub-card">
            <h3>Submit a Commercial Request</h3>
            <p style="color:#7a9ac0; margin-bottom: 20px;">
                This form is for larger or commercial projects. For small tasks and quick fixes,
                use the <a href="/client/new-request.php" style="color:#36f3ff;">standard request form</a>.
                Submitting is free and doesn't commit you to anything.
            </p>

            <?php if ($error !== ''): ?>
                <div class="portal-alert-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="submit_commercial" value="1">

                <div class="two-col">
                    <div class="form-field">
                        <label for="com_biz">Business / Organization Name <span class="optional-label">(optional)</span></label>
                        <input id="com_biz" type="text" name="biz_name" class="portal-input"
                               value="<?php echo htmlspecialchars($_POST['biz_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                               placeholder="Your company or project name">
                    </div>
                    <div class="form-field">
                        <label for="com_contact">Contact Name *</label>
                        <input id="com_contact" type="text" name="contact_name" class="portal-input" required
                               value="<?php echo htmlspecialchars($_POST['contact_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                               placeholder="Your name">
                    </div>
                </div>

                <div class="two-col">
                    <div class="form-field">
                        <label for="com_email">Email Address *</label>
                        <input id="com_email" type="email" name="email" class="portal-input" required
                               value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                               placeholder="you@example.com">
                    </div>
                    <div class="form-field">
                        <label for="com_phone">Phone <span class="optional-label">(optional)</span></label>
                        <input id="com_phone" type="tel" name="phone" class="portal-input"
                               value="<?php echo htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                               placeholder="+1 (555) 000-0000">
                    </div>
                </div>

                <div class="form-field">
                    <label for="com_type">Project Type *</label>
                    <select id="com_type" name="project_type" class="portal-select" required>
                        <option value="">— Select project type —</option>
                        <?php foreach ($projectTypes as $t): ?>
                            <option value="<?php echo htmlspecialchars($t, ENT_QUOTES, 'UTF-8'); ?>"
                                <?php echo (($_POST['project_type'] ?? '') === $t) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($t, ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-field">
                    <label for="com_desc">Project Description *</label>
                    <textarea id="com_desc" name="description" class="portal-textarea" required
                              placeholder="Describe your project in as much detail as you can. What is it? Who will use it? What problem does it solve?"><?php echo htmlspecialchars($_POST['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>

                <div class="form-field">
                    <label for="com_outcome">Desired Outcome</label>
                    <textarea id="com_outcome" name="desired_outcome" class="portal-textarea" style="min-height:80px;"
                              placeholder="What does success look like for you? What do you want the final result to be?"><?php echo htmlspecialchars($_POST['desired_outcome'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>

                <div class="two-col">
                    <div class="form-field">
                        <label for="com_timeline">Timeline</label>
                        <input id="com_timeline" type="text" name="timeline" class="portal-input"
                               value="<?php echo htmlspecialchars($_POST['timeline'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                               placeholder="e.g. 3 months, Q4 launch, flexible">
                    </div>
                    <div class="form-field">
                        <label for="com_budget">Estimated Budget Range *</label>
                        <select id="com_budget" name="budget_range" class="portal-select" required>
                            <option value="">— Select range —</option>
                            <?php foreach ($budgetRanges as $b): ?>
                                <option value="<?php echo htmlspecialchars($b, ENT_QUOTES, 'UTF-8'); ?>"
                                    <?php echo (($_POST['budget_range'] ?? '') === $b) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($b, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-field">
                    <label for="com_link">Existing Website or Project Link <span class="optional-label">(optional)</span></label>
                    <input id="com_link" type="url" name="project_link" class="portal-input"
                           value="<?php echo htmlspecialchars($_POST['project_link'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                           placeholder="https://your-website.com or GitHub link">
                </div>

                <div class="form-field">
                    <label for="com_contact_method">Best Contact Method</label>
                    <input id="com_contact_method" type="text" name="contact_method" class="portal-input"
                           value="<?php echo htmlspecialchars($_POST['contact_method'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                           placeholder="Email, phone, Discord, etc.">
                </div>

                <p style="font-size:0.85rem; color:#5a7a9e; margin-bottom:16px;">
                    By submitting this form you agree to the
                    <a href="/runlevel-terms.php" target="_blank" rel="noopener noreferrer" style="color:#36f3ff;">Terms of Service</a>.
                    This is a request for review — not a binding commitment. We'll reach out to discuss your project.
                </p>

                <button type="submit" class="portal-btn">Submit Commercial Request</button>
            </form>
        </div>

        <?php endif; ?>

    </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/jquery-1.12.3.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
