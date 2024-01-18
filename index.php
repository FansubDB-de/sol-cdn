<?php

// Set the base directory for the static files
define('BASE_DIR', __DIR__ . '/storage/');
// Set the cache directory
define('CACHE_DIR', __DIR__ . '/cache/');

// Set the environment variable in your server configuration or virtual host
$environment = getenv('APPLICATION_ENV') ?: 'production';

// Security Headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Cache-Control Header for one week
header('Cache-Control: max-age=604800, public');

// Get the requested file path
$requestedFile = $_SERVER['REQUEST_URI'];

// If no specific file is requested, set a default file
if ($requestedFile === '/') {
    $requestedFile = '/default.jpg'; // Change this to your desired default file
}

// Build the full path to the requested file
$filePath = BASE_DIR . $requestedFile;

// Check if the file exists
if (file_exists($filePath)) {
    // Check if the cached version exists
    $cacheFile = CACHE_DIR . md5($requestedFile);

    if (file_exists($cacheFile) && filemtime($cacheFile) >= filemtime($filePath)) {
        // Serve the cached version if it's up-to-date
        serveCachedFile($cacheFile);
    } else {
        // Generate cache and serve the file
        generateAndServeCache($filePath, $cacheFile);
    }
} else {
    // If the file doesn't exist, return a 404 error
    header('HTTP/1.1 404 Not Found');
    echo '404 Not Found...?';
    exit;
}

// Log the request in the production environment
if ($environment === 'production') {
    error_log(date('[Y-m-d H:i:s]') . ' ' . $_SERVER['REQUEST_URI'] . PHP_EOL, 3, __DIR__ . '/logs/access.log');
}

function serveCachedFile($cacheFile)
{
    // Get the file's MIME type
    $fileMimeType = mime_content_type($cacheFile);

    // Set appropriate headers
    header('Content-Type: ' . $fileMimeType);
    header('Content-Length: ' . filesize($cacheFile));

    // Output the cached file content
    readfile($cacheFile);
}

function generateAndServeCache($filePath, $cacheFile)
{
    // Copy the original file content to the cache directory
    copy($filePath, $cacheFile);

    // Get the file's MIME type
    $fileMimeType = mime_content_type($filePath);

    // Set appropriate headers
    header('Content-Type: ' . $fileMimeType);
    header('Content-Length: ' . filesize($filePath));

    // Output the original file content
    readfile($filePath);
}
