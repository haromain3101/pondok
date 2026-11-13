<?php

declare(strict_types=1);

$pageTitle = 'Dashboard';
require __DIR__ . '/includes/header.php';

$counts = [
    'berita' => (int) $pdo->query('SELECT COUNT(*) FROM berita')->fetchColumn(),
    'agenda' => (int) $pdo->query('SELECT COUNT(*) FROM agenda')->fetchColumn(),
    'galeri' => (int) $pdo->query('SELECT COUNT(*) FROM galeri')->fetchColumn(),
    'pendaftaran' => (int) $pdo->query('SELECT COUNT(*) FROM pendaftaran')->fetchColumn(),
    'menunggu' => (int) $pdo->query("SELECT COUNT(*) FROM pendaftaran WHERE status = 'menunggu'")->fetchColumn(),
];

$pendaftarBaru = $pdo->query('SELECT * FROM pendaftaran ORDER BY created_at DESC LIMIT 5')->fetchAll();
$beritaTerbaru = $pdo->query('SELECT * FROM berita ORDER BY tanggal DESC LIMIT 5')->fetchAll();
?>

<div class="stat-grid">
    <div class="stat-card"><strong><?= $counts['berita'] ?></strong><span>Berita</span></div>
    <div class="stat-card"><strong><?= $counts['agenda'] ?></strong><span>Agenda</span></div>
    <div class="stat-card"><strong><?= $counts['galeri'] ?></strong><span>Foto galeri</span></div>
    <div class="stat-card"><strong><?= $counts['menunggu'] ?></strong><span>Pendaftaran menunggu</span></div>
</div>

<div class="panel" style="margin-bottom: 1rem;">
    <div class="panel-head">
        <h2>Pendaftaran terbaru</h2>
        <a class="btn btn-secondary btn-sm" href="<?= e(adminUrl('pendaftaran/')) ?>">Lihat semua</a>
    </div>
    <?php if (!$pendaftarBaru): ?>
        <div class="empty">Belum ada data pendaftaran.</div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Jenjang</th>
                        <th>WhatsApp</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendaftarBaru as $row): ?>
                        <tr>
                            <td><?= e($row['nama_lengkap']) ?></td>
                            <td><?= e($row['jenjang']) ?></td>
                            <td><?= e($row['no_wa']) ?></td>
                            <td><span class="badge badge-<?= e($row['status']) ?>"><?= e(ucfirst($row['status'])) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="panel">
    <div class="panel-head">
        <h2>Berita terbaru</h2>
        <a class="btn btn-secondary btn-sm" href="<?= e(adminUrl('berita/')) ?>">Kelola berita</a>
    </div>
    <?php if (!$beritaTerbaru): ?>
        <div class="empty">Belum ada berita.</div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($beritaTerbaru as $row): ?>
                        <tr>
                            <td><?= e($row['judul']) ?></td>
                            <td><?= e($row['kategori']) ?></td>
                            <td><?= e(formatTanggal($row['tanggal'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
