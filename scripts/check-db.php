<?php

require_once __DIR__ . '/security-guard.php';
require dirname(__DIR__) . '/config/database.php';

echo 'DB OK' . PHP_EOL;
echo 'berita=' . $pdo->query('SELECT COUNT(*) FROM berita')->fetchColumn() . PHP_EOL;
echo 'agenda=' . $pdo->query('SELECT COUNT(*) FROM agenda')->fetchColumn() . PHP_EOL;
echo 'galeri=' . $pdo->query('SELECT COUNT(*) FROM galeri')->fetchColumn() . PHP_EOL;
echo 'pengaturan=' . $pdo->query('SELECT COUNT(*) FROM pengaturan')->fetchColumn() . PHP_EOL;
$p = $pdo->query('SELECT * FROM pengaturan WHERE id = 1')->fetch();
echo 'nama_web=' . $p['nama_web'] . ', inisial=' . $p['inisial_teks'] . PHP_EOL;
