<?php

declare(strict_types=1);

$pageTitle = 'Agenda';
require dirname(__DIR__) . '/includes/header.php';

$items = $pdo->query('SELECT * FROM agenda ORDER BY tanggal_mulai ASC, id DESC')->fetchAll();
?>

<div class="toolbar">
    <p style="margin:0;color:var(--ink-soft);">Kelola jadwal kegiatan pondok.</p>
    <a class="btn btn-primary" href="<?= e(adminUrl('agenda/form.php')) ?>">Tambah agenda</a>
</div>

<div class="panel">
    <?php if (!$items): ?>
        <div class="empty">Belum ada agenda.</div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Tanggal</th>
                        <th>Lokasi</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><strong><?= e($item['judul']) ?></strong></td>
                            <td>
                                <?= e(formatTanggal($item['tanggal_mulai'])) ?>
                                <?php if (!empty($item['tanggal_selesai']) && $item['tanggal_selesai'] !== $item['tanggal_mulai']): ?>
                                    – <?= e(formatTanggal($item['tanggal_selesai'])) ?>
                                <?php endif; ?>
                            </td>
                            <td><?= e($item['lokasi']) ?></td>
                            <td>
                                <div class="actions">
                                    <a class="btn btn-secondary btn-sm" href="<?= e(adminUrl('agenda/form.php?id=' . $item['id'])) ?>">Edit</a>
                                    <form method="post" action="<?= e(adminUrl('agenda/delete.php')) ?>" onsubmit="return confirm('Hapus agenda ini?')">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                        <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                        <button class="btn btn-danger btn-sm" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
