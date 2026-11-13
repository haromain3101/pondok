<?php

declare(strict_types=1);

$pageTitle = 'Pondok Pesantren Nurul Hikmah';
$pageDescription = 'Website resmi Pondok Pesantren Nurul Hikmah — profil, galeri, berita, agenda, dan pendaftaran santri baru.';

require __DIR__ . '/includes/header.php';

$profil = getProfil($pdo);
$beritaTerbaru = $pdo->query('SELECT * FROM berita ORDER BY tanggal DESC LIMIT 3')->fetchAll();
$agendaTerdekat = $pdo->query('SELECT * FROM agenda ORDER BY tanggal_mulai ASC LIMIT 3')->fetchAll();
?>

<section class="hero">
    <div class="hero-media" aria-hidden="true">
        <img src="https://images.unsplash.com/photo-1564769625905-50e93615e769?w=1800&q=80" alt="">
    </div>
    <div class="container hero-content">
        <p class="hero-arabic" lang="ar">نُورٌ عَلَى نُورٍ</p>
        <h1 class="hero-brand"><?= e($pengaturan['nama_web'] ?? 'Nurul Hikmah') ?></h1>
        <p class="hero-lead"><?= e($profil['hero_deskripsi'] ?? $pengaturan['tagline'] ?? 'Membentuk generasi berilmu, berakhlak, dan mandiri melalui pendidikan pesantren yang kokoh dalam tradisi dan terbuka pada kemajuan.') ?></p>
        <div class="hero-actions">
            <a class="btn btn-primary" href="<?= e(url('pendaftaran.php')) ?>">Daftar Santri Baru</a>
            <a class="btn btn-ghost" href="<?= e(url('profil.php')) ?>">Kenali Pondok</a>
        </div>
    </div>
</section>

<section class="section" style="padding-top: 0;">
    <div class="container">
        <div class="stats reveal">
            <div class="stat"><strong>32+</strong><span>Tahun berkhidmat</span></div>
            <div class="stat"><strong>850+</strong><span>Santri aktif</span></div>
            <div class="stat"><strong>45</strong><span>Pengajar &amp; pembina</span></div>
            <div class="stat"><strong>12</strong><span>Program unggulan</span></div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container split">
        <div class="split-media reveal">
            <img src="<?= e($profil['sejarah_gambar'] ?? 'https://images.unsplash.com/photo-1585036156171-435537cbd1d4?w=1200&q=80') ?>" alt="<?= e($profil['sejarah_judul'] ?? 'Suasana ibadah di lingkungan pondok') ?>">
        </div>
        <div class="reveal">
            <div class="section-head" style="margin-bottom: 1rem;">
                <span class="eyebrow"><?= e($profil['sejarah_eyebrow'] ?? 'Profil singkat') ?></span>
                <h2><?= e($profil['sejarah_judul'] ?? 'Rumah ilmu yang menumbuhkan adab') ?></h2>
                <p><?= e($profil['sejarah_ringkas'] ?? 'Sejak berdiri, Nurul Hikmah mendidik santri dengan keseimbangan antara tahfiz, kitab kuning, ilmu umum, dan pembinaan karakter.') ?></p>
            </div>
            <ul class="feature-list">
                <li>Program Tahfiz Al-Qur'an dengan target bertahap dan murojaah harian.</li>
                <li>Kajian kitab kuning dan bahasa Arab sebagai fondasi keilmuan.</li>
                <li>Pendidikan formal MTs &amp; MA terintegrasi dengan kehidupan asrama.</li>
                <li>Lingkungan yang aman, disiplin, dan penuh kebersamaan.</li>
            </ul>
            <a class="btn btn-solid" href="<?= e(url('profil.php')) ?>">Selengkapnya</a>
        </div>
    </div>
</section>

<section class="section band">
    <div class="container">
        <div class="section-head reveal">
            <span class="eyebrow">Agenda</span>
            <h2>Kegiatan yang akan datang</h2>
            <p>Ikuti jadwal penting pondok agar tidak terlewat moment kebersamaan dan pembelajaran.</p>
        </div>
        <div class="agenda-list">
            <?php foreach ($agendaTerdekat as $item): ?>
                <article class="agenda-item reveal">
                    <div class="agenda-date">
                        <strong><?= e(date('d', strtotime($item['tanggal_mulai']))) ?></strong>
                        <span><?= e(formatTanggal($item['tanggal_mulai'], 'M Y')) ?></span>
                    </div>
                    <div>
                        <h3><?= e($item['judul']) ?></h3>
                        <p><?= e($item['deskripsi']) ?></p>
                        <div class="agenda-meta">
                            <span><?= e($item['lokasi']) ?></span>
                            <span><?= e($item['waktu'] ?: 'Menyesuaikan') ?></span>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="cta-row reveal">
            <a class="btn btn-primary" href="<?= e(url('agenda.php')) ?>">Lihat semua agenda</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head reveal">
            <span class="eyebrow">Berita</span>
            <h2>Kabar dari pondok</h2>
            <p>Update kegiatan, prestasi santri, dan pengumuman resmi.</p>
        </div>
        <div class="card-grid">
            <?php foreach ($beritaTerbaru as $berita): ?>
                <a class="card reveal" href="<?= e(url('berita-detail.php')) ?>?slug=<?= e(urlencode($berita['slug'])) ?>">
                    <div class="card-media">
                        <img src="<?= e($berita['gambar']) ?>" alt="<?= e($berita['judul']) ?>">
                    </div>
                    <div class="card-body">
                        <div class="card-meta">
                            <span class="pill"><?= e($berita['kategori']) ?></span>
                            <span><?= e(formatTanggal($berita['tanggal'])) ?></span>
                        </div>
                        <h3 class="card-title"><?= e($berita['judul']) ?></h3>
                        <p><?= e($berita['ringkasan']) ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        <div class="cta-row reveal">
            <a class="btn btn-outline" href="<?= e(url('berita.php')) ?>">Semua berita</a>
            <a class="btn btn-solid" href="<?= e(url('galeri.php')) ?>">Jelajahi galeri</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
