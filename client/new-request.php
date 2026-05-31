<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';

portalRequireClient();
$client = portalGetClientUser();

$error   = '';
$success = false;

$requestTypes = [
    'Quick Fix',
    'Website',
    'Mobile App',
    'Game / Interactive',
    'Server / Mod / Script',
    'Training / Simulation',
    'Business Application',
    'Commercial / Long-Term',
    'Other',
];

$budgetRanges = [
    'Under $40',
    '$40 – $100',
    '$100 – $250',
    '$250 – $500',
    '$500 – $1,000',
    '$1,000+',
    'Not sure yet',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_request'])) {
    $title        = trim($_POST['project_title'] ?? '');
    $type         = trim($_POST['request_type'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $budget       = trim($_POST['budget_range'] ?? '');
    $timeline     = trim($_POST['timeline'] ?? '');
    $repoLink     = trim($_POST['repo_link'] ?? '');
    $contactMethod= trim($_POST['contact_method'] ?? '');
    $agreed       = isset($_POST['agreement']);

    if (!$agreed) {
        $error = 'Please check the agreement checkbox before submitting.';
    } elseif ($title === '') {
        $error = 'Please enter a project title.';
    } elseif ($type === '' || !in_array($type, $requestTypes, true)) {
        $error = 'Please select a request type.';
    } elseif ($description === '') {
        $error = 'Please enter a description of your project.';
    } elseif ($budget === '') {
        $error = 'Please select a budget range.';
    } elseif ($contactMethod === '') {
        $error = 'Please tell us how to contact you.';
    } else {
        // Validate optional URL
        if ($repoLink !== '' && !filter_var($repoLink, FILTER_VALIDATE_URL)) {
            $error = 'The file or repository link does not appear to be a valid URL.';
        } else {
            $request = [
                'id'              => bin2hex(random_bytes(8)),
                'client_username' => $client['username'],
                'project_title'   => $title,
                'request_type'    => $type,
                'description'     => $description,
                'budget_range'    => $budget,
                'timeline'        => $timeline,
                'repo_link'       => $repoLink,
                'contact_method'  => $contactMethod,
                'status'          => 'new',
                'created_at'      => date('c'),
            ];
            if (portalAppendRequest($request)) {
                $success = true;
            } else {
                $error = 'There was an error saving your request. Please try again or contact us directly.';
            }
        }
    }
}

$current_page = 'client-portal';
$header_class = 'inner-header';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../assets/images/RL-icon.png">
    <title>Submit Request | Client Portal | Runlevel Systems</title>
    <link href="../assets/css/coreloop.css" rel="stylesheet">
    <style>
        .portal-wrap { padding: 40px 0 80px; }
        .portal-back { color: #36f3ff; font-size: 0.9rem; margin-bottom: 18px; display: inline-block; }
        .portal-back:hover { color: #ffc600; }
        .portal-card { background: #0c1729; border: 1px solid rgba(54,243,255,0.18); border-radius: 10px; padding: 30px; margin-bottom: 24px; }
        .portal-card h2 { color: #ffc600; margin-top: 0; }
        label { color: #a8bedc; font-size: 0.9rem; margin-bottom: 4px; display: block; }
        .form-field { margin-bottom: 20px; }
        .portal-input, .portal-textarea, .portal-select { background: #09111d; color: #eaf3ff; border: 1px solid rgba(54,243,255,0.25); border-radius: 6px; padding: 10px 12px; font-size: 0.95rem; width: 100%; }
        .portal-textarea { min-height: 130px; resize: vertical; }
        .portal-input:focus, .portal-textarea:focus, .portal-select:focus { outline: 2px solid #0a84ff; border-color: #0a84ff; }
        .portal-btn { background: #ffc600; color: #08111f; border: none; border-radius: 6px; padding: 12px 28px; font-weight: 700; font-size: 1rem; cursor: pointer; }
        .portal-btn:hover { background: #36f3ff; }
        .portal-alert-error { background: rgba(239,68,68,0.18); border: 1px solid #ef4444; color: #fecaca; border-radius: 6px; padding: 12px 14px; margin-bottom: 18px; }
        .portal-alert-success { background: rgba(34,197,94,0.15); border: 1px solid #22c55e; color: #86efac; border-radius: 6px; padding: 16px; margin-bottom: 18px; }
        .pricing-link { background: rgba(54,243,255,0.06); border: 1px solid rgba(54,243,255,0.15); border-radius: 6px; padding: 10px 14px; font-size:0.875rem; color: #7a9ac0; margin-bottom: 20px; }
        .agreement-row { display: flex; gap: 10px; align-items: flex-start; background: rgba(255,198,0,0.06); border: 1px solid rgba(255,198,0,0.2); border-radius: 6px; padding: 14px; }
        .agreement-row input[type=checkbox] { width: auto; margin-top: 3px; flex-shrink: 0; }
        .agreement-row label { color: #c7a800; font-size: 0.875rem; margin: 0; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>

<section class="portal-wrap">
    <div class="container">
        <a href="/client/dashboard.php" class="portal-back">← Back to Dashboard</a>

        <?php if ($success): ?>
            <div class="portal-alert-success">
                <strong>✅ Request submitted!</strong><br>
                Thank you. We have received your request and will review it. We'll follow up using the contact method you provided.
                All requests are reviewed before a quote or proposal is prepared.
                <br><br>
                <a href="/client/requests.php" style="color:#36f3ff;">View my requests</a> ·
                <a href="/client/dashboard.php" style="color:#36f3ff;">Back to dashboard</a>
            </div>
        <?php else: ?>

        <div class="portal-card">
            <h2>➕ Submit a New Request</h2>
            <p style="color:#7a9ac0; margin-bottom: 20px;">
                Tell us about your project. This is a request for review — not a final quote.
                We will review your request and follow up with an estimate or proposal.
            </p>

            <div class="pricing-link">
                💡 Not sure what to budget? Check our <a href="/pricing.php" style="color:#36f3ff;">typical price ranges</a>
                or read about <a href="/payments.php" style="color:#36f3ff;">how payments work</a>.
            </div>

            <?php if ($error !== ''): ?>
                <div class="portal-alert-error"><?php echo pe($error); ?></div>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="submit_request" value="1">

                <div class="form-field">
                    <label for="req_title">Project Title *</label>
                    <input id="req_title" type="text" name="project_title" class="portal-input" required
                           value="<?php echo pe($_POST['project_title'] ?? ''); ?>"
                           placeholder="e.g. Fix checkout bug, Add login page, Unity animation issue">
                </div>

                <div class="form-field">
                    <label for="req_type">Request Type *</label>
                    <select id="req_type" name="request_type" class="portal-select" required>
                        <option value="">— Select type —</option>
                        <?php foreach ($requestTypes as $t): ?>
                            <option value="<?php echo pe($t); ?>" <?php echo (($_POST['request_type'] ?? '') === $t) ? 'selected' : ''; ?>>
                                <?php echo pe($t); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-field">
                    <label for="req_desc">Description *</label>
                    <textarea id="req_desc" name="description" class="portal-textarea" required
                              placeholder="Describe what you need in as much detail as you can. Include what's currently broken, what you want added, or what your goal is."><?php echo pe($_POST['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-field">
                    <label for="req_budget">Budget Range *</label>
                    <select id="req_budget" name="budget_range" class="portal-select" required>
                        <option value="">— Select range —</option>
                        <?php foreach ($budgetRanges as $b): ?>
                            <option value="<?php echo pe($b); ?>" <?php echo (($_POST['budget_range'] ?? '') === $b) ? 'selected' : ''; ?>>
                                <?php echo pe($b); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-field">
                    <label for="req_timeline">Desired Timeline</label>
                    <input id="req_timeline" type="text" name="timeline" class="portal-input"
                           value="<?php echo pe($_POST['timeline'] ?? ''); ?>"
                           placeholder="e.g. ASAP, within 2 weeks, no rush, flexible">
                </div>

                <div class="form-field">
                    <label for="req_link">File Link / Repository Link (optional)</label>
                    <input id="req_link" type="url" name="repo_link" class="portal-input"
                           value="<?php echo pe($_POST['repo_link'] ?? ''); ?>"
                           placeholder="https://github.com/yourrepo or a file share link">
                </div>

                <div class="form-field">
                    <label for="req_contact">Preferred Contact Method *</label>
                    <input id="req_contact" type="text" name="contact_method" class="portal-input" required
                           value="<?php echo pe($_POST['contact_method'] ?? ''); ?>"
                           placeholder="e.g. email@example.com, Discord username, etc.">
                </div>

                <div class="form-field">
                    <div class="agreement-row">
                        <input type="checkbox" name="agreement" id="req_agree" value="1" <?php echo isset($_POST['agreement']) ? 'checked' : ''; ?>>
                        <label for="req_agree">
                            I understand this is a request for review and not a final quote.
                            I have read the <a href="/runlevel-terms.php" target="_blank" rel="noopener noreferrer" style="color:#36f3ff;">Terms of Service</a>
                            and understand that Runlevel Systems will review this request and provide an estimate or proposal before any work begins.
                        </label>
                    </div>
                </div>

                <button type="submit" class="portal-btn">Submit Request</button>
            </form>
        </div>

        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="../assets/js/jquery-1.12.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>
