<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
requireAdmin();

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM pendaftaran WHERE id = ?');
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    setFlash('error', 'Data pendaftaran tidak ditemukan.');
    redirect(adminUrl('pendaftaran/'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? null)) {
        setFlash('error', 'Sesi tidak valid.');
        redirect(adminUrl('pendaftaran/detail.php?id=' . $id));
    }

    $status = trim((string) ($_POST['status'] ?? ''));
    if (!in_array($status, ['menunggu', 'diterima', 'ditolak'], true)) {
        setFlash('error', 'Status tidak valid.');
        redirect(adminUrl('pendaftaran/detail.php?id=' . $id));
    }

    $update = $pdo->prepare('UPDATE pendaftaran SET status = ? WHERE id = ?');
    $update->execute([$status, $id]);
    setFlash('success', 'Status pendaftaran diperbarui.');
    redirect(adminUrl('pendaftaran/detail.php?id=' . $id));
}

$pageTitle = 'Detail pendaftaran';
require dirname(__DIR__) . '/includes/header.php';
?>

<div class="toolbar">
    <a class="btn btn-secondary" href="<?= e(adminUrl('pendaftaran/')) ?>">Kembali</a>
    <a class="btn btn-primary" href="https://wa.me/<?= e(preg_replace('/[^0-9]/', '', $item['no_wa']) ?? '') ?>" target="_blank" rel="noopener">Hubungi WhatsApp</a>
</div>

<div class="panel" style="margin-bottom:1rem;">
    <div class="panel-head">
        <h2><?= e($item['nama_lengkap']) ?></h2>
        <span class="badge badge-<?= e($item['status']) ?>"><?= e(ucfirst($item['status'])) ?></span>
    </div>
    <dl class="detail-grid">
        <div>
            <dt>NISN</dt>
            <dd><?= e($item['nisn'] ?: '-') ?></dd>
        </div>
        <div>
            <dt>Jenjang</dt>
            <dd><?= e($item['jenjang']) ?></dd>
        </div>
        <div>
            <dt>Tempat, tanggal lahir</dt>
            <dd><?= e($item['tempat_lahir']) ?>, <?= e(formatTanggal($item['tanggal_lahir'])) ?></dd>
        </div>
        <div>
            <dt>Jenis kelamin</dt>
            <dd><?= e($item['jenis_kelamin']) ?></dd>
        </div>
        <div>
            <dt>Nama wali</dt>
            <dd><?= e($item['nama_wali']) ?></dd>
        </div>
        <div>
            <dt>No. WhatsApp</dt>
            <dd><?= e($item['no_wa']) ?></dd>
        </div>
        <div style="grid-column:1/-1;">
            <dt>Alamat</dt>
            <dd><?= e($item['alamat']) ?></dd>
        </div>
        <div style="grid-column:1/-1;">
            <dt>Catatan</dt>
            <dd><?= e($item['catatan'] ?: '-') ?></dd>
        </div>
        <div>
            <dt>Dikirim</dt>
            <dd><?= e($item['created_at']) ?></dd>
        </div>
    </dl>
</div>

<div class="panel">
    <div class="panel-head">
        <h2>Ubah status</h2>
    </div>
    <form method="post" class="form-grid">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <div class="field">
            <label for="status">Status</label>
            <select id="status" name="status">
                <?php foreach (['menunggu', 'diterima', 'ditolak'] as $status): ?>
                    <option value="<?= e($status) ?>" <?= $item['status'] === $status ? 'selected' : '' ?>><?= e(ucfirst($status)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field" style="align-self:end;">
            <button class="btn btn-primary" type="submit">Simpan status</button>
        </div>
    </form>
</div>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
