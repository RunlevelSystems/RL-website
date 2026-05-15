<?php
session_start();
define('WDS_SYSTEM', true);

require_once __DIR__ . '/../includes/db-config.php';
require_once __DIR__ . '/../includes/client-portal-data.php';
requireAdminLogin();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $ok = createClientAccount(
            $_POST['username'] ?? '',
            $_POST['password'] ?? '',
            $_POST['display_name'] ?? '',
            $_POST['folder'] ?? '',
            $error
        );
        if ($ok) {
            $message = 'Client account created successfully.';
        }
    } elseif ($action === 'update') {
        $ok = updateClientAccount(
            $_POST['id'] ?? '',
            $_POST['display_name'] ?? '',
            $_POST['folder'] ?? '',
            $_POST['password'] ?? '',
            isset($_POST['active']) && $_POST['active'] === '1',
            $error
        );
        if ($ok) {
            $message = 'Client account updated successfully.';
        }
    } elseif ($action === 'delete') {
        $ok = deleteClientAccount($_POST['id'] ?? '', $error);
        if ($ok) {
            $message = 'Client account deleted.';
        }
    } elseif ($action === 'upload_file') {
        $uploadClientId = trim((string)($_POST['upload_client_id'] ?? ''));
        $uploadClient = findClientAccountById($uploadClientId);
        if (!$uploadClient) {
            $error = 'Selected client account was not found.';
        } else {
            $ok = clientPortalUploadFile(
                $uploadClient['folder'] ?? '',
                $_FILES['upload_file'] ?? null,
                $error
            );
            if ($ok) {
                $message = 'File uploaded to client folder: ' . ($uploadClient['folder'] ?? '');
            }
        }
    } elseif ($action === 'upload_files') {
        $uploadClientId = trim((string)($_POST['upload_client_id'] ?? ''));
        $uploadClient = findClientAccountById($uploadClientId);
        if (!$uploadClient) {
            $error = 'Selected client account was not found.';
        } else {
            $uploadedFiles = [];
            $failedFiles = [];
            $ok = clientPortalUploadFiles(
                $uploadClient['folder'] ?? '',
                $_FILES['upload_files'] ?? null,
                $uploadedFiles,
                $failedFiles,
                $error
            );
            if ($ok) {
                $message = 'Uploaded ' . count($uploadedFiles) . ' file(s) to folder: ' . ($uploadClient['folder'] ?? '') . '.';
                if (!empty($failedFiles)) {
                    $failedParts = [];
                    foreach ($failedFiles as $failed) {
                        $failedParts[] = ($failed['name'] ?? 'unknown') . ' (' . ($failed['reason'] ?? 'error') . ')';
                    }
                    $error = 'Some files failed: ' . implode('; ', $failedParts);
                }
            }
        }
    } elseif ($action === 'delete_file') {
        $uploadClientId = trim((string)($_POST['upload_client_id'] ?? ''));
        $uploadClient = findClientAccountById($uploadClientId);
        if (!$uploadClient) {
            $error = 'Selected client account was not found.';
        } else {
            $ok = clientPortalDeleteFile(
                $uploadClient['folder'] ?? '',
                $_POST['file_name'] ?? '',
                $error
            );
            if ($ok) {
                $message = 'File deleted from client folder.';
            }
        }
    }
}

$accounts = loadClientAccounts();
$editId = trim((string)($_GET['edit'] ?? ''));
$editing = $editId !== '' ? findClientAccountById($editId) : null;

$selectedClientId = trim((string)($_GET['files_client'] ?? ($_POST['upload_client_id'] ?? '')));
$selectedClient = $selectedClientId !== '' ? findClientAccountById($selectedClientId) : null;
$selectedClientFiles = [];
if ($selectedClient && !empty($selectedClient['folder'])) {
    $selectedClientFiles = clientPortalListFolderFiles($selectedClient['folder']);
}

$current_page = 'staff-client-portal';
$header_class = 'projects-header inner-header';
$page_subtitle = 'Client Portal Admin';

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="/assets/images/RL-icon.png">
    <title>Runlevel Systems | Client Portal Admin</title>
    <link href="../assets/css/coreloop.css" rel="stylesheet">
    <style>
        body { background-color: #071228; }
        .staff-card { background:#111827; border-radius:10px; padding:20px; margin-bottom:20px; border:1px solid #374151; }
        .staff-card h3 { color:#ffd166; margin-top:0; }
        .staff-label { color:#c7d7e8; display:block; margin:10px 0 6px; }
        .staff-input { width:100%; padding:9px 10px; border-radius:6px; border:1px solid #4b5563; background:#0f172a; color:#eaf3ff; }
        .staff-btn { background: #ffc600; border:1px solid #ffc600; color:#08111f; border-radius:6px; padding:9px 14px; font-weight:700; }
        .staff-btn:hover { background:#36f3ff; border-color:#36f3ff; }
        .staff-btn-secondary { background:#0a84ff; border:1px solid #0a84ff; color:#eaf3ff; border-radius:6px; padding:9px 14px; font-weight:700; text-decoration:none; display:inline-block; }
        .staff-btn-danger { background:#dc2626; border:1px solid #dc2626; color:#fff; border-radius:6px; padding:7px 12px; font-weight:700; }
        .upload-drop-zone {
            margin-top: 10px;
            border: 2px dashed rgba(54,243,255,0.45);
            border-radius: 8px;
            background: rgba(10,132,255,0.08);
            color: #c7d7e8;
            padding: 16px;
            text-align: center;
            transition: border-color 0.2s ease, background 0.2s ease;
        }
        .upload-drop-zone.dragover {
            border-color: #ffc600;
            background: rgba(255,198,0,0.12);
            color: #ffd166;
        }
        .upload-help { color:#a8bedc; margin-top:10px; margin-bottom:0; }
        .portal-table th, .portal-table td { color:#c7d7e8; border-color:#374151 !important; vertical-align:middle !important; }
        .msg { padding:10px 12px; border-radius:6px; margin-bottom:14px; }
        .msg.ok { background:rgba(54,243,255,0.15); border:1px solid rgba(54,243,255,0.35); color:#c7f9ff; }
        .msg.err { background:rgba(239,68,68,0.2); border:1px solid #ef4444; color:#fecaca; }
        code.inline { color:#ffd166; background:rgba(255,198,0,0.12); padding:2px 6px; border-radius:5px; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>
<section class="staff-information">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="title-box">
                    <p>Welcome, <?php echo h($_SESSION['wds_admin_user']); ?></p>
                    <h2 class="title mt0" style="color:#ffd166;">Client Portal Administration</h2>
                    <p style="color:#a8bedc; max-width:760px;">Add, edit, or remove client login accounts and map each account to a subfolder under <code class="inline">portal/files/&lt;folder&gt;</code>.</p>
                </div>
            </div>
        </div>

        <?php if ($message !== ''): ?>
            <div class="msg ok"><?php echo h($message); ?></div>
        <?php endif; ?>
        <?php if ($error !== ''): ?>
            <div class="msg err"><?php echo h($error); ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-sm-6">
                <div class="staff-card">
                    <h3><?php echo $editing ? 'Edit Client Account' : 'Create Client Account'; ?></h3>
                    <form method="post" action="<?php echo $editing ? 'client-portal.php?edit=' . urlencode($editing['id']) : 'client-portal.php'; ?>">
                        <?php if ($editing): ?>
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="id" value="<?php echo h($editing['id']); ?>">
                            <label class="staff-label">Username</label>
                            <input class="staff-input" type="text" value="<?php echo h($editing['username']); ?>" disabled>
                        <?php else: ?>
                            <input type="hidden" name="action" value="create">
                            <label class="staff-label" for="username">Username</label>
                            <input class="staff-input" id="username" name="username" type="text" required>
                        <?php endif; ?>

                        <label class="staff-label" for="display_name">Display Name</label>
                        <input class="staff-input" id="display_name" name="display_name" type="text" value="<?php echo h($editing['display_name'] ?? ''); ?>">

                        <label class="staff-label" for="folder">Subfolder</label>
                        <input class="staff-input" id="folder" name="folder" type="text" value="<?php echo h($editing['folder'] ?? ''); ?>" required>

                        <label class="staff-label" for="password"><?php echo $editing ? 'Password (leave blank to keep current)' : 'Password'; ?></label>
                        <input class="staff-input" id="password" name="password" type="password" <?php echo $editing ? '' : 'required'; ?>>

                        <?php if ($editing): ?>
                            <label class="staff-label" for="active">Account Status</label>
                            <select class="staff-input" id="active" name="active">
                                <option value="1" <?php echo !empty($editing['active']) ? 'selected' : ''; ?>>Active</option>
                                <option value="0" <?php echo empty($editing['active']) ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        <?php endif; ?>

                        <div style="margin-top:14px;">
                            <button class="staff-btn" type="submit"><?php echo $editing ? 'Save Changes' : 'Create Account'; ?></button>
                            <?php if ($editing): ?>
                                <a class="staff-btn-secondary" href="client-portal.php" style="margin-left:8px;">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="staff-card">
                    <h3>Portal References</h3>
                    <p style="color:#c7d7e8;">Client login URL: <code class="inline">/portal</code></p>
                    <p style="color:#c7d7e8;">Folder root path: <code class="inline">portal/files/</code></p>
                    <p style="color:#c7d7e8; margin-bottom:0;">Each account automatically creates <code class="inline">index.html</code> in its subfolder if missing.</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="staff-card">
                    <h3>Upload and Manage Client Files</h3>
                    <?php if (empty($accounts)): ?>
                        <p style="color:#c7d7e8; margin-bottom:0;">Create a client account first, then upload files into that client folder.</p>
                    <?php else: ?>
                        <form method="post" action="client-portal.php" enctype="multipart/form-data" style="margin-bottom:18px;">
                            <input type="hidden" name="action" value="upload_files">
                            <div class="row">
                                <div class="col-sm-4">
                                    <label class="staff-label" for="upload_client_id">Client</label>
                                    <select class="staff-input" id="upload_client_id" name="upload_client_id" required>
                                        <option value="">Select client account</option>
                                        <?php foreach ($accounts as $account): ?>
                                            <option value="<?php echo h($account['id']); ?>" <?php echo ($selectedClientId === ($account['id'] ?? '')) ? 'selected' : ''; ?>>
                                                <?php echo h(($account['display_name'] ?? $account['username']) . ' (' . ($account['folder'] ?? '') . ')'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-sm-5">
                                    <label class="staff-label" for="upload_files">Files</label>
                                    <input class="staff-input" id="upload_files" name="upload_files[]" type="file" multiple required>
                                    <div id="upload_drop_zone" class="upload-drop-zone">
                                        Drag and drop files here, or use the file picker above.
                                    </div>
                                </div>
                                <div class="col-sm-3" style="padding-top:34px;">
                                    <button class="staff-btn" type="submit">Upload Files</button>
                                </div>
                            </div>
                            <p class="upload-help">All file types are allowed for team-managed client delivery. Use trusted files only.</p>
                        </form>

                        <form method="get" action="client-portal.php" style="margin-bottom:16px;">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label class="staff-label" for="files_client">View files for client</label>
                                    <select class="staff-input" id="files_client" name="files_client" onchange="this.form.submit()">
                                        <option value="">Select client account</option>
                                        <?php foreach ($accounts as $account): ?>
                                            <option value="<?php echo h($account['id']); ?>" <?php echo ($selectedClientId === ($account['id'] ?? '')) ? 'selected' : ''; ?>>
                                                <?php echo h(($account['display_name'] ?? $account['username']) . ' (' . ($account['folder'] ?? '') . ')'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </form>

                        <?php if ($selectedClient): ?>
                            <p style="color:#c7d7e8;">Folder: <code class="inline"><?php echo h($selectedClient['folder'] ?? ''); ?></code> &middot; <a href="../portal/files/<?php echo rawurlencode($selectedClient['folder']); ?>/index.html" target="_blank" rel="noopener">Open index page</a></p>
                            <?php if (empty($selectedClientFiles)): ?>
                                <p style="color:#c7d7e8; margin-bottom:0;">No files found in this client folder.</p>
                            <?php else: ?>
                                <div class="table-responsive" style="margin-bottom:8px;">
                                    <table class="table portal-table">
                                        <thead>
                                            <tr>
                                                <th>File</th>
                                                <th>Size</th>
                                                <th>Modified</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($selectedClientFiles as $file): ?>
                                                <tr>
                                                    <td><a href="../portal/<?php echo h($file['url']); ?>" target="_blank" rel="noopener"><?php echo h($file['name']); ?></a></td>
                                                    <td><?php echo number_format(((int)$file['size']) / 1024, 2); ?> KB</td>
                                                    <td><?php echo !empty($file['modified']) ? h(date('Y-m-d H:i', (int)$file['modified'])) : '—'; ?></td>
                                                    <td>
                                                        <?php if (strtolower((string)$file['name']) !== 'index.html'): ?>
                                                            <form method="post" action="client-portal.php?files_client=<?php echo urlencode($selectedClientId); ?>" style="display:inline-block;" onsubmit="return confirm('Delete this file?');">
                                                                <input type="hidden" name="action" value="delete_file">
                                                                <input type="hidden" name="upload_client_id" value="<?php echo h($selectedClientId); ?>">
                                                                <input type="hidden" name="file_name" value="<?php echo h($file['name']); ?>">
                                                                <button class="staff-btn-danger" type="submit">Delete</button>
                                                            </form>
                                                        <?php else: ?>
                                                            <span style="color:#a8bedc;">Protected</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="staff-card">
                    <h3>Existing Client Accounts</h3>
                    <?php if (empty($accounts)): ?>
                        <p style="color:#c7d7e8; margin-bottom:0;">No client accounts found yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table portal-table">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Display Name</th>
                                        <th>Folder</th>
                                        <th>Status</th>
                                        <th>Updated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($accounts as $account): ?>
                                        <tr>
                                            <td><?php echo h($account['username']); ?></td>
                                            <td><?php echo h($account['display_name'] ?? $account['username']); ?></td>
                                            <td>
                                                <code class="inline"><?php echo h($account['folder']); ?></code>
                                                <br>
                                                <a href="../portal/files/<?php echo rawurlencode($account['folder']); ?>/index.html" target="_blank" rel="noopener">Open index page</a>
                                            </td>
                                            <td><?php echo !empty($account['active']) ? 'Active' : 'Inactive'; ?></td>
                                            <td><?php echo !empty($account['updated_at']) ? h(date('Y-m-d H:i', strtotime($account['updated_at']))) : '—'; ?></td>
                                            <td>
                                                <a class="staff-btn-secondary" href="client-portal.php?edit=<?php echo urlencode($account['id']); ?>">Edit</a>
                                                <form method="post" action="client-portal.php" style="display:inline-block; margin-left:6px;" onsubmit="return confirm('Delete this client account?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo h($account['id']); ?>">
                                                    <button class="staff-btn-danger" type="submit">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="../assets/js/jquery-1.12.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script>
(function () {
    var fileInput = document.getElementById('upload_files');
    var dropZone = document.getElementById('upload_drop_zone');
    if (!fileInput || !dropZone) {
        return;
    }

    var prevent = function (event) {
        event.preventDefault();
        event.stopPropagation();
    };

    ['dragenter', 'dragover'].forEach(function (name) {
        dropZone.addEventListener(name, function (event) {
            prevent(event);
            dropZone.classList.add('dragover');
        });
    });

    ['dragleave', 'drop'].forEach(function (name) {
        dropZone.addEventListener(name, function (event) {
            prevent(event);
            dropZone.classList.remove('dragover');
        });
    });

    dropZone.addEventListener('drop', function (event) {
        var files = event.dataTransfer && event.dataTransfer.files ? event.dataTransfer.files : null;
        if (!files || files.length === 0) {
            return;
        }

        var transfer = new DataTransfer();
        for (var index = 0; index < files.length; index++) {
            transfer.items.add(files[index]);
        }
        fileInput.files = transfer.files;
    });
})();
</script>
</body>
</html>
