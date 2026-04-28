<?php
class RegisterService {
    public static function register($data) {
        $errors = [];

        if (!is_array($data) || empty($data)) {
            return ['success' => false, 'errors' => ['No data provided']];
        }

        $name = trim($data['name'] ?? '');
        $email = strtolower(trim($data['email'] ?? ''));
        $password = $data['password'] ?? '';
        $confirm = $data['confirm'] ?? '';

        if ($name === '') {
            $errors[] = 'Name is required';
        } elseif (!preg_match("/^[a-zA-Z0-9_]+$/", $name)) {
            $errors[] = 'Name can only contain letters, numbers, and underscores';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email address';
        }
        if (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters long';
        }
        if ($password !== $confirm) {
            $errors[] = 'Passwords do not match';
        }
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $cleanData = [
            'name' => $name,
            'email' => $email,
            'password' => $password];
            
        return Register::saveUser($cleanData);
    }
}