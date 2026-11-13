<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
requireAdmin();

$pengaturan = getPengaturan($pdo);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Sesi tidak valid, silakan muat ulang halaman.';
    } else {
        $nama_web = trim((string) ($_POST['nama_web'] ?? ''));
        $sub_nama = trim((string) ($_POST['sub_nama'] ?? ''));
        $tagline = trim((string) ($_POST['tagline'] ?? ''));
        $logo_tipe = (($_POST['logo_tipe'] ?? 'inisial') === 'gambar') ? 'gambar' : 'inisial';
        $inisial_teks = trim((string) ($_POST['inisial_teks'] ?? 'ن ه'));
        if ($inisial_teks === '') {
            $inisial_teks = 'ن ه';
        }

        if ($nama_web === '') {
            $errors[] = 'Nama website tidak boleh kosong.';
        }

        // Kelola Logo Gambar
        $logo_gambar = $pengaturan['logo_gambar'] ?? null;

        if (!empty($_POST['hapus_logo'])) {
            deleteUploadedFile($logo_gambar);
            $logo_gambar = null;
        }

        if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $resLogo = uploadFile($_FILES['logo_file'], 'uploads/logo', ['png', 'jpg', 'jpeg', 'webp'], 2097152);
            if ($resLogo['success']) {
                deleteUploadedFile($logo_gambar);
                $logo_gambar = $resLogo['path'];
                $logo_tipe = 'gambar'; // otomatis aktifkan mode gambar jika baru upload
            } else {
                $errors[] = 'Logo: ' . $resLogo['error'];
            }
        } elseif (!empty($_POST['logo_url']) && empty($_POST['hapus_logo'])) {
            $urlInput = trim((string) $_POST['logo_url']);
            if ($urlInput !== '' && $urlInput !== ($logo_gambar ?? '')) {
                deleteUploadedFile($logo_gambar);
                $logo_gambar = $urlInput;
            }
        }

        // Kelola Favicon
        $favicon = $pengaturan['favicon'] ?? null;

        if (!empty($_POST['hapus_favicon'])) {
            deleteUploadedFile($favicon);
            $favicon = null;
        }

        if (isset($_FILES['favicon_file']) && $_FILES['favicon_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $resFavicon = uploadFile($_FILES['favicon_file'], 'uploads/logo', ['ico', 'png', 'svg', 'webp'], 1048576);
            if ($resFavicon['success']) {
                deleteUploadedFile($favicon);
                $favicon = $resFavicon['path'];
            } else {
                $errors[] = 'Favicon: ' . $resFavicon['error'];
            }
        } elseif (!empty($_POST['favicon_url']) && empty($_POST['hapus_favicon'])) {
            $favUrlInput = trim((string) $_POST['favicon_url']);
            if ($favUrlInput !== '' && $favUrlInput !== ($favicon ?? '')) {
                deleteUploadedFile($favicon);
                $favicon = $favUrlInput;
            }
        }

        if (!$errors) {
            $stmt = $pdo->prepare('UPDATE pengaturan SET
                nama_web = ?,
                sub_nama = ?,
                tagline = ?,
                logo_gambar = ?,
                favicon = ?,
                logo_tipe = ?,
                inisial_teks = ?,
                updated_at = CURRENT_TIMESTAMP
                WHERE id = 1');
            $stmt->execute([
                $nama_web,
                $sub_nama,
                $tagline,
                $logo_gambar,
                $favicon,
                $logo_tipe,
                $inisial_teks,
            ]);

            setFlash('success', 'Pengaturan identitas dan logo website berhasil disimpan.');
            redirect(adminUrl('pengaturan/'));
        }

        // Update data sementara jika ada error
        $pengaturan = array_merge($pengaturan, [
            'nama_web' => $nama_web,
            'sub_nama' => $sub_nama,
            'tagline' => $tagline,
            'logo_tipe' => $logo_tipe,
            'inisial_teks' => $inisial_teks,
            'logo_gambar' => $logo_gambar,
            'favicon' => $favicon,
        ]);
    }
}

$pageTitle = 'Pengaturan Website & Logo';
require dirname(__DIR__) . '/includes/header.php';
?>

<?php if ($errors): ?>
    <div class="admin-alert admin-alert-error"><?= e(implode(' ', $errors)) ?></div>
<?php endif; ?>

<div class="toolbar" style="margin-bottom: 1.5rem;">
    <p style="margin:0;color:var(--ink-soft);">Kelola identitas, logo gambar atau inisial teks, dan favicon website.</p>
</div>

<!-- Pratinjau Tampilan Header Website Saat Ini -->
<div class="panel" style="margin-bottom: 1.5rem; background: #fafaf7;">
    <div class="panel-head">
        <h2>Pratinjau Tampilan Header Saat Ini</h2>
        <a class="btn btn-secondary btn-sm" href="<?= e(url()) ?>" target="_blank" rel="noopener">Buka Website</a>
    </div>
    <div style="padding: 1rem; background: #ffffff; border: 1px solid var(--line); border-radius: 12px; display: inline-flex; align-items: center; gap: 0.85rem;">
        <?php if (!empty($pengaturan['logo_gambar']) && ($pengaturan['logo_tipe'] ?? 'inisial') === 'gambar'): ?>
            <img src="<?= e(assetOrUploadUrl($pengaturan['logo_gambar'])) ?>" alt="Logo" style="max-height: 48px; max-width: 140px; object-fit: contain; display: block;">
        <?php else: ?>
            <div style="width: 48px; height: 48px; border-radius: 50%; display: grid; place-items: center; background: linear-gradient(145deg, #1f6b52, #2d8a68); color: #e6d5a8; font-family: 'Amiri', serif; font-size: 1rem; font-weight: 700;">
                <?= e($pengaturan['inisial_teks'] ?: 'ن ه') ?>
            </div>
        <?php endif; ?>
        <div style="display: flex; flex-direction: column; line-height: 1.2;">
            <strong style="font-size: 1.25rem; font-family: var(--font-display);"><?= e($pengaturan['nama_web'] ?: 'Nurul Hikmah') ?></strong>
            <small style="color: var(--ink-soft); font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.04em;"><?= e($pengaturan['sub_nama'] ?: 'Pondok Pesantren') ?></small>
        </div>
    </div>
</div>

<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

    <div class="panel" style="margin-bottom: 1.5rem;">
        <div class="panel-head">
            <h2>Identitas Website</h2>
        </div>
        <div class="form-grid">
            <div class="field">
                <label for="nama_web">Nama Website / Lembaga</label>
                <input id="nama_web" name="nama_web" type="text" required value="<?= e($pengaturan['nama_web'] ?? 'Nurul Hikmah') ?>" placeholder="Contoh: Nurul Hikmah">
                <span class="form-hint">Nama utama yang ditampilkan di logo dan header.</span>
            </div>

            <div class="field">
                <label for="sub_nama">Sub Nama / Label</label>
                <input id="sub_nama" name="sub_nama" type="text" value="<?= e($pengaturan['sub_nama'] ?? 'Pondok Pesantren') ?>" placeholder="Contoh: Pondok Pesantren">
                <span class="form-hint">Keterangan kecil di bawah nama utama (misal: Pondok Pesantren, Islamic Boarding School).</span>
            </div>

            <div class="field full">
                <label for="tagline">Tagline / Slogan</label>
                <input id="tagline" name="tagline" type="text" value="<?= e($pengaturan['tagline'] ?? '') ?>" placeholder="Slogan lembaga">
                <span class="form-hint">Digunakan di deskripsi meta SEO dan footer website.</span>
            </div>
        </div>
    </div>

    <div class="panel" style="margin-bottom: 1.5rem;">
        <div class="panel-head">
            <h2>Pengaturan Logo Website</h2>
        </div>

        <div class="form-grid">
            <div class="field full">
                <label>Model Tampilan Logo</label>
                <div class="radio-group">
                    <label class="radio-option">
                        <input type="radio" name="logo_tipe" value="gambar" <?= ($pengaturan['logo_tipe'] ?? 'inisial') === 'gambar' ? 'checked' : '' ?>>
                        <strong>Gunakan Gambar Logo</strong> (Upload file gambar / logo resmi)
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="logo_tipe" value="inisial" <?= ($pengaturan['logo_tipe'] ?? 'inisial') === 'inisial' ? 'checked' : '' ?>>
                        <strong>Gunakan Inisial Teks Badge</strong> (Lingkaran inisial teks/Arab bawaan)
                    </label>
                </div>
            </div>

            <div class="field full" style="border-top: 1px solid var(--line); padding-top: 1rem;">
                <label for="logo_file">Upload File Logo Baru</label>
                <input id="logo_file" name="logo_file" type="file" accept="image/png,image/jpeg,image/webp">
                <span class="form-hint">Mendukung file foto PNG (latar transparan direkomendasikan), JPG, atau WebP. Maksimal 2 MB.</span>

                <?php if (!empty($pengaturan['logo_gambar'])): ?>
                    <div class="preview-box">
                        <div class="preview-img-wrap">
                            <img src="<?= e(assetOrUploadUrl($pengaturan['logo_gambar'])) ?>" alt="Logo Saat Ini">
                        </div>
                        <div class="preview-info">
                            <p><strong>File logo saat ini:</strong> <?= e($pengaturan['logo_gambar']) ?></p>
                            <label style="display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--danger); font-size: 0.88rem;">
                                <input type="checkbox" name="hapus_logo" value="1">
                                Hapus file logo ini (kembali ke default)
                            </label>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="field full">
                <label for="logo_url">Atau Gunakan Tautan URL Gambar Logo</label>
                <input id="logo_url" name="logo_url" type="text" value="<?= e(str_starts_with($pengaturan['logo_gambar'] ?? '', 'http') ? $pengaturan['logo_gambar'] : '') ?>" placeholder="https://example.com/logo.png">
                <span class="form-hint">Isi kolom ini jika logo berada di server eksternal dan Anda tidak ingin mengunggah file.</span>
            </div>

            <div class="field full" style="border-top: 1px solid var(--line); padding-top: 1rem;">
                <label for="inisial_teks">Teks Inisial Badge (Jika Memilih Model Inisial)</label>
                <input id="inisial_teks" name="inisial_teks" type="text" value="<?= e($pengaturan['inisial_teks'] ?? 'ن ه') ?>" style="max-width: 200px;" placeholder="ن ه">
                <span class="form-hint">Huruf inisial yang tampil di dalam lingkaran jika mode inisial aktif (misal: Arab atau Latin seperti NH).</span>
            </div>
        </div>
    </div>

    <div class="panel" style="margin-bottom: 1.5rem;">
        <div class="panel-head">
            <h2>Favicon (Icon Tab Browser)</h2>
        </div>
        <div class="form-grid">
            <div class="field full">
                <label for="favicon_file">Upload File Favicon</label>
                <input id="favicon_file" name="favicon_file" type="file" accept=".ico,image/png,image/svg+xml,image/webp">
                <span class="form-hint">Icon kecil di tab browser (.ico, .png, .svg). Maksimal 1 MB.</span>

                <?php if (!empty($pengaturan['favicon'])): ?>
                    <div class="preview-box">
                        <div class="preview-img-wrap" style="width: 42px; height: 42px;">
                            <img src="<?= e(assetOrUploadUrl($pengaturan['favicon'])) ?>" alt="Favicon Saat Ini">
                        </div>
                        <div class="preview-info">
                            <p><strong>Favicon saat ini:</strong> <?= e($pengaturan['favicon']) ?></p>
                            <label style="display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--danger); font-size: 0.88rem;">
                                <input type="checkbox" name="hapus_favicon" value="1">
                                Hapus favicon ini
                            </label>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="field full">
                <label for="favicon_url">Atau URL Favicon Eksternal</label>
                <input id="favicon_url" name="favicon_url" type="text" value="<?= e(str_starts_with($pengaturan['favicon'] ?? '', 'http') ? $pengaturan['favicon'] : '') ?>" placeholder="https://example.com/favicon.ico">
            </div>
        </div>
    </div>

    <div class="form-actions" style="display: flex; gap: 0.75rem;">
        <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
        <a class="btn btn-secondary" href="<?= e(adminUrl()) ?>">Batal</a>
    </div>
</form>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
