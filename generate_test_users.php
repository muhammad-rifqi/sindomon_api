<?php
/**
 * Test user generator + JWT token factory.
 * Direct DB + manual JWT — no CI3 bootstrap needed.
 * Run: php generate_test_users.php
 */

$host = '127.0.0.1';
$user = 'root';
$pass = 'root';
$db   = 'sindomondb';
$jwt_secret = 'secret_key_yang_sangat_panjang_dan_aman';

$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
function jwt_encode_manual($payload, $secret) {
    $header = base64url_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $payload_enc = base64url_encode(json_encode($payload));
    $signature = base64url_encode(hash_hmac('sha256', $header . '.' . $payload_enc, $secret, true));
    return $header . '.' . $payload_enc . '.' . $signature;
}

// seed roles
$roles = [
    ['id' => 1, 'roles' => 'Administrator'],
    ['id' => 2, 'roles' => 'Superadmin'],
    ['id' => 3, 'roles' => 'Operator Polda'],
];
$stmt = $pdo->prepare("INSERT IGNORE INTO tbl_role (id, roles, created_at) VALUES (?, ?, NOW())");
foreach ($roles as $r) $stmt->execute([$r['id'], $r['roles']]);

// seed polda
$pdo->exec("INSERT IGNORE INTO tbl_polda (id, nama_polda, created_at) VALUES (12, 'Polda Jawa Barat', NOW())");

// wipe existing test users
$pdo->exec("DELETE FROM tbl_users WHERE username IN ('admin_pusat','operator_jabar','pimpinan_mabes')");

$users = [
    ['username' => 'admin_pusat',    'password' => password_hash('admin123', PASSWORD_DEFAULT),    'roles_id' => 1],
    ['username' => 'operator_jabar', 'password' => password_hash('operator123', PASSWORD_DEFAULT), 'roles_id' => 2],
    ['username' => 'pimpinan_mabes', 'password' => password_hash('pimpinan123', PASSWORD_DEFAULT), 'roles_id' => 3],
];

$pw_map = ['admin_pusat' => 'admin123', 'operator_jabar' => 'operator123', 'pimpinan_mabes' => 'pimpinan123'];
$insert = $pdo->prepare(
    "INSERT INTO tbl_users (username, password, roles_id, uuid, token, expired, created_at)
     VALUES (?, ?, ?, ?, ?, '365', NOW())"
);

foreach ($users as $u) {
    $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0x3fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
    $token_str = bin2hex(random_bytes(16));

    $insert->execute([$u['username'], $u['password'], $u['roles_id'], $uuid, $token_str]);
    $userId = (int) $pdo->lastInsertId();

    $payload = [
        'uid' => $userId, 'username' => $u['username'], 'role' => $u['roles_id'],
        'iat' => time(), 'exp' => time() + 365 * 24 * 3600,
    ];
    $jwt = jwt_encode_manual($payload, $jwt_secret);

    echo $u['username'] . '|' . $pw_map[$u['username']] . '|' . $u['roles_id'] . '|' . $jwt . "\n";
}
