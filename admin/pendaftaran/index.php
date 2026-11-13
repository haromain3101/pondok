<?php

declare(strict_types=1);

$pageTitle = 'Pendaftaran';
require dirname(__DIR__) . '/includes/header.php';

$filter = trim((string) ($_GET['status'] ?? ''));
$allowed = ['menunggu', 'diterima', 'ditolak'];

if ($filter !== '' && in_array($filter, $allowed, true)) {
    $stmt = $pdo->prepare('SELECT * FROM pendaftaran WHERE status = ? ORDER BY created_at DESC');
    $stmt->execute([$filter]);
    $items = $stmt->fetchAll();
} else {
    $filter = '';
    $items = $pdo->query('SELECT * FROM pendaftaran ORDER BY created_at DESC')->fetchAll();
}
?>

<div class="toolbar">
    <div class="actions">
        <a class="btn btn-secondary btn-sm <?= $filter === '' ? 'btn-primary' : '' ?>" href="<?= e(adminUrl('pendaftaran/')) ?>">Semua</a>
        <?php foreach ($allowed as $status): ?>
            <a class="btn btn-secondary btn-sm <?= $filter === $status ? 'btn-primary' : '' ?>" href="<?= e(adminUrl('pendaftaran/?status=' . $status)) ?>"><?= e(ucfirst($status)) ?></a>
        <?php endforeach; ?>
    </div>
</div>

<div class="panel">
    <?php if (!$items): ?>
        <div class="empty">Tidak ada data pendaftaran.</div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Jenjang</th>
                        <th>Wali / WA</th>
                        <th>Status</th>
                        <th>Masuk</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <strong><?= e($item['nama_lengkap']) ?></strong>
                                <div style="color:var(--ink-soft);font-size:0.85rem;"><?= e($item['jenis_kelamin']) ?></div>
                            </td>
                            <td><?= e($item['jenjang']) ?></td>
                            <td>
                                <?= e($item['nama_wali']) ?><br>
                                <a href="https://wa.me/<?= e(preg_replace('/[^0-9]/', '', $item['no_wa']) ?? '') ?>" target="_blank" rel="noopener"><?= e($item['no_wa']) ?></a>
                            </td>
                            <td><span class="badge badge-<?= e($item['status']) ?>"><?= e(ucfirst($item['status'])) ?></span></td>
                            <td><?= e(formatTanggal(substr((string) $item['created_at'], 0, 10))) ?></td>
                            <td>
                                <a class="btn btn-secondary btn-sm" href="<?= e(adminUrl('pendaftaran/detail.php?id=' . $item['id'])) ?>">Detail</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
