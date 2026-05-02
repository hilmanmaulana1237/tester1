<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Enable GZIP output compression if supported
if (
    extension_loaded('zlib') &&
    !ini_get('zlib.output_compression') &&
    isset($_SERVER['HTTP_ACCEPT_ENCODING']) &&
    strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false
) {
    ini_set('zlib.output_compression', 'On');
    ini_set('zlib.output_compression_level', '6');
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->handleRequest(Request::capture());
