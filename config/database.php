<?php

declare(strict_types=1);

$dbConfig = require __DIR__ . '/db.php';

try {
    $dsnServer = sprintf(
        'mysql:host=%s;port=%s;charset=%s',
        $dbConfig['host'],
        $dbConfig['port'],
        $dbConfig['charset']
    );

    $serverPdo = new PDO($dsnServer, $dbConfig['username'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $serverPdo->exec(
        'CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '``', $dbConfig['database']) . '`
         CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
    );

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $dbConfig['host'],
        $dbConfig['port'],
        $dbConfig['database'],
        $dbConfig['charset']
    );

    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    exit('Koneksi MySQL gagal. Periksa config/db.php dan pastikan MySQL Laragon aktif.');
}

function migrate(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS berita (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            judul VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            ringkasan TEXT NOT NULL,
            isi MEDIUMTEXT NOT NULL,
            gambar VARCHAR(500) DEFAULT NULL,
            kategori VARCHAR(100) NOT NULL DEFAULT 'Umum',
            tanggal DATE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS agenda (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            judul VARCHAR(255) NOT NULL,
            deskripsi TEXT NOT NULL,
            lokasi VARCHAR(255) NOT NULL,
            tanggal_mulai DATE NOT NULL,
            tanggal_selesai DATE DEFAULT NULL,
            waktu VARCHAR(100) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS pendaftaran (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            nama_lengkap VARCHAR(255) NOT NULL,
            nisn VARCHAR(50) DEFAULT NULL,
            tempat_lahir VARCHAR(100) NOT NULL,
            tanggal_lahir DATE NOT NULL,
            jenis_kelamin VARCHAR(20) NOT NULL,
            alamat TEXT NOT NULL,
            nama_wali VARCHAR(255) NOT NULL,
            no_wa VARCHAR(30) NOT NULL,
            jenjang VARCHAR(50) NOT NULL,
            catatan TEXT DEFAULT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'menunggu',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS galeri (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            judul VARCHAR(255) NOT NULL,
            kategori VARCHAR(100) NOT NULL,
            gambar VARCHAR(500) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admin (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            nama VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS profil (
            id TINYINT UNSIGNED PRIMARY KEY,
            hero_judul VARCHAR(255) NOT NULL,
            hero_deskripsi TEXT NOT NULL,
            sejarah_eyebrow VARCHAR(100) NOT NULL,
            sejarah_judul VARCHAR(255) NOT NULL,
            sejarah_ringkas TEXT NOT NULL,
            sejarah_lengkap TEXT NOT NULL,
            sejarah_gambar VARCHAR(500) NOT NULL,
            visi TEXT NOT NULL,
            misi TEXT NOT NULL,
            motto_arab VARCHAR(255) NOT NULL,
            motto_teks TEXT NOT NULL,
            program_eyebrow VARCHAR(100) NOT NULL,
            program_judul VARCHAR(255) NOT NULL,
            program_deskripsi TEXT NOT NULL,
            fasilitas_judul VARCHAR(255) NOT NULL,
            fasilitas_deskripsi TEXT NOT NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS profil_program (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            label VARCHAR(100) NOT NULL,
            judul VARCHAR(255) NOT NULL,
            deskripsi TEXT NOT NULL,
            urutan INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS pengaturan (
            id TINYINT UNSIGNED PRIMARY KEY,
            nama_web VARCHAR(255) NOT NULL,
            sub_nama VARCHAR(100) NOT NULL DEFAULT 'Pondok Pesantren',
            tagline VARCHAR(255) NOT NULL,
            logo_gambar VARCHAR(500) DEFAULT NULL,
            favicon VARCHAR(500) DEFAULT NULL,
            logo_tipe VARCHAR(20) NOT NULL DEFAULT 'inisial',
            inisial_teks VARCHAR(50) NOT NULL DEFAULT 'ن ه',
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
}

function seedAdmin(PDO $pdo): void
{
    $count = (int) $pdo->query('SELECT COUNT(*) FROM admin')->fetchColumn();
    if ($count > 0) {
        return;
    }

    $stmt = $pdo->prepare('INSERT INTO admin (username, password, nama) VALUES (?, ?, ?)');
    $stmt->execute([
        'admin',
        password_hash('nurulhikmah2026', PASSWORD_DEFAULT),
        'Administrator',
    ]);
}

function seedIfEmpty(PDO $pdo): void
{
    $count = (int) $pdo->query('SELECT COUNT(*) FROM berita')->fetchColumn();
    if ($count > 0) {
        return;
    }

    $berita = [
        [
            'Pembukaan Tahun Ajaran Baru 1447 H / 2026 M',
            'pembukaan-tahun-ajaran-baru-2026',
            'Pondok Pesantren Nurul Hikmah menyambut santri baru dengan rangkaian kegiatan orientasi dan silaturahmi wali.',
            "Alhamdulillah, Pondok Pesantren Nurul Hikmah resmi membuka tahun ajaran baru. Kegiatan dimulai dengan pembacaan ayat suci Al-Qur'an, sambutan pengasuh, dan pengenalan lingkungan pondok.\n\nPara santri baru mengikuti masa ta'aruf selama tiga hari yang mencakup pengenalan aturan asrama, jadwal kegiatan, serta penanaman adab sehari-hari. Wali santri juga diundang untuk musyawarah awal tahun.\n\nSemoga tahun ajaran ini menjadi langkah baru menuju ilmu yang bermanfaat dan akhlak yang mulia.",
            'https://images.unsplash.com/photo-1564769625905-50e93615e769?w=1200&q=80',
            'Pengumuman',
            '2026-07-15',
        ],
        [
            'Santri Nurul Hikmah Raih Juara MTQ Kabupaten',
            'santri-raih-juara-mtq-kabupaten',
            'Tim tilawah dan hifzil Qur\'an pondok membawa pulang tiga piala di ajang MTQ tingkat kabupaten.',
            "Prestasi membanggakan kembali ditorehkan santri Pondok Pesantren Nurul Hikmah. Dalam Musabaqah Tilawatil Qur'an tingkat kabupaten, kontingen pondok meraih juara 1 tilawah putra, juara 2 hifzil 10 juz, dan juara harapan fahmil Qur'an.\n\nKeberhasilan ini merupakan buah dari latihan rutin ba'da Maghrib serta bimbingan ustadz tahfiz. Pengasuh mengucapkan apresiasi dan mengingatkan agar prestasi menjadi motivasi beribadah, bukan sekadar kebanggaan duniawi.",
            'https://images.unsplash.com/photo-1609599006353-e629aaabfeae?w=1200&q=80',
            'Prestasi',
            '2026-09-02',
        ],
        [
            'Kajian Kitab Kuning Bersama Kyai Tamu',
            'kajian-kitab-kuning-kyai-tamu',
            'Kajian terbuka kitab Riyadhus Shalihin digelar di masjid pondok dan dihadiri santri serta masyarakat sekitar.',
            "Masjid Pondok Pesantren Nurul Hikmah penuh jamaah pada malam kajian kitab Riyadhus Shalihin. Kyai tamu dari pesantren mitra menyampaikan penjelasan tentang adab menuntut ilmu dan menjaga lisan.\n\nKajian semacam ini menjadi program rutin bulanan agar santri terbiasa dengan tradisi keilmuan pesantren. Masyarakat sekitar juga dipersilakan hadir tanpa biaya.",
            'https://images.unsplash.com/photo-1585036156171-435537cbd1d4?w=1200&q=80',
            'Kegiatan',
            '2026-10-18',
        ],
    ];

    $stmt = $pdo->prepare('INSERT INTO berita (judul, slug, ringkasan, isi, gambar, kategori, tanggal) VALUES (?, ?, ?, ?, ?, ?, ?)');
    foreach ($berita as $row) {
        $stmt->execute($row);
    }

    $agenda = [
        [
            'Pendaftaran Santri Baru Gelombang 1',
            'Pembukaan pendaftaran resmi untuk jenjang MTs dan MA. Formulir dapat diisi online atau datang ke sekretariat.',
            'Sekretariat PP Nurul Hikmah',
            '2026-11-01',
            '2026-12-15',
            '08.00 – 15.00 WIB',
        ],
        [
            'Haul & Harlah Pondok ke-32',
            'Peringatan haul para masyayikh sekaligus harlah pondok dengan pengajian umum, khataman, dan pentas seni santri.',
            'Halaman Masjid Utama',
            '2026-12-20',
            '2026-12-20',
            '08.00 – selesai',
        ],
        [
            'Ujian Semester Ganjil',
            'Pelaksanaan ujian tulis dan lisan untuk seluruh santri MTs dan MA, termasuk evaluasi hafalan.',
            'Gedung Belajar',
            '2026-12-01',
            '2026-12-12',
            'Sesuai jadwal kelas',
        ],
        [
            'Rihlah Ilmiah Santri',
            'Kunjungan edukatif ke lembaga mitra dan situs bersejarah untuk memperluas wawasan keilmuan santri kelas akhir.',
            'Titik kumpul: gerbang pondok',
            '2027-01-10',
            '2027-01-12',
            '06.30 WIB',
        ],
    ];

    $stmt = $pdo->prepare('INSERT INTO agenda (judul, deskripsi, lokasi, tanggal_mulai, tanggal_selesai, waktu) VALUES (?, ?, ?, ?, ?, ?)');
    foreach ($agenda as $row) {
        $stmt->execute($row);
    }

    $galeri = [
        ['Suasana shalat berjamaah di masjid pondok', 'Ibadah', 'https://images.unsplash.com/photo-1564769625905-50e93615e769?w=900&q=80'],
        ['Kegiatan belajar kitab di kelas', 'Pendidikan', 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=900&q=80'],
        ['Hafalan Al-Qur\'an ba\'da Subuh', 'Tahfiz', 'https://images.unsplash.com/photo-1609599006353-e629aaabfeae?w=900&q=80'],
        ['Gotong royong membersihkan lingkungan', 'Kegiatan', 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=900&q=80'],
        ['Pentas seni hari santri', 'Prestasi', 'https://images.unsplash.com/photo-1511632765486-a01980e3818f?w=900&q=80'],
        ['Asrama putra tampak depan', 'Fasilitas', 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=900&q=80'],
        ['Perpustakaan & ruang baca', 'Fasilitas', 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?w=900&q=80'],
        ['Olahraga pagi di lapangan', 'Kegiatan', 'https://images.unsplash.com/photo-1461896836934-ffe607ba6851?w=900&q=80'],
    ];

    $stmt = $pdo->prepare('INSERT INTO galeri (judul, kategori, gambar) VALUES (?, ?, ?)');
    foreach ($galeri as $row) {
        $stmt->execute($row);
    }
}

function seedProfil(PDO $pdo): void
{
    $count = (int) $pdo->query('SELECT COUNT(*) FROM profil')->fetchColumn();
    if ($count > 0) {
        return;
    }

    $pdo->prepare('INSERT INTO profil (
        id, hero_judul, hero_deskripsi, sejarah_eyebrow, sejarah_judul, sejarah_ringkas, sejarah_lengkap, sejarah_gambar,
        visi, misi, motto_arab, motto_teks, program_eyebrow, program_judul, program_deskripsi, fasilitas_judul, fasilitas_deskripsi
    ) VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)')->execute([
        'Mengenal Nurul Hikmah',
        'Pesantren yang tumbuh dari semangat dakwah, pendidikan, dan pengabdian kepada umat.',
        'Sejarah singkat',
        'Dari surau kecil hingga lembaga pendidikan',
        'Pondok Pesantren Nurul Hikmah didirikan untuk menjawab kebutuhan masyarakat akan pendidikan agama yang mendalam sekaligus bekal ilmu dunia yang bermanfaat.',
        'Berawal dari pengajian malam di surau desa, pondok berkembang menjadi lembaga dengan asrama, madrasah formal, dan program tahfiz. Hingga kini, ribuan alumni berkiprah sebagai pendidik, dai, profesional, dan penggerak masyarakat.',
        'https://images.unsplash.com/photo-1542816417-0983c9c9ad53?w=1200&q=80',
        'Menjadi pesantren unggulan yang mencetak generasi Qur\'ani, berakhlak karimah, dan siap berkhidmat untuk umat.',
        'Menyelenggarakan pendidikan terpadu, membina adab, memperkuat hafalan, serta menumbuhkan kemandirian santri dalam kehidupan sehari-hari.',
        'الْعِلْمُ نُورٌ',
        'Ilmu adalah cahaya — dan cahaya itu dijaga dengan adab.',
        'Program unggulan',
        'Yang kami tanam setiap hari',
        'Kurikulum dirancang agar santri tumbuh utuh: ruhani, intelektual, dan sosial.',
        'Lingkungan yang mendukung belajar',
        'Masjid, asrama, ruang kelas, perpustakaan, lapangan olahraga, dan klinik kesehatan santri.',
    ]);

    $programs = [
        ['Tahfiz', 'Hifzul Qur\'an', 'Target hafalan bertahap, setoran harian, dan murojaah terjadwal dengan pembimbing tetap.', 1],
        ['Kitab', 'Dirasah Islamiyah', 'Pembelajaran nahwu, sharaf, fiqih, akhlak, dan tafsir melalui tradisi bandongan serta sorogan.', 2],
        ['Formal', 'MTs & MA', 'Pendidikan formal yang selaras dengan kehidupan asrama agar santri siap melanjutkan studi.', 3],
    ];

    $stmt = $pdo->prepare('INSERT INTO profil_program (label, judul, deskripsi, urutan) VALUES (?, ?, ?, ?)');
    foreach ($programs as $row) {
        $stmt->execute($row);
    }
}

function seedPengaturan(PDO $pdo): void
{
    $count = (int) $pdo->query('SELECT COUNT(*) FROM pengaturan')->fetchColumn();
    if ($count > 0) {
        return;
    }

    $stmt = $pdo->prepare('INSERT INTO pengaturan (
        id, nama_web, sub_nama, tagline, logo_gambar, favicon, logo_tipe, inisial_teks
    ) VALUES (1, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        'Nurul Hikmah',
        'Pondok Pesantren',
        'Membentuk generasi berilmu, berakhlak, dan mandiri',
        null,
        null,
        'inisial',
        'ن ه',
    ]);
}

migrate($pdo);
seedIfEmpty($pdo);
seedAdmin($pdo);
seedProfil($pdo);
seedPengaturan($pdo);
