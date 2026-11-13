<?php

declare(strict_types=1);

$pageTitle = 'Galeri';
require dirname(__DIR__) . '/includes/header.php';

$items = $pdo->query('SELECT * FROM galeri ORDER BY id DESC')->fetchAll();
?>

<div class="toolbar">
    <p style="margin:0;color:var(--ink-soft);">Kelola foto kegiatan dan fasilitas.</p>
    <a class="btn btn-primary" href="<?= e(adminUrl('galeri/form.php')) ?>">Tambah foto</a>
</div>

<div class="panel">
    <?php if (!$items): ?>
        <div class="empty">Belum ada foto galeri.</div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <?php if ($item['gambar']): ?>
                                    <img class="thumb" src="<?= e($item['gambar']) ?>" alt="">
                                <?php endif; ?>
                            </td>
                            <td><strong><?= e($item['judul']) ?></strong></td>
                            <td><?= e($item['kategori']) ?></td>
                            <td>
                                <div class="actions">
                                    <a class="btn btn-secondary btn-sm" href="<?= e(adminUrl('galeri/form.php?id=' . $item['id'])) ?>">Edit</a>
                                    <form method="post" action="<?= e(adminUrl('galeri/delete.php')) ?>" onsubmit="return confirm('Hapus foto ini?')">
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
