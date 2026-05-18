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

    /**
     * Get a user account by its ID.
     * @param int $id User ID
     * @return array|null User data or null if not found
     */
    public static function getUserByID($id) {
        $sql = 'SELECT * FROM `users` WHERE `id` = ?';
        $db = new Database();
        return $db->getOne($sql, [$id]);
    }

    /**
     * Get users for admin listing, optionally filtered by username or email.
     * @param string $search Optional search term
     * @return array Array of matching users
     */
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

    /**
     * Update a user's account status.
     * @param int $userId User ID
     * @param string $status New status, either active or blocked
     * @return bool Success status
     */
    public static function updateStatus($userId, $status) {
        if (!in_array($status, ['active', 'blocked'], true)) {
            return false;
        }

        $sql = 'UPDATE `users` SET `status` = ? WHERE `id` = ?';
        $db = new Database();
        return $db->executeRun($sql, [$status, (int)$userId]);
    }

    /**
     * Refresh session user data from the database and clear invalid sessions.
     * @return array|null Authenticated user data or null when the session is invalid
     */
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

    /**
     * Get the authenticated user after validating the current session.
     * @return array|null Authenticated user data or null
     */
    public static function getAuthenticatedUser() {
        return self::syncSessionStatus();
    }

    /**
     * Require a logged-in user and optionally require a specific role.
     * Redirects to login when the session is missing or unauthorized.
     * @param string|null $requiredRole Required role name, or null for any logged-in user
     * @param string|null $errorMessage Optional login error message
     * @return array Authenticated user data
     */
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
            // Clear unauthorized sessions and redirect to login with an access denied message.
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

    /**
     * Require an authenticated session and optionally a specific role.
     * @param string|null $requiredRole Required role name, or null for any logged-in user
     * @param string|null $errorMessage Optional login error message
     * @return array Authenticated user data
     */
    public static function requireSession($requiredRole = null, $errorMessage = null) {
        return self::requireRole($requiredRole, $errorMessage);
    }

    /**
     * Require that the current session belongs to a regular user account.
     * Redirects back to the previous page when the role is not allowed.
     * @param string $errorMessage Error message shown to unauthorized users
     * @return array Authenticated user data
     */
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

    /**
     * Clear the active PHP session.
     * @return void
     */
    private static function clearSession() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
    }

    /**
     * Check whether another user already uses the given email.
     * @param string $email Email address to check
     * @param int $userId User ID to exclude
     * @return bool True when the email exists for a different user
     */
    public static function existsEmailExceptUser($email, $userId) {
        $sql = 'SELECT id FROM `users` WHERE `email` = ? AND `id` <> ? LIMIT 1';
        $db = new Database();
        return (bool)$db->getOne($sql, [$email, (int)$userId]);
    }

    /**
     * Check whether another user already uses the given username.
     * @param string $username Username to check
     * @param int $userId User ID to exclude
     * @return bool True when the username exists for a different user
     */
    public static function existsUsernameExceptUser($username, $userId) {
        $sql = 'SELECT id FROM `users` WHERE `username` = ? AND `id` <> ? LIMIT 1';
        $db = new Database();
        return (bool)$db->getOne($sql, [$username, (int)$userId]);
    }

    /**
     * Update account profile fields for a user.
     * @param int $userId User ID
     * @param string $username New username
     * @param string $email New email address
     * @return bool Success status
     */
    public static function updateAccount($userId, $username, $email) {
        $sql = 'UPDATE `users` SET `username` = ?, `email` = ? WHERE `id` = ?';
        $db = new Database();
        return $db->executeRun($sql, [$username, $email, (int)$userId]);
    }

    /**
     * Update a user's password hash.
     * @param int $userId User ID
     * @param string $passwordHash Hashed password
     * @return bool Success status
     */
    public static function updatePassword($userId, $passwordHash) {
        $sql = 'UPDATE `users` SET `password` = ? WHERE `id` = ?';
        $db = new Database();
        return $db->executeRun($sql, [$passwordHash, (int)$userId]);
    }

    /**
     * Count all user accounts.
     * @return int Total number of users
     */
    public static function count() {
        $db = new Database();
        $row = $db->getOne('SELECT COUNT(*) AS cnt FROM users');
        return intval($row['cnt'] ?? 0);
    }

    /**
     * Count user accounts by role.
     * @param string $role Role name
     * @return int Number of users with the role
     */
    public static function countByRole($role) {
        $db = new Database();
        $row = $db->getOne('SELECT COUNT(*) AS cnt FROM users WHERE role = ?', [$role]);
        return intval($row['cnt'] ?? 0);
    }
}

?>
