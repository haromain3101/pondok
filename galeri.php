<?php

declare(strict_types=1);

$pageTitle = 'Galeri — Pondok Pesantren Nurul Hikmah';
$pageDescription = 'Dokumentasi kegiatan, fasilitas, dan kehidupan santri Pondok Pesantren Nurul Hikmah.';

require __DIR__ . '/includes/header.php';

$galeri = $pdo->query('SELECT * FROM galeri ORDER BY id DESC')->fetchAll();
$kategori = $pdo->query('SELECT DISTINCT kategori FROM galeri ORDER BY kategori')->fetchAll(PDO::FETCH_COLUMN);
?>

<section class="page-hero">
    <div class="container reveal">
        <span class="eyebrow">Galeri</span>
        <h1>Jejak kegiatan pondok</h1>
        <p>Potret ibadah, belajar, kebersamaan, dan fasilitas yang menjadi rumah kedua para santri.</p>
    </div>
</section>

<section class="section" style="padding-top: 0;">
    <div class="container">
        <div class="gallery-filters reveal">
            <button class="filter-btn is-active" type="button" data-filter="Semua">Semua</button>
            <?php foreach ($kategori as $kat): ?>
                <button class="filter-btn" type="button" data-filter="<?= e($kat) ?>"><?= e($kat) ?></button>
            <?php endforeach; ?>
        </div>

        <?php if (!$galeri): ?>
            <div class="empty-state">Belum ada foto di galeri.</div>
        <?php else: ?>
            <div class="gallery-grid">
                <?php foreach ($galeri as $item): ?>
                    <figure class="gallery-item reveal" data-category="<?= e($item['kategori']) ?>">
                        <img src="<?= e($item['gambar']) ?>" alt="<?= e($item['judul']) ?>" loading="lazy">
                        <figcaption><?= e($item['judul']) ?></figcaption>
                    </figure>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
