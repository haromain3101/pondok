<?php

// Proteksi Keamanan Siber: Blokir akses langsung melalui browser (HTTP/HTTPS)
// File ini hanya boleh dijalankan lewat terminal CLI oleh developer
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    header('Content-Type: text/html; charset=UTF-8');
    exit('<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden — Akses Ditolak</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background: #f8fafc; color: #0f172a; display: grid; place-items: center; min-height: 100vh; margin: 0; padding: 1rem; box-sizing: border-box; }
        .card { background: #ffffff; padding: 2.5rem; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); text-align: center; max-width: 480px; width: 100%; border: 1px solid #e2e8f0; }
        .badge { display: inline-block; background: #fee2e2; color: #b91c1c; font-weight: 700; padding: 6px 14px; border-radius: 999px; font-size: 0.85rem; margin-bottom: 1rem; letter-spacing: 0.05em; text-transform: uppercase; }
        h1 { color: #dc2626; font-size: 2.5rem; margin: 0 0 0.5rem; }
        p { color: #64748b; font-size: 1rem; line-height: 1.6; margin: 0; }
        .notice { margin-top: 1.5rem; padding: 0.75rem 1rem; background: #f1f5f9; border-radius: 8px; font-size: 0.85rem; color: #475569; }
    </style>
</head>
<body>
    <div class="card">
        <span class="badge">Keamanan Siber Aktif</span>
        <h1>403 Forbidden</h1>
        <p><strong>Akses Ditolak!</strong><br>File pengujian ini bersifat internal dan dilarang diakses melalui browser web demi keamanan data sistem.</p>
        <div class="notice">
            Status Keamanan: <strong>Dilindungi dari Information Disclosure</strong>
        </div>
    </div>
</body>
</html>');
}

require dirname(__DIR__) . '/config/database.php';

$count = (int) $pdo->query('SELECT COUNT(*) FROM admin')->fetchColumn();
echo "admin_count={$count}" . PHP_EOL;

if ($count > 0) {
    $admin = $pdo->query('SELECT username, nama FROM admin')->fetch();
    echo 'user=' . $admin['username'] . ' nama=' . $admin['nama'] . PHP_EOL;
    echo 'status=akun_admin_terdaftar' . PHP_EOL;
}
