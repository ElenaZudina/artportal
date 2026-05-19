<?php
/**
 * Register Service - handles user registration validation and creation
 * Validates registration data and creates new user accounts
 */
class RegisterService {
    
    /**
     * Register new user account
     * Validates username, email, password requirements and password confirmation
     * Returns errors if validation fails
     * @param array $data Registration form data (name, email, password, confirm)
     * @return array Success status with user data or errors
     */
    public static function register($data, $db = null) {
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
            
        return Register::saveUser($cleanData, $db);
    }
}
/**
 * Authentication Service - handles user login validation
 * Validates credentials and returns authenticated user data
 */
class AuthService {
    
    /**
     * Authenticate user login credentials
     * Validates email format, checks user exists, verifies password
     * Checks user account is active before returning success
     * @param array $data Login form data (email, password)
     * @return array Success status with user data or error messages
     */
    public static function login($data, $db = null) {
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

        $user=Auth::findUserByEmail($email, $db);

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
