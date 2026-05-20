<?php

$_SERVER['APP_ENV'] = 'test';

require_once __DIR__ . '/../config/Database.php';

$db = new Database();
$email = 'admin@artportal.ee';
$passwordHash = password_hash('123456', PASSWORD_DEFAULT);
$existing = $db->getOne('SELECT id FROM `users` WHERE `email` = ?', [$email]);

if ($existing) {
    $db->executeRun(
        'UPDATE `users` SET `username` = ?, `password` = ?, `role` = ?, `status` = ? WHERE `email` = ?',
        ['admin', $passwordHash, 'admin', 'active', $email]
    );
    return;
}

$db->executeRun(
    'INSERT INTO `users` (`username`, `email`, `password`, `role`, `status`, `created_at`) VALUES (?, ?, ?, ?, ?, ?)',
    ['admin', $email, $passwordHash, 'admin', 'active', date('Y-m-d H:i:s')]
);
