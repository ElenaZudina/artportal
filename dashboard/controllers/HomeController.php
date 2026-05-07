<?php
class HomeController {

    private static function requireAuth() {
        if (empty($_SESSION['userId'])) {
            header('Location: /artportal/login');
            exit;
        }
    }

    // Вход в Дашборд
    public static function startDashboard() {
        include_once('views/start-dashboard.php');
}

    public static function profile() {
        self::requireAuth();

        $profile = Artists::getArtistByUserId((int)$_SESSION['userId']);
        include_once('views/profile.php');
    }

    public static function account() {
        self::requireAuth();

        $user = Auth::getUserByID((int)$_SESSION['userId']);
        include_once('views/account.php');
    }

    public static function editAccount() {
        self::requireAuth();

        $user = Auth::getUserByID((int)$_SESSION['userId']);
        $formData = [
            'username' => $user['username'] ?? '',
            'email' => $user['email'] ?? ''
        ];
        include_once('views/account-edit-form.php');
    }

    public static function updateAccount() {
        self::requireAuth();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /artportal/dashboard/edit-account');
            exit;
        }

        $userId = (int)$_SESSION['userId'];
        $username = trim((string)($_POST['username'] ?? ''));
        $email = strtolower(trim((string)($_POST['email'] ?? '')));

        $errors = [];
        if ($username === '') {
            $errors[] = 'Username is required';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors[] = 'Username can only contain letters, numbers, and underscores';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email address';
        }

        if (Auth::existsUsernameExceptUser($username, $userId)) {
            $errors[] = 'Username exists already';
        }

        if (Auth::existsEmailExceptUser($email, $userId)) {
            $errors[] = 'Email exists already';
        }

        $test = empty($errors);
        if ($test) {
            $saved = Auth::updateAccount($userId, $username, $email);
            if (!$saved) {
                $test = false;
                $errors[] = 'Database error while updating account';
            } else {
                $_SESSION['name'] = $username;
            }
        }

        $errorMessage = !empty($errors) ? implode(' ', $errors) : null;
        $user = Auth::getUserByID($userId);
        $formData = [
            'username' => $username,
            'email' => $email
        ];
        include_once('views/account-edit-form.php');
    }

    public static function changePassword() {
        self::requireAuth();

        include_once('views/account-password-form.php');
    }

    public static function updatePassword() {
        self::requireAuth();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /artportal/dashboard/change-password');
            exit;
        }

        $userId = (int)$_SESSION['userId'];
        $currentPassword = (string)($_POST['current_password'] ?? '');
        $newPassword = (string)($_POST['new_password'] ?? '');
        $confirmPassword = (string)($_POST['confirm_password'] ?? '');

        $errors = [];
        $user = Auth::getUserByID($userId);
        if (!$user) {
            $errors[] = 'User not found';
        }

        if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
            $errors[] = 'All password fields are required';
        }

        if (strlen($newPassword) < 6) {
            $errors[] = 'New password must be at least 6 characters long';
        }

        if ($newPassword !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }

        if ($user && !password_verify($currentPassword, $user['password'])) {
            $errors[] = 'Current password is incorrect';
        }

        $test = empty($errors);
        if ($test) {
            $saved = Auth::updatePassword($userId, password_hash($newPassword, PASSWORD_DEFAULT));
            if (!$saved) {
                $test = false;
                $errors[] = 'Database error while changing password';
            }
        }

        $errorMessage = !empty($errors) ? implode(' ', $errors) : null;
        include_once('views/account-password-form.php');
    }

    public static function editProfile() {
        self::requireAuth();

        $user = Auth::getUserByID((int)$_SESSION['userId']);
        $profile = Artists::getArtistByUserId((int)$_SESSION['userId']);

        if (!$profile) {
            header('Location: profile');
            exit;
        }

        include_once('views/profile-edit-form.php');
    }

    public static function updateProfile() {
        self::requireAuth();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /artportal/dashboard/edit-profile');
            exit;
        }

        $resultProfile = ArtistProfileService::updateProfile($_POST, $_FILES, (int)$_SESSION['userId']);
        $test = $resultProfile['success'] ?? false;
        $errorMessage = $resultProfile['errorMessage'] ?? null;
        if ($errorMessage === null && !empty($resultProfile['errors']) && is_array($resultProfile['errors'])) {
            $errorMessage = implode(' ', $resultProfile['errors']);
        }
        $user = Auth::getUserByID((int)$_SESSION['userId']);
        $profile = Artists::getArtistByUserId((int)$_SESSION['userId']);
        $formData = $resultProfile['data'] ?? $profile ?? [];
        include_once('views/profile-edit-form.php');
    }

    public static function myFavorites() {
        self::requireAuth();

        $favorites = Favorite::getUserFavorites((int)$_SESSION['userId']);
        include_once('views/my-favorites.php');
    }

    public static function myPaintings() {
        self::requireAuth();

        $artist = Artists::getArtistByUserId((int)$_SESSION['userId']);
        if (!$artist) {
            header('Location: profile');
            exit;
        }

        $paintings = Paintings::getPaintingsByArtistPortfolio((int)$artist['id']);
        include_once('views/my-paintings.php');
    }

    public static function addPainting() {
        self::requireAuth();

        $artist = Artists::getArtistByUserId((int)$_SESSION['userId']);
        if (!$artist) {
            header('Location: profile');
            exit;
        }

        $categories = Categories::getCategoriesList();
        $formData = [];
        include_once('views/painting-form.php');
    }

    public static function storePainting() {
        self::requireAuth();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: add-painting');
            exit;
        }

        $resultPainting = PaintingService::createPainting($_POST, $_FILES, (int)$_SESSION['userId']);
        $test = $resultPainting['success'] ?? false;
        $errorMessage = !empty($resultPainting['errors']) && is_array($resultPainting['errors']) ? implode(' ', $resultPainting['errors']) : null;
        $artist = Artists::getArtistByUserId((int)$_SESSION['userId']);
        $categories = Categories::getCategoriesList();
        $formData = $resultPainting['data'] ?? $_POST;
        include_once('views/painting-form.php');
    }

    public static function editPainting() {
        self::requireAuth();

        $artist = Artists::getArtistByUserId((int)$_SESSION['userId']);
        if (!$artist) {
            header('Location: profile');
            exit;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $painting = $id > 0 ? Paintings::getPaintingByID($id) : null;
        if (!$painting || (int)($painting['artist_id'] ?? 0) !== (int)$artist['id']) {
            header('Location: my-paintings');
            exit;
        }

        $categories = Categories::getCategoriesList();
        $formData = $painting;
        include_once('views/painting-form.php');
    }

    public static function updatePainting() {
        self::requireAuth();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: my-paintings');
            exit;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $resultPainting = PaintingService::updatePainting($id, $_POST, $_FILES, (int)$_SESSION['userId']);
        $test = $resultPainting['success'] ?? false;
        $errorMessage = !empty($resultPainting['errors']) && is_array($resultPainting['errors']) ? implode(' ', $resultPainting['errors']) : null;
        $artist = Artists::getArtistByUserId((int)$_SESSION['userId']);
        $painting = $id > 0 ? Paintings::getPaintingByID($id) : null;
        $categories = Categories::getCategoriesList();
        $formData = $resultPainting['data'] ?? $painting ?? $_POST;
        include_once('views/painting-form.php');
    }

    public static function deletePainting() {
        self::requireAuth();

        $artist = Artists::getArtistByUserId((int)$_SESSION['userId']);
        if (!$artist) {
            header('Location: profile');
            exit;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $painting = $id > 0 ? Paintings::getPaintingByID($id) : null;
        if (!$painting || (int)($painting['artist_id'] ?? 0) !== (int)$artist['id']) {
            header('Location: my-paintings');
            exit;
        }

        include_once('views/painting-delete-form.php');
    }

    public static function destroyPainting() {
        self::requireAuth();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: my-paintings');
            exit;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $resultPainting = PaintingService::deletePainting($id, (int)$_SESSION['userId']);
        $test = $resultPainting['success'] ?? false;
        $errorMessage = !empty($resultPainting['errors']) && is_array($resultPainting['errors']) ? implode(' ', $resultPainting['errors']) : null;
        $artist = Artists::getArtistByUserId((int)$_SESSION['userId']);
        $painting = $id > 0 ? Paintings::getPaintingByID($id) : null;
        include_once('views/painting-delete-form.php');
    }


  /*  // Выход из админ панели
    public static function logoutAction() {
        modelAdmin::userLogout();
        header("Location: login");
        exit;
    }*/
    // Страница Error
    public static function error404() {
        include_once('views/error404.php');
    }
}//end class
?>