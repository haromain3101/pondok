<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrf($_POST['csrf_token'] ?? null)) {
    setFlash('error', 'Permintaan tidak valid.');
    redirect(adminUrl('berita/'));
}

$id = (int) ($_POST['id'] ?? 0);
$stmt = $pdo->prepare('DELETE FROM berita WHERE id = ?');
$stmt->execute([$id]);
setFlash('success', 'Berita dihapus.');
redirect(adminUrl('berita/'));
