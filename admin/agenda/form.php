<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
requireAdmin();

$id = (int) ($_GET['id'] ?? 0);
$item = null;

if ($id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM agenda WHERE id = ?');
    $stmt->execute([$id]);
    $item = $stmt->fetch();
    if (!$item) {
        setFlash('error', 'Agenda tidak ditemukan.');
        redirect(adminUrl('agenda/'));
    }
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Sesi tidak valid.';
    } else {
        $judul = trim((string) ($_POST['judul'] ?? ''));
        $deskripsi = trim((string) ($_POST['deskripsi'] ?? ''));
        $lokasi = trim((string) ($_POST['lokasi'] ?? ''));
        $tanggalMulai = trim((string) ($_POST['tanggal_mulai'] ?? ''));
        $tanggalSelesai = trim((string) ($_POST['tanggal_selesai'] ?? ''));
        $waktu = trim((string) ($_POST['waktu'] ?? ''));

        if ($judul === '' || $deskripsi === '' || $lokasi === '' || $tanggalMulai === '') {
            $errors[] = 'Judul, deskripsi, lokasi, dan tanggal mulai wajib diisi.';
        }

        if (!$errors) {
            if ($item) {
                $stmt = $pdo->prepare('UPDATE agenda SET judul=?, deskripsi=?, lokasi=?, tanggal_mulai=?, tanggal_selesai=?, waktu=? WHERE id=?');
                $stmt->execute([$judul, $deskripsi, $lokasi, $tanggalMulai, $tanggalSelesai ?: null, $waktu ?: null, $id]);
                setFlash('success', 'Agenda berhasil diperbarui.');
            } else {
                $stmt = $pdo->prepare('INSERT INTO agenda (judul, deskripsi, lokasi, tanggal_mulai, tanggal_selesai, waktu) VALUES (?, ?, ?, ?, ?, ?)');
                $stmt->execute([$judul, $deskripsi, $lokasi, $tanggalMulai, $tanggalSelesai ?: null, $waktu ?: null]);
                setFlash('success', 'Agenda berhasil ditambahkan.');
            }
            redirect(adminUrl('agenda/'));
        }

        $item = compact('judul', 'deskripsi', 'lokasi') + [
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'waktu' => $waktu,
        ];
    }
}

$pageTitle = $item && $id ? 'Edit agenda' : 'Tambah agenda';
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
            <div class="field full">
                <label for="deskripsi">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" rows="4" required><?= e($item['deskripsi'] ?? '') ?></textarea>
            </div>
            <div class="field">
                <label for="lokasi">Lokasi</label>
                <input id="lokasi" name="lokasi" type="text" required value="<?= e($item['lokasi'] ?? '') ?>">
            </div>
            <div class="field">
                <label for="waktu">Waktu</label>
                <input id="waktu" name="waktu" type="text" placeholder="08.00 – 15.00 WIB" value="<?= e($item['waktu'] ?? '') ?>">
            </div>
            <div class="field">
                <label for="tanggal_mulai">Tanggal mulai</label>
                <input id="tanggal_mulai" name="tanggal_mulai" type="date" required value="<?= e($item['tanggal_mulai'] ?? '') ?>">
            </div>
            <div class="field">
                <label for="tanggal_selesai">Tanggal selesai</label>
                <input id="tanggal_selesai" name="tanggal_selesai" type="date" value="<?= e($item['tanggal_selesai'] ?? '') ?>">
            </div>
        </div>
        <div class="toolbar" style="margin-top:1rem;margin-bottom:0;">
            <a class="btn btn-secondary" href="<?= e(adminUrl('agenda/')) ?>">Batal</a>
            <button class="btn btn-primary" type="submit">Simpan</button>
        </div>
    </form>
</div>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
