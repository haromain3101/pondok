<?php

declare(strict_types=1);

function basePath(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }

    if (!empty($_SERVER['DOCUMENT_ROOT'])) {
        $docRoot = rtrim(str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']) ?: $_SERVER['DOCUMENT_ROOT']), '/');
        $rootDir = rtrim(str_replace('\\', '/', realpath(dirname(__DIR__)) ?: dirname(__DIR__)), '/');
        if (str_starts_with($rootDir, $docRoot)) {
            $base = substr($rootDir, strlen($docRoot));
            return $base = rtrim($base, '/');
        }
    }

    $script = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $base = rtrim($script, '/');

    // Naik ke root project bila berada di subfolder
    while (
        $base !== '' &&
        $base !== '/' &&
        (
            str_ends_with($base, '/includes') ||
            str_ends_with($base, '/admin') ||
            str_ends_with($base, '/pengaturan') ||
            str_ends_with($base, '/berita') ||
            str_ends_with($base, '/agenda') ||
            str_ends_with($base, '/galeri') ||
            str_ends_with($base, '/pendaftaran') ||
            str_ends_with($base, '/profil') ||
            str_ends_with($base, '/scripts') ||
            str_ends_with($base, '/uploads')
        )
    ) {
        $base = rtrim(str_replace('\\', '/', dirname($base)), '/');
    }

    if ($base === '/' || $base === '.' || $base === '\\') {
        return '';
    }

    return $base;
}

function url(string $path = ''): string
{
    $path = '/' . ltrim($path, '/');
    if ($path === '/') {
        return basePath() !== '' ? basePath() . '/' : '/';
    }

    return basePath() . $path;
}

function adminUrl(string $path = ''): string
{
    $path = ltrim($path, '/');
    return url('admin/' . $path);
}

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text) ?? '';
    $text = preg_replace('/[\s-]+/', '-', $text) ?? '';
    return trim($text, '-') ?: 'item-' . time();
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function formatTanggal(?string $date, string $format = 'd M Y'): string
{
    if (!$date) {
        return '-';
    }

    $bulan = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
        7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
    ];

    $ts = strtotime($date);
    if ($ts === false) {
        return $date;
    }

    $d = (int) date('j', $ts);
    $m = $bulan[(int) date('n', $ts)];
    $y = date('Y', $ts);

    return match ($format) {
        'd M Y' => "{$d} {$m} {$y}",
        'M Y' => "{$m} {$y}",
        default => date($format, $ts),
    };
}

function isActive(string $page): string
{
    $current = basename($_SERVER['PHP_SELF'] ?? '');
    return $current === $page ? 'is-active' : '';
}

function siteName(?PDO $pdo = null): string
{
    if ($pdo !== null) {
        $setting = getPengaturan($pdo);
        if (!empty($setting['nama_web'])) {
            return $setting['nama_web'];
        }
    }
    return 'Pondok Pesantren Nurul Hikmah';
}

function siteTagline(?PDO $pdo = null): string
{
    if ($pdo !== null) {
        $setting = getPengaturan($pdo);
        if (!empty($setting['tagline'])) {
            return $setting['tagline'];
        }
    }
    return 'Membentuk generasi berilmu, berakhlak, dan mandiri';
}

function getPengaturan(PDO $pdo): array
{
    static $pengaturan = null;
    if ($pengaturan !== null) {
        return $pengaturan;
    }

    try {
        $stmt = $pdo->query('SELECT * FROM pengaturan WHERE id = 1');
        $data = $stmt ? $stmt->fetch() : false;
        if ($data) {
            $pengaturan = $data;
            return $pengaturan;
        }
    } catch (PDOException $e) {
        // Fallback jika tabel belum terbaca
    }

    $pengaturan = [
        'id' => 1,
        'nama_web' => 'Nurul Hikmah',
        'sub_nama' => 'Pondok Pesantren',
        'tagline' => 'Membentuk generasi berilmu, berakhlak, dan mandiri',
        'logo_gambar' => null,
        'favicon' => null,
        'logo_tipe' => 'inisial',
        'inisial_teks' => 'ن ه',
    ];
    return $pengaturan;
}

function assetOrUploadUrl(?string $path): string
{
    if (!$path) {
        return '';
    }
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) {
        return $path;
    }
    return url(ltrim($path, '/'));
}

function uploadFile(
    array $file,
    string $subfolder = 'uploads',
    array $allowedExts = ['png', 'jpg', 'jpeg', 'webp', 'ico'],
    int $maxBytes = 2097152
): array {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'error' => 'Parameter upload tidak valid.'];
    }

    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'error' => 'Tidak ada file yang diunggah.'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Gagal mengunggah file (kode ' . $file['error'] . ').'];
    }

    if ($file['size'] > $maxBytes) {
        $mb = round($maxBytes / 1048576, 1);
        return ['success' => false, 'error' => "Ukuran file terlalu besar. Maksimal {$mb} MB."];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExts, true)) {
        return ['success' => false, 'error' => 'Format file tidak didukung. Format yang diizinkan: ' . implode(', ', $allowedExts) . '.'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowedMimes = [
        'png'  => ['image/png'],
        'jpg'  => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'webp' => ['image/webp'],
        'ico'  => ['image/x-icon', 'image/vnd.microsoft.icon', 'image/ico'],
    ];

    if (isset($allowedMimes[$ext]) && !in_array($mime, $allowedMimes[$ext], true)) {
        return ['success' => false, 'error' => 'Format MIME file tidak cocok dengan ekstensinya.'];
    }

    $rootDir = dirname(__DIR__);
    $targetDir = rtrim($rootDir, '/\\') . DIRECTORY_SEPARATOR . trim($subfolder, '/\\');
    if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
        return ['success' => false, 'error' => 'Gagal membuat folder penyimpanan file di server.'];
    }

    $filename = bin2hex(random_bytes(12)) . '.' . $ext;
    $destination = $targetDir . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => false, 'error' => 'Gagal menyimpan file ke folder tujuan.'];
    }

    $relativePath = str_replace('\\', '/', trim($subfolder, '/\\') . '/' . $filename);
    return ['success' => true, 'path' => $relativePath, 'filename' => $filename];
}

function deleteUploadedFile(?string $relativePath): void
{
    if (!$relativePath) {
        return;
    }
    if (str_starts_with($relativePath, 'http://') || str_starts_with($relativePath, 'https://')) {
        return;
    }
    $rootDir = dirname(__DIR__);
    $cleanPath = ltrim(str_replace(['../', '..\\'], '', $relativePath), '/\\');
    if (!str_starts_with($cleanPath, 'uploads/')) {
        return;
    }
    $fullPath = $rootDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $cleanPath);
    if (is_file($fullPath)) {
        @unlink($fullPath);
    }
}

function renderParagraphs(?string $text): string
{
    if ($text === null || trim($text) === '') {
        return '';
    }

    // Normalisasi karakter baris baru
    $text = str_replace(["\r\n", "\r"], "\n", trim($text));

    // Pecah berdasarkan 2 atau lebih baris baru (paragraf)
    $blocks = preg_split('/\n{2,}/', $text);
    $html = '';

    foreach ($blocks as $block) {
        $block = trim($block);
        if ($block !== '') {
            $html .= '<p>' . nl2br(e($block)) . '</p>';
        }
    }

    return $html;
}

function getProfil(PDO $pdo): array
{
    $profil = $pdo->query('SELECT * FROM profil WHERE id = 1')->fetch();
    if (!$profil) {
        return [];
    }

    return $profil;
}

function getProfilProgram(PDO $pdo): array
{
    return $pdo->query('SELECT * FROM profil_program ORDER BY urutan ASC, id ASC')->fetchAll();
}
