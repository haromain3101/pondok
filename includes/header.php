<?php

declare(strict_types=1);

require_once __DIR__ . '/functions.php';
require_once dirname(__DIR__) . '/config/database.php';

$pengaturan = getPengaturan($pdo);
$pageTitle = $pageTitle ?? siteName($pdo);
$pageDescription = $pageDescription ?? siteTagline($pdo);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($pageDescription) ?>">
    <?php if (!empty($pengaturan['favicon'])): ?>
        <link rel="icon" href="<?= e(assetOrUploadUrl($pengaturan['favicon'])) ?>">
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Fraunces:opsz,wght@9..144,500;9..144,700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
</head>
<body>
    <a class="skip-link" href="#konten">Lewati ke konten</a>

    <header class="site-header" data-header>
        <div class="container header-inner">
            <a class="brand" href="<?= e(url()) ?>">
                <?php if (!empty($pengaturan['logo_gambar']) && ($pengaturan['logo_tipe'] ?? 'inisial') === 'gambar'): ?>
                    <img class="brand-logo" src="<?= e(assetOrUploadUrl($pengaturan['logo_gambar'])) ?>" alt="<?= e($pengaturan['nama_web'] ?? 'Logo') ?>">
                <?php else: ?>
                    <span class="brand-mark" aria-hidden="true"><?= e($pengaturan['inisial_teks'] ?: 'ن ه') ?></span>
                <?php endif; ?>
                <span class="brand-text">
                    <strong><?= e($pengaturan['nama_web'] ?? 'Nurul Hikmah') ?></strong>
                    <small><?= e($pengaturan['sub_nama'] ?? 'Pondok Pesantren') ?></small>
                </span>
            </a>

            <button class="nav-toggle" type="button" aria-label="Buka menu" aria-expanded="false" data-nav-toggle>
                <span></span><span></span>
            </button>

            <nav class="site-nav" data-nav>
                <a class="<?= isActive('index.php') ?>" href="<?= e(url()) ?>">Beranda</a>
                <a class="<?= isActive('profil.php') ?>" href="<?= e(url('profil.php')) ?>">Profil</a>
                <a class="<?= isActive('galeri.php') ?>" href="<?= e(url('galeri.php')) ?>">Galeri</a>
                <a class="<?= isActive('berita.php') ?: isActive('berita-detail.php') ?>" href="<?= e(url('berita.php')) ?>">Berita</a>
                <a class="<?= isActive('agenda.php') ?>" href="<?= e(url('agenda.php')) ?>">Agenda</a>
                <a class="nav-cta <?= isActive('pendaftaran.php') ?>" href="<?= e(url('pendaftaran.php')) ?>">Pendaftaran</a>
            </nav>
        </div>
    </header>

    <main id="konten">
