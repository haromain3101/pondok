<?php

declare(strict_types=1);

$pageTitle = 'Pendaftaran — Pondok Pesantren Nurul Hikmah';
$pageDescription = 'Formulir pendaftaran santri baru Pondok Pesantren Nurul Hikmah.';

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/database.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Proteksi CSRF
    if (!verifyCsrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Sesi formulir kedaluwarsa. Silakan muat ulang halaman.';
    }

    // 2. Proteksi Honeypot Bot Spam
    if (!empty($_POST['website_bot_trap'])) {
        $errors[] = 'Pengiriman formulir tidak valid.';
    }

    $nama = trim((string) ($_POST['nama_lengkap'] ?? ''));
    $nisn = trim((string) ($_POST['nisn'] ?? ''));
    $tempatLahir = trim((string) ($_POST['tempat_lahir'] ?? ''));
    $tanggalLahir = trim((string) ($_POST['tanggal_lahir'] ?? ''));
    $jenisKelamin = trim((string) ($_POST['jenis_kelamin'] ?? ''));
    $alamat = trim((string) ($_POST['alamat'] ?? ''));
    $namaWali = trim((string) ($_POST['nama_wali'] ?? ''));
    $noWa = trim((string) ($_POST['no_wa'] ?? ''));
    $jenjang = trim((string) ($_POST['jenjang'] ?? ''));
    $catatan = trim((string) ($_POST['catatan'] ?? ''));

    if ($nama === '') {
        $errors[] = 'Nama lengkap wajib diisi.';
    }
    if ($tempatLahir === '') {
        $errors[] = 'Tempat lahir wajib diisi.';
    }
    if ($tanggalLahir === '') {
        $errors[] = 'Tanggal lahir wajib diisi.';
    }
    if (!in_array($jenisKelamin, ['Laki-laki', 'Perempuan'], true)) {
        $errors[] = 'Jenis kelamin tidak valid.';
    }
    if ($alamat === '') {
        $errors[] = 'Alamat wajib diisi.';
    }
    if ($namaWali === '') {
        $errors[] = 'Nama wali wajib diisi.';
    }
    if ($noWa === '' || !preg_match('/^[0-9+\-\s]{8,20}$/', $noWa)) {
        $errors[] = 'Nomor WhatsApp tidak valid.';
    }
    if (!in_array($jenjang, ['MTs', 'MA', 'Tahfiz'], true)) {
        $errors[] = 'Jenjang tidak valid.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO pendaftaran (nama_lengkap, nisn, tempat_lahir, tanggal_lahir, jenis_kelamin, alamat, nama_wali, no_wa, jenjang, catatan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $nama,
            $nisn !== '' ? $nisn : null,
            $tempatLahir,
            $tanggalLahir,
            $jenisKelamin,
            $alamat,
            $namaWali,
            $noWa,
            $jenjang,
            $catatan !== '' ? $catatan : null,
        ]);
        $success = true;
        $_POST = [];
    }
}

require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container reveal">
        <span class="eyebrow">Pendaftaran</span>
        <h1>Gabung menjadi santri</h1>
        <p>Isi formulir di bawah. Tim sekretariat akan menghubungi via WhatsApp untuk langkah berikutnya.</p>
    </div>
</section>

<section class="section" style="padding-top: 0;">
    <div class="container form-shell">
        <aside class="form-intro reveal">
            <div class="section-head">
                <span class="eyebrow">Informasi</span>
                <h2>Gelombang 1 dibuka</h2>
                <p>Pendaftaran santri baru tahun ajaran 2026/2027 untuk jenjang MTs, MA, dan program Tahfiz.</p>
            </div>
            <ul class="feature-list">
                <li>Kuota terbatas per asrama putra &amp; putri.</li>
                <li>Siapkan fotokopi KK, akta, dan rapor terakhir saat verifikasi.</li>
                <li>Konfirmasi biasanya dalam 1–3 hari kerja.</li>
            </ul>
            <p style="color: var(--ink-soft);">Bantuan: <a href="tel:+6281234567890" style="color: var(--forest); font-weight: 600;">0812-3456-7890</a></p>
        </aside>

        <div class="form-panel reveal">
            <?php if ($success): ?>
                <div class="alert alert-success">Pendaftaran berhasil dikirim. Mohon menunggu hubungi dari sekretariat.</div>
            <?php endif; ?>

            <?php if ($errors): ?>
                <div class="alert alert-error">
                    <?= e(implode(' ', $errors)) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= e(url('pendaftaran.php')) ?>" novalidate>
                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                <!-- Honeypot field (tersembunyi untuk manusia, menjebak bot spam otomatis) -->
                <div style="position: absolute; left: -9999px; opacity: 0; pointer-events: none;" aria-hidden="true">
                    <input type="text" name="website_bot_trap" tabindex="-1" autocomplete="off">
                </div>
                <div class="form-grid">
                    <div class="field full">
                        <label for="nama_lengkap">Nama lengkap calon santri</label>
                        <input id="nama_lengkap" name="nama_lengkap" type="text" required value="<?= e($_POST['nama_lengkap'] ?? '') ?>">
                    </div>

                    <div class="field">
                        <label for="nisn">NISN (opsional)</label>
                        <input id="nisn" name="nisn" type="text" value="<?= e($_POST['nisn'] ?? '') ?>">
                    </div>

                    <div class="field">
                        <label for="jenjang">Jenjang</label>
                        <select id="jenjang" name="jenjang" required>
                            <?php
                            $jenjangOptions = ['MTs', 'MA', 'Tahfiz'];
                            $selectedJenjang = $_POST['jenjang'] ?? '';
                            ?>
                            <option value="">Pilih jenjang</option>
                            <?php foreach ($jenjangOptions as $opt): ?>
                                <option value="<?= e($opt) ?>" <?= $selectedJenjang === $opt ? 'selected' : '' ?>><?= e($opt) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="field">
                        <label for="tempat_lahir">Tempat lahir</label>
                        <input id="tempat_lahir" name="tempat_lahir" type="text" required value="<?= e($_POST['tempat_lahir'] ?? '') ?>">
                    </div>

                    <div class="field">
                        <label for="tanggal_lahir">Tanggal lahir</label>
                        <input id="tanggal_lahir" name="tanggal_lahir" type="date" required value="<?= e($_POST['tanggal_lahir'] ?? '') ?>">
                    </div>

                    <div class="field">
                        <label for="jenis_kelamin">Jenis kelamin</label>
                        <select id="jenis_kelamin" name="jenis_kelamin" required>
                            <?php $jk = $_POST['jenis_kelamin'] ?? ''; ?>
                            <option value="">Pilih</option>
                            <option value="Laki-laki" <?= $jk === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="Perempuan" <?= $jk === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="no_wa">No. WhatsApp wali</label>
                        <input id="no_wa" name="no_wa" type="tel" required placeholder="08xxxxxxxxxx" value="<?= e($_POST['no_wa'] ?? '') ?>">
                    </div>

                    <div class="field full">
                        <label for="alamat">Alamat lengkap</label>
                        <textarea id="alamat" name="alamat" rows="3" required><?= e($_POST['alamat'] ?? '') ?></textarea>
                    </div>

                    <div class="field full">
                        <label for="nama_wali">Nama wali / orang tua</label>
                        <input id="nama_wali" name="nama_wali" type="text" required value="<?= e($_POST['nama_wali'] ?? '') ?>">
                    </div>

                    <div class="field full">
                        <label for="catatan">Catatan tambahan (opsional)</label>
                        <textarea id="catatan" name="catatan" rows="3"><?= e($_POST['catatan'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="cta-row">
                    <button class="btn btn-solid" type="submit">Kirim pendaftaran</button>
                </div>
            </form>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
