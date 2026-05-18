<?php
/**
 * Dashboard Profile Controller - manages artist profile
 * Handles artist profile editing and updates
 */

/**
 * Controller for managing artist profile in the dashboard.
 * Handles viewing, editing, and updating artist profile information.
 */
class ProfileController {

    /**
     * Display the artist profile page for the logged-in user.
     */
    public static function profile() {
        Auth::requireSession();

        $profile = Artists::getArtistByUserId((int)$_SESSION['userId']);
        include_once('views/profile.php');
    }

    /**
     * Show the artist profile edit form for the logged-in user.
     */
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

    /**
     * Handle the submission of the artist profile edit form and update profile data.
     * Validates CSRF token and POST method, updates profile, and handles errors.
     */
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
