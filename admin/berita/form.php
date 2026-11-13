<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
requireAdmin();

$id = (int) ($_GET['id'] ?? 0);
$item = null;

if ($id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM berita WHERE id = ?');
    $stmt->execute([$id]);
    $item = $stmt->fetch();
    if (!$item) {
        setFlash('error', 'Berita tidak ditemukan.');
        redirect(adminUrl('berita/'));
    }
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Sesi tidak valid.';
    } else {
        $judul = trim((string) ($_POST['judul'] ?? ''));
        $slug = trim((string) ($_POST['slug'] ?? ''));
        $ringkasan = trim((string) ($_POST['ringkasan'] ?? ''));
        $isi = trim((string) ($_POST['isi'] ?? ''));
        $gambar = trim((string) ($_POST['gambar'] ?? ''));
        $kategori = trim((string) ($_POST['kategori'] ?? 'Umum'));
        $tanggal = trim((string) ($_POST['tanggal'] ?? ''));

        if ($judul === '') {
            $errors[] = 'Judul wajib diisi.';
        }
        if ($ringkasan === '') {
            $errors[] = 'Ringkasan wajib diisi.';
        }
        if ($isi === '') {
            $errors[] = 'Isi berita wajib diisi.';
        }
        if ($tanggal === '') {
            $errors[] = 'Tanggal wajib diisi.';
        }

        $slug = $slug !== '' ? slugify($slug) : slugify($judul);

        if (!$errors) {
            if ($item) {
                $stmt = $pdo->prepare('UPDATE berita SET judul=?, slug=?, ringkasan=?, isi=?, gambar=?, kategori=?, tanggal=? WHERE id=?');
                $stmt->execute([$judul, $slug, $ringkasan, $isi, $gambar, $kategori, $tanggal, $id]);
                setFlash('success', 'Berita berhasil diperbarui.');
            } else {
                $stmt = $pdo->prepare('INSERT INTO berita (judul, slug, ringkasan, isi, gambar, kategori, tanggal) VALUES (?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$judul, $slug, $ringkasan, $isi, $gambar, $kategori, $tanggal]);
                setFlash('success', 'Berita berhasil ditambahkan.');
            }
            redirect(adminUrl('berita/'));
        }

        $item = [
            'judul' => $judul,
            'slug' => $slug,
            'ringkasan' => $ringkasan,
            'isi' => $isi,
            'gambar' => $gambar,
            'kategori' => $kategori,
            'tanggal' => $tanggal,
        ];
    }
}

$pageTitle = $item && $id ? 'Edit berita' : 'Tambah berita';
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
                <label for="slug">Slug (opsional)</label>
                <input id="slug" name="slug" type="text" value="<?= e($item['slug'] ?? '') ?>">
            </div>
            <div class="field">
                <label for="kategori">Kategori</label>
                <input id="kategori" name="kategori" type="text" value="<?= e($item['kategori'] ?? 'Umum') ?>">
            </div>
            <div class="field">
                <label for="tanggal">Tanggal</label>
                <input id="tanggal" name="tanggal" type="date" required value="<?= e($item['tanggal'] ?? date('Y-m-d')) ?>">
            </div>
            <div class="field">
                <label for="gambar">URL gambar</label>
                <input id="gambar" name="gambar" type="url" placeholder="https://..." value="<?= e($item['gambar'] ?? '') ?>">
            </div>
            <div class="field full">
                <label for="ringkasan">Ringkasan</label>
                <textarea id="ringkasan" name="ringkasan" rows="3" required><?= e($item['ringkasan'] ?? '') ?></textarea>
            </div>
            <div class="field full">
                <label for="isi">Isi berita</label>
                <textarea id="isi" name="isi" rows="10" required><?= e($item['isi'] ?? '') ?></textarea>
            </div>
        </div>
        <div class="toolbar" style="margin-top:1rem;margin-bottom:0;">
            <a class="btn btn-secondary" href="<?= e(adminUrl('berita/')) ?>">Batal</a>
            <button class="btn btn-primary" type="submit">Simpan</button>
        </div>
    </form>
</div>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
