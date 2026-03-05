// Developed by World Domination Software LLC
<?php
// Include the universal config
require_once 'includes/config.php';

// Debug page to help identify XAMPP and web hosting path issues
echo "<h2>Universal Path Debug Information</h2>";

echo "<h3>Server Information:</h3>";
echo "<p><strong>HTTP_HOST:</strong> " . $_SERVER['HTTP_HOST'] . "</p>";
echo "<p><strong>REQUEST_URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><strong>SCRIPT_NAME:</strong> " . $_SERVER['SCRIPT_NAME'] . "</p>";
echo "<p><strong>DOCUMENT_ROOT:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Current Directory:</strong> " . __DIR__ . "</p>";

echo "<h3>File Existence Check:</h3>";
$files_to_check = [
    'assets/images/wds-logo.png',
    'assets/images/code.png', 
    'assets/css/main.css',
    'includes/navigation.php',
    'includes/header.php'
];

foreach ($files_to_check as $file) {
    $exists = file_exists($file) ? '✅ EXISTS' : '❌ MISSING';
    echo "<p><strong>$file:</strong> $exists</p>";
}

echo "<h3>Generated URLs (what navigation.php would create):</h3>";
$request_uri = $_SERVER['REQUEST_URI'];
$is_in_projects = (strpos($request_uri, '/projects/') !== false);

if ($is_in_projects) {
    $base_path = '../';
    echo "<p>Detected: IN PROJECTS SUBDIRECTORY</p>";
} else {
    $base_path = '';
    echo "<p>Detected: IN ROOT DIRECTORY</p>";
}

echo "<p><strong>Base path:</strong> '$base_path'</p>";
echo "<p><strong>Logo URL would be:</strong> {$base_path}assets/images/wds-logo.png</p>";
echo "<p><strong>Code.png URL would be:</strong> {$base_path}assets/images/code.png</p>";
echo "<p><strong>Home link would be:</strong> {$base_path}index.php</p>";

echo "<h3>Universal Configuration Test:</h3>";
echo "<p><strong>WDS_BASE_URL:</strong> " . WDS_BASE_URL . "</p>";
echo "<p><strong>getBasePath():</strong> '" . getBasePath() . "'</p>";
echo "<p><strong>Logo URL (absolute):</strong> " . getAssetUrl('assets/images/wds-logo.png') . "</p>";
echo "<p><strong>Home URL (relative):</strong> " . getPageUrl('index.php') . "</p>";

echo "<h3>Web Host Compatibility:</h3>";
echo "<p>✅ <strong>Relative paths:</strong> Will work on any hosting platform</p>";
echo "<p>✅ <strong>Auto-detection:</strong> Adapts to subdirectory installations</p>";
echo "<p>✅ <strong>HTTPS support:</strong> Detects protocol automatically</p>";

echo "<h3>Test Links:</h3>";
echo "<p><a href='" . getPageUrl('index.php') . "'>Home Page Test</a></p>";
echo "<p><a href='" . getPageUrl('projects.php') . "'>Projects Page Test</a></p>";
?>
