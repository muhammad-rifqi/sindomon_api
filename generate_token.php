<?php
/**
 * Temporary JWT Token Generator for Endpoint 7.3 testing.
 *
 * Usage (CLI):
 *   php generate_token.php
 *
 * Usage (Browser):
 *   http://sindomon-api.test/generate_token.php
 *
 * DELETE THIS FILE AFTER TESTING.
 */

// Must match application/config/config.php
$secret = 'your_placeholder_secret_key';

// ── Helper functions ──
function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function generate_jwt($payload, $secret) {
    $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);

    $segments = array();
    $segments[] = base64url_encode($header);
    $segments[] = base64url_encode(json_encode($payload));

    $signing_input = implode('.', $segments);
    $signature = hash_hmac('sha256', $signing_input, $secret, true);
    $segments[] = base64url_encode($signature);

    return implode('.', $segments);
}

// ── Token 1: role_id=2 (as per your blueprint request — will 403) ──
$token_2 = generate_jwt([
    'user_id'  => 1,
    'role_id'  => 2,
    'polda_id' => 12,
], $secret);

// ── Token 2: role_id=3 (matches our controller — will 200 on happy path) ──
$token_3 = generate_jwt([
    'user_id'  => 1,
    'role_id'  => 3,
    'polda_id' => 12,
], $secret);

echo "========================================\n";
echo "  TOKEN 1 (role_id=2) — WILL RETURN 403\n";
echo "========================================\n";
echo $token_2 . "\n\n";

echo "========================================\n";
echo "  TOKEN 2 (role_id=3) — HAPPY PATH\n";
echo "========================================\n";
echo $token_3 . "\n\n";

// Verify roundtrip
$decoded = json_decode(base64url_decode(explode('.', $token_3)[1]), true);
echo "Token 2 payload verification:\n";
echo json_encode($decoded, JSON_PRETTY_PRINT) . "\n";

// ── base64url_decode helper ──
function base64url_decode($input) {
    $remainder = strlen($input) % 4;
    if ($remainder) {
        $input .= str_repeat('=', 4 - $remainder);
    }
    return base64_decode(strtr($input, '-_', '+/'));
}
