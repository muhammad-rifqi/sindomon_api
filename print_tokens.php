<?php
$json = json_decode(file_get_contents(__DIR__ . '/test_jwts.json'), true);
foreach ($json as $u) {
    echo "Username: {$u['username']}\n";
    echo "Password: {$u['password']}\n";
    echo "Role ID:  {$u['role_id']}\n";
    echo "JWT:\n";
    // Split token into small chunks so terminal doesn't truncate
    echo chunk_split($u['jwt'], 48, "\n");
    echo "\n";
}
