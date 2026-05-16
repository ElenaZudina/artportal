<?php
require_once __DIR__ . '/../services/EmailService.php';

class controllerAuth {

    public static function formLoginSite() {
        include_once('views/formLogin.php');
    }

    public static function forgotPasswordForm() {
        include_once('views/forgot-password.php');
    }

    public static function forgotPasswordRequest() {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
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
    // Авторизация с учетом ролей
    public static function loginAction() {
        //$login=Auth::userAuthentication();
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

        // Проверяем, есть ли профиль художника для этого пользователя
        if ($user['role'] == 'user') {
            $db = new Database();
            $artist = $db->getOne("SELECT id FROM artists WHERE user_id = ?", [$user['id']]);
            if ($artist) {
                $_SESSION['status'] = 'artist';
            }
        }

        
        if ($user["role"] == "admin") {
            header("Location: /artportal/admin/startAdmin");
            exit;
        } else {
            header("Location: /artportal/dashboard/startDashboard");
            exit;
        }
    }

    // Выход из аккаунта
    public static function logoutAction() {
       session_unset(); // удаляет все переменные сессии
        session_destroy();
        
        header("Location: login");
        exit;
    }
}//end class
?>