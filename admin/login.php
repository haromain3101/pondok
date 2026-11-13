<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/init.php';

if (currentAdmin()) {
    redirect(adminUrl());
}

$error = '';
$maxAttempts = 5;          // Maksimal 5 kali percobaan gagal
$lockoutSeconds = 180;      // Dibekukan selama 3 menit (180 detik)

// Periksa apakah pengguna sedang dalam status dibekukan (Lockout)
$isLocked = false;
$sisaDetik = 0;

if (!empty($_SESSION['login_lockout_until'])) {
    if (time() < $_SESSION['login_lockout_until']) {
        $isLocked = true;
        $sisaDetik = (int) ($_SESSION['login_lockout_until'] - time());
        $menit = ceil($sisaDetik / 60);
        $error = "Terlalu banyak percobaan gagal! Form login dibekukan demi keamanan. Silakan coba lagi dalam {$sisaDetik} detik ({$menit} menit).";
    } else {
        // Waktu pembekuan selesai, pulihkan status
        unset($_SESSION['login_lockout_until'], $_SESSION['login_attempts']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($isLocked) {
        // Abaikan permintaan jika form masih terkunci
    } elseif (!verifyCsrf($_POST['csrf_token'] ?? null)) {
        $error = 'Sesi tidak valid. Muat ulang halaman.';
    } else {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            $error = 'Username dan password wajib diisi.';
        } elseif (attemptLogin($pdo, $username, $password)) {
            // Berhasil login: reset hitungan percobaan gagal
            unset($_SESSION['login_attempts'], $_SESSION['login_lockout_until']);
            setFlash('success', 'Selamat datang kembali.');
            redirect(adminUrl());
        } else {
            // Gagal login: tambah hitungan percobaan
            $_SESSION['login_attempts'] = (int) ($_SESSION['login_attempts'] ?? 0) + 1;
            $sisaKesempatan = $maxAttempts - $_SESSION['login_attempts'];

            if ($sisaKesempatan <= 0) {
                // Mencapai batas maksimal: kunci form login
                $_SESSION['login_lockout_until'] = time() + $lockoutSeconds;
                $_SESSION['login_attempts'] = 0;
                $isLocked = true;
                $sisaDetik = $lockoutSeconds;
                $error = "Terlalu banyak percobaan gagal! Form login dibekukan selama 3 menit demi keamanan sistem.";
            } else {
                $error = "Username atau password salah! Sisa kesempatan mencoba: {$sisaKesempatan} kali.";
            }
        }
    }
}

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — Nurul Hikmah</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(url('assets/css/admin.css')) ?>">
</head>
<body class="login-page">
    <div class="login-card">
        <h1>Login Admin</h1>
        <p>Kelola berita, agenda, galeri, dan pendaftaran.</p>

        <?php if ($flash): ?>
            <div class="admin-alert admin-alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <div class="admin-alert admin-alert-error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= e(adminUrl('login.php')) ?>">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
            <div class="field">
                <label for="username">Username</label>
                <input id="username" name="username" type="text" required autofocus value="<?= e($_POST['username'] ?? '') ?>" <?= $isLocked ? 'disabled' : '' ?>>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required <?= $isLocked ? 'disabled' : '' ?>>
            </div>
            <?php if ($isLocked): ?>
                <button class="btn btn-secondary" type="button" disabled style="width:100%; cursor:not-allowed; opacity:0.7;">
                    Login Terkunci (Tunggu <?= e((string)$sisaDetik) ?> detik)
                </button>
            <?php else: ?>
                <button class="btn btn-primary" type="submit">Masuk</button>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
