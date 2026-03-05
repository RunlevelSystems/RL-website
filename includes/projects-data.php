// Developed by World Domination Software LLC
<?php

// Central JSON-based projects data access helpers

require_once __DIR__ . '/config.php';

// Get absolute filesystem path to the projects JSON file
function getProjectsJsonPath() {
    $baseDir = realpath(__DIR__ . '/..');
    return $baseDir . '/content/projects/projects.json';
}

// Load all projects (migrates from legacy folders on first run)
function loadAllProjects() {
    $jsonPath = getProjectsJsonPath();

    if (!file_exists($jsonPath)) {
        $projects = migrateLegacyProjectsToJson();
        saveAllProjects($projects);
        return $projects;
    }

    $raw = file_get_contents($jsonPath);
    if ($raw === false || $raw === '') {
        return array();
    }

    $data = json_decode($raw, true);
    if (!is_array($data)) {
        return array();
    }

    return $data;
}

// Get category display order from a JSON file, or defaults
function getCategoryOrder() {
    $baseDir = realpath(__DIR__ . '/..');
    $path = $baseDir . '/content/projects/categories.json';
    if (file_exists($path)) {
        $raw = file_get_contents($path);
        if ($raw !== false && $raw !== '') {
            $data = json_decode($raw, true);
            if (is_array($data)) {
                // filter to scalar strings only
                $out = array();
                foreach ($data as $item) {
                    if (is_string($item) && $item !== '') {
                        $out[] = $item;
                    }
                }
                if (!empty($out)) {
                    return $out;
                }
            }
        }
    }

    // Default order
    return array('Completed Projects', 'Current Projects', 'Upcoming Projects');
}

// Persist projects array back to the JSON file
function saveAllProjects($projects) {
    if (!is_array($projects)) {
        $projects = array();
    }

    $jsonPath = getProjectsJsonPath();
    $dir = dirname($jsonPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    // Avoid JSON_UNESCAPED_SLASHES for maximum compatibility
    if (defined('JSON_PRETTY_PRINT')) {
        $json = json_encode($projects, JSON_PRETTY_PRINT);
    } else {
        $json = json_encode($projects);
    }

    if ($json === false) {
        return false;
    }

    return file_put_contents($jsonPath, $json) !== false;
}

// Find a project by slug
function findProjectBySlug($projects, $slug) {
    if (!is_array($projects)) {
        return null;
    }
    foreach ($projects as $project) {
        if (isset($project['slug']) && $project['slug'] === $slug) {
            return $project;
        }
    }
    return null;
}

// Update (or insert) a project by slug
function upsertProjectBySlug(&$projects, $slug, $newData) {
    if (!is_array($projects)) {
        $projects = array();
    }
    if (!is_array($newData)) {
        $newData = array();
    }

    $updated = false;
    foreach ($projects as $index => $project) {
        if (isset($project['slug']) && $project['slug'] === $slug) {
            $projects[$index] = array_merge($project, $newData);
            $updated = true;
            break;
        }
    }

    if (!$updated) {
        if (!isset($newData['slug'])) {
            $newData['slug'] = $slug;
        }
        if (!isset($newData['id'])) {
            $newData['id'] = $slug;
        }
        $projects[] = $newData;
    }

    return true;
}

// Remove a project by slug
function deleteProjectBySlug(&$projects, $slug) {
    if (!is_array($projects)) {
        return false;
    }

    $found = false;
    foreach ($projects as $index => $project) {
        if (isset($project['slug']) && $project['slug'] === $slug) {
            unset($projects[$index]);
            $found = true;
            break;
        }
    }

    if ($found) {
        $projects = array_values($projects);
    }

    return $found;
}

// One-time migration from legacy /projects/*/project.json layout
function migrateLegacyProjectsToJson() {
    $baseDir = realpath(__DIR__ . '/..');
    $projectsDir = $baseDir . '/projects';
    $projects = array();

    if (!is_dir($projectsDir)) {
        return $projects;
    }

    $directories = scandir($projectsDir);
    if (!is_array($directories)) {
        return $projects;
    }

    foreach ($directories as $dir) {
        if ($dir === '.' || $dir === '..') {
            continue;
        }

        $dirPath = $projectsDir . '/' . $dir;
        if (!is_dir($dirPath)) {
            continue;
        }

        $metadataFile = $dirPath . '/project.json';
        if (!file_exists($metadataFile)) {
            continue;
        }

        $metadataRaw = file_get_contents($metadataFile);
        if ($metadataRaw === false) {
            continue;
        }

        $metadata = json_decode($metadataRaw, true);
        if (!is_array($metadata)) {
            continue;
        }

        $slug = $dir;
        $title = isset($metadata['title']) ? $metadata['title'] : $slug;
        $category = isset($metadata['category']) ? $metadata['category'] : 'Current Projects';
        $normalizedCategory = mapLegacyCategory($category);
        $description = isset($metadata['description']) ? $metadata['description'] : '';

        $project = array(
            'id' => $slug,
            'slug' => $slug,
            'title' => $title,
            'category' => $normalizedCategory,
            'shortDescription' => $description,
            'fullDescription' => $description,
            'votes' => 0,
            'images' => array(),
        );

        $projects[] = $project;
    }

    // Rename Infestation Control: Earth to Optimization Protocol
    foreach ($projects as $index => $project) {
        if (isset($project['title']) && stripos($project['title'], 'Infestation Control') !== false) {
            $projects[$index]['title'] = 'Optimization Protocol';
            $projects[$index]['shortDescription'] = 'AI was given the task to optimize the world\'s automated systems and human civilization was the part that needed the most optimization which creates a dystopian, survival sandbox in a chaotic world.';
            $projects[$index]['fullDescription'] = $projects[$index]['shortDescription'];
        }
    }

    return $projects;
}

// Map legacy category labels to the new defaults
function mapLegacyCategory($category) {
    $normalized = trim($category);

    switch ($normalized) {
        case 'Legacy Project':
        case 'Completed Project':
        case 'Completed Projects':
            return 'Completed Projects';
        case 'Current Project':
        case 'Active Development':
        case 'Current Projects':
            return 'Current Projects';
        case 'Upcoming Project':
        case 'Upcoming Projects':
        case 'Idea Board':
            return 'Upcoming Projects';
        default:
            return $normalized !== '' ? $normalized : 'Current Projects';
    }
}

?>
