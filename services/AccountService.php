<?php

/**
 * Account Service - manages user account operations
 * Handles account deletion and account management
 */
class AccountService {
    
    /**
     * Обновить данные аккаунта (username и email)
     */
    public static function updateAccount($userId, $data) {
        $errors = [];

        if (!is_array($data) || empty($data)) {
            return ['success' => false, 'errors' => ['No data provided']];
        }

        $username = trim((string)($data['username'] ?? ''));
        $email = strtolower(trim((string)($data['email'] ?? '')));

        // Валидация username
        if ($username === '') {
            $errors[] = 'Username is required';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors[] = 'Username can only contain letters, numbers, and underscores';
        }

        // Валидация email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email address';
        }

        // Проверка уникальности username
        if (Auth::existsUsernameExceptUser($username, $userId)) {
            $errors[] = 'Username exists already';
        }

        // Проверка уникальности email
        if (Auth::existsEmailExceptUser($email, $userId)) {
            $errors[] = 'Email exists already';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Сохранение в БД
        $saved = Auth::updateAccount($userId, $username, $email);
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
     * Обновить пароль
     */
    public static function updatePassword($userId, $data) {
        $errors = [];

        if (!is_array($data) || empty($data)) {
            return ['success' => false, 'errors' => ['No data provided']];
        }

        $currentPassword = (string)($data['current_password'] ?? '');
        $newPassword = (string)($data['new_password'] ?? '');
        $confirmPassword = (string)($data['confirm_password'] ?? '');

        // Получить пользователя
        $user = Auth::getUserByID($userId);
        if (!$user) {
            return ['success' => false, 'errors' => ['User not found']];
        }

        // Валидация полей
        if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
            $errors[] = 'All password fields are required';
        }

        if (strlen($newPassword) < 6) {
            $errors[] = 'New password must be at least 6 characters long';
        }

        if ($newPassword !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }

        // Проверка текущего пароля
        if (!password_verify($currentPassword, $user['password'])) {
            $errors[] = 'Current password is incorrect';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Сохранение нового пароля
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $saved = Auth::updatePassword($userId, $hashedPassword);
        if (!$saved) {
            return ['success' => false, 'errors' => ['Database error while changing password']];
        }

        return ['success' => true];
    }
}

?>
