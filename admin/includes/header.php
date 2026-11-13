<?php

declare(strict_types=1);

require_once __DIR__ . '/init.php';
requireAdmin();

$admin = currentAdmin();
$pageTitle = $pageTitle ?? 'Dashboard Admin';
$flash = getFlash();
$pengaturan = getPengaturan($pdo);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> — Admin <?= e($pengaturan['nama_web'] ?? 'Nurul Hikmah') ?></title>
    <?php if (!empty($pengaturan['favicon'])): ?>
        <link rel="icon" href="<?= e(assetOrUploadUrl($pengaturan['favicon'])) ?>">
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(url('assets/css/admin.css')) ?>">
</head>
<body class="admin-body">
    <aside class="admin-sidebar">
        <div class="admin-brand">
            <?php if (!empty($pengaturan['logo_gambar']) && ($pengaturan['logo_tipe'] ?? 'inisial') === 'gambar'): ?>
                <img src="<?= e(assetOrUploadUrl($pengaturan['logo_gambar'])) ?>" alt="Logo" class="admin-brand-logo">
            <?php endif; ?>
            <div>
                <strong><?= e($pengaturan['nama_web'] ?? 'Nurul Hikmah') ?></strong>
                <span>Panel Admin</span>
            </div>
        </div>
        <nav class="admin-nav">
            <a class="<?= adminActive('dashboard') ?>" href="<?= e(adminUrl()) ?>">Dashboard</a>
            <a class="<?= adminActive('profil') ?>" href="<?= e(adminUrl('profil/')) ?>">Profil</a>
            <a class="<?= adminActive('berita') ?>" href="<?= e(adminUrl('berita/')) ?>">Berita</a>
            <a class="<?= adminActive('agenda') ?>" href="<?= e(adminUrl('agenda/')) ?>">Agenda</a>
            <a class="<?= adminActive('galeri') ?>" href="<?= e(adminUrl('galeri/')) ?>">Galeri</a>
            <a class="<?= adminActive('pendaftaran') ?>" href="<?= e(adminUrl('pendaftaran/')) ?>">Pendaftaran</a>
            <a class="<?= adminActive('pengaturan') ?>" href="<?= e(adminUrl('pengaturan/')) ?>">Pengaturan</a>
        </nav>
        <div class="admin-sidebar-foot">
            <a href="<?= e(url()) ?>" target="_blank" rel="noopener">Lihat website</a>
            <a href="<?= e(adminUrl('logout.php')) ?>">Keluar</a>
        </div>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <div>
                <p class="eyebrow">Administrasi</p>
                <h1><?= e($pageTitle) ?></h1>
            </div>
            <div class="admin-user">
                <span><?= e($admin['nama'] ?? 'Admin') ?></span>
            </div>
        </header>

        <div class="admin-content">
            <?php if ($flash): ?>
                <div class="admin-alert admin-alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
            <?php endif; ?>
