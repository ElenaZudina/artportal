<?php
class Auth {
    // АВТОРИЗАЦИЯ 
    public static function findUserByEmail($email) {
        $sql='SELECT * FROM `users` WHERE `email` = ?';
        $db = new Database();
        return $db->getOne($sql, [$email]);
    }

    public static function getUserByID($id) {
        $sql = 'SELECT * FROM `users` WHERE `id` = ?';
        $db = new Database();
        return $db->getOne($sql, [$id]);
    }

    public static function existsEmailExceptUser($email, $userId) {
        $sql = 'SELECT id FROM `users` WHERE `email` = ? AND `id` <> ? LIMIT 1';
        $db = new Database();
        return (bool)$db->getOne($sql, [$email, (int)$userId]);
    }

    public static function existsUsernameExceptUser($username, $userId) {
        $sql = 'SELECT id FROM `users` WHERE `username` = ? AND `id` <> ? LIMIT 1';
        $db = new Database();
        return (bool)$db->getOne($sql, [$username, (int)$userId]);
    }

    public static function updateAccount($userId, $username, $email) {
        $sql = 'UPDATE `users` SET `username` = ?, `email` = ? WHERE `id` = ?';
        $db = new Database();
        return $db->executeRun($sql, [$username, $email, (int)$userId]);
    }

    public static function updatePassword($userId, $passwordHash) {
        $sql = 'UPDATE `users` SET `password` = ? WHERE `id` = ?';
        $db = new Database();
        return $db->executeRun($sql, [$passwordHash, (int)$userId]);
    }

    public static function count() {
        $db = new Database();
        $row = $db->getOne('SELECT COUNT(*) AS cnt FROM users');
        return intval($row['cnt'] ?? 0);
    }

    public static function countByRole($role) {
        $db = new Database();
        $row = $db->getOne('SELECT COUNT(*) AS cnt FROM users WHERE role = ?', [$role]);
        return intval($row['cnt'] ?? 0);
    }
}

?>