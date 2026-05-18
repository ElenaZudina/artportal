<?php
/**
 * Dashboard Account Controller - manages artist account settings
 * Handles account information and settings management
 */
class AccountController {

    public static function account() {
        Auth::requireSession();

        $user = Auth::getUserByID((int)$_SESSION['userId']);
        include_once('views/account.php');
    }

    public static function editAccount() {
        Auth::requireSession();

        $user = Auth::getUserByID((int)$_SESSION['userId']);
        $formData = [
            'username' => $user['username'] ?? '',
            'email' => $user['email'] ?? ''
        ];
        include_once('views/account-edit-form.php');
    }

    public static function updateAccount() {
        Auth::requireSession();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /artportal/dashboard/edit-account');
            exit;
        }

        if (!CsrfHelper::validate()) {
            $_SESSION['errorString'] = 'Invalid form token. Please try again.';
            header('Location: /artportal/dashboard/edit-account');
            exit;
        }

        $userId = (int)$_SESSION['userId'];
        $result = AccountService::updateAccount($userId, $_POST);

        if ($result['success']) {
            $_SESSION['name'] = $result['username'];
            $_SESSION['successString'] = 'Account updated successfully.';
            header('Location: /artportal/dashboard/account');
            exit;
        }

        $test = false;
        $errorMessage = !empty($result['errors']) ? implode(' ', $result['errors']) : null;
        $user = Auth::getUserByID($userId);
        $formData = [
            'username' => $_POST['username'] ?? '',
            'email' => $_POST['email'] ?? ''
        ];
        include_once('views/account-edit-form.php');
    }

    public static function changePassword() {
        Auth::requireSession();

        include_once('views/account-password-form.php');
    }

    public static function updatePassword() {
        Auth::requireSession();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /artportal/dashboard/change-password');
            exit;
        }

        if (!CsrfHelper::validate()) {
            $_SESSION['errorString'] = 'Invalid form token. Please try again.';
            header('Location: /artportal/dashboard/change-password');
            exit;
        }

        $userId = (int)$_SESSION['userId'];
        $result = AccountService::updatePassword($userId, $_POST);

        if ($result['success']) {
            $_SESSION['successString'] = 'Password changed successfully.';
            header('Location: /artportal/dashboard/account');
            exit;
        }

        $test = false;
        $errorMessage = !empty($result['errors']) ? implode(' ', $result['errors']) : null;
        include_once('views/account-password-form.php');
    }

}
?>
