<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
requireAdmin();

$profil = getProfil($pdo);
$programs = getProfilProgram($pdo);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_profil') {
    if (!verifyCsrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Sesi tidak valid.';
    } else {
        $fields = [
            'hero_judul', 'hero_deskripsi', 'sejarah_eyebrow', 'sejarah_judul', 'sejarah_ringkas', 'sejarah_lengkap', 'sejarah_gambar',
            'visi', 'misi', 'motto_arab', 'motto_teks', 'program_eyebrow', 'program_judul', 'program_deskripsi',
            'fasilitas_judul', 'fasilitas_deskripsi',
        ];

        $data = [];
        foreach ($fields as $field) {
            $data[$field] = trim((string) ($_POST[$field] ?? ''));
            if ($data[$field] === '') {
                $errors[] = 'Semua field profil wajib diisi.';
                break;
            }
        }

        if (!$errors) {
            $stmt = $pdo->prepare('UPDATE profil SET
                hero_judul=?, hero_deskripsi=?, sejarah_eyebrow=?, sejarah_judul=?, sejarah_ringkas=?, sejarah_lengkap=?, sejarah_gambar=?,
                visi=?, misi=?, motto_arab=?, motto_teks=?, program_eyebrow=?, program_judul=?, program_deskripsi=?,
                fasilitas_judul=?, fasilitas_deskripsi=?, updated_at=CURRENT_TIMESTAMP
                WHERE id=1');
            $stmt->execute([
                $data['hero_judul'], $data['hero_deskripsi'], $data['sejarah_eyebrow'], $data['sejarah_judul'],
                $data['sejarah_ringkas'], $data['sejarah_lengkap'], $data['sejarah_gambar'], $data['visi'], $data['misi'],
                $data['motto_arab'], $data['motto_teks'], $data['program_eyebrow'], $data['program_judul'],
                $data['program_deskripsi'], $data['fasilitas_judul'], $data['fasilitas_deskripsi'],
            ]);
            setFlash('success', 'Halaman profil berhasil diperbarui.');
            redirect(adminUrl('profil/'));
        }

        $profil = array_merge($profil ?: [], $data);
    }
}

$pageTitle = 'Kelola Profil';
require dirname(__DIR__) . '/includes/header.php';
?>

<?php if ($errors): ?>
    <div class="admin-alert admin-alert-error"><?= e(implode(' ', $errors)) ?></div>
<?php endif; ?>

<div class="toolbar">
    <p style="margin:0;color:var(--ink-soft);">Edit konten halaman profil website.</p>
    <a class="btn btn-secondary" href="<?= e(url('profil.php')) ?>" target="_blank" rel="noopener">Lihat halaman</a>
</div>

<div class="panel" style="margin-bottom:1rem;">
    <div class="panel-head">
        <h2>Konten profil</h2>
    </div>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="action" value="save_profil">

        <div class="form-grid">
            <div class="field full"><strong style="color:var(--leaf);">Hero</strong></div>
            <div class="field full">
                <label for="hero_judul">Judul hero</label>
                <input id="hero_judul" name="hero_judul" type="text" required value="<?= e($profil['hero_judul'] ?? '') ?>">
            </div>
            <div class="field full">
                <label for="hero_deskripsi">Deskripsi hero</label>
                <textarea id="hero_deskripsi" name="hero_deskripsi" rows="2" required><?= e($profil['hero_deskripsi'] ?? '') ?></textarea>
            </div>

            <div class="field full"><strong style="color:var(--leaf);">Sejarah</strong></div>
            <div class="field">
                <label for="sejarah_eyebrow">Label sejarah</label>
                <input id="sejarah_eyebrow" name="sejarah_eyebrow" type="text" required value="<?= e($profil['sejarah_eyebrow'] ?? '') ?>">
            </div>
            <div class="field">
                <label for="sejarah_gambar">URL gambar sejarah</label>
                <input id="sejarah_gambar" name="sejarah_gambar" type="url" required value="<?= e($profil['sejarah_gambar'] ?? '') ?>">
            </div>
            <div class="field full">
                <label for="sejarah_judul">Judul sejarah</label>
                <input id="sejarah_judul" name="sejarah_judul" type="text" required value="<?= e($profil['sejarah_judul'] ?? '') ?>">
            </div>
            <div class="field full">
                <label for="sejarah_ringkas">Ringkasan sejarah</label>
                <textarea id="sejarah_ringkas" name="sejarah_ringkas" rows="3" required><?= e($profil['sejarah_ringkas'] ?? '') ?></textarea>
            </div>
            <div class="field full">
                <label for="sejarah_lengkap">Uraian sejarah</label>
                <textarea id="sejarah_lengkap" name="sejarah_lengkap" rows="4" required><?= e($profil['sejarah_lengkap'] ?? '') ?></textarea>
            </div>

            <div class="field full"><strong style="color:var(--leaf);">Visi, misi &amp; motto</strong></div>
            <div class="field full">
                <label for="visi">Visi</label>
                <textarea id="visi" name="visi" rows="3" required><?= e($profil['visi'] ?? '') ?></textarea>
            </div>
            <div class="field full">
                <label for="misi">Misi</label>
                <textarea id="misi" name="misi" rows="3" required><?= e($profil['misi'] ?? '') ?></textarea>
            </div>
            <div class="field">
                <label for="motto_arab">Motto (Arab)</label>
                <input id="motto_arab" name="motto_arab" type="text" required value="<?= e($profil['motto_arab'] ?? '') ?>">
            </div>
            <div class="field">
                <label for="motto_teks">Arti motto</label>
                <input id="motto_teks" name="motto_teks" type="text" required value="<?= e($profil['motto_teks'] ?? '') ?>">
            </div>

            <div class="field full"><strong style="color:var(--leaf);">Bagrogram unggulan</strong></div>
            <div class="field">
                <label for="program_eyebrow">Label program</label>
                <input id="program_eyebrow" name="program_eyebrow" type="text" required value="<?= e($profil['program_eyebrow'] ?? '') ?>">
            </div>
            <div class="field">
                <label for="program_judul">Judul program</label>
                <input id="program_judul" name="program_judul" type="text" required value="<?= e($profil['program_judul'] ?? '') ?>">
            </div>
            <div class="field full">
                <label for="program_deskripsi">Deskripsi program</label>
                <textarea id="program_deskripsi" name="program_deskripsi" rows="2" required><?= e($profil['program_deskripsi'] ?? '') ?></textarea>
            </div>

            <div class="field full"><strong style="color:var(--leaf);">Fasilitas</strong></div>
            <div class="field full">
                <label for="fasilitas_judul">Judul fasilitas</label>
                <input id="fasilitas_judul" name="fasilitas_judul" type="text" required value="<?= e($profil['fasilitas_judul'] ?? '') ?>">
            </div>
            <div class="field full">
                <label for="fasilitas_deskripsi">Deskripsi fasilitas</label>
                <textarea id="fasilitas_deskripsi" name="fasilitas_deskripsi" rows="2" required><?= e($profil['fasilitas_deskripsi'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="toolbar" style="margin-top:1rem;margin-bottom:0;">
            <span></span>
            <button class="btn btn-primary" type="submit">Simpan profil</button>
        </div>
    </form>
</div>

<div class="panel">
    <div class="panel-head">
        <h2>Kartu program unggulan</h2>
        <a class="btn btn-primary btn-sm" href="<?= e(adminUrl('profil/program-form.php')) ?>">Tambah program</a>
    </div>
    <?php if (!$programs): ?>
        <div class="empty">Belum ada program. Tambahkan minimal satu kartu program.</div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Urutan</th>
                        <th>Label</th>
                        <th>Judul</th>
                        <th>Deskripsi</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($programs as $item): ?>
                        <tr>
                            <td><?= (int) $item['urutan'] ?></td>
                            <td><?= e($item['label']) ?></td>
                            <td><strong><?= e($item['judul']) ?></strong></td>
                            <td><?= e($item['deskripsi']) ?></td>
                            <td>
                                <div class="actions">
                                    <a class="btn btn-secondary btn-sm" href="<?= e(adminUrl('profil/program-form.php?id=' . $item['id'])) ?>">Edit</a>
                                    <form method="post" action="<?= e(adminUrl('profil/program-delete.php')) ?>" onsubmit="return confirm('Hapus program ini?')">
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
