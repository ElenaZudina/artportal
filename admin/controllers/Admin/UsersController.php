<?php

/**
 * Admin Users Controller - manages user accounts
 * Handles user list, status changes, and user account operations
 */
class UsersController {
    /**
     * Display users list
     * Optionally filters users by search query and renders users-list view
     */
    public static function index() {
        $searchQuery = trim((string)($_GET['q'] ?? ''));
        $arr = Auth::getUsers($searchQuery);
        include_once 'views/users-list.php';
    }

    /**
     * Update a user's status
     * Validates request and updates user status ('active'|'blocked')
     * Protects admin accounts and prevents self-status changes
     */
    public static function updateStatus() {
        $searchQuery = trim((string)($_POST['q'] ?? ''));
        $userId = (int)($_POST['id'] ?? 0);
        $status = trim((string)($_POST['status'] ?? ''));

        if ($userId <= 0 || !in_array($status, ['active', 'blocked'], true)) {
            $_SESSION['errorString'] = 'Invalid user update request.';
            header('Location: users' . ($searchQuery !== '' ? '?q=' . urlencode($searchQuery) : ''));
            exit;
        }

        if (isset($_SESSION['userId']) && (int)$_SESSION['userId'] === $userId) {
            $_SESSION['errorString'] = 'You cannot change your own account status.';
            header('Location: users' . ($searchQuery !== '' ? '?q=' . urlencode($searchQuery) : ''));
            exit;
        }

        $user = Auth::getUserByID($userId);
        if (!$user || (($user['role'] ?? '') === 'admin')) {
            $_SESSION['errorString'] = 'This account is protected.';
            header('Location: users' . ($searchQuery !== '' ? '?q=' . urlencode($searchQuery) : ''));
            exit;
        }

        Auth::updateStatus($userId, $status);
        $_SESSION['successString'] = 'User status updated successfully.';

        header('Location: users' . ($searchQuery !== '' ? '?q=' . urlencode($searchQuery) : ''));
        exit;
    }
}