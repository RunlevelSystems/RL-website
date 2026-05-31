<?php
define('WDS_SYSTEM', true);
require_once __DIR__ . '/includes/portal-helpers.php';
require_once __DIR__ . '/includes/email.php';

$current_page = 'start-project';
$header_class = 'inner-header';

$requestTypes = [
    'Quick Fix',
    'Website',
    'Web Application',
    'Mobile App',
    'Business Application',
    'Backend / Server Work',
    'Automation',
    'Training / Simulation',
    'Game / Interactive Project',
    'Game Server / Mod / Script',
    'Project Rescue',
    'Commercial / Long-Term Work',
    'Other',
];
$budgetRanges = [
    'Not sure yet',
    '$20 - $40',
    '$30 - $100',
    '$40 - $100',
    '$20 - $150',
    '$40 - $150',
    '$150 - $500',
    '$500+',
    'Custom quote expected',
];
$timelineOptions = ['ASAP', '1-2 weeks', '2-4 weeks', '1-3 months', 'Flexible', 'Not sure'];
$contactOptions = ['Email', 'Phone', 'Text Message', 'Google Meet', 'Discord'];

$prefillType = trim((string)($_GET['type'] ?? ''));
$prefillTitle = trim((string)($_GET['title'] ?? ''));

$defaults = [
    'name' => '',
    'email' => '',
    'phone' => '',
    'company' => '',
    'project_title' => $prefillTitle,
    'request_type' => in_array($prefillType, $requestTypes, true) ? $prefillType : '',
    'description' => '',
    'problem_to_solve' => '',
    'existing_assets' => '',
    'repo_link' => '',
    'budget_range' => 'Not sure yet',
    'desired_timeline' => '',
    'contact_method' => 'Email',
    'review_acknowledged' => '',
];

$form = $defaults;
foreach ($form as $key => $value) {
    if (isset($_POST[$key])) {
        $form[$key] = trim((string)$_POST[$key]);
    }
}
if (isset($_POST['review_acknowledged'])) {
    $form['review_acknowledged'] = '1';
}

$error = '';
$success = false;
$submitted = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_request'])) {
    if ($form['name'] === '') {
        $error = 'Please enter your name.';
    } elseif ($form['email'] === '' || !filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif ($form['project_title'] === '') {
        $error = 'Please enter a project title.';
    } elseif (!in_array($form['request_type'], $requestTypes, true)) {
        $error = 'Please select a request type.';
    } elseif ($form['description'] === '') {
        $error = 'Please describe what you need.';
    } elseif ($form['problem_to_solve'] === '') {
        $error = 'Please describe the problem you are trying to solve.';
    } elseif ($form['existing_assets'] === '') {
        $error = 'Please tell us whether you already have files, code, a website, a repo, or a project.';
    } elseif ($form['repo_link'] !== '' && !filter_var($form['repo_link'], FILTER_VALIDATE_URL)) {
        $error = 'Please enter a valid repository, website, or file link.';
    } elseif (!in_array($form['budget_range'], $budgetRanges, true)) {
        $error = 'Please select an approximate budget range.';
    } elseif (!in_array($form['desired_timeline'], $timelineOptions, true)) {
        $error = 'Please select a desired timeline.';
    } elseif (!in_array($form['contact_method'], $contactOptions, true)) {
        $error = 'Please select a preferred contact method.';
    } elseif ($form['review_acknowledged'] !== '1') {
        $error = 'Please confirm that this is a request for review and not a final quote.';
    }

    if ($error === '') {
        $requestId = portalGenerateUniqueRequestId();
        $request = [
            'id' => bin2hex(random_bytes(8)),
            'request_id' => $requestId,
            'estimate_id' => $requestId,
            'client_username' => '',
            'name' => $form['name'],
            'email' => $form['email'],
            'phone' => $form['phone'],
            'company' => $form['company'],
            'project_title' => $form['project_title'],
            'project_type' => $form['request_type'],
            'request_type' => $form['request_type'],
            'description' => $form['description'],
            'problem_to_solve' => $form['problem_to_solve'],
            'existing_assets' => $form['existing_assets'],
            'repo_link' => $form['repo_link'],
            'budget_range' => $form['budget_range'],
            'budget_comfort' => $form['budget_range'],
            'desired_timeline' => $form['desired_timeline'],
            'timeline' => $form['desired_timeline'],
            'contact_method' => $form['contact_method'],
            'preferred_contact_method' => $form['contact_method'],
            'review_acknowledged' => true,
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
            $error = 'There was an error saving your request. Please try again or contact Runlevel Systems directly.';
        } else {
            $success = true;
            $submitted = $request;
            $subject = 'Start Project Request Received - ' . $requestId;
            $body = "Hello {$form['name']},\n\n"
                . "Every project starts with a conversation.\n\n"
                . "We received your Start Project request.\n\n"
                . "Request ID:\n{$requestId}\n\n"
                . "This is a request for review, not a final quote. Runlevel Systems will review the request first and then respond with an estimate, proposal, or next step.\n\n"
                . "Runlevel Systems\n"
                . "DESIGN • DEBUG • DEPLOY\n";
            send_email($form['email'], $subject, $body);
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
    <title>Start A Project | Runlevel Systems</title>
    <meta name="description" content="Submit a project request to Runlevel Systems for software development, fixes, websites, mobile apps, systems, and support.">
    <link href="assets/css/coreloop.css" rel="stylesheet">
</head>
<body>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navigation.php'; ?>

<main class="service-site" aria-label="Start a project page">
    <section class="service-hero compact">
        <div class="container">
            <p class="service-kicker">Start Project</p>
            <h1>Start A Project</h1>
            <p class="service-lead">Tell us what you need built, fixed, improved, or launched.</p>
            <p class="service-sublead">Every project starts with a conversation.</p>
            <p class="service-sublead">Whether you need a small fix, a website, a mobile app, a business tool, a training simulator, or a larger commercial system, tell us what you need and Runlevel Systems will review the request.</p>
            <p class="service-sublead">This does not commit you to anything.</p>
            <p class="service-sublead">We review the request first, then provide an estimate, proposal, or next step.</p>
            <p class="hero-tagline">DESIGN • DEBUG • DEPLOY</p>
        </div>
    </section>

    <section class="service-section">
        <div class="container request-shell">
            <?php if ($success && $submitted): ?>
                <div class="request-section-card notice-card">
                    <h2>Project Request Received</h2>
                    <p>Your request has been saved for review.</p>
                    <div class="request-id"><?php echo pe($submitted['request_id']); ?></div>
                    <p>This is a request for review and not a final quote. Runlevel Systems will respond with an estimate, proposal, or next step after review.</p>
                    <div class="service-actions">
                        <a class="core-action primary" href="/start-project.php">Submit Another Request</a>
                        <a class="core-action secondary" href="/payments.php">Read Payment Details</a>
                        <a class="core-action secondary" href="/contact.php">Contact Runlevel Systems</a>
                    </div>
                </div>
            <?php else: ?>
                <?php if ($error !== ''): ?>
                    <div class="error-card"><?php echo pe($error); ?></div>
                <?php endif; ?>

                <div class="request-section-card">
                    <h2>Tell Us About The Work</h2>
                    <p class="request-helper">Use this form to request a quote, submit an idea, ask for help, begin a project, or create a work request for Runlevel Systems to review.</p>
                    <form method="post" class="request-shell">
                        <input type="hidden" name="submit_request" value="1">
                        <div class="request-form-grid">
                            <div class="request-field">
                                <label for="name">Name *</label>
                                <input id="name" type="text" name="name" value="<?php echo pe($form['name']); ?>" required>
                            </div>
                            <div class="request-field">
                                <label for="email">Email *</label>
                                <input id="email" type="email" name="email" value="<?php echo pe($form['email']); ?>" required>
                            </div>
                            <div class="request-field">
                                <label for="phone">Phone</label>
                                <input id="phone" type="text" name="phone" value="<?php echo pe($form['phone']); ?>">
                            </div>
                            <div class="request-field">
                                <label for="company">Company / Organization</label>
                                <input id="company" type="text" name="company" value="<?php echo pe($form['company']); ?>">
                            </div>
                            <div class="request-field request-field-full">
                                <label for="project_title">Project title *</label>
                                <input id="project_title" type="text" name="project_title" value="<?php echo pe($form['project_title']); ?>" required>
                            </div>
                            <div class="request-field">
                                <label for="request_type">Request type *</label>
                                <select id="request_type" name="request_type" required>
                                    <option value="">Select a request type</option>
                                    <?php foreach ($requestTypes as $type): ?>
                                        <option value="<?php echo pe($type); ?>" <?php echo $form['request_type'] === $type ? 'selected' : ''; ?>><?php echo pe($type); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="request-field">
                                <label for="contact_method">Preferred contact method *</label>
                                <select id="contact_method" name="contact_method" required>
                                    <option value="">Select a contact method</option>
                                    <?php foreach ($contactOptions as $option): ?>
                                        <option value="<?php echo pe($option); ?>" <?php echo $form['contact_method'] === $option ? 'selected' : ''; ?>><?php echo pe($option); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="request-field request-field-full">
                                <label for="description">Describe what you need *</label>
                                <textarea id="description" name="description" required><?php echo pe($form['description']); ?></textarea>
                            </div>
                            <div class="request-field request-field-full">
                                <label for="problem_to_solve">What problem are you trying to solve? *</label>
                                <textarea id="problem_to_solve" name="problem_to_solve" required><?php echo pe($form['problem_to_solve']); ?></textarea>
                            </div>
                            <div class="request-field request-field-full">
                                <label for="existing_assets">Do you already have files, code, a website, repo, or project? *</label>
                                <textarea id="existing_assets" name="existing_assets" required><?php echo pe($form['existing_assets']); ?></textarea>
                            </div>
                            <div class="request-field request-field-full">
                                <label for="repo_link">Link to repo / site / files</label>
                                <input id="repo_link" type="url" name="repo_link" value="<?php echo pe($form['repo_link']); ?>" placeholder="https://github.com/example/project">
                            </div>
                            <div class="request-field">
                                <label for="budget_range">Approximate budget range *</label>
                                <select id="budget_range" name="budget_range" required>
                                    <option value="">Select a budget range</option>
                                    <?php foreach ($budgetRanges as $range): ?>
                                        <option value="<?php echo pe($range); ?>" <?php echo $form['budget_range'] === $range ? 'selected' : ''; ?>><?php echo pe($range); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="request-field">
                                <label for="desired_timeline">Desired timeline *</label>
                                <select id="desired_timeline" name="desired_timeline" required>
                                    <option value="">Select a timeline</option>
                                    <?php foreach ($timelineOptions as $option): ?>
                                        <option value="<?php echo pe($option); ?>" <?php echo $form['desired_timeline'] === $option ? 'selected' : ''; ?>><?php echo pe($option); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="request-field request-field-full">
                                <label class="request-check" for="review_acknowledged">
                                    <input id="review_acknowledged" type="checkbox" name="review_acknowledged" value="1" <?php echo $form['review_acknowledged'] === '1' ? 'checked' : ''; ?>>
                                    <span>I understand this is a request for review and not a final quote.</span>
                                </label>
                            </div>
                        </div>
                        <div class="service-actions">
                            <button type="submit" class="core-action primary">Submit Project Request</button>
                            <a class="core-action secondary" href="/payments.php">Read Payment Details</a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="service-section alt">
        <div class="container">
            <h2>What Might It Cost?</h2>
            <p class="section-intro">Every project is different, but we want customers to have a realistic idea before submitting a request.</p>
            <div class="cost-grid">
                <article class="service-card-item"><h3>Quick Fixes</h3><div class="cost-card-price">$20 - $40</div><p>Small bug fixes, config issues, small code changes, animation problems, or minor website edits.</p></article>
                <article class="service-card-item"><h3>Small Development Tasks</h3><div class="cost-card-price">$30 - $100</div><p>Small features, scripts, game logic fixes, mod adjustments, automation tasks, or project cleanup.</p></article>
                <article class="service-card-item"><h3>Starter Websites</h3><div class="cost-card-price">$40 - $100</div><p>Simple landing pages, basic business pages, project pages, or contact pages.</p></article>
                <article class="service-card-item"><h3>Website Improvements</h3><div class="cost-card-price">$20 - $150</div><p>Layout fixes, content updates, mobile improvements, new pages, or simple forms.</p></article>
                <article class="service-card-item"><h3>Mobile / Build Help</h3><div class="cost-card-price">$40 - $150</div><p>Android builds, iOS preparation, store publishing help, or build troubleshooting.</p></article>
                <article class="service-card-item"><h3>Project Rescue</h3><div class="cost-card-price">Quoted After Review</div><p>Broken projects, inherited code, build failures, unknown bugs, or messy systems.</p></article>
                <article class="service-card-item"><h3>Commercial Projects</h3><div class="cost-card-price">Custom Quote</div><p>Business applications, training simulations, backend systems, infrastructure platforms, mobile apps, or ongoing development work.</p></article>
            </div>
            <p class="section-intro" style="margin-top: 1rem;">These are typical starting ranges. Final pricing depends on the project condition, requested work, testing needs, and timeline.</p>
        </div>
    </section>

    <section class="service-section">
        <div class="container cta-block">
            <h2>How Payment Works</h2>
            <p>You submit a request first.</p>
            <p>We review it and provide an estimate or proposal.</p>
            <p>For small jobs, payment may be required before work begins.</p>
            <p>For larger jobs, we may use a deposit, milestone payment, or written project agreement.</p>
            <p>Payments can be handled through PayPal invoice or payment link.</p>
            <p>Payment allows work to begin, but final acceptance happens after the agreed work is delivered and reviewed according to the proposal or project agreement.</p>
            <div class="service-actions">
                <a class="core-action primary" href="/payments.php">Read Payment Details</a>
                <a class="core-action secondary" href="/contact.php">Contact Runlevel Systems</a>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/jquery-1.12.3.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/jquery.magnific-popup.min.js"></script>
<script src="assets/js/owl.carousel.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>
