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
