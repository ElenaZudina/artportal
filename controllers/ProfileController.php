<?php
/**
 * Profile Controller - manages user artist profile operations
 * Handles profile form display and saving artist profile data
 * Requires authenticated user session
 */
class ProfileController {

    /**
     * Display artist profile edit form
     * Retrieves existing profile data if artist already has one
     * Requires user to be logged in
     */
    public static function artistProfileForm() {
        Auth::requireSession();

        $existingProfile = Artists::getArtistByUserId((int)$_SESSION['userId']);
        include_once('views/formArtistProfile.php');
    }

    /**
     * Process and save artist profile data
     * Validates CSRF token, handles file uploads for profile image
     * Creates or updates artist profile information
     * Requires user to be logged in
     */
    public static function artistProfileSave() {
        Auth::requireSession();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /artportal/artistProfileForm');
            exit;
        }

        if (!CsrfHelper::validate()) {
            $_SESSION['errorString'] = 'Invalid form token. Please try again.';
            header('Location: /artportal/artistProfileForm');
            exit;
        }

        $resultArtist = ArtistProfileService::createProfile($_POST, $_FILES, (int)$_SESSION['userId']);
        if (!empty($resultArtist['success'])) {
            include_once('views/answerArtistProfile.php');
            return;
        }

        $existingProfile = Artists::getArtistByUserId((int)$_SESSION['userId']);
        $formData = $resultArtist['data'] ?? [];
        include_once('views/formArtistProfile.php');
    }
}
?>
