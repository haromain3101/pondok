<?php

require_once __DIR__ . '/security-guard.php';

$cookie = tempnam(sys_get_temp_dir(), 'pondok');

function request(string $url, string $cookie, ?array $post = null): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEJAR => $cookie,
        CURLOPT_COOKIEFILE => $cookie,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_HEADER => true,
    ]);

    if ($post !== null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    }

    $raw = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ['code' => $code, 'raw' => (string) $raw];
}

$loginPage = request('http://localhost:8080/admin/login.php', $cookie);
preg_match('/name="csrf_token" value="([^"]+)"/', $loginPage['raw'], $m);
$token = $m[1] ?? '';

if ($token === '') {
    echo "FAIL: csrf token not found\n";
    exit(1);
}

$login = request('http://localhost:8080/admin/login.php', $cookie, [
    'csrf_token' => $token,
    'username' => 'admin',
    'password' => 'nurulhikmah2026',
]);

$dashboard = request('http://localhost:8080/admin/index.php', $cookie);
$ok = str_contains($dashboard['raw'], 'Dashboard') && str_contains($dashboard['raw'], 'Pendaftaran menunggu');

echo 'login_status=' . $login['code'] . PHP_EOL;
echo 'dashboard_status=' . $dashboard['code'] . PHP_EOL;
echo $ok ? "LOGIN_OK\n" : "LOGIN_FAIL\n";

@unlink($cookie);
