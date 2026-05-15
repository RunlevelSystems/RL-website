<?php
session_start();
define('WDS_SYSTEM', true);

require_once __DIR__ . '/../includes/client-portal-data.php';

$current_page = 'portal';
$header_class = 'projects-header inner-header';
$page_subtitle = 'Client Portal';

$message = '';
$error = '';

if (isset($_SESSION['wds_client_id'])) {
    $account = findClientAccountById($_SESSION['wds_client_id']);
    if (!$account || empty($account['active'])) {
        unset($_SESSION['wds_client_id'], $_SESSION['wds_client_user'], $_SESSION['wds_client_name'], $_SESSION['wds_client_folder']);
        $message = 'Your client session has ended. Please sign in again.';
    } else {
        $_SESSION['wds_client_user'] = $account['username'];
        $_SESSION['wds_client_name'] = $account['display_name'] ?? $account['username'];
        $_SESSION['wds_client_folder'] = $account['folder'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['client_login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $account = verifyClientLogin($username, $password);
    if ($account) {
        $_SESSION['wds_client_id'] = $account['id'];
        $_SESSION['wds_client_user'] = $account['username'];
        $_SESSION['wds_client_name'] = $account['display_name'] ?? $account['username'];
        $_SESSION['wds_client_folder'] = $account['folder'];
        header('Location: index.php');
        exit;
    }

    $error = 'Invalid credentials or inactive account.';
}

$is_client_logged_in = isset($_SESSION['wds_client_id'], $_SESSION['wds_client_user'], $_SESSION['wds_client_folder']);
$client_name = $_SESSION['wds_client_name'] ?? '';
$client_folder = clientPortalNormalizeFolder($_SESSION['wds_client_folder'] ?? '');
$folder_files = $is_client_logged_in ? clientPortalListFolderFiles($client_folder) : [];
$folder_index_url = 'files/' . rawurlencode($client_folder) . '/index.html';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../assets/images/RL-icon.png">
    <title>Runlevel Systems | Client Portal</title>
    <link href="../assets/css/coreloop.css" rel="stylesheet">
    <style>
        .client-portal-wrap { padding: 40px 0; }
        .client-card { background: #0c1729; border: 1px solid rgba(54,243,255,0.2); border-radius: 12px; padding: 24px; margin-bottom: 20px; }
        .client-card h3 { color: #ffc600; margin-top: 0; }
        .client-card p, .client-card li, .client-card label { color: #c7d7e8; }
        .client-form input { width: 100%; background: #09111d; color: #eaf3ff; border: 1px solid rgba(54,243,255,0.25); border-radius: 8px; padding: 10px 12px; margin-bottom: 12px; }
        .client-form button,
        .client-action-btn { background: #ffc600; border: 1px solid #ffc600; color: #08111f; border-radius: 8px; padding: 10px 16px; font-weight: 700; text-decoration: none; display: inline-block; }
        .client-form button:hover,
        .client-action-btn:hover { background: #36f3ff; border-color: #36f3ff; color: #08111f; text-decoration: none; }
        .client-alert { border-radius: 8px; padding: 12px 14px; margin-bottom: 15px; }
        .client-alert.error { background: rgba(239,68,68,0.2); border: 1px solid #ef4444; color: #fecaca; }
        .client-alert.info { background: rgba(54,243,255,0.15); border: 1px solid rgba(54,243,255,0.35); color: #c7f9ff; }
        .file-table { width: 100%; border-collapse: collapse; }
        .file-table th, .file-table td { border-bottom: 1px solid rgba(54,243,255,0.15); padding: 10px 8px; text-align: left; color: #c7d7e8; }
        .file-table a { color: #ffc600; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>
<section class="client-portal-wrap">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="title-box">
                    <p>Secure Access</p>
                    <h2 class="title mt0" style="color:#ffc600;">Client Portal</h2>
                    <p style="color:#a8bedc;">Sign in to view your project folder, open your HTML index page, and download shared files.</p>
                </div>
            </div>
        </div>

        <?php if ($error !== ''): ?>
            <div class="client-alert error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($message !== ''): ?>
            <div class="client-alert info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if (!$is_client_logged_in): ?>
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3">
                    <div class="client-card">
                        <h3>Client Login</h3>
                        <form method="post" class="client-form">
                            <input type="hidden" name="client_login" value="1">
                            <label for="username">Username</label>
                            <input id="username" name="username" type="text" required>
                            <label for="password">Password</label>
                            <input id="password" name="password" type="password" required>
                            <button type="submit">Sign In</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="client-card">
                        <h3>Welcome, <?php echo htmlspecialchars($client_name); ?></h3>
                        <p>Your folder: <code><?php echo htmlspecialchars($client_folder); ?></code></p>
                        <p>
                            <a class="client-action-btn" href="<?php echo htmlspecialchars($folder_index_url); ?>" target="_blank" rel="noopener">Open Folder Index Page</a>
                            <a class="client-action-btn" href="logout.php" style="margin-left:8px;">Sign Out</a>
                        </p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="client-card">
                        <h3>Available Files</h3>
                        <?php if (empty($folder_files)): ?>
                            <p>No files are currently listed in your folder yet. Please check back soon.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="file-table">
                                    <thead>
                                        <tr>
                                            <th>File</th>
                                            <th>Size</th>
                                            <th>Last Modified</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($folder_files as $file): ?>
                                            <tr>
                                                <td><a href="<?php echo htmlspecialchars($file['url']); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($file['name']); ?></a></td>
                                                <td><?php echo number_format(((int)$file['size']) / 1024, 2); ?> KB</td>
                                                <td><?php echo $file['modified'] ? date('Y-m-d H:i', (int)$file['modified']) : '—'; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="../assets/js/jquery-1.12.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>
