<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Smart JWT Token Extraction
 *
 * Handles both:
 *   - "Bearer <token>" format (Postman, standard HTTP clients)
 *   - Raw token format (Flutter sends token directly: "eyJhbGci...")
 *
 * @param CI_Controller $ci  The controller instance ($this)
 * @return array|null        Decoded payload, or null if token missing/invalid
 */
function get_jwt_payload($ci) {
    $headers = $ci->input->request_headers();
    if (!isset($headers['Authorization'])) {
        return null;
    }

    $auth = $headers['Authorization'];

    // Strip "Bearer " prefix if present; otherwise treat entire string as raw token
    if (substr($auth, 0, 7) === 'Bearer ') {
        $token = substr($auth, 7);
    } else {
        $token = $auth;
    }

    return $ci->jwt->decode($token) ?: null;
}
if (!function_exists('base64url_encode')) {
    function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}

if (!function_exists('base64url_decode')) {
    function base64url_decode($data)
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(strtr($data, '-_', '+/'));
    }
}

if (!function_exists('jwt_encode')) {
    function jwt_encode($payload)
    {
        $CI =& get_instance();
        $secret = $CI->config->item('jwt_secret');

        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];

        $header = base64url_encode(json_encode($header));
        $payload = base64url_encode(json_encode($payload));

        $signature = hash_hmac(
            'sha256',
            $header.'.'.$payload,
            $secret,
            true
        );

        $signature = base64url_encode($signature);

        return $header.'.'.$payload.'.'.$signature;
    }
}

if (!function_exists('jwt_decode')) {
    function jwt_decode($token)
    {
        $CI =& get_instance();
        $secret = $CI->config->item('jwt_secret');

        $parts = explode('.', $token);

        if (count($parts) != 3) {
            return false;
        }

        list($header, $payload, $signature) = $parts;

        $expected = base64url_encode(
            hash_hmac(
                'sha256',
                $header.'.'.$payload,
                $secret,
                true
            )
        );

        if (!hash_equals($expected, $signature)) {
            return false;
        }

        $payload = json_decode(base64url_decode($payload), true);

        if (isset($payload['exp']) && time() > $payload['exp']) {
            return false;
        }

        return $payload;
    }
}
