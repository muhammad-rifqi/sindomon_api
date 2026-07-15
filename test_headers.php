<?php
ob_start();
header('Content-Type: application/json');
echo json_encode([
    'http_authorization' => $_SERVER['HTTP_AUTHORIZATION'] ?? 'NOT SET',
    'redirect_http_auth' => $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? 'NOT SET',
    'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'NOT SET',
    'all_keys' => preg_grep('/AUTH|HTTP_/i', array_keys($_SERVER)),
    'request_headers' => function_exists('apache_request_headers') ? apache_request_headers() : 'not available'
]);
