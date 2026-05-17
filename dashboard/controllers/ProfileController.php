<?php
class ProfileController {

    public static function profile() {
        Auth::requireSession();

        $profile = Artists::getArtistByUserId((int)$_SESSION['userId']);
        include_once('views/profile.php');
    }

    public static function editProfile() {
        Auth::requireSession();

        $user = Auth::getUserByID((int)$_SESSION['userId']);
        $profile = Artists::getArtistByUserId((int)$_SESSION['userId']);

        if (!$profile) {
            header('Location: profile');
            exit;
        }

        include_once('views/profile-edit-form.php');
    }

    public static function updateProfile() {
        Auth::requireSession();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /artportal/dashboard/edit-profile');
            exit;
        }

        if (!CsrfHelper::validate()) {
            $_SESSION['errorString'] = 'Invalid form token. Please try again.';
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

}
?>
