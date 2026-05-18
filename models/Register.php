<?php
/**
 * Register Model - manages user registration data
 * Handles storing and retrieving registration information
 */
class Register {
    public static function saveUser($cleanData, $db = null) {
        $db = $db ?? new Database();

        // Проверка уникальности email
        $user = $db->getOne("SELECT * FROM users WHERE email = ?", [$cleanData['email']]);
        if ($user) {
            return ['success' => false, 'errors' => ['Email exists already']];
        }

        // Проверка уникальности username
        $user = $db->getOne("SELECT * FROM users WHERE username = ?", [$cleanData['name']]);
        if ($user) {
            return ['success' => false, 'errors' => ['Username exists already']];
        }

        $query = "INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `status`, `created_at`) VALUES (NULL, ?, ?, ?, 'user', 'active', ?)";
        $params = [
            $cleanData['name'],
            $cleanData['email'],
            password_hash($cleanData['password'], PASSWORD_DEFAULT),
            date("Y-m-d")
        ];
        $result = $db->executeRun($query, $params);
        if ($result) {
            $userId = (int)$db->getLastInsertId();
            return [
                'success' => true,
                'user' => [
                    'id' => $userId,
                    'username' => $cleanData['name'],
                    'role' => 'user',
                    'status' => 'active'
                ]
            ];
        } else {
            return ['success' => false, 'errors' => ['Database error: Unable to save user']];
        }
    }
}
?>