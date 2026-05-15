<?php
if (!defined('WDS_SYSTEM')) {
    die('Access denied');
}

if (!defined('WDS_CLIENT_PORTAL_DATA_DIR')) {
    define('WDS_CLIENT_PORTAL_DATA_DIR', __DIR__ . '/../content/client-portal');
}
if (!defined('WDS_CLIENT_PORTAL_DATA_FILE')) {
    define('WDS_CLIENT_PORTAL_DATA_FILE', WDS_CLIENT_PORTAL_DATA_DIR . '/clients.json');
}
if (!defined('WDS_CLIENT_PORTAL_FILES_DIR')) {
    define('WDS_CLIENT_PORTAL_FILES_DIR', __DIR__ . '/../portal/files');
}

function clientPortalEnsureStorage() {
    if (!is_dir(WDS_CLIENT_PORTAL_DATA_DIR)) {
        mkdir(WDS_CLIENT_PORTAL_DATA_DIR, 0775, true);
    }
    if (!is_dir(WDS_CLIENT_PORTAL_FILES_DIR)) {
        mkdir(WDS_CLIENT_PORTAL_FILES_DIR, 0775, true);
    }
    if (!file_exists(WDS_CLIENT_PORTAL_DATA_FILE)) {
        file_put_contents(WDS_CLIENT_PORTAL_DATA_FILE, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}

function clientPortalNormalizeUsername($value) {
    $value = trim((string)$value);
    $value = strtolower($value);
    return preg_replace('/[^a-z0-9._-]/', '', $value);
}

function clientPortalNormalizeFolder($value) {
    $value = trim((string)$value);
    $value = strtolower($value);
    $value = preg_replace('/[^a-z0-9_-]/', '-', $value);
    $value = preg_replace('/-+/', '-', $value);
    return trim($value, '-');
}

function loadClientAccounts() {
    clientPortalEnsureStorage();

    $raw = @file_get_contents(WDS_CLIENT_PORTAL_DATA_FILE);
    if ($raw === false || trim($raw) === '') {
        return [];
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        return [];
    }

    $accounts = [];
    foreach ($decoded as $item) {
        if (!is_array($item)) {
            continue;
        }
        if (empty($item['id']) || empty($item['username']) || empty($item['password_hash']) || empty($item['folder'])) {
            continue;
        }
        $accounts[] = $item;
    }

    return $accounts;
}

function saveClientAccounts(array $accounts) {
    clientPortalEnsureStorage();

    $json = json_encode(array_values($accounts), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        return false;
    }

    return file_put_contents(WDS_CLIENT_PORTAL_DATA_FILE, $json, LOCK_EX) !== false;
}

function findClientAccountById($id) {
    $id = trim((string)$id);
    foreach (loadClientAccounts() as $account) {
        if (($account['id'] ?? '') === $id) {
            return $account;
        }
    }
    return null;
}

function findClientAccountByUsername($username) {
    $username = clientPortalNormalizeUsername($username);
    foreach (loadClientAccounts() as $account) {
        if (($account['username'] ?? '') === $username) {
            return $account;
        }
    }
    return null;
}

function clientPortalEnsureFolder($folder) {
    $folder = clientPortalNormalizeFolder($folder);
    if ($folder === '') {
        return false;
    }

    $folderPath = WDS_CLIENT_PORTAL_FILES_DIR . '/' . $folder;
    if (!is_dir($folderPath) && !mkdir($folderPath, 0775, true)) {
        return false;
    }

    $indexPath = $folderPath . '/index.html';
    if (!file_exists($indexPath)) {
        $title = htmlspecialchars('Client Portal Files - ' . $folder, ENT_QUOTES, 'UTF-8');
        $html = "<!DOCTYPE html>\n"
            . "<html lang=\"en\">\n"
            . "<head>\n"
            . "  <meta charset=\"UTF-8\">\n"
            . "  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n"
            . "  <title>{$title}</title>\n"
            . "  <style>body{font-family:Segoe UI,Tahoma,Verdana,sans-serif;background:#07111f;color:#eaf3ff;padding:24px}a{color:#0a84ff}h1{color:#ffc600}</style>\n"
            . "</head>\n"
            . "<body>\n"
            . "  <h1>{$title}</h1>\n"
            . "  <p>Upload files into this folder and add links here as needed.</p>\n"
            . "</body>\n"
            . "</html>\n";
        file_put_contents($indexPath, $html);
    }

    return true;
}

function createClientAccount($username, $password, $displayName, $folder, &$error = '') {
    $username = clientPortalNormalizeUsername($username);
    $folder = clientPortalNormalizeFolder($folder);
    $displayName = trim((string)$displayName);

    if ($username === '' || strlen($username) < 3) {
        $error = 'Username must be at least 3 characters and use only letters, numbers, dot, underscore, or dash.';
        return false;
    }
    if (strlen((string)$password) < 8) {
        $error = 'Password must be at least 8 characters.';
        return false;
    }
    if ($folder === '') {
        $error = 'Folder is required.';
        return false;
    }

    $accounts = loadClientAccounts();
    foreach ($accounts as $account) {
        if (($account['username'] ?? '') === $username) {
            $error = 'That username already exists.';
            return false;
        }
    }

    if (!clientPortalEnsureFolder($folder)) {
        $error = 'Unable to create or access client folder.';
        return false;
    }

    $accounts[] = [
        'id' => bin2hex(random_bytes(8)),
        'username' => $username,
        'password_hash' => password_hash((string)$password, PASSWORD_DEFAULT),
        'display_name' => $displayName !== '' ? $displayName : $username,
        'folder' => $folder,
        'active' => true,
        'created_at' => date('c'),
        'updated_at' => date('c')
    ];

    if (!saveClientAccounts($accounts)) {
        $error = 'Failed to save client account data.';
        return false;
    }

    return true;
}

function updateClientAccount($id, $displayName, $folder, $password = '', $active = true, &$error = '') {
    $id = trim((string)$id);
    $folder = clientPortalNormalizeFolder($folder);
    $displayName = trim((string)$displayName);
    $accounts = loadClientAccounts();

    $found = false;
    foreach ($accounts as &$account) {
        if (($account['id'] ?? '') !== $id) {
            continue;
        }

        if ($folder === '') {
            $error = 'Folder is required.';
            return false;
        }

        if (!clientPortalEnsureFolder($folder)) {
            $error = 'Unable to create or access client folder.';
            return false;
        }

        $account['display_name'] = $displayName !== '' ? $displayName : ($account['username'] ?? 'client');
        $account['folder'] = $folder;
        $account['active'] = (bool)$active;

        if (trim((string)$password) !== '') {
            if (strlen((string)$password) < 8) {
                $error = 'Password must be at least 8 characters.';
                return false;
            }
            $account['password_hash'] = password_hash((string)$password, PASSWORD_DEFAULT);
        }

        $account['updated_at'] = date('c');
        $found = true;
        break;
    }
    unset($account);

    if (!$found) {
        $error = 'Client account not found.';
        return false;
    }

    if (!saveClientAccounts($accounts)) {
        $error = 'Failed to save client account updates.';
        return false;
    }

    return true;
}

function deleteClientAccount($id, &$error = '') {
    $id = trim((string)$id);
    $accounts = loadClientAccounts();
    $remaining = [];
    $found = false;

    foreach ($accounts as $account) {
        if (($account['id'] ?? '') === $id) {
            $found = true;
            continue;
        }
        $remaining[] = $account;
    }

    if (!$found) {
        $error = 'Client account not found.';
        return false;
    }

    if (!saveClientAccounts($remaining)) {
        $error = 'Failed to delete client account.';
        return false;
    }

    return true;
}

function verifyClientLogin($username, $password) {
    $account = findClientAccountByUsername($username);
    if (!$account || empty($account['active'])) {
        return false;
    }

    if (!password_verify((string)$password, (string)($account['password_hash'] ?? ''))) {
        return false;
    }

    return $account;
}

function clientPortalListFolderFiles($folder) {
    $folder = clientPortalNormalizeFolder($folder);
    if ($folder === '') {
        return [];
    }

    $dir = WDS_CLIENT_PORTAL_FILES_DIR . '/' . $folder;
    if (!is_dir($dir)) {
        return [];
    }

    $items = [];
    $entries = scandir($dir);
    if (!is_array($entries)) {
        return [];
    }

    foreach ($entries as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }
        $fullPath = $dir . '/' . $entry;
        if (!is_file($fullPath)) {
            continue;
        }
        $items[] = [
            'name' => $entry,
            'size' => filesize($fullPath) ?: 0,
            'modified' => filemtime($fullPath) ?: 0,
            'url' => 'files/' . rawurlencode($folder) . '/' . rawurlencode($entry)
        ];
    }

    usort($items, function ($a, $b) {
        return strcmp($a['name'], $b['name']);
    });

    return $items;
}

function clientPortalResolveFolderPath($folder) {
    $folder = clientPortalNormalizeFolder($folder);
    if ($folder === '') {
        return false;
    }

    $path = WDS_CLIENT_PORTAL_FILES_DIR . '/' . $folder;
    return is_dir($path) ? $path : false;
}

function clientPortalSanitizeUploadName($name) {
    $name = trim((string)$name);
    $name = basename($name);
    $name = preg_replace('/[^a-zA-Z0-9._-]/', '_', $name);
    $name = preg_replace('/_+/', '_', $name);
    return trim((string)$name, '._');
}

function clientPortalUploadFile($folder, $fileInfo, &$error = '') {
    $folder = clientPortalNormalizeFolder($folder);
    if ($folder === '') {
        $error = 'A valid client folder is required for upload.';
        return false;
    }

    if (!clientPortalEnsureFolder($folder)) {
        $error = 'Unable to create or access client folder.';
        return false;
    }

    if (!is_array($fileInfo) || !isset($fileInfo['tmp_name'], $fileInfo['name'], $fileInfo['error'])) {
        $error = 'No upload file data was received.';
        return false;
    }

    if ((int)$fileInfo['error'] !== UPLOAD_ERR_OK) {
        $error = 'Upload failed (error code ' . (int)$fileInfo['error'] . ').';
        return false;
    }

    $safeName = clientPortalSanitizeUploadName($fileInfo['name']);
    if ($safeName === '') {
        $error = 'Uploaded filename is invalid.';
        return false;
    }

    $targetDir = clientPortalResolveFolderPath($folder);
    if ($targetDir === false) {
        $error = 'Client folder does not exist.';
        return false;
    }

    $targetPath = $targetDir . '/' . $safeName;
    if (!is_uploaded_file((string)$fileInfo['tmp_name'])) {
        $error = 'Invalid uploaded file.';
        return false;
    }

    if (!move_uploaded_file((string)$fileInfo['tmp_name'], $targetPath)) {
        $error = 'Failed to move uploaded file.';
        return false;
    }

    @chmod($targetPath, 0644);
    return true;
}

function clientPortalDeleteFile($folder, $fileName, &$error = '') {
    $folder = clientPortalNormalizeFolder($folder);
    $fileName = clientPortalSanitizeUploadName($fileName);

    if ($folder === '' || $fileName === '') {
        $error = 'Folder and file are required.';
        return false;
    }

    if (strtolower($fileName) === 'index.html') {
        $error = 'The default index.html file cannot be deleted from this tool.';
        return false;
    }

    $targetDir = clientPortalResolveFolderPath($folder);
    if ($targetDir === false) {
        $error = 'Client folder does not exist.';
        return false;
    }

    $path = $targetDir . '/' . $fileName;
    if (!is_file($path)) {
        $error = 'File not found.';
        return false;
    }

    if (!unlink($path)) {
        $error = 'Failed to delete file.';
        return false;
    }

    return true;
}

function clientPortalNormalizeUploadFileBag($fileBag) {
    $normalized = [];

    if (!is_array($fileBag) || !isset($fileBag['name'])) {
        return $normalized;
    }

    if (is_array($fileBag['name'])) {
        $count = count($fileBag['name']);
        for ($index = 0; $index < $count; $index++) {
            $normalized[] = [
                'name' => $fileBag['name'][$index] ?? '',
                'type' => $fileBag['type'][$index] ?? '',
                'tmp_name' => $fileBag['tmp_name'][$index] ?? '',
                'error' => $fileBag['error'][$index] ?? UPLOAD_ERR_NO_FILE,
                'size' => $fileBag['size'][$index] ?? 0,
            ];
        }
        return $normalized;
    }

    $normalized[] = $fileBag;
    return $normalized;
}

function clientPortalUploadFiles($folder, $fileBag, &$uploadedFiles = [], &$failedFiles = [], &$error = '') {
    $uploadedFiles = [];
    $failedFiles = [];

    $files = clientPortalNormalizeUploadFileBag($fileBag);
    if (empty($files)) {
        $error = 'No upload files were provided.';
        return false;
    }

    foreach ($files as $fileInfo) {
        $uploadError = '';

        if ((int)($fileInfo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        $ok = clientPortalUploadFile($folder, $fileInfo, $uploadError);
        if ($ok) {
            $uploadedFiles[] = (string)($fileInfo['name'] ?? '');
        } else {
            $failedFiles[] = [
                'name' => (string)($fileInfo['name'] ?? 'unknown-file'),
                'reason' => $uploadError !== '' ? $uploadError : 'Upload failed.'
            ];
        }
    }

    if (empty($uploadedFiles) && !empty($failedFiles)) {
        $error = 'All selected files failed to upload.';
        return false;
    }

    if (empty($uploadedFiles) && empty($failedFiles)) {
        $error = 'No files were selected.';
        return false;
    }

    return true;
}
