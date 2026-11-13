<?php

declare(strict_types=1);

$pageTitle = 'Agenda — Pondok Pesantren Nurul Hikmah';
$pageDescription = 'Jadwal kegiatan dan agenda resmi Pondok Pesantren Nurul Hikmah.';

require __DIR__ . '/includes/header.php';

$agendaList = $pdo->query('SELECT * FROM agenda ORDER BY tanggal_mulai ASC')->fetchAll();
?>

<section class="page-hero">
    <div class="container reveal">
        <span class="eyebrow">Agenda</span>
        <h1>Kalender kegiatan pondok</h1>
        <p>Rangkaian kegiatan pendidikan, ibadah, dan silaturahmi yang akan datang.</p>
    </div>
</section>

<section class="section" style="padding-top: 0;">
    <div class="container">
        <?php if (!$agendaList): ?>
            <div class="empty-state">Belum ada agenda.</div>
        <?php else: ?>
            <div class="agenda-list">
                <?php foreach ($agendaList as $item): ?>
                    <article class="agenda-item reveal">
                        <div class="agenda-date">
                            <strong><?= e(date('d', strtotime($item['tanggal_mulai']))) ?></strong>
                            <span><?= e(formatTanggal($item['tanggal_mulai'], 'M Y')) ?></span>
                        </div>
                        <div>
                            <h2 style="font-family: var(--font-display); font-size: 1.45rem; margin: 0 0 0.45rem;"><?= e($item['judul']) ?></h2>
                            <p style="color: var(--ink-soft); margin: 0 0 0.75rem;"><?= e($item['deskripsi']) ?></p>
                            <div class="agenda-meta">
                                <span><?= e($item['lokasi']) ?></span>
                                <span>
                                    <?= e(formatTanggal($item['tanggal_mulai'])) ?>
                                    <?php if (!empty($item['tanggal_selesai']) && $item['tanggal_selesai'] !== $item['tanggal_mulai']): ?>
                                        – <?= e(formatTanggal($item['tanggal_selesai'])) ?>
                                    <?php endif; ?>
                                </span>
                                <span><?= e($item['waktu'] ?: 'Menyesuaikan') ?></span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
