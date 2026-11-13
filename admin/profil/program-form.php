<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
requireAdmin();

$id = (int) ($_GET['id'] ?? 0);
$item = null;

if ($id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM profil_program WHERE id = ?');
    $stmt->execute([$id]);
    $item = $stmt->fetch();
    if (!$item) {
        setFlash('error', 'Program tidak ditemukan.');
        redirect(adminUrl('profil/'));
    }
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Sesi tidak valid.';
    } else {
        $label = trim((string) ($_POST['label'] ?? ''));
        $judul = trim((string) ($_POST['judul'] ?? ''));
        $deskripsi = trim((string) ($_POST['deskripsi'] ?? ''));
        $urutan = (int) ($_POST['urutan'] ?? 0);

        if ($label === '' || $judul === '' || $deskripsi === '') {
            $errors[] = 'Label, judul, dan deskripsi wajib diisi.';
        }

        if (!$errors) {
            if ($item) {
                $stmt = $pdo->prepare('UPDATE profil_program SET label=?, judul=?, deskripsi=?, urutan=? WHERE id=?');
                $stmt->execute([$label, $judul, $deskripsi, $urutan, $id]);
                setFlash('success', 'Program berhasil diperbarui.');
            } else {
                $stmt = $pdo->prepare('INSERT INTO profil_program (label, judul, deskripsi, urutan) VALUES (?, ?, ?, ?)');
                $stmt->execute([$label, $judul, $deskripsi, $urutan]);
                setFlash('success', 'Program berhasil ditambahkan.');
            }
            redirect(adminUrl('profil/'));
        }

        $item = compact('label', 'judul', 'deskripsi', 'urutan');
    }
}

$pageTitle = $item && $id ? 'Edit program' : 'Tambah program';
require dirname(__DIR__) . '/includes/header.php';
?>

<?php if ($errors): ?>
    <div class="admin-alert admin-alert-error"><?= e(implode(' ', $errors)) ?></div>
<?php endif; ?>

<div class="panel">
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <div class="form-grid">
            <div class="field">
                <label for="label">Label</label>
                <input id="label" name="label" type="text" required placeholder="Tahfiz" value="<?= e($item['label'] ?? '') ?>">
            </div>
            <div class="field">
                <label for="urutan">Urutan</label>
                <input id="urutan" name="urutan" type="number" min="0" value="<?= e((string) ($item['urutan'] ?? '0')) ?>">
            </div>
            <div class="field full">
                <label for="judul">Judul</label>
                <input id="judul" name="judul" type="text" required value="<?= e($item['judul'] ?? '') ?>">
            </div>
            <div class="field full">
                <label for="deskripsi">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" rows="4" required><?= e($item['deskripsi'] ?? '') ?></textarea>
            </div>
        </div>
        <div class="toolbar" style="margin-top:1rem;margin-bottom:0;">
            <a class="btn btn-secondary" href="<?= e(adminUrl('profil/')) ?>">Batal</a>
            <button class="btn btn-primary" type="submit">Simpan</button>
        </div>
    </form>
</div>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
