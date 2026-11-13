<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/database.php';

$slug = trim((string) ($_GET['slug'] ?? ''));

$stmt = $pdo->prepare('SELECT * FROM berita WHERE slug = ? LIMIT 1');
$stmt->execute([$slug]);
$berita = $stmt->fetch();

if (!$berita) {
    http_response_code(404);
    $pageTitle = 'Berita tidak ditemukan';
    require __DIR__ . '/includes/header.php';
    echo '<section class="section"><div class="container"><div class="empty-state">Berita tidak ditemukan. <a href="' . e(url('berita.php')) . '">Kembali ke daftar berita</a></div></div></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $berita['judul'] . ' — Nurul Hikmah';
$pageDescription = $berita['ringkasan'];

require __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container">
        <article class="article reveal">
            <div class="card-meta" style="margin-bottom: 1rem;">
                <span class="pill"><?= e($berita['kategori']) ?></span>
                <span><?= e(formatTanggal($berita['tanggal'])) ?></span>
            </div>
            <h1 class="article-title" style="font-size: clamp(2rem, 4vw, 3.2rem); margin: 0 0 1.25rem;"><?= e($berita['judul']) ?></h1>
            <div class="article-hero">
                <img src="<?= e($berita['gambar']) ?>" alt="<?= e($berita['judul']) ?>">
            </div>
            <div class="article-body">
                <p><?= e($berita['isi']) ?></p>
            </div>
            <div class="cta-row">
                <a class="btn btn-outline" href="<?= e(url('berita.php')) ?>">Kembali ke berita</a>
            </div>
        </article>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
