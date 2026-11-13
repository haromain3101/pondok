</main>

    <?php $pengaturanFooter = $pengaturan ?? (isset($pdo) ? getPengaturan($pdo) : []); ?>
    <footer class="site-footer">
        <div class="container footer-grid">
            <div class="footer-brand">
                <p class="arabic" lang="ar">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</p>
                <h2><?= e($pengaturanFooter['sub_nama'] ?? 'Pondok Pesantren') ?><br><?= e($pengaturanFooter['nama_web'] ?? 'Nurul Hikmah') ?></h2>
                <p><?= e($pengaturanFooter['tagline'] ?? siteTagline($pdo ?? null)) ?></p>
            </div>

            <div>
                <h3>Jelajahi</h3>
                <ul class="footer-links">
                    <li><a href="<?= e(url('profil.php')) ?>">Profil Pondok</a></li>
                    <li><a href="<?= e(url('galeri.php')) ?>">Galeri Kegiatan</a></li>
                    <li><a href="<?= e(url('berita.php')) ?>">Berita Terbaru</a></li>
                    <li><a href="<?= e(url('agenda.php')) ?>">Agenda Pondok</a></li>
                    <li><a href="<?= e(url('pendaftaran.php')) ?>">Pendaftaran Santri</a></li>
                </ul>
            </div>

            <div>
                <h3>Kontak</h3>
                <ul class="footer-contact">
                    <li>Jl. Pendidikan No. 32, Desa Hijau, Kec. Ilmiah</li>
                    <li>Kab. Berkah, Jawa Tengah 50123</li>
                    <li><a href="tel:+6281234567890">0812-3456-7890</a></li>
                    <li><a href="mailto:info@nurulhikmah.ac.id">info@nurulhikmah.ac.id</a></li>
                </ul>
            </div>
        </div>

        <div class="container footer-bottom">
            <p>&copy; <?= date('Y') ?> Pondok Pesantren Nurul Hikmah. Semua hak dilindungi. · <a href="<?= e(adminUrl('login.php')) ?>">Admin</a></p>
        </div>
    </footer>

    <script src="<?= e(url('assets/js/main.js')) ?>"></script>
</body>
</html>
