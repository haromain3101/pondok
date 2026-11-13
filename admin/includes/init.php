<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    session_start();
}

// HTTP Security Headers untuk melindungi panel admin
if (!headers_sent()) {
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

require_once dirname(__DIR__, 2) . '/includes/functions.php';
require_once dirname(__DIR__, 2) . '/config/database.php';

function currentAdmin(): ?array
{
    return $_SESSION['admin'] ?? null;
}

function requireAdmin(): void
{
    if (!currentAdmin()) {
        setFlash('error', 'Silakan login terlebih dahulu.');
        redirect(adminUrl('login.php'));
    }
}

function attemptLogin(PDO $pdo, string $username, string $password): bool
{
    $stmt = $pdo->prepare('SELECT * FROM admin WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if (!$admin || !password_verify($password, $admin['password'])) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['admin'] = [
        'id' => (int) $admin['id'],
        'username' => $admin['username'],
        'nama' => $admin['nama'],
    ];

    return true;
}

function logoutAdmin(): void
{
    unset($_SESSION['admin']);
    session_regenerate_id(true);
}

function adminActive(string $section): string
{
    $uri = $_SERVER['SCRIPT_NAME'] ?? '';
    return str_contains($uri, '/admin/' . $section) || ($section === 'dashboard' && str_ends_with($uri, '/admin/index.php'))
        ? 'is-active'
        : '';
}
