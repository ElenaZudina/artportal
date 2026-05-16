<?php
class ProfileController {

    public static function artistProfileForm() {
        Auth::requireSession();

        $existingProfile = Artists::getArtistByUserId((int)$_SESSION['userId']);
        include_once('views/formArtistProfile.php');
    }

    public static function artistProfileSave() {
        Auth::requireSession();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
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
