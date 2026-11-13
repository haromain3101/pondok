<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
requireAdmin();

$id = (int) ($_GET['id'] ?? 0);
$item = null;

if ($id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM galeri WHERE id = ?');
    $stmt->execute([$id]);
    $item = $stmt->fetch();
    if (!$item) {
        setFlash('error', 'Foto tidak ditemukan.');
        redirect(adminUrl('galeri/'));
    }
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Sesi tidak valid.';
    } else {
        $judul = trim((string) ($_POST['judul'] ?? ''));
        $kategori = trim((string) ($_POST['kategori'] ?? ''));
        $gambar = trim((string) ($_POST['gambar'] ?? ''));

        if ($judul === '' || $kategori === '' || $gambar === '') {
            $errors[] = 'Judul, kategori, dan URL gambar wajib diisi.';
        }

        if (!$errors) {
            if ($item) {
                $stmt = $pdo->prepare('UPDATE galeri SET judul=?, kategori=?, gambar=? WHERE id=?');
                $stmt->execute([$judul, $kategori, $gambar, $id]);
                setFlash('success', 'Foto berhasil diperbarui.');
            } else {
                $stmt = $pdo->prepare('INSERT INTO galeri (judul, kategori, gambar) VALUES (?, ?, ?)');
                $stmt->execute([$judul, $kategori, $gambar]);
                setFlash('success', 'Foto berhasil ditambahkan.');
            }
            redirect(adminUrl('galeri/'));
        }

        $item = compact('judul', 'kategori', 'gambar');
    }
}

$pageTitle = $item && $id ? 'Edit foto' : 'Tambah foto';
require dirname(__DIR__) . '/includes/header.php';
?>

<?php if ($errors): ?>
    <div class="admin-alert admin-alert-error"><?= e(implode(' ', $errors)) ?></div>
<?php endif; ?>

<div class="panel">
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <div class="form-grid">
            <div class="field full">
                <label for="judul">Judul</label>
                <input id="judul" name="judul" type="text" required value="<?= e($item['judul'] ?? '') ?>">
            </div>
            <div class="field">
                <label for="kategori">Kategori</label>
                <input id="kategori" name="kategori" type="text" required list="kategori-list" value="<?= e($item['kategori'] ?? '') ?>">
                <datalist id="kategori-list">
                    <option value="Ibadah">
                    <option value="Pendidikan">
                    <option value="Tahfiz">
                    <option value="Kegiatan">
                    <option value="Prestasi">
                    <option value="Fasilitas">
                </datalist>
            </div>
            <div class="field">
                <label for="gambar">URL gambar</label>
                <input id="gambar" name="gambar" type="url" required placeholder="https://..." value="<?= e($item['gambar'] ?? '') ?>">
            </div>
        </div>
        <div class="toolbar" style="margin-top:1rem;margin-bottom:0;">
            <a class="btn btn-secondary" href="<?= e(adminUrl('galeri/')) ?>">Batal</a>
            <button class="btn btn-primary" type="submit">Simpan</button>
        </div>
    </form>
</div>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
