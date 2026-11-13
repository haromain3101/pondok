<?php

require_once __DIR__ . '/security-guard.php';
require dirname(__DIR__) . '/includes/functions.php';
require dirname(__DIR__) . '/config/database.php';

$profil = getProfil($pdo);
$programs = getProfilProgram($pdo);

echo 'profil=' . (!empty($profil) ? 'ok' : 'missing') . PHP_EOL;
echo 'hero=' . ($profil['hero_judul'] ?? '-') . PHP_EOL;
echo 'programs=' . count($programs) . PHP_EOL;
