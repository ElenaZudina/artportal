<?php
/**
 * Authentication Model - handles user authentication and session management
 * Manages user lookups, status updates, and session validation
 */
class Auth {
    
    /**
     * Find user account by email address
     * Supports dependency injection for testing purposes
     * @param string $email User email address
     * @param Database $db Optional database instance for testing
     * @return array User data or null if not found
     */
    public static function findUserByEmail($email, $db = null) {
        // Allow injecting a Database instance for testing; fall back to real Database in production
        $db = $db ?? new Database();
        return $db->getOne('SELECT * FROM `users` WHERE `email` = ?', [$email]);
    }

    public static function getUserByID($id) {
        $sql = 'SELECT * FROM `users` WHERE `id` = ?';
        $db = new Database();
        return $db->getOne($sql, [$id]);
    }

    public static function getUsers($search = '') {
        $db = new Database();
        $search = trim((string)$search);

        if ($search !== '') {
            $like = '%' . $search . '%';
            return $db->getAll(
                'SELECT id, username, email, role, status, created_at FROM users WHERE username LIKE ? OR email LIKE ? ORDER BY id DESC',
                [$like, $like]
            );
        }

        return $db->getAll('SELECT id, username, email, role, status, created_at FROM users ORDER BY id DESC');
    }

    public static function updateStatus($userId, $status) {
        if (!in_array($status, ['active', 'blocked'], true)) {
            return false;
        }

        $sql = 'UPDATE `users` SET `status` = ? WHERE `id` = ?';
        $db = new Database();
        return $db->executeRun($sql, [$status, (int)$userId]);
    }

    public static function syncSessionStatus() {
        if (empty($_SESSION['userId'])) {
            return null;
        }

        $user = self::getUserByID((int)$_SESSION['userId']);
        if (!$user || (($user['status'] ?? 'active') !== 'active')) {
            self::clearSession();
            return null;
        }

        $_SESSION['status'] = $user['role'] ?? ($_SESSION['status'] ?? null);
        $_SESSION['name'] = $user['username'] ?? ($_SESSION['name'] ?? null);
        $_SESSION['accountStatus'] = $user['status'] ?? 'active';

        return $user;
    }

    public static function getAuthenticatedUser() {
        return self::syncSessionStatus();
    }

    public static function requireRole($requiredRole = null, $errorMessage = null) {
        $user = self::getAuthenticatedUser();
        if (!$user) {
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
            $_SESSION['errorString'] = $errorMessage ?? 'You must be logged in to perform this action.';
            header('Location: /artportal/login');
            exit;
        }

        if ($requiredRole !== null && (($user['role'] ?? '') !== $requiredRole)) {
            // У пользователя нет нужной роли — принудительно сбрасываем сессию
            // и перенаправляем на страницу логина с сообщением об отказе в доступе.
            self::clearSession();
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
            $_SESSION['errorString'] = 'You do not have access to this section.';
            header('Location: /artportal/login');
            exit;
        }

        return $user;
    }

    public static function requireSession($requiredRole = null, $errorMessage = null) {
        return self::requireRole($requiredRole, $errorMessage);
    }

    public static function requireUserAction($errorMessage = 'Only users can perform this action.') {
        $user = self::requireSession(null, 'You must be logged in to perform this action.');

        if (($user['role'] ?? '') !== 'user') {
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }

            $_SESSION['errorString'] = $errorMessage;
            $redirect = $_SERVER['HTTP_REFERER'] ?? '/artportal/';
            header('Location: ' . $redirect);
            exit;
        }

        return $user;
    }

    private static function clearSession() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
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
