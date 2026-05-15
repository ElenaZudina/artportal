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
        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $password)) {
            $errors[] = 'Password must be at least 8 characters and contain at least one letter and one number';
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
class AuthService {
    public static function login($data) {
        $errors = [];
        if (!is_array($data) || empty($data)) {
            return ['success' => false, 'errors' => ['No data provided']];
        }
        $email = strtolower(trim($data['email'] ?? ''));
        $password = $data['password'] ?? '';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email address';
        }
      
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $user=Auth::findUserByEmail($email);

        if(!$user) {
            return ['success' => false, 'errors' => ['User not found']];
        }
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'errors' => ['Incorrect password']];
        }

        if (($user['status'] ?? 'active') !== 'active') {
            return ['success' => false, 'blocked' => true, 'errors' => ['Your account is blocked. Please contact the administrator.']];
        }
            
        return ['success' => true, 'user' => $user];
    }
}