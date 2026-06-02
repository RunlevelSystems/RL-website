<?php
define('WDS_SYSTEM', true);
require_once __DIR__ . '/includes/portal-helpers.php';
require_once __DIR__ . '/includes/email.php';

$current_page = 'start-project';
$header_class = 'inner-header';

$requestTypes = [
    'New Website',
    'Existing Software Issue',
    'Mobile Application',
    'Multiplayer Game',
    'Infrastructure Project',
    'Training Simulator',
    'Custom Development',
    'Other',
];
$budgetRanges = [
    'Not sure yet',
    'Under $500',
    '$500 - $2,500',
    '$2,500 - $10,000',
    '$10,000+',
    'Custom quote expected',
];
$timelineOptions = ['ASAP', '1-2 weeks', '2-4 weeks', '1-3 months', '3+ months', 'Flexible', 'Not sure'];
$assetOptions = [
    'Existing Website',
    'Existing Application',
    'Existing Repository',
    'Existing Files',
    'Existing Server',
    'Starting From Scratch',
];

$prefillType = trim((string)($_GET['type'] ?? ''));

$defaults = [
    'name' => '',
    'email' => '',
    'phone' => '',
    'company' => '',
    'request_type' => in_array($prefillType, $requestTypes, true) ? $prefillType : '',
    'request_summary' => '',
    'problem_to_solve' => '',
    'existing_work' => '',
    'repo_link' => '',
    'budget_range' => 'Not sure yet',
    'desired_timeline' => '',
];

$form = $defaults;
foreach ($form as $key => $value) {
    if (isset($_POST[$key])) {
        $form[$key] = trim((string)$_POST[$key]);
    }
}

$error = '';
$success = false;
$submitted = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_request'])) {
    if ($form['name'] === '') {
        $error = 'Please enter your name.';
    } elseif ($form['email'] === '' || !filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (!in_array($form['request_type'], $requestTypes, true)) {
        $error = 'Please select a project type.';
    } elseif ($form['request_summary'] === '') {
        $error = 'Please add a request summary.';
    } elseif ($form['problem_to_solve'] === '') {
        $error = 'Please describe the problem to solve.';
    } elseif (!in_array($form['existing_work'], $assetOptions, true)) {
        $error = 'Please select whether this project already exists.';
    } elseif ($form['repo_link'] !== '' && !filter_var($form['repo_link'], FILTER_VALIDATE_URL)) {
        $error = 'Please enter a valid repository or website link.';
    } elseif (!in_array($form['budget_range'], $budgetRanges, true)) {
        $error = 'Please select a budget range.';
    } elseif (!in_array($form['desired_timeline'], $timelineOptions, true)) {
        $error = 'Please select a desired timeline.';
    }

    $savedAttachments = [];
    if ($error === '' && isset($_FILES['attachments']) && is_array($_FILES['attachments']['name'] ?? null)) {
        $fileNames = $_FILES['attachments']['name'];
        $tmpNames = $_FILES['attachments']['tmp_name'] ?? [];
        $fileErrors = $_FILES['attachments']['error'] ?? [];
        $fileSizes = $_FILES['attachments']['size'] ?? [];
        $maxBytes = 10 * 1024 * 1024;
        $allowed = [
            'pdf', 'doc', 'docx', 'txt', 'rtf',
            'png', 'jpg', 'jpeg', 'gif', 'webp',
            'zip', 'rar', '7z',
            'csv', 'xlsx', 'xls',
        ];
        $nonEmptyCount = 0;
        foreach ($fileNames as $idx => $originalName) {
            $originalName = trim((string)$originalName);
            $tmp = (string)($tmpNames[$idx] ?? '');
            $err = (int)($fileErrors[$idx] ?? UPLOAD_ERR_NO_FILE);
            $size = (int)($fileSizes[$idx] ?? 0);
            if ($originalName === '' || $err === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $nonEmptyCount++;
            if ($err !== UPLOAD_ERR_OK || !is_uploaded_file($tmp)) {
                $error = 'One or more attachments failed to upload. Please try again.';
                break;
            }
            if ($size <= 0 || $size > $maxBytes) {
                $error = 'Attachments must be less than 10MB each.';
                break;
            }
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            if ($ext === '' || !in_array($ext, $allowed, true)) {
                $error = 'One or more attachments have an unsupported file type.';
                break;
            }
        }

        if ($error === '' && $nonEmptyCount > 5) {
            $error = 'Please attach up to 5 files per request.';
        }

        if ($error === '') {
            $requestIdForFiles = portalGenerateUniqueRequestId();
            $uploadBase = __DIR__ . '/data/project-attachments';
            $uploadDir = $uploadBase . '/' . preg_replace('/[^A-Za-z0-9_-]/', '', $requestIdForFiles);
            if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
                $error = 'Unable to save attachments right now. Please submit again without files or contact support.';
            } else {
                foreach ($fileNames as $idx => $originalName) {
                    $originalName = trim((string)$originalName);
                    $tmp = (string)($tmpNames[$idx] ?? '');
                    $err = (int)($fileErrors[$idx] ?? UPLOAD_ERR_NO_FILE);
                    if ($originalName === '' || $err === UPLOAD_ERR_NO_FILE) {
                        continue;
                    }
                    $safeOriginal = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($originalName));
                    $safeOriginal = trim((string)$safeOriginal, '._');
                    if ($safeOriginal === '') {
                        $safeOriginal = 'attachment';
                    }
                    $storedName = date('His') . '-' . bin2hex(random_bytes(4)) . '.upload';
                    $storedPath = $uploadDir . '/' . $storedName;
                    if (!move_uploaded_file($tmp, $storedPath)) {
                        $error = 'Unable to save one of the attachments. Please try again.';
                        break;
                    }
                    $savedAttachments[] = [
                        'original_name' => $safeOriginal,
                        'stored_name' => $storedName,
                        'stored_path' => 'data/project-attachments/' . basename($uploadDir) . '/' . $storedName,
                        'uploaded_at' => date('c'),
                    ];
                }
            }

            if ($error === '') {
                $requestId = $requestIdForFiles;
            }
        }
    }

    if ($error === '' && !isset($requestId)) {
        $requestId = portalGenerateUniqueRequestId();
    }

    if ($error === '') {
        $request = [
            'id' => bin2hex(random_bytes(8)),
            'request_id' => $requestId,
            'estimate_id' => $requestId,
            'client_username' => '',
            'name' => $form['name'],
            'email' => $form['email'],
            'phone' => $form['phone'],
            'company' => $form['company'],
            'project_title' => $form['request_type'],
            'project_type' => $form['request_type'],
            'request_type' => $form['request_type'],
            'description' => $form['request_summary'],
            'request_summary' => $form['request_summary'],
            'problem_to_solve' => $form['problem_to_solve'],
            'existing_assets' => $form['existing_work'],
            'existing_work' => $form['existing_work'],
            'repo_link' => $form['repo_link'],
            'budget_range' => $form['budget_range'],
            'budget_comfort' => $form['budget_range'],
            'desired_timeline' => $form['desired_timeline'],
            'timeline' => $form['desired_timeline'],
            'contact_method' => 'Email',
            'preferred_contact_method' => 'Email',
            'review_acknowledged' => true,
            'status' => 'new',
            'estimated_cost_range' => '',
            'estimated_time_range' => '',
            'staff_summary' => '',
            'recommended_next_step' => '',
            'internal_notes' => '',
            'proposal_ids' => [],
            'agreement_ids' => [],
            'attachments' => $savedAttachments,
            'created_at' => date('c'),
        ];

        if (!portalAppendProjectRequest($request)) {
            $error = 'There was an error saving your request. Please try again or contact Runlevel Systems directly.';
        } else {
            $success = true;
            $submitted = $request;
            $host = $_SERVER['HTTP_HOST'] ?? 'runlevel.systems';
            $dashboardUrl = 'https://' . $host . '/dashboard.php';
            $staffUrl = 'https://' . $host . '/staff/estimate-requests.php';
            send_project_request_confirmation_email($form['email'], $form['name'], $requestId, $dashboardUrl);
            send_staff_new_request_email($requestId, $form['name'], $form['email'], $form['request_type'], $staffUrl);
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
            <p class="service-lead">Tell us what you need and we will review your request.</p>
            <p class="service-sublead">Simple request form. No commitment. No charge to submit.</p>
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
                    <p>We will review your request and follow up with recommendations, timeline guidance, and pricing options.</p>
                    <div class="service-actions">
                        <a class="core-action primary" href="/start-project.php">Submit Another Request</a>
                        <a class="core-action secondary" href="/dashboard.php">Open Dashboard</a>
                        <a class="core-action secondary" href="/contact.php">Contact Runlevel Systems</a>
                    </div>
                </div>
            <?php else: ?>
                <?php if ($error !== ''): ?>
                    <div class="error-card"><?php echo pe($error); ?></div>
                <?php endif; ?>

                <div class="request-section-card">
                    <h2>Submit Project Request</h2>
                    <p class="request-helper">Share the essentials and our team will take it from there.</p>
                    <form method="post" class="request-shell" enctype="multipart/form-data">
                        <input type="hidden" name="submit_request" value="1">
                        <div class="request-form-grid">
                            <div class="request-field">
                                <label for="name">Name *</label>
                                <input id="name" type="text" name="name" value="<?php echo pe($form['name']); ?>" required>
                            </div>
                            <div class="request-field">
                                <label for="company">Company</label>
                                <input id="company" type="text" name="company" value="<?php echo pe($form['company']); ?>">
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
                                <label for="request_type">Project Type *</label>
                                <select id="request_type" name="request_type" required>
                                    <option value="">Select a project type</option>
                                    <?php foreach ($requestTypes as $type): ?>
                                        <option value="<?php echo pe($type); ?>" <?php echo $form['request_type'] === $type ? 'selected' : ''; ?>><?php echo pe($type); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="request-helper" style="margin:.45rem 0 0;">What kind of work is this? Example: website, mobile app, business software, training simulator, game/server script, quick fix, project rescue.</p>
                            </div>
                            <div class="request-field">
                                <label for="existing_work">Existing Work *</label>
                                <select id="existing_work" name="existing_work" required>
                                    <option value="">Select one option</option>
                                    <?php foreach ($assetOptions as $option): ?>
                                        <option value="<?php echo pe($option); ?>" <?php echo $form['existing_work'] === $option ? 'selected' : ''; ?>><?php echo pe($option); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="request-helper" style="margin:.45rem 0 0;">Does this project already exist, or are we starting from scratch?</p>
                            </div>
                            <div class="request-field request-field-full">
                                <label for="request_summary">Request Summary *</label>
                                <textarea id="request_summary" name="request_summary" required placeholder="Customer’s plain-English description of what they need."><?php echo pe($form['request_summary']); ?></textarea>
                                <p class="request-helper" style="margin:.45rem 0 0;">Customer’s plain-English description of what they need.</p>
                            </div>
                            <div class="request-field request-field-full">
                                <label for="problem_to_solve">Problem To Solve *</label>
                                <textarea id="problem_to_solve" name="problem_to_solve" required placeholder="What problem should this work fix or improve?"><?php echo pe($form['problem_to_solve']); ?></textarea>
                                <p class="request-helper" style="margin:.45rem 0 0;">What problem should this work fix or improve?</p>
                            </div>
                            <div class="request-field request-field-full">
                                <label for="repo_link">Repository / Website Link</label>
                                <input id="repo_link" type="url" name="repo_link" value="<?php echo pe($form['repo_link']); ?>" placeholder="https://github.com/example/project">
                                <p class="request-helper" style="margin:.45rem 0 0;">Optional link to code, website, repo, screenshots, files, or other information.</p>
                            </div>
                            <div class="request-field">
                                <label for="desired_timeline">Timeline *</label>
                                <select id="desired_timeline" name="desired_timeline" required>
                                    <option value="">Select a timeline</option>
                                    <?php foreach ($timelineOptions as $option): ?>
                                        <option value="<?php echo pe($option); ?>" <?php echo $form['desired_timeline'] === $option ? 'selected' : ''; ?>><?php echo pe($option); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="request-helper" style="margin:.45rem 0 0;">When would you ideally like this completed?</p>
                            </div>
                            <div class="request-field">
                                <label for="budget_range">Budget Comfort *</label>
                                <select id="budget_range" name="budget_range" required>
                                    <option value="">Select a budget range</option>
                                    <?php foreach ($budgetRanges as $range): ?>
                                        <option value="<?php echo pe($range); ?>" <?php echo $form['budget_range'] === $range ? 'selected' : ''; ?>><?php echo pe($range); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="request-helper" style="margin:.45rem 0 0;">This helps us recommend a realistic solution. It is not a final price.</p>
                            </div>
                            <div class="request-field request-field-full">
                                <label for="attachments">Optional Attachments</label>
                                <input id="attachments" type="file" name="attachments[]" multiple>
                                <p class="request-helper" style="margin:.45rem 0 0;">Up to 5 files, 10MB each. Supported: PDF, DOC, images, ZIP, CSV/XLSX.</p>
                            </div>
                        </div>
                        <div class="service-actions">
                            <button type="submit" class="core-action primary">Submit Project Request</button>
                            <a class="core-action secondary" href="/contact.php">Need Help?</a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="service-section alt">
        <div class="container">
            <h2>What Might It Cost?</h2>
            <p class="section-intro">Every project is unique.</p>
            <p class="section-intro">After reviewing your request, we will provide recommendations, estimated timelines, and pricing options.</p>
            <p class="section-intro">There is no obligation and no charge for submitting a project request.</p>
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
