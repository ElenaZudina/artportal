<?php

/**
 * Account Service - manages user account operations
 * Handles account deletion and account management
 */
class AccountService {
    
    /**
     * Update account profile fields, including username and email.
     * @param int $userId User ID
     * @param array $data Account form data
     * @return array Result data with success flag and validation errors
     */
    public static function updateAccount($userId, $data, $db = null) {
        $errors = [];

        if (!is_array($data) || empty($data)) {
            return ['success' => false, 'errors' => ['No data provided']];
        }

        $username = trim((string)($data['username'] ?? ''));
        $email = strtolower(trim((string)($data['email'] ?? '')));

        // Validate username.
        if ($username === '') {
            $errors[] = 'Username is required';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors[] = 'Username can only contain letters, numbers, and underscores';
        }

        // Validate email.
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email address';
        }

        // Check username uniqueness.
        if (Auth::existsUsernameExceptUser($username, $userId, $db)) {
            $errors[] = 'Username exists already';
        }

        // Check email uniqueness.
        if (Auth::existsEmailExceptUser($email, $userId, $db)) {
            $errors[] = 'Email exists already';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Save account changes to the database.
        $saved = Auth::updateAccount($userId, $username, $email, $db);
        if (!$saved) {
            return ['success' => false, 'errors' => ['Database error while updating account']];
        }

        return [
            'success' => true,
            'username' => $username,
            'email' => $email
        ];
    }

    /**
     * Update account password after validating the current password.
     * @param int $userId User ID
     * @param array $data Password change form data
     * @return array Result data with success flag and validation errors
     */
    public static function updatePassword($userId, $data, $db = null) {
        $errors = [];

        if (!is_array($data) || empty($data)) {
            return ['success' => false, 'errors' => ['No data provided']];
        }

        $currentPassword = (string)($data['current_password'] ?? '');
        $newPassword = (string)($data['new_password'] ?? '');
        $confirmPassword = (string)($data['confirm_password'] ?? '');

        // Load the user before validating the current password.
        $user = Auth::getUserByID($userId, $db);
        if (!$user) {
            return ['success' => false, 'errors' => ['User not found']];
        }

        // Validate password fields.
        if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
            $errors[] = 'All password fields are required';
        }

        if (strlen($newPassword) < 6) {
            $errors[] = 'New password must be at least 6 characters long';
        }

        if ($newPassword !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }

        // Verify the current password.
        if (!password_verify($currentPassword, $user['password'])) {
            $errors[] = 'Current password is incorrect';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Save the new password hash.
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $saved = Auth::updatePassword($userId, $hashedPassword, $db);
        if (!$saved) {
            return ['success' => false, 'errors' => ['Database error while changing password']];
        }

        return ['success' => true];
    }
}

?>
