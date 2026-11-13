<?php

declare(strict_types=1);

$pageTitle = 'Berita — Pondok Pesantren Nurul Hikmah';
$pageDescription = 'Berita dan pengumuman resmi Pondok Pesantren Nurul Hikmah.';

require __DIR__ . '/includes/header.php';

$beritaList = $pdo->query('SELECT * FROM berita ORDER BY tanggal DESC')->fetchAll();
?>

<section class="page-hero">
    <div class="container reveal">
        <span class="eyebrow">Berita</span>
        <h1>Kabar &amp; pengumuman</h1>
        <p>Informasi kegiatan, prestasi, dan kebijakan pondok untuk santri serta wali.</p>
    </div>
</section>

<section class="section" style="padding-top: 0;">
    <div class="container">
        <?php if (!$beritaList): ?>
            <div class="empty-state">Belum ada berita.</div>
        <?php else: ?>
            <div class="card-grid">
                <?php foreach ($beritaList as $berita): ?>
                    <a class="card reveal" href="<?= e(url('berita-detail.php')) ?>?slug=<?= e(urlencode($berita['slug'])) ?>">
                        <div class="card-media">
                            <img src="<?= e($berita['gambar']) ?>" alt="<?= e($berita['judul']) ?>">
                        </div>
                        <div class="card-body">
                            <div class="card-meta">
                                <span class="pill"><?= e($berita['kategori']) ?></span>
                                <span><?= e(formatTanggal($berita['tanggal'])) ?></span>
                            </div>
                            <h2 class="card-title"><?= e($berita['judul']) ?></h2>
                            <p><?= e($berita['ringkasan']) ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
