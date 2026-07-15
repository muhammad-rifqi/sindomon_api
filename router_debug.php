<?php
// Debug router — show $_SERVER keys
$_SERVER['CI_ENV'] = 'testing';
error_reporting(0);
ini_set('display_errors', 0);

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

if ($uri === '/debug-headers') {
    header('Content-Type: application/json');
    $h = [];
    foreach ($_SERVER as $k => $v) {
        if (strpos($k, 'HTTP') === 0 || $k === 'HTTP_AUTHORIZATION') {
            $h[$k] = $v;
        }
    }
    if (function_exists('getallheaders')) {
        $h['getallheaders()'] = getallheaders();
    }
    echo json_encode($h, JSON_PRETTY_PRINT);
    return;
}

if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}
$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__ . '/index.php';
