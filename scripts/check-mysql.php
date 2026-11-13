<?php

require_once __DIR__ . '/security-guard.php';
require dirname(__DIR__) . '/includes/functions.php';
require dirname(__DIR__) . '/config/database.php';

$config = require dirname(__DIR__) . '/config/db.php';

echo 'driver=' . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . PHP_EOL;
echo 'database=' . $config['database'] . PHP_EOL;
echo 'berita=' . $pdo->query('SELECT COUNT(*) FROM berita')->fetchColumn() . PHP_EOL;
echo 'agenda=' . $pdo->query('SELECT COUNT(*) FROM agenda')->fetchColumn() . PHP_EOL;
echo 'galeri=' . $pdo->query('SELECT COUNT(*) FROM galeri')->fetchColumn() . PHP_EOL;
echo 'admin=' . $pdo->query('SELECT COUNT(*) FROM admin')->fetchColumn() . PHP_EOL;
echo 'profil=' . $pdo->query('SELECT COUNT(*) FROM profil')->fetchColumn() . PHP_EOL;
echo 'profil_program=' . $pdo->query('SELECT COUNT(*) FROM profil_program')->fetchColumn() . PHP_EOL;

$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
echo 'tables=' . implode(',', $tables) . PHP_EOL;
