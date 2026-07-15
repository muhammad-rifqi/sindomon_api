<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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