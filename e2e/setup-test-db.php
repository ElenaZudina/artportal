<?php

$_SERVER['APP_ENV'] = 'test';

require_once __DIR__ . '/../config/Database.php';

$db = new Database();
$passwordHash = password_hash('123456', PASSWORD_DEFAULT);
$now = date('Y-m-d H:i:s');

function upsertUser(Database $db, string $username, string $email, string $role, string $passwordHash, string $status = 'active'): int
{
    $existing = $db->getOne('SELECT id FROM `users` WHERE `email` = ?', [$email]);

    if ($existing) {
        $db->executeRun(
            'UPDATE `users` SET `username` = ?, `password` = ?, `role` = ?, `status` = ? WHERE `email` = ?',
            [$username, $passwordHash, $role, $status, $email]
        );
        return (int)$existing['id'];
    }

    $db->executeRun(
        'INSERT INTO `users` (`username`, `email`, `password`, `role`, `status`, `created_at`) VALUES (?, ?, ?, ?, ?, ?)',
        [$username, $email, $passwordHash, $role, $status, date('Y-m-d H:i:s')]
    );

    return (int)$db->getLastInsertId();
}

function upsertCategory(Database $db, string $name): int
{
    $existing = $db->getOne('SELECT id FROM `categories` WHERE `name` = ?', [$name]);
    if ($existing) {
        return (int)$existing['id'];
    }

    $db->executeRun('INSERT INTO `categories` (`name`) VALUES (?)', [$name]);
    return (int)$db->getLastInsertId();
}

function upsertArtistProfile(Database $db, int $userId, string $name, string $status): int
{
    $existing = $db->getOne('SELECT id FROM `artists` WHERE `user_id` = ?', [$userId]);

    if ($existing) {
        $db->executeRun(
            'UPDATE `artists` SET `name` = ?, `location` = ?, `birth_date` = ?, `bio` = ?, `picture` = ?, `status` = ?, `updated_at` = ? WHERE `user_id` = ?',
            [$name, 'Estonia, Tallinn', '1990-01-01', 'E2E seeded artist profile', 'test.jpg', $status, date('Y-m-d H:i:s'), $userId]
        );
        return (int)$existing['id'];
    }

    $db->executeRun(
        'INSERT INTO `artists` (`name`, `location`, `birth_date`, `bio`, `picture`, `status`, `created_at`, `user_id`, `updated_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
        [$name, 'Estonia, Tallinn', '1990-01-01', 'E2E seeded artist profile', 'test.jpg', $status, date('Y-m-d H:i:s'), $userId, date('Y-m-d H:i:s')]
    );

    return (int)$db->getLastInsertId();
}

function upsertPainting(Database $db, int $artistId, int $categoryId, string $title = 'E2E Seed Painting'): int
{
    $existing = $db->getOne('SELECT id FROM `paintings` WHERE `title` = ? AND `artist_id` = ?', [$title, $artistId]);

    if ($existing) {
        $db->executeRun(
            'UPDATE `paintings` SET `description` = ?, `image` = ?, `year_created` = ?, `category_id` = ?, `medium` = ?, `dimensions` = ?, `price` = ?, `updated_at` = ? WHERE `id` = ?',
            ['Painting used by Playwright role scenarios', 'test.jpg', 2026, $categoryId, 'Acrylic', '40x50', 123.45, date('Y-m-d H:i:s'), (int)$existing['id']]
        );
        return (int)$existing['id'];
    }

    $db->executeRun(
        'INSERT INTO `paintings` (`title`, `description`, `image`, `year_created`, `category_id`, `artist_id`, `medium`, `dimensions`, `price`, `created_at`, `updated_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
        [$title, 'Painting used by Playwright role scenarios', 'test.jpg', 2026, $categoryId, $artistId, 'Acrylic', '40x50', 123.45, date('Y-m-d H:i:s'), date('Y-m-d H:i:s')]
    );

    return (int)$db->getLastInsertId();
}

$adminId = upsertUser($db, 'admin', 'admin@artportal.ee', 'admin', $passwordHash);
$userId = upsertUser($db, 'e2e_user', 'e2e-user@artportal.test', 'user', $passwordHash);
$artistUserId = upsertUser($db, 'e2e_artist', 'e2e-artist@artportal.test', 'artist', $passwordHash);
$pendingUserId = upsertUser($db, 'e2e_pending_artist', 'e2e-pending-artist@artportal.test', 'user', $passwordHash);

$categoryId = upsertCategory($db, 'E2E Category');
$artistId = upsertArtistProfile($db, $artistUserId, 'E2E Seed Artist', 'approved');
upsertArtistProfile($db, $pendingUserId, 'E2E Pending Artist', 'pending');
$paintingId = upsertPainting($db, $artistId, $categoryId);
$inquiryPaintingId = upsertPainting($db, $artistId, $categoryId, 'E2E Inquiry Painting');

if (!$db->getOne('SELECT id FROM `favorites` WHERE `user_id` = ? AND `painting_id` = ?', [$userId, $paintingId])) {
    $db->executeRun(
        'INSERT INTO `favorites` (`user_id`, `painting_id`, `created_at`) VALUES (?, ?, ?)',
        [$userId, $paintingId, $now]
    );
}

if (!$db->getOne('SELECT id FROM `purchase_requests` WHERE `user_id` = ? AND `painting_id` = ?', [$userId, $paintingId])) {
    $db->executeRun(
        'INSERT INTO `purchase_requests` (`user_id`, `painting_id`, `created_at`) VALUES (?, ?, ?)',
        [$userId, $paintingId, $now]
    );
}

$db->executeRun(
    'DELETE FROM `purchase_requests` WHERE `user_id` = ? AND `painting_id` = ?',
    [$userId, $inquiryPaintingId]
);
