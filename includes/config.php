<?php
/**
 * Universal Configuration for Runlevel Systems Website
 * Works with both XAMPP and web hosting platforms
 */

// Define the website root URL based on environment
if (!defined('WDS_BASE_URL')) {
    
    // Auto-detect environment
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $request_uri = $_SERVER['REQUEST_URI'];
    $script_name = $_SERVER['SCRIPT_NAME'];
    
    // Detect if we're in a subdirectory installation
    $web_root = '';
    if (strpos($script_name, 'world-domination') !== false) {
        // Extract the path up to world-domination
        $parts = explode('/', $script_name);
        $path_parts = [];
        foreach ($parts as $part) {
            if ($part === 'world-domination') {
                $path_parts[] = $part;
                break;
            }
            if ($part !== '') {
                $path_parts[] = $part;
            }
        }
        $web_root = '/' . implode('/', $path_parts);
    }
    
    define('WDS_BASE_URL', $protocol . '://' . $host . $web_root);
}

// Function to get correct relative path based on current location
function getBasePath() {
    $current_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $is_in_wiki = (strpos($current_url, '/staff/wiki/') !== false);
    $is_in_staff = (strpos($current_url, '/staff/') !== false);
    $is_in_projects = (strpos($current_url, '/projects/') !== false);
    $is_in_client = (strpos($current_url, '/client/') !== false);
	
    if ($is_in_wiki) {
        // Wiki lives one level deeper under /staff/wiki/
        return '../../';
    } elseif ($is_in_staff || $is_in_projects || $is_in_client) {
        // Staff tools and project pages sit one level below the site root
        return '../';
    }
	
    // Site root (index, contact, joinus, etc.)
    return '';
}

// Function to get absolute URL for assets
function getAssetUrl($path) {
    return WDS_BASE_URL . '/' . ltrim($path, '/');
}

// Function to get page URL
function getPageUrl($page) {
    $base_path = getBasePath();
    return $base_path . $page;
}
?>