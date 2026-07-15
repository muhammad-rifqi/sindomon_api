<?php
// Dev server router — forwards Authorization header
if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['HTTP_AUTHORIZATION'];
}
// Some PHP built-in server versions strip it entirely; also check REDIRECT_HTTP_AUTHORIZATION
if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
    $_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
}
// Fallback: also parse from request headers (Apache-friendly)
if (!isset($_SERVER['HTTP_AUTHORIZATION']) && function_exists('apache_request_headers')) {
    $headers = apache_request_headers();
    if (isset($headers['Authorization'])) {
        $_SERVER['HTTP_AUTHORIZATION'] = $headers['Authorization'];
    }
}
return false; // Serve normally
