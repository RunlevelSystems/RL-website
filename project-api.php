// Developed by World Domination Software LLC
<?php
// Project API for list/detail/votes/admin edits

define('WDS_SYSTEM', true);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db-config.php';
require_once __DIR__ . '/includes/projects-data.php';

if (!isset($_SESSION)) {
    session_start();
}

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

// Default to JSON responses
header('Content-Type: application/json; charset=utf-8');

function json_error($message, $code = 400) {
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $message]);
    exit;
}

function get_slug_from_request() {
    $slug = isset($_REQUEST['slug']) ? $_REQUEST['slug'] : '';
    $slug = preg_replace('~[^a-zA-Z0-9_-]~', '', $slug);
    if ($slug === '') {
        json_error('Missing or invalid slug', 400);
    }
    return $slug;
}

switch ($action) {
    case 'detail': {
        $slug = get_slug_from_request();
        $projects = loadAllProjects();
        $project = findProjectBySlug($projects, $slug);
        if (!$project) {
            json_error('Project not found', 404);
        }
        $isAdmin = isLoggedInAdmin();

        echo json_encode([
            'ok' => true,
            'project' => $project,
            'isAdmin' => $isAdmin,
        ]);
        exit;
    }

    case 'vote': {
        $slug = get_slug_from_request();
        $delta = isset($_POST['delta']) ? (int)$_POST['delta'] : 0;
        if ($delta === 0) {
            json_error('Invalid vote delta', 400);
        }

        $projects = loadAllProjects();
        $found = false;
        foreach ($projects as &$p) {
            if (isset($p['slug']) && $p['slug'] === $slug) {
                if (!isset($p['votes'])) {
                    $p['votes'] = 0;
                }
                $p['votes'] = (int)$p['votes'] + $delta; // can go below zero
                $newVotes = $p['votes'];
                $found = true;
                break;
            }
        }
        unset($p);

        if (!$found) {
            json_error('Project not found', 404);
        }

        if (!saveAllProjects($projects)) {
            json_error('Failed to save vote', 500);
        }

        echo json_encode(['ok' => true, 'votes' => $newVotes]);
        exit;
    }

    case 'save': {
        if (!isLoggedInAdmin()) {
            json_error('Not authorized', 403);
        }
        $slug = get_slug_from_request();

        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $category = isset($_POST['category']) ? trim($_POST['category']) : '';
        $short = isset($_POST['shortDescription']) ? trim($_POST['shortDescription']) : '';
        $full = isset($_POST['fullDescription']) ? trim($_POST['fullDescription']) : '';
        $votes = isset($_POST['votes']) ? (int)$_POST['votes'] : 0;
        $imagesText = isset($_POST['images']) ? trim($_POST['images']) : '';

        $images = [];
        if ($imagesText !== '') {
            $lines = preg_split('/\r?\n/', $imagesText);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line !== '') {
                    $images[] = basename($line);
                }
            }
        }

        $projects = loadAllProjects();
        $found = false;
        foreach ($projects as &$p) {
            if (isset($p['slug']) && $p['slug'] === $slug) {
                if ($title !== '') {
                    $p['title'] = $title;
                }
                if ($category !== '') {
                    $p['category'] = $category;
                }
                $p['shortDescription'] = $short;
                $p['fullDescription'] = $full;
                $p['votes'] = $votes;
                $p['images'] = $images;
                $found = true;
                $updatedProject = $p;
                break;
            }
        }
        unset($p);

        if (!$found) {
            json_error('Project not found', 404);
        }

        if (!saveAllProjects($projects)) {
            json_error('Failed to save project', 500);
        }

        echo json_encode(['ok' => true, 'project' => $updatedProject]);
        exit;
    }

    case 'delete': {
        if (!isLoggedInAdmin()) {
            json_error('Not authorized', 403);
        }
        $slug = get_slug_from_request();

        $projects = loadAllProjects();
        $deleted = deleteProjectBySlug($projects, $slug);
        if (!$deleted) {
            json_error('Project not found', 404);
        }

        if (!saveAllProjects($projects)) {
            json_error('Failed to delete project', 500);
        }

        // Optionally remove images directory
        $baseDir = realpath(__DIR__);
        $imagesDir = $baseDir . '/assets/images/projects/' . $slug;
        if (is_dir($imagesDir)) {
            $items = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($imagesDir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($items as $item) {
                if ($item->isDir()) {
                    @rmdir($item->getRealPath());
                } else {
                    @unlink($item->getRealPath());
                }
            }
            @rmdir($imagesDir);
        }

        echo json_encode(['ok' => true]);
        exit;
    }

    case 'uploadImages': {
        if (!isLoggedInAdmin()) {
            json_error('Not authorized', 403);
        }
        $slug = get_slug_from_request();

        if (!isset($_FILES['images'])) {
            json_error('No files uploaded', 400);
        }

        $baseDir = realpath(__DIR__);
        $projectImagesDir = $baseDir . '/assets/images/projects/' . $slug;
        if (!is_dir($projectImagesDir)) {
            mkdir($projectImagesDir, 0775, true);
        }

        $projects = loadAllProjects();
        $project = null;
        foreach ($projects as &$p) {
            if (isset($p['slug']) && $p['slug'] === $slug) {
                if (!isset($p['images']) || !is_array($p['images'])) {
                    $p['images'] = [];
                }
                $project = &$p;
                break;
            }
        }
        unset($p);

        if ($project === null) {
            json_error('Project not found', 404);
        }

        $files = $_FILES['images'];
        $count = is_array($files['name']) ? count($files['name']) : 0;
        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }
            $name = basename($files['name'][$i]);
            $target = $projectImagesDir . '/' . $name;
            if (move_uploaded_file($files['tmp_name'][$i], $target)) {
                if (!in_array($name, $project['images'], true)) {
                    $project['images'][] = $name;
                }
            }
        }

        if (!saveAllProjects($projects)) {
            json_error('Failed to save images', 500);
        }

        echo json_encode(['ok' => true, 'images' => $project['images']]);
        exit;
    }

    case 'deleteImage': {
        if (!isLoggedInAdmin()) {
            json_error('Not authorized', 403);
        }
        $slug = get_slug_from_request();
        $filename = isset($_POST['filename']) ? basename($_POST['filename']) : '';
        if ($filename === '') {
            json_error('Missing filename', 400);
        }

        $baseDir = realpath(__DIR__);
        $filePath = $baseDir . '/assets/images/projects/' . $slug . '/' . $filename;
        if (is_file($filePath)) {
            @unlink($filePath);
        }

        $projects = loadAllProjects();
        $project = null;
        foreach ($projects as &$p) {
            if (isset($p['slug']) && $p['slug'] === $slug) {
                if (!isset($p['images']) || !is_array($p['images'])) {
                    $p['images'] = [];
                }
                $project = &$p;
                break;
            }
        }
        unset($p);

        if ($project === null) {
            json_error('Project not found', 404);
        }

        $project['images'] = array_values(array_filter($project['images'], function ($img) use ($filename) {
            return $img !== $filename;
        }));

        if (!saveAllProjects($projects)) {
            json_error('Failed to update project images', 500);
        }

        echo json_encode(['ok' => true, 'images' => $project['images']]);
        exit;
    }

    default:
        json_error('Unknown action', 400);
}

?>
