<?php

/**
 * Custom Router for PHP Built-in Server
 * 
 * This file serves as a router when using `php artisan serve`.
 * It adds caching headers and GZIP compression for static assets.
 * 
 * Usage: php -S localhost:8000 -t public server.php
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Check if this is a static asset request
if (preg_match('/\.(css|js|woff2?|ttf|eot|svg|png|jpg|jpeg|gif|ico|webp)$/i', $uri)) {
    $filePath = __DIR__ . '/public' . $uri;

    if (file_exists($filePath)) {
        $extension = strtolower(pathinfo($uri, PATHINFO_EXTENSION));

        // MIME types
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject',
            'svg' => 'image/svg+xml',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'ico' => 'image/x-icon',
            'webp' => 'image/webp',
        ];

        $contentType = $mimeTypes[$extension] ?? 'application/octet-stream';
        $etag = '"' . md5_file($filePath) . '"';

        // Check for 304 Not Modified
        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] === $etag) {
            http_response_code(304);
            header('Cache-Control: public, max-age=31536000, immutable');
            header('ETag: ' . $etag);
            exit;
        }

        // Set caching headers (1 year for versioned assets)
        header('Content-Type: ' . $contentType . '; charset=UTF-8');
        header('Cache-Control: public, max-age=31536000, immutable');
        header('ETag: ' . $etag);
        header('Vary: Accept-Encoding');

        $content = file_get_contents($filePath);

        // Apply GZIP compression for text-based assets
        $acceptEncoding = $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '';
        $compressible = in_array($extension, ['css', 'js', 'svg']);

        if ($compressible && strpos($acceptEncoding, 'gzip') !== false && function_exists('gzencode')) {
            $compressed = gzencode($content, 6);
            if ($compressed !== false) {
                header('Content-Encoding: gzip');
                header('Content-Length: ' . strlen($compressed));
                echo $compressed;
                exit;
            }
        }

        header('Content-Length: ' . strlen($content));
        echo $content;
        exit;
    }
}

// For all other requests, let Laravel handle it
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/public/index.php';
require __DIR__ . '/public/index.php';
