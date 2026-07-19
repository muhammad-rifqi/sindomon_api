#!/usr/bin/env php
<?php
$host = getenv('DB_HOST') ?: '127.0.0.1';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: 'root';
$db   = getenv('DB_NAME') ?: 'sindomondb';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    fwrite(STDERR, "DB connection failed: " . $e->getMessage() . "\n");
    exit(1);
}

$pdo->exec("DELETE FROM tbl_proses_hukum WHERE personil_id IN (SELECT personil_id FROM (SELECT personil_id FROM tbl_personil WHERE nrp LIKE '88______') t)");
$pdo->exec("DELETE FROM tbl_personil WHERE nrp LIKE '88______'");
$pdo->exec("DELETE FROM tbl_users WHERE username = 'operator_test'");

$hash = password_hash('operator123', PASSWORD_DEFAULT);
$pdo->exec("INSERT INTO tbl_users (username, password, roles_id, polda_id, uuid, token, expired, created_at)
    VALUES ('operator_test', " . $pdo->quote($hash) . ", 2, 12, UUID(), 'testtoken', '30', NOW())");
echo "  test user 'operator_test' seeded\n";

$pdo->exec("INSERT IGNORE INTO tbl_pangkat (pangkat_id, nama_pangkat) VALUES (1, 'BRIPDA')");
$pdo->exec("INSERT IGNORE INTO tbl_pangkat (pangkat_id, nama_pangkat) VALUES (2, 'BRIPTU')");
echo "  tbl_pangkat seeded\n";

$pdo->exec("INSERT IGNORE INTO tbl_jabatan (jabatan_id, nama_jabatan, formasi_ideal, parent_id)
    VALUES (1, 'Staff', 10, NULL)");
$pdo->exec("INSERT IGNORE INTO tbl_jabatan (jabatan_id, nama_jabatan, formasi_ideal, parent_id)
    VALUES (2, 'Kanit', 2, 1)");
echo "  tbl_jabatan seeded\n";

$pdo->exec("INSERT IGNORE INTO tbl_personil (personil_id, nrp, nama_lengkap, pangkat_id, jabatan_id, status_aktif, polda_id, polres_id)
    VALUES ('00000000-0000-0000-0000-00000000dead', '99999999', 'TRAP_PERSONIL_POLDA15', 1, 1, 'Aktif', 15, NULL)");
echo "  cross-region trap personil seeded (polda_id=15)\n";

echo "\nSeed complete.\n";
