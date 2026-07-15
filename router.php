<?php
// Hermes test router — PHP 8.5 compat + PHP built-in server Auth passthrough
$_SERVER['CI_ENV'] = getenv('CI_ENV') ?: 'testing';
error_reporting(0);
ini_set('display_errors', 0);

// PHP built-in server drops Authorization header — restore it
if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['HTTP_AUTHORIZATION'];
}

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}
$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__ . '/index.php';
