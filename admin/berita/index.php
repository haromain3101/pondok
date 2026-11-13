<?php

declare(strict_types=1);

$pageTitle = 'Berita';
require dirname(__DIR__) . '/includes/header.php';

$items = $pdo->query('SELECT * FROM berita ORDER BY tanggal DESC, id DESC')->fetchAll();
?>

<div class="toolbar">
    <p style="margin:0;color:var(--ink-soft);">Kelola artikel dan pengumuman website.</p>
    <a class="btn btn-primary" href="<?= e(adminUrl('berita/form.php')) ?>">Tambah berita</a>
</div>

<div class="panel">
    <?php if (!$items): ?>
        <div class="empty">Belum ada berita.</div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Tanggal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <strong><?= e($item['judul']) ?></strong>
                                <div style="color:var(--ink-soft);font-size:0.85rem;"><?= e($item['slug']) ?></div>
                            </td>
                            <td><?= e($item['kategori']) ?></td>
                            <td><?= e(formatTanggal($item['tanggal'])) ?></td>
                            <td>
                                <div class="actions">
                                    <a class="btn btn-secondary btn-sm" href="<?= e(adminUrl('berita/form.php?id=' . $item['id'])) ?>">Edit</a>
                                    <form method="post" action="<?= e(adminUrl('berita/delete.php')) ?>" onsubmit="return confirm('Hapus berita ini?')">
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
