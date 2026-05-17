<?php
function requireArtistProfile() {
    $artist = Artists::getArtistByUserId((int)$_SESSION['userId']);
    if (!$artist) {
        header('Location: profile');
        exit;
    }
    return $artist;
}
