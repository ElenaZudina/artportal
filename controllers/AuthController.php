<?php
require_once __DIR__ . '/../services/EmailService.php';

/**
 * Authentication Controller - handles all auth-related requests
 * Manages login, logout, registration, and password reset flows
 * Validates CSRF tokens and manages user sessions
 */
class AuthController {

    /**
     * Display login form page
     */
    public static function formLoginSite() {
        include_once('views/formLogin.php');
    }

    /**
     * Display forgot password form page
     */
    public static function forgotPasswordForm() {
        include_once('views/forgot-password.php');
    }

    /**
     * Process password reset request
     * Validates CSRF token, checks email exists in system
     * Sends password reset request to admin for approval
     */
    public static function forgotPasswordRequest() {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /artportal/forgot-password');
            exit;
        }

        if (!CsrfHelper::validate()) {
            $_SESSION['errorString'] = 'Invalid form token. Please try again.';
            header('Location: /artportal/forgot-password');
            exit;
        }

        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['errorString'] = 'Please enter a valid email address.';
            header('Location: /artportal/forgot-password');
            exit;
        }

        $user = Auth::findUserByEmail($email);
        if (!$user) {
            $_SESSION['successString'] = 'If the email exists, your request has been sent to the admin.';
            header('Location: /artportal/login');
            exit;
        }

        $sent = EmailService::sendPasswordResetRequestToAdmin($user);
        if ($sent) {
            $_SESSION['successString'] = 'Your request has been sent to the admin. He will contact you shortly.';
            header('Location: /artportal/login');
            exit;
        }

        $_SESSION['errorString'] = 'Could not send your request right now. Please try again later.';
        header('Location: /artportal/forgot-password');
        exit;
    }

    /**
     * Process user login
     * Validates CSRF token, authenticates user credentials
     * Sets session variables and redirects to dashboard or admin panel
     */
    public static function loginAction() {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /artportal/login');
            exit;
        }

        if (!CsrfHelper::validate()) {
            $_SESSION['errorString'] = 'Invalid form token. Please try again.';
            header('Location: /artportal/login');
            exit;
        }

        $login = AuthService::login($_POST);
        if(!$login['success']){
            $_SESSION['errorString'] = $login['errors'][0] ?? 'Incorrect username and password';
            header("Location: login");
            exit;
        }

        $user = $login['user'];

        $_SESSION['userId'] = $user['id'];
        $_SESSION['name'] = $user['username'];
        $_SESSION['status'] = $user['role'];
        $_SESSION['accountStatus'] = $user['status'] ?? 'active';

        if ($_SESSION['status'] === 'admin') {
            header("Location: /artportal/admin/startAdmin");
            exit;
        } else {
            header("Location: /artportal/dashboard/startDashboard");
            exit;
        }
    }

    /**
     * Logout user and destroy session
     * Clears all session data and redirects to login page
     */
    public static function logoutAction() {
        session_unset();
        session_destroy();
        
        header("Location: login");
        exit;
    }

    /**
     * Display user registration form page
     */
    public static function registerForm() {
        include_once('views/formRegister.php');
    }

    /**
     * Process new user registration
     * Validates CSRF token and registration data through RegisterService
     * Sets session on successful registration
     */
    public static function registerUser() {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /artportal/registerForm');
            exit;
        }

        if (!CsrfHelper::validate()) {
            $_SESSION['errorString'] = 'Invalid form token. Please try again.';
            header('Location: /artportal/registerForm');
            exit;
        }

        $result = RegisterService::register($_POST);

        if (!empty($result['success']) && !empty($result['user'])) {
            $_SESSION['userId'] = $result['user']['id'];
            $_SESSION['name'] = $result['user']['username'];
            $_SESSION['status'] = $result['user']['role'];
            $_SESSION['accountStatus'] = $result['user']['status'] ?? 'active';
        }

        include_once('views/answerRegister.php');
    }
}
?>
