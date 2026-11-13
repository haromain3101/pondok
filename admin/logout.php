<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/init.php';

logoutAdmin();
setFlash('success', 'Anda telah keluar.');
redirect(adminUrl('login.php'));
