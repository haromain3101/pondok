<?php

declare(strict_types=1);

$pageTitle = 'Profil — Pondok Pesantren Nurul Hikmah';
$pageDescription = 'Sejarah, visi misi, dan nilai-nilai Pondok Pesantren Nurul Hikmah.';

require __DIR__ . '/includes/header.php';

$profil = getProfil($pdo);
$programs = getProfilProgram($pdo);

if (!$profil) {
    echo '<section class="section"><div class="container"><div class="empty-state">Konten profil belum tersedia.</div></div></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}
?>

<section class="page-hero">
    <div class="container reveal">
        <span class="eyebrow">Profil</span>
        <h1><?= e($profil['hero_judul']) ?></h1>
        <p><?= e($profil['hero_deskripsi']) ?></p>
    </div>
</section>

<section class="section" style="padding-top: 0;">
    <div class="container split">
        <div class="reveal">
            <div class="section-head">
                <span class="eyebrow"><?= e($profil['sejarah_eyebrow']) ?></span>
                <h2><?= e($profil['sejarah_judul']) ?></h2>
                <div class="prose-text lead-text"><?= renderParagraphs($profil['sejarah_ringkas']) ?></div>
            </div>
            <div class="prose-text" style="color: var(--ink-soft); margin-top: 1rem;">
                <?= renderParagraphs($profil['sejarah_lengkap']) ?>
            </div>
        </div>
        <div class="split-media reveal">
            <img src="<?= e($profil['sejarah_gambar']) ?>" alt="<?= e($profil['sejarah_judul']) ?>">
        </div>
    </div>
</section>

<section class="section" style="padding-top: 0;">
    <div class="container">
        <div class="profil-grid">
            <article class="profil-block reveal">
                <h3>Visi</h3>
                <div class="prose-text"><?= renderParagraphs($profil['visi']) ?></div>
            </article>
            <article class="profil-block reveal">
                <h3>Misi</h3>
                <div class="prose-text"><?= renderParagraphs($profil['misi']) ?></div>
            </article>
            <article class="profil-block reveal">
                <h3>Motto</h3>
                <p class="arabic" lang="ar" style="font-size: 1.35rem; color: var(--forest); margin-bottom: 0.5rem;"><?= e($profil['motto_arab']) ?></p>
                <div class="prose-text"><?= renderParagraphs($profil['motto_teks']) ?></div>
            </article>
        </div>
    </div>
</section>

<section class="section band">
    <div class="container">
        <div class="section-head reveal">
            <span class="eyebrow"><?= e($profil['program_eyebrow']) ?></span>
            <h2><?= e($profil['program_judul']) ?></h2>
            <p><?= e($profil['program_deskripsi']) ?></p>
        </div>
        <?php if ($programs): ?>
            <div class="card-grid">
                <?php foreach ($programs as $program): ?>
                    <article class="card reveal">
                        <div class="card-body">
                            <span class="pill"><?= e($program['label']) ?></span>
                            <h3 class="card-title"><?= e($program['judul']) ?></h3>
                            <p><?= e($program['deskripsi']) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="container reveal">
        <div class="section-head">
            <span class="eyebrow">Fasilitas</span>
            <h2><?= e($profil['fasilitas_judul']) ?></h2>
            <p><?= e($profil['fasilitas_deskripsi']) ?></p>
        </div>
        <div class="cta-row">
            <a class="btn btn-solid" href="<?= e(url('galeri.php')) ?>">Lihat galeri</a>
            <a class="btn btn-outline" href="<?= e(url('pendaftaran.php')) ?>">Daftar sekarang</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
